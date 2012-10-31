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
  * Used primarily in conjunction with the hash function.
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
}
