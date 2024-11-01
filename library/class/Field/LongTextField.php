<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Class TextField
 * @since 0.0.01
 * @author GuilleGarcia
 */
class LongTextField extends Field{

	protected $type = "longtext";

	/**
	 * Constructor
	 */
	function __construct($ID=NULL){

		parent::__construct($ID);

	}

	/**
	 * Checks whether the value corresponds to the specifications
	 * {@inheritDoc}
	 * @see TEF_Field::validate()
	 */
	function validate($value){


	}
}