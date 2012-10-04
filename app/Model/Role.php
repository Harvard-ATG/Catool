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
 * Role name constants.
 *
 * @type constant
 */
	const SUPER = 'super';
	const ADMIN = 'admin';
	const MOD = 'mod';
	const USER = 'user';
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
 * Called before every save.
 *
 * See Cake's beforeSave callback.
 *
 * @param array $options
 * @return boolean
 */
	public function beforeSave($options = array()) {
		$this->beforeSaveCheckName($options);
		return true;
	}

/**
 * Checks that the role name is valid before a save.
 *
 * Called from beforeSave().
 *
 * Note: this could throw a CakeException if the role name is invalid.
 *
 * @param array $options
 * @return void
 */
 	public function beforeSaveCheckName($options = array()) {
		$role_name = $this->getRoleNameOnSave();
		$this->assertValidName($role_name); 
 	}

/**
 * Called after every save.
 *
 * See Cake's afterSave callback.
 *
 * @param boolean $created
 * @return void
 */
	public function afterSave($created) {
		$this->afterSaveSetAcoAlias($created);
	}

/**
 * Sets the ACO alias for the role after every save.
 *
 * A Role model is automatically bound to an Aco node, but it does not 
 * automatically get an alias assigned to it. So this function fixes that by
 * mapping the role name to the ACO alias. This is useful because then we can
 * query ACOs using the alias instead of the model/id syntax.
 *
 * @param boolean $created
 * @return void
 */
 	public function afterSaveSetAcoAlias($created) {
		// the role ACO automatically binds to the model by id,
		// but we also want to be able to query by alias
		$role_name = $this->getRoleNameOnSave();
		$this->assertValidName($role_name);
		$this->Aco->save(array('alias' => $role_name)); 	 
 	}

/**
 * Finds the parent node in the ACL ACO structure for this model.
 *
 * Roles are considered "controlled" objects, which things like Users and
 * UserCollections might want to access. The roles are organized hierarchically,
 * starting with Super User at the top and going all the way down to Guest. Roles
 * at the top of the hierarchy automatically have permissions for those at the
 * bottom.
 *
 * Note: this method is used with Cake's AclBehavior
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
 * Returns the default role.
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
 * Returns a flat list of all valid role names.
 *
 * @return array
 */
	public function getRoleNames() {
		return array(self::SUPER, self::ADMIN, self::MOD, self::USER, self::GUEST);
	}

/**
 * Returns the display name for a role.
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
 * Returns the path from the root or "super" user down to a given role.
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
 * Returns the role ID associated with a role name.
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
 * Returns all admin role IDs that have access to the "admin" role.
 *
 * Since roles are organized hierarchically, this means any roles above admin
 * also inherit admin permissions.
 *
 * @return array of IDs
 */
	public function getAdminRoleIds() {
		$path = $this->getPathToRole(self::ADMIN);
		return Set::classicExtract($path, "{n}.{$this->alias}.id");
	}

/**
 * Asserts that a role name is valid.
 *
 * @param string $name of the role
 * @return void
 * @throws CakeException if the role name is invalid
 */
	protected function assertValidName($name) {
		if(!in_array($name, $this->getRoleNames())) {
			throw new CakeException("Invalid parameter name: $name");
		}
	}


/**
 * Fetches the role name from the current model data, or attempts to look
 * it up in the database with the current model id.
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
