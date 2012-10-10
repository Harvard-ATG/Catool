<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppController', 'Controller');

/**
 * Pages Controller
 *
 * @package       app.Controller
 */
class NotesController extends AppController {

/**
* components added by cake
* @var array
*/
	public $components = array('RequestHandler', 'Acl');

/**
* helpers 
* @var array
*/
	public $helpers = array();

/**
 * beforeFilter method
 * 
 * Callback that is called before the controller action.
 * 
 * @return void
 */
	public function beforeFilter() {
		parent::beforeFilter();

		 // pre-emptively start request handler so that the data is parsed before auth check
		$this->RequestHandler->startup($this);
		
		// intentionally not using the ControllerAuthorize auth adapter here because
		// that adapter redirects the user on failure. instead, we just want to throw an error 
		// that is converted to JSON because this controller is accessed via AJAX.
		if(!$this->isAuthorized()) {
			throw new ForbiddenException();
		}
	}
	
/**
 * isAuthorized method
 * 
 * @param User $user the user object
 * @param CakeRequest $request the request object
 * @return boolean true if authorized, false otherwise
 */
	public function isAuthorized() {		
		$user_id = $this->_getUserId();
		$request = $this->request;
		$action = $request->params['action'];

		// retrieve the user's collection membership based on the note's target
		switch($action) {
			case 'index':
				$target_id = $request->query['target_id'];
				break;
			case 'view':
			case 'edit':
			case 'delete':
				$this->Note->id = $request->params['pass'][0];
				$target_id = $this->Note->field('target_id');
				break;
			case 'add':
				$target_id = $request->data['Note']['target_id'];
				break;
			default:
				$target_id = null;
				break;
		}

		// check for restrictions on add/edit/delete
		$role = 'role/super/admin/mod/user';
		switch($action) {
			// only admins can add notes if they are locked
			case 'add':
				$target_id = $request->data['Note']['target_id'];
				$type = $request->data['Note']['type'];
				$settings = $this->Note->Target->getSettings($target_id);
				$locked = (($this->Note->isAnnotation($type) && $settings['lock_annotations']) 
					|| ($this->Note->isComment($type) && $settings['lock_comments']));

				if($locked) {
					$role = 'role/super/admin';
				}
				break;
			// only mods or note owners can edit notes
			case 'edit':
			case 'delete':
				$note_id = $request->params['pass'][0];
				if(!$this->Note->isOwnedBy($note_id, $user_id)) { 
					$role = 'role/super/admin/mod';
				}
				break;
		}

		$this->loadModel('UserCollection');
		$user_collection_aro = $this->UserCollection->findFromTarget($user_id, $target_id);
		$user_aro = array('model' => 'User', 'foreign_key' => $user_id);

		return $this->Acl->check($user_aro, 'role/super/admin/mod') || $this->Acl->check($user_collection_aro, $role);
	}

/**
 * index method
 *
 * @return void
 */
	public function index() {
		$query = '';
		if(isset($this->request->query['q'])) {
			$query = $this->request->query['q'];
		}		
	
		$target_id = null;
		if(isset($this->request->query['target_id'])) {
			$target_id = $this->request->query['target_id'];
		}
		
		$this->Note->Target->id = $target_id;
		if (!$this->Note->Target->exists()) {
			throw new NotFoundException(__('Invalid target'));
		}

		$results = $this->Note->findNotesByTarget($target_id, $query);
		foreach($results as &$row) {
			unset($row['Target']);
		}
		
		$this->set('results', $results);
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Note->id = $id;
		if (!$this->Note->exists()) {
			throw new NotFoundException(__('Invalid note'));
		} 
		
		$results = $this->Note->find('first', array(
			'conditions' => array('Note.id' => $id),
			'recursive' => 1
		));
		
		$this->set(compact('results'));
        $this->set('_serialize', array('results'));		
    }

/**
 * add method
 *
 * @return void
 */
	public function add() {
		$data = array();
		$data['Note'] = $this->request->data['Note'];
		$data['Note']['user_id'] = $this->_getUserId();
		
		if(isset($this->request->data['Segment'])) {
			$data['Segment'] = $this->request->data['Segment'];
		}
		
		$this->Note->create();
		if(!$this->Note->saveAll($data)) {
			throw new InternalErrorException();
		}
		
		$results = $this->Note->find('first', array(
			'conditions' => array('Note.id' => $this->Note->id),
			'recursive' => 1
		));	
		
		$this->set(compact('results'));
		$this->set('_serialize', array('results'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Note->create();
		if(!$this->Note->save($this->request->data, true, array('title', 'body'))) {
			throw new InternalErrorException();
		}

		if(isset($this->request->data['Segment'])) {
			$this->Note->Segment->create();
			if(!$this->Note->Segment->save($this->request->data)) {
				throw new InternalErrorException();
			}
		}

		$results = array('success' => true);

		$this->set(compact('results'));
		$this->set('_serialize', array('results'));
    }

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Note->id = $id;

		$deleted = $this->Note->delete();
		$results = array('success' => (bool) $deleted);
		
		$this->set(compact('results'));
		$this->set('_serialize', array('results'));
	}
}
