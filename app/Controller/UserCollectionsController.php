<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppController', 'Controller');
/**
 * Users Controller
 *
 * @property User $User
 */
class UserCollectionsController extends AppController {

/**
 * components added by cake
 * @var array
 */
    public $components = array('Acl');
	
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

		switch($action) {
			case 'admin_add_user':
			case 'admin_edit_user':
			case 'admin_delete_user':
				$collection_id = $request->params['pass'][0];
				$aro = $this->UserCollection->findFromCollection($user_id, $collection_id);
				$aco = 'role/super/admin';
				return $this->_isAdmin() || $this->Acl->check($aro, $aco);
				break;
			default:
		}

		return false; // deny by default
	}

/**
 * edit_collection method
 *
 * @param string $collection_id
 * @return redirect header
 */
	public function admin_edit_user($collection_id = null) {
		$this->UserCollection->Collection->id = $collection_id;
		if (!$this->UserCollection->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}

		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$data = $this->request->data;
		$data['UserCollection']['collection_id'] = $collection_id;

		if ($this->UserCollection->save($data)) {
			$this->Session->setFlash(__('Collection user permissions saved'), 'flash_success');
		} else {
			$this->Session->setFlash(__('Collection user permissions NOT saved'), 'flash_failure');
		}

		$url = array(
			'controller' => 'collections',
			'action' => 'edit_permissions',
			'admin' => true,
			$collection_id
		);

		return $this->redirect($url);
	}

/**
 * add_user method
 *
 * @param string $collection_id
 * @return redirect header
 */
	public function admin_add_user($collection_id = null) {
		if (!$this->UserCollection->Collection->exists($collection_id)) {
			throw new NotFoundException(__('Invalid collection'));
		}

		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$user_id = $email = '';
		if(isset($this->request->data['User'])) {
			if(isset($this->request->data['User']['id'])) {
				$user_id = trim($this->request->data['User']['id']);
			}
			if(isset($this->request->data['User']['email'])) {
				$email = trim($this->request->data['User']['email']);
			}
		}

		if(!empty($user_id)) {
			if($this->UserCollection->User->exists($user_id)) {
				$data = $this->request->data;
				$data['UserCollection']['collection_id'] = $collection_id;
				if ($this->UserCollection->save($data)) {
					$this->Session->setFlash(__('User added to collection'), 'flash_success');
				} else {
					$this->Session->setFlash(__('User NOT added to collection'), 'flash_failure');
				}
			} else {
				$this->Session->setFlash(__("No such user exists with ID: $user_id"), 'flash_failure');
			}
		} else if(!empty($email)) {
			$found = $this->UserCollection->findByEmailAndCollection($email, $collection_id);

			if(empty($found)) {
				$this->Session->setFlash(__("No such user exists with email: $email"), 'flash_notice');
			} else if(count($found) === 1) {
				$data = $found[0]['UserCollection'];
				$data = array_merge($data, array('collection_id' => $collection_id, 'user_id' => $found[0]['User']['id']));
				if ($this->UserCollection->save($data)) {
					$this->Session->setFlash(__('User added to collection'), 'flash_success');
				} else {
					$this->Session->setFlash(__('User NOT added to collection'), 'flash_failure');
				}
			} else {
				// Note: we assume that this branch can't happen because 
				// the search doesn't allow partial matches and the email
				// field by definition must be unique. 
				//
				// If we decide to allow wildcards or partial matches,
				// then we need to create a selection interface.
			}
		} else {
			$this->Session->setFlash(__("No users added to collection"), 'flash_notice');
		}

		$url = array(
			'controller' => 'collections',
			'action' => 'edit_permissions', 
			'admin' => true,
			$collection_id
		);

		return $this->redirect($url);
	}

/**
 * Admin action to remove a user from a collection
 *
 * @param string $collection_id
 * @return void
 */
 	public function admin_delete_user($collection_id = null) {
		$this->UserCollection->id = $this->request->data['UserCollection']['id'];
		if (!$this->UserCollection->exists()) {
			throw new NotFoundException(__('Invalid user collection id'));
		}
		if(!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}

		$data = $this->UserCollection->read();
		$name = $data['User']['name'];
		$email = $data['User']['email'];

		if($this->UserCollection->delete()) {
			$this->Session->setFlash(__("User $name <$email> removed from collection"), 'flash_success');
		} else {
			$this->Session->setFlash(__("User $name <$email> NOT removed from collection"), 'flash_failure');
		}

		$url = array(
			'controller' => 'collections', 
			'action' => 'edit_permissions', 
			'admin' => true, 
			$collection_id
		);

		return $this->redirect($url);
	}

}
