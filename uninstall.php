<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

uninstall_aw_skr();

/**
 * Uninstall script for AW SKR.
 *
 * @return void
 */
function uninstall_aw_skr() {
	if ( function_exists( 'is_multisite' ) && is_multisite() ) {
		if ( false === is_super_admin() ) {
			return;
		}
		$blogs = get_sites();
		foreach ( $blogs as $blog ) {
			switch_to_blog( $blog->blog_id );
			aw_skr_uninstall_remove_options();
			restore_current_blog();
		}
	} else {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}
		aw_skr_uninstall_remove_options();
	}
}

/**
 * Remove AW SKR Options
 *
 * @return void
 */
function aw_skr_uninstall_remove_options() {
	$aw_skr_options = [ 'aw-skr-version', 'aw-skr-settings' ];
	foreach ( $aw_skr_options as $option ) {
		delete_option( $option );
	}
}
