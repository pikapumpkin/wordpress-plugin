<?php
if ( ! defined( 'ABSPATH' ) && ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	die();
}
/**
 * Contains uninstall routines to clean up data on plugin deletion.
 */
function idx_delete_plugin_data() {
	// Delete the assigned dynamic wrapper page ID. Legacy - we remove all idx-wrapper posts later.
	$page_id = get_option( 'idx_broker_dynamic_wrapper_page_id' );
	if ( $page_id ) {
		wp_delete_post( $page_id, true );
	}

	global $wpdb;
	// Delete pseudo-transients.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%idx_%_cache' ) );
	// Delete omnibar data.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%idx_omnibar%' ) );
	// Delete middleware widgets.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%widget_idx%' ) );
	// Delete dismissed notices.
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%idx-notice-dismissed%' ) );
	// Delete any other idx_broker prefixed options. *Includes API key
	$wpdb->query( $wpdb->prepare( "DELETE FROM $wpdb->options WHERE option_name LIKE %s", '%idx_broker%' ) );

	// Delete all IDX page posts.
	$idx_pages = get_posts(
		array(
			'post_type'   => 'idx_page',
			'numberposts' => -1,
		)
	);
	foreach ( $idx_pages as $post ) {
		wp_delete_post( $post->ID, true );
	}

	// Delete all wrapper posts.
	$idx_wrappers = get_posts(
		array(
			'post_type'   => 'idx-wrapper',
			'numberposts' => -1,
		)
	);
	foreach ( $idx_wrappers as $post ) {
		wp_delete_post( $post->ID, true );
	}
}

// Run cleanup method.
idx_delete_plugin_data();