<?php
/**
 * @author YAY!Scripting
 * @package files
 * @subpackage loader
 * @version v1.3.6
 */
 
/** Prevent CLI usage */
$_GET['ys_mode'] = ($_GET['ys_mode'] == 'com') ? 'com' : 'browser';

/** Loading the core-class */
require_once('system/core/core.class.php');

/** Loading the router-class (which extends the core-class) */
require_once('system/core/router.class.php');
	
// start core/router
$router = new YS_Router();