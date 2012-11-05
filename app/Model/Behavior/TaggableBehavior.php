<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
App::uses('Tag', 'Model');
App::uses('TagCollection', 'Model');
App::uses('TagCollectionTag', 'Model');

/**
 * Taggable Behavior
 * 
 * Enables a model to save and retrieve tags.
 * 
 * In order for tags to be saved, models must contain a foreign key named 
 * 'tag_collection_id'. The behavior will look for a 'tags' field in the model
 * which may either be an array of tag names or a comma-separated string of tags.
 *
 * During the save operation, the behavior will validate and save/update/delete the tags.
 * The 'tag_collection_id' foreign key will reference the appropriate tag collection 
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
 * Constant for the tag field in the model.
 */
	const TAG_FIELD = 'tags';

/**
 * Constant for the name of the tag collection foreign key in the model.
 */
	const TAG_FOREIGN_KEY = 'tag_collection_id';
 
/**
 * Settings per model.
 *
 * @var array
 */
	public $settings = array();
	
/**
 * Default settings for each model.
 *
 * @var array
 */
	public $defaultSettings = array(
		'maxTags' => 10
	);
	
/**
 * Tag Collection model.
 * 
 * @var Model
 */
	public $TagCollection;
	
/**
 * Tag model.
 * 
 * @var Model
 */
	public $Tag;

/**
 * Setup this behavior with the specified configuration settings.
 * 
 * @param Model $Model Model using this behavior
 * @param array $settings Configuration settings for $model
 * @return void
 */
	public function setup(Model $Model, $settings = array()) {
		$this->settings[$Model->alias] = array_merge($this->defaultSettings, $settings);
		
		$this->Tag = ClassRegistry::init('Tag');
		$this->TagCollection = ClassRegistry::init('TagCollection');
	}

/**
 * beforeValidate callback
 *
 * @param Model $Model Model using this behavior
 * @return boolean False or null will abort the operation. Any other result will continue.
 */
	public function beforeValidate(Model $Model) {
		if(isset($Model->data[$Model->alias][self::TAG_FIELD])) {
			$tags = $this->TagCollection->parseTags($Model->data[$Model->alias][self::TAG_FIELD]);
			if(empty($tags)) {
				return true;
			}
			
			// check number of tags
			if(isset($this->settings[$Model->alias]['maxTags']) && count($tags) > $this->settings[$Model->alias]['maxTags']) {
				$Model->invalidate(self::TAG_FIELD, __('Too many tags. Maximum number of tags: %d', $this->settings[$Model->alias]['maxTags']));
				return false;
			}
			
			// check length of tags
			foreach($tags as $tag) {
				$this->Tag->create();
				$this->Tag->set('name', $tag);
				if(!$this->Tag->validates()) {
					$Model->invalidate(self::TAG_FIELD, __('Invalid tag: %s. Max length: %d', $tag, Tag::NAME_MAX_LENGTH));
					return false;
				}
			}
		}

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
		if(isset($Model->data[$Model->alias][self::TAG_FIELD])) {
			$tags = $Model->data[$Model->alias][self::TAG_FIELD];
			$tag_collection_id = $this->TagCollection->saveTags($tags);
			
			if(isset($Model->data[$Model->alias][self::TAG_FOREIGN_KEY])) {
				$this->TagCollection->decrementInstances($Model->data[$Model->alias][self::TAG_FOREIGN_KEY]);
			}

			$Model->data[$Model->alias][self::TAG_FOREIGN_KEY] = $tag_collection_id;
		}

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
		if(isset($Model->data[$Model->alias][self::TAG_FOREIGN_KEY])) {
			$this->TagCollection->incrementInstances($Model->data[$Model->alias][self::TAG_FOREIGN_KEY]);
		}
	}

/**
 * afterDelete callback
 *
 * @param Model $Model Model using this behavior
 * @return boolean
 */	
	public function beforeDelete(Model $Model, $cascade = true) {
		$tag_collection_id = $Model->field(self::TAG_FOREIGN_KEY);
		if(isset($tag_collection_id)) {
			$this->TagCollection->decrementInstances($tag_collection_id);
		}	
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
		// we only want to retrieve tags if the model was queried directly
		if(!$primary) {
			return $result;
		}
		
		// pre-process result to find which collections we need to lookup
		$tag_collection_ids = array();
		foreach($result as $row) {
			if(isset($row[$Model->alias][self::TAG_FOREIGN_KEY])) {
				$id = $row[$Model->alias][self::TAG_FOREIGN_KEY];
				$tag_collection_ids[$id] = true;
			}
		}
		$tag_collection_ids = array_keys($tag_collection_ids);
		
		// fetch the tags from the db
		$tags_for = $this->TagCollection->findAllTagsFor($tag_collection_ids);

		// augment the result set with tags
		foreach($result as &$row) {
			if(isset($row[$Model->alias][self::TAG_FOREIGN_KEY])) {
				$id = $row[$Model->alias][self::TAG_FOREIGN_KEY];
				if(isset($tags_for[$id])) {
					$row['Tag'] = $tags_for[$id];
				}
			}
		}

		return $result;
	}

}