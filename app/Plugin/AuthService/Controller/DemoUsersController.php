<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('AuthServiceAppController', 'AuthService.Controller');

class DemoUsersController extends AuthServiceAppController {

    public $components = array(
		'AuthService.DemoAuthService',
	);

	public function login() {
        $this->DemoAuthService->login();
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
    }
}

