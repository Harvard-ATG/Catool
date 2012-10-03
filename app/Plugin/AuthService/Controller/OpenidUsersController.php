<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php

App::uses('AuthServiceAppController', 'AuthService.Controller');

class OpenidUsersController extends AuthServiceAppController {

    public $components = array(
		'AuthService.OpenidAuthService',
	);

	public function login() {
        $this->OpenidAuthService->login();
    }

    public function logout() {
        $this->redirect($this->Auth->logout());
    }

    public function proxy() {
        // TODO: User must be logged-in and have superuser role to proxy
        $this->OpenidAuthService->proxy();
    }
}

