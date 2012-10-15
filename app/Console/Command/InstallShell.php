<?php
App::uses('AppShell', 'Console/Command');

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
 * Coordinates tasks such as creating the database config setup, 
 * creating the schema, etc.
 * 
 * @return void
 */
	public function app() {
		if(!config('database')) {
			$this->dispatchShell('bake', 'db_config');
		}
		$this->dispatchShell('schema', 'create',  '--connection', $this->connection);		
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

}
