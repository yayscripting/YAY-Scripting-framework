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
$_GET['a'] = $_GET['b'];
$_GET['b'] = $_GET['c'];
$_GET['c'] = $_GET['d'];
$_GET['d'] = $_GET['e'];
$_GET['e'] = $_GET['f'];
$_GET['f'] = $_GET['g'];
$_GET['g'] = $_GET['h'];
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

