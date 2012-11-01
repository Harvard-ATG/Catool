<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

/**
 * Tag Model
 *
 * @package       app.Model
 */
class TagCollectionTag extends AppModel {

/**
 * belongsTo association
 *
 * @var array
 */
	public $belongsTo = array(
		'Tag' => array(
			'foreignKey' => 'tag_id',
			'type' => 'INNER'
		),
		'TagCollection' => array(
			'foreignKey' => 'tag_collection_id',
			'type' => 'INNER',
			'counterCache' => 'tag_count'
		)
	);
}
