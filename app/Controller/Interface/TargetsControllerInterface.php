<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * interface TargetsControllerInterface
 *
 * @package app.Controller.Interface
 */
interface TargetsControllerInterface {

	/**
	 * view
	 * @param integer $id
	 */
	public function view($id = null);
	/**
	 * admin_add
	 */
	public function admin_add();
	/**
	 * admin_edit
	 * @param integer $id
	 */
	public function admin_edit($id = null);
	/**
	 * admin_delete
	 * @param integer $id
	 */
	public function admin_delete($id = null); 
}
