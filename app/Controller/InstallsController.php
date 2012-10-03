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
 * beforeFilter method
 *
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->Auth->allow('*');
	}

/**
 * promote method
 *
 * Promotes the currently logged-in user to super user, or logs in
 * as the default super user (id = 1).
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

		$role = 'role/super';
		$user = array('model' => 'User', 'foreign_key' => $user_id);

		if(!$this->Acl->check($user, $role)) {
			$this->Acl->allow($user, $role);
		}

		$this->Session->setFlash(__('You have been promoted to super user.'), 'flash_success');
		$this->redirect(array('controller' => 'collections', 'action' => 'index', 'admin' => true));
	}
}
