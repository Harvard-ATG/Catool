<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * UserFixture
 *
 */
class UserFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'User';
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'name' => 'John Smith',
			'email' => 'john@smith.localhost',
			'created' => '2011-01-01 16:47:13',
			'modified' => '2012-02-01 16:47:13',
			'role_id' => 1
		),
		array(
			'id' => 2,
			'name' => 'Robert Baratheon',
			'email' => 'robert@kingslanding.localhost',
			'created' => '2011-01-01 16:47:13',
			'modified' => '2012-02-01 16:47:13',
			'role_id' => 2
		),
		array(
			'id' => 3,
			'name' => 'Ned Stark',
			'email' => 'ned@winterfell.localhost',
			'created' => '2011-01-01 16:47:13',
			'modified' => '2012-02-01 16:47:13',
			'role_id' => 4
		),
		array(
			'id' => 4,
			'name' => 'Daenerys Targaryen',
			'email' => 'dragon_mother@qarth.localhost',
			'created' => '2011-01-01 16:47:13',
			'modified' => '2012-02-01 16:47:13',
			'role_id' => 4
		),
		array(
			'id' => 5,
			'name' => 'Khal Drogo',
			'email' => 'drogo@fareast.localhost',
			'created' => '2011-01-01 16:47:13',
			'modified' => '2012-02-01 16:47:13',
			'role_id' => 5
		),
		array(
			'id' => 6,
			'name' => null,
			'email' => null,
			'created' => null,
			'modified' => null,
			'role_id' => 5
		)
	);
}
