<?php
namespace tef\UI;

use \tef\UI\FieldController;
use tef\Field\FieldList;

/**
 * Create and manage User Interface
 *
 * @author Guillermo
 *
 */
class UI{

	protected $twig_loader;
	protected $twig;

	protected $admin_pages = array();
	protected $hidden_admin_pages = array();

	function __construct(){
		$this->register_actions();
	}

	/**
	 * Register UI functions in actions hooks
	 */
	function register_actions(){

		// Register menus
		add_action('admin_menu', array($this, 'register_menus'), 10);

		// Register scripts (CSS and JS)
		add_action('admin_enqueue_scripts', array($this, 'register_admin_scripts'));
	}

	/**
	 * Register all admin scripts (CSS and JavaScript)
	 */
	function register_admin_scripts(){

		$screens = array(
			'toplevel_page_taxonomy-extra-fields',
			'admin_page_tef-manage-taxonomy',
			'taxonomy-extra-fields_page_taxonomy-extra-fields-credits',
			'edit-category'
		);

		if(in_array( get_current_screen()->id, $screens)){
			/*
			 * Load wp.media library
			 */
			wp_enqueue_media();

			/*
			 * FONT AWESOME
			 */
			wp_register_style( 'font-awesome', TEF_URL.'/vendor/font-awesome-4.6.3/css/font-awesome.min.css', false, '1.0.0' );
			wp_enqueue_style( 'font-awesome' );

			/*
			 * FONT AWESOME
			 */
			wp_register_style( 'animate-css', TEF_URL.'/vendor/animate-css/animate.css', false, '1.0.0' );
			wp_enqueue_style( 'animate-css' );

			/*
			 * NOTY
			 */
			wp_register_script( 'jquery-noty', TEF_URL.'/vendor/noty/packaged/jquery.noty.packaged.min.js', array('jquery'), '2.3.8', true );
			wp_enqueue_script( 'jquery-noty' );

			/*
			 * TAXONOMY EXTRA FIELDS
			 */
			wp_register_script( 'tef_admin_functions', TEF_URL.'/assets/javascript/admin-functions.js', array('jquery','jquery-ui-core','jquery-ui-sortable','media-upload'), '1.0.0', true );
			wp_enqueue_script( 'tef_admin_functions' );

			wp_register_style( 'tef_admin_style', TEF_URL.'/assets/css/admin-style.min.css', false, '1.0.0' );
			wp_enqueue_style( 'tef_admin_style' );

		}
	}

	function register_menus(){

		$this->admin_pages = array(
			// Principal Plugin Menu
			array(
				'page_title' => __('Taxonomy Extra Fields','tef'),
				'menu_title' => __('Taxonomy Extra Fields','tef'),
				'capability' => 'manage_options',
				'menu_slug' => 'taxonomy-extra-fields',
				'function' => array(new TaxonomyController, 'listAction'),
				'icon_url' => 'dashicons-smiley',
				'position' => '50',
				// Subpages
				'subpages' => array(
					array(
						'page_title' => __('Credits','tef'),
						'menu_title' => __('Credits','tef'),
						'capability' => 'manage_options',
						'menu_slug' => 'taxonomy-extra-fields-credits',
						'function' => array($this, 'credits_view'),
					),
				),
			),

			/*
			 * TEMPLATE:
			array(
				'page_title' => __('','tef'),
				'menu_title' => __('','tef'),
				'capability' => '',
				'menu-slug' => '',
				'function' => array(),
				'icon_url' => '',
				'position' => '',
				'subpages' => array(
					array(
						'page_title' => __('','tef'),
						'menu_title' => __('','tef'),
						'capability' => '',
						'menu-slug' => '',
						'function' => array(),
					),
				),
			),
			*/
		);

		// HIDDEN PAGES
		$this->hidden_admin_pages = array(
			array(
				'page_title' => __('Manage Taxonomies','tef'),
				'menu_title' => __('Manage Taxonomies','tef'),
				'capability' => 'manage_options',
				'menu_slug' => 'tef-manage-taxonomy',
				'function' => array(new TaxonomyController, 'manageAction'),
			),
			array(
				'page_title' => __('New Field','tef'),
				'menu_title' => __('New Field','tef'),
				'capability' => 'manage_options',
				'menu_slug' => 'tef-new-field',
				'function' => array(new FieldController, 'newAction'),
			),
			array(
				'page_title' => __('Edit Field','tef'),
				'menu_title' => __('Edit Field','tef'),
				'capability' => 'manage_options',
				'menu_slug' => 'tef-edit-field',
				'function' => array(new FieldController, 'editAction'),
			),
			array(
				'page_title' => __('Delete Field','tef'),
				'menu_title' => __('Delete Field','tef'),
				'capability' => 'manage_options',
				'menu_slug' => 'tef-delete-field',
				'function' => array(new FieldController, 'deleteAction'),
			),
		);

		// Create pages in menu
		if(0 < count( $this->admin_pages )){
			foreach($this->admin_pages as $page){

				add_menu_page($page['page_title'], $page['menu_title'], $page['capability'], $page['menu_slug'], $page['function'], $page['icon_url'], $page['position']);

				if(isset($page['subpages']) && 0 < count($page['subpages'])){
					foreach($page['subpages'] as $subpage){
						add_submenu_page($page['menu_slug'],$subpage['page_title'],$subpage['menu_title'],$subpage['capability'],$subpage['menu_slug'],$subpage['function']);
					}
				}

			}
		}

		// Create hidden pages
		if(0 < count($this->hidden_admin_pages)){
			foreach($this->hidden_admin_pages as $page){
				add_submenu_page('admin.php',$page['page_title'],$page['menu_title'],$page['capability'],$page['menu_slug'],$page['function']);
			}
		}

	}

	// VIEWS
	function welcome_view(){

	}

	function credits_view(){

		$data = array(
			'about_title' => __('About this plugin','tef'),
			'about_text_one' => sprintf(
				__('This plugin is created by <a href="%1$s" title="%2$s">%3$s</a> <a href="%4$s" title="%5$s">%6$s</a>. Actually, the plugin is in Beta version, it\'s posible that errors arising, you can get help writing in <a href="%7$s">%8$s</a>.','tef'),
				'http://guillermogarcia.info',
				__('Personal website','tef'),
				'Guillermo Garcia',
				'https://twitter.com/FlewPs',
				__('Twitter profile @Flewps','tef'),
				'@Flewps',
				'https://wordpress.org/support/plugin/taxonomy-extra-fields',
				__('plugin support','tef')
			),
			'about_text_two' => sprintf(
				__('If you\'re interested in extends the functionality of this plugin, this is the <a href="%1$s" title="%2$s">Github repository</a>. All contributios are welcome.','tef'),
				'https://github.com/Flewps/taxonomy-extra-fields',
				__('Open Github URL')
			),
			'about_text_three' => sprintf(
				__('You can check the next milestones <a href="%s">here</a>.','tef'),
				'https://wordpress.org/plugins/taxonomy-extra-fields/milestones/'
			),
			'about_text_four' => __('Thank you for use this plugin.','tef'),

			'vendors_title' => __('Recognition to thirds','tef'),
			'vendors' => array(
				array(
					'url' => 'http://twig.sensiolabs.org/',
					'title' => __('Twig: The flexible, fast, and secure template engine for PHP','tef'),
					'img' => TEF_URL.'/assets/images/TWIG.png'
				),
				array(
					'url' => 'http://ned.im/noty/',
					'title' => __('(Noty)2: jquery notification plugin','tef'),
					'img' => TEF_URL.'/assets/images/jQueryNoty.png'
				),
				array(
					'url' => 'https://daneden.github.io/animate.css/',
					'title' => __('Animate.CSS: Just-add-water CSS animations','tef'),
					'img' => TEF_URL.'/assets/images/AnimateCSS.png'
				),
				array(
					'url' => 'http://fontawesome.io/',
					'title' => __('Font Awesome: The iconic font and CSS toolkit','tef'),
					'img' => TEF_URL.'/assets/images/FontAwesome.png'
				),
			)
		);

		echo $this->render('credits', $data);

	}

	/**
	 * Render the view (if exists)
	 * @param string $path: Ex: admin/manage-field
	 * @param array $data
	 * @return string
	 */
	function render( $path, $data=array() ){

		require_once TEF_DIR . 'vendor/Twig/Autoloader.php';

		\Twig_Autoloader::register();

		$templates_dir = TEF_DIR .'assets/templates';
		$file_name = $path . '.html.twig';
		$file_dir = $templates_dir . '/' . $file_name;

		if(file_exists( $file_dir )){

			if(!$this->twig_loader)
				$this->initialize_twig_loader( $templates_dir );

			if(!$this->twig)
				$this->initialize_twig();

			return $this->twig->render($file_name,$data);

		}

	}

	function initialize_twig_loader($templates_dir){
			$this->twig_loader = new \Twig_Loader_Filesystem($templates_dir);
	}

	function initialize_twig(){
		$options = array(
			//'cache' => TEF_DIR . '/cache',
		);

		if(defined('WP_DEBUG') && WP_DEBUG)
			$options['debug'] = true;

		$this->twig = new \Twig_Environment($this->twig_loader, $options );

		if(defined('WP_DEBUG') && WP_DEBUG)
			$this->twig->addExtension(new \Twig_Extension_Debug());

		$this->twig->addFunction( '__', new \Twig_SimpleFunction( '__', function ( $text, $domain = 'default' ) {
				return __( $text, $domain );
		} ) );

	}

	/**
	 *
	 * @param unknown $taxonomy
	 */
	static function display_add_form_fields( $taxonomy ){

		$fieldList = new FieldList( array("all", $taxonomy) );
		$fieldList->set_from_db();

		if($fieldList->count_fields()){

			foreach($fieldList->get_fields() as $field){
				$data = array(
					'name' => $field->get_name(),
					'label' => $field->get_label(),
					'value' => $field->get_default(),
					'description' => $field->get_description(),
					'options' => $field->get_options(),
				);

				// Render $Field
				echo get_TEFUI()->render('/fields/add/'.strtolower($field->get_type()), $data);
			}

		}

	}

	/**
	 *
	 * @param unknown $term
	 */
	static function display_edit_form_fields($term, $taxonomy){

		$taxonomy = $term->taxonomy;
		$fieldList = new FieldList( array("all", $taxonomy) );
		$fieldList->set_from_db();

		if($fieldList->count_fields()){

			foreach($fieldList->get_fields() as $field){

				$data = array(
					'name' => $field->get_name(),
					'label' => $field->get_label(),
					'value' => $field->get_saved_value( $term->term_id ),
					'description' => $field->get_description(),
					'options' => $field->get_options(),
				);

				// Render $Field
				echo get_TEFUI()->render('/fields/edit/'.strtolower($field->get_type()), $data);
			}

		}


	}

	/**
	 * Foreach all fields for an taxonomy and check if exists, validate and save fields values.
	 */
	static function save_form_fields($term_id, $tt_id){

		$term = get_term($term_id);

		if(!$term)
			return false;

		// Create FielList (For all)
		$FieldList = new FieldList( array('all', $term->taxonomy) );
		$FieldList->set_from_db();

		// Foreach both FieldLists
		foreach ($FieldList->get_fields() as $field){

			// Check if data has sent
			if(isset( $_POST['term_meta'][$field->get_name()]) ){

				// Check if data value is valid for this field
				if($field->validate_value( $_POST['term_meta'][$field->get_name()] )){

					// Save
					$field->save_value($term->term_id, $_POST['term_meta'][$field->get_name()]);

				}

			}

		}

	}

	/**
	 * Register add, edit and save actions for the taxonomies
	 */
	static function register_term_actions(){


		$taxonomies = tef_get_taxonomies();

		// Register actions to all displayed taxonomies
		if(FieldList::count("all")){

			foreach( array_keys($taxonomies) as $taxonomy){

				if("all" == $taxonomy)
					continue;

				// Form Add new Term
				add_action($taxonomy.'_add_form_fields', array(get_called_class(), "display_add_form_fields"), 10, 1);

				// Form Edit Term
				add_action($taxonomy.'_edit_form_fields', array(get_called_class(), "display_edit_form_fields"), 10, 2);

				// On edit Term
				add_action( 'edited_'.$taxonomy, array(get_called_class(),"save_form_fields"), 10, 2 );

				// On create Term
				add_action( 'create_'.$taxonomy, array(get_called_class(),"save_form_fields"), 10, 2 );

			}

		}

		// Display only for taxonomies that have customs fields
		else{

			foreach( array_keys($taxonomies) as $taxonomy){

				if("all" == $taxonomy)
					continue;

				if(FieldList::count( $taxonomy )){
					// Form Add new Term
					add_action($taxonomy.'_add_form_fields', array(get_called_class(), "display_add_form_fields"), 10, 1);

					// Form Edit Term
					add_action($taxonomy.'_edit_form_fields', array(get_called_class(), "display_edit_form_fields"), 10, 2);

					// On edit Term
					add_action( 'edit_'.$taxonomy, array(get_called_class(),"save_form_fields"), 10, 2 );

					// On create Term
					add_action( 'create_'.$taxonomy, array(get_called_class(),"save_form_fields"), 10, 2 );

				}

			}

		}


	}
}

UI::register_term_actions();
