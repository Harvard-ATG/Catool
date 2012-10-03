<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('AppHelper', 'View/Helper');

/**
 * Helper for creating the navigation.
 */
class NavRendererHelper extends AppHelper {
	public $helpers = array('Html');

/**
 * True if the user has the ability to proxy (switch user), false otherwise.
 * @var boolean
 */
	public $allowProxy = false;

/**
 * True if the user is allowed to access the admin panel, false otherwise.
 * @var boolean
 */
	public $allowAdmin = false;

/** 
 * Constructor
 *
 * Expects to be passed user permission settings. See the beforeRender()
 * function in the AppController.
 *
 * @param View $view
 * @param array $settings 
 * @return void
 */
	public function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);

		if(isset($settings['allowProxy'])) {
			$this->allowProxy = $settings['allowProxy'];
		}
		if(isset($settings['allowAdmin'])) {
			$this->allowAdmin = $settings['allowAdmin'];
		}
	}
	
/**
 * Returns the primary nav as an unordered list.
 * 
 * @param array $items list of items, each with a name and url
 * @param array $options used to create the ul tag
 * @return string
 */
	public function primary($items = array(), $options = array()) {
		$list = '';
		foreach($items as $item) {
		    $list .= $this->Html->tag('li',  
		        $this->Html->link($item['name'], $item['url']), 
		        ($this->request->here === $item['url'] ? array('class' => 'active') : array())
		    );
		}
		return $this->Html->tag('ul', $list, $options);
	}

/**
 * Returns nav items meant only for an app admin.
 *
 * @param array $items list of items, each with a name and url
 * @param array $options used to create the ul tag
 * @return string
 */
	public function adminOnly($items = array(), $options = array()) {
		if(!$this->allowAdmin) {
			return '';
		}

		$list = '<li class="divider-vertical"></li>';
		foreach($items as $item) {
		    $list .= $this->Html->tag('li',  
		        $this->Html->link($item['name'], $item['url']), 
		        ($this->request->here === $item['url'] ? array('class' => 'active') : array())
		    );
		}

		return $this->Html->tag('ul', $list, $options);
	}
	
/**
 * Returns a the user name
 * 
 * @param array $user Auth.User
 * @return string
 */
	public function getUserName($user = array()) {
		$out = '';
		if(!empty($user['id']) && isset($user['name'])) {
			$out = $user['name'];
		}
		return $out;
	}
	
/**
 * Returns a list of user menu items
 * 
 * @return array
 */
	public function getUserMenuItems() {
		$items = array();
		
		if($this->allowProxy) {
			$items[] =  $this->Html->link('Proxy', '/users/proxy');
		}
		
		$items[] = $this->Html->link('Sign out', '/users/logout');

		return $items;
	}
}
