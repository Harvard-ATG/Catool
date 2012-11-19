<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('PurifiableBehavior', 'Model/Behavior');
App::uses('Model', 'Model');

/**
 * PurifiableBehavior Test Case
 *
 * @package app.Test.Model.Behavior
 */
class PurifiableBehaviorTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.note');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Note = new Model(array('table' => 'notes', 'name' => 'Note', 'ds' => 'default'));
		$this->Note->create();
	}

/**
 * testLoadBehavior
 *
 * @return void
 */
	public function testLoadBehavior() {
		$this->assertTrue($this->loadPurifiable(), 'loaded purifiable behavior');
	}

/**
 * testPurifyFieldHTML
 * 
 * @return void
 */
	public function testPurifyFieldHTML() {
		$tests = array(
			array(
				'field' => 'foo',
				'data' => '<img src="javascript:evil();" onload="evil();" />',
				'settings' => array('overwrite' => true, 'keepDirty' => false),
				'expected' => array('foo' => ''),
				'msg' => 'Malicious code removed'
			),
			array(
				'field' => 'bar',
				'data' => '<b>Bold',
				'settings' => array('overwrite' => true, 'keepDirty' => false),
				'expected' => array('bar' => '<b>Bold</b>'),
				'msg' => 'Missing end tags fixed'
			),
			array(
				'field' => 'ham',
				'data' => '<b>Inline <del>context <div>No block allowed</div></del></b>',
				'settings' => array('overwrite' => true, 'keepDirty' => false),
				'expected' => array('ham' => '<b>Inline <del>context No block allowed</del></b>'),
				'msg' => 'Illegal nesting fixed'
			),
			array(
				'field' => 'cheese',
				'data' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>',
				'settings' => array('overwrite' => true, 'keepDirty' => false),
				'expected' => array('cheese' => '<span>Text</span>'),
				'msg' => 'CSS validated'
			),
			array(
				'field' => 'cheese1',
				'data' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>',
				'settings' => array('overwrite' => false, 'keepDirty' => false),
				'expected' => array(
					'cheese1_clean' => '<span>Text</span>'
				),
				'msg' => 'CSS validated'			
			),
			array(
				'field' => 'cheese2',
				'data' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>',
				'settings' => array('overwrite' => false, 'keepDirty' => true),
				'expected' => array(
					'cheese2_clean' => '<span>Text</span>',
					'cheese2_dirty' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>'
				),
				'msg' => 'CSS validated'			
			),
			array(
				'field' => 'cheese3',
				'data' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>',
				'settings' => array('overwrite' => true, 'keepDirty' => true),
				'expected' => array(
					'cheese3' => '<span>Text</span>',
					'cheese3_dirty' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>'
				),
				'msg' => 'CSS validated'			
			)
		);
		
		foreach($tests as $test) {
			$actual = PurifiableBehavior::purifyFieldHTML($test['field'], $test['data'], $test['settings']);
			$expected = $test['expected'];
			$this->assertEquals($actual, $expected, $test['msg']);
		}
	}

/**
 * testSave
 * 
 * @return void
 */
	public function testSave() {

		$this->loadPurifiable(array(
			'fields' => array('body', 'body2', 'body3'),
			'overwrite' => true,
			'keepDirty' => true
		));

		$data = array(
			'body' => '<img src="javascript:evil();" onload="evil();" />',
			'body2' => 'allgood',
			'body3' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>'
		);

		$expected_data = array(
			'body' => '',
			'body_dirty' => '<img src="javascript:evil();" onload="evil();" />',
			'body2' => 'allgood',
			'body2_dirty' => 'allgood',
			'body3' => '<span>Text</span>',
			'body3_dirty' => '<span style="color:#COW;float:around;text-decoration:blink;">Text</span>'
		);

		$this->Note->set($data);
		$result = $this->Note->save();
		$this->assertTrue(!empty($result), 'note data saved');

		$actual_data = $result['Note'];
		$fields = array('body', 'body_dirty', 'body2', 'body2_dirty', 'body3', 'body3_dirty');
		foreach($expected_data as $field => $expected_value) {
			$this->assertTrue(isset($actual_data[$field]), "saved data should contain field: $field");
			$this->assertEquals($expected_value, $actual_data[$field], "saved data for field: $field");
		}
	}

/**
 * loadPurifiable
 * 
 * @return boolean
 */
	public function loadPurifiable($config = array()) {
		return $this->Note->Behaviors->load('Purifiable', $config);
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
