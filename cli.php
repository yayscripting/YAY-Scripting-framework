<?php
/**
 * @author YAY!Scripting
 * @package files
 * @subpackage loader
 */
 
// extra safety
$_GET = array();
 
// check array for $argv
if(is_array($argv)){

	// load GET-vars
	foreach ($argv as $index => $command) {
		
		// filename
		if ($index == 0)
			continue;
			
		// parameters
		if (strpos($command, "=") !== false && substr($command, 0, 2) == '--')
			$_GET[substr($command, 2, strpos($command, "=") - 2)] = substr($command, strpos($command, "=") + 1);	
		
		
	}
	
}

echo $_SERVER['REQUEST_URI'];

// set correct cwd
chdir(dirname(__FILE__));

/** Loading the core-class */
require_once('system/core/core.class.php');

/** Loading the router-class (which extends the core-class) */
require_once('system/core/router.class.php');
	
// start core/router
$router = new YS_Router();