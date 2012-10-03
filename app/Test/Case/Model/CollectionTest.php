<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Collection', 'Model');

/**
 * Collection Test Case
 *
 * @package app.Test.Model
 */
class CollectionTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.collection', 'app.user_collection', 'app.target', 'app.note', 'app.user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = ClassRegistry::init('Collection');
	}

/**
 * testFind method
 *
 * @return void
 */
	public function testFind() {
		$result = $this->Collection->find('first', array(
			'recursive' => -1,
			'conditions' => array(
				'Collection.id' => 1
		)));

		$this->assertEqual($result['Collection']['display_name'], 'Lorem ipsum');
		$this->assertEqual($result['Collection']['display_description'], 'Lorem ipsum dolor sit amet');
	}

/**
 * testFindTargetsWith method
 * 
 * @return void
 */
	public function testFindTargetsWith() {
		$result = $this->Collection->findTargetsWith('This is the name', 1);
		$this->assertTrue(count($result) > 0, 'matched on name');
		
		$result = $this->Collection->findTargetsWith('This is the description', 1);
		$this->assertTrue(count($result) > 0, 'matched on description');
		
		$result = $this->Collection->findTargetsWith('Supercalifrag%expialidocious', 1);
		$this->assertTrue(count($result) > 0, 'matched on description using wild card');
	}

/**
 * getTargetStats method
 *
 * @return void
 */
	public function testGetTargetStats() {
		$result = $this->Collection->Target->find("first", array(
			"recursive" => -1,
			"conditions" => array("Target.collection_id" => 1),
			"fields" => "COUNT(*) AS num_targets"
		));
		
		$num_targets = $result[0]['num_targets'];
		$note_stats_for = $this->Collection->getTargetStats(1);
		$this->assertEquals($num_targets, count(array_keys($note_stats_for)));

		foreach($note_stats_for as $target_id => $target_stats) {
			$this->assertArrayHasKey('note_last_post', $target_stats);
			$this->assertArrayHasKey('note_count', $target_stats);
			$this->assertNotNull($target_stats['note_count']);
		}
	}

/**
 * testFindUsersWithPosts
 * 
 * @return void
 */
	public function testFindUsersWithPosts() {
		$result = $this->Collection->findUsersWithPosts(1);
		
		$this->assertTrue(is_array($result), 'users with posts should be an array');
		$this->assertEquals(array('total_notes', 'max_post_date', 'users'), array_keys($result), 'data should contain summary data and user records');
		
		$this->assertTrue(is_array($result['users']), 'should be an array of users with posts');
		$this->assertTrue(count($result) > 0, 'should have users with posts');
		
		$expected_data_keys = array('id', 'name', 'num_notes', 'last_post_date');
		foreach($result['users'] as $row) {
			$this->assertNotNull($row['id'], 'user id should be non-null');
			$this->assertTrue(!empty($row['name']), 'user name should not be the empty string');
			$this->assertEquals($expected_data_keys, array_keys($row), 'each record should contain user summary data');
		}
	}
	
/**
 * testFindPostsByCollection
 * 
 * @return void
 */
	public function testFindPostsByCollection() {
		$tests = array(
			array('collection_id' => 1, 'user_id' => null),
			array('collection_id' => 1, 'user_id' => 1),
			array('collection_id' => 1, 'user_id' => 2)
		);

		foreach($tests as $test) {
			$result = $this->Collection->findPostsByCollection($test['collection_id'], $test['user_id']);
			foreach($result as $row) {
				$this->assertEquals(array('Target', 'Note', 'User', 'Collection'), array_keys($row), 'should contain data associated with each note');
				$this->assertNotNull($row['Note']['id'], 'Note ID should be non-null');
				$this->assertNotNull($row['Target']['id'], 'Target ID should be non-null');
				$this->assertNotNull($row['Collection']['id'], 'Collection ID should be non-null');
				
				if(isset($test['user_id'])) {
					$this->assertEquals($test['user_id'], $row['User']['id'], 'user id should match');
				}
			}
		}
	}

/**
 * testGetCollectionsIndexedById
 * 
 * @return void
 */

 	public function testGetCollectionsIndexedById() {
 		$result = $this->Collection->find('all', array('fields' => 'Collection.id'));
		$collection_ids = Set::extract($result, '{n}.Collection.id');
		
 		$result = $this->Collection->getCollectionsIndexedById();
		$this->assertEquals($collection_ids, array_keys($result));
		
		foreach($result as $id => $data) {
			$this->assertTrue(isset($data['id']), 'should contain collection id');
			$this->assertTrue(isset($data['display_name']), 'should contain display as well');
		}
 	}
 	
/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Collection);

		parent::tearDown();
	}

}
