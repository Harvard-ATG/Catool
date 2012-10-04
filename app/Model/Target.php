<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Target Model
 *
 * @package       app.Model
 */
class Target extends AppModel {
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Collection' => array(
			'className' => 'Collection',
			'foreignKey' => 'collection_id'
		),
		'Resource' => array(
			'className' => 'Resource',
			'foreignKey' => 'resource_id'
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Note' => array(
			'className' => 'Note',
			'foreignKey' => 'target_id',
			'counterCache' => true
		)
	);

/**
 * validate 
 * 
 * @var array
 */
	public $validate = array(
		'display_name' => array(
			'rule' => array('maxLength', 500),
			'allowEmpty' => false
		)
	);

/**
 * The target type.
 *
 * Subclasses should override this value with their target type, whether it
 * is video, audio, etc.
 *
 * @var string
 */
	public $targetType = null;  // subclass responsibility


/**
 * Called before each save operation, after validation.
 * 
 * @param array $options
 * @return boolean True if the operation should continue, false if it should abort
 */
	public function beforeSave($options = array()) {
		parent::beforeSave($options);
		if(isset($this->targetType)) {
			$this->data[$this->alias]['type'] = $this->targetType;
		}
		return true;
	}

/**
 * Called after each save operation.
 *
 * See Cake's afterSave callback.
 *
 * @param boolean $created
 * @return void
 */
	public function afterSave($created) {
		$this->afterSaveSetOrder($created);
	}
	
/**
 * Places a new item at the bottom of a collection.
 *
 * Called from the afterSave callback.
 *
 * @param boolean $created
 * @return void
 */
 	public function afterSaveSetOrder($created) {
		if($created && isset($this->data[$this->alias]['collection_id'])) {
			$sort_order = $this->getNextSortOrder($this->data[$this->alias]['collection_id']);
			$this->saveField('sort_order', $sort_order);
		}
 	}

/**                                                                                   
 * Returns data about the current position of a target in a collection.
 *
 * Given a list of ordered targets from left to right, this function returns
 * the immediate neighbors of a particular target. So it will return the 
 * "previous" and "next" targets in the sequence.
 *                                                                                    
 * @param integer $target_id
 * @param array $options an array with 
 *		- onlyVisible: if true, excludes hidden targets. Defaults to true.
 * @return array Array with keys for next and previous items in the collection.       
 */                                                                                   
    public function getNeighbors($target_id, $options = array()) {                  
		$alias = $this->alias;
		$neighbors = array('next' => null, 'prev' => null);
		$options = array_merge(array('onlyVisible' => true), $options);

		$current = $this->find('first', array(
			'conditions' => array("$alias.id" => $target_id),
			'recursive' => -1
		));

		if(empty($current)) {
			return $neighbors;
		}
		
		$conditions = array("$alias.collection_id" => $current[$alias]['collection_id']);
		if($options['onlyVisible']) {
			$conditions["$alias.hidden <>"] = 1;
		}

		$targets = $this->find('all', array(
			'fields' => array("$alias.id", "$alias.type"),
			'conditions' => $conditions,
			'order' => "$alias.sort_order",
			'recursive' => -1
		));

		if(empty($targets)) {
			return $neighbors;
		}

		$position = null;
		$total = count($targets);
		for($i = 0; $i < $total; $i++) {
			if($target_id == $targets[$i][$alias]['id']) {
				$position = $i;
				break;	
			}
		}

		if(isset($position)) {
			if(isset($targets[$position + 1])) {
				$neighbors['next'] = $targets[$position + 1][$alias];
			}
			if(isset($targets[$position - 1])) {
				$neighbors['prev'] = $targets[$position - 1][$alias];
			}
		}

		return $neighbors;
	}

/**
 * Returns the next sort order in a collection.
 * 
 * @param integer $collection_id
 * @return integer the next sort order
 */
	public function getNextSortOrder($collection_id) {
		$result = $this->find('first', array(
			'fields' => array('MAX(sort_order) AS max_order'),
			'conditions' => array($this->alias.'.collection_id' => $collection_id),
			'recursive' => -1,
		));
		
		$max = $result ? $result[0]['max_order'] : 0;
		
		return $max + 1;
	}                      
}
