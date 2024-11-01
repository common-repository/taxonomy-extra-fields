<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Abstract class TEF_Field
 * @since 0.6.03
 * @author GuilleGarcia
 */
abstract class OptionsField extends Field{

  protected $options = array(
    'default' => null,
    'multiple' => false,
    'options' => array(),
		'split' => false,
  );

  function set_options($options){

    if(!is_array($options) && !is_object($options))
      return false;

    $options = (array) $options;

    // Set split option
		if(isset($options['split'])){

			if($options['split'])
				$this->options['split'] = true;
			else
				$this->options['split'] = false;

        // Delete split option for not send to parent constructor
        unset( $options['split'] );

		}

		// Set options list
		if(isset($options['options'])){
			$this->options['options'] = $this->parse_options_list( $options['options'], $this->options['split'] );
      // Delete split option for not send to parent constructor
      unset( $options['options'] );
    }else{
			$this->options['options'] = array();
    }

    $this->options = wp_parse_args($options, $this->options );
  }

  /**
   *
   * @param unknown $options
   * @param string $split
   * @return NULL[]|unknown[]
   */
  function parse_options_list($options, $split=false){

    $options = (array) $options;
    $optionList = array();

    // Array key=>value
    if($split && isset($options['keys']) && isset($options['values'])){

      // num of keys must be equals that num of values
      if(count($options['keys']) == count($options['values'])){
        for($i=0;$i<=count($options['keys']); $i++){

          if(!empty($options['values'][$i])){

            if(!empty($options['keys'][$i])){
              $optionList[ sanitize_title( $options['keys'][$i] ) ] = sanitize_text_field( $options['values'][$i] );
            }else{
              $optionList[ sanitize_text_field( $options['values'][$i] ) ] = sanitize_text_field( $options['values'][$i] );
            }

          }

        }
      }

    }
    // Array numeric (key will be equal that value)
    else{

      if(isset($options['values']) && is_array($options['values'])){
        foreach($options['values'] as $option){
          if(!empty($option))
            $optionList[] = sanitize_text_field( $option );
        }
      }

      else if(is_array($options)){
        foreach($options as $option){
          if(!empty($option) && !is_array($option))
            $optionList[] = sanitize_text_field( $option );
        }

      }

    }

    return $optionList;
  }

  /**
	 * Validate de data of Field Object for create/update
	 */
	function validate($value){

    return true;
  }

}
