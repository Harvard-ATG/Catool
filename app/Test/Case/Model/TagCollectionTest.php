<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('TagCollection', 'Model');

/**
 * TagCollection Test Case
 *
 * @package app.Test.Model
 */
class TagCollectionTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.tag_collection');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->TagCollection = ClassRegistry::init('TagCollection');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->TagCollection);
		parent::tearDown();
	}

/**
 * testHashOf
 * 
 * @return void
 */
	public function testHashOf() {
		$tag_str = 'one,two,three';
		$expected_hash = hash(TagCollection::HASH_ALGO, $tag_str, FALSE);
		$actual_hash = $this->TagCollection->hashOf($tag_str);
		$this->assertEquals($expected_hash, $actual_hash, 'hash values match for list of tags in the collection');
	}
}
