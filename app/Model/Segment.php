<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Segment Model
 *
 * @package       app.Model
 * @property Annotation $Annotation
 */
class Segment extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'note_id'
		)
	);
}
