<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * CollectionFixture
 *
 */
class CollectionFixture extends CakeTestFixture {
/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'Collection';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'display_name' => 'Lorem ipsum',
			'display_description' => 'Lorem ipsum dolor sit amet',
			'created' => '2011-02-03 11:27:15',
			'modified' => '2011-02-03 16:27:15'
		),
		array(
			'id' => 2,
			'display_name' => 'Foo Bar',
			'display_description' => 'Test',
			'created' => '2012-01-01 16:27:15',
			'modified' => '2012-01-01 16:27:15'
		),
		array(
			'id' => 3,
			'display_name' => 'Hello World',
			'display_description' => 'Greetings Earthlings',
			'created' => '2012-02-03 16:27:15',
			'modified' => '2012-02-03 16:27:15'
		)
	);
}
