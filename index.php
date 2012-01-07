<?php
/**
 * @author YAY!Scripting
 * @package files
 * @subpackage loader
 */

/** Loading the core-class */
require_once('system/core/core.class.php');

/** Loading the router-class (which extends the core-class) */
require_once('system/core/router.class.php');
	
// start core/router
$router = new YS_Router();