<?php

require_once(dirname(__FILE__) .'/../IsitesRequestUtil.php');

/**
 * IsitesRequestUtilTest
 *
 * @package app.Vendor.Isitestool.Test
 */
class IsitesRequestUtilTest extends PHPUnit_Framework_TestCase {
	
	/**
	 * encryptionKey
	 * @var string
	 */
	public $encryptionKey = 'Loremipsumdolorsitametconsecteturadipiscingelit';
	
	/**
	 * generate encrypted user id for testing purposes
	 * @param integer $user_id
	 * @return string
	 */
	public function generateEncryptedUserId($user_id) {
		$rc4 = new Crypt_RC4();
		$rc4->setKey($this->encryptionKey);

		$token = "{$user_id}|".strtotime('1 January 2000');
		$encrypted_token = $rc4->encrypt($token);
		$encrypted_token = implode('', unpack('H*', $encrypted_token));

		return $encrypted_token;
	}
	
	/**
	 * generates testing params
	 * @param integer $userid
	 * @param array $permissions
	 * @param string $userinclassroom
	 * @param string $state
	 * @return array params
	 */
	public function generateParams($userid = '12345678', $permissions = null, $userinclassroom = 'yes', $state = 'maximize') {
		return array(
			'context' => 'OK',
			'userid' => $this->generateEncryptedUserId($userid),
			'keyword' => 'k69009',
			'siteId' => 'icb.site68641',
			'siteType' => '10',
			'topicId' => 'icb.topic1023386',
			'coreToolId' => '13548',
			'urlRoot' => 'isites.localhost',
			'remoteAddr' => '140.247.29.247',
			'permissions' => urlencode(implode(',', isset($permissions) ? $permissions : range(7,20))),
			'pageContentId' => 'icb.pagecontent1009147',
			'pageid' => 'icb.page476061',
			'icb_userinclassroom' => isset($userinclassroom) ? $userinclassroom : '',
			'state' => isset($state) ? $state : ''
		);
	}
	
	/**
	 * tests to make sure the following methods exist: 
	 * getBaseUrl, getUser, getParam, setParam
	 */
	public function testMethodsExist() {
		$p = new IsitesRequestUtil($this->encryptionKey);
		$methods = array(
			'getBaseUrl',
			'getUser',
			'getParam',
			'setParam'
		);
		
		foreach($methods as $method) {
			$this->assertTrue(method_exists($p, $method), "check method exists: {$method}()");
		}
	}	

	/**
	 * tests that get user gets the appropriate value
	 */
	public function testGetUser() {
		$p = new IsitesRequestUtil();
		$this->assertInstanceOf('IsitesUser', $p->getUser());
	}

	/**
	 * tests the decrypting of the userid
	 */
	public function testUserIdDecrypted() {
		$id = '90386634';
		$p = new IsitesRequestUtil($this->encryptionKey, array(
			'userid' => $this->generateEncryptedUserId($id)
		));
				
		
		$this->assertEquals($p->getParam('userid'), $id);
	}
	
	/**
	 * tests the online classroom
	 */
	public function testOnlineClassroom() {
		$p = new IsitesRequestUtil($this->encryptionKey, $this->generateParams('12345678'));
		$this->assertTrue($p->isOnlineClassroom(), 'this is an online classroom');
		$this->assertTrue($p->userInOnlineClassroom(), 'user belongs to online classroom');
	}
	
	/**
	 * tests the params are set appropriately
	 */
	public function testParams() {
		$userid = '12345678';
		$params = $this->generateParams($userid);
		
		$p = new IsitesRequestUtil($this->encryptionKey, $params);
		$params['_userid'] = $params['userid'];
		$params['userid'] = $userid;
		
		$this->assertEquals($p->params, $params, 'check params match');
	}
	
	/**
	 * checks that invalid params fail
	 */
	public function testGetInvalidParam() {
		$p = new IsitesRequestUtil($this->encryptionKey, $this->generateParams('123'));
		$this->setExpectedException('InvalidIsitesParamException');
		$p->getParam('topicid'); // should be topicId
	}
	
	/**
	 * tests that the base url matches
	 */
	public function testBaseUrl() {
		$params = $this->generateParams('90347613');
		$p = new IsitesRequestUtil($this->encryptionKey, $params);
		$base_url = $p->getBaseUrl();
		$this->assertTrue(!empty($base_url));
		
		$base = parse_url($base_url);
		$this->assertEquals($base, array(
			'scheme' => 'http',
			'host' => 'isites.localhost',
			'path' => '/icb/icb.do',
			'query' => 'keyword=k69009&pageid=icb.page476061&pageContentId=icb.pagecontent1009147&state=maximize'
		));
	}

}
