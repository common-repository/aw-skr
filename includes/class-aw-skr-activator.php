<?php
/**
 * Fired during plugin activation
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/includes
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR_Activator {

	/**
	 * Fired when the plugin is activated.
	 *
	 * @param boolean $network_wide True if active in a multiste, false if classic site.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public static function activate( $network_wide ) {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			if ( $network_wide ) {
				// Get all blog ids.
				$blogs = get_sites();
				foreach ( $blogs as $blog ) {
					switch_to_blog( $blog->blog_id );
					self::single_activate();
					restore_current_blog();
				}
				return;
			}
		}
		self::single_activate();
	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @param integer $blog_id ID of the new blog.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function activate_new_site( $blog_id ) {
		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}
		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();
	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	private static function single_activate() {
		self::upgrade_procedure();
	}

	/**
	 * Upgrade procedure
	 *
	 * @return void
	 */
	public static function upgrade_procedure() {
		if ( is_admin() ) {
			$version = get_option( 'aw-skr-version' );
			// Remove 'php_version'-setting from 1.0.1.
			if ( version_compare( '1.0.1', $version, '==' ) ) {
				aw_skr_delete_option( 'php_version' );
			}
			if ( version_compare( AW_SKR_PVERSION, $version, '>' ) ) {
				update_option( 'aw-skr-version', AW_SKR_PVERSION );
			}

		}
	}

}
