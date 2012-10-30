<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Tag', 'Model');

/**
 * Tag Test Case
 *
 * @package app.Test.Model
 */
class TagTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.tag');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Tag = ClassRegistry::init('Tag');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Tag);

		parent::tearDown();
	}

/**
 * testSaveTagName
 * 
 * @return void
 */
	public function testSaveTagName() {
		$data = array('Tag' => array(
			'name' => 'super awesome tag!@#$%^&*()_+'
		));
		
		$result = $this->Tag->save($data);
		$this->assertTrue((bool)$result);
		
		$actual = $this->Tag->find('first', array(
			'recursive' => -1,
			'conditions' => array('name' => $data['Tag']['name'])
		));
		
		$actual_name = $actual['Tag']['name'];
		$expected_name = $data['Tag']['name'];

		$this->assertEquals($expected_name, $actual_name, 'tag name saved');
	}
	
/**
 * testSaveTagNameLength
 *
 * @return void
 */
 	public function testSaveTagNameLength() {
 		$max_length = Tag::NAME_MAX_LENGTH;
 		$tag_name = 'tag';
 		$pad_str = 'x';
 		$validate = true;

 		$name_just_right = str_pad($tag_name, $max_length, $pad_str);
 		$data = array('Tag' => array('name' => $name_just_right));
 		$result = $this->Tag->save($data, $validate);
 		$this->assertTrue((bool)$result, 'name is valid because it does not exceed the max length rule');

 
 		$name_too_long = str_pad($tag_name, $max_length + 1, $pad_str); 
 		$data = array('Tag' => array('name' => $name_too_long));
 		$result = $this->Tag->save($data, $validate);
 		$this->assertFalse($result, 'name is invalid because it exceeds the max length rule');
 	}
}