<?php
/**
 * AW Scheidungskostenrechner Plugin.
 *
 * @link              http://scheidungskostenrechner.org
 * @since             1.0.0
 * @package           AW_SKR
 *
 * @wordpress-plugin
 * Plugin Name:       Scheidungskostenrechner
 * Plugin URI:        http://scheidungskostenrechner.org/
 * Description:       Der Scheidungskostenrechner von RA Christian Kieppe
 * Version:           1.0.6
 * Author:            Active Websight
 * Author URI:        http://www.active-websight.de
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/*
CONSTANTS
*/
define( 'AW_SKR_PVERSION', '1.0.6' );
define( 'AW_SKR_PNAME', 'aw-skr' ); // Used for: i18n, styles/scripts naming, template_part.
define( 'AW_SKR_PNAMECAMEL', 'AW_SKR' ); // Used for: ajax scripts.
define( 'AW_SKR_PDIR', plugin_dir_path( __FILE__ ) ); // Absolute Path, for include/require.
define( 'AW_SKR_PURL', plugin_dir_url( __FILE__ ) ); // Webpath, for public styles/scripts/files.
define( 'AW_SKR_PBASENAME', plugin_basename( __FILE__ ) ); // Used for: hooks, i18n.
define( 'AW_SKR_SETTINGS', 'aw-skr-settings' ); // The used settings-store-object.

/**
 * The code that runs during plugin activation.
 */
function activate_deactivate_aw_skr() {
	/**
	 * Activator
	 */
	require_once AW_SKR_PDIR . 'includes/class-aw-skr-activator.php';
	/**
	 * Deactivator
	 */
	require_once AW_SKR_PDIR . 'includes/class-aw-skr-deactivator.php';

	add_action( 'wpmu_new_blog', array( 'AW_SKR_Activator', 'activate_new_site' ) );
	add_action( 'admin_init', array( 'AW_SKR_Activator', 'upgrade_procedure' ) );

	register_activation_hook( __FILE__, array( 'AW_SKR_Activator', 'activate' ) );
	register_deactivation_hook( __FILE__, array( 'AW_SKR_Deactivator', 'deactivate' ) );
}
activate_deactivate_aw_skr();

/**
 * The core plugin class.
 */
require AW_SKR_PDIR . 'includes/class-aw-skr.php';

/**
 * Begins execution of the plugin.
 *
 * @since    1.0.0
 */
function run_aw_skr() {

	$plugin = new AW_SKR();
	$plugin->run();

}
run_aw_skr();
