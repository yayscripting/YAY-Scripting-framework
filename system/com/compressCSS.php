<?php
/** minifies all CSS-files
 * 
 * You can use LESS(http://leafo.net/lessphp/docs/) in any CSS-file, it will automatically get parsed(if the right config is set)
 * This file is automatically called when a css-file is beeing loaded.
 * This file does not use the core, that would be a huge overhead.
 * 
 * @author YAY!Scripting
 * @package com
 * @subpackage loading
 */
 
// strip dir
$file = $_GET['file'];

// change working dir
chdir('../../');

// wrong suffix?
if (substr($file, strlen($file) - 4) != '.css')
	throwError();

// file exists?
if (!file_exists('./' .$file))
	throwError();

/** Load possible exceptions */
include('system/core/exception.class.php');

/** Load singleton feature (required by config) */
include('system/core/singleton.class.php');

/** Load the config-class */
include('system/core/config.class.php');

// load config
$config = \System\Config::Load();

// get cache-dir
$cache = $config->cache->directory . '/';

// new or old file?
$new = true;

$filepath = './'.$file;

if(file_exists($cache.md5(filectime($filepath) . $filepath).'.css')) 
	$new = (filectime($cache.md5(filectime($filepath) . $filepath).'.css') >= filectime($filepath));

if ($config->cache->css !== true)
	$new = true;

// CACHE
if ($new === true) {
	
	$content = file_get_contents($filepath);
	
	if ($config->compress->css === true) {
		
		// lessPHP
		if ($config->compress->use_less === true) {
			
			require 'system/external/lessphp/lessc.inc.php';
			
			$less = new lessc();
			$content = $less->parse($content);
			
		}
		
		// strip comments/compress
		$content = preg_replace("/\/\*[^!](.+?)\*\//s", "", $content);
		$content = preg_replace("/[\n\t\r]/", "", $content);
		$content = preg_replace("/[\ ]{1,}/", " ", $content);
		
		// add newline after special comment (/*!)
		$content = preg_replace("/(\/\*[!\*].+?\*\/)/s", "\n".'$1'."\n", $content);
	
	// just lessphp?	
	} else if ($config->compress->use_less === true) {
		
		require 'system/external/lessphp/lessc.inc.php';
			
		$less = new lessc();
		$content = $less->parse($content);
		
	}
	
	// cache
	if ($config->cache->css === true) {
		
		$fh = fopen($cache.md5(filectime($filepath) . $filepath).'.css', 'w');
		fwrite($fh, $content);
		fclose($fh);
		
	}
	
} else {
	
	$content = file_get_contents($cache.md5(filectime($filepath) . $filepath).'.css');
	
}	

// headers
header("Content-type: text/css");
header("Cache-control: max-age");
header("Expires: max-age");
header("ETag: ".sha1($content));

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