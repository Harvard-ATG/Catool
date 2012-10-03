<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
class AuthServiceComponent extends Component {
	
	/**
	 * List of available authentication services.
	 * 
	 * @var array
	 */
	public static $authServices = array('Demo', 'Openid', 'Isites');
	
	/**
	 * Default authentication service if none is configured.
	 * 
	 * @var string
	 */
	public static $defaultAuthService = 'Demo';
	
	/**
	 * Specifies the login action.
	 *
	 * @var string
	 */
	public $loginType = 'null';
	
	/**
	 * The initialize method is called before the controller’s beforeFilter method.
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function initialize($controller) {
		$loginType = self::$defaultAuthService;
		if(isset($this->loginType) && in_array($this->loginType, self::$authServices)) {
			$loginType = $this->loginType;
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
	
	/**
	 * Initializes authentication for demo.
	 * 
	 * @param $controller Controller
	 * @return void
	 */
	public function initAuthIsites($controller) {
		$component = $controller->Components->load('AuthService.IsitesAuthService');
		$component->initialize(&$controller);
		$component->login();
	}
}
	
