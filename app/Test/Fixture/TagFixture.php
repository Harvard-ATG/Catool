<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TagFixture
 *
 */
class TagFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'Tag';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array('id' => 1, 'name' => 'foo'),
		array('id' => 2, 'name' => 'foo+bar'),
		array('id' => 3, 'name' => 'moe larry and curly'),
		array('id' => 4, 'name' => 'Фёдор Михайлович Достоевский'),
		array('id' => 5, 'name' => '서울의 빛')

	);
}