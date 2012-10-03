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
 * Target type.
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
 * @param boolean $created
 */
	public function afterSave($created) {
		// new items should be placed at the end of the collection
		if($created && isset($this->data[$this->alias]['collection_id'])) {
			$sort_order = $this->getNextSortOrder($this->data[$this->alias]['collection_id']);
			$this->saveField('sort_order', $sort_order);
		}		
	}

/**                                                                                   
 * Returns the information about the current position of the target in a collection   
 * including the next and previous targets in the collection.                         
 *
 * Note: reads all targets into memory and does a brute-force linear search
 * to keep things simple, since we don't expect a collection to have more than 
 * a hundred or so items.
 *                                                                                    
 * @param integer $target_id                                                          
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
