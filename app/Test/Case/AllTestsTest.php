<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * AllTest
 *
 * This is Cake's way of building test suites
 *
 * It is equivalent to: <br>
 * <testsuites> <br>
 *  <testsuite name="My Test Suite"> <br>
 *     <directory>/path/to/*Test.php files</directory> <br>
 *     <file>/path/to/MyTest.php</file> <br>
 *     <exclude>/path/to/exclude</exclude> <br>
 *   </testsuite> <br>
 * </testsuites>
 *
 * @package app.Test
 */
class AllTest extends CakeTestSuite {

	/**
	 * Test suite to run everything
	 */
    public static function suite() {
        $suite = new CakeTestSuite('All test cases');
        $suite->addTestDirectoryRecursive(TESTS . 'Case');
		
		// test plugins that are part of the app
		foreach(array('AuthService') as $plugin) {
			$suite->addTestDirectoryRecursive(APP . 'Plugin' . DS . $plugin . DS . 'Test' . DS . 'Case');
		}

        return $suite;
    }
}
