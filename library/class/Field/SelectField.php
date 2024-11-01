<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Class SelectField
 * @since 0.0.01
 * @author GuilleGarcia
 */
class SelectField extends OptionsField{

	protected $type = "select";

	/**
	 * Checks whether the value corresponds to the specifications
	 * {@inheritDoc}
	 * @see TEF_Field::validate()
	 */
	function validate($value){
		return true;
	}
	
}
