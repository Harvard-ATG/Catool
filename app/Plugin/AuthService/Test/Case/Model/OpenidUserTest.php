<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('OpenidUser', 'AuthService.Model');

/**
 * OpenidUser Test Case
 *
 */
class OpenidUserTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.auth_service.openid_user', 'app.user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OpenidUser = ClassRegistry::init('OpenidUser');
	}
	
/**
 * testIdentityType method
 * 
 * @return void
 */
	public function testIdentityType() {
		$this->assertEqual($this->OpenidUser->identityType, 'openid');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OpenidUser);

		parent::tearDown();
	}

}
