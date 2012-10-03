<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'AuthService.LightOpenID', array('file' => 'lightopenid'.DS.'openid.php')); 

class OpenidAuthServiceComponent extends Component {
	
	/**
	 * Component debugging output.
	 * 
	 * @var boolean
	 */
	public $enableDebug = false;

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
	 * Holds a reference to the LightOpenID object.
	 * 
	 * @var LightOpenID
	 */
	public $LightOpenID;
	
	/**
	 * Maps User model attributes to AX/SREG openid attributes
	 * 
	 * @var array
	 */
	public $userAttributes = array(
		'firstname' => 'namePerson/first',
		'lastname' 	=> 'namePerson/last',
		'email' 	=> 'contact/email'
	);

	/**
	 * Called before the Controller::beforeFilter().
	 * 
	 * @param $controller 
	 * @return void
	 */
	public function initialize($controller) {
		parent::initialize($controller);
		$this->LightOpenID = new LightOpenID(env('SERVER_NAME'));
		$this->Controller =& $controller;
		$this->Controller->loadModel('AuthService.OpenidUser');
		$this->Controller->loadModel('User');
	}
	
	/**
	 * Called after the Controller::beforeFilter() and before the controller action
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function startup($controller) {
		parent::startup($controller);
		$this->_debug($this->Controller->request);	
	}

	/**
	 * Login with OpenID
	 * 
	 * @return void
	 */
	public function login() {
		if($this->_isOpenIDResponse()) {
			$this->_handleOpenIDResponse();
		} else if($this->_isUserLogin()) {
			$this->_makeOpenIDRequest();
			} else {
			// show user login form
		}
	}

	/**
	 * Impersonate an OpenID user (requires super user access)
	 * 
	 * @return void
	 */
	public function proxy() {
		if($this->Controller->request->is('post')) {
			$claimed_id = trim($this->Controller->request->data['OpenidUser']['claimed_id']);
			if(empty($claimed_id)) {
				$this->_setRealUser(); 
				$this->Session->setFlash(__('You are now logged in as yourself'));
				$this->redirect($this->Auth->redirect());
			} else if($this->_existsOpenIDUser($claimed_id)) {
				$this->_setProxyUser($claimed_id);
				$this->Session->setFlash(__("You are now logged in as: %s", $claimed_id));
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash(__('User does not exist'));
			}
		}
	}
	
	/**
	 * Wrapper for controller redirect
	 * 
	 * @param string $url 
	 * @return void
	 */
	public function redirect($url) {
		$this->Controller->redirect($url);
	}

	/**
	 * Returns true if there is a response from the OpenID provider, false otherwise
	 * 
	 * @return boolean
	 */
	public function _isOpenIDResponse() {
		return $this->LightOpenID->mode ? true : false;
	}
	
	/**
	 * Returns true if the user has initiated the sign-in process, false otherwise
	 * 
	 * @return boolean
	 */
	public function _isUserLogin() {
		return $this->Controller->request->is('post') && $this->Controller->request->data['is_login'] === 'yes';
	}
	
	/**
	 * Redirects the user to the OpenID provider for authentication. 
	 *
	 * @return void
	 */
	public function _makeOpenIDRequest() {
		$this->LightOpenID->realm = (!empty($_SERVER['HTTPS']) ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'];
		$this->LightOpenID->returnUrl = $this->LightOpenID->realm . $_SERVER['REQUEST_URI'];
		$this->LightOpenID->identity = 'https://www.google.com/accounts/o8/id';
		$this->LightOpenID->required = array($this->userAttributes['firstname'], $this->userAttributes['lastname'], $this->userAttributes['email']);
		$this->LightOpenID->optional = array();
		$this->redirect($this->LightOpenID->authUrl());
	}

	/**
	 * Validates the OpenID provider's response and logs in the user.
	 * 
	 * If the user doesn't already exist, a new user account is created for them
	 * and their attributes are saved.
	 * 
	 * @return void
	 */
	public function _handleOpenIDResponse() {
		if($this->LightOpenID->mode == 'cancel') {
			$this->Session->setFlash(__('Login canceled'), 'default', array(), 'auth');
		} else if($this->LightOpenID->validate()) {
			if(!$this->_existsOpenIDUser($this->LightOpenID->identity)) {
				$this->_registerOpenIDUser($this->LightOpenID->identity, $this->LightOpenID->getAttributes());
			}
			$data = $this->_loadOpenIDUser($this->LightOpenID->identity);
			if($data) {
				$this->Auth->login($data['User']);
				$this->redirect($this->Auth->redirect());
			} else {
				$this->Session->setFlash("OpenID verified, but failed to load user data from the database");
			}
		} else {
			$this->Session->setFlash(__('OpenID verification failed'), 'default', array(), 'auth');
		}
	}
	
	/**
	 * Returns true if the OpenID identity exists in the database, false otherwise.
	 * 
	 * @param string $claimed_id user's claimed identity
	 * @return boolean
	 */
	public function _existsOpenIDUser($claimed_id) {
		return $total = $this->Controller->OpenidUser->existsUser($claimed_id);
	}
	
	/**
	 * Loads a user from the database.
	 * 
	 * @param string $claimed_id user's claimed identity
	 * @return mixed
	 */
	public function _loadOpenIDUser($claimed_id) {
		return $this->Controller->OpenidUser->loadUser($claimed_id);
	}

	/**
	 * Registers a user in the database.
	 * 
	 * Creates a new user account using the provided attributes (name, email, etc),
	 * and links that to the user's OpenID identity (claimed ID).
	 * 
	 * @param string $claimed_id user's claimed identity
	 * @param mixed $attributes the AX/SREG attributes provided by the OP
	 * @return void
	 */
	public function _registerOpenIDUser($claimed_id, $attributes = array()) {
		$user_attributes = $this->_extractOpenIDAttributes($attributes);
		$user_attributes['name'] = $user_attributes['firstname'].' '.$user_attributes['lastname'];
		$this->Controller->OpenidUser->registerUser($claimed_id, $user_attributes);
	}

	/**
	 * Extracts the attributes sent back from the OpenID provider and
	 * maps them to user attributes.
	 * 
	 * @param mixed $attributes the AX/SREG attributes provided by the OP
	 * @return mixed
	 */
	public function _extractOpenIDAttributes($attributes) {
		$attributeFor = array_flip($this->userAttributes);
		$data = array();
		foreach($attributes as $name => $value) {
			$field = $attributeFor[$name];
			$data[$field] = $value;
		}
		
		return $data;
	}

	/**
	 * Sets the currently logged in user to their real account.
	 * 
	 * @return void
	 */
	public function _setRealUser() {
		$user = $this->Auth->user();
		$user['id'] = $user['real_user_id'];
		unset($user['real_user_id']);

		$this->Auth->login($user);
	}

	/**
	 * Sets the currently logged in user to a different account.
	 * 
	 * @return void
	 */
	public function _setProxyUser($claimed_id) {
		$data = $this->_loadOpenIDUser($claimed_id);
		if(!$data) {
			throw new InternalErrorException("Unable to load user account data.");
		}

		$proxy_user_id = $data['User']['id'];
		$real_user_id = $this->Auth->user('id');

		if($proxy_user_id !== $real_user_id) {
			$proxy_user = array_merge($data['User'], array('real_user_id' => $real_user_id));
			$this->Auth->login($proxy_user);
			$this->log("User [$real_user_id] proxied as [$proxy_user_id] via openid [$claimed_id]", 'debug');
		}
	}

	/**
	 * Logs some debug output.
	 * 
	 * @return void
	 */
	private function _debug($o) {
		if($this->enableDebug) {
			$ds = str_repeat('-', 4);
			error_log(implode("\n", array(
				'Begin debug output... ', 
				"{$ds}Var Export{$ds}",
				Debugger::exportVar($o),
				"{$ds}Stack Trace{$ds}",
				Debugger::trace()
			)));
		}
	}
}
