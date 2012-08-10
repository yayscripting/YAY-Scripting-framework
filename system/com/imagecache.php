<?php
/** Makes the browser remember all images.
 * 
 * This file is automatically called when an image is beeing loaded.
 * This file does not use the core, because that would be a huge overhead.
 * 
 * @author YAY!Scripting
 * @package com
 * @subpackage loading
 */

// set correct working directory
chdir('../../');
	
// both variables set?
if (empty($_GET['file']) || empty($_GET['ext']))
	throwError();

// strip dir
$file = $_GET['file'];
$ext  = strtolower($_GET['ext']);
$dir = '';

// get dirs
while (strpos($file, '/') !== false) {
	
	$pos = strpos($file, '/');
	
	if (strpos(substr($file, 0, $pos + 1), '.') !== false)
		throwError();	
	
	$dir .= substr($file, 0, $pos + 1);
	$file = substr($file, $pos + 1);
	
}		

// unsafe characters?
if (strpos($file, '.') !== false)
	throwError();		
		
// check extension
$exts = array('png', 'jpg', 'jpeg', 'gif', 'bmp', 'ico');
if (!in_array($ext, $exts))
	throwError();
	
// file exists?
if (!file_exists($dir.$file.'.'.$ext))
	throwError();
	

// load MIMES
$mime = array (
	'png'  => 'image/png',
	'jpg'  => 'image/jpeg',
	'jpeg' => 'image/jpeg',
	'gif'  => 'image/gif',
	'bmp'  => 'image/bmp',
	'ico'  => 'image/x-icon'
);

// headers
header('Content-Disposition: inline; filename="'.$file . '.' . $ext.'"');
header("Content-type: ".$mime[$ext]);
header("Cache-control: max-age");
header("Expires: max-age");
header("ETag: ".sha1($content));

// read file
readfile($dir.$file.'.'.$ext);

/** Loads the error-page (404)
 * 
 * @ignore
 */
function throwError()
{
	
	$_GET['ys_route'] = 'error/trigger_404';
	include 'index.php';
	
}