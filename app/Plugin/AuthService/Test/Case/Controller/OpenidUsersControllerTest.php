<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('OpenidUsersController', 'AuthService.Controller');


/**
 * OpenidUsersController Test Case
 *
 */
class OpenidUsersControllerTestCase extends ControllerTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('plugin.auth_service.openid_user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->OpenidUsers = new OpenidUsersController();
		$this->OpenidUsers->constructClasses();
	}
	
	

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->OpenidUsers);

		parent::tearDown();
	}

/**
 * testLogin method
 *
 * @return void
 */
	public function testComponents() {
		$this->assertInstanceOf('OpenidAuthServiceComponent', $this->OpenidUsers->OpenidAuthService);
	}
}
