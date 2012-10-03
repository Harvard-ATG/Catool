<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * RoleFixture
 *
 */
class RoleFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'Role';

/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'name' => 'super',
			'display_name' => 'Super Administrator',
			'parent_id' => null,
			'lft' => 1,
			'rght' => 10
		),
		array(
			'id' => 2,
			'name' => 'admin',
			'display_name' => 'Administrator',
			'parent_id' => 1,
			'lft' => 2,
			'rght' => 9
		),
		array(
			'id' => 3,
			'name' => 'mod',
			'display_name' => 'Moderator',
			'parent_id' => 2,
			'lft' => 3,
			'rght' => 8
		),
		array(
			'id' => 4,
			'name' => 'user',
			'display_name' => 'Member',
			'parent_id' => 3,
			'lft' => 4,
			'rght' => 7
		),
		array(
			'id' => 5,
			'name' => 'guest',
			'display_name' => 'Guest',
			'parent_id' => 4,
			'lft' => 5,
			'rght' => 6
		)
	);
}
