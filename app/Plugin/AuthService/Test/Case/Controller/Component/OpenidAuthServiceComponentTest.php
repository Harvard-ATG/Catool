<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Configure', 'Core');
App::uses('Controller', 'Controller');
App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('ComponentCollection', 'Controller');
App::uses('OpenidAuthServiceComponent', 'AuthService.Controller/Component');

/**
 * OpenidAuthServiceComponent Test Case
 *
 * 	- To run this test case:
 * 		cake testsuite AuthService Controller/Component/OpenidAuthServiceComponent
 */
class OpenidAuthServiceComponentTestCase extends CakeTestCase {
	public $OpenidAuthServiceComponent = null;
	public $Controller = null;
	public $_SERVER = null;
	public $fixtures = array('plugin.auth_service.openid_user', 'app.user');

	public function setUp() {
		parent::setUp();

		$this->setServerEnv();
		$Collection = new ComponentCollection();
		$this->OpenidAuthServiceComponent = new OpenidAuthServiceComponent($Collection);
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$this->Controller = new Controller($CakeRequest, $CakeResponse);
		$this->OpenidAuthServiceComponent->initialize($this->Controller);
		$this->OpenidAuthServiceComponent->startup($this->Controller);
	}

	public function tearDown() {
		unset($this->OpenidAuthServiceComponent);
		unset($this->Controller);
		$this->unsetServerEnv();

		parent::tearDown();
	}

	public function setServerEnv() {
		$this->_SERVER = $_SERVER;
		$_SERVER = 	array(
		  'HTTP_HOST' => 'localhost',
		  'QUERY_STRING' => '',
		  'REQUEST_METHOD' => 'GET',
		  'REQUEST_URI' => '/env.php',
		  'SCRIPT_NAME' => '/env.php',
		  'SERVER_NAME' => 'localhost'
		);
	}

	public function unsetServerEnv() {
		$_SERVER = $this->_SERVER;
	}

	public function testLogin() {
		$mock = $this->getMock('LightOpenID', array(), array(env('SERVER_NAME')));
		$this->assertInstanceOf('LightOpenID', $mock);
	}
	
	public function test_extractOpenIDAttributes() {
		$given = array(
			'namePerson/first' => 'John', 
			'namePerson/last' => 'Smith', 
			'contact/email' => 'john_smith@test.com'
		);
			
		$expected = array(
			'firstname' => 'John', 
			'lastname' => 'Smith', 
			'email' => 'john_smith@test.com'
		);
			
		$result = $this->OpenidAuthServiceComponent->_extractOpenIDAttributes($given);
		$this->assertEqual($result, $expected, 'converted list of openid attributes');
	}

	public function test_existsOpenIDUser() {
		$testUsers = array(
			array('https://www.google.com/accounts/o8/id?id=AItOawl8oosmkjU28MlsCb3zNg-nr1-wxJ_bjQc', true, 'user should exist'),
			array('https://www.google.com/accounts/o8/id?id=DOES_NOT_EXIST', false, 'user should not exist')
		);
		
		foreach($testUsers as $testUser) {
			list($claimed_id, $expected, $message) = $testUser;
			$result = $this->OpenidAuthServiceComponent->_existsOpenIDUser($claimed_id);
			$this->assertEqual($result, $expected, $message);
		}
	}
	
	public function test_registerOpenIDUser() {
		$claimed_id = 'https://www.google.com/accounts/o8/id?id=IM_MATT_DAMON_YO';
		$attributes = array(
			'namePerson/first' => 'Matt', 
			'namePerson/last' => 'Damon', 
			'contact/email' => 'matt_damon@hollywood.com');
		
		$this->OpenidAuthServiceComponent->_registerOpenIDUser($claimed_id, $attributes);
		$this->assertTrue($this->OpenidAuthServiceComponent->_existsOpenIDUser($claimed_id), 'openid user created');
		
		$data = $this->OpenidAuthServiceComponent->Controller->OpenidUser->findClaimedId('first', $claimed_id);
		$this->assertTrue($this->OpenidAuthServiceComponent->Controller->User->exists($data['OpenidUser']['user_id']), 'openid user linked to a user account');
		
		$this->assertEqual($data['User']['name'], $attributes['namePerson/first'].' '.$attributes['namePerson/last'], 'check attribute - openid user name');
		$this->assertEqual($data['User']['email'], $attributes['contact/email'], 'check attribute - openid user email');
	}
}
