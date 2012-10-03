<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Note', 'Model');

/**
 * Note Test Case
 *
 * @package app.Test.Model
 */
class NoteTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.note', 'app.target', 'app.user', 'app.segment');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Note = ClassRegistry::init('Note');
	}

/**
 * testFindNotesByTarget method
 *
 * @return void
 */
	public function testFindNotesByTarget() {
		$target_id = 1;
		$result = $this->Note->findNotesByTarget($target_id);
		$this->assertEquals(3, count($result));

		$result = $this->Note->findNotesByTarget($target_id, 'in reply');
		$this->assertEquals(3, count($result));

		$result = $this->Note->findNotesByTarget($target_id, 'Test Annotation #1');
		$this->assertEquals(3, count($result));
		$this->assertEquals('Test Annotation #1', $result[0]['Note']['title']);
	}

/**
 * testIsOwnedBy method
 * 
 * @return void
 */
	public function testIsOwnedBy() {
		$this->assertTrue(method_exists($this->Note, 'isOwnedBy'), 'Note model should contain method isOwnedBy()');
		$this->assertTrue($this->Note->isOwnedBy(1,1), 'note 1 is owned by user 1');
		$this->assertTrue($this->Note->isOwnedBy(2,1), 'note 2 is owned by user 1');
		$this->assertFalse($this->Note->isOwnedBy(1,2), 'note 1 is not owned by user 2');
		$this->assertFalse($this->Note->isOwnedBy(2,2), 'note 2 is not owned by user 2');
		$this->assertFalse($this->Note->isOwnedBy(-1,1), 'user 1 does not own this non-existent note');
	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Note);

		parent::tearDown();
	}

}
