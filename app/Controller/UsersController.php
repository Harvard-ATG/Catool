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
class UsersController extends AppController {

/**
 * components added by cake
 * @var array
 */
    public $components = array('Acl');
	
/**
 * logout method
 *
 * @return void
 */
    public function logout() {
        $this->redirect($this->Auth->logout());
    }

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
		$request = $this->request;
		$action = $request->params['action'];

		// require "super" role to proxy
		if($action === 'proxy') {
			$user_aro = array('model' => 'User', 'foreign_key' => $this->_getRealUserId());
			return $this->Acl->check($user_aro, 'role/super');
		}

		return true;
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->User->id = $id;
		if (!$this->User->exists()) {
			throw new NotFoundException(__('Invalid user'));
		}
		$this->set('user', $this->User->read(null, $id));
	}

/**
 * proxy method
 *
 * @return void
 */
	public function proxy() {

		if($this->request->is('post')) {
			$real_user_id = $this->_getRealUserId(); 
			$proxy_user_id = trim($this->request->data['User']['id']);
			$redirect_to = array('controller' => 'collections');

			if(empty($proxy_user_id) || $proxy_user_id === $real_user_id) {
				$user = $this->User->find('first', array('conditions' => array('User.id' => $real_user_id)));
				$this->Auth->login($user['User']);
				$this->Session->setFlash(__('You are now logged in as yourself'));
				$this->redirect($redirect_to);
			} else {
				$user = $this->User->find('first', array('conditions' => array('User.id' => $proxy_user_id)));
				if($user) {
					$user['User']['real_user_id'] = $real_user_id;
					$this->Auth->login($user['User']);
					$this->Session->setFlash(__('You are now logged in as user %s', $proxy_user_id));
					$this->redirect($redirect_to);
				} else {
					$this->Session->setFlash(__('User %s does not exist', $proxy_user_id));
				}
			}
		}
	}

/**
 * _getRealUserId method
 *
 * @return id
 */
	protected function _getRealUserId() {
		$id = $this->Auth->user('real_user_id');
		if(!isset($id)) {
			$id = $this->_getUserId();
		}

		return $id;
	}
}
