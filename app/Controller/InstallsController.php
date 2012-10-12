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
	public $components = array('WebInstaller');

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
 * Displays the installer.
 *
 * @return void
 */
	public function index() {
		$db_config = new DATABASE_CONFIG();
		$db_status = $this->WebInstaller->testDbConfig();

		$this->set('db', $db_config);
		$this->set('db_status', $db_status);

		if(isset($this->request->params['create']) && $db_status['connected']) {
			$db_schema = $this->WebInstaller->createSchema();
			$this->set('db_schema', $db_schema);
		}
	}

/**
 * Prompts the user for database configuration and checks the connection.
 *
 * @return void
 */
	public function database() {
		if($this->request->is('post')) {
			$this->WebInstaller->saveDbConfig($this->request->data['DATABASE_CONFIG']);
		} 
		return $this->redirect(array('action' => 'index'));
	}

/**
 * Creates the schema. 
 *
 */

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
