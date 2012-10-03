<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
App::uses('Role', 'Model');
/**
 * UserCollection Model
 *
 * @package       app.Model
 * @property Role $Role
 * @property Note $Note
 */
class UserCollection extends AppModel {

/**
 * name
 *
 * @var string
 */
	public $name = 'UserCollection';

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		),
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'collection_id',
		),
		'Role' => array(
			'className' => 'Role',
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
		'Acl' => array('type' => 'requester'),
		'RBACL'
	);

/**
 * parentNode
 *
 * method used with Cake's AclBehavior
 */
	public function parentNode() {
		if(!isset($this->data[$this->alias]['user_id'])) {
			return null;
		}

		$parent = array(
			'model' => $this->User->alias,
			'foreign_key' => $this->data[$this->alias]['user_id']
		);

		return $parent;
	}

/**
 * isAdmin
 * 
 * This should probably be converted to use the Acl methods.
 * 
 * @param number $user_id
 * @return true if the user is in the "admins" or "superadmins" roles
 */
	public function isAdmin($user_id, $collection_id = null) {
		return $this->hasAdminRole($user_id, $collection_id);
	}

/**
 * findAllCollectionUsers
 * 
 * @param $collection_id
 * @return array
 */
	public function findAllCollectionUsers($collection_id = null) {
		$result = $this->find('all', array(
			'conditions' => array(
				"{$this->alias}.collection_id" => $collection_id
			),
			'recursive' => 0
		));
		
		return $result;
	}

/**
 * findAllCollectionUsersIndexedById
 * 
 * @param $collection_id
 * @return array 
 */
	public function findAllCollectionUsersIndexedById($collection_id = null) {
		$result = $this->findAllCollectionUsers($collection_id);
		
		$users = array();
		foreach($result as $row) {
			$id = $row[$this->alias]['id'];
			$users[$id] = $row;
		}
		
		return $users;
	}

/**
 * findFromCollection
 *
 * Locate a user's membership in a collection.
 *
 * @param number $user_id
 * @param number $collection_id
 * @return array containing the record if it exists, false otherwise
 */	
	public function findFromCollection($user_id, $collection_id) {
		$result = $this->find('first', array(
			'conditions' => array(
				"{$this->alias}.user_id" => $user_id,
				"{$this->alias}.collection_id" => $collection_id
			),
			'recursive' => -1
		));

		return $result;
	}

/**
 * hasAdminRole method
 *
 * @param $user_id
 * @param $collection_id
 * @return boolean true if has any admin roles, false otherwise
 */
	public function hasAdminRole($user_id, $collection_id = null) {
		$conditions = array(
			"Role.id" => $this->Role->getAdminRoleIds(),
			"{$this->alias}.user_id" => $user_id
		);

		if(isset($collection_id)) {
			$conditions["{$this->alias}.collection_id"] = $collection_id;
		}

		$count = $this->find('count', array(
			'conditions' => $conditions,
			'recursive' => 0 
		));

		return $count > 0;
	}

/**
 * findCollectionsWithAdminRole method
 *
 * @param $user_id 
 * @param $collection_id optional
 * @return mixed collection IDs with admin privilege, false otherwise 
 */
	public function findCollectionsWithAdminRole($user_id, $collection_id = null) {
		$conditions = array(
			"{$this->alias}.user_id" => $user_id,
			"Role.id" => $this->Role->getAdminRoleIds()
		);

		if(isset($collection_id)) {
			$conditions["{$this->alias}.collection_id"] = $collection_id;
		}

		$result = $this->find('all', array(
			'fields' => array('DISTINCT collection_id'),
			'conditions' => $conditions,
			'recursive' => 0
		));

		$collection_ids = Set::classicExtract($result, "{n}.{$this->alias}.collection_id");

		return $collection_ids;
	}

/**
 * findFromTarget
 *
 * Locate a user's membership in a collection from a target.
 *
 * @param number $user_id
 * @param number $collection_id
 * @return array containing the record if it exists, false otherwise
 */
	public function findFromTarget($user_id, $target_id) {
		$target = $this->Collection->Target->find('first', array(
			'fields' => array('Target.collection_id'),
			'recursive' => -1,
			'conditions' => array('Target.id' => $target_id)
		));
		if(empty($target)) {
			return false;
		}

		$collection_id = $target['Target']['collection_id'];

		$conditions = array(
			"{$this->alias}.user_id" => $user_id,
			"{$this->alias}.collection_id" => $collection_id
		);

		$result = $this->find('first', array(
			'conditions' => $conditions,
			'recursive' => -1
		));

		return $result;
	}

/**
 * findByEmailAndCollection
 *
 */
	public function findByEmailAndCollection($email, $collection_id) {
		$email = strtolower(trim($email));
		$results = $this->User->find('all', array(
			'conditions' => array('LOWER(User.email)' => $email),
			'recursive' => 1
		));

		if(empty($results)) {
			return array();
		}

		$data = array();
		foreach($results as $result) {
			$user_collection = array();
			if(!empty($result[$this->alias])) {
				foreach($result[$this->alias] as $row) {
					if($collection_id === $row['collection_id']) {
						$user_collection = $row;
						break;
					}
				}
			}
			$data[] = array(
				'User' => $result['User'], 
				"$this->alias" => $user_collection
			);
		}

		return $data;
	}

/**
 * getRoleTypes
 *
 * @return array of possible roles someone can have in a collection
 */
	public function getRoleTypes() {
		$conditions = array('Role.name' => array(Role::ADMIN, Role::MOD, Role::USER));
		$result = $this->Role->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions
		));

		return $result;
	}

/**
 * getDefaultRole method
 *
 * @return Role model
 */
 	public function getDefaultRole() {
		return $this->Role->getDefaultRole();
	}
}
