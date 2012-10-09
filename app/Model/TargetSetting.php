<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * TargetSetting Model
 *
 * @package       app.Model
 */
class TargetSetting extends AppModel {
/**
 * hasMany associations
 *
 * @var array
 */
	public $hasOne = array(
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'target_setting_id'
		),
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'target_setting_id'
		)
	);
}
