<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('UserCollection', 'Model');

/**
 * UserCollection Test Case
 *
 * @package app.Test.Model
 */
class UserCollectionTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.user', 'app.role', 'app.collection', 'app.user_collection', 'app.note');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->UserCollection = ClassRegistry::init('UserCollection');
	}

/**
 * testUserIsAdmin
 * 
 * @return void
 */
	public function testIsAdmin() {
		$collection_id = 1;
		$model = $this->UserCollection;
		$this->assertTrue($model->isAdmin(1, $collection_id), 'admin should have admin permission');
		$this->assertTrue($model->isAdmin(2, $collection_id), 'admin should have admin permission');
		$this->assertFalse($model->isAdmin(3, $collection_id), 'mod should NOT have admin permission');
		$this->assertFalse($model->isAdmin(4, $collection_id), 'user should NOT have admin permission');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->UserCollection);

		parent::tearDown();
	}

}
