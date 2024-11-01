<?php
/*
Plugin Name: Taxonomy Extra Fields (BETA)
Plugin URI: http://tef.guillermogarcia.info
Description: "Taxonomy Extra Fields" is a plugin for create and manage your own custom fields for all public taxonomies  of Wordpress (natives or created by the owner), directly from admin interface.
Version: 0.6.04
Author: Guillermo Garcia (@Flewps)
Author URI: http://guillermogarcia.info
Text Domain: tef
Domain Path: /languages

Copyright (C) 2016 Guillermo Garcia (@Flewps) <http://guillermogarcia.info>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

*/
defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

global $wpdb;

/*
 * Configuration
 */
define('TEF_DIR', plugin_dir_path( __FILE__ ));
define('TEF_BASENAME', plugin_basename( dirname( __FILE__ ) ));
define('TEF_URL', plugins_url(null, __FILE__));
define('TEF_FIELD_TABLE_NAME', $wpdb->prefix."tef_fields");

/*
 * Load required classes
 */
require_once 'library/class/LoadClasses.php';
require_once 'library/class/Core.php';

/*
 * Load required files
 */
require_once 'library/functions.php';

/*
 * Initialize the plugin
 */
\tef\Core::init();
