<?php

require_once(dirname(__FILE__) .'/IsitesUser.php');
require_once(dirname(__FILE__) .'/../phpseclib/Crypt/RC4.php');

/**
 * Just a named extension of Exception
 * @package app.Vendor.Isitestool
 */
class InvalidIsitesParamException extends Exception {}

/**
 * IsitesRequestUtil
 *
 * @package app.Vendor.Isitestool
 */
class IsitesRequestUtil {
	
	/**
	 * params sent
	 * @var array
	 */
	public $params = array();

	/**
	 * The usual params sent by iSites
	 * @var array
	 */
	public static $validParams  = array(
		'context',
		'userid',
		'keyword',
		'siteId',
		'siteType',
		'topicId',
		'coreToolId',
		'urlRoot',
		'remoteAddr',
		'permissions',
		'pageContentId',
		'pageid',
		'state',
		'icb_userinclassroom'
	);
	
	/**
	 * IsitesUser
	 * @var object
	 */
	protected $user;

	/**
	 * encryptionKey
	 * @var string
	 */
	protected $encryptionKey = '';
	
	/**
	 * constructor
	 * @param string $encryptionKey
	 * @param array $params
	 */
	public function __construct($encryptionKey = '', $params = array()) {
		$this->encryptionKey = $encryptionKey;
		$this->_initParams($params);
		$this->_initUser();
	}

	/**
	 * sets the params
	 * @param array $params
	 */
	protected function _initParams($params) {
		foreach(self::$validParams as $param) {
			if(isset($params[$param])) {
				$this->params[$param] = $params[$param];
			}
		}
	}

	/**
	 * initialize user (IsitesUser)
	 *
	 * gets the user infor from the param userid and calls decryptUserId
	 */
	protected function _initUser() {
		$userid = $this->getParam('userid');
		if(isset($userid)) {
			$this->_decryptUserId($userid);
		}
		
		$this->user = new IsitesUser($this->getParam('userid'), $this->getParam('permissions'));
	}

	/**
	 * decrypts the userid given the encryptionKey
	 *
	 * sets the userid param
	 */
	protected function _decryptUserId() {
		if(!empty($this->encryptionKey)) {
			$rc4 = new Crypt_RC4();
			$rc4->setKey($this->encryptionKey);

			$ciphertext = $this->getParam('userid');
			$plaintext = $rc4->decrypt(pack('H*', $ciphertext));

			$parts = explode('|', $plaintext, 2);
			$userid = isset($parts[0]) ? $parts[0] : null;
			$timestamp = isset($parts[1]) ? $parts[1] : null;

			$this->setParam('_userid', $ciphertext);
			$this->setParam('userid', $userid);
		}
	}
	
	/**
	 * sets a param in $params
	 * @param string $name
	 * @param string $value
	 */
	protected function setParam($name, $value) {
		$this->params[$name] = $value;
	}

	/**
	 * gets a param from $params
	 * @param string $name
	 * @return string param value for the key $name
	 */
	public function getParam($name) {
		if(!in_array($name, self::$validParams)) {
			throw new InvalidIsitesParamException("Invalid isites param: $name");
		}
		return isset($this->params[$name]) ? $this->params[$name] : null;
	}
		
	/**
	 * returns the user object
	 */
	public function getUser() {
		return $this->user;
	}
	
	/**
	 * gets the base url
	 */
	public function getBaseUrl() {
		$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] ? "https" : "http";
		$base = $protocol .'://' .$this->getParam('urlRoot') .'/icb/icb.do';
		
		$url_query = array();
		foreach(array('keyword', 'pageid', 'pageContentId', 'state') as $query_param) {
			if(isset($this->params[$query_param])) {
				$url_query[$query_param] = $this->params[$query_param];
			}
		}
		
		$base_url = $base . '?' . http_build_query($url_query);
		
		return $base_url;
	}
	
	/**
	 * For information about the "online classroom", see:
	 * @link http://isites.harvard.edu/icb/icb.do?keyword=developer_help
	 */
	public function isOnlineClassroom() {
		$value = $this->getParam('icb_userinclassroom');
		return $value === 'yes' || $value === 'no';
	}
	
	/**
	 * userInOnlineClassroom
	 * @return boolean
	 */
	public function userInOnlineClassroom() {
		return $this->getParam('icb_userinclassroom') === 'yes';
	}
}

