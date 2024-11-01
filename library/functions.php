<?php

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

require_once 'field_types.php';
require_once 'ajax.php';
require_once 'class/Field/FieldsList.php';

use \tef\Field\FieldList;

/**
 * 
 * @return \tef\Core
 */
function tef_getInstance(){
	
	return \tef\Core::init();
	
}

/**
 * 
 * @return \tef\Core
 */
function get_TEF(){
	return tef_getInstance();
}

/**
 * 
 * @return \tef\UI\UI
 */
function get_TEFUI(){
	return tef_getInstance()->get_UI();
}


function tef_js_translations(){
	echo "<script> var tef = JSON.parse('";
		echo json_encode(array(
			'translations' => array(
				'msg' => array(
					'confirm' => __('Do you want to continue?','tef'),
					'confirm_delete' => __('Do you want remove this field?', 'tef'),
					'save' => __('Save','tef'),
					'accept' => __('Accept','tef'),
					'saved' => __('Saved','tef'),
					'cancel' => __('Cancel','tef'),
				),
				'types' =>  tef_fields_types('names'),
				'button' => array(
					'addfile' => __('Add File','tef'),
					'addimage' => __('Add Image','tef'),
					'replace'=> __('Replace','tef'),
				),
			)
		));
	echo "');</script>";
}
add_action( 'admin_head', 'tef_js_translations' );


/**
 * Return an array of all Categories (that can be displayed)
 * @return array[category-name] = category-label
 */
function tef_get_taxonomies(){
	
	$taxonomies = array();
	
	$taxonomies['all'] = __('All','tef');
	
	foreach(get_taxonomies(null, 'objects') as $taxonomy){
		if($taxonomy->show_ui){
			$taxonomies[$taxonomy->name] = $taxonomy->label;
		}
	}
	
	return $taxonomies;
	
}

function tef_load_plugin_textdomain() {
	load_plugin_textdomain( 'tef', false, TEF_BASENAME."/languages" );
}
add_action( 'plugins_loaded', 'tef_load_plugin_textdomain' );