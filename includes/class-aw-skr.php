<?php
/**
 * The file that defines the core plugin class
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/includes
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      AW_SKR_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * Indicates, if the plugin is active
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      $status_active
	 */
	protected $status_active;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		$this->load_dependencies();
		$this->define_admin_hooks();

		$this->set_active_status();

		if ( $this->is_active() ) {
			$this->define_public_hooks();
			$this->define_widget_hooks();
		}

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - AW_SKR_Loader. Orchestrates the hooks of the plugin.
	 * - AW_SKR_Admin. Defines all hooks for the admin area.
	 * - AW_SKR_Widget. Defines the Widget.
	 * - AW_SKR_Public. Defines all hooks for the public side of the site.
	 * - helpers
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once AW_SKR_PDIR . 'includes/class-aw-skr-loader.php';

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once AW_SKR_PDIR . 'admin/class-aw-skr-admin.php';

		/**
		 * The widget class.
		 */
		require_once AW_SKR_PDIR . 'includes/class-aw-skr-widget.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once AW_SKR_PDIR . 'public/class-aw-skr-public.php';

		/**
		 * Load CMB2.
		 */
		if ( file_exists( AW_SKR_PDIR . 'includes/lib/cmb2/init.php' ) ) {
			/**
			 * Load CMB2.
			 */
			require_once AW_SKR_PDIR . 'includes/lib/cmb2/init.php';
		} elseif ( file_exists( AW_SKR_PDIR . 'includes/lib/CMB2/init.php' ) ) {
			/**
			 * Load CMB2.
			 */
			require_once AW_SKR_PDIR . 'includes/lib/CMB2/init.php';
		}

		/**
		 * Loads the wpbp_get_template_part() function
		 */
		require_once AW_SKR_PDIR . 'includes/helper-template.php';

		/**
		 * Loads the csvData and csvFilter classes
		 */
		require_once AW_SKR_PDIR . 'includes/helper-csv.php';

		/**
		 * Helper functions
		 */
		require_once AW_SKR_PDIR . 'includes/helper-functions.php';

		$this->loader = new AW_SKR_Loader();

	}

	/**
	 * Register Widget Hooks.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @return   void
	 */
	private function define_widget_hooks() {

		add_action( 'widgets_init', function() {
			register_widget( 'aw_skr_widget' );
		} );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new AW_SKR_Admin();

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_scripts' );
		$this->loader->add_action( 'cmb2_admin_init', $plugin_admin, 'cmb_admin_page' );

		$this->loader->add_filter( 'plugin_action_links_' . AW_SKR_PBASENAME, $plugin_admin, 'add_action_links' );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new AW_SKR_Public();

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );
		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_scripts' );
		$this->loader->add_action( 'wp_ajax_aw_skr_ajax', $plugin_public, 'skr_result_ajax' );
		$this->loader->add_action( 'wp_ajax_nopriv_aw_skr_ajax', $plugin_public, 'skr_result_ajax' );
		$this->loader->add_filter( 'query_vars', $plugin_public, 'query_vars_filter' );

		$plugin_public->register_shortcodes();

	}

	/**
	 * Sets plugin active status
	 *
	 * @since     1.0.0
	 */
	private function set_active_status() {
		$status = false;
		if ( aw_skr_get_option( 'setup' ) === 'ok' ) {
			$status = true;
		}
		$this->status_active = $status;
	}

	/**
	 * Returns plugin active status
	 *
	 * @since     1.0.0
	 * @return    bool
	 */
	public function is_active() {
		return $this->status_active;
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    AW_SKR_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

}
