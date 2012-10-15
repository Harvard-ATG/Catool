<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * Installation Controller 
 *
 * WARNING:
 * Remove me in production!!!
 */

App::uses('AppController', 'Controller');

/**
 * Installation controller.
 */
class InstallsController extends AppController {

/**
 * Controller name
 *
 * @var string
 */
	public $name = 'Installs';

/**
 * This controller does not use a model
 *
 * @var array
 */
	public $uses = array('User');

/**
 * Components
 *
 * @var array
 */
	public $components = array('Auth', 'Acl');

/**
 * beforeFilter method
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}

/**
 * Either promotes the current user to super user or logs them
 * in as a super user.
 *
 * @return void
 */
	public function promote() {
		if($this->Auth->loggedIn()) {
			$user_id = $this->Auth->user('id');
		} else {
			$default = $this->User->findById(1);
			$this->Auth->login($default['User']);
			$user_id = $default['User']['id'];
		}

		$this->User->promoteToSuper(1);
		$this->Session->setFlash(__('You are now logged in with super user permissions.'), 'flash_notice');
		$this->redirect(array('controller' => 'collections', 'action' => 'index', 'admin' => true));
	}
}
