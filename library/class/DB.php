<?php
namespace tef;

class DB{
	
	private static $DB_version = "1.0.00";

	private $fields_table_name;
	
	function __construct(){
		// Create database tables
		add_action('admin_init', array($this,'manage_tables'), 10);
	}
	
	/**
	 * Create custom plugin required database tables
	 */
	function manage_tables(){
		
		$actual_version = get_option('_TEF_DB_version', false);
		
		// Create tables
		if(!$actual_version){
			
			if( $this->create_table() )
				update_option( "_TEF_DB_version", self::$DB_version, false );
		}
		
		// Update tables
		elseif( str_replace('.','', self::$DB_version) > str_replace('.','', $actual_version) ){
			if( $this->update_talbe($actual_version))
				update_option( "_TEF_DB_version", self::$DB_version, false );
		}
		
	}
	
	/**
	 * Create table
	 */
	function create_table(){
		global $wpdb;
		
		$this->fields_table_name = $wpdb->prefix."tef_fields";
		$charset_collate = $wpdb->get_charset_collate();
		
		$sql = "CREATE TABLE IF NOT EXISTS ".TEF_FIELD_TABLE_NAME." (
			ID mediumint(9) NOT NULL AUTO_INCREMENT,
			position smallint(4) NOT NULL DEFAULT 1,
			taxonomy varchar(32) NOT NULL DEFAULT 'all',
			label VARCHAR(100),
			name VARCHAR(100),
			type VARCHAR(32),
			description LONGTEXT,
			required TINYINT(1),
			options LONGTEXT,
			PRIMARY KEY ID (ID),
			UNIQUE KEY taxonomy_name (taxonomy, name)
		) $charset_collate;";
		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		
		return dbDelta( $sql );
	}
	
	/**
	 * TO DO
	 * @param unknown $version
	 */
	function update_table($version){
		
	}
	
}