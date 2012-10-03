<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('JstestAppController', 'Jstest.Controller');
App::uses('Folder', 'Utility');
App::uses('File', 'Utility');

/**
 * Jstests Controller
 *
 */
class JstestsController extends JstestAppController {

	public $components = array('Auth');
	
	/**
	 * beforeFilter method
	 */
	public function beforeFilter() {
		$this->Auth->allow('*'); // auth not required for running tests
		
		// Just to be extra safe, don't allow this controller to be accessed
		// in production. See app/Config/bootstrap.php for plugin loading logic.
		if(Configure::read('debug') == 0) {
			die('This plugin should be disabled in production.');
		}
	}

	/**
	 * run method
	 *
	 * This executes all the tests. It loads all the js/test/*.js files
	 * and Qunit does the rest.
	 */
	public function run() {
		$tests = $this->_getAllTests();
		$modules = $this->_getAllModules();

		$this->set('modules', $modules);
		$this->set('tests', $tests);
	}
	
	/**
	 * _getModules method
	 * 
	 * This utility function returns a list of js modules
	 * to load for testing. We assume that the modules are in
	 * the correct build order.
	 */
	protected function _getAllModules() {
		$modules = include(WWW_ROOT . DS . 'js/build.php');
		if($modules === FALSE || !is_array($modules)) {
			error_log("Error loading javascript modules or files that are being tested");
		}
		
		return array_merge($modules['lib'], $modules['app']);
	}

	/**
	 * _getAllTests method 
	 *
	 * This is a utility function to find all the tests.
	 * It returns an array of relative urls to the *.js test files.
	 */
	protected function _getAllTests() {
		$testDir = Configure::read('Jstest.testDir');
		$testUrl = Configure::read('Jstest.testUrl');
		if(!isset($testDir)) {
			error_log("No javascript test directory has been configured");
		}

		$tests = array();
		$dir = new Folder($testDir);
		$files = $dir->find('.*\.js'); // find all js files

		$urls = array();
		foreach($files as $file) {
			$urls[] = $testUrl . '/' . $file;
		}

		return $urls;
	}
}

