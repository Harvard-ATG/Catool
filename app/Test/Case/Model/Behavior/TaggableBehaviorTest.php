<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('TaggableBehavior', 'Model/Behavior');
App::uses('Model', 'Model');

/**
 * TaggableBehavior Test Case
 *
 * @package app.Test.Model.Behavior
 */
class TaggableBehaviorTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.tag_collection', 'app.tag', 'app.tag_collection_tag', 'app.note');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Note = new Model(array('table' => 'notes', 'name' => 'Note', 'ds' => 'default'));
		$this->Note->create();
		
		$this->TagCollection = ClassRegistry::init('TagCollection');
		$this->TagCollection->create();
	}

/**
 * testLoadBehavior
 *
 * @return void
 */
	public function testLoadBehavior() {
		$this->assertTrue($this->Note->Behaviors->load('Taggable'), 'loaded taggable behavior');
	}

/**
 * testValidateTags
 *
 * @return void
 */
	public function testValidateTags() {
		$this->Note->Behaviors->load('Taggable');

		$max_tags = $this->Note->Behaviors->Taggable->defaultSettings['maxTags'];
		$max_len = Tag::NAME_MAX_LENGTH;
		$exceeds_tag_length = str_pad('', $max_len + 1, 'x');
		$exceeds_max_tags = implode(',', range(1, $max_tags + 1));

		$tests = array(
			array('data' => '', 'expected' => true, 'msg' => 'no tags to validate'),
			array('data' => 'one,two,three,four,five', 'expected' => true, 'msg' => 'max number of allowed tags'),
			array('data' => $exceeds_tag_length, 'expected' => false, 'msg' => "exceeds max tag length: $max_len"),
			array('data' => $exceeds_max_tags, 'expected' => false, 'msg' => "exceeds maximum number of tags: $max_tags")
		);

		foreach($tests as $test) {
			$expected = $test['expected'];
			$data = $test['data'];
			$msg = $test['msg'];

			$this->Note->create();
			$this->Note->set('tags', $data);

			$validates = $this->Note->Behaviors->Taggable->beforeValidate($this->Note);
			if($expected === true) {
				$this->assertTrue($validates, $msg);
			} else {
				$this->assertFalse($validates, $msg);
			}
		}
	}

/**
 * testSaveTags
 * 
 * @return void
 */
	public function testSaveTags() {
		$this->Note->Behaviors->load('Taggable');
		
		$tags = 'a,b,c,d';
		$this->Note->create(array('tags' => $tags));
		$result = $this->Note->save();
		
		$this->assertTrue(!empty($result), 'saved note with tags');
		$this->assertTrue(!empty($result['Note'][TaggableBehavior::TAG_FOREIGN_KEY]), 'tag collection foreign key exists');
		$this->assertTrue($this->TagCollection->existsTagCollection($tags), "tag collection created for tags: $tags");
		
		$expected_id = $this->TagCollection->findTagCollectionIdByTags($tags);
		$actual_id = $result['Note'][TaggableBehavior::TAG_FOREIGN_KEY];
		$this->assertEquals($expected_id, $actual_id, 'tag collection and foreign key matches');
		
		$tag_collection = $this->TagCollection->loadTagCollection($actual_id);
		$this->assertEquals(1, $tag_collection['TagCollection']['instance_count'], 'instance count');
		$this->assertEquals(4, $tag_collection['TagCollection']['tag_count'], 'tag count');		
		$this->assertTrue(!empty($tag_collection['TagCollection']['hash']), 'collection hash is non-empty');
}

/**
 * testDeleteTags
 * 
 * @return void
 */
 	public function testDeleteTags() {
 		$this->Note->Behaviors->load('Taggable');
		$this->Note->create(array('tags' => 'a,b,c,d'));
		$result = $this->Note->save();

		$this->TagCollection->id = $result['Note'][TaggableBehavior::TAG_FOREIGN_KEY];
		$old_instance_count = $this->TagCollection->field('instance_count');
		$this->Note->delete();
		$new_instance_count = $this->TagCollection->field('instance_count');
		
		$this->assertTrue($new_instance_count >= 0, 'instance count should always be a positive integer');
		$this->assertEquals($old_instance_count - 1, $new_instance_count, 'instance count should be decremented by one after delete');
	}
	
/**
 * testFindTags
 * 
 * @return void
 */
	public function testFindTags() {
		$this->Note->Behaviors->load('Taggable');

		$expected_tags = array('a','b','c');
		$this->Note->create();
		$this->Note->set('tags', implode(',', $expected_tags));
		$saved = $this->Note->save();
		$this->assertTrue($saved !== false, 'saved note');

		$result = $this->Note->find('first', array(
			'conditions' => array(
				'Note.id' => $this->Note->id
			)
		));

		$this->assertTrue(isset($result['Tag']), 'note has an associated Tag model');
		$this->assertCount(count($expected_tags), $result['Tag'], 'note has the expected number of Tag models');
		
		$actual_tags = array();
		foreach($result['Tag'] as $tag) {
			$actual_tags[] = $tag['name'];
		}
		
		$this->assertEquals($expected_tags, $actual_tags, 'the note tags match');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Note);
		parent::tearDown();
	}

}
