<?php
/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/admin
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR_Admin {

	/**
	 * Slug of the plugin screen.
	 *
	 * @var string
	 */
	protected $admin_view_page = 'toplevel_page_' . AW_SKR_SETTINGS;

	/**
	 * Default settings for CMB2 forms
	 *
	 * @var array $admin_page_cmb_defaults .
	 */
	protected $admin_page_cmb_defaults = [
		'id'           => AW_SKR_SETTINGS . '-page',
		'title'        => 'Der Scheidungskostenrechner <small>(von RA Christian Kieppe)</small>',
		'menu_title'   => 'AW SKR',
		'capabilites'  => 'manage_options',
		'object_types' => array( 'options-page' ),
		'option_key'   => AW_SKR_SETTINGS,
		'icon_url'     => 'dashicons-portfolio',
		'display_cb'   => 'aw_skr_admin_settings_page_output', // Override the options-page form output (CMB2_Hookup::options_page_output()).
	];

	/**
	 * The cmb2_admin_init includes
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function cmb_admin_page() {
		$settings = 'cmb-settings.php';
		if ( ! aw_skr_get_option( 'agbs_accepted' ) ) {
			$settings = 'cmb-agbs.php';
		}
		/**
		 * Required needed settings part.
		 */
		include_once AW_SKR_PDIR . 'admin/partials/' . $settings;
	}

	/**
	 * CMB2 callback for displaying the admin page
	 *
	 * @param object $hookup .
	 * @return void
	 */
	public static function settings_page_output( $hookup ) {
		$check_agbs  = aw_skr_get_option( 'agbs_accepted' );
		$check_setup = aw_skr_get_option( 'setup' ) === 'ok';
		$check_ready = $check_setup;

		?>
		<div class="wrap cmb2-options-page option-<?php echo $hookup->option_key; ?>">
			<?php if ( $hookup->cmb->prop( 'title' ) ) : ?>
				<h2><?php echo wp_kses_post( $hookup->cmb->prop( 'title' ) ); ?></h2>
			<?php endif; ?>
			<div class="stati">
				<div class="status">AGBs <span class="dashicons dashicons-<?php echo $check_agbs ? 'yes' : 'no'; ?>"></span></div>
				<div class="status">Setup <span class="dashicons dashicons-<?php echo $check_setup ? 'yes' : 'no'; ?>"></span></div>
				<div class="status">Einsatzbereit <span class="dashicons dashicons-<?php echo $check_ready ? 'smiley' : 'no'; ?>"></span></div>
			</div>
			<?php if ( $hookup->cmb->prop( 'description' ) ) : ?>
				<h2><?php echo wp_kses_post( $hookup->cmb->prop( 'description' ) ); ?></h2>
			<?php endif; ?>
			<form class="cmb-form" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" method="POST" id="<?php echo $hookup->cmb->cmb_id; ?>" enctype="multipart/form-data" encoding="multipart/form-data">
				<input type="hidden" name="action" value="<?php echo esc_attr( $hookup->option_key ); ?>">
				<?php $hookup->options_page_metabox(); ?>
				<?php submit_button( esc_attr( $hookup->cmb->prop( 'save_button' ) ), 'primary', 'submit-cmb' ); ?>
			</form>
			<?php
			if ( $check_agbs ) :
				echo "<div style='font-size: .9em; color: #777'>";
				AW_SKR_Admin::settings_load_agbs(false, false);
				echo "</div>";
			endif;
			?>
		</div>
		<?php
	}

	/**
	 * CMB2 callback, to sanitize textarea
	 *
	 * @param string|array $value .
	 * @param array        $field_args .
	 * @param object       $field .
	 * @return array
	 */
	public static function sanitize_mail_body( $value, $field_args, $field ) {
		$sanitized_value = strip_tags( $value );
		$sanitized_value = sanitize_textarea_field( $value );
		return $sanitized_value;
	}

	/**
	 * CMB2 callback before_row, to display the agbs
	 *
	 * @param array  $field_args .
	 * @param object $field .
	 * @return void
	 */
	public static function settings_load_agbs( $field_args, $field ) {
		ob_start();
		/**
		 * Includes AGBs file.
		 */
		include AW_SKR_PDIR . 'admin/partials/agbs.txt';
		$agbs = ob_get_contents();
		ob_end_clean();
		$agbs = nl2br( $agbs );
		echo '<div class="cmb-row text-agbs">' . $agbs . '</div>';
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since 1.0.0
	 *
	 * @param array $links Array of links.
	 *
	 * @return array
	 */
	public function add_action_links( $links ) {
		return array_merge(
			[
				'settings' => '<a href="' . admin_url( 'admin.php?page=' . AW_SKR_SETTINGS ) . '">Einstellungen</a>',
			],
			$links
		);
	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		$screen = get_current_screen();
		if ( $this->admin_view_page === $screen->id ) {
			wp_enqueue_style( AW_SKR_PNAME . '-settings-styles', AW_SKR_PURL . 'admin/css/settings.css', array( 'dashicons' ), aw_skr_get_file_version( 'admin/css/settings.css' ), 'all' );
		}

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();
		if ( $this->admin_view_page === $screen->id ) {
			wp_enqueue_script( AW_SKR_PNAME . '-settings-scripts', AW_SKR_PURL . 'admin/js/settings.min.js', array( 'jquery' ), aw_skr_get_file_version( 'admin/js/settings.min.js' ), false );
		}

	}

}
