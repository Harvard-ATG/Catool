<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Resource', 'Model');

/**
 * Resource Test Case
 *
 * @package app.Test.Model
 */
class ResourceTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.resource', 'app.target');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Resource = ClassRegistry::init('Resource');
	}

/**
 * testDummy method
 * 
 * @return void
 */
	public function testDummy() {
		
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Resource);

		parent::tearDown();
	}

}
