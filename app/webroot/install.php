<?php
// Copyright (c) 2012 The President and Fellows of Harvard College
// Use of this source code is governed by the LICENSE file found in the root of this project.
?>
<?php
/**
 * Basic web installer for the application.
 *
 * IMPORTANT:
 * Please remember to remove this file in PRODUCTION!
 *
 * USAGE:
 * Begin the install by accessing "install.php" withou parameters.
 */

//---------------------------------------------------------------------- 
// Define Constants (taken from index.php)

error_reporting(E_ALL);

/**
 * Use the DS to separate the directories in other defines
 */
if (!defined('DS')) {
	define('DS', DIRECTORY_SEPARATOR);
}
/**
 * These defines should only be edited if you have cake installed in
 * a directory layout other than the way it is distributed.
 * When using custom settings be sure to use the DS and do not add a trailing DS.
 */

/**
 * The full path to the directory which holds "app", WITHOUT a trailing DS.
 *
 */
if (!defined('ROOT')) {
	define('ROOT', dirname(dirname(dirname(__FILE__))));
}
/**
 * The actual directory name for the "app".
 *
 */
if (!defined('APP_DIR')) {
	define('APP_DIR', basename(dirname(dirname(__FILE__))));
}

/**
 * The absolute path to the "cake" directory, WITHOUT a trailing DS.
 *
 * Un-comment this line to specify a fixed path to CakePHP.
 * This should point at the directory containing `Cake`.
 *
 * For ease of development CakePHP uses PHP's include_path.  If you
 * cannot modify your include_path set this value.
 *
 * Leaving this constant undefined will result in it being defined in Cake/bootstrap.php
 */
	//define('CAKE_CORE_INCLUDE_PATH', ROOT . DS . 'lib');

/**
 * Editing below this line should NOT be necessary.
 * Change at your own risk.
 *
 */
if (!defined('WEBROOT_DIR')) {
	define('WEBROOT_DIR', basename(dirname(__FILE__)));
}
if (!defined('WWW_ROOT')) {
	define('WWW_ROOT', dirname(__FILE__) . DS);
}

//----------------------------------------------------------------------
// Installer Classes

/**
 * Exception class for the installer.
 */
class WebInstallerException extends Exception { }

/**
 * Web Installer class.
 *
 * A very minimal controller/view for installing the essential Cake dependencies.
 *
 * Cake dies early and often when key dependencies are missing, so the goal of 
 * this class is to facilitate the initial setup.
 *
 * Installation tasks are kicked off by AJAX requests from the default action
 * (i.e. install.php?action=default or just install.php).
 */
class WebInstaller {

	public $errors = array(); // error messages
	public $logs = array(); // log messages for the user
	public $format = 'html'; // html|json
	public $action = ''; // action method name
	public $template = ''; // template method name
	public $templateParams = array();

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
		'login' => '',
		'password' => '',
		'database' => '',
		'schema' => null,
		'prefix' => null,
		'encoding' => null,
		'port' => null
	);

	/**
	 * Run the installer.
	 */
	public function run() {
		$this->action = isset($_GET['action']) ? $_GET['action'] : 'default';
		$this->template = $this->action;
		$method = "action_{$this->action}";

		try {
			$this->_invoke($method);
		} catch(WebInstallerException $e) {
			header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
			$this->template = 'error';
			$this->error($e->getMessage());
		}

		$this->render();
	}

	/**
	 * Renders the action.
	 */
	public function render() {
		$this->templateParams['error'] = $this->errors;
		$this->templateParams['log'] = $this->logs;

		$out = '';
		switch($this->format) {
			case 'json':
				header('Content-type: application/json');
				$out = json_encode($this->templateParams);
				break;
			case 'html':
			default:
				header('Content-type: text/html');
				$out = $this->_invoke("template_{$this->template}");
		}

		echo $out;
	}

	/**
	 * Sets a template variable.
	 */
	public function set($name, $value) {
		$this->templateParams[$name] = $value;
	}

	/**
	 * Gets a template variable.
	 */
	public function get($name) {
		return isset($this->templateParams[$name]) ? $this->templateParams[$name] : null;
	}

	/**
	 * Adds an error message to the stack.
	 */
	public function error($message = null) {
		$this->errors[] = $message;
	}
	
	/**
	 * Adds a log message to the stack.
	 *
	 * NOTE: this is intended to be an audit log that will be displayed to 
	 * the user, not for logging to disk.
	 */
	public function log($message = null) {
		$this->logs[] = $message;
	}

	/**
	 * Displays the error.
	 */
	public function template_error() {
		return "Error: ".implode("\n", $this->errors);
	}

	/**
	 * Default action called by requesting install.php
	 *
 	 * Just renders base HTML page. From here on out, the client should
	 * proceed with the installation by submitting AJAX requests.
	 */
	public function action_default() {
		$this->format = 'html';
   	}

	/**
	 * Default action template. 
	 */
	public function template_default() {
		$html = <<<'__HTML'
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Catool Install</title>
	<link rel="stylesheet" type="text/css" href="css/bootstrap.css" />
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript" src="js/lib/jquery.js"></script>
	<script type="text/javascript" src="js/lib/underscore.js"></script>
	<script type="text/javascript" src="js/lib/backbone.js"></script>
	<script type="text/javascript" src="js/lib/bootstrap.js"></script>
	<script type="text/javascript" src="js/app/install.js"></script>
</head>

<body>
<div class="row">
<div class="span10 offset2">
<div class="page-header">
	<h1>CATool Web Installation</h1>
</div>

<div id="InstallTaskError" class="alert alert-error hide" style="margin: 1em 0">
	<a class="close" data-dismiss="alert">&times;</a>
	<p class="js-error"></p>
</div>

<div id="InstallTasksView">
</div>

<!----------------------------------------------------------------------------->
<!-- Render Main BackboneJS View -->

<script>
$(function() {
	var el = $('#InstallTasksView');
	var view = new Catool.InstallTasksView({ el: el });
	view.render();
});
</script>

<!----------------------------------------------------------------------------->
<!-- BackboneJS View Templates -->

<script type="text/template" id="InstallTasksViewTemplate">
	<div class="row">
		<div class="span6">
			<h3>Tasks</h3>
		</div>
		<div class="span3">
			<h3>Status</h3>
		</div>
	</div>
</script>

<script type="text/template" id="InstallTaskViewTemplate">
	<div class="js-header" style="cursor: pointer">
		<div class="span6">
			<h4><%= description %></h4>
			<p class="js-error hide" style="white-space: pre-wrap"><%= error %></p>
			<p class="js-log hide" style="white-space: pre-wrap"><%= log %></p>
		</div>
		<div class="span3 js-status"><%= status %></div>
	</div>
</script>

<script type="text/template" id="InstallTaskErrorTemplate">
	<h4>Install Task Error: <%= description %></h4>
	<ul>
		<li><%= message %></li>
		<li style="white-space: pre-wrap"><%= error %></li>
	</ul>
</script>

<script type="text/template" id="InstallTasksCompleteTemplate">
	<hr/>
	<h3>Installation Complete</h3>
	<p>You may now <a href="?action=finish">login to the application</a>.
	Don't forget to remove <em>app/webroot/install.php</em> and <em>app/Controller/InstallsController.php</em>
	for production.
	</p>
</script>

<script type="text/template" id="InstallTaskDatabaseForm">
<div class="row" style="margin: 2em 0">
	<div class="span8 well">
		<form id="InstallTaskDatabaseFormInput" class="form form-vertical">
		<fieldset>
			<div class="control-group">
				<label class="control-label" for="host">Host</label>
				<div class="controls">
					<input type="text" id="host" name="host" value="<%= host %>" />
				</div>
			</div>
	
			<div class="control-group">
				<label class="control-label" for="port">Port (optional)</label>
				<div class="controls">
					<input type="text" id="port" name="port" value="<%= port %>" />
				</div>
			</div>
	
			<div class="control-group">
				<label class="control-label" for="login">User</label>
				<div class="controls">
					<input type="text" id="login" name="login" value="<%= login %>" />
				</div>
			</div>
	
			<div class="control-group">
				<label class="control-label" for="password">Password</label>
				<div class="controls">
					<input type="text" id="password" name="password" value="<%= password %>" />
				</div>
			</div>
	
			<div class="control-group">
				<label class="control-label" for="database">Database Name</label>
				<div class="controls">
					<input type="text" id="database" name="database" value="<%= database %>" />
				</div>
			</div>

			<!-- Disabling for now because there seems to be an issue with the prefix -->
			<div class="control-group">
				<label class="control-label" for="prefix">Table Prefix (optional)</label>
				<div class="controls">
					<input type="text" id="prefix" name="prefix" value="<%= prefix %>" />
				</div>
			</div>
			
			<div class="control-group">
				<div class="controls">
					<input type="submit" name="submit" value="Save Configuration" class="js-submit-btn btn" />
				</div>
			</div>
		</fieldset>
		
		</form>
	</div>
</div>
</script>

</div><!-- end span -->
</div><!-- end row -->
</body>
</html>

__HTML;

		return $html;
	}

	/**
	 * Setup cake so that we can successfully bootstrap the application.
	 *
	 * This method attempts to bootstrap CakePHP and create temporary
	 * directories. This is the first thing that needs to be done in
	 * the installation, otherwise CakePHP will not work.
	 *
	 * NOTE: called as an AJAX query. 
	 *
	 * @return void
	 */
	public function action_setup_cake() {
		$this->format = 'json';

		$this->_loadCakeBootstrap();

		$success = $this->_makeTempDirs();

		$this->set('success', $success);
		$this->set('message', $success ? 'OK' : 'Error creating temporary directories');
	}

	/**
	 * Creates the database config and tests the connection.
	 *
	 * NOTE: called as an AJAX query from default action
	 *
	 * @return void
	 */
	public function action_configure_database() {
		$this->format = 'json';

		$this->_loadCakeBootstrap();

		$config = array();
		foreach(array_keys($this->_defaultDbConfig) as $key) {
			if(isset($_POST[$key])) {
				$val = $_POST[$key];
				$val = str_replace("'", "\\'", $val); // escape single quotes
				$config[$key] = $val;
			} else {
				$config[$key] = $this->_defaultDbConfig[$key];
			}
		}

		$success = $this->_writeDbConfig($config);
		if($success) {
			$success = $this->_testDbConnection();
			if($success) {
				$message = 'OK';
			} else {
				$message = 'Error connecting to the database. Please make sure your configuration settings are correct.';
			}
		} else {
			$message = 'Error saving database config file.';
		}

		$this->set('success', $success);
		$this->set('message', $message);
	}
	
	/**
	 * Creates the database schema.
	 *
	 * NOTE: called as an AJAX query. 
	 *
	 * @return void
	 */
	 public function action_create_schema() {
	 	 $this->format = 'json';
	 	 
	 	 $this->_loadCakeBootstrap();
	 	 $success = $this->_createSchema();

	 	 $this->set('success', $success);
	 	 $this->set('message', $success ? 'OK' : 'Error creating schema');
	 }
	 
	 /**
	  * Finishes the install.
	  *
	  * Just redirects to the CakePHP Controller/InstallsController 
	  * (i.e. /installs) in order to login the user as the
	  * default administrator. 
	  *
	  * This is NOT called as an AJAX query.
	  *
	  * @return void
	  */
	  public function action_finish() {
	  	$this->_loadCakeBootstrap();
		header('Location: '.FULL_BASE_URL.'/installs/promote');
		exit;
	  }

	/**
	 * Load the cake bootstrap libraries.
	 *
	 * The CakePHP bootstrap library defines some useful constants, basic
	 * functions, etc. If this can't be loaded successfully, then there is a 
	 * serious problem and the install should immediately abort.
	 *
	 * NOTE: most actions require Cake to be bootstrapped.
	 *
	 * @throws a WebInstallerException if loading the bootstrap fails.
	 */
	protected function _loadCakeBootstrap() {
		if (!defined('CAKE_CORE_INCLUDE_PATH')) {
			if (function_exists('ini_set')) {
				ini_set('include_path', ROOT . DS . 'lib' . PATH_SEPARATOR . ini_get('include_path'));
			}
			if (!include ('Cake' . DS . 'bootstrap.php')) {
				$failed = true;
			}
		} else {
			if (!include (CAKE_CORE_INCLUDE_PATH . DS . 'Cake' . DS . 'bootstrap.php')) {
				$failed = true;
			}
		}
		if (!empty($failed)) {
			throw new WebInstallerException("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/install.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
		}
	}
	
	/**
	 * Test the database connection using the default configuration.
	 *
	 * @return boolean true if connected, false otherwise
	 */
	protected function _testDbConnection() {
		App::uses('ConnectionManager', 'Model');
		
		$connected = false;
		$error = '';

		try {
			$ds = ConnectionManager::getDataSource('default');
			$connected = $ds->connect();
		} catch (MissingDatasourceConfigException $e) {
			$error = $e->getMessage();
		
		} catch(MissingConnectionException $e) {
			$error = $e->getMessage();
		}
		
		if(!$connected) {
			$this->error($error);
		}
		
		return $connected;
	}

	/**
	 * Setup temporary directories required by Cake. 
	 *
	 * @return boolean true if created, false otherwise
	 */
	protected function _makeTempDirs() {
		// maps to a glob pattern for removing temp files
		$temp_dirs = array(
			TMP => false, 
			CACHE => false, 
			CACHE.'persistent' => '*cake_core_*', 
			CACHE.'models' => '*cake_model_*', 
			LOGS => '*.log'
		);

		$temp_dir_errors = array();
		foreach($temp_dirs as $dir => $glob_pattern) {
			if(file_exists($dir)) {
				if(is_writable($dir)) {
					$this->log("Temporary directory $dir created (already exists).");
				} else {
					$temp_dir_errors[] = "Temporary directory $dir exists, but is NOT writable.";
				}

				if($glob_pattern !== false) {
					$files_to_clear = glob($dir.DS.$glob_pattern);
					foreach($files_to_clear as $file) {
						if(is_file($file)) {
							if(unlink($file)) {
								$this->log("Cleared temporary file $file");
							} else {
								$temp_dir_errors[] = "Can't clear temporary file $file";
							}
						}
					}
				}
			} else {
				if(mkdir($dir, 0770, true)) {
					$this->log("Created temporary directory $dir");
				} else {
					$temp_dir_errors[] = "Can't create directory $dir";
				}
			}
		}

		if(count($temp_dir_errors) > 0) {
			$this->error(implode("\n", $temp_dir_errors));
			return false;
		}
		
		return true;
	}

	/**
	 * Writes the database configuration file.
	 *
	 * NOTE: adapated from lib/Cake/Console/Command/Task/DbConfigTask.php
	 *
	 * @param array $default configuration
	 * @return boolean
	 */
	protected function _writeDbConfig($default) {
		$default['name'] = 'default'; // force this to be the default config
		
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

		$config_dir = APP . 'Config';
		$config_file = $config_dir . DS . 'database.php';
		$result = file_put_contents($config_file, $out, LOCK_EX);
		
		// check for some common issues if the config save failed
		if($result === FALSE) {
			if(!file_exists($config_dir)) {
				$this->error(sprintf('Database config directory does NOT exist: %s', $config_dir));
			} else if(!is_writable($config_dir)) {
				$this->error(sprintf('Database config directory is NOT writable: %s', $config_dir));
			} else if(!is_writable($config_file)) {
				$this->error(sprintf('Database config is NOT writable: %s', $config_file));
			}
			return false;
		}
		
		$this->log(sprintf('Saved config settings to PHP file: %s', $config_file));

		return true;	
	}

	/**
	 * Creates the schema.
	 *
	 * NOTE: adapted from the schema shell lib/Cake/Console/Command/SchemaShell.php
	 * 
	 * @return mixed false on failure, otherwise a string message
	 */
	protected function _createSchema() {
		App::uses('ConnectionManager', 'Model');
		App::uses('CakeSchema', 'Model');

		$db = ConnectionManager::getDataSource('default');
		$CakeSchema = new CakeSchema(array(
			'name' => 'App', 
			'connection' => $db
		));

		$Schema = $CakeSchema->load();
		if(!$Schema) {
			$this->error(sprintf('Schema could not be loaded: %s', $CakeSchema->path . DS . $CakeSchema->file));
			return false;
		}
		
		$out = '';
		$drop = $create = array();

		foreach ($Schema->tables as $table => $fields) {
			$drop[$table] = $db->dropSchema($Schema, $table);
			$create[$table] = $db->createSchema($Schema, $table);
		}

		if (empty($drop) || empty($create)) {
			$out .= 'Schema is up to date.';
			return $out;
		}

		$out .= "\n".'The following table(s) will be dropped.';
		$out .= "\n".implode(", ", array_keys($drop));

		$out .= "\n".'Dropping table(s).';
		$result = $this->_runSchema($db, $drop, 'drop', $Schema);
		if($result === false) {
			return false;
		} else {
			$out .= $result;
		}

		$out .= "\n".'The following table(s) will be created.';
		$out .= "\n".implode(", ", array_keys($create));

		$out .= "\n".'Creating table(s).';
		$result = $this->_runSchema($db, $create, 'create', $Schema);
		if($result === false) {
			return false;
		} else {
			$out .= $result;
		}

		$out .= "\n".'End create.';
		$this->log($out);
		
		return true;
	}

	/**
	 * Runs sql from _createSchema()
	 *
	 * NOTE: adapted from the schema shell lib/Cake/Console/Command/SchemaShell.php
	 *
	 * @param array $contents
	 * @param string $event
	 * @param CakeSchema $Schema
	 * @return string
	 */
	protected function _runSchema($db, $contents, $event, &$Schema) {
		$out = '';
		if (empty($contents)) {
			$this->error('Sql could not be run');
			return false;
		}

		foreach ($contents as $table => $sql) {
			if (empty($sql)) {
				$out .= sprintf("%s is up to date.\n", $table);
			} else {
				if (!$Schema->before(array($event => $table))) {
					return false;
				}
				$error = null;
				try {
					$db->execute($sql);
				} catch (PDOException $e) {
					$error = $table . ': ' . $e->getMessage();
				}

				$Schema->after(array($event => $table, 'errors' => $error));

				if (!empty($error)) {
					$this->error($error);
					return false;
				} else {
					$out .= sprintf("%s updated\n", $table);
				}
			}
		}
	
		return $out;
	}
	
	/**
	 * Invokes a method on the class.
	 * 
	 * @param string $method name
	 * @return mixed
	 */
	protected function _invoke($method) {
		$rm = new ReflectionMethod(get_class($this), $method);
		return $rm->invoke($this);
	}

}


//----------------------------------------------------------------------
// Run the installer

try { 
	$installer = new WebInstaller();
	$installer->run();
} catch(Exception $e) {
	header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	echo "<!DOCTYPE html>\n";
	echo "<html lang=\"en\">\n";
	echo "<head><title>Install Error</title></head>\n";
	echo "<body>\n";
	echo "<h1>Fatal Install Error</h1>\n";
	echo "<p><strong>Error:</strong> {$e->getMessage()}</p>\n";	
	echo "</body>\n";
	echo "</html>\n";
}
