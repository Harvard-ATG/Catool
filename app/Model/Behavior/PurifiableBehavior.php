<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
App::import('Vendor', 'HTMLPurifier', array('file' => 'htmlpurifier-4.4.0'.DS.'library'.DS.'HTMLPurifier.auto.php'));

/**
 * Purifiable Behavior
 *
 * Enables a model to sanitize HTML input using the HTML Purifier
 * library, which is a standards-compliant HTML filter library written
 * in pure PHP.
 *
 * See also: http://htmlpurifier.org/
 */
class PurifiableBehavior extends ModelBehavior {
/**
 * Settings per model.
 *
 * @var array
 */
	public $settings = array();

/**
 * Default settings.
 *
 * @var array
 */
	public static $defaultSettings = array(
		'fields' => array(), // list of fields to purify
		'overwrite' => true, // overwrites the field with clean data
		'keepDirty' => false, // keeps a copy of the dirty field data
		'dirtySuffix' => '_dirty', // for when keepDirty = true
		'cleanSuffix' => '_clean' // for when overwrite = false
	);

/**
 * Setup this behavior with the specified configuration settings.
 * 
 * @param Model $Model Model using this behavior
 * @param array $settings Configuration settings for $model
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$merged_settings = array_merge(self::$defaultSettings, $settings);
		
		// sanity check the settings to make sure we don't have any unexpected or invalid combinations
		if($merged_settings['dirtySuffix'] == $merged_settings['cleanSuffix']) {
			throw new CakeException("Purifiable configuration error. Dirty suffix and clean suffix should be different.");
		}
		if($merged_settings['keepDirty'] && empty($merged_settings['dirtySuffix'])) {
			throw new CakeException("Purifiable configuration error. In order to keep dirty data, you must specify a dirty suffix.");
		}
		if(!$merged_settings['overwrite'] && empty($merged_settings['cleanSuffix'])) {
			throw new CakeException("Purifiable configuration error. In order to keep clean data, you must specify a clean suffix.");			
		}
		
		$this->settings[$Model->alias] = $merged_settings;
	}

/**
 * beforeValidate callback
 *
 * @param Model $Model Model using this behavior
 * @return boolean False or null will abort the operation. Any other result will continue.
 */
	public function beforeValidate(Model $Model) {
		return true;
	}

/**
 * beforeSave callback
 *
 * @param Model $Model Model using this behavior
 * @param array $options
 * @return boolean
 */
	public function beforeSave(Model $Model, $options = array()) {
		$fields = isset($this->settings[$Model->alias]['fields']) ? $this->settings[$Model->alias]['fields'] : array();

		foreach($fields as $field) {
			if(isset($Model->data[$Model->alias][$field])) {
				$purified_data = self::purifyFieldHTML($field, $Model->data[$Model->alias][$field], $this->settings[$Model->alias]);
				foreach($purified_data as $key => $value) {
					$Model->data[$Model->alias][$key] = $value;
				}
			}
		}

		return true;
	}

/**
 * Purifies the html for a given field name.
 * 
 * @param string $field_name 
 * @param string $dirty_html
 * @param array $settings
 * @return array of purified field data
 */
	public static function purifyFieldHTML($field_name, $dirty_html, $settings = array()) {
		$settings = array_merge(self::$defaultSettings, $settings);

		$overwrite = $settings['overwrite'];
		$keepDirty = $settings['keepDirty'];
		$cleanSuffix = $settings['cleanSuffix'];
		$dirtySuffix = $settings['dirtySuffix'];

		$clean_field_name = $overwrite ? $field_name : $field_name . $cleanSuffix;
		$dirty_field_name = $field_name . $dirtySuffix;
		
		$purifier = self::makeHTMLPurifier();

		$purified_data = array();
		$purified_data[$clean_field_name] = $purifier->purify($dirty_html);
		if($keepDirty) {
			$purified_data[$dirty_field_name] = $dirty_html;
		}

		return $purified_data;
	}

/**
 * Makes an instance of HTML Purifier.
 * 
 * @return HTMLPurifier object
 */
	public static function makeHTMLPurifier() {
		$config = HTMLPurifier_Config::createDefault();
		$purifier = new HTMLPurifier($config);
		return $purifier;
	}
}
