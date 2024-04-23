<?php

	if ( ! defined( 'ABSPATH' ) ) {
		exit; // Exit if accessed directly.
	}

/**
 * Elementor Form Field - Date picker
 * Add a new "Date picker" field to Elementor form widget.
 */
class Elementor_Date_Picker_Field extends \ElementorPro\Modules\Forms\Fields\Field_Base {
	/**
	  * Adding the flatpickr to use the native validation
	*/
	public $depended_scripts = [
		'flatpickr',
	];
	public $depended_styles = [
		'flatpickr',
	];

	/**
	 * Get field type.
	*/
	public function get_type() {
		return 'date-picker';
	}

	/**
	 * Get field name.
	*/
	public function get_name() {
		return esc_html__( 'Date Picker', 'elementor-form-date-picker-field' );
	}

	/**
	 * Render field output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 * To use some native scripts to date, elementor uses css classes (example: elementor-date-field)
	 */
	public function render( $item, $item_index, $form ) {
		$form_id = $form->get_id();

		$form->add_render_attribute( 'input' . $item_index, 'class', 'elementor-field-textual elementor-date-field' );
		$form->add_render_attribute( 'input' . $item_index, 'pattern', '[0-9]{4}-[0-9]{2}-[0-9]{2}' );

		$form->add_render_attribute(
			'input' . $item_index,
			[
				'class' => 'elementor-field-textual',
				'for' => $form_id . $item_index,
				'inputmode' => 'numeric',	
				'title' => esc_html__( 'Format: aaaa-mm-dd', 'elementor-form-date-picker-field' ),
				'min' => $item['data_min'],
				'max' => $item['data_max']
			]
		);

		echo '<input ' . $form->get_render_attribute_string( 'input' . $item_index ) . '>';
	}

	/**
	 * Field validation.
	 *
	 * Validate date-picker field value to ensure it complies to certain rules.
	 */
	public function validation( $field, $record, $ajax_handler ) {
		if ( empty( $field['value'] ) ) {
			return;
		}
	}

	/**
	 * Update form widget controls.
	 *
	 * Add data input fields minimum and data maximum to allow the user to set the date picker field between to values.
	 */
	public function update_controls( $widget ) {
		$elementor = \ElementorPro\Plugin::elementor();
		$control_data = $elementor->controls_manager->get_control_from_stack( $widget->get_unique_name(), 'form_fields' );

		if ( is_wp_error( $control_data ) ) {
			return;
		}

		$field_controls = [
			'data_min' => [
				'name' => 'data_min',
				'label' => esc_html__( 'Data Min.', 'elementor-form-date-picker-field' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'label_block' => false,
				'picker_options' => [
					'enableTime' => false,
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			],
			'data_max' => [
				'name' => 'data_max',
				'label' => esc_html__( 'Data Máx.', 'elementor-form-date-picker-field' ),
				'type' => \Elementor\Controls_Manager::DATE_TIME,
				'condition' => [
					'field_type' => $this->get_type(),
				],
				'label_block' => false,
				'picker_options' => [
					'enableTime' => false,
				],
				'tab' => 'content',
				'inner_tab' => 'form_fields_content_tab',
				'tabs_wrapper' => 'form_fields_tabs',
			], 
		];

		/**
		 * Add default fields from elementor, such as placeholder
		*/
		foreach ( $control_data['fields'] as $index => $field ) {
			if ( 'placeholder' !== $field['name'] ) {
				continue;
			}
			foreach ( $field['conditions']['terms'] as $condition_index => $terms ) {
				if ( ! isset( $terms['name'] ) || 'field_type' !== $terms['name'] || ! isset( $terms['operator'] ) || 'in' !== $terms['operator'] ) {
					continue;
				}
				$control_data['fields'][ $index ]['conditions']['terms'][ $condition_index ]['value'][] = $this->get_type();
				break;
			}
			break;
		}

		$control_data['fields'] = $this->inject_field_controls( $control_data['fields'], $field_controls );

		$widget->update_control( 'form_fields', $control_data );
	}

	/**
	 * Default function: Field constructor. Default function
	 *
	 * Used to add a script to the Elementor editor preview.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/preview/init', [ $this, 'editor_preview_footer' ] );
	}

	/**
	 * Default function: Elementor editor preview.
	 *
	 * Add a script to the footer of the editor preview screen.
	*/
	public function editor_preview_footer() {
		add_action( 'wp_footer', [ $this, 'content_template_script' ] );
	}

	/**
	 * Content template script.
	 *
	 * Add content template alternative, to display the field in Elementor editor.
	 */
	public function content_template_script() {
	?>
		<script>
		jQuery( document ).ready( () => {

			elementor.hooks.addFilter(
				'elementor_pro/forms/content_template/field/<?php echo $this->get_type(); ?>',
				function ( inputField, item, i ) {
					const fieldType    = 'date';
					const fieldId    = `form_field_${i}`;
					const fieldClass = `elementor-field-textual elementor-field ${item.css_classes}`;
					const pattern    = '[0-9]{4}-[0-9]{2}-[0-9]{2}';
					const title      = "<?php echo esc_html__( 'Format: aaaa-mm-dd', 'elementor-forms-date-picker-field' ); ?>";

					return `<input 
								id="${fieldId}" 
								type="${fieldType}" 
								class="${fieldClass}" 
								pattern="${pattern}" 
								title="${title}"
							>`;
				}, 10, 3
			);

		});
		</script>
		<?php
	}

}