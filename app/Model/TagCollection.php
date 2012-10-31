<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * TagCollection Model
 *
 * @package       app.Model
 */
class TagCollection extends AppModel {

/**
 * Constant for the algorithm used to calculate the tag collection hash.
 */
	const HASH_ALGO = 'sha1';

/**
 * Calculates the hash value of a list of tags.
 *
 * @param string $input a comma-separated list of tags
 * @return hash value
 */
	public function hashOf($input = '') {
		return hash(self::HASH_ALGO, $input, FALSE);
	}
}
