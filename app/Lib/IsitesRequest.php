<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('CakeRequest', 'Network');
App::uses('MissingIsitesKeyException', 'Lib');
App::uses('IsitesRequestUtil', 'Vendor/Isitestool');
App::uses('IsitesUser', 'Vendor/Isitestool');

/**
 * A class that helps parse iSites Request information. Delegates to a utility
 * library.
 */
class IsitesRequest extends CakeRequest {
	
	/**
	 * Object used to inspect iSites request parameters.
	 *
	 * @var object contains instance of IsitesRequestUtil
	 */
	public $isites;
	
	/**
	 * Name of the config entry that holds the producer key. This is used to 
	 * encrypt/decrypt some iSite request information.
	 */
	public static $producerConfig = 'Isites.producerKey';
	
	/**
	 * Constructor
	 * 
	 * Extending to modify the base url after parsing the request.
	 */
	public function __construct($url = null, $parseEnvironment = true) {
		parent::__construct($url, $parseEnvironment);
		$this->isites = new IsitesRequestUtil($this->getProducerKey(), $this->query);
	}

	/**
	 * Gets the producer key used for encrypting/decrypting the user ID 
	 *
	 * @return string
	 */
	public function getProducerKey() {
		$producerConfig = self::$producerConfig;
		$result = Configure::read($producerConfig);
		if(!isset($result)) {
			throw new MissingIsitesKeyException("$producerConfig not configured");
		}
		return $result;
	}

	/**
	 * Get the iSites user.
	 *
	 * @return IsitesUser
	 */
	public function getIsitesUser() {
		return $this->isites->getUser();
	}
	
	/**
	 * Get the client's IP address, as reported by iSites.
	 */
	public function clientIp() {
		return $this->isites->getParam('remoteAddr');
	}
}
