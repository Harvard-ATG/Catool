<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Target', 'Model');

/**
 * Target Test Case
 *
 * @package app.Test.Model
 */
class TargetTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.target', 'app.collection', 'app.resource', 'app.note', 'app.user', 'app.segment');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Target = ClassRegistry::init('Target');
	}

/**
 * testSaveTargetType method
 * 
 * @return void
 */
	public function testSaveTargetType() {
		$testTargetType = 'foobar';
		$testData = array(
			array('Target' => array(
				'id' => 1000,
				'type' => null,
				'collection_id' => 1,
				'resource_id' => 1,
				'display_name' => 'Testing with unspecified target type'		
			)),
			array('Target' => array(
				'id' => 1001,
				'type' => 'hamANDeggs',
				'collection_id' => 1,
				'resource_id' => 1,
				'display_name' => 'Testing with target type; should be overridden by model'
			))
		);
		
		$this->Target->targetType = $testTargetType;
		
		$saved = $this->Target->saveAll($testData);
		$this->assertTrue($saved, 'saved target test data');
		
		$result = $this->Target->find('all', array(
			'recursive' => -1,
			'conditions' => array('Target.id' => array(1000,1001)),
			'order' => 'Target.id'
		));
		
		$this->assertEquals(count($testData), count($result));
		foreach($result as $target) {
			$this->assertEquals($testTargetType, $target['Target']['type'], 'target type automatically set on save');
		}
	}

/**
 * testGetNeighbors method
 * 
 * @return void
 */
	public function testGetNeighbors() {

		// check invalid target
		$neighbors = $this->Target->getNeighbors(-1);
		foreach(array('next', 'prev') as $dir) {
			$this->assertArrayHasKey($dir, $neighbors, "result should contain $dir key");
			$this->assertNull($neighbors[$dir], "$dir should not exist because target id was invalid");
		}

		// check that we have prev and next as well as expected properties for both
		$neighbors = $this->Target->getNeighbors(2);
		$this->assertNotNull($neighbors['prev'], 'middle target should have a previous neighbor');
		$this->assertArrayHasKey('id', $neighbors['prev'], 'middle target prev should have id');
		$this->assertArrayHasKey('type', $neighbors['prev'], 'middle target prev should have type');
		$this->assertEquals('1', $neighbors['prev']['id'], 'middle target id should match');
		$this->assertEquals('image', $neighbors['prev']['type'], 'middle target type should match');

		$this->assertNotNull($neighbors['next'], 'middle target should have a next neighbor');
		$this->assertArrayHasKey('id', $neighbors['next'], 'middle target next should have id');
		$this->assertArrayHasKey('type', $neighbors['next'], 'middle target next should have type');
		$this->assertEquals('3', $neighbors['next']['id'], 'middle target id should match');
		$this->assertEquals('video', $neighbors['next']['type'], 'middle target type should match');

		// check leftmost
		$neighbors = $this->Target->getNeighbors(1);
		$this->assertNull($neighbors['prev'], 'first target in collection should NOT have a previous neighbor');
		$this->assertNotNull($neighbors['next'], 'first target in collection should have a next neighbor');

		// check rightmost
		$neighbors = $this->Target->getNeighbors(6);
		$this->assertNotNull($neighbors['prev'], 'last target in collection should have a previous neighbor');
		$this->assertNull($neighbors['next'], 'last target in collection should NOT have a next neighbor');
		
		// check hidden neighbor
		$neighbors = $this->Target->getNeighbors(6, array('onlyVisible' => true));
		$this->assertNotNull($neighbors['prev']);
		$this->assertNotEquals('5', $neighbors['prev']);
		
		$neighbors = $this->Target->getNeighbors(6, array('onlyVisible' => false));
		$this->assertNotNull($neighbors['prev']);
		$this->assertEquals('5', $neighbors['prev']['id'], 'previous neighbor should be hidden');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Target);

		parent::tearDown();
	}

}
