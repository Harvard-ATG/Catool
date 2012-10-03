<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Resource Model
 *
 * @package       app.Model
 * @property Target $Target
 */
class Resource extends AppModel {

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'resource_id'
		)
	);

/**
 * validate
 * 
 */
	public $validate = array(
		'duration' => array(
			'rule' => 'naturalNumber',
			'message' => 'Please supply the duration in seconds.',
			'allowEmpty' => false
		)
	);
}
