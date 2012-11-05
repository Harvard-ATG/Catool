<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * NoteFixture
 *
 */
class NoteFixture extends CakeTestFixture {

/**
 * Import table definition
 *
 * @var array
 */
	public $import = 'Note';
	
/**
 * Records
 *
 * @var array
 */
	public $records = array(
		array(
			'id' => 1,
			'type' => 'annotation',
			'parent_id' => null,
			'tag_collection_id' => 2,
			'target_id' => 1,
			'user_id' => 1,
			'title' => 'Test Annotation #1',
			'body' => 'Lorem ipsum dolor...',
			'lft' => 1,
			'rght' => 6,
			'created' => '2012-03-09 16:45:05',
			'modified' => '2012-03-09 16:45:05',
			'deleted' => 1,
			'hidden' => 1
		),
		array(
			'id' => 2,
			'type' => 'comment',
			'parent_id' => 1,
			'tag_collection_id' => 3,
			'target_id' => 1,
			'user_id' => 1,
			'title' => 'RE: Test Annotation #1',
			'body' => 'In reply to lorem ipsum!',
			'lft' => 2,
			'rght' => 3,
			'created' => '2012-04-19 16:45:05',
			'modified' => '2012-04-19 16:45:05',
			'deleted' => 0,
			'hidden' => 0
		),
		array(
			'id' => 3,
			'type' => 'comment',
			'parent_id' => 1,
			'tag_collection_id' => 4,
			'target_id' => 1,
			'user_id' => 2,
			'title' => 'RE: Test Annotation #1',
			'body' => 'In reply to lorem ipsum by someone else!',
			'lft' => 4,
			'rght' => 5,
			'created' => '2012-04-19 16:45:05',
			'modified' => '2012-04-19 16:45:05',
			'deleted' => 0,
			'hidden' => 0
		)
	);
}
