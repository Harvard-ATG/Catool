<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Segment', 'Model');

/**
 * Segment Test Case
 *
 * @package app.Test.Model
 */
class SegmentTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.segment', 'app.note', 'app.target', 'app.user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Segment = ClassRegistry::init('Segment');
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
		unset($this->Segment);

		parent::tearDown();
	}

}
