<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * OpenidUserFixture
 *
 */
class OpenidUserFixture extends CakeTestFixture {
/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'AuthService.OpenidUser';
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'type' => 'openid',
			'claimed_id' => 'https://www.google.com/accounts/o8/id?id=AItOawl8oosmkjU28MlsCb3zNg-nr1-wxJ_bjQc',
			'user_id' => 1010,
			'created' => '2012-01-01 01:23:45',
		),
		array(
			'id' => 2,
			'type' => 'openid',
			'claimed_id' => 'https://www.google.com/accounts/o8/id?id=BXtOawl8oosmkjU28MlsCb3zNg-nr1-wxJ_bjQc',
			'user_id' => 2020,
			'created' => '2012-02-01 01:01:30',
		)
	);
}
