<?php 
App::uses('ClassRegistry', 'Utility');
App::uses('Aro', 'Model');
App::uses('Aco', 'Model');
App::Uses('Permission', 'Model');
App::uses('Role', 'Model');
App::uses('User', 'Model');
App::uses('DbAcl', 'Controller/Component/Acl');

class AppSchema extends CakeSchema {

	public function before($event = array()) {
		// flush cache so we can insert data into more than one table
		$db = ConnectionManager::getDataSource($this->connection);
		$db->cacheSources = false;
		return true;
	}

	public function after($event = array()) {
		static $createdTables = 0;

		// determine how many tables there are using reflection
		$totalTables = 0;
		$refclass = new ReflectionClass($this);
		foreach($refclass->getProperties() as $property) {
			if($property->class === $refclass->name) {
				++$totalTables;
			}
		}

		// handle create table events
		if(isset($event['create'])) {
			++$createdTables;
			switch($event['create']) {
				case 'roles':
					// Create hierarchical roles
					$role = ClassRegistry::init('Role');
					$role->setDataSource($this->connection);
					$parent_id = null;
					$role_names = $role->getRoleNames();
					foreach($role_names as $role_name) {
						$role->create();
						$role->save(array(
							'name' => $role_name, 
							'display_name' => $role->getDisplayNameFor($role_name), 
							'parent_id' => $parent_id
						));
						$parent_id = $role->id;
					}
					break;
				case 'users':
					// Create default user
					$user = ClassRegistry::init('User');
					$user->setDataSource($this->connection);
					$user->create();
					$user->save(array('name' => 'Admin'));
					break;
				case 'aros':
					// Create ARO root node for users and collection memberships
					$aro = ClassRegistry::init('Aro');
					$aro->setDataSource($this->connection);
					$aro->create();
					$aro->save(array('alias' => 'users'));
					break;
				case 'acos':
					// Create ACO root node for the role hierarchy
					$aco = ClassRegistry::init('Aco');
					$aco->setDataSource($this->connection);
					$aco->create();
					$aco->save(array('alias' => 'role'));
					break;
				case 'aros_acos':
					break;
			}

			// need to wait until all tables are created
			if($createdTables === $totalTables) {
				$acl = new DbAcl(); // component
				$acl->allow('users', 'role/super/admin/mod/user');
				$acl->allow(array('model' => 'User', 'foreign_key' => 1), 'role/super');
			}
		}

		return true;
	}

	public $acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $aros = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'model' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'foreign_key' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'alias' => array('type' => 'string', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'length' => 10),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $aros_acos = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'primary'),
		'aro_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10, 'key' => 'index'),
		'aco_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'length' => 10),
		'_create' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_read' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_update' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'_delete' => array('type' => 'string', 'null' => false, 'default' => '0', 'length' => 2, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'ARO_ACO_KEY' => array('column' => array('aro_id', 'aco_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $collections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'target_setting_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'target_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'deleted' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'collection_target_setting_fk' => array('column' => 'target_setting_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $roles = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45, 'key' => 'unique', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_description' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 4000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'name_UNIQUE' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $notes = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'target_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'tag_collection_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'parent_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'lft' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'rght' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'title' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'body' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'deleted' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'hidden' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'note_target_fk' => array('column' => 'target_id', 'unique' => 0), 'note_user_fk' => array('column' => 'user_id', 'unique' => 0), 'note_tag_collection_fk' => array('column' => 'tag_collection_id', 'unique' => 0), 'note_parent_fk' => array('column' => 'parent_id', 'unique' => 0), 'note_type_fk' => array('column' => 'type', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $resources = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'content' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_size' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'file_from_url' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 1000, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'file_path' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'duration' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'deleted' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $segments = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 45, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'note_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'start_time' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'end_time' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 25, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'segment_type_fk' => array('column' => 'type', 'unique' => 0), 'segment_note_fk' => array('column' => 'note_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 50, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tag_name_uk' => array('column' => 'name', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $tag_collections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'instance_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'tag_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'hash' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 40, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tag_collections_hash_uk' => array('column' => 'hash', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $tag_collection_tags = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'tag_collection_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'tag_id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'index'),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'tag_collection_tags_fk1' => array('column' => 'tag_collection_id', 'unique' => 0),'tag_collections_tag_fk2' => array('column' => 'tag_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $targets = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 45, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'collection_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'resource_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'target_setting_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'sort_order' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'note_count' => array('type' => 'integer', 'null' => false, 'default' => 0),
		'display_name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_description' => array('type' => 'text', 'null' => true, 'default' => NULL, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_creator' =>  array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 250, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'display_created' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 100, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'hidden' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'deleted' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'target_collection_fk' => array('column' => 'collection_id', 'unique' => 0), 'target_resource_fk' => array('column' => 'resource_id', 'unique' => 0), 'target_target_setting_fk' => array('column' => 'target_setting_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $target_settings = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'lock_annotations' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'lock_comments' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'sync_annotations' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'highlight_admins' => array('type' => 'integer', 'null' => false, 'default' => 0, 'length' => 1),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')		
	);
	public $users = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'name' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'email' => array('type' => 'string', 'null' => true, 'default' => NULL, 'length' => 500, 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'role_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'users_role_fk' => array('column' => 'role_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $user_collections = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'user_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'collection_id' => array('type' => 'integer', 'null' => true, 'default' => NULL),
		'role_id' => array('type' => 'integer', 'null' => false, 'default' => NULL),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'modified' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_collections_user_fk' => array('column' => 'user_id', 'unique' => 0), 'user_collections_collection_fk' => array('column' => 'collection_id', 'unique' => 0), 'user_collections_role_fk' => array('column' => 'role_id', 'unique' => 0), 'user_collections_uk' => array('column' => array('user_id', 'collection_id'), 'unique' => 1)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
	public $user_identities = array(
		'id' => array('type' => 'integer', 'null' => false, 'default' => NULL, 'key' => 'primary'),
		'type' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 25, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'claimed_id' => array('type' => 'string', 'null' => false, 'default' => NULL, 'length' => 500, 'key' => 'index', 'collate' => 'utf8_general_ci', 'charset' => 'utf8'),
		'user_id' => array('type' => 'integer', 'null' => true, 'default' => NULL, 'key' => 'index'),
		'created' => array('type' => 'datetime', 'null' => true, 'default' => NULL),
		'indexes' => array('PRIMARY' => array('column' => 'id', 'unique' => 1), 'user_identities_type' => array('column' => 'type', 'unique' => 0), 'user_identities_claimed_id' => array('column' => 'claimed_id', 'unique' => 0), 'user_identities_user_id' => array('column' => 'user_id', 'unique' => 0)),
		'tableParameters' => array('charset' => 'utf8', 'collate' => 'utf8_general_ci', 'engine' => 'InnoDB')
	);
}
