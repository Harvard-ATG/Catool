<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('VideosController', 'Controller');

/**
 * VideosController Test Case
 *
 * @package app.Test.Controller
 */
class VideosControllerTestCase extends ControllerTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.target', 'app.target_setting', 'app.collection', 'app.resource', 'app.note', 'app.user', 'app.user_collection', 'app.role', 'app.segment');

/**
 * setUp method
 * 
 * @return void
 */
	public function setUp() {
		$controller = $this->generate('Videos', array(
			'methods' => array('isAuthorized', '_isAdmin', '_getUserId')
		));

		$controller->expects($this->any())
			->method('isAuthorized')
			->will($this->returnValue(TRUE));
		
		$controller->expects($this->any())
			->method('_isAdmin')
			->will($this->returnValue(FALSE));

		$controller->expects($this->any())
			->method('_getUserId')
			->will($this->returnValue(1));
		
		$this->assertTrue($controller->isAuthorized(), 'isAuthorized should always return true (bypass auth check)');
		$this->assertFalse($controller->_isAdmin(), 'isAdmin should always return false for tests');
			
		$this->controller = $controller;
	}
	
/**
 * testViewExists method
 * 
 * @return void
 */
	public function testViewExists() {
		$result = $this->testAction('/videos/view/1', array('return' => 'vars'));
		
		$this->assertTrue(isset($result['target']), 'has target vars');
		$this->assertTrue(isset($result['notes']), 'has notes vars');
		
		$this->assertEquals(1, count($result['target']['Video']['id']), 'has video');
		$this->assertGreaterThan(0, count($result['notes']), 'has notes');
	}

/**
 * testViewNotExists method
 * 
 * @return void
 */
	public function testViewNotExists() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/videos/view/BAD_ID');
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
		
		$result = $this->controller->Video->Collection->find('first', array(
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
		$this->testAction('/admin/videos/edit/1', array(
			'method' => 'get'
		));
		
		$data = $this->controller->request->data;
		$this->assertEquals($data['Video']['id'], 1, 'id matches');
		$this->assertTrue(!empty($data['Video']['display_name']));
	}

/**
 * testAdminEditPost method
 *
 * @return void
 */
	public function testAdminEditPost() {
		$id = 1;
		$data = array(
			'Video' => array(
				'id' => $id,
				'target_setting_id' => 1,
				'display_name' => 'Foo Bar!',
				'display_description' => 'The terms foobar /ˈfʊːbɑːr/, fubar, or foo, bar, baz and qux (alternatively quux) are sometimes used as placeholder names (also referred to as metasyntactic variables) in computer programming or computer-related documentation.',
				'display_creator' => 'Fooer of the Bar',
				'hidden' => 0
			),
			'TargetSetting' => array(
				'id' => 1,
				'lock_annotations' => 1,
				'lock_comments' => 1,
				'sync_annotations' => 1
			),
			'Resource' => array(
				'duration' => 123,
				'url' => 'http://this.localhost/file.mp4'
			)
		);
		
		$this->testAction("/admin/videos/edit/$id", array(
			'data' => $data,
			'method' => 'post'
		));
		
		$result = $this->controller->Video->find('first', array(
			'recursive' => 0,
			'fields' => array_keys(Set::flatten($data)),
			'conditions' => array('Video.id' => $id)
		));
		
		$this->assertEqual($result['Video'], $data['Video'], 'video updated');
		$this->assertEqual($result['TargetSetting'], $data['TargetSetting'], 'video settings updated');
	}

/**
 * testAdminEditInvalid method
 *
 * @return void
 */	
	public function testAdminEditInvalid() {
		$this->setExpectedException('NotFoundException');
		$this->testAction('/admin/videos/edit/COLLECTION_DOES_NOT_EXIST', array(
			'method' => 'post'
		));
	}

/**
 * testAdminDelete method
 *
 * @return void
 */
	public function testAdminDelete() {
		$this->testAction('/admin/videos/delete/1?collection_id=1', array('method' => 'post'));
		$this->controller->Video->id = 1;
		$this->assertFalse($this->controller->Video->exists());
	}

/**
 * testAdminDeleteRequiresPost method
 * 
 * @return void
 */
	public function testAdminDeleteRequiresPost() {
		$this->setExpectedException('MethodNotAllowedException');
		$this->testAction('/admin/videos/delete/1', array(
			'method' => 'get'
		));
	}
}
