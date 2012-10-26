<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

class BadAuthServiceLoginType extends CakeException {
	protected $_messageTemplate = 'Invalid login type %s.'; 

	public function __construct($message, $code = 500) {
		parent::__construct($message, $code);
	}
}

class AuthServiceComponent extends Component {
	
	/**
	 * List of available authentication services.
	 * 
	 * @var array
	 */
	public static $validLoginTypes = array('Demo', 'Openid');
	
	/**
	 * Default authentication service if none is configured.
	 * 
	 * @var string
	 */
	public static $defaultLoginType = 'Demo';
	
	/**
	 * Specifies the login method to use.
	 * Determined at run time. Uses the value specified on the configuration, 
	 * otherwise the default.
	 *
	 * @var string
	 */
	public $loginType = null;
	
	/**
	 * The initialize method is called before the controller’s beforeFilter method.
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function initialize($controller) {
		if(!isset($this->loginType)) {
			$configLoginType = Configure::read('App.loginType');
			$this->loginType = isset($configLoginType) ? $configLoginType : self::$defaultLoginType;
		}

		if(in_array($this->loginType, self::$validLoginTypes)) {
			$loginType = $this->loginType;
		} else {
			throw new BadAuthServiceLoginType(array('loginType' => $this->loginType));
		}

		$controller->loginRedirect = Router::url('/'); // shouldnt require auth

		$this->{'initAuth'.$loginType}($controller);
	}
	
	/**
	 * Initializes authentication for openid.
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function initAuthOpenid($controller) {
		$controller->Auth->loginAction = array(
			'plugin' => 'auth_service', 
			'controller' => 'openid_users', 
			'action' => 'login'
		);
	}
	
	/**
	 * Initializes authentication for demo.
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function initAuthDemo($controller) {
		$controller->Auth->loginAction = array(
			'plugin' => 'auth_service', 
			'controller' => 'demo_users', 
			'action' => 'login'
		);
	}
}

