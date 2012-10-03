<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('NavRendererHelper', 'View/Helper');

/**
 * NavRendererHelperTest
 * 
 * @package app.Test.View
 */
class NavRendererHelperTest extends CakeTestCase {
	/**
	 * NavRenderer
	 * @var string
	 */
	public $NavRenderer;
	/**
	 * testPrimaryItems
	 * @var array
	 */
	public $testPrimaryItems = array(
		array('name' => 'Home', 'url' => '/'),
		array('name' => 'About','url' => '/about'),
		array('name' => 'Contact', 'url' => '/contact')
	);
	
	/**
	 * setUp
	 */
	public function setUp() {
		$CakeRequest = new CakeRequest();
		$CakeResponse = new CakeResponse();
		$Controller = new Controller($CakeRequest, $CakeResponse);
		$View = new View($Controller);
		$this->NavRenderer = new NavRendererHelper($View);
	}
	
	/**
	 * testPrimary
	 */
	public function testPrimary() {
		$result = $this->NavRenderer->primary($this->testPrimaryItems);
		$this->assertPattern('!<ul.*Home.*</ul>!', $result, 'primary nav should be an html list');
	}
	
	/**
	 * testgetUserName
	 */
	public function testgetUserName() {
		$result = $this->NavRenderer->getUserName(array(
			'id' => 1,
			'name' => 'Boo Radley',
		));
		$this->assertPattern('!Boo.+Radley!', $result);
	}

	/**
	 * testgetUserNameMissingUserId
	 */
	public function testgetUserNameMissingUserId() {
		$result = $this->NavRenderer->getUserName(array(
			'name' => 'Boo Radley'
		));
		$this->assertEqual($result, '');
	}
	
	/**
	 * testgetUserNameMissingName
	 */
	public function testgetUserNameMissingName() {
		$result = $this->NavRenderer->getUserName(array(
			'id' => 1,
			'name' => ''
		));
		$this->assertPattern('!^\s*$!', $result);
	}
}
