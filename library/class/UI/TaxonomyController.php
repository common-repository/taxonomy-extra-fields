<?php
namespace tef\UI;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

use \tef\Auxiliary\TaxonomyFieldsTable;
use \tef\Auxiliary\TaxonomiesListTable;
use \tef\Field\FieldList;
/**
 *
 * @author GuilleGarcia
 *
 */
class TaxonomyController{
	
	/**
	 *
	 */
	function controller(){
		
		$action = $_GET['action'];
		
		if(method_exists($this, $action.'Action'))
			$this->{$action.'Action'}();
		
	}
	
	/**
	 * 
	 */
	function listAction(){
	
		$table = new TaxonomiesListTable();
		
		$table->prepare_items();

		$data = array(
			'title' => 'Taxonomy Extra Fields',
			'table' => $table,
		);
		
		echo get_TEFUI()->render('admin/select-taxonomy', $data);
	}
	
	/**
	 * 
	 */
	function manageAction(){

		$taxonomy = sanitize_title( $_GET['taxonomy'] );
		if("all" == $taxonomy){
			$taxonomy = (object) array(
				'name' => 'all',
				'label' => __('All Taxonomies','tef'),
			);
		}
		else
			$taxonomy = get_taxonomy( $taxonomy );
		
		if( !$taxonomy )
			return false;

		$table = new TaxonomyFieldsTable( $taxonomy->name );
		$table->prepare_items();

		$data = array(
			'title' => sprintf( __('Taxonomy: %s','tef'), $taxonomy->label),
			'taxonomy' => $taxonomy,
			'table' => $table,
			'translations' => array(
				'addnew' => __('Add new field', 'tef'),
				
			),
			'url_new' => '?page=tef-new-field&taxonomy='.$taxonomy->name,
			'unique' => $unique = rand(1000,9999),
			'nonce' => wp_create_nonce('save_fields_positions_'.$unique),
			'position' => FieldList::count( $taxonomy->name ) + 1,
 		);
		
		echo get_TEFUI()->render('admin/manage-taxonomy', $data);

	}
		

}