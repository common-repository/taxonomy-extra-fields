=== Plugin Name ===
Contributors: guillegarcia
Donate link: http://tef.guillermogarcia.info
Tags: taxonomy, extra, fields, custom, terms, tags, tag, category, categories
Requires at least: 4.4.0
Tested up to: 4.4
Stable tag: 4.4
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Add your own custom fields to all wordpress taxonomies, easily and efficiently, directly from admin UI.

== Description ==

"Taxonomy Extra Fields" is a plugin for create and manage your own custom fields for all public taxonomies  of Wordpress (natives or created by the owner), directly from admin interface.

Thanks to:
* Twig <http://twig.sensiolabs.org/>
* jQuery (Noty) v2 <http://ned.im/noty/>
* Daniel Eden for 'animate.css' <https://daneden.github.io/animate.css/>
* Font Awesome <http://fontawesome.io/>

== Installation ==
1. Download clicking directly on the Download button.
2. Extract the zip content into your wordpress plugins directory: /path/to/your/wordpress/wp-content/plugins/
3. Go to WP Admin plugins: wp-admin/plugins.php
4. Activate the plugin **Taxonomy Extra Fields**
5. Enjoy it

== Screenshots ==
1. Taxonomies list
2. Taxonomy fields
3. Add/edit field form

== Changelog ==

= 0.0.01 =
* Begins development

= 0.5.00 =
* Create beta version

= 0.6.00 =
* Add new Fields types
* Create credits page
* First release

= 0.6.01 =
* Add support for file and image fields types

= 0.6.02 =
* Error solved in add actions to taxonomies
* Updated feautes about Field and Image types (validate, show, save, display...)

= 0.6.03 =
* Delete debug output strings
* Add Twig DEBUG support
* Add new abstract field-type class: OptionsField
* Refactorization of Select, Radio and Checkbox fields class

= 0.6.04 =
* Add radio and checkbox templates (previously forgotten)

== Future Goals ==
* Add new field types: geoposition...
* Add Shortcodes and Frontend/Backend outputs
* Add new fields options: multiple
* Create/manage/delete new taxonomy
* Support for non-ajax functions.
* Add javascript validation to add/edit term form
