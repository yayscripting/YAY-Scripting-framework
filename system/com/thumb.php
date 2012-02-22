<?php
/** Makes thumbnails of any image called with the correct parameters
 * 
 * Calling this file works by adding the GET-parameter 'thumb' with the value width|height. e.g. /application/resources/images/file.png?thumb=250|500
 * With only one parameter given, that value will be the height. e.g. /application/resources/images/file.png?thumb=500
 * Adding the GET-parameter zc=1 will enable zoomcropping.
 * This file does not use the core, because that would be a huge overhead.
 * 
 * @author YAY!Scripting
 * @package com
 * @subpackage loading
 */
 
// set working directory
chdir('../../');

// sended through htaccess-shortcut?
if (isset($_GET['thumb']))
	$_GET['size'] = $_GET['thumb'];

// verify file
if (empty($_GET['src']) || empty($_GET['size']))
	throwError();
	
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
if (!file_exists(dirname($file) . '/.thumb'))
	throwError();
	
// set GET
// src=/usercontent/photos/$1/$2&fltr[]=ric|5|5&wp=28&hp=28&zc=1

$pos	= strpos($_GET['size'], '|');
$height = intval(($pos !== false) ? substr($_GET['size'], $pos + 1) : $_GET['size']);
$width  = ($pos !== false) ? intval(substr($_GET['size'], 0, $pos)) : 0;
$zc	= ((isset($_GET['zc']) == false && isset($_GET['iar']) == false) || $_GET['zc'] == '1');
$iar	= (isset($_GET['iar']) && $_GET['iar'] == '1');

unset($_GET);
$_GET = array();

$_GET['src'] = $file . '.' . $ext;
if ($zc) 	 $_GET['zc']  = '1';
if ($width > 0)  $_GET['w']  = $width;
if ($height > 0) $_GET['h']  = $height;
if ($iar)	 $_GET['iar'] = '1';

// set DOCUMENT ROOT
chdir('system/external/thumb');

/** Required by i.php(system/externa/thumb/i.php). */
define('LOAD', true);


// headers
header("Cache-control: max-age");
header("Expires: max-age");
ob_start();

// include the blackbox
require 'i.php';

// send the checksum of the image.
header("ETag: ".sha1(ob_get_contents()));

/** Loads the error-page (404)
 * 
 * @ignore
 */
function throwError()
{
	
	$_GET['ys_route'] = 'error/trigger_404';
	include 'index.php';
	
}