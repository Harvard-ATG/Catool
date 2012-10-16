<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * User Model
 *
 * @package       app.Model
 * @property UserCollection $UserCollection
 * @property Note $Note
 */
class User extends AppModel {

/**
 * name
 *
 * @var string
 */
	public $name = 'User';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Role' => array(
			'className' => 'Role',
			'foreignKey' => 'role_id'
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'UserCollection' => array(
			'className' => 'UserCollection',
			'foreignKey' => 'user_id'
		),
		'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'user_id',
			'dependent' => false
		),
	);
	

/**
 * actsAs
 *
 * used by Cake's AclBehavior
 *
 * @var array
 */
	public $actsAs = array(
		'Acl' => array('type' => 'requester'),
		'RBACL'
	);
	
/**
 * Find the parent node in the ACL tree structure for User models.
 *
 * Since this model acts as a requester, the parent node will be the "users"
 * node in the AROs tree (alias since it doesn't correspond to any particular
 * model). The "users" node should already be setup to grant access to the 
 * "user" role in the ACOs tree.
 *
 * Note: this is used with Cake's AclBehavior
 *
 * @return mixed
 */
	public function parentNode() {
		return 'users';
	}

/**
 * Returns a list of possible roles that a person can have in the site.
 *
 * @return array 
 */
	public function getRoleTypes() {
		$conditions = array('Role.name' => array(Role::SUPER, Role::ADMIN, Role::USER));
		$result = $this->Role->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions
		));

		return $result;
	}

/**
 * Finds all users.
 *
 * @return array
 */
	public function findAllUsers() {
		return $this->find('all', array('recursive' => 0));
	}

/**
 * Find all admin users.
 * 
 * Note: users who have the admin role should automatically be granted
 * admin privileges to all collections.
 * 
 * @param $collection_id
 * @return array 
 */
	public function findAdminUserIds() {
		$result =  $this->find('all', array(
			'conditions' => array('User.role_id' => $this->Role->getAdminRoleIds()),
			'recursive' => -1
		));

		if(!$result) {
			return array();
		}

		return Set::classicExtract($result, '{n}.User.id');
	}

/**
 * Finds all users and returns them keyed by their user ID.
 *
 * @return array keyed by user id
 */
	public function findAllUsersIndexedById() {
		$users = $this->findAllUsers();

		$users_by_id = array();
		foreach($users as $row) {
			$id = $row[$this->alias]['id'];
			$users_by_id[$id] = $row;
		}

		return $users_by_id;
	}

/**
 * Returns the default role of a user.
 *
 * @return Role model
 */
 	public function getDefaultRole() {
		return $this->Role->getDefaultRole();
	}

/**
 * Promotes user to super admin.
 *
 * @param $user_id
 * @return void
 */
	public function promoteToSuper($user_id) {
		$this->id = $user_id;
		$this->set('role_id', $this->Role->getRoleIdByName(Role::SUPER));
		$this->save();
	}
	
}
