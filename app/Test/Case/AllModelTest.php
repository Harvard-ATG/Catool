<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * AllModelTest
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
class AllModelTest extends CakeTestSuite {
	/**
	 * test suite getting everything in app/Test/Case/Model
	 */
    public static function suite() {
        $suite = new CakeTestSuite('All model tests');
        $suite->addTestDirectory(TESTS . 'Case' . DS . 'Model');
        return $suite;
    }
}
