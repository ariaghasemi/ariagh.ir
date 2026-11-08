<?php
/**
 * Template library.
 *
 * Copies the shipped Elementor templates from /templates into the WordPress
 * database once, so the hero appears under Templates > Saved Templates and can
 * be inserted anywhere the Elementor template library is available.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Templates {

	/**
	 * Post type Elementor uses for saved templates.
	 *
	 * @var string
	 */
	const CPT = 'elementor_library';

	/**
	 * Meta key holding the Elementor document type.
	 *
	 * @var string
	 */
	const META_TYPE = '_elementor_template_type';

	/**
	 * Meta key holding the element tree.
	 *
	 * @var string
	 */
	const META_DATA = '_elementor_data';

	/**
	 * Meta key marking a template as imported by this plugin.
	 *
	 * @var string
	 */
	const META_SOURCE = '_mfh_source';

	/**
	 * Meta key holding the version of this plugin that imported the template.
	 *
	 * @var string
	 */
	const META_VERSION = '_mfh_version';

	/**
	 * Meta key holding the edit-mode flag Elementor needs.
	 *
	 * @var string
	 */
	const META_EDIT_MODE = '_elementor_edit_mode';

	/**
	 * Option listing the manifests already imported, keyed by slug.
	 *
	 * @var string
	 */
	const OPTION = 'mfh_imported_templates';

	/**
	 * Seed every shipped template that is not present yet.
	 *
	 * Runs on activation, on explicit re-import, and lazily when the library
	 * screen is opened if the seeding was skipped (e.g. Elementor was inactive
	 * at activation time).
	 *
	 * @param bool $force Re-import even when already present.
	 *
	 * @return array Slug => status. Status is 'imported', 'exists', or 'error'.
	 */
	public static function seed( $force = false ) {
		if ( ! mfh_elementor_active() ) {
			return array();
		}

		$results = array();

		foreach ( self::manifests() as $manifest ) {
			$slug = isset( $manifest['slug'] ) ? $manifest['slug'] : '';

			if ( ! $slug ) {
				continue;
			}

			$existing = self::find( $slug );

			if ( $existing && ! $force ) {
				$results[ $slug ] = 'exists';
				continue;
			}

			if ( $existing && $force ) {
				wp_delete_post( $existing, true );
			}

			$results[ $slug ] = self::import( $manifest );
		}

		self::remember( $results );

		return $results;
	}

	/**
	 * Import one manifest into the library.
	 *
	 * @param array $manifest Manifest array with slug, title, type and file.
	 *
	 * @return string 'imported' or 'error'.
	 */
	private static function import( $manifest ) {
		$data = mfh_read_json_template( $manifest['file'] );

		if ( empty( $data ) ) {
			return 'error';
		}

		$title = isset( $manifest['title'] ) ? $manifest['title'] : $manifest['slug'];
		$type  = isset( $manifest['type'] ) ? $manifest['type'] : 'section';

		$post_id = wp_insert_post(
			array(
				'post_title'  => $title,
				'post_type'   => self::CPT,
				'post_status' => 'publish',
				'meta_input'  => array(
					self::META_EDIT_MODE => 'builder',
					self::META_TYPE      => $type,
					self::META_DATA      => wp_slash( wp_json_encode( $data ) ),
					self::META_SOURCE    => $manifest['slug'],
					self::META_VERSION   => MFH_VERSION,
				),
			),
			true
		);

		if ( is_wp_error( $post_id ) || ! $post_id ) {
			return 'error';
		}

		// Register the document with the relevant taxonomy term so the library
		// filter bar lists it under the right tab.
		$term = term_exists( $type, 'elementor_library_type' );

		if ( ! $term && function_exists( 'wp_insert_term' ) ) {
			$term = wp_insert_term( $type, 'elementor_library_type' );
		}

		if ( $term && ! is_wp_error( $term ) ) {
			$term_id = is_array( $term ) ? (int) $term['term_id'] : (int) $term;
			wp_set_object_terms( $post_id, array( $term_id ), 'elementor_library_type', true );
		}

		// A container-based document must declare its version, otherwise the
		// editor sandboxes it into flexbox emulation mode.
		if ( self::uses_containers( $data ) ) {
			update_post_meta( $post_id, '_elementor_container_width', 1140 );
		}

		return 'imported';
	}

	/**
	 * Whether the document tree is built with containers.
	 *
	 * @param array $data Elementor data.
	 *
	 * @return bool
	 */
	private static function uses_containers( $data ) {
		if ( ! is_array( $data ) ) {
			return false;
		}

		foreach ( $data as $element ) {
			if ( is_array( $element ) && isset( $element['elType'] ) && 'container' === $element['elType'] ) {
				return true;
			}
		}

		return false;
	}

	/**
	 * Find the library post previously imported for a slug.
	 *
	 * @param string $slug Template slug.
	 *
	 * @return int Post ID, or 0.
	 */
	public static function find( $slug ) {
		$query = new \WP_Query(
			array(
				'post_type'              => self::CPT,
				'post_status'            => 'any',
				'posts_per_page'         => 1,
				'fields'                 => 'ids',
				'no_found_rows'          => true,
				'update_post_meta_cache'  => false,
				'update_post_term_cache'  => false,
				'meta_query'             => array(
					array(
						'key'   => self::META_SOURCE,
						'value' => $slug,
					),
				),
			)
		);

		if ( empty( $query->posts ) ) {
			return 0;
		}

		return (int) $query->posts[0];
	}

	/**
	 * Remember which templates have been seeded, and when.
	 *
	 * @param array $results Result map from seed().
	 *
	 * @return void
	 */
	private static function remember( $results ) {
		$known = get_option( self::OPTION, array() );

		if ( ! is_array( $known ) ) {
			$known = array();
		}

		foreach ( $results as $slug => $status ) {
			if ( 'error' === $status ) {
				continue;
			}

			$known[ $slug ] = array(
				'status'  => $status,
				'at'      => time(),
				'version' => MFH_VERSION,
			);
		}

		update_option( self::OPTION, $known, false );
	}

	/**
	 * The shipped manifests.
	 *
	 * @return array
	 */
	public static function manifests() {
		return array(
			array(
				'slug'        => 'mainframe-hero',
				'title'       => __( 'هیروی Mainframe', 'mainframe-hero' ),
				'description' => __( 'هیروی تمام‌صفحه با ویدیوی پس‌زمینهٔ اسکراب‌شونده با حرکتِ اشاره‌گر، ناوبارِ شفاف با منوی موبایل، متنِ آغازینِ محو، متنِ تایپ‌شونده و دکمه‌های کپسولی.', 'mainframe-hero' ),
				'type'        => 'container',
				'file'        => 'hero.json',
			),
		);
	}

}
