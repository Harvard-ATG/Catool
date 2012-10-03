<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
App::uses('AppModel', 'Model');
/**
 * RBACL Behavior
 * 
 * Enables automatic ACL updates whenever a model's role is changed.
 * 
 * Models must implement a getDefaultRole() method so that any time a 
 * role isn't specified, a default role is assigned.
 *
 * @package       app.Model.Behavior
 */
class RBACLBehavior extends ModelBehavior {

/**
 * Saves the old role for updating ACL.
 * @var array
 */
	protected $oldRole = array();
	
/**
 * The name of the role model.
 * @var string
 */
	public $roleModel = 'Role';
	
/**
 * Setup this behavior with the specified configuration settings.
 * 
 * @param Model $Model Model using this behavior
 * @param array $config Configuration settings for $model
 * @return void
 */
	public function setup(Model $Model, $config = array()) {
		if(isset($config['roleModel'])) {
			$this->roleModel = $config['roleModel'];
		}
		
		App::uses($this->roleModel, 'Model');		
	}

/**
 * beforeSave callback
 *
 * @param Model $Model Model using this behavior
 * @param array $options
 * @return boolean
 */
	public function beforeSave(Model $Model, $options = array()) {
		$this->oldRole[$Model->alias] = null;
		if(isset($Model->data[$Model->alias]['id'])) {
			$current = $this->getCurrentRole($Model);
			if($current !== false && !empty($current[$Model->alias]['role_id'])) {
				$this->oldRole[$Model->alias] = array(
					'model' => $this->roleModel,
					'foreign_key' => $current[$Model->alias]['role_id']
				);
			}
		}

		return true;
	}

/**
 * afterSave callback
 *
 * @param Model $Model Model using this behavior
 * @param boolean $created True if this save created a new record
 * @return void
 */
	public function afterSave(Model $Model, $created) {
		if($created) {
			$default = $Model->getDefaultRole();
			$role_id = $default[$this->roleModel]['id'];
			$Model->saveField('role_id', $role_id);
			
			$this->syncAclRole($Model, array(
				'model' => $this->roleModel,
				'foreign_key' => $role_id
			));
		} else {
			$new_role = array(
				'model' => $this->roleModel,
				'foreign_key' => $Model->field('role_id'),
			);
			$this->syncAclRole($Model, $new_role, $this->oldRole[$Model->alias]);
		}

		return true;
	}

/**
 * syncAclRole method
 *
 * Called by the afterSave model callback.
 * Grants access to the model's new role.
 *
 * @param Model $Model Model using this behavior
 * @param array $new_role
 * @param mixed $old_role
 * @return void
 */
	public function syncAclRole(Model $Model, $new_role, $old_role = null) {
		$aclAdapter = $this->loadAclAdapter();
		if(isset($old_role)) {
			$aclAdapter->inherit($Model, $old_role);
		}
		$aclAdapter->allow($Model, $new_role);
	}

/**
 * loadAclAdapter method
 *
 * @return instance of Acl.classname
 */
	protected function loadAclAdapter() {
		$aclClass = Configure::read('Acl.classname');
		App::uses($aclClass, 'Controller/Component/Acl');

		$aclAdapter = new $aclClass();
		if(!$aclAdapter instanceof AclInterface) {
			throw new CakeException(__d('cake_dev', 'Acl adapters must implement AclInterface'));
		}

		return $aclAdapter;
	}

/**
 * getCurrentRole
 * 
 * @param Model $Model Model using this behavior
 * @return array
 */
	public function getCurrentRole(Model $Model) {
		return $Model->find('first', array(
			'recursive' => -1,
			'conditions' => array("{$Model->alias}.id" => $Model->data[$Model->alias]['id'])
		));
	}
}
