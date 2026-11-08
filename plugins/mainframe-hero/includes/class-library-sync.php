<?php
/**
 * Library sync.
 *
 * Keeps the shipped templates present in Elementor's library, groups them under
 * their own category, and marks them in the list table so it is obvious which
 * templates came from this plugin.
 *
 * A note on the design: this plugin does NOT subclass Elementor's template
 * "source" API. That API carries ten abstract methods whose signatures move
 * between Elementor releases, and a mismatch there is a compile-time fatal
 * error that cannot be caught at runtime. Seeding into the `elementor_library`
 * post type instead lands the template in the very same place users look —
 * Templates > Saved Templates, and the "My Templates" tab of the library
 * modal — through a path that has been stable for years.
 *
 * @package MainframeHero
 */

namespace MFH;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Library_Sync extends Templates {

	/**
	 * Taxonomy slug Elementor uses for library categories.
	 *
	 * @var string
	 */
	const TAXONOMY = 'elementor_library_type';

	/**
	 * Term assigned to templates this plugin owns.
	 *
	 * @var string
	 */
	const TERM = 'mainframe';

	/**
	 * Wire the hooks.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_category' ), 20 );
		add_action( 'admin_init', array( $this, 'maybe_seed' ) );
		add_action( 'load-edit.php', array( $this, 'maybe_seed' ) );

		add_filter( 'manage_' . self::CPT . '_posts_columns', array( $this, 'add_column' ) );
		add_action( 'manage_' . self::CPT . '_posts_custom_column', array( $this, 'render_column' ), 10, 2 );

		add_action( 'admin_post_mfh_reseed', array( $this, 'handle_reseed' ) );
	}

	/**
	 * Make sure the Mainframe category exists in the library taxonomy.
	 *
	 * @return void
	 */
	public function register_category() {
		if ( ! taxonomy_exists( self::TAXONOMY ) ) {
			return;
		}

		if ( term_exists( self::TERM, self::TAXONOMY ) ) {
			return;
		}

		$created = wp_insert_term(
			__( 'Mainframe', 'mainframe-hero' ),
			self::TAXONOMY,
			array( 'slug' => self::TERM )
		);

		// Tag any template seeded before the term existed.
		if ( ! is_wp_error( $created ) && is_array( $created ) ) {
			$this->tag_owned( array( (int) $created['term_id'] ) );
		}
	}

	/**
	 * Assign the Mainframe term to every template the plugin owns.
	 *
	 * @param array $term_ids Optional term IDs; defaults to the Mainframe term.
	 *
	 * @return void
	 */
	private function tag_owned( $term_ids = array() ) {
		if ( ! $term_ids ) {
			$term = term_exists( self::TERM, self::TAXONOMY );

			if ( ! $term || is_wp_error( $term ) ) {
				return;
			}

			$term_ids = array( is_array( $term ) ? (int) $term['term_id'] : (int) $term );
		}

		foreach ( self::manifests() as $manifest ) {
			$post_id = self::find( $manifest['slug'] );

			if ( $post_id ) {
				wp_set_object_terms( $post_id, $term_ids, self::TAXONOMY, true );
			}
		}
	}

	/**
	 * Seed lazily if the templates are missing.
	 *
	 * Activation runs before some Elementor versions finish booting, so the
	 * seed can be missed. Opening the library (or any admin screen) repairs it.
	 *
	 * @return void
	 */
	public function maybe_seed() {
		if ( ! mfh_elementor_active() ) {
			return;
		}

		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		foreach ( self::manifests() as $manifest ) {
			if ( ! isset( $manifest['slug'] ) ) {
				continue;
			}

			if ( self::find( $manifest['slug'] ) ) {
				continue;
			}

			self::seed( false );
			return;
		}
	}

	/**
	 * Add a source column to the library list table.
	 *
	 * @param array $columns Existing columns.
	 *
	 * @return array
	 */
	public function add_column( $columns ) {
		$out = array();

		foreach ( (array) $columns as $key => $label ) {
			$out[ $key ] = $label;

			if ( 'title' === $key ) {
				$out['mfh_source'] = __( 'منبع', 'mainframe-hero' );
			}
		}

		if ( ! isset( $out['mfh_source'] ) ) {
			$out['mfh_source'] = __( 'منبع', 'mainframe-hero' );
		}

		return $out;
	}

	/**
	 * Render the source column.
	 *
	 * @param string $column Column key.
	 * @param int    $post_id Post being rendered.
	 *
	 * @return void
	 */
	public function render_column( $column, $post_id ) {
		if ( 'mfh_source' !== $column ) {
			return;
		}

		if ( get_post_meta( $post_id, self::META_SOURCE, true ) ) {
			echo '<span class="mfh-badge">' . esc_html__( 'Mainframe Hero', 'mainframe-hero' ) . '</span>';
		}
	}

	/**
	 * Re-import the templates, replacing the current copies.
	 *
	 * @return void
	 */
	public function handle_reseed() {
		if ( ! current_user_can( 'publish_posts' ) ) {
			wp_die( esc_html__( 'اجازهٔ انجام این کار را ندارید.', 'mainframe-hero' ) );
		}

		check_admin_referer( 'mfh_reseed' );

		self::seed( true );

		wp_safe_redirect(
			add_query_arg(
				array( 'mfh' => 'reseeded' ),
				admin_url( 'edit.php?post_type=' . self::CPT )
			)
		);
		exit;
	}
}
