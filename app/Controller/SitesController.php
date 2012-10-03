<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppController', 'Controller');
/**
 * Collections Controller
 *
 * @package app.Controller
 * @property Collection $Collection
 */
class SitesController extends AppController {

/**
 * beforeFilter method
 * 
 * Callback that is called before the controller action.
 * 
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->Auth->authorize = array('Controller');
	}

/**
 * isAuthorized method
 * 
 * @return boolean true if authorized, false otherwise
 */
	public function isAuthorized() {
		$user_id = $this->_getUserId();
		$request = $this->request;
		$action = $request->params['action'];

		$aro = array(
			'model' => 'User', 
			'foreign_key' => $user_id
		);

		return $this->Acl->check($aro, 'role/super');
	}

/**
 * index method
 *
 * @return void
 */
	public function admin_index() {
		$this->setAction('admin_users');
	}

/**
 * users method
 *
 * @return void
 */
	public function admin_users() {
		$this->loadModel('User');
		$this->set('user_id', $this->_getUserId());
		$this->set('users', $this->User->findAllUsers());
		$this->set('users_by_id', $this->User->findAllUsersIndexedById());
		$this->set('roles', $this->User->getRoleTypes());
	}

/**
 * Update user information, such as their role, name, email, etc.
 *
 * @return redirect header
 */
	public function admin_edit_user() {
		$this->loadModel('User');
		$this->User->id = $this->request->data['User']['id'];

		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$data = $this->request->data;
		if ($this->User->save($data)) {
			$this->Session->setFlash(__('User updated'), 'flash_success');
		} else {
			$this->Session->setFlash(__('User NOT updated'), 'flash_failure');
		}

		$url = array('controller' => 'sites', 'action' => 'users', 'admin' => true);

		return $this->redirect($url);
	}

/**
 * Removes a user from the application.
 *
 * @return void
 */
 	public function admin_delete_user() {
		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$this->loadModel('User');
		$this->User->id = $this->request->data['User']['id'];
		if($this->User->delete()) {
			$this->Session->setFlash(__("User deleted"), 'flash_success');
		} else {
			$this->Session->setFlash(__("User NOT deleted"), 'flash_failure');
		}

		$url = array('controller' => 'sites', 'action' => 'users', 'admin' => true);

		return $this->redirect($url);
	}
}
