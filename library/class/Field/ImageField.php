<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Class ImageField
 * @since 0.0.01
 * @author GuilleGarcia
 */
class ImageField extends FileField{

	protected $type = "image";

	protected $options = array(
		'multiple' => false,
		'formats' => array('image/*'), // Empty for all
		'button' => array(
			'class' => '',
			'text' => '',
		),
	);

	/**
	 * Constructor
	 */
	function __construct($ID=NULL){

		$this->options['button']['text'] = __('Add Image','tef');

		parent::__construct($ID);

	}

	/**
	 * Checks whether the value corresponds to the specifications
	 * {@inheritDoc}
	 * @see TEF_Field::validate()
	 */
	function validate($value){
		return true;
	}

	/**
	 * Check if $value is a valid wordpress file (Image)
	 */
	function validate_value($value){

		if( is_numeric($value) && wp_attachment_is_image( intval($value) ) )
			return true;

		return false;

	}


	/**
	 * @return 0 (not attachment found) | array metadata
	 * @see https://codex.wordpress.org/Function_Reference/wp_get_attachment_metadata
	 */
	function get_saved_value( $term_id ){

		$term_id = intval( $term_id );
		$attachment_id =  get_term_meta( $term_id, $this->get_name(), true);

		if( wp_attachment_is_image( $attachment_id ) )
			return array(
				'id' => $attachment_id,
				'title' => get_the_title( $attachment_id ),
				'url' => wp_get_attachment_url( $attachment_id ),
				'thumbnail' => wp_get_attachment_thumb_url( $attachment_id ),
				'metadata' => wp_get_attachment_metadata( $attachment_id )
			);

		return 0;

	}

}
