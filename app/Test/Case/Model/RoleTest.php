<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('Role', 'Model');

/**
 * Role Test Case
 *
 * @package app.Test.Model
 */
class RoleTestCase extends CakeTestCase {
/**
 * Fixtures
 *
 * @var array
 */
	public $fixtures = array('app.role', 'app.user');

/**
 * setUp method
 *
 * @return void
 */
	public function setUp() {
		parent::setUp();
		$this->Role = ClassRegistry::init('Role');
	}

/**
 * testRoleNames
 * 
 * @return void
 */
	public function testRoleNames() {
		$expected = array(Role::SUPER, Role::ADMIN, Role::MOD, Role::USER, Role::GUEST);
		
		$result = $this->Role->find('list', array(
			'recursive' => -1,
			'fields' => array('Role.name'),
			'order' => 'Role.id'
		));
		$actual = array_values($result); // re-index from zero

		$this->assertEquals($expected, $actual, 'list of valid role names');
	}

/**
 * testGetAdminRoleIds
 *
 * @return array
 */
	public function testGetAdminRoleIds() {
		$expected = array(1,2);
		
		$result = $this->Role->getAdminRoleIds();
		$actual = array_values($result); // re-index from zero
		
		$this->assertEquals($expected, $actual, 'all roles that should have admin permissions');
	}
	
/**
 * testGetRoleIdByName
 *
 * @return void
 */
	 public function testGetRoleIdByName() {
	 	 $tests = array(
	 	 	 array('id' => 1, 'name' => Role::SUPER),
	 	 	 array('id' => 2, 'name' => Role::ADMIN),
	 	 	 array('id' => 3, 'name' => Role::MOD),
	 	 	 array('id' => 4, 'name' => Role::USER),
	 	 	 array('id' => 5, 'name' => Role::GUEST)
	 	 );
	 	 
	 	 foreach($tests as $test) {
	 	 	 $expected = $test['id'];
	 	 	 $actual = $this->Role->getRoleIdByName($test['name']);
	 	 	 $this->assertEquals($expected, $actual, 'get correct id for role name');
	 	 }
	 }

	 
/**
 * testGetPathToRole
 *
 * @return void
 */
	 public function testGetPathToRole() {
	 	 $expected = array(Role::SUPER, Role::ADMIN, Role::MOD, Role::USER);
	 	 
	 	 $result = $this->Role->getPathToRole(Role::USER);
	 	 $actual = Set::classicExtract($result, '{n}.Role.name');
	 	 
	 	 $this->assertEquals($expected, $actual, 'path to user role should go super->admin->mod->user');
	 }

/**
 * testInvalidRoleNameOnSave
 *
 * @return void
 */
 	public function testInvalidRoleNameOnSave() {
 		$this->setExpectedException('CakeException');
 		$this->Role->save(array('name' => 'FOO_BAR_ROLE'));
 	}
 	
 /**
 * testValidRoleNameOnSave
 *
 * @return void
 */
 	public function testValidRoleNameOnSave() {
 		$names = $this->Role->getRoleNames();
 		foreach($names as $name) {
 			$saved = (bool) $this->Role->save(array('name' => $name, 'display_description' => 'We <3 '.$name));
 			$this->assertTrue($saved, "Saved role with name: $name");
 		}
 	}

/**
 * tearDown method
 *
 * @return void
 */
	public function tearDown() {
		unset($this->Role);

		parent::tearDown();
	}

}
