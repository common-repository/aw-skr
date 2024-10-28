<?php
/**
 * The agbs settings page
 *
 * @link       http://scheidungskostenrechner.org
 * @since      1.0.0
 *
 * @package    AW_SKR
 * @subpackage AW_SKR/admin/partials
 * @author     Active Websight <info@active-websight.de>
 */

$options = array_merge( $this->admin_page_cmb_defaults, [
	'id' => AW_SKR_SETTINGS . '-agbs',
]);

$cmb = new_cmb2_box( $options );
$cmb->add_field( array(
	'name'       => 'AGBs akzeptieren',
	'desc'       => 'Ich habe die AGBs, Haftungsausschluss und Nutzungsbedingungen gelesen, verstanden und akzeptiere diese.',
	'id'         => 'agbs_accepted',
	'type'       => 'checkbox',
	'before_row' => 'AW_SKR_Admin::settings_load_agbs',
) );
