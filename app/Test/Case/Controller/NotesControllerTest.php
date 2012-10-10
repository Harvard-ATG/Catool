<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('NotesController', 'Controller');
App::uses('Set', 'Utility');

/**
 * NotesController Test Case
 * 
 * Console/cake test app Controller/NotesController
 * Console/cake install app --connection=test
 * @package app.Test.Controller
 */
class NotesControllerTestCase extends ControllerTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.collection', 'app.note', 'app.user', 'app.user_collection', 'app.target', 'app.target_setting', 'app.segment', 'app.resource');

/**
 * setUp method
 * 
 * @return void
 */
	public function setUp() {
		
	}
	
/**
 * generateControllerToSkipAuth method
 * 
 * @return void
 */
	public function generateControllerToSkipAuth() {
		$this->generate('Notes', array(
			'methods' => array('isAuthorized')
		));

		$this->controller->expects($this->any())
			->method('isAuthorized')
			->will($this->returnValue(TRUE));

		$this->assertTrue($this->controller->isAuthorized(), 'isAuthorized() should always return true in order to skip the authorization check');
	}
	
/**
 * testView method
 *
 * @return void
 */
	public function testView() {
		$this->generateControllerToSkipAuth();
		
		$result = $this->testAction('/notes/view/1', array('method' => 'get', 'return' => 'vars'));

		$this->assertGreaterThan(0, count($result['results']), 'result should contain one or more entries');
		$this->assertEquals($result['results']['Note']['id'], 1, 'note id should match');
	}

/**
 * testAddAnnotation method
 *
 * @return void
 */
	public function testAddAnnotation() {
		$this->generateControllerToSkipAuth();
		
		$data = array(
			'Note' => array(
				'target_id'=> 1,
				'title' =>'Test Title',
				'body' =>'Test Body',
				'type' =>'annotation'
			),
			'Segment' => array(
				'start_time' => '1',
				'end_time' => '2'
			)
		);
		
		$result = $this->testAction('/notes/add', array(
			'data' => $data,
			'method' => 'post',
			'return' => 'vars'
		));
		
		$this->assertTrue(is_array($result), 'result should be an array');
		$this->assertTrue(isset($result['results']), 'should return a results var');
		$this->assertTrue(isset($result['results']['Note']), 'should return Note data');
		$this->assertTrue(isset($result['results']['Segment']), 'should return Segment data');
		$this->assertEquals($data['Note']['type'], $result['results']['Note']['type'], 'note type should be annotation');
	}
	
/**
 * testAddAnnotation method
 *
 * @return void
 */
	public function testAddComment() {
		$this->generateControllerToSkipAuth();
		
		$data = array(
			'Note' => array(
				'target_id'=> 1,
				'title' =>'Test Comment Title',
				'body' =>'Test Comment Body',
				'type' =>'comment'
			)
		);
		
		$result = $this->testAction('/notes/add', array(
			'data' => $data,
			'method' => 'post',
			'return' => 'vars'
		));
		
		$this->assertTrue(is_array($result), 'result should be an array');
		$this->assertTrue(isset($result['results']), 'should return a results var');
		$this->assertTrue(isset($result['results']['Note']), 'should return Note data');
		$this->assertEquals($data['Note']['type'], $result['results']['Note']['type'], 'note type should be comment');
	}
	
/**
 * testUnauthorizedViewAccess method
 * 
 * @return void
 */
 	public function testUnauthorizedAccess() {
		$this->generate('Notes', array(
			'components' => array('Auth' => array('user')) // we mock the Auth Component here
		));
		
		$this->controller->Auth
			->expects($this->any())
			->method('user') 
    		->with('id') // will be called with first param 'id'
    		->will($this->returnValue(123)); // return a non-existent/invalid user id

    	// TODO: why is the mocked method returning NULL?
		//$this->assertEquals(123, $this->controller->Auth->user('id'), 'mocked user');

		// TODO: These exception tests are failing. See above issue with mock objects.
		// Skipping for now until these can be fixed.
		$this->markTestSkipped();
		
		$exception = 'ForbiddenException';
 		$this->setExpectedException($exception);
		$this->testAction('/notes/view/1', array('method' => 'get'));
		 
		$this->setExpectedException($exception);
		$this->testAction('/notes/index?target_id=1', array('method' => 'get'));

		$this->setExpectedException($exception);
		$this->testAction('/notes/add', array('method' => 'post', 'data' => array()));

		$this->setExpectedException($exception);
		$this->testAction('/notes/edit/1', array('method' => 'post', 'data' => array()));
		 
		$this->setExpectedException($exception);
		$this->testAction('/notes/delete/1', array('method' => 'post'));
 	}

}
