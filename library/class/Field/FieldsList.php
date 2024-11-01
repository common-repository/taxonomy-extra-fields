<?php
namespace tef\Field;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 * Class FieldList
 * @since 0.0.01
 * @author GuilleGarcia
 */
class FieldList{

	protected $taxonomy = "all";

	protected $fields = array();

	/**
	 *
	 * @param string|array $taxonomy
	 */
	function __construct($taxonomy = 'all'){

		if(is_string($taxonomy))
			$this->taxonomy = sanitize_title( $taxonomy );
		elseif(is_array($taxonomy))
			$this->taxonomy = array_map("sanitize_title", $taxonomy);

	}

	function count_fields(){
		return count( $this->fields );
	}

	/**
	 * Set fields from database
	 * @param array $args
	 */
	function set_from_db( $args = array() ){
		global $wpdb;
		$types = tef_fields_types();

		$args_default = array(
			'orderby' => 'taxonomy, position',
			'order' => 'ASC',
		);

		$args = wp_parse_args($args, $args_default);


		// WHERE
		$where = "";

		// Taxonomy
		if(is_string($this->taxonomy)){
			$taxonomy = array( $this->taxonomy );
			$where .= ' taxonomy LIKE %s ';
		}
		elseif(is_array($this->taxonomy)){
			$taxonomy = $this->taxonomy;
			$where .= ' taxonomy IN("'. implode('","', array_fill(0, count( $this->taxonomy ), '%s') ).'") ';
		}
		else{
			$taxonomy = array( intval( $this->taxonomy ));
			$where .= ' 1 = %s ';
		}

		$sql = 'SELECT *  FROM '.TEF_FIELD_TABLE_NAME.' WHERE '.$where.' ORDER BY '.$args['orderby'].' '.$args['order'];
		$query = call_user_func_array(array($wpdb, 'prepare'), array_merge(array($sql), $taxonomy));

		$rows = $wpdb->get_results($query, ARRAY_A );

		if(0 < count( $rows )):
			foreach($rows as $row):
				if(in_array($row['type'], array_keys($types)) && class_exists( $types[ $row['type'] ]['object'] )){

					$field = new $types[$row['type']]['object']($row['ID']);
					$field->set_name( $row['name'] );
					$field->set_label( $row['label'] );
					$field->set_required( $row['required'] );
					$field->set_type( $row['type'] );
					$field->set_options_from_db( json_decode( $row['options'], true ) );
					$field->set_description( $row['description'] );
					$field->set_taxonomy( $row['taxonomy'] );
					$field->set_position( $row['position'] );

					$this->fields[] = $field;

				}

				else
					continue;

			endforeach;
		endif;
	}

	/**
	 * Return table fields
	 */
	function get_fields(){
		return $this->fields;
	}


	static function count( $taxonomy = 'all' ){
		global $wpdb;

		$taxonomy = sanitize_text_field( $taxonomy );

		return $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(ID) FROM ".TEF_FIELD_TABLE_NAME." WHERE taxonomy LIKE %s", $taxonomy) );

	}

}
