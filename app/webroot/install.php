<?php

//---------------------------------------------------------------------- 
// Define Constants

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
 * this class is to facilitate the initial setup. Once the dependencies are resolved
 * and the Cake infrastructure is up and running, installation tasks are
 * delegated to the InstallsController.
 *
 * Installation tasks are kicked off by AJAX requests from the default view.
 */
class WebInstaller {

	public $error = false;
	public $format = 'html';
	public $action = '';
	public $template = '';
	public $templateParams = array();

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
			$this->error($e->getMessage());
		}

		$this->render();
	}

	/**
	 * Renders the action.
	 */
	public function render() {
		$out = '';
		$template = $this->error ? 'error' : $this->template;
		switch($this->format) {
			case 'json':
				header('Content-type: application/json');
				$out = json_encode($this->templateParams);
				break;
			case 'html':
			default:
				$out = $this->_invoke("template_$template");
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
	 * Creates an error message.
	 */
	public function error($message) {
		$this->error = true;
		$this->templateParams['error'] = $message;
		header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error', true, 500);
	}

	/**
	 * Displays the error
	 */
	public function template_error() {
		return $this->get('error');
	}

	/**
	 * Default action called by requesting install.php
	 *
 	 * Just renders base HTML page. From here on out, the client should
	 * proceed with the installation by submitting only AJAX requests to 
	 * take care of CakePHP dependencies (see action_cake_setup)
	 * and then to the CakePHP InstallsController.
	 */
	public function action_default() {
		// do nothing but serve up static page with html and js
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
	<link rel="stylesheet" type="text/css" href="css/install.css" />
	<!--[if IE]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
	<script type="text/javascript" src="js/lib/jquery.js"></script>
	<script type="text/javascript" src="js/lib/underscore.js"></script>
	<script type="text/javascript" src="js/lib/backbone.js"></script>
	<script type="text/javascript" src="js/app/install.js"></script>
</head>

<body id="home">
<h1>CATool Web Install</h1>
<div id="install-view">
</div>
<script>
$(function() {
	var el = $('#install-view');
	new Catool.InstallView({ el: el });
	console.log(el);
});
</script>
</body>
</html>

__HTML;

		return $html;
	}

	/**
	 * Setup cake so that we can successfully bootstrap the application.
	 *
	 * The goal of this method is to setup all the pre-requisites so that
	 * we can dispatch to the actual Cake controllers. In order to do that,
	 * must ensure that the following are done:
	 *
	 * 		1) Temporary dirs for caching, logging, etc are writable.
	 * 		2) Database config file exists.
	 *
	 * Once those things are setup, Cake should be functional so we can
	 * dispatch requests to the install controller which will take care of
	 * configuring the database connection and creating the schema.
	 *
	 * NOTE: this action is called via AJAX
	 */
	public function action_setup_cake() {
		$this->format = 'json';
		$this->_loadCakeBootstrap();
		$this->_createTempDirs();
		$this->_createDefaultDbConfig();
		$this->set('success', true);
		$this->set('message', 'Cake temporary directories and default database config created');
	}

	/**
	 * Attempts to load the cake bootstrap library which defines a number
	 * of constants, basic functions, etc that are need by the framework.
	 *
	 * Throws a WebInstallerException if loading fails.
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
	 * Setup temporary directories before doing anything because Cake throws an 
	 * internal error if these directoriers don't exist or aren't writable.
	 *
	 * Throws a WebInstallerException if the dirs can't be created.
	 */
	protected function _createTempDirs() {
		$temp_dirs = array(TMP, CACHE, CACHE.'persistent', CACHE.'models', LOGS);
		$temp_dir_errors = array();
		foreach($temp_dirs as $dir) {
			if(!file_exists($dir)) {
				if(!mkdir($dir, 0770, true)) {
					$temp_dir_errors[] = "Error creating tmp dir: $dir ";
				}
			}
			if(count($temp_dir_errors) > 0) {
				$error_str = implode("\n",$temp_dir_errors);
				throw new WebInstallerException("Unable to create temporary directories for the application: $error_str");
			}
		}
	}

	/**
	 * Copy the default database configuration. Cake throws an internal error
	 * when this config file is missing.
	 *
	 * Throws a WebInstallerException if the config can't be copied.
	 */
	protected function _createDefaultDbConfig() {
		if(!copy(APP.'Config'.DS.'database.php.default', APP.'Config'.DS.'database.php')) {
			throw new WebInstallerException("Unable to create the default database config. Please check that your ".APP."Config directory is writable.");
		}
	}

	/**
	 * Invokes a method on the class.
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
