<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Taggable Behavior
 * 
 * Enables a model to save and retrieve tags.
 * 
 * In order for tags to be saved, models must contain a foreign key named 
 * 'tag_collection_id'. The behavior will look for a 'tags' field in the model
 * which may either be an array of tag names or a comma-separated string of tags.
 *
 * During the save operation, the behavior will save/update/delete the tags and set
 * the 'tag_collection_id' foreign key to reference the appropriate tag collection 
 * containing those tags or NULL if it doesn't have any tags. During find operations, 
 * the behavior will automatically include an array of Tag models in the result set.
 *
 * NOTE: models must contain a 'tag_collection_id' foreign key to use this behavior.
 * NOTE: tag collections may not be unique to model.
 *
 *
 * @package       app.Model.Behavior
 */
class TaggableBehavior extends ModelBehavior {
	
/**
 * Settings per model.
 *
 * @var array
 */
	public $settings = array();

/**
 * Setup this behavior with the specified configuration settings.
 * 
 * @param Model $Model Model using this behavior
 * @param array $settings Configuration settings for $model
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$default_settings = array('foreignKey' => 'tag_collection_id');
		$this->settings[$Model->alias] = array_merge($default_settings, $settings);
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
		return true;
	}

/**
 * afterSave callback
 *
 * @param Model $Model Model using this behavior
 * @param boolean $created True if this save created a new record
 * @return void
 */
	public function afterSave(Model $Model, $created) {
		return true;
	}

/**
 * afterFind callback
 *
 * @param Model $Model Model using this behavior
 * @param mixed $result The results of the find operation
 * @param boolean $primary Whether this model is being queried directly (vs. being queried as an association)
 * @return void
 */
	public function afterFind(Model $Model, $result, $primary) {
		return $result;
	}

}