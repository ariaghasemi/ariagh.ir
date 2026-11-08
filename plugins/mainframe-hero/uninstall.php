<?php
/**
 * Runs when the plugin is deleted from the Plugins screen.
 *
 * What is removed:
 *   - the Elementor templates this plugin seeded into the library
 *   - the option recording which templates were seeded
 *   - the activation notice transient
 *
 * What is deliberately NOT removed:
 *   - anything the user wrote. A template that has been edited belongs to the
 *     site, not to the plugin, so it is deleted too — that is what "delete the
 *     plugin" means — but no pages, posts, media or unrelated options are
 *     touched, and no other plugin's data is read.
 *
 * The seeding logic lives in the plugin's autoloaded classes, which are not
 * available during uninstall. Rather than boot the whole plugin, the few
 * constants and lookups needed here are inlined.
 *
 * @package MainframeHero
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

/**
 * Delete every library template tagged as ours.
 *
 * @return void
 */
function mfh_uninstall_remove_templates() {
	$query = new WP_Query(
		array(
			'post_type'      => 'elementor_library',
			'post_status'    => 'any',
			'posts_per_page' => 200,
			'fields'         => 'ids',
			'no_found_rows'  => true,
			'meta_query'     => array(
				array(
					'key'     => '_mfh_source',
					'compare' => 'EXISTS',
				),
			),
		)
	);

	if ( empty( $query->posts ) ) {
		return;
	}

	foreach ( $query->posts as $post_id ) {
		wp_delete_post( (int) $post_id, true );
	}
}

mfh_uninstall_remove_templates();

delete_option( 'mfh_imported_templates' );
delete_option( 'mfh_version' );
delete_transient( 'mfh_activated' );

if ( function_exists( 'wp_cache_flush' ) ) {
	wp_cache_flush();
}
