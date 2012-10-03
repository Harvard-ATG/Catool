<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('IsitesRequest', 'Lib');

/**
 * A class that helps parse iSites Request information. Delegates to a utility
 * library.
 */
class IsitesRequestTest extends CakeTestCase {
	
/**
 * test that the producer key is not found
 *
 * @return void
 */
	public function testMissingProducerKey() {
		Configure::write(IsitesRequest::$producerConfig, null);
		$this->setExpectedException('MissingIsitesKeyException');
		$request = new IsitesRequest(null, false);
	}
	
/**
 * test that the request has a delegate
 *
 * @return void
 */
	public function testHasRequestUtil() {
		Configure::write(IsitesRequest::$producerConfig, 'foo');
		$request = new IsitesRequest(null, false);
		$this->assertInstanceOf('IsitesRequestUtil', $request->isites);
	}
}
