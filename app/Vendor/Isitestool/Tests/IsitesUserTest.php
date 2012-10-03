<?php
require_once(dirname(__FILE__) .'/../IsitesUser.php');

/**
 * IsitesUserTest
 *
 * @package app.Vendor.Isitestool.Test
 */
class IsitesUserTest extends PHPUnit_Framework_TestCase {
		
	/**
	 * PHPUnit's setUp
	 */
	public function setUp() {
		parent::setUp();
	}

	/**
	 * tests attributes
	 */
	public function testAttributes() {
		$user = new IsitesUser('12345678');
		$this->assertClassHasAttribute('id', get_class($user));
		$this->assertClassHasAttribute('role', get_class($user));
		$this->assertClassHasAttribute('permissions', get_class($user));
	}
	
	/**
	 * tests getId returns the right value
	 */
	public function testGetId() {
		$id = '12345678';
		$user = new IsitesUser($id);
		$this->assertTrue(method_exists($user, 'getId'), 'has getId() method');
		$this->assertEquals($user->getId(), $id, 'ids match');
	}
	
	/**
	 * tests permissions
	 */
	public function testPermissions() {
		$this->assertTrue(method_exists(new IsitesUser(1), 'getPermissions'), 'has getPermissions() method');
		
		$permission_tests = array(
			array('arg' => array(7,8,9), 'result' => array(7,8,9)),
			array('arg' => '7,8,9', 'result' => array(7,8,9)),
			array('arg' => '7', 'result' => array(7)),
			array('arg' => null, 'result' => array())
		);
		
		foreach($permission_tests as $permissions) {
			$user = new IsitesUser(1, $permissions['arg']);
			$this->assertEquals($user->getPermissions(), $permissions['result'], 'permissions match');
		}
	}

	/**
	 * testIsSuper
	 */ 
	public function testIsSuper() {
		$user = new IsitesUser(1, range(7,21));
		$this->assertTrue($user->isSuper());
	}

	/**
	 * testIsAdmin
	 */ 
	public function testIsAdmin() {
		$user = new IsitesUser(1, range(7,16));
		$this->assertTrue($user->isAdmin());
		$this->assertFalse($user->isSuper());
	}

	/**
	 * testIsEnrollee
	 */ 
	public function testIsEnrollee() {
		$user = new IsitesUser(1, range(7,9));
		$this->assertTrue($user->isEnrollee());
		$this->assertFalse($user->isAdmin());
		$this->assertFalse($user->isSuper());
	}

	/**
	 * testIsGuest
	 */ 
	public function testIsGuest() {
		$user = new IsitesUser(1, array(7));
		$this->assertTrue($user->isGuest());
		$this->assertFalse($user->isEnrollee());
		$this->assertFalse($user->isAdmin());
		$this->assertFalse($user->isSuper());
	}
	
	/**
	 * testGetRole
	 */ 
	public function testGetRole() {
		$user = new IsitesUser(1, range(7,21));
		$role = $user->getRole();
		$this->assertTrue(!empty($role));
		
		$user = new IsitesUser(1);
		$role = $user->getRole();
		$this->assertFalse(isset($role));
	}
	
	/**
	 * testToString
	 */ 
	public function testToString() {
		$userid = '12345678';
		$user = new IsitesUser($userid, range(7,9));
		$this->assertEquals($user->toString(), sprintf("%s:%s", $userid, 'enrollee'));
	}
}
