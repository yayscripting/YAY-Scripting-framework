<?php
/** Checks if you're visiting an admin-page and modifies GET-values if you do so.
 * 
 * Checks for environment-settings
 * 
 * @author YAY!Scripting
 * @package functions
 * @subpackage router
 */
 
// set up special GET
$_GET['a'] = (isset($_GET['b'])) ? $_GET['b'] : null;
$_GET['b'] = (isset($_GET['c'])) ? $_GET['c'] : null;
$_GET['c'] = (isset($_GET['d'])) ? $_GET['d'] : null;
$_GET['d'] = (isset($_GET['e'])) ? $_GET['e'] : null;
$_GET['e'] = (isset($_GET['f'])) ? $_GET['f'] : null;
$_GET['f'] = (isset($_GET['g'])) ? $_GET['g'] : null;
$_GET['g'] = (isset($_GET['h'])) ? $_GET['h'] : null;
$_GET['h'] = null;

// delete empty values
foreach($_GET as $key => $value) {

	// check empty
	if (empty($value)){
	
		unset($_GET[$key]);
		
	} else {
		
		$_GET[$key] = urldecode($value);
		
	}
	
}

