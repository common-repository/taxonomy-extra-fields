<?php
namespace tef;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

use \tef\UI\UI;
use \tef\DB;
use \tef\LoadClasses;

/**
 * Class Core
 * @since 0.0.01
 * @author GuilleGarcia
 */
final class Core{
	
	static public $instance = NULL;
	
	private $UI;
	private $DB;
	
	protected $fields_types = array();
	
	/**
	 * Constructor
	 */
	function __construct(){
		
		LoadClasses::load_classes();
		
		$this->DB = new DB();
		$this->UI = new UI();
	}

	/**
	 * 
	 */
	function get_UI(){
		return $this->UI;
	}
	
	/**
	 * 
	 * @return \tef\DB
	 */
	function get_DB(){
		return $this->DB;
	}
	
	/**
	 * Function that create and return an instance of Core
	 * @return Core
	 */
	static function init(){
		
		if(is_null(self::$instance))
			self::$instance = new self();
		
		return self::$instance;
	}
}