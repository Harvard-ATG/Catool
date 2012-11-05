<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * Note Model
 *
 * @package       app.Model
 */
class Note extends AppModel {
	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Target' => array(
			'className' => 'Target',
			'foreignKey' => 'target_id',
			'counterCache' => true
		),
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id'
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Segment' => array(
			'className' => 'Segment',
			'foreignKey' => 'note_id'
		)
	);

/**
 * actsAs
 * @var array
 */
    public $actsAs = array('Tree', 'Taggable');
	
/**
 * Called after every save operation.
 * 
 * See Cake's afterSave callback
 * 
 * @param boolean $created
 * @return void
 */
	public function afterSave($created) {
		$this->afterSaveSetCommentTitle($created);
	}
	
/**
 * Sets the comment title after saving.
 *
 * Called from aftersave().
 *
 * @param boolean $created
 * @return void
 */
 	public function afterSaveSetCommentTitle($created) {
		if($created && $this->isComment()) {
			$this->saveField('title', $this->createCommentTitle());
		}
	}

/**
 * Get the root note of a discussion thread.
 * 
 * This is a convenience method to get the root element of a tree 
 * since it is not provided by Cake's TreeBehavior.
 * 
 * @param string $id model id
 * @return mixed
 */
	public function getRoot($id) {
		$parents = $this->getPath($id);
		return empty($parents) ? false : $parents[0];
	}
	
/**
 * Checks if the model is a comment.
 *
 * @param string $type defaults to Model::data type
 * @return boolean true if it is a comment, false otherwise
 */
	public function isComment($type = null) {
		if(!isset($type)) {
			$type = $this->data[$this->alias]['type'];
		}
		return $type === 'comment';
	}

/**
 * Checks if the model is an annotation.
 *
 * @param string $type defaults to Model::data type
 * @return boolean true if it is an annotation, false otherwise
 */
	public function isAnnotation($type = null) {
		if(!isset($type)) {
			$type = $this->data[$this->alias]['type'];
		}
		return $type === 'annotation';
	}

/**
 * Checks if a note is owned by a user.
 * 
 * @param integer $note_id
 * @param integer $user_id
 * @return boolean true if note is owned by user, false otherwise
 */
	public function isOwnedBy($note_id, $user_id) {
		$count = $this->find('count', array(
			'recursive' => -1,
			'conditions' => array(
				'Note.id' => $note_id,
				'Note.user_id' => $user_id
			)
		));
		
		return $count == 1;
	}
 
 /**
  * Automatically create a title for comments.
  *
  * This is for when comments are displayed outside of the annotation
  * interface.
  * 
  * @return string
  */
 	public  function createCommentTitle() {
		$root = $this->getRoot($this->id);
		if($root === false) {
			return '';
		}
		return __('RE: ') . $root[$this->alias]['title'];
 	}
 
/**
 * Find a list of notes in a target that match a query.
 * 
 * @param integer $target_id
 * @param string $query
 * @return array
 */
	public function findNotesByTarget($target_id, $query = '') {
		$query = trim($query);
		$is_query = mb_strlen($query) > 0;

		if(!$is_query) {
			return $this->find('all', array(
				'conditions' => array('Note.target_id' => $target_id),
				'recursive' => 1
			));
		}

		$matches = $this->find('all', array(
			'fields' => array('Note.id', 'Note.parent_id'),
			'conditions' => array(
				'Note.target_id' => $target_id,
				'OR' => Set::flatten(array(
					'Note' => array(
						'title LIKE' => "%$query%",
						'body LIKE' => "%$query%"
					)
				))
			),
			'recursive' => -1
		));

		$notes = $this->find('all', array(
			'fields' => array('Note.id', 'Note.parent_id'),
			'conditions' => array('Note.target_id' => $target_id),
			'recursive' => -1
		));

		$note_for = array();
		foreach($notes as &$item) {
			$note_for[ $item['Note']['id'] ] = $item;
		}

		// Every note that matched the query should be included
		// in the result as well as any notes on the path to the root.
		$output = array();
		foreach($matches as $match) {
			$id = $match['Note']['id'];
			$parent_id = $match['Note']['parent_id'];
			$output[$id] = true;

			while($parent_id) {
				$parent = $note_for[$parent_id];
				$output[ $parent['Note']['id'] ] = true;
				if(isset($parent['Note']['parent_id']) && $parent['Note']['parent_id'] < $parent_id) {
					$parent_id = $parent['Note']['parent_id'];
				} else {
					$parent_id = null;
				}
			}
		}

		$output_ids = array_keys($output);
		$result = $this->find('all', array(
			'conditions' => array('Note.id' => $output_ids),
			'recursive' => 1
		));


		return $result;
	}
}
