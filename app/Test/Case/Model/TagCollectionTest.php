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
 * testParseTags
 *
 * @return void
 */
 	public function testParseTags() {
 		$tests = array(
 			array('data' => '', 'expected' => array()),
 			array('data' => 'a,', 'expected' => array('a')),
 			array('data' => ',a', 'expected' => array('a')),
  			array('data' => 'a,b,c', 'expected' => array('a', 'b', 'c')),
 			array('data' => 'a, ,c', 'expected' => array('a', 'c')),
 			array('data' => 'a b c', 'expected' => array('a b c'))
 		);
 		
 		foreach($tests as $test) {
 			$actual = $this->TagCollection->parseTags($test['data']);
 			$expected = $test['expected'];
 			$this->assertEquals($expected, $actual, 'parsing tags');
 		}
 	}

/**
 * testStringify
 *
 * @return void
 */
 	public function testStringify() {
 		$tests = array(
 			array('data' => array(), 'expected' => ''),
 			array('data' => array(''), 'expected' => ''),
 			array('data' => array('a'), 'expected' => 'a'),
 			array('data' => array('a','b'), 'expected' => 'a,b'),
 			array('data' => array('c','b','a'), 'expected' => 'c,b,a'),
 			array('data' => array('a','b','c'), 'expected' => 'a,b,c')
 		);
 		
 		foreach($tests as $test) {
 			$actual = $this->TagCollection->stringify($test['data']);
 			$expected = $test['expected'];
 			$this->assertEquals($expected, $actual, 'stringify tags');
 		}
 	}

/**
 * testStringifyCanonical
 *
 * @return void
 */
 	public function testStringifyCanonical() {
 		$tests = array(
 			array('data' => array('c','b','a'), 'expected' => 'a,b,c'),
 			array('data' => array('a','b','c'), 'expected' => 'a,b,c')
 		);
 		
 		foreach($tests as $test) {
 			$actual = $this->TagCollection->stringifyCanonical($test['data']);
 			$expected = $test['expected'];
 			$this->assertEquals($expected, $actual, 'stringify tags in canonical order');
 		}
 	}
 	
/**
 * testSortTags
 *
 * @return void
 */
 	public function testSortTags() {
 		$tests = array(
 			array('data' => array(), 'expected' => array()),
 			array('data' => array('a'), 'expected' => array('a')),
 			array('data' => array('c','b','a'), 'expected' => array('a','b','c')),
 			array('data' => array('uno','dos','tres'), 'expected' => array('dos', 'tres','uno'))
 		);
 		
 		foreach($tests as $test) {
 			$actual = $this->TagCollection->sortTags($test['data']);
 			$expected = $test['expected'];
 			$this->assertEquals($expected, $actual, 'sorted tags by locale collation');
 		}
 	}
 
/**
 * testHashOf
 * 
 * @return void
 */
	public function testHashOf() {
		$tags = array('uno', 'dos', 'tres');
		$tag_str = $this->TagCollection->stringifyCanonical($tags);

		$expected = hash(TagCollection::HASH_ALGORITHM, $tag_str, FALSE);
		$actual = $this->TagCollection->hashOf($tags);

		$this->assertEquals($expected, $actual, 'hash values match for list of tags in the collection');
		$this->assertFalse($this->TagCollection->hashOf(array()), 'no tags to hash');
		$this->assertFalse($this->TagCollection->hashOf(''), 'empty tag string to hash');
	}
}
