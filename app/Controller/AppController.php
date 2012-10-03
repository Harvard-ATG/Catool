<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2012, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
 
App::uses('Controller', 'Controller');
 
/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 * @link http://book.cakephp.org/2.0/en/controllers.html#the-app-controller
 */
class AppController extends Controller {
	/**
	* components
	* @var array
	*/
    public $components = array('Auth', 'Session', 'Acl');
    /**
	* helpers
	* @var array
	*/
	public $helpers = array('Form', 'Text', 'Session', 'NavRenderer', 'Js');
	
	/**
	* constructor
	* @param string $request
	* @param string $response
	*/
	function __construct($request = null, $response = null) {
		parent::__construct($request, $response);
		
		$authService = $this->Components->load('AuthService.AuthService');
		$helpers = array();
		if(Configure::read('App.Isites')) {
			$authService->loginType = 'Isites';
			$helpers = array(
				'Html' => array('className' => 'IsitesHtml')
			);
		} else {
			$authService->loginType = 'Demo';
			$helpers = array(
				'Html' => array('className' => 'Html')
			);
		}
		
		$this->helpers = array_merge($this->helpers, $helpers);
	}

	/**
	 * beforeFilter method
	 *
	 * @return void
	 */
	public function beforeFilter() {
		parent::beforeFilter();
		$this->__setNavRendererPerms();
	}

	/**
	 * _isAdmin method
	 *
	 * Check if the user is an admin
	 *
	 * @return boolean true if the user has the admin role, false otherwise
	 */
	public function _isAdmin() {
		$user_id = $this->Auth->user('id');
		$aro = array('model' => 'User', 'foreign_key' => $user_id); 
		$aco = 'role/super/admin';
		return $this->Acl->check($aro, $aco);
	}
	
	/**
	 * _getUserId method
	 *
	 * @return number
	 */
	 public function _getUserId() {
	 	 return $this->Auth->user('id');
	 }

	/**
	 * __setNavRendererPerms method
	 *
	 * Sets permissions for the helper that renders the nav bar.
	 *
	 * @return void
	 */
	private function __setNavRendererPerms() {
		$userPermission = array(
			'allowProxy' => false, 
			'allowAdmin' => false
		);

		if($this->Auth->loggedIn()) {
			$user = array('model' => 'User', 'foreign_key' => $this->Auth->user('id'));
			$isSuper = $this->Acl->check($user, 'role/super');
			$userPermission = array(
				'allowProxy' => $isSuper,
				'allowAdmin' => $isSuper
			);
		} 

		// pass as settings to the helper constructor
		$this->helpers['NavRenderer'] = $userPermission;
	}
}
