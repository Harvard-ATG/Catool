<?php
/**
 * Install
 */
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
	trigger_error("CakePHP core could not be found.  Check the value of CAKE_CORE_INCLUDE_PATH in APP/webroot/install.php.  It should point to the directory containing your " . DS . "cake core directory and your " . DS . "vendors root directory.", E_USER_ERROR);
}

/**
 * Setup temporary directories before doing anything because Cake throws an 
 * internal error if these directoriers don't exist or aren't writable.
 */
$temp_dirs = array(TMP, CACHE, CACHE.'persistent', CACHE.'models', LOGS);
$temp_dir_errors = array();
foreach($temp_dirs as $dir) {
	if(!file_exists($dir)) {
		if(!mkdir($dir, 0770, true)) {
			$temp_dir_errors[] = "Error creating tmp dir: $dir ";
		}
	}
	if(count($temp_dir_errors) > 0) {
		die(implode("\n",$temp_dir_errors));
	}
}

/**
 * Copy over the default database configuration, otherwise Cake throws an
 * internal error. 
 */
if(!copy(APP.'Config'.DS.'database.php.default', APP.'Config'.DS.'database.php')) {
	die("Error creating default database config.");
}

/** 
 * Now that the pre-requisites are out of the way as far as Cake is concerned, 
 * redirect to the installation controller which will handle everything else.
 */
App::uses('Router', 'Routing');
header('Location: '.Router::url('/installs'));
?>
