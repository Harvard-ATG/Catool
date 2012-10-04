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
 * Model name.
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
 * Finds the parent node in the ACL structure for this model. 
 *
 * Since this model acts as a requester, the parent node will be the User 
 * model in the AROs tree. This is so that user permissions can cascade down
 * to collection permissions. For example, if a user is an unprivileged member
 * of a collection and they are subsequently made a site administrator, they
 * will automatically inherit that admin privilege in all of their collections.
 * If necessary, this can be overridden on a per-collection basis by explicitly
 * denying access.
 *
 * Note: it assumes that the the User model already exists in the ARO tree. If
 * that is not the case, then the parent node will be the root of the ARO tree.
 *
 * @return array
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
 * Finds all the members of a collection.
 * 
 * @param number $collection_id
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
 * Finds all members of a collection keyed by ID.
 *
 * Note: the ID is the UserCollection.id, _not_ User.id.
 * 
 * @param number $collection_id
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
 * Find a user's membership record for a collection.
 *
 * @param number $user_id
 * @param number $collection_id
 * @return array if they are a member, false otherwise
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
 * Check if the user has admin permissions.
 *
 * Note: if no collection ID is passed, it will return true if the user has 
 * admin permission in _any_ collection, false otherwise.  
 *
 * @param number $user_id
 * @param number $collection_id
 * @return boolean 
 */
	public function isAdmin($user_id, $collection_id = null) {
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
 * Find collection(s) that a user can administer.
 *
 * Finds a list of collections that the user has the ability to make changes
 * (i.e. admin permission).
 *
 * @param number $user_id 
 * @param number $collection_id optional
 * @return array of collection IDs or false  
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
 * findFromTarget method
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
 * @param string $email 
 * @param number $collection_id
 * @return array
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
 * getRoleTypes method
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
