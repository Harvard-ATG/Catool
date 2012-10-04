<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Collection Model
 *
 * @package       app.Model
 * @property Target $Target
 */
class Collection extends AppModel {
	

/**
 * Validation rules

 * @var array
 */
	public $validate = array(
		'display_name' => array(
			'rule' => array('maxlength', 1000),
			'allowEmpty' => false
		),
		'display_description' => array(
			'maxlength' => array(
				'rule' => array('maxlength', 4000)
			)
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'collection_id',
			'counterCache' => true
		),
		'UserCollection' => array(
			'className' => 'UserCollection',
			'foreignKey' => 'collection_id'
		)
	);

/**
 * Finds all targets that match a query.
 *
 * @param string $query
 * @param integer $collection_id
 * @return array
 */
	public function findTargetsWith($query = '', $collection_id = null) {
		$conditions = array();
		$conditions['Target.collection_id'] = $collection_id;
		
		$query = trim($query);
		if(mb_strlen($query) > 0) {
			$conditions['OR'] = Set::flatten(array(
				'Target' => array(
					'display_name LIKE' => "%$query%",
					'display_description LIKE' => "%$query%"
				)
			));
		}

		$result = $this->Target->find('all', array(
			'conditions' => $conditions,
			'recursive' => -1
		));

		return $result;
	}
	
/**
 * Finds and aggregates a list of users who have created annotations or comments.
 * 
 * @param mixed $collection_id
 * @return array
 */
	public function findUsersWithPosts($collection_id = null) {
		$conditions = array();
		if(isset($collection_id)) {
			$conditions['Target.collection_id'] = $collection_id;
		}
		
		// remove segments from the result
		$this->Target->Note->unbindModel(array('hasMany' => array('Segment')));
		
		$result = $this->Target->Note->find('all', array(
			'recursive' => 2,
			'conditions' => $conditions,
			'fields' => array(
				'User.id',
				'User.name',
				'Target.collection_id',
				'COUNT(Note.id) AS num_notes',
				'MAX(Note.created) AS last_post_date'
			),
			'order' => 'User.name',
			'group' => 'User.id, User.name, Target.collection_id'
		));
		
		
		$users = array();
		$total_notes = 0;
		$max_post_date = '';
		$max_post_date_unix = null;
		
		foreach($result as $row) {
			$num_notes = intval($row[0]['num_notes']);
			$last_post_date = $row[0]['last_post_date'];
			
			$user = $row['User'];
			$user['num_notes'] = $num_notes;
			$user['last_post_date'] = $last_post_date;
			$users[] = $user;
			
			$total_notes += $num_notes;
			
			$last_post_date_unix = strtotime($last_post_date);
			if(empty($max_post_date) || $last_post_date_unix > $max_post_date_unix) {
				$max_post_date = $last_post_date;
				$max_post_date_unix = $last_post_date_unix;
			}
			
		}
		
		$data = array(
			'total_notes' => $total_notes,
			'max_post_date' => $max_post_date,
			'users' => $users
		);
		
		return $data;
	}
 
 /**
  * Finds a list of notes filtered by collection and/or user.
  * 
  * @param $collection_id
  * @param $user_id
  * @return array
  */
 	public function findPostsByCollection($collection_id = null, $user_id = null) {
		$conditions = array();
		if(isset($collection_id)) {
			$conditions['Target.collection_id'] = $collection_id;
		}
		if(isset($user_id)) {
			$conditions['Note.user_id'] = $user_id;
		}
		
		// remove segments from the result
		$this->Target->Note->unbindModel(array('hasMany' => array('Segment')));
		
		$result = $this->Target->Note->find('all', array(
			'recursive' => 2,
			'conditions' => $conditions,
			'fields' => array(
				'Target.id',
				'Target.type',
				'Target.collection_id',
				'Target.display_name',
				'Note.id',
				'Note.title',
				'Note.created',
				'Note.type',
				'User.id',
				'User.name'
			),
			'order' => 'Note.created DESC'
		));
		
		// map collection.id => collection
		$collection_for = $this->getCollectionsIndexedById($collection_id);
		
		$notes = array();
		foreach($result as $row) {
			$collection_id = $row['Target']['collection_id'];
			$row['Collection'] = $collection_for[$collection_id];
			$notes[] = $row;
		}

		return $notes;
 	}

/**
 * Returns a list of collections keyed by the Collection ID.
 * 
 * @param integer $id collection id
 * @return array
 */
	public function getCollectionsIndexedById($id = null) {
		$conditions = array();
		if(isset($id)) {
			$conditions['Collection.id'] = $id;
		}
		
		$collections = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions
		));
		
		// map collection.id => collection data
		$collection_for = Set::combine($collections, '{n}.Collection.id', '{n}.Collection');
		
		return $collection_for;
	}

/**
 * Returns aggregate information about each target in a collection.
 *
 * This includes things like the last post date for a target, the total number
 * of posts, etc.
 *
 * @param integer $id the collection ID
 * @return array keyed by target ID
 */
	public function getTargetStats($id = null) {
		$db = $this->Target->getDataSource();

		$result = $this->Target->find('all', array(
			'fields' => array(
				'Target.id', 
				'MAX(Note.created) AS note_last_post', 
				'COUNT(Note.id) AS note_count'),
			'group' => array('Target.id'),
			'joins' => array(
				array('table' => $db->fullTableName('notes'),
					'alias' => 'Note',
					'type' => 'LEFT',
					'conditions' => array('Target.id = Note.target_id')
				)
			),
			'conditions' => array('Target.collection_id' => $id),
			'recursive' => -1
		));

		$note_stats_for = array();
		foreach($result as $row) {
			$target_id = $row['Target']['id'];
			$note_stats_for[$target_id] = $row[0];
		}

		return $note_stats_for;
	}

/**
 * Returns a list of collections of which the user is a member.
 *
 * @param $user_id 
 * @return array of collections
 */
	public function findUserCollections($user_id) {
		$collections = $this->UserCollection->find('all', array(
			'fields' => array('DISTINCT collection_id'),
			'conditions' => array('UserCollection.user_id' => $user_id),
			'recursive' => -1
		));

		$has_all_collections = count($collections) === 1 && $collections[0]['UserCollection']['collection_id'] < 0;

		$conditions = array();
		if(!$has_all_collections) {
			$conditions[$this->alias.'.id'] = Set::classicExtract($collections, '{n}.UserCollection.collection_id');
		}

		$result = $this->find('all', array(
			'recursive' => -1,
			'conditions' => $conditions
		));

		return $result;
	}

/**
 * Returns a list of collections in which the user has admin privileges.
 *
 * @param $user_id 
 * @return array of collections
 */
	public function findUserAdminCollections($user_id) {
		$collection_ids = $this->UserCollection->findCollectionsWithAdminRole($user_id);

		$result = $this->find('all', array(
			'recursive' => -1,
			'conditions' => array('Collection.id' => $collection_ids)
		));

		return $result;
	}
}
