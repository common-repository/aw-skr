<?php
/**
 * Fired during plugin deactivation
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/includes
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR_Deactivator {

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses
	 *                              "Network Deactivate" action, false if
	 *                              WPMU is disabled or plugin is
	 *                              deactivated on an individual blog.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function deactivate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids.
				$blogs = get_sites();
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->blog_id );
					self::single_deactivate();
					restore_current_blog();
				}
				return;
			}
		}
		self::single_deactivate();
	}
	/**
	 * Fired for each blog when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function single_deactivate() {
		// @TODO: Define deactivation functionality here
		// DO NOTHING HERE
	}

}
