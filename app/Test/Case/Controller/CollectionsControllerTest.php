<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('CollectionsController', 'Controller');
App::uses('AuthComponent', 'Controller/Component');
App::uses('Set', 'Utility');
App::uses('CakeSession', 'Model/DataSource');

/**
 * CollectionsController Test Case
 * @package app.Test.Controller
 */
class CollectionsControllerTestCase extends ControllerTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.collection', 'app.user_collection', 'app.target', 'app.target_setting', 'app.note', 'app.user', 'app.segment', 'app.role');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Collection = ClassRegistry::init('Collection');

		$controller = $this->generate('Collections', array(
			'methods' => array('_getUserId', '_isAdmin')
		));
		
		$controller->expects($this->any())
			->method('_getUserId')
			->will($this->returnValue(1));
		
		$controller->expects($this->any())
			->method('_isAdmin')
			->will($this->returnValue(false));
			
		$this->controller = $controller;
	}
	
/**
 * testGetUserId method
 * 
 * @return void
 */
	public function testGetUserId() {
			$this->assertEquals(1, $this->controller->_getUserId(), 'auth user id is 1');
	}
	
/**
 * testIndex method
 *
 * @return void
 */
	public function testIndexDisplaysList() {
		$result = $this->testAction('/collections/index', array('return' => 'vars'));	
		$this->assertTrue(count($result['collections']) > 0);
		foreach($result['collections'] as $collection) {
			$this->_assertCollectionViewVars($collection);
		}
	}
	
/**
 * testPostsActionId method
 * 
 * @return void
 */
	public function testPostsActionInvalidId() {
		$tests = array('-1', 'foo', '3.14159', '999999999999999999');
		foreach($tests as $invalid_id) {
			try {
				$this->testAction("/collections/posts/$invalid_id");
			} catch(NotFoundException $e) {
				$this->pass("Invalid collection ID");
				continue;
			}
			$this->fail("Invalid collection ID [$invalid_id] must raise an exception");
		}
	}

/**
 * testView method
 *
 * @return void
 */
	public function testView() {
		$result = $this->testAction('/collections/view/1', array('return' => 'vars'));
		$this->assertTrue(isset($result['collection']['Collection']));
		$this->assertTrue(isset($result['targets']));
		$this->assertTrue(isset($result['note_stats_for']));
		
		$num_targets = count($result['targets']);
		$this->assertGreaterThan(0, $num_targets);

		$this->_assertNoteStatsExist($result['note_stats_for'], $num_targets);
	}
	
/**
 * testViewSearchWithResults method
 *
 * @return void
 */
	public function testViewSearchWithResults() {
		$result = $this->testAction('/collections/view/1?search='.urlencode('Testing'), array('return' => 'vars'));
		$targets = $result['targets'];
		$this->assertTrue(count($targets) > 0, 'search has results');
		$this->assertTrue($this->_targetExists($targets, 2), 'search term found in results');
	}

/**
 * testViewSearchWithoutResults method
 *
 * @return void
 */
	public function testViewSearchWithoutResults() {
		$result = $this->testAction('/collections/view/1?search='.urlencode('<INVALID SEARCH>'), array('return' => 'vars'));
		$targets = $result['targets'];
		
		$this->assertTrue(empty($targets), 'search has no results');
	}
	
/**
 * testAdminIndex method
 *
 * @return void
 */
	public function testAdminIndex() {
		$result = $this->testAction('/admin/collections/index', array('return' => 'vars'));
		$this->assertTrue(count($result['collections']) > 0);
		foreach($result['collections'] as $collection) {
			$this->_assertCollectionViewVars($collection);
		}
	}

/**
 * testAdminAdd method
 *
 * @return void
 */
	public function testAdminAdd() {
		$collection = array(
			'Collection' => array(
				'display_name' => 'Chewie Chewie Chewie!',
				'display_description' => 'Chewbacca, also known as Chewie, is a fictional character in the Star Wars franchise, portrayed by Peter Mayhew.'
		));
		
		$this->testAction('/admin/collections/add', array(
			'data' => $collection,
			'method' => 'post'
		));
		
		$result = $this->controller->Collection->find('first', array(
			'conditions' => Set::flatten($collection)
		));
		
		$this->assertTrue(!empty($result['Collection']['id']));
		foreach(array_keys($collection['Collection']) as $key) {
			$this->assertEqual($result['Collection'][$key], $collection['Collection'][$key]);
		}
	}


/**
 * testAdminEditGet method
 * 
 * @return void
 */
	public function testAdminEditGet() {
		$this->testAction('/admin/collections/edit/1', array(
			'method' => 'get'
		));
		
		$data = $this->controller->request->data;
		$this->assertEquals($data['Collection']['id'], 1, 'collection id matches');
		$this->assertTrue(!empty($data['Collection']['display_name']));
	}

/**
 * testAdminEditVars method
 *
 * @return void
 */
	public function testAdminEditVars() {
		$result = $this->testAction('/admin/collections/edit/1', array('method' => 'get', 'return' => 'vars'));

		$this->assertTrue(isset($result['collection']['Collection']), 'has collection');
		$this->assertEquals(1, $result['collection_id'], 'collection id matches');
	}
	
/**
 * testAdminEditItems method
 *
 * @return void
 */
 	public function testAdminEditItems() {
 		$result = $this->testAction('/admin/collections/edit_items/1', array('method' => 'get', 'return' => 'vars'));

		$this->assertTrue(isset($result['collection']['Collection']), 'has collection');
		$this->assertEquals(1, $result['collection_id'], 'collection id matches');		
 		$this->assertTrue(isset($result['targets']), 'has targets');
		$this->assertTrue(isset($result['note_stats_for']), 'has note stats');

		$num_targets = count($result['targets']);
		$this->_assertNoteStatsExist($result['note_stats_for'], $num_targets);
 	}

/**
 * testAdminEditPost method
 *
 * @return void
 */
	public function testAdminEditPost() {
		$collection = array(
			'Collection' => array(
				'id' => 2,
				'display_name' => 'Foo Bar!',
				'display_description' => 'The terms foobar /ˈfʊːbɑːr/, fubar, or foo, bar, baz and qux (alternatively quux) are sometimes used as placeholder names (also referred to as metasyntactic variables) in computer programming or computer-related documentation.'
			)
		);
		
		$this->testAction('/admin/collections/edit/2', array(
			'data' => $collection,
			'method' => 'post'
		));
		
		$result = $this->controller->Collection->find('first', array(
			'fields' => array_keys(Set::flatten($collection)),
			'conditions' => array('Collection.id' => 2)
		));
		
		$this->assertEqual($result['Collection'], $collection['Collection'], 'collection information updated');
	}

/**
 * testAdminEditInvalid method
 *
 * @return void
 */	
	public function testAdminEditInvalid() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/admin/collections/edit/COLLECTION_DOES_NOT_EXIST', array(
			'method' => 'post'
		));
	}
	
/**
 * testAdminDelete method
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->testAction('/admin/collections/delete/1', array('method' => 'post'));
		$this->controller->Collection->id = 1;
		$this->assertFalse($this->controller->Collection->exists());
	}

/**
 * testAdminDeleteRequiresPost method
 * 
 * @return void
 */
	public function testAdminDeleteRequiresPost() {
		$this->setExpectedException('MethodNotAllowedException');
		$this->testAction('/admin/collections/delete/1', array(
			'method' => 'get'
		));
	}

/**
 * Helper method to assert that required collection view vars are present
 * @param array $collection
 */
	public function _assertCollectionViewVars($collection) {
		$this->assertTrue(isset($collection['Collection']));
		$this->assertTrue(!empty($collection['Collection']['id']));
		$this->assertTrue(!empty($collection['Collection']['display_name']));
	}

/**
 * Helper method to assert that a collection contains a specific target.
 * @param array $items
 * @param integer $id
 */
	public function _targetExists($items, $id) {
		foreach($items as $item) {
			if($item['Target']['id'] == $id) {
				return true;
				break;
			}
		}
		return false;
	}

/**
 * Helper method to assert that a target note stats exist for each target
 * @param array $note_stats_for
 * @param integer $num_targets
 */
	public function _assertNoteStatsExist($note_stats_for, $num_targets) {
		$num_target_stats = count(array_keys($note_stats_for));
		$this->assertEquals($num_targets, $num_target_stats);

		foreach($note_stats_for as $target_id => $target_stats) {
			$this->assertArrayHasKey('note_last_post', $target_stats);
			$this->assertArrayHasKey('note_count', $target_stats);
			$this->assertNotNull($target_stats['note_count']);
		}
	}

}
