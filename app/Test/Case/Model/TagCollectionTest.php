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
	public $fixtures = array('app.tag_collection', 'app.tag', 'app.tag_collection_tag');

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
 * testLoadTagCollection method
 *
 * @return void
 */
	public function testLoadTagCollection() {
		$expected = array('TagCollection' => array('id' => '1'));
		$actual = $this->TagCollection->loadTagCollection(1);
		
		$this->assertTrue(!empty($actual), 'loading a tag collection returned results');
		$this->assertEquals($expected['TagCollection']['id'], $actual['TagCollection']['id'], 'loaded complete tag collection');
		$this->assertEquals(3, count($actual['TagCollectionTag']), 'three tags loaded for collection');
	}

/**
 * testExistsTagCollection method
 *
 * @return void
 */
	 public function testExistsTagCollection() {
	 	 $tests = array(
	 	 	 array(
	 	 	 	 'data' => array('foo', 'foo+bar', 'moe larry and curly'), 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => array('moe larry and curly', 'foo', 'foo+bar'), 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => array('foo+bar', 'moe larry and curly', 'foo'), 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => array('foo+bar', 'foo'), 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => 'foo+bar, moe larry and curly, foo', 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => 'foo+bar, moe larry and curly, FOO', 
	 	 	 	 'expected' => false),
	 	 	 array(
	 	 	 	 'data' => 'foo', 
	 	 	 	 'expected' => false),
	 	 	 array(
	 	 	 	 'data' => 'foo+bar', 
	 	 	 	 'expected' => false),
	 	 	 array(
	 	 	 	 'data' => 'moe larry and curly', 
	 	 	 	 'expected' => false),
	 	 	 array(
	 	 	 	 'data' => array('!@#$%^&*()_+-=[]{};:\'".?\/~', '<my local="var">you betcha</my>'), 
	 	 	 	 'expected' => true),
	 	 	 array(
	 	 	 	 'data' => array('<my local="var">you betcha</my>', '!@#$%^&*()_+-=[]{};:\'".?\/~'), 
	 	 	 	 'expected' => true)
	 	 );
	 	 
	 	 foreach($tests as $test) {
	 	 	 $data = $test['data'];
	 	 	 $data_dump = var_export($data,1);
	 	 	 $expected = $test['expected'];
	 	 	 $actual = $this->TagCollection->existsTagCollection($data);
	 	 	 if($expected === true) {
	 	 	 	 $this->assertTrue($actual, "tag collection EXISTS for data: $data_dump");
	 	 	 } else if($expected === false) {
	 	 	 	 $this->assertFalse($actual, "tag collection DOES NOT EXIST for data: $data_dump");
	 	 	 } else {
	 	 	 	 $this->fail('invalid "expected" value for test (must be a boolean):'.var_export($expected,1));
	 	 	 }
	 	 }
	 }

/**
 * testFindTagCollectionIdByTags
 *
 * @return void
 */
	public function testFindTagCollectionIdByTags() {
		$tests = array(
			array(
				'data' => 'foo+bar, moe larry and curly, foo', 
				'expected' => 1),
			array(
				'data' => 'foo, foo+bar', 
				'expected' => 2),
			array(
				'data' => array('!@#$%^&*()_+-=[]{};:\'".?\/~', '<my local="var">you betcha</my>'), 
				'expected' => 3),
			array(
				'data' => '<my local="var">you betcha</my>', 
				'expected' => 4)
		);
		
		foreach($tests as $test) {
			$data = $test['data'];
			$expected = $test['expected'];
			$actual = $this->TagCollection->findTagCollectionIdByTags($data);
			$this->assertEquals($expected, $actual, 'tag collection id matched');
		}
	}

/**
 * testCreateTagCollection
 *
 * @return void
 */
	public function testCreateTagCollection() {
		// stub
	}

/**
 * testSaveTags
 *
 * @return void
 */
	public function testSaveTags() {
		$tag_collection_id = $this->TagCollection->saveTags(array('foo+bar', 'moe larry and curly', 'foo'));
		$this->assertEquals(1, $tag_collection_id, 'saved tags that already have an existing collection');
		
		$all_tag_collection_ids = $this->TagCollection->find('list', array(
			'field' => 'TagCollection.id',
			'recursive' => -1
		));
		$tag_collection_id = $this->TagCollection->saveTags(array('TAG_DOES_NOT_EXIST', 'ANOTHER_TAG_DNE'));
		
		$this->assertTrue(!in_array($tag_collection_id, $all_tag_collection_ids), 'new tag collection created');
	}

/**
 * testUniqueTags
 *
 * @return void
 */
	public function testUniqueTags() {
 		$tests = array(
 			 array('data' => array(), 'expected' => array()),
 			array('data' => array('a'), 'expected' => array('a')),
 			array('data' => array('a','a'), 'expected' => array('a')),
  			array('data' => array('a','a','b'), 'expected' => array('a','b')),
 			array('data' => array('a','a','b','b'), 'expected' => array('a','b')),
 			array('data' => array('b','a','b','a'), 'expected' => array('b','a')),
  			array('data' => array('a','b','a','b'), 'expected' => array('a','b')),
  			array('data' => array('b','b','b','a'), 'expected' => array('b', 'a'))
 		);

 		foreach($tests as $test) {
 			$actual = $this->TagCollection->uniqueTags($test['data']);
 			$expected = $test['expected'];
 			$this->assertEquals($expected, $actual, 'unique tags');
 		}
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
