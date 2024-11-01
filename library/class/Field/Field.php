<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Abstract class TEF_Field
 * @since 0.0.01
 * @author GuilleGarcia
 */
abstract class Field{

	protected $ID = NULL;

	protected $position = 1;

	protected $taxonomy = '';

	protected $name = '';

	protected $label = '';

	protected $description = '';

	protected $options = array(
		'default' => null,
		'pattern' => null, // Regular expresion to validate
		'length' => array(
			'min' => 0, // 0: none
			'max' => 0, // 0: none
		),
		'placeholder' => "",
		'multiple' => false, // false: unique value | true: multiple values
	);

	protected $required = 0;

	protected $type;

	/**
	 * Constructor
	 * Create a istance
	 * @param integer $ID
	 */
	function __construct($ID = null){

		if(is_numeric($ID))
			$this->ID = intval( $ID );

	}

	/**
	 *
	 */
	function get_ID(){
		return intval( $this->ID );
	}

	/**
	 *
	 */
	function get_position(){
		return intval( $this->position );
	}

	/**
	 *
	 */
	function set_position($position){
		if(is_numeric($position))
			$this->position = intval( $position );
	}

	/**
	 *
	 */
	function get_taxonomy(){
		return esc_attr( $this->taxonomy );
	}

	/**
	 *
	 */
	function set_taxonomy($taxonomy){

		$taxonomies = array_merge(array('all'), get_taxonomies());

		if(is_string($taxonomy)){
			$taxonomy = sanitize_title( $taxonomy );

			if(in_array($taxonomy, $taxonomies))
				$this->taxonomy = $taxonomy;
		}

	}

	/**
	 *
	 */
	function get_name(){
		return esc_attr( $this->name );
	}

	/**
	 *
	 */
	function set_name($name){
		if(is_string($name))
			$this->name = sanitize_title( $name );
	}

	/**
	 *
	 */
	function get_label(){
		return ucwords( esc_attr( $this->label ) );
	}

	/**
	 *
	 */
	function set_label($label){
		if(is_string($label))
			$this->label = sanitize_text_field( $label );
	}

	/**
	 *
	 */
	function get_description(){
		return esc_html( $this->description );
	}

	/**
	 *
	 */
	function set_description( $description ){
		if(is_string($description))
			$this->description = sanitize_text_field( $description );
	}

	/**
	 *
	 */
	function get_options(){
		return $this->options;
	}



	/**
	 *
	 */
	function set_options($options){

		$options = (array) $options;

		// Set default value
		if(isset($options['default']))
			$options['default'] = sanitize_text_field( $options['default'] );

		$this->options = wp_parse_args($options, $this->options );

	}

	function set_options_from_db($options){

		$this->options = (array) $options;

	}

	/**
	 *
	 */
	function get_required(){
		return $this->required;
	}

	/**
	 *
	 */
	function is_required(){

		if($this->required)
			return true;

		return false;

	}

	/**
	 *
	 */
	function set_required($boolean){

		if($boolean)
			$this->required = 1;

		else
			$this->required = 0;

	}

	/**
	 *
	 */
	function get_type(){
		return esc_attr( strtolower( $this->type ) );
	}

	/**
	 *
	 */
	function set_type($type){
		if(is_string($type) && in_array($type, array_keys( tef_fields_types() )))
			$this->type = $type;
	}

	function get_default(){
		if(isset($this->options['default']))
			return esc_attr( $this->options['default'] );

		return null;
	}



	/**
	 * Save the current field
	 * If ID is empty create a new field
	 * Else update the existing field
	 * @return boolean
	 */
	function save(){
		global $wpdb;

		$data = array(
			'position' => $this->position,
			'taxonomy' => $this->taxonomy,
			'label' => $this->label,
			'name' => $this->name,
			'type' => $this->type,
			'description' => $this->description,
			'required' => $this->required,
			'options' => json_encode( $this->options ),
		);
		$data_format = array(
			'%d',
			'%s',
			'%s',
			'%s',
			'%s',
			'%s',
			'%d',
			'%s',
		);

		// Create new
		if(is_null($this->ID) || $this->ID == 0){

			if($wpdb->insert(TEF_FIELD_TABLE_NAME,$data,$data_format)){
				$this->ID = $wpdb->insert_id;
				return $this->to_JSON();
			}else
				return 0;

		// Update existing
		}else{

			if($wpdb->update(TEF_FIELD_TABLE_NAME, $data,array('ID' => $this->ID,),$data_format,array('%d')))
				return $this->to_JSON();
			else
				return 0;

		}


	}

	function validate_value( $value ){
		return true;
	}

	/**
	 *
	 * @param unknown $term_id
	 * @param unknown $value
	 */
	function save_value($term_id, $value){

		update_term_meta( $term_id, $this->name, sanitize_text_field( $value ) );

	}

	function get_saved_value( $term_id ){

		return esc_attr( get_term_meta($term_id, $this->get_name(), true) );

	}

	/**
	 * Delete the field from DB
	 */
	function delete(){
		global $wpdb;

		return $wpdb->delete(
			TEF_FIELD_TABLE_NAME,
			array('ID' => $this->ID),
			array('%d')
		);

	}

	/**
	 * Encode object to JSON
	 * @return string
	 */
	function to_JSON(){
		return json_encode(array(
			'ID' => $this->ID,
			'position' => $this->position,
			'taxonomy' => $this->taxonomy,
			'name' => $this->name,
			'label' => $this->label,
			'description' => $this->description,
			'options' => $this->options,
			'required' => $this->required,
			'type' => $this->type,
		));
	}

	/**
	 * Create OBJECT from JSON
	 * @param string $JSON
	 * @return boolean
	 */
	function from_JSON($JSON){

		$array = json_decode($JSON);

		if(!$array || !is_array($array))
			return false;

		if(isset($array['ID']))
			$this->set_ID( $array[''] );

		if(isset($array['position']))
			$this->set_position( $array['position'] );

		if(isset($array['taxonomy']))
			$this->set_( $array['taxonomy'] );

		if(isset($array['name']))
			$this->set_( $array['name'] );

		if(isset($array['label']))
			$this->set_label( $array['label'] );

		if(isset($array['description']))
			$this->set_description( $array['description'] );

		if(isset($array['options']))
			$this->set_options( $array['options'] );

		if(isset($array['required']))
			$this->set_required( $array['required'] );

		if(isset($array['type']))
			$this->set_type( $array['type'] );

	}

	/**
	 * Validate de data of Field Object for create/update
	 */
	abstract function validate($value);


	static function save_field($ID,$position,$taxonomy,$name,$label,$description,$options,$required,$type){

		$types = tef_fields_types();
		$taxonomies = array_merge(array('all'), get_taxonomies());

		if(!in_array($taxonomy, $taxonomies))
			return 0;

		if(!in_array($type, array_keys($types)))
			return 0;

		if(!class_exists($types[$type]['object']))
			return 0;

		$field = new $types[$type]['object']($ID);

		$field->set_position($position);
		$field->set_taxonomy($taxonomy);
		$field->set_name($name);
		$field->set_label($label);
		$field->set_description($description);
		$field->set_options($options);
		$field->set_required($required);
		$field->set_type($type);

		return $field->save();
	}

	static function delete_field($ID){

		$field = new NoTypeField( intval( $ID ) );

		return $field->delete();

	}
}
