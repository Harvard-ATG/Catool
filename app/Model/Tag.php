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
class Tag extends AppModel {

/**
 * Constant for the maximum length of a tag name.
 */
	const NAME_MAX_LENGTH = 50;


/**
 * Validation rules for tags.
 *
 * @var array
 */
	public $validate = array(
		'name' => array(
			'rule' => array('maxLength', self::NAME_MAX_LENGTH),
			'message' => array('Maximum 50 characters long')
		)
	);
}
