<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('View', 'View');
App::uses('MissingIsitesKeyException', 'Lib');
App::uses('IsitesRequest', 'Lib');
App::uses('IsitesRequestUtil', 'Vendor/Isitestool');
App::uses('IsitesUser', 'Vendor/Isitestool');
App::uses('IsitesHtmlHelper', 'View/Helper');

/**
 * IsitesHtemlHelperTest
 * 
 * @package app.Test.View
 */
class IsitesHtmlHelperTest extends CakeTestCase {
	
	/**
	 * isiteState
	 *
	 * 'maximize', 'edit', 'popup', or the empty string
	 * @var string
	 */
	protected $isiteState = null; // 'maximize', 'edit', 'popup', or the empty string
	
	/**
	 * mock params
	 * @return array
	 */
	public function generateParams() {
		$mergeParams = array(
			'context' => 'OK',
			'userid' => '64c6c33eef5830ab4b4de1248ca0dbdb6e3d',
			'keyword' => 'k69009',
			'siteId' => 'icb.site68641',
			'siteType' => '10',
			'topicId' => 'icb.topic1023386',
			'coreToolId' => '13548',
			'urlRoot' => 'isites.localhost',
			'remoteAddr' => '140.247.29.247',
			'permissions' => '7%2C8%2C9%2C10%2C11%2C12%2C13%2C14%2C15%2C16%2C17%2C18%2C19%2C20',
			'pageContentId' => 'icb.pagecontent1009147',
			'pageid' => 'icb.page476061',
			'icb_userinclassroom' => 'yes'
		);
		if(isset($this->isiteState)) {
			$mergeParams['state'] = $this->isiteState;
		}
		return $mergeParams;
	}

	/**
	 * resets $this->isiteState
	 */
	public function setUp() {
		$this->isiteState = null;
	}

	/**
	 * helpFactory
	 * @return IsitesHtmlHelper
	 */
	public function helperFactory() {
		$_GET = $this->generateParams();
		$IsitesRequest = $this->getMock('IsitesRequest');
		$IsitesRequest->expects($this->any())
			->method('getProducerKey')
			->will($this->returnValue('secret'));
		$Controller = new Controller($IsitesRequest, new CakeResponse());
		$View = new View($Controller);
		return new IsitesHtmlHelper($View);
	}

	/**
	 * tests that the helperFactory creates a url that matches the form: 
	 * http://isites.localhost/ ... keyword=k1234 ... pageid=icb.page4567 ... pageContentId=icb.pagecontent7890
	 */
	public function testBaseUrl() {
		
		$html = $this->helperFactory();
		$url = $html->url();
		$this->assertRegExp('{^http://isites.localhost/}', $url);
		$this->assertRegExp('{keyword=k\d+}', $url);
		$this->assertRegExp('{pageid=icb.page\d+}', $url);
		$this->assertRegExp('{pageContentId=icb.pagecontent\d+}', $url);
	}
			
	/**
	 * tests to see that the isiteState matches with that the helperFactory makes for the url given that isiteState
	 * 
	 * possible states: 'edit', 'maximize', 'popup'
	 */
	public function testUrlWithStates() {	
		foreach(array('edit', 'maximize', 'popup') as $state) {
			$this->isiteState = $state;
			$html = $this->helperFactory();
			$this->assertRegExp('{state='.$this->isiteState.'}', $html->url());
		}
	}
	
	/**
	 * given state=popup, tests that the helperFactory creates a url that matches the form:
	 *
	 * ... /main/index?Larry=Bird&Best=Ever 
	 * ... keyword=k\d+  
	 * ... state=popup 
	 * ... topicid=icb.topic\d+ 
	 * ... view=main%2Findex 
	 * ... viewParam_Larry=Bird 
	 * ... viewParam_Best=Ever
	 */
	public function testUrlWithPopupState() {
		$this->isiteState = 'popup';
		$html = $this->helperFactory();
		$url = $html->url('/main/index?Larry=Bird&Best=Ever');
		$this->assertRegExp('{keyword=k\d+}', $url);
		$this->assertRegExp('{state=popup}', $url);
		$this->assertRegexp('{topicid=icb.topic\d+}', $url); // topicid, not topicId
		$this->assertRegexp('{view=main%2Findex}', $url);
		$this->assertRegexp('{viewParam_Larry=Bird}', $url);
		$this->assertRegexp('{viewParam_Best=Ever}', $url);
	}
	
	/**
	 * given states=array(null, 'edit', 'maximize'), tests that the helperFactory creates a url that matches the form: 
	 * 
	 * ^http://isites.localhost/ 
	 * ... /main/index?Larry=Bird&Best=Ever
	 * ... keyword=k\d+
	 * ... pageid=icb.page\d+
	 * ... pageContentId=icb.pagecontent\d+
	 * ... panel='icb.pagecontent\d+' . urlencode(":rmain/index?Larry=Bird&Best=Ever")'
	 */
	public function testUrlWithPanelParam() {
		$statesWithPanelParam = array(null, 'edit', 'maximize');
		
		foreach($statesWithPanelParam as $state) {
			$this->isiteState = $state;
			$html = $this->helperFactory();
			$url = $html->url('/main/index?Larry=Bird&Best=Ever');
			$panelParamPattern = 'icb.pagecontent\d+' . urlencode(":rmain/index?Larry=Bird&Best=Ever");
			
			$this->assertRegExp('{^http://isites.localhost/}', $url);
			$this->assertRegExp('{keyword=k\d+}', $url);
			$this->assertRegExp('{pageid=icb.page\d+}', $url);
			$this->assertRegExp('{pageContentId=icb.pagecontent\d+}', $url);
			$this->assertRegExp('{panel='.$panelParamPattern.'}', $url);
		}
	}
}
	
