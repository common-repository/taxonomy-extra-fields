<?php

namespace tef\Auxiliary;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

use tef\Field\FieldList;
use tef\Field\TextField;
use tef\Field\NoTypeField;

if( ! class_exists( 'WP_List_Table' ) ) {
	require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

class TaxonomyFieldsTable extends \WP_List_table {
	
	protected $columns = array();
	protected $columns_hidden = array();
	protected $columns_sortables = array();
	
	protected $taxonomy_name = "all";
	
	/**
	 *
	 */
	function __construct( $taxonomy = "all" ){
		if(is_string($taxonomy))
			$this->taxonomy_name = sanitize_title( $taxonomy );
		
		parent::__construct();
	}
	
	/**
	 *
	 */
	function get_columns(){
		
		$this->columns = array(
			'ID' => __('ID','tef'),
			'taxonomy' => __('Taxonomy','tef'),
			'position' => '#',
			'label' => __('Label','tef'),
			'name' => __('Name','tef'),
			'type' => __('Type','tef'),
			'description' => __('Description','tef'),
			'required' => __('Is Required','tef'),
		);
		
		return $this->columns;
	}
	
	/**
	 * 
	 */
	protected function get_table_classes() {
		
		return array_merge(parent::get_table_classes(), array('tef_fields_table'));
		
	}
	
	
	/**
	 *
	 */
	function get_columns_hidden(){
		
		$this->columns_hidden = array(
			'ID',
			'required',
			'taxonomy'
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
	function prepare_columns(){
		$this->_column_headers = array($this->get_columns(), $this->get_columns_hidden(), $this->get_columns_sortables());
	}
	
	/**
	 * 
	 */
	function prepare_items() {

		$this->prepare_columns();

		$fieldList = new FieldList( $this->taxonomy_name );
		$fieldList->set_from_db();
		
		if(0 < count($fields = $fieldList->get_fields())):
			foreach($fields as $field):
				$this->items[] = array(
					'ID' => $field->get_ID(),
					'taxonomy' => $field->get_taxonomy(),
					'position' => $field->get_position(),
					'name' => $field->get_name(),
					'label' => $field->get_label(),
					'type' => $field->get_type(),
					'description' => $field->get_description(),
					'required' => $field->get_required(),
					'json' => $field->to_JSON(),
					'options' => $field->get_options(),
				);
			endforeach;
		endif;
	}
	

	/**
	 * 
	 * @param unknown $item
	 * @return string
	 */
	function column_label($item) {
		
		$page = "tef-edit-field";
		$link = '?page='.$page.'&ID='.$item['ID'];

		$actions = array(
			'edit' => '<a href="'.$link.'">'.__('Edit','tef').'</a>',
			'delete' => sprintf('<a href="?page=%1$s&ID=%2$s">Delete</a>', 'tef-delete-field', $item['ID']),
		);
	
		if($item['required'])
			$required = '<span class="required">*</span>';
		else 
			$required = '';
		
		return sprintf(
			'<strong><a class="row-title" href="%4$s"><span class="label">%1$s</span> %3$s</a></strong> %2$s %5$s', 
			$item['label'] ? $item['label'] : __('New Field','tef'), 
			$this->row_actions($actions), 
			$required, 
			$link, 
			get_TEFUI()->render('form/field', array(
				'item'=>$item,
				'taxonomy'=>$item['taxonomy'],
				'position'=>$item['position'],
				'field_types' => tef_fields_types(),
				'unique' => $unique = rand(1000,9999),
				'nonce' => wp_create_nonce('save_field_'.$unique),
				'translation' => array(
					'label' => __('Label','tef'),
					'type' => __('Type','tef'),
					'name' => __('Name','tef'),
					'description' => __('Description','tef'),
					'options' => __('Options','tef'),
					'required' => __('Required','tef'),
					'select_option' => __('Select an option','tef'),
					'save' => __('Save','tef'),
					'cancel' => __('Cancel','tef'),
					'unlock' => __('Unlock','tef'),
					'default' => __('Default value','tef'),
					'placeholder' => __('Placeholder','tef'),
					'options' => __('Options list','tef'),
					'split' => __('Split Key/Value','tef'),
				),
				
			)) 
		);
	}
	
	/**
	 * 
	 * @param unknown $item
	 * @param unknown $column_name
	 * @return string|unknown
	 */
	function column_default( $item, $column_name ) {
		switch( $column_name ) {
			case 'position':
				return '<span class="sortable-icon dashicons dashicons-menu" title="'.__('Drag to reorder','tef').'"></span><input type="hidden" class="position" name="field['.$item['ID'].']" value="'.$item[ $column_name ].'"  form="fields-positions" />';
			case 'type':
				$types = tef_fields_types();
				if( in_array($item['type'], array_keys( $types ) ))
					return $types[ $item['type'] ]['name'];
				else
					return __('Unknow','tef');
			case 'ID':
			case 'taxonomy':
			case 'name':
			case 'description':
			case 'required':
			case 'json':
				return $item[ $column_name ];
			default:
				return $item;
		}
	}
	
	/**
	 * 
	 */
	function no_items() {
		echo '<h2>'.__( 'No custom fields found.', 'tef' ).' <a href="?page=tef-new-field&taxonomy='.$this->taxonomy_name.'" class="button button-primary add-new-field">'.__('Add new field', 'tef').'</a></h2>';
	}
	
	
}