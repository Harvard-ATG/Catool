<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AuthServiceAppModel', 'AuthService.Model');
App::uses('UserIdentity', 'AuthService.Model');
/**
 * OpenidUser Model
 *
 */
class OpenidUser extends UserIdentity {
	/**
	 * Type of identity, or the domain of this model.
	 * 
	 * @var string
	 */
	public $identityType = 'openid';

	/**
	 * Validation rules
	 * 
	 * @var string
	 */
	public $validate = array(
		'claimed_id' => array(
			'rule' => array('url', true)
		)
	);
}
