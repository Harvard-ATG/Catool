<?php
App::uses('AppShell', 'Console/Command');
App::uses('Controller', 'Controller');
App::uses('ComponentCollection', 'Controller');
App::uses('AclComponent', 'Controller/Component');
App::uses('DbAcl', 'Model');
App::uses('Role', 'Model');


/**
 * Application Installation Shell
 *
 * Central place for coordinating and managing installation tasks such as
 * configuring the database, creating the schema, creating the ACLs, and
 * running the test suite.
 */
class InstallShell extends AppShell {

/**
 * Contains instance of an Acl component
 * 
 * @var AclComponent
 */
	public $Acl;

/**
 * The connection being used.
 *
 * @var string
 */
	public $connection = 'default';

/**
 * Assign $this->connection to the active task if a connection param is set.
 *
 * for installing to test db: Console/cake install app --connection=test
 *
 * @return void
 */
	public function startup() {
		parent::startup();
		Configure::write('Security.salt', sha1('Catool'.time()));
		Configure::write('Security.cipherSeed', implode("", array_map("rand", array_fill(0, 20, 1), array_fill(0, 20, 10))));

		Configure::write('debug', 2);
		Configure::write('Cache.disable', 1);
		
		if (isset($this->params['connection'])) {
			$this->connection = $this->params['connection'];
		}
	}

/**
 * Install the application.
 * 
 * It is responsible for doing the following:
 *   1) Coordinate and sequence the tasks of setting up the database config
 *      and schema. Dispatch to other Cake shells as required.
 *   2) Populate the schema with default data.
 *   3) Setup ACLs.
 *   4) Miscellaneous post-install tasks.
 * 
 * @return void
 */
	public function app() {
		if(!config('database')) {
			$this->dispatchShell('bake', 'db_config');
		}
		$this->dispatchShell('schema', 'create',  '--connection', $this->connection);		
		$this->_startupAcl();
		$this->_setupAclStructure();
		$this->_setupModels();
		$this->_setupAclGrants();
		$this->out(__d('cake_console', 'Installation complete'));
	}

/**
 * Run the application test suite. 
 *
 * @return void
 */
	public function test() {
		$this->dispatchShell('testsuite', 'app', 'AllTests');
	}

/**
 * Get and configure the Option parser
 *
 * @return ConsoleOptionParser
 */
	public function getOptionParser() {
		$parser = parent::getOptionParser();

		return $parser->description(__d('cake_console',
			'The Install Shell guides the user through the process of installing the application.')
		)->addSubcommand('app', array(
			'help' => __d('cake_console', 'Install the app (setup db, ACLs, etc)')
		))->addSubcommand('test', array(
			'help' => __d('cake_console', 'Runs the application test suite')
		))->addOption('connection', array(
			'help' => __d('cake_console', 'Database connection to use in conjunction with `bake all`.'),
			'short' => 'c',
			'default' => 'default'
		));
	}

/**
 * Startup the AclComponent so it's ready to use
 *
 * @return void
 */
	protected function _startupAcl() {
		Configure::write('Acl.database', $this->connection);

		$collection = new ComponentCollection();
		$this->Acl = new AclComponent($collection);
		$controller = new Controller();
		$this->Acl->startup($controller);
	}

/**
 * Loads a model and sets the correct data source
 * (i.e. default, test, etc).
 *
 * @return void
 */
	protected function _m($className) {
		$model = ClassRegistry::init($className);
		$model->useDbConfig = $this->connection;
		return $model;
	}

/**
 * Populate the schema with data required for operation.
 * 
 * @return void
 */
	protected function _setupModels() {
		$role = $this->_m('Role');
		$user = $this->_m('User');

		// setup hierarchical roles
		$parent_id = null;
		$role_names = $role->getRoleNames();
		foreach($role_names as $role_name) {
			$role->create();
			$role->save(array(
				'name' => $role_name, 
				'display_name' => $role->getDisplayNameFor($role_name), 
				'parent_id' => $parent_id
			));
			$parent_id = $role->id;
		}

		// setup default user
		$user->create();
		$user->save(array(
			'name' => 'Root', 
			'role_id' => $role->getRoleIdByName(Role::SUPER)
		));
	}

/**
 * Setup the default ACL structures.
 *
 * Must be done *before* models are populated.
 *
 * @return void
 */
	protected function _setupAclStructure() {
		$this->dispatchShell('acl', 'initdb');

		// Create ACO root node for the role hierarchy
		$this->Acl->Aco->create();
		$this->Acl->Aco->save(array('alias' => 'role'));

		// Create ARO root node for users and collection memberships
		$this->Acl->Aro->create();
		$this->Acl->Aro->save(array('alias' => 'users'));
	}

/**
 * Setup the default ACL grants.
 *
 * Must be done *after* models are populated
 *
 * @return void
 */
 	protected function _setupAclGrants() {
		// all users are given the user role by default
		$this->Acl->allow('users', 'role/super/admin/mod/user'); 

		// grant super powers to default user
		$default_user = $this->_m('User')->findById(1);
		if($default_user) {
			$this->Acl->allow($default_user, 'role/super'); 
		}
	}
}
