<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('TargetsControllerInterface', 'Controller/Interface');
App::uses('Component', 'Controller/AuthComponent');


/**
 * Videos Controller
 *
 * @package       app.Controller
 */
class VideosController extends AppController implements TargetsControllerInterface {

/**
 * components added by cake
 * @var array
 */
    public $components = array('RequestHandler', 'Acl');

/**
 * helpers added by cake
 * @var array
 */
	public $helpers = array('TargetLink');

/**
 * beforeFilter method
 * 
 * Callback that is called before the controller action.
 * 
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		$this->RequestHandler->startup($this); // pre-emptively call this so that data is parsed before authorization happens
		$this->Auth->authorize = array('Controller');
	}

/**
 * isAuthorized method
 * 
 * @param User $user the user object
 * @param CakeRequest $request the request object
 * @return boolean true if authorized, false otherwise
 */
	public function isAuthorized() {
		$request = $this->request;
		$action = $request->params['action'];
		
		$this->loadModel('UserCollection');
		$user_id = $this->_getUserId();

		// retrieve the user's collection membership based on the note's target
		switch($action) {
			case 'view':
			case 'admin_edit':
			case 'admin_delete':
				$target_id = $request->params['pass'][0];
				$user_collection_aro = $this->UserCollection->findFromTarget($user_id, $target_id);
				break;
			case 'admin_add':
				$collection_id = $request->params['pass'][0];
				$user_collection_aro = $this->UserCollection->findFromCollection($user_id, $collection_id);
				break;
			default: 
				return false;
		}

		// user must have the admin role to add/edit/delete a target
		$role_aco = 'role/super/admin/mod/user';
		if(in_array($action, array('admin_add', 'admin_edit', 'admin_delete'))) {
			$role_aco = 'role/super/admin';
		}

		return $this->_isAdmin() || $this->Acl->check($user_collection_aro, $role_aco);
	}

/**
 * isModerator method
 * 
 * Checks if the user is a moderator or can moderate.
 * 
 * @param $video_id
 * @return true if has admin privileges for the collection, otherwise false
 */	
	public function isModerator($video_id) {
		$this->loadModel('UserCollection');
		
		$this->Video->id = $video_id;
		$collection_id = $this->Video->field('collection_id');
		$user_id = $this->_getUserId();
		
		$user_collection_aro = $this->UserCollection->findFromCollection($user_id, $collection_id);
		$role_aco = 'role/super/admin/mod';
		
		return $this->_isAdmin() || $this->Acl->check($user_collection_aro, $role_aco);
	}
	
/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid target'));
		} 
		
		$target = $this->Video->find('first', array(
			'conditions' => array('Video.id' => $id),
			'recursive' => 1
		));
		$collection = $this->Video->Collection->read(null, $target['Video']['collection_id']);
		
		$notes = $this->Video->Note->findNotesByTarget($id);

		$neighbors = $this->Video->getNeighbors($id);

		$current_user = array(
			'id' => $this->_getUserId(), 
			'isModerator' => $this->isModerator($id)
		);

		$this->set(compact(array('collection', 'target', 'notes', 'neighbors', 'current_user')));
		$this->set('note_id', isset($this->request->query['note_id']) ? $this->request->query['note_id'] : null);
	}

/**
 * admin_add method
 *
 * @param integer $collection_id
 * @return void
 */
	public function admin_add($collection_id = null) {
		if ($this->request->is('post')) {
			$this->Video->create();
			if ($this->Video->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Session->setFlash(__('Video added.'), 'flash_success');
				$this->redirect(array(
					'controller' => 'collections', 
					'action' => 'edit', 
					$collection_id
				));
			} else {
				$this->Session->setFlash(__('Video not added. Please check the form for errors.'), 'flash_failure');
				$this->set('video', $this->Video->data);
			}
		}

		$this->set('collection_id', $collection_id);
		$this->set('collection', $this->Video->Collection->read(null, $collection_id));
	}

/**
 * admin_edit method
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Video->id = $id;
		if (!$this->Video->exists()) {
			throw new NotFoundException(__('Invalid target'));
		}
		
		if ($this->request->is('post')) {
			if ($this->Video->saveAll($this->request->data, array('validate' => 'first'))) {
				$this->Session->setFlash(__('Video saved'), 'flash_success');
				$this->redirect(array(
					'controller' => 'collections',
					'action' => 'edit_items', 
					$this->Video->field('collection_id')
				));
			} else {
				$this->Session->setFlash(__('Video not saved. Please check the form for errors.'), 'flash_failure');
			}
		} 
		
		$video = $this->Video->read(null, $id);
		$this->request->data = $video;

		$this->set('video', $video);
		$this->set('collection', $this->Video->Collection->read(null, $video['Video']['collection_id']));
	}

/**
 * admin_delete method
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		
		$this->Video->id = $id;
		if ($this->Video->exists()) {
			$this->Video->delete();
		}
		
		$this->Session->setFlash(__('Video deleted'), 'flash_success');
		
		$this->redirect(array(
			'controller' => 'collections', 
			'action' => 'edit', 
			$this->request->query['collection_id']
		));
	}
}
