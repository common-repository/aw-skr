<?php
/**
 * Helper Functions
 *
 * @package AW_SKR
 */

if ( ! function_exists( 'write_log' ) ) {
	/**
	 * Log function: write_log
	 *
	 * @param mixed $log .
	 */
	function write_log( $log ) {
		if ( true === WP_DEBUG ) {
			if ( is_array( $log ) || is_object( $log ) ) {
				error_log( print_r( $log, true ) );
			} else {
				error_log( $log );
			}
		}
	}
}

if ( ! function_exists( 'http_post' ) ) {
	/**
	 * Send httpPost / retrieve Array from Json
	 *
	 * @param string $url .
	 * @param array  $data  Post Data.
	 * @return array
	 */
	function http_post( $url, $data = [] ) {
		$curl = curl_init( $url );
		curl_setopt( $curl, CURLOPT_POST, true );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $data );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		$response = json_decode( curl_exec( $curl ), true );
		curl_close( $curl );
		return $response;
	}
}

/**
 * Gets a single option / based on CMB2
 *
 * @param string $key .
 * @param mixed  $default .
 * @return mixed
 */
function aw_skr_get_option( $key = '', $default = false ) {
	if ( function_exists( 'cmb2_get_option' ) ) {
		// Use cmb2_get_option as it passes through some key filters.
		return cmb2_get_option( AW_SKR_SETTINGS, $key, $default );
	}
	// Fallback to get_option if CMB2 is not loaded yet.
	$opts = get_option( AW_SKR_SETTINGS, $default );
	$val  = $default;
	if ( 'all' == $key ) {
		$val = $opts;
	} elseif ( is_array( $opts ) && array_key_exists( $key, $opts ) && false !== $opts[ $key ] ) {
		$val = $opts[ $key ];
	}
	return $val;
}

/**
 * Updates a single option / based on CMB2
 *
 * @param string  $option .
 * @param string  $value .
 * @param boolean $autoload .
 * @return bool
 */
function aw_skr_update_option( $option, $value, $autoload = false ) {
	$settings            = get_option( AW_SKR_SETTINGS );
	$settings[ $option ] = $value;
	return update_option( AW_SKR_SETTINGS, $settings, $autoload );
}


/**
 * Updates an array of options / based on CMB2
 *
 * @param array   $options_values .
 * @param boolean $autoload .
 * @return bool
 */
function aw_skr_update_options( $options_values = [], $autoload = false ) {
	$settings = get_option( AW_SKR_SETTINGS );
	foreach ( $options_values as $option => $value ) {
		$settings[ $option ] = $value;
	}
	return update_option( AW_SKR_SETTINGS, $settings, $autoload );
}

/**
 * Deletes a single option / based on CMB2
 *
 * @param string $option .
 * @return bool
 */
function aw_skr_delete_option( $option ) {
	$settings = get_option( AW_SKR_SETTINGS );
	unset( $settings[ $option ] );
	return update_option( AW_SKR_SETTINGS, $settings );
}

/**
 * Deletes an array of options / based on CMB2
 *
 * @param array $options .
 * @return bool
 */
function aw_skr_delete_options( $options = [] ) {
	$settings = get_option( AW_SKR_SETTINGS );
	foreach ( $options as $option ) {
		unset( $settings[ $option ] );
	}
	return update_option( AW_SKR_SETTINGS, $settings );
}

/**
 * Returns file version (workaround for cache problems).
 *
 * @param string $file .
 * @return string
 */
function aw_skr_get_file_version( $file ) {
	$file_version = AW_SKR_PVERSION;
	$file = AW_SKR_PDIR . $file;
	if ( file_exists( $file ) ) {
		$file_mtime = filemtime( $file );
		$file_atime = fileatime( $file );
		$time = $file_mtime > $file_atime ? $file_mtime : $file_atime;
		$file_version .= '.' . date( 'YmdHis', $time );
	}
	return $file_version;
}

/**
 * Mail Convert.
 *
 * @param string $text .
 * @return string
 */
function aw_skr_mail_convert( $text ) {
	return
	rawurlencode(
		html_entity_decode(
			iconv('UTF-8', 'ASCII//TRANSLIT',
				str_replace(
					[ 'ä', 'ö', 'ü', 'Ä', 'Ö', 'Ü', 'ß' ],
					[ 'ae', 'oe', 'ue', 'Ae', 'Oe', 'Ue', 'ss' ],
					$text
				)
			)
		)
	);
}

/**
 * Mail Link.
 *
 * @param string         $text .
 * @param boolean|string $subject .
 * @param boolean|string $to .
 * @param boolean|string $linkname .
 * @param boolean|string $class .
 * @return string
 */
function aw_skr_mail_link( $text, $subject = false, $to = false, $linkname = false, $class = false ) {
	if ( ! $to ) {
		return;
	}
	if ( ! $linkname ) {
		$linkname = $to;
	}
	$msubject = aw_skr_mail_convert( $subject );
	$mbody = aw_skr_mail_convert( $text );
	$mlink = "mailto:$to?Subject=" . $msubject . '&Body=' . $mbody;

	return "<a href='$mlink' class='$class'>$linkname</a>";
}

/**
 * Workaround for issue with not displaying any Admin Settings Page on some servers.
 *
 * @since 1.0.3
 *
 * @param object $hookup .
 * @return void
 */
function aw_skr_admin_settings_page_output( $hookup ) {
	AW_SKR_Admin::settings_page_output( $hookup );
}
