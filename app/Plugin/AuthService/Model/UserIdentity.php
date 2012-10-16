<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AuthServiceAppModel', 'AuthService.Model');
/**
 * UserIdentity Model
 *
 * Used to link an external identity with a user.
 */
class UserIdentity extends AuthServiceAppModel {
	/**
	 * Table to use.
	 * 
	 * @var string
	 */
	public $useTable = 'user_identities';
	
	/**
	 * Type of identity, or the domain of this model.
	 * 
	 * @var string
	 */
	public $identityType = 'default';

	/**
	 * BelongsTo associations
	 * 
	 * @var string
	 */
    public $belongsTo = array(
        'User' => array(
            'className' => 'User',
            'foreignKey' => 'user_id'
        )
    );
	
	/**
	 * Called before each save operation, after validation.
	 * 
	 * @param array $options
	 * @return boolean True if the operation should continue, false if it should abort
	 */
	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		$this->data[$this->alias]['type'] = $this->identityType;
		return true;
	}
	
	/**
	 * Called before each find operation.
	 * 
	 * @param array $queryData
	 * @return mixed true if the operation should continue, false if it should abort; or, modified
	 *  $queryData to continue with new $queryData
	 */
	public function beforeFind($queryData) {
		$queryData['conditions'][] = array("{$this->alias}.type" => $this->identityType);
		return $queryData;
	}

	/**
	 * Returns true if the claimed ID exists, false otherwise.
	 *
	 * @param string $claimed_id 
	 * @return boolean
	 */
	public function existsUser($claimed_id) {
		return $this->findClaimedId('count', $claimed_id) > 0;
	}

	/**
	 * Loads a single user identity.
	 *
	 * @param string $claimed_id
	 * @return mixed
	 */
	public function loadUser($claimed_id) {
		return $this->findClaimedId('first', $claimed_id);
	}

	/**
	 * Registers a new user and links them to their claimed ID.
	 *
	 * @param string $claimed_id
	 * @param array $user_attributes
	 * @return void
	 */
	public function registerUser($claimed_id, $user_attributes = array()) {
		$this->User->create();
		$this->User->set($user_attributes);
		$this->User->save();

		$this->create();
		$this->set('claimed_id', $claimed_id);
		$this->set('user_id', $this->User->id);
		$this->save();
	}

	/**
	 * Wrapper for finding a claimed ID.
	 * 
	 * @param string $type Type of find (i.e. 'all', 'count', etc)
	 * @param string $claimed_id a claimed ID.
	 * @return mixed result of the find() operation
	 */
	public function findClaimedId($type, $claimed_id) {
		return $this->find($type, array('conditions' => array(
			"{$this->alias}.claimed_id" => $claimed_id
		)));
	}
}
