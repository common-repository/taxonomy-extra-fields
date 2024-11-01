<?php

namespace tef\Auxiliary;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

use \tef\Field\FieldList;

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TaxonomiesListTable extends \WP_List_table{
	
	protected $columns = array();
	protected $columns_hidden = array();
	protected $columns_sortables = array();
	
	/**
	 * 
	 */
	function get_columns(){
		
		$this->columns = array(
			'taxonomy' => __('Taxonomy','tef'),
			'slug' => __('Slug','tef'),
			'fields'    => __('Custom Fields','tef'),
		);
		
		return $this->columns;
	}
	
	/**
	 *
	 */
	function get_columns_hidden(){
		
		$this->columns_hidden = array(
			'slug',
		);
		
		return $this->columns_hidden;
		
	}
	
	/**
	 *
	 */	
	function get_columns_sortables(){
		$this->columns_sortables = array(
			
		);
		
		return $this->columns_sortables;
	}

	/**
	 *
	 */
	function prepare_items() {

		$this->_column_headers = array($this->get_columns(), $this->get_columns_hidden(), $this->get_columns_sortables());

		$this->items[] = array(
			'taxonomy' => __('All Taxonomies','tef'),
			'slug' =>'all',
			'fields'  => FieldList::count( "all" ),
		);
		
		foreach(get_taxonomies(null, 'objects') as $taxonomy){
			if($taxonomy->show_ui){
				$this->items[] = array(
					'taxonomy' => $taxonomy->label,
					'slug' => $taxonomy->name,
					'fields'  => FieldList::count( $taxonomy->name ),
				);
			}
		}

	}

	/**
	 * It's determine the appareance of the taxonomy name column
	 */
	function column_taxonomy($item) {
		
		$page = "tef-manage-taxonomy";
		$manage_link = '?page='.$page.'&taxonomy='.$item['slug'];

		$actions = array(
			'manage' => '<a href="'.$manage_link.'">'.__('Manage','tef').'</a>',
		);
	
		return sprintf('<strong><a class="row-title" href="'.$manage_link.'">%1$s</a></strong> %2$s', $item['taxonomy'], $this->row_actions($actions) );
	}

	/**
	 *
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'taxonomy':
			case 'fields':
				return $item[ $column_name ];
			default:
				return $item; //Show the whole array for troubleshooting purposes
		}
	}
	
	
}