<?php
/** Forces the browser to download an image.
 * 
 * Calling this file works by adding the GET-parameter 'download' with a blank value. e.g. /application/resources/images/file.png?download
 * This file does not use the core, because that would be a huge overhead.
 * 
 * @author YAY!Scripting
 * @package com
 * @subpackage loading
 */
 
 
// set working directory
chdir('../../');

// names
$file = substr($_GET['src'], 0, strrpos($_GET['src'], '.'));
$ext  = substr($_GET['src'], strrpos($_GET['src'], '.') + 1);

// competability
if (substr($file, 0, 1) != '/')
	$file = getcwd() . '/' . $file;
	
// verify extension. 		
if (!in_array(strtolower($ext), array('png', 'jpg', 'jpeg', 'gif', 'bmp', 'ico')))
	throwError();
	
// stupid dots in the name?
if (preg_match("/\.{1,2}[\/\\\\]/", $file) == 1)
	throwError();
	
// Can these images get accessed?
if (!file_exists(dirname($file) . '/.download'))
	throwError();
	
if (!file_Exists($file . '.' . $ext))
	throwError();
	
// headers
header('Content-Description: File Transfer');
header('Content-Type: IMAGE/'.strtoupper($ext));
header('Content-Disposition: attachment; filename="'.basename($file).'.'.$ext.'"');
header('Content-Transfer-Encoding: binary');
header('Expires: 0');
header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
header('Pragma: private');
header('Content-Length: ' . filesize($file . '.' . $ext));

echo file_get_contents($file . '.' . $ext);

/** Loads the error-page (404)
 * 
 * @ignore
 */
function throwError()
{
	
	$_GET['ys_route'] = 'error/trigger_404';
	include 'index.php';
	
}