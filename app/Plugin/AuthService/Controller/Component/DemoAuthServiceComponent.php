<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('Component', 'Controller');

class DemoAuthServiceComponent extends Component {

	/**
	 * Required components
	 * 
	 * @var array
	 */
	public $components = array('Session', 'Auth');
	
	/**
	 * Reference to a controller
	 * 
	 * @var Controller
	 */
	public $Controller;
	
	/**
	 * Called before the Controller::beforeFilter().
	 * 
	 * @param $controller 
	 * @return void
	 */
	public function initialize($controller) {
		parent::initialize($controller);
		$this->Controller =& $controller;
		$this->Controller->loadModel('User');
	}

	/**
	 * Login 
	 * 
	 * @return void
	 */
	public function login() {
		if($this->Controller->request->is('post')) {
			$user_id = $this->Controller->request->data['User']['id'];
			$result = $this->Controller->User->find('first', array(
				'conditions' => array('User.id' => $user_id),
				'recursive' => -1
			));
			if($result) {
				$this->Auth->login($result['User']);
				$this->Controller->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('User does not exist'));
			}
		}
	}
}
