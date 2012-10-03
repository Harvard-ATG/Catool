<?php

/**
 * IsitesUser
 *
 * @package app.Vendor.Isitestool
 */
class IsitesUser {
	/**
	 * userid
	 * @var integer
	*/
	protected $id;
	/**
	 * role is one of the consts below
	 * @var string
	*/
	protected $role;
	/**
	 * permissions
	 * @var string
	*/
	protected $permissions;
	
	const SUPER = 18;
	const ADMIN = 16; // same as an owner
	const ENROLLEE = 9; // renamed from participant
	const GUEST = 7;
	
	/**
	 * constructor
	 *
	 * just sets the following params
	 * @param integer $user_id
	 * @param string $permissions
	 */
	public function __construct($user_id = null, $permissions = '') {
		$this->id = $user_id;
		$this->permissions = $this->parsePermissions($permissions);
	}
	
	/**
	 * gets the id
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * gets the permissions
	 * @return array keys of $permissions
	 */
	public function getPermissions() {
		return array_keys($this->permissions);
	}
	
	/**
	 * gets the role
	 * @return string ('super', 'admin', 'enrollee', 'guest')
	 */
	public function getRole() {
		if($this->isSuper()) {
			return 'super';
		} else if($this->isAdmin()) {
			return 'admin';
		} else if($this->isEnrollee()) {
			return 'enrollee';
		} else if($this->isGuest()) {
			return 'guest';
		}
		
		return null;
	}
	
	/**
	 * checks to see if user is a SUPER
	 * @return boolean
	 */
	public function isSuper() {
		return $this->hasPermission(self::SUPER);
	}
	
	/**
	 * checks to see if user is a ADMIN
	 * @return boolean
	 */
	public function isAdmin() {
		return $this->hasPermission(self::ADMIN);
	}
	
	/**
	 * checks to see if user is a ENROLLEE
	 * @return boolean
	 */
	public function isEnrollee() {
		return $this->hasPermission(self::ENROLLEE);
	}
	
	/**
	 * checks to see if user is a GUEST
	 * @return boolean
	 */
	public function isGuest() {
		return $this->hasPermission(self::GUEST);
	}
	
	/**
	 * returns the <id>:<role>
	 * @return string
	 */
	public function toString() {
		return sprintf("%s:%s", $this->getId(), $this->getRole());
	}
	
	/**
	 * checks if the permission exists for user
	 * @param integer $level constants
	 * @return boolean
	 */
	protected function hasPermission($level) {
		return isset($this->permissions[$level]);
	}
	
	/**
	 * parses the permissions
	 * @param array/string $permissions
	 * @return array
	 */
	protected function parsePermissions($permissions) {
		$result = array();
		if(is_array($permissions)) {
			$result = $permissions;
		} else if(is_string($permissions) && !empty($permissions)) {
			$result = explode(',', $permissions);
		}
		return array_flip($result);
	}
}
