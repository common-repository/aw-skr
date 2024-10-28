<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/public
 * @author     Active Websight <info@active-websight.de>
 */
class AW_SKR_Public {

	/**
	 * The needed form vars for SKR.
	 *
	 * @var array
	 */
	protected $form_vars_form = [
		'skr-einkommen-ehemann' => [
			'default'  => '0',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => 'empty',
			'format'   => 'floatval',
		],
		'skr-einkommen-ehefrau' => [
			'default'  => '0',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => 'empty',
			'format'   => 'floatval',
		],
		'skr-kinder'            => [
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => 'empty',
			'format'   => 'intval',
		],
		'skr-rversicherungen'   => [
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => 'empty',
			'format'   => 'intval',
		],
		'skr-unter3'            => [
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => '!isset',
			'format'   => 'intval',
		],
		'skr-ehevertrag'        => [
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => '!isset',
			'format'   => 'intval',
		],
		'skr-datenschutz'       => [
			'default'  => '',
			'sanitize' => 'sanitize_text_field',
			'errorif'  => '!isset',
			'format'   => 'intval',
		],
	];

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		wp_enqueue_style( AW_SKR_PNAME, AW_SKR_PURL . 'public/css/public.css', array(), aw_skr_get_file_version( 'public/css/public.css' ), 'all' );

	}

	/**
	 * Register the JavaScript for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		wp_enqueue_script( AW_SKR_PNAME, AW_SKR_PURL . 'public/js/public.min.js', array( 'jquery' ), aw_skr_get_file_version( 'public/js/public.min.js' ), false );

		wp_localize_script(
			AW_SKR_PNAME, AW_SKR_PNAMECAMEL, array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'security' => wp_create_nonce( 'skr-formular-ajax' ),
			)
		);

	}

	/**
	 * Make Vars public.
	 *
	 * @param array $vars .
	 * @return array
	 */
	public function query_vars_filter( $vars ) {
		foreach ( array_keys( $this->form_vars_form ) as $var ) {
			$vars[] = $var;
		}
		return $vars;
	}

	/**
	 * Register the Shortcodes
	 *
	 * @since    1.0.0
	 */
	public function register_shortcodes() {
		add_shortcode( 'aw_skr', array( $this, 'shortcode_aw_skr' ) );
	}

	/**
	 * Placeholder for later use
	 *
	 * @param array  $atts .
	 * @param string $content .
	 * @return string
	 */
	public function shortcode_aw_skr( $atts, $content = null ) {
		$atts = shortcode_atts(
			array(
				'title' => 'Scheidungskosten&shy;rechner',
				'style' => '',
			), $atts, 'aw_skr'
		);
		// Updates Request, adds defaults, if data is missing.
		// $request_include = true, $request_saveback = true.
		$data = $this->merge_request_data_with_defaults_and_request( false, false );
		// Additional vars to use in template.
		$title      = $atts['title'];
		$style      = '' !== $atts['style'] ? 'skr--' . $atts['style'] : '';
		$dsgvo      = $this->dsgvo_output();
		$grecaptcha = $this->grecaptcha_output();
		$poweredby  = $this->poweredby_output();
		/**
		 * Loads the /templates/aw-skr-formular.php file
		 */
		ob_start();
		include wpbp_get_template_part( dirname( AW_SKR_PBASENAME ), AW_SKR_PNAME, 'formular', false );
		$ret = ob_get_contents();
		ob_end_clean();

		return $ret;
	}

	/**
	 * Handles and returns all ajax requests.
	 */
	public function skr_result_ajax() {

		if ( ! check_ajax_referer( 'skr-formular-ajax', 'security' ) ) {
			wp_send_json_error( 'Sorry, nicht erlaubt.' );
			wp_die();
		}

		// Sanify all the Request data.
		$this->sanify_data();

		// Updates Request, adds defaults, if data is missing.
		// $request_include = true, $request_saveback = true.
		$query_args = $this->merge_request_data_with_defaults_and_request( true, false );

		// Edit form.
		if ( 'edit' === $query_args['do'] ) {

			// Calculate Result.
			$return = $this->skr_result_calc( $query_args );

			wp_send_json( $return );

		}

		wp_send_json_error( 'Keine Daten gefunden.' );
		wp_die();

	}

	/**
	 * Returns the calculated data and the raw_data.
	 *
	 * @param array $query_args .
	 * @return array
	 */
	public function skr_result_calc( $query_args = [] ) {
		// Clean.
		foreach ( [ 'timestamp', 'action', 'security', 'do' ] as $key ) {
			unset( $query_args[ $key ] );
		}

		// RawData.
		$rawdata = $query_args;

		// Data preparation/calculation.
		$data = $query_args;

		// Vars.
		$data['datum'] = date( 'd.m.Y' );
		foreach ( [ 'skr-einkommen-ehemann', 'skr-einkommen-ehefrau' ] as $key ) {
			$data[ $key ] = number_format( $query_args[ $key ], 2, ',', '.' ) . ' €';
		}
		if ( 0 === $query_args['skr-kinder'] ) {
			$data['skr-kinder'] = 'Keine';
		}
		if ( 0 === $query_args['skr-rversicherungen'] ) {
			$data['skr-rversicherungen'] = 'Keine';
		}
		$data['skr-unter3']     = 1 === $query_args['skr-unter3'] ? 'Ja' : 'Nein';
		$data['skr-ehevertrag'] = 1 === $query_args['skr-ehevertrag'] ? 'Ja' : 'Nein';

		// Calc1 reduziert und (N)ormal.
		$data['gw-ehe-gemeinsames-einkommen'] = $query_args['skr-einkommen-ehemann'] + $query_args['skr-einkommen-ehefrau'];
		$data['gw-ehe-kinder']                = ( 0 === $query_args['skr-kinder'] ? 0 : (int) $query_args['skr-kinder'] * 250 );
		$data['gw-ehe-ergebnis-monat']        = $data['gw-ehe-gemeinsames-einkommen'] - $data['gw-ehe-kinder'];
		$data['gw-ehe-ergebnis-monat']        = $data['gw-ehe-ergebnis-monat'] < 0 ? 0 : $data['gw-ehe-ergebnis-monat'];

		$data['gw-ehe-ergebnis'] = 3 * $data['gw-ehe-ergebnis-monat'];
		$data['gw-mw-ehe']       = 0;
		if ( $data['gw-ehe-ergebnis'] < 3000 ) {
			$data['gw-mw-ehe']       = 3000;
			$data['gw-ehe-ergebnis'] = $data['gw-mw-ehe'];
		}
		// - Ehe unter 3 >> 0
		// - Versorgungsausgleich durch Ehevertrag ausgeschlossen >> 1000
		// - Anzahl Versicherungen x 10% von Ehe-3-Monatswert, aber Minimum 1000
		$data['gw-versorgung-10']  = 0.1 * $data['gw-ehe-ergebnis'];
		$data['gw-versorgung-rvs'] = (int) $query_args['skr-rversicherungen'];
		$data['gw-mw-rvs']         = 0;
		$calc_versorgung_rvs       = $data['gw-versorgung-rvs'] * $data['gw-versorgung-10'];
		if ( $calc_versorgung_rvs < 1000 ) {
			$data['gw-mw-rvs']   = 1000;
			$calc_versorgung_rvs = $data['gw-mw-rvs'];
		}
		$data['gw-versorgung']     = 1 === $query_args['skr-unter3']
										? 0
										: (
											1 === $query_args['skr-ehevertrag']
											? 1000
											: (
												$calc_versorgung_rvs
											)
										);
		$data['gw-ehe']            = $data['gw-ehe-ergebnis'] + $data['gw-versorgung'];

		// CSV. Gerichts und Anwaltskosten.
		ob_start();
		$tabelle_csv_file = 'tabelle.csv';
		if (date( 'Y', current_time( 'timestamp', 0 ) ) >= '2021') {
			$tabelle_csv_file_2021 = 'tabelle_2021.csv';
			if (file_exists(AW_SKR_PDIR . 'templates/' . $tabelle_csv_file_2021)) {
				$tabelle_csv_file = $tabelle_csv_file_2021;
			}
		}
		include AW_SKR_PDIR . 'templates/' . $tabelle_csv_file;
		$csvdata = ob_get_contents();
		ob_end_clean();

		// Ergebnis: Kosten normal.
		$csv        = new csvData( $csvdata );
		$csv_arr    = $csv->gegenstandswert( '~>', $data['gw-ehe'] )->toArray();
		$csv_result = array_shift( $csv_arr );
		// Trimming CSV Data.
		foreach ( $csv_result as $key => $val ) {
			unset( $csv_result[ $key ] );
			$csv_result[ trim( $key ) ] = trim( $val );
		}
		$data['gerichtskosten']   = floatval( $csv_result['gericht'] );
		$data['anwaltskosten']    = floatval( $csv_result['anwalt'] );
		$data['scheidungskosten'] = $data['anwaltskosten'] + $data['gerichtskosten'];

		// Format.
		$format_keys = [ 'gw-ehe-gemeinsames-einkommen', 'gw-ehe-kinder', 'gw-ehe-ergebnis-monat', 'gw-ehe-ergebnis', 'gw-reduktion', 'gw-versorgung-10', 'gw-versorgung', 'gw-ehe', 'anwaltskosten', 'gerichtskosten', 'gerichtskosten-halb', 'scheidungskosten' ];
		foreach ( $format_keys as $key ) {
			if ( ! isset( $data[ $key ] ) || false === $data[ $key ] ) {
				continue;
			}
			$data[ $key ] = number_format( $data[ $key ], 2, ',', '.' ) . ' €';
		}

		// ADD E-Mail.
		$data['kontaktbutton'] = $this->kontaktbutton_output();

		// Return.
		return [
			'data'    => $data,
			'rawdata' => $rawdata,
		];

	}

	/**
	 * Returns the mail contakt link, if option is set to true.
	 *
	 * @return string
	 */
	public function kontaktbutton_output() {
		$ret = '';
		if ( 'on' === aw_skr_get_option( 'send_request' ) ) {
			$text = aw_skr_get_option( 'request_mail_body' );
			if ( ! $text ) {
				$text = "Sehr geehrte Damen und Herren,\n\nMit freundlichen Grüßen\n\n\nName: \nTelefon: \nE-Mail: ";
			}
			$subject = aw_skr_get_option( 'request_mail_subject' );
			if ( ! $subject ) {
				$subject = get_option( 'blogname' ) . ' - Anfrage';
			}
			$email = aw_skr_get_option( 'request_mail_email' );
			if ( ! $email ) {
				$email = get_option( 'admin_email' );
			}
			$label = aw_skr_get_option( 'request_mail_label' );
			if ( ! $label ) {
				$label = 'E-Mail Kontakt';
			}
			$ret = aw_skr_mail_link( $text, $subject, $email, $label, 'button mail' );
		}
		return $ret;
	}

	/**
	 * Returns the dsgvo text, if option is set to true.
	 *
	 * @return string
	 */
	public function dsgvo_output() {
		$ret = '';
		if ( 'on' === aw_skr_get_option( 'show_dsgvo' ) ) {
			$text = aw_skr_get_option( 'dsgvo_text' );
			if ( ! empty( $text ) ) {
				$ret = $text;
			}
		}
		return $ret;
	}

	/**
	 * Returns the powered by section, if option is set to true.
	 *
	 * @return string
	 */
	public function poweredby_output() {
		$ret = '';
		if ( 'on' === aw_skr_get_option( 'frontend_show_plugin_link' ) ) {
			$ret .= '<div class="skr-powered">';
			$ret .= 'Der <a href="http://scheidungskostenrechner.org" target="_blank">Scheidungskosten&shy;rechner <img src="' . AW_SKR_PURL . 'public/images/skr-icon.png" alt="Scheidungskostenrechner.org" title="Scheidungskostenrechner.org" width="" height="36" /></a>';
			$ret .= '<span>als Plugin für Ihre Homepage – Kostenfrei online Scheidungskosten berechnen.</span>';
			$ret .= '</div>';
		}
		return $ret;
	}


	/**
	 * Returns the g-reCAPTCHA code with Script tag.
	 *
	 * @return string
	 */
	public function grecaptcha_output() {
		$ret = '<div class="timestamp" style="display:none">Bitte geben Sie die aktuelle Uhrzeit ein: <input type="text" name="timestamp" id="timestamp" tabindex="-1"></div>';
		return $ret;
	}

	/**
	 * Checks, if there is a recaptcha, if yes, verifies the recaptcha.
	 *
	 * @return boolean
	 */
	public function grecaptcha_verify() {
		$success  = true;
		$req_data = $this->merge_request_data_with_defaults_and_request( true, false );

		if ( isset( $req_data['timestamp'] ) && '' !== $req_data['timestamp'] ) {
			$success = false;
		}

		return $success;
	}

	/**
	 * Get Form Vars
	 *
	 * @param boolean $data_key  all settings if false, else only the key.
	 * @return array
	 */
	protected function get_form_vars_data_key( $data_key = false ) {
		$key = 'form';
		$ret = [];
		foreach ( $this->{ 'form_vars_' . $key } as $key => $settings ) {
			$data = $settings;
			if ( $data_key && key_exists( $data_key, $settings ) ) {
				$data = $settings[ $data_key ];
			}
			$ret[ $key ] = $data;
		}
		return $ret;
	}

	/**
	 * Verifiy and Sanitize Request Data. But only once.
	 *
	 * @return boolean
	 */
	protected function sanify_data() {
		$form_var_key = 'form';
		$has_error    = false;

		foreach ( $this->{ 'form_vars_' . $form_var_key } as $key => $settings ) {
			$el_error = false;
			if ( isset( $_REQUEST[ $key ] ) ) {
				// sanitize.
				if ( '' !== $settings['sanitize'] ) {
					$_REQUEST[ $key ] = call_user_func( $settings['sanitize'], $_REQUEST[ $key ] );
				}
				// errorcheck.
				switch ( $settings['errorif'] ) {
					case '':
						break;
					case 'empty':
						if ( '' === $_REQUEST[ $key ] ) {
							$el_error[] = 'errorif:empty';
						}
						break;
				}
				// format.
				switch ( $settings['format'] ) {
					case '':
						break;
					case 'floatval':
					case 'intval':
						if ( '' !== $_REQUEST[ $key ] ) {
							$_REQUEST[ $key ] = call_user_func( $settings['format'], $_REQUEST[ $key ] );
						}
						break;
				}
			} elseif ( '!isset' === $settings['errorif'] ) {
				$el_error[] = 'errorif:!isset';
			}
			if ( $el_error ) {
				$has_error[ $key ] = $el_error;
			}
		}
		return $has_error;
	}

	/**
	 * Verify nonce
	 *
	 * @param string $nonce_key  .
	 * @return boolean
	 */
	protected function verify_nonce( $nonce_key = '' ) {
		if ( '' === $nonce_key ) {
			return true;
		}
		return isset( $_REQUEST['_wpnonce'] ) && wp_verify_nonce( $_REQUEST['_wpnonce'], $nonce_key );
	}


	/**
	 * Gets all request data within the keys.
	 *
	 * @param array $keys .
	 * @return array
	 */
	protected function get_existing_request_data( $keys = [] ) {
		$data = [];
		foreach ( $keys as $key ) {
			if ( isset( $_REQUEST[ $key ] ) ) {
				$data[ $key ] = $_REQUEST[ $key ];
			}
		}
		return $data;
	}

	/**
	 * Merges Request data and/or defaults with requested data. But only form_vars_form.
	 *
	 * @param boolean $request_include .
	 * @param boolean $request_saveback .
	 * @return array
	 */
	protected function merge_request_data_with_defaults_and_request( $request_include = true, $request_saveback = true ) {
		// All Request Data, so nothing gets lost.
		$data = $request_include ? $_REQUEST : [];
		// Merge Default Values, if nothing is there.
		$data = array_merge( $data, $this->get_form_vars_data_key( 'default' ) );
		// Merge sent plugin request data.
		$data = array_merge( $data, $this->get_existing_request_data( array_keys( $this->form_vars_form ) ) );
		// Saveback.
		if ( $request_saveback ) {
			$_REQUEST = array_merge( $_REQUEST, $data );
		}
		// Return.
		return $data;
	}

}
