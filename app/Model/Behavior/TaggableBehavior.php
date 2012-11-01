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
}