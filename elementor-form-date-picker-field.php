<?php
/**
 * Plugin Name: Elementor Forms - Date Picker Field
 * Description: Custom addon that adds a "date picker" field to Elementor Forms Widget, let you select a possible date range.
 * Plugin URI:  
 * Version:     1.0.0
 * Author:      Aldry Rocha
 * Author URI:  
 * Text Domain: elementor-form-local-teldate-picker-field
 *
 * Requires Plugins: elementor Pro
 * Elementor tested up to: 3.21.1
 * Elementor Pro tested up to: 3.20.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Add new `date-picker` field to Elementor form widget.
 *
 * @since 1.0.0
 * @param \ElementorPro\Modules\Forms\Registrars\Form_Fields_Registrar $form_fields_registrar
 * @return void
 */
function add_new_form_field( $form_fields_registrar ) {

	require_once( __DIR__ . '/form-fields/date-picker.php' );

	$form_fields_registrar->register( new \Elementor_Date_Picker_Field() );

}
add_action( 'elementor_pro/forms/fields/register', 'add_new_form_field' );