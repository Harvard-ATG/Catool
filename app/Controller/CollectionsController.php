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
class CollectionsController extends AppController {
	/**
	* helpers included by cake
	* @var array
	*/
	public $helpers = array('Time', 'TargetLink');

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
			case 'view':
			case 'posts':
				$collection_id = $request->params['pass'][0];
				$aro = $this->Collection->UserCollection->findFromCollection($user_id, $collection_id);
				$aco = 'role/super/admin/mod/user';
				return $this->_isAdmin() || $this->Acl->check($aro, $aco);
			case 'admin_add':
				return $this->_isAdmin(); 
			case 'admin_index':
				return $this->_isAdmin() || $this->Collection->UserCollection->isAdmin($user_id);
			case 'admin_edit':
			case 'admin_edit_permissions':
			case 'admin_edit_items':
			case 'admin_delete':
				$collection_id = $request->params['pass'][0];
				$aro = $this->Collection->UserCollection->findFromCollection($user_id, $collection_id);
				$aco = 'role/super/admin';
				return $this->_isAdmin() || $this->Acl->check($aro, $aco);
			default:
		}

		return true; // allow by default
	}


/**
 * Displays a list of collections
 *
 * @return void
 */
	public function index() {
		$user_id = $this->_getUserId();
		if($this->_isAdmin()) {
			$this->Collection->recursive = -1;
			$collections = $this->Collection->find('all');
		} else {
			$collections = $this->Collection->findUserCollections($user_id);
		}

		$this->set('hasManagePermission', $this->_isAdmin() || $this->Collection->UserCollection->isAdmin($user_id));
		$this->set('collections', $collections);
	}

/**
 * View a single collection
 *
 * @param integer $id
 * @return void
 */
	public function view($id = null) {
		$this->Collection->id = $id;
		if (!$this->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}
		
		$search = '';
		if(isset($this->request->query['search'])) {
			$search = $this->request->query['search'];
		}

		$this->set('note_stats_for', $this->Collection->getTargetStats($id));
		$this->set('targets', $this->Collection->findTargetsWith($search, $id));
		$this->set('collection', $this->Collection->find('first', array(
			'conditions' => array('Collection.id' => $id),
			'recursive' => 0
		)));
	}
	
/**
 * View all the posts in one or many collections.
 * 
 */
	public function posts($collection_id = null) {
		$this->Collection->id = $collection_id;
		if (isset($this->Collection->id) && !$this->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}
		
		$user_id = isset($this->request->query['user_id']) ? $this->request->query['user_id'] : null;
		
		$this->set('collection_id', $collection_id);
		$this->set('collection', $this->Collection->read(null, $collection_id));
		$this->set('user_id', $user_id);
		$this->set('users', $this->Collection->findUsersWithPosts($collection_id));
		$this->set('notes', $this->Collection->findPostsByCollection($collection_id, $user_id));
	}
 
/**
 * Admin view of collection list
 *
 * @return void
 */
	public function admin_index() {
		$user_id = $this->_getUserId();

		if($this->_isAdmin()) {
			$this->Collection->recursive = -1;
			$collections = $this->Collection->find('all');
		} else {
			$collections = $this->Collection->findUserAdminCollections($user_id);
		}

		$this->set('collections', $collections);
		$this->set('hasCreatePermission', $this->_isAdmin());
	}

/**
 * Admin action to add a new collection
 *
 * @return void
 */
	public function admin_add() {
		if ($this->request->is('post')) {
			$this->Collection->create();
			$result = $this->Collection->save($this->request->data);
			if ($result !== false) {
				$this->Session->setFlash(__('Collection created'), 'flash_success');
				$this->Collection->UserCollection->create();
				
				$result2 = $this->Collection->UserCollection->save(array(
					'collection_id' => $this->Collection->id,
					'user_id' => $this->_getUserId(),
					'role_id' => ClassRegistry::init('Role')->getRoleIdByName(Role::ADMIN)	
				));
				if(!$result2) {
					$this->Session->setFlash(__('Error adding you as an admin to the collection.'), 'flash_failure');
				}

				return $this->redirect(array('action' => 'edit', $result['Collection']['id']));
			} else {
				$this->Session->setFlash(__('Collection not created', 'flash_failure'));
			}
		}
	}

/**
 * Admin action to edit a collection
 *
 * @param string $id
 * @return void
 */
	public function admin_edit($id = null) {
		$this->Collection->id = $id;
		if (!$this->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Collection->save($this->request->data)) {
				$this->Session->setFlash(__('Collection saved'), 'flash_success');
			} else {
				$this->Session->setFlash(__('Collection not saved'), 'flash_failure');
			}
			return $this->redirect($this->request->here);
		} 

		$this->request->data = $this->Collection->read(null, $id); // FormHelper uses request data
		
		$this->set('collection_id', $id);
		$this->set('collection', $this->Collection->read(null, $id));
	}
/**
 * Admin action to edit a collection
 *
 * @param string $id
 * @return void
 */
	public function admin_edit_permissions($id = null) {
		$this->Collection->id = $id;
		if (!$this->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}

		$this->set('user_id', $this->_getUserId());
		$this->set('collection_id', $id);
		$this->set('collection', $this->Collection->read(null, $id));
		$this->set('user_collections', $this->Collection->UserCollection->findAllCollectionUsers($id));
		$this->set('user_collections_by_id', $this->Collection->UserCollection->findAllCollectionUsersIndexedById($id));
		$this->set('roles', array_reverse($this->Collection->UserCollection->getRoleTypes()));
		
	}
	
/**
 * Admin action to edit a collection
 *
 * @param string $id
 * @return void
 */
	public function admin_edit_items($id = null) {
		$this->Collection->id = $id;
		if (!$this->Collection->exists()) {
			throw new NotFoundException(__('Invalid collection'));
		}
			
		$search = isset($this->request->query['search']) ? $this->request->query['search'] : '';
		
		$this->set('collection_id', $id);
		$this->set('collection', $this->Collection->read(null, $id));
		$this->set('note_stats_for', $this->Collection->getTargetStats($id));
		$this->set('targets', $this->Collection->findTargetsWith($search, $id));
	}

/**
 * Admin action to delete a collection
 *
 * @param string $id
 * @return void
 */
	public function admin_delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		
		$this->Collection->id = $id;
		if($this->Collection->exists()) {
			$this->Collection->delete();
		}
		
		$this->Session->setFlash(__('Collection deleted'), 'flash_success');
		
		return $this->redirect(array('action' => 'index'));
	}
}
