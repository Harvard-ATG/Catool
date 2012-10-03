<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Component', 'Controller');
App::uses('IsitesRequest', 'Lib');

class IsitesAuthServiceComponent extends Component {

	/**
	 * Required components
	 * 
	 * @var array
	 */
	public $components = array('Session', 'Auth');
	
	/**
	 * Reference to a controller
	 * 
	 * @var Controller
	 */
	public $Controller;
	
	/**
	 * Called before the Controller::beforeFilter().
	 * 
	 * @param $controller 
	 * @return void
	 */
	public function initialize($controller) {
		parent::initialize($controller);
		$this->Controller =& $controller;
		$this->Controller->loadModel('AuthService.UniversityUser');
		$this->Controller->loadModel('User');
	}

	/**
	 * Login 
	 * 
	 * @return void
	 */
	public function login() {
		$request = $this->Controller->request;
		$id = $request->isites->getUser()->getId();
		$universityUser = $this->Controller->UniversityUser;

		if(!$universityUser->existsUser($id)) {
			$universityUser->registerUser($id);
		}
		
		$data = $universityUser->loadUser($id);
		if($data) {
			$this->Auth->login($data['User']);
		} else {
			throw new CakeException("Failed to load university user");
		}
	}
}
