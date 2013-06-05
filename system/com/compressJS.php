<?php
/** minifies all JS-files
 * 
 * This file is automatically called when a JS-file is beeing loaded.
 * This file does not use the core, that would be a huge overhead.
 * 
 * @author YAY!Scripting
 * @package com
 * @subpackage loading
 */

$file = $_GET['file'];


// change dir
chdir('../../');

// wrong suffix?
if (substr($file, strlen($file) - 3) != '.js')
	throwError();
	
// file exists?
if (!file_exists('./'.$file))
	throwError();
	
/** Load possible exceptions */
include('system/core/exception.class.php');

/** Load singleton feature (required by config) */
include('system/core/singleton.class.php');

/** Load the config-class */
include('system/core/config.class.php');

// load config
$config = \System\Config::Load();

// get cache-dir (with trailing slash)
$cache = $config->cache->directory . '/';

// new or old file?
$new = true;
$filepath = './'.$file;

if (file_exists($cache.md5(filectime($filepath) . $file).'.js'))
	$new = (filectime($cache.md5(filectime($filepath) . $file).'.js') < filectime($filepath));

if ($config->cache->js !== true)
	$new = true;
	
// CACHE
if ($new === true) {
	
	$content = file_get_contents($filepath);
	
	if ($config->compress->js === true) {
		
		// delete comments
		$content = preg_replace("/\/\*[^!](.+?)\*\//s", "", $content);
		
		//compress//compress
		include ('../external/JShrink/jshrink.class.php');
		try {
			
			$content = JShrink::minify($content);
			
		} catch (JShrinkException $ex) {
			
			$content = preg_replace("/\/\*[^!](.+?)\*\//s", "", $content);
			$content = JShrink::minify($content);
			
		}
		
	}
	
	// cache
	if ($config->cache->js === true && $config->compress->js === true) {
		
		$fh = fopen($cache.md5(filectime($filepath) . $file).'.js', 'w');
		fwrite($fh, $content);
		fclose($fh);
		
	}
	
} else {
	
	$content = file_get_contents($cache.md5(filectime($filepath) . $file).'.js');
	
}

// get etag
$etag = sha1($content);
$last_modified_time = filemtime($filepath); 


// headers
header("Content-type: text/javascript");
header("Cache-control: max-age");
header("Expires: ".gmdate("r", strtotime("+1 year")));
header("ETag: ".$etag);
header("Last-Modified: ".gmdate("r", $last_modified_time)); 

// exit if not modified
if (@strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $last_modified_time || @trim($_SERVER['HTTP_IF_NONE_MATCH']) == $etag) {

	header("HTTP/1.1 304 Not Modified"); 
	exit; 
    
}

// echo
echo $content;


/** Loads the error-page (404)
 * 
 * @ignore
 */
function throwError()
{
	
	$_GET['ys_route'] = 'error/trigger_404';
	include 'index.php';
	
}
