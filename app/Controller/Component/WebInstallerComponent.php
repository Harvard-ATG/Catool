<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * WebInstallerComponent class
 *
 * Handles installation tasks.
 *
 * @package app.Lib
 */
App::uses('Cache', 'Cache');

class WebInstallerComponent extends Component {

/**
 * Default configuration settings to use
 *
 * @var array
 */
	protected $_defaultDbConfig = array(
		'name' => 'default',
		'datasource' => 'Mysql',
		'persistent' => 'false',
		'host' => 'localhost',
		'login' => 'root',
		'password' => 'password',
		'database' => 'project_name',
		'schema' => null,
		'prefix' => null,
		'encoding' => null,
		'port' => null
	);

	/**
	 * Constructor for base component class.
	 *
	 * Note: all $settings that are also public properties will have their
	 * values changed to the matching value in $settings.
	 */
	public function __construct(ComponentCollection $collection, $settings = array()) {
		parent::__construct($collection, $settings);
	}

	/**
	 * Called before the controller's beforeFilter
	 */
	public function initialize(Controller $controller) {
		
	}

	/**
	 * Called after controller's beforeFilter but before action method
	 */
	public function startup(Controller $controller) {

	}

	/**
	 * Tests if the database config is valid.
	 *
	 * Note: no parsing is done, we just check to see if we can connect
	 * using the supplied parameters.
	 *
	 * @return array 
	 */
	public function testDbConfig() {
		$ds = ConnectionManager::getDataSource('default');
		error_log("datasource");

		$connected = true;
		$error = '';
		try {
			$ds->connect();
		} catch(MissingConnectionException $e) {
			$connected = false;
			$error = $e->getMessage();
			error_log("Missing connection: $error");
		}

		return compact('connected', 'error');
	}

	/**
	 * Saves the database configuration to disk.
	 *
	 * @param array $default configuration
	 * @return boolean
	 */
	public function saveDbConfig($default) {
		$out = "<?php\n";
		$out .= "class DATABASE_CONFIG {\n\n";

		$configs = array($default);
		foreach ($configs as $config) {
			$config = array_merge($this->_defaultDbConfig, $config);
			extract($config);

			$out .= "\tpublic \${$name} = array(\n";
			$out .= "\t\t'datasource' => 'Database/{$datasource}',\n";
			$out .= "\t\t'persistent' => {$persistent},\n";
			$out .= "\t\t'host' => '{$host}',\n";

			if ($port) {
				$out .= "\t\t'port' => {$port},\n";
			}

			$out .= "\t\t'login' => '{$login}',\n";
			$out .= "\t\t'password' => '{$password}',\n";
			$out .= "\t\t'database' => '{$database}',\n";

			if ($schema) {
				$out .= "\t\t'schema' => '{$schema}',\n";
			}

			if ($prefix) {
				$out .= "\t\t'prefix' => '{$prefix}',\n";
			}

			if ($encoding) {
				$out .= "\t\t'encoding' => '{$encoding}'\n";
			}

			$out .= "\t);\n";
		}

		$out .= "}\n";

		$result = file_put_contents(APP.'Config'.DS.'database.php', $out);

		return $result === FALSE ? false : true;	
	}

	/**
	 * Creates and populates the database schema.
	 *
	 * @return array 
	 */

	public function createSchema() {
		return array('created' => true, 'log' => array());
	}
}
