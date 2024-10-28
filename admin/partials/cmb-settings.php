<?php
/**
 * The settings page
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/admin/partials
 * @author     Active Websight <info@active-websight.de>
 */

$options = array_merge( $this->admin_page_cmb_defaults, [
	'id'          => AW_SKR_SETTINGS . '-settings',
	'description' => 'Einstellungen ...',
]);

$cmb = new_cmb2_box( $options );

if ( aw_skr_get_option( 'setup' ) !== 'ok' ) {
	$cmb->add_field( array(
		'name' => 'Setup',
		'type' => 'title',
		'id'   => 'first_setup',
		'after_field'  => ( function ( $field_args, $field ) {
			echo '<p>Nehmen Sie die gewünschten Einstellungen des Widgets/Plugins vor.<br><strong>Bitte beachten Sie</strong>, dass das Widget/Plugin erst mit dem Klick auf den untenstehenden Button "Änderungen speichern" aktiv wird.</p>';
		} ),
	) );
} else {
	$cmb->add_field( array(
		'name' => 'Verwendung',
		'type' => 'title',
		'id'   => 'usage',
		'after_field'  => ( function ( $field_args, $field ) {
			echo '<p>Verwenden Sie den Scheidungskostenrechner, indem Sie das integrierte Widget an gewünschter Position platzieren (gehe zu <a href="' . admin_url( 'widgets.php' ) . '">Widgets</a>).</p>';
			echo '<p>Alternativ können Sie den Shortcode <code>[aw_skr]</code> an der gewünschten Stelle in einer Seite/Beitrag oder im Template verwenden.</p>';
			echo '<p>Um den Titel und/oder den Style des mittels Shortcode eingebundenen Scheidungskostenrechners zu verändern, können Sie den Shortcode folgendermaßen ergänzen:</p>';
			echo '<p><code>[aw_skr title="Eigener Titel" style="wide"]</code> &mdash; wobei <code>title</code> alles sein kann, <code>style</code> aber lediglich "" oder "wide".</p>';
		} ),
	) );
}

$cmb->add_field( array(
	'name' => 'DSGVO-Einstellungen',
	'desc' => 'Einstellungen für die DSGVO - Datenschutz Grundverordnung',
	'type' => 'title',
	'id'   => 'dsgvo',
) );
$cmb->add_field( array(
	'name'    => 'DSGVO-Text darstellen?',
	'desc'    => 'DSGVO-Text vor dem "Scheidungskosten berechnen"-Button darstellen',
	'id'      => 'show_dsgvo',
	'type'    => 'checkbox',
	'default' => 'on',
) );
$cmb->add_field( array(
	'name'    => 'DSGVO-Text',
	'desc'    => 'Geben Sie den Text ein, der vor dem "Scheidungskosten berechnen"-Button dargestellt werden soll.<br>Standard: "Ich habe zur Kenntnis genommen, dass meine Daten bei der Berechnung an den Webserver dieser Seite gesendet werden."',
	'id'      => 'dsgvo_text',
	'type'    => 'textarea_small',
	'default' => 'Ich habe zur Kenntnis genommen, dass meine Daten bei der Berechnung an den Webserver dieser Seite gesendet werden.',
) );

$cmb->add_field( array(
	'name' => 'Kontakt-Link & E-Mail-Einstellungen',
	'desc' => 'E-Mail-Einstellungen für Interessenten',
	'type' => 'title',
	'id'   => 'request_mail',
) );
$cmb->add_field( array(
	'name' => 'Kontakt-Link unter dem Ergebnis darstellen?',
	'desc' => 'Link zur Kontaktaufnahme darstellen',
	'id'   => 'send_request',
	'type' => 'checkbox',
) );
$cmb->add_field( array(
	'name'    => 'Empfänger',
	'desc'    => 'An welche E-Mail-Adresse sollen Anfragen weitergeleitet werden.',
	'id'      => 'request_mail_email',
	'type'    => 'text_email',
	'default' => get_option( 'admin_email' ),
) );
$cmb->add_field( array(
	'name'    => 'Betreff',
	'id'      => 'request_mail_subject',
	'type'    => 'text',
	'default' => get_option( 'blogname' ) . ' - Anfrage',
) );
$cmb->add_field( array(
	'name'            => 'Vorlage',
	'desc'            => 'Der vorausgefüllte E-Mail-Text. Achtung: Umlaute werden konvertiert (Ä > Ae...).',
	'id'              => 'request_mail_body',
	'type'            => 'textarea',
	'sanitization_cb' => 'AW_SKR_Admin::sanitize_mail_body',
	'default'         => "Sehr geehrte Damen und Herren,\n\nMit freundlichen Grüßen\n\n\nName: \nTelefon: \nE-Mail: ",
) );
$cmb->add_field( array(
	'name'    => 'Link Beschriftung',
	'id'      => 'request_mail_label',
	'type'    => 'text',
	'default' => 'E-Mail-Anfrage',
) );

$cmb->add_field( array(
	'name' => 'Frontend Darstellung',
	'desc' => 'Einstellungen zur Darstellung des Plugins/Widgets im Frontend',
	'type' => 'title',
	'id'   => 'frontend',
) );
$cmb->add_field( array(
	'name'         => 'Plugin-Homepage-Link darstellen?',
	'desc'         => 'Ja, ich unterstütze gerne die Verbreitung des kostenlosen Scheidungskostenrechners.',
	'id'           => 'frontend_show_plugin_link',
	'type'         => 'checkbox',
	'default'      => 'on',
	'after_field'  => ( function ( $field_args, $field ) {
		echo '<p>Am Ende des Widgets/Plugins wird ein kleiner Link und Text eingefügt, der zur Plugin-Homepage führt.<br>Wir bedanken uns für die freundliche Unterstützung!</p>';
	} ),
	'before_field' => ( function ( $field_args, $field ) {
		echo '<span class="dashicons dashicons-heart" style="font-size: 3em; margin-right: 30px; float: right;"></span>';
	} ),
) );
// $cmb->add_field( array(
// 	'id'      => 'frontend_show_plugin_link',
// 	'type'    => 'hidden',
// 	'default' => 'on',
// ) );

if ( aw_skr_get_option( 'setup' ) !== 'ok' ) {
	$cmb->add_field( array(
		'id'      => 'setup',
		'type'    => 'hidden',
		'default' => 'ok',
	) );
}
