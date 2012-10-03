<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Role Model
 *
 * @package app.Model
 */
class Role extends AppModel {

/**
 * Super user role name constant.
 */
	const SUPER = 'super';
	
/**
 * Administrator role name constant.
 */
	const ADMIN = 'admin';

/**
 * Moderator role name constant.
 */
	const MOD = 'mod';

/**
 * Normal user role name constant.
 */
	const USER = 'user';

/**
 * Guest role name constant.
 */
	const GUEST = 'guest';

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UserCollection' => array(
			'className' => 'UserCollection',
			'foreignKey' => 'role_id'
		)
	);

/**
 * actsAs
 *
 * used by Cake's AclBehavior
 *
 * @var array
 */
	public $actsAs = array(
		'Tree',
		'Acl' => array('type' => 'controlled')
	);

/**
 * beforeSave callback
 *
 * @param array $options
 */
	public function beforeSave($options = array()) {
		// all roles must have a valid alias or name 
		$role_name = $this->getRoleNameOnSave();
		$this->assertValidName($role_name);
	}

/**
 * afterSave callback
 *
 * @param boolean $created
 * @return void
 */
	public function afterSave($created) {
		// the role ACO automatically binds to the model by id,
		// but we also want to be able to query by alias
		$role_name = $this->getRoleNameOnSave();
		$this->assertValidName($role_name);
		$this->Aco->save(array('alias' => $role_name));
	}

/**
 * parentNode
 *
 * method used with Cake's AclBehavior
 *
 * @return mixed
 */
	public function parentNode() {
		$parent_id = null;
		if(isset($this->data[$this->alias]['parent_id'])) {
			$parent_id = $this->data[$this->alias]['parent_id'];
		} else {
			$parent_id = $this->field('parent_id');
		}

		if(!isset($parent_id)) {
			return 'role';
		} 

		$parent = array(
			'model' => $this->alias, 
			'foreign_key' => $parent_id
		);

		return $parent;
	}

/**
 * getDefaultRole method
 *
 * @return Role model
 */
 	public function getDefaultRole() {
		$role =  $this->find('first', array(
			'conditions' => array('name' => self::USER),
			'recursive' => -1
		));
		return $role;
	}

/**
 * getRoleNames method
 *
 * @return array
 */
	public function getRoleNames() {
		return array(self::SUPER, self::ADMIN, self::MOD, self::USER, self::GUEST);
	}

/**
 * getDisplayNameFor
 *
 * @param string $name of the role
 * @return array
 */
	public function getDisplayNameFor($name) {
		$this->assertValidName($name);

		$display_for = array(
			self::SUPER => 'Super User',
			self::ADMIN => 'Administrator',
			self::MOD => 'Moderator',
			self::USER => 'Member',
			self::GUEST => 'Guest'
		);

		return $display_for[$name];
	}

/**
 * getPathToRole
 *
 * @param string $name of the role
 * @return array
 */
	public function getPathToRole($name) {
		$this->assertValidName($name);
		$role_id = $this->getRoleIdByName($name);
		$path = $this->getPath($role_id);

		return $path;
	}

/**
 * getRoleIdByName
 * 
 * @param string $name of the role
 * @return integer
 */
 	public function getRoleIdByName($name) {
		$this->assertValidName($name);
		$role = $this->find('first', array(
			'recursive' => -1,
			'fields' => array("{$this->alias}.id"),
			'conditions' => array("{$this->alias}.name" => $name)
		));
		return $role[$this->alias]['id'];
	}

/**
 * getAdminRoleIds
 *
 * @return array
 */
	public function getAdminRoleIds() {
		$path = $this->getPathToRole(self::ADMIN);
		return Set::classicExtract($path, "{n}.{$this->alias}.id");
	}

/**
 * assertValidName
 *
 * @param string $name of the role
 * @throws CakeException if the role name is invalid
 */
	protected function assertValidName($name) {
		if(!in_array($name, $this->getRoleNames())) {
			throw new CakeException("Invalid parameter name: $name");
		}
	}


/**
 * getRoleNameOnSave
 *
 * @return string name in Model::$data or from database
 */
 	protected function getRoleNameOnSave() {
		$role_name = '';
		if(isset($this->data[$this->alias]['name'])) {
			$role_name = $this->data[$this->alias]['name'];
		} else {
			$role_name = $this->field('name');
		}

		return $role_name;
	}

}
