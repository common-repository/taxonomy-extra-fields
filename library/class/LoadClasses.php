<?php
namespace tef;

defined( 'ABSPATH' ) or die('Don\'t touch the eggs, please!');

/**
 *
 * @author GuilleGarcia
 *
 */
class LoadClasses{

	static function load_classes(){
		// General
		require_once 'DB.php';

		// User Interface
		require_once 'UI/UI.php';
		require_once 'UI/FieldController.php';
		require_once 'UI/TaxonomyController.php';

		// Auxiliary
		require_once 'Auxiliary/TaxonomiesListTable.php';
		require_once 'Auxiliary/TaxonomyFieldsTable.php';

		// Fields
		require_once 'Field/FieldsList.php';
		require_once 'Field/Field.php';
		require_once 'Field/NoTypeField.php';
		require_once 'Field/FileField.php';
		require_once 'Field/ImageField.php';
		require_once 'Field/LongTextField.php';
		require_once 'Field/NumberField.php';
		require_once 'Field/OptionsField.php';
		require_once 'Field/SelectField.php';
		require_once 'Field/RadioField.php';
		require_once 'Field/CheckboxField.php';
		require_once 'Field/TextField.php';

	}

}
