<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('String', 'Utility');

/**
 * TagCollection Model
 *
 * @package       app.Model
 */
class TagCollection extends AppModel {

/**
 * Constant for the algorithm used to calculate the tag collection hash.
 */
	const HASH_ALGORITHM = 'sha1';
	
/**
 * Constant for tag list separator.
 */
	const TAG_SEPARATOR = ',';

/**
 * One-to-many associations
 *
 * @var array
 */
	public $hasMany = array('TagCollectionTag');

/**
 * Validation rules.
 *
 * @var array
 */
	public $validate = array();

/**
 * Loads a tag collection by id.
 *
 * @param integer $tag_collection_id
 * @return array
 */
	public function loadTagCollection($tag_collection_id) {
		$options = array(
			'conditions' => array(
				"{$this->alias}.id" => $tag_collection_id
			)
		);

		return $this->find('first', $options);
	}
	
/**
 * Tests if a tag collection exists for a set of tags.
 *
 * @param mixed $tags comma-separated string or array of tags
 * @return boolean true if it exists, false otherwise
 */
	public function existsTagCollection($tags = array()) {
		$count = 0;
		$hash = $this->hashOf($tags);
		if($hash !== false) {
			$count = $this->find('count', array(
				'recursive' => -1,
				'conditions' => array("{$this->alias}.hash" => $hash)
			));
		}

		return $count > 0;
	}

/**
 * Saves a string of tags and returns the tag collection ID.
 *
 * @param mixed $tags comma-separated string or array of tags
 * @return integer the tag_collection_id
 */
	public function saveTags($tags = array()) {
		if(is_string($tags)) {
			$tags = $this->parseTags($tags);
		}

		$tag_collection_id = false;
		if($this->existsTagCollection($tags)) {
			$tag_collection_id = $this->findTagCollectionIdByTags($tags);
		}

		if($tag_collection_id === false) {
			$tag_collection_id = $this->createTagCollection($tags);
		}
		
		return $tag_collection_id;
	}

/**
 * Creates a new tag collection.
 *
 * @param mixed $tags comma-separated string or array of tags
 * @return integer the tag_collection_id
 */
	public function createTagCollection($tags = array()) {
		if(is_string($tags)) {
			$tags = $this->parseTags($tags);
		}

		$hash = $this->hashOf($tags);
		if($hash === false) {
			error_log("Unable to get hash value of tags when creating new tag collection. Tags: ".var_export($tags,1));
			return false;
		}

		$result = $this->save(array('hash' => $hash));
		if($result === false) {
			error_log("Error creating new tag collection with tags: ".var_export($tags,1));
			return false;
		}
		
		$tag_collection_id = $this->id;
		$tag_ids = array();
		foreach($tags as $tag) {
			$this->TagCollectionTag->Tag->create();
			$tag_result = $this->TagCollectionTag->Tag->save(array('name' => $tag));
			if($tag_result === false) {
				error_log("Error creating tag: $tag");
			} else {
				$tag_ids[] = $this->TagCollectionTag->Tag->id;
			}
		}
		
		$tag_collection_tags = array();
		foreach($tag_ids as $tag_id) {
			$tag_collection_tags[] = array(
				'tag_collection_id' => $tag_collection_id,
				'tag_id' => $tag_id,
			);
		}
		
		$tag_collection_tag_result = $this->TagCollectionTag->saveMany($tag_collection_tags);
		if($tag_collection_tag_result === false) {
			error_log("Error saving tag collection tags mapping: ".var_export($tag_collection_tags,1));
		}
		
		return $tag_collection_id;
	}

/**
 * Decrements instance count.
 * 
 * @param number $tag_collection_id
 * @return boolean true on update, false otherwise
 */
	public function decrementInstances($tag_collection_id) {
		if(!empty($tag_collection_id)) {
			$fields = array('instance_count' => 'instance_count - 1');
			$conditions = array("{$this->alias}.id" => $tag_collection_id);
			return $this->updateAll($fields, $conditions);
		}
		return false;
	}
	
/**
 * Increments instance count.
 * 
 * @param number $tag_collection_id
 * @return boolean true on update, false otherwise
 */
	public function incrementInstances($tag_collection_id) {
		if(!empty($tag_collection_id)) {
			$fields = array('instance_count' => 'instance_count + 1');
			$conditions = array("{$this->alias}.id" => $tag_collection_id);
			return $this->updateAll($fields, $conditions);
		}
		return false;
	}

/**
 * Fetches the tag collection ID for a set of tags (assuming it exists).
 *
 * @param mixed $tags comma-separated string or array of tags
 * @return mixed the ID if it was found, otherwise false
 */
	public function findTagCollectionIdByTags($tags = array()) {
		$hash = $this->hashOf($tags);
		if(!$hash) {
			return false;
		}

		$result = $this->find('first', array(
			'fields' => array("{$this->alias}.id"),
			'recursive' => -1,
			'conditions' => array("{$this->alias}.hash" => $hash)
		));

		if(!$result) {
			return false;
		}

		return $result[$this->alias]['id'];
	}

/**
 * Parses a comma-separated string of tags.
 *
 * @param string $tag_str a comma-separated list of tags
 * @return array of tags
 */
 	public function parseTags($tag_str = '') {
 		$tags = array();
 		$items = explode(self::TAG_SEPARATOR, $tag_str);
 		
 		foreach($items as $item) {
 			$tag = preg_replace( "/(^\s+)|(\s+$)/us", "", $item);
 			$is_valid_trimmed_tag = ($tag !== null && $tag !== '');
 			if($is_valid_trimmed_tag) {
 				$tags[] = $tag;
 			}
 		}
 
 		return $tags;
 	}

/**
 * Eliminates duplicates from a list of tags.
 *
 * @param array $tags an array of tags
 * @return array of tags with duplicates removed
 */
	public function uniqueTags($tags = array()) {
		$seen = array();
		$unique = array();

		foreach($tags as $tag) {
			if(!isset($seen[$tag])) {
				$seen[$tag] = true;
				$unique[] = $tag;
			}
		}

		return $unique;
	}

/**
 * Formats a list of tags as a string.
 *
 * @param array $tags a list of tags
 * @return string of tags
 */
 	public function stringify($tags = array()) {
 		return implode(self::TAG_SEPARATOR, $tags);
 	}
 	
/**
 * Formats a list of tags as a string in canonical order.
 * 
 * Note: used in conjunction with the tag hashing method.
 *
 * @param array $tags a list of tags
 * @returns string of tags in canonical order
 */
	public function stringifyCanonical($tags = array()) {
		$sorted_tags = $this->sortTags($tags);
		return $this->stringify($sorted_tags);
	}

/**
 * Sort a list of tags.
 *
 * @param array $tags a list of tags
 * @return array of sorted tags
 */
 	public function sortTags($tags = array()) {
 		// will sort according to the current locale (see setlocale())
 		usort($tags, 'strcoll'); // strcoll equivalent to strcmp() if current locale is C or POSIX
 		return $tags;
 	}

/**
 * Calculates the hash value of a list of tags.
 *
 * @param string $tags a list of tags
 * @return false if no tags to hash, otherwise the value of the hash() function
 */
	public function hashOf($tags = array()) {
		if(is_string($tags)) {
			$tags = $this->parseTags($tags);
		}
		$tag_str = $this->stringifyCanonical($tags);
		if($tag_str === '') {
			return false;
		}

		return hash(self::HASH_ALGORITHM, $tag_str, FALSE);
	}
	
/**
 * Fetches a list of all the tag collection IDs
 *
 * @return array of ids
 */
	public function findAllTagCollectionIds() {
		$list = $this->find('list', array(
			'field' => array("{$this->alias}.id"),
			'recursive' => -1
		));

		return array_values($list);
	}
}
