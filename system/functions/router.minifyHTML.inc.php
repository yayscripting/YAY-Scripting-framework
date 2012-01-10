<?php
/** Minifies outputted HTML.
 * 
 * Checks for config-setting 'compress_html', and is called at the router __destruct-function.
 * 
 * @author YAY!Scripting
 * @package functions
 * @subpackage router
 */
 
try {
 	
 	$config = YS_Config::Load();
 	
	// compress
	if ($config->compress->html === true) {
		
		// check if headers allow minifying
		$headers = headers_list();
		foreach($headers as $header) {
			
			// file-transfer: return
			if ($header == "Content-Description: File Transfer")
				return;
		
			// other MIME-type	
			if (preg_match('/Content\-type: (^text\/html)/', $header))
				return;
				
		}
	
		// Thanks mwwaygoo @ http://www.php.net/manual/en/function.php-strip-whitespace.php#65245, 
		// I stole his textarea-proof function, and edited it.
		$exceptions = array('textarea', 'pre');
		
		$string = '';
		foreach($exceptions as $exception)
			$string .= ($string != '') ? '|'.$exception : $exception;
		
		$html = preg_replace_callback("/>[^<]*<\\/(".$string.")/i", "harden_characters", ob_get_clean());
		$html = preg_replace("/(\t|\n|\r)/", "", $html);
		$html = preg_replace("/<!--(.+?)-->/", "", $html);
		$html = preg_replace("/[\ ]{2,}/", " ", $html);
		$html = preg_replace_callback("/>[^<]*<\\/(".$string.")/i", "unharden_characters", $html);
		
		echo $html;
		
	}			

} catch (ConfigException $ex) {
	
	YS_Error::exceptionHandler($ex);
	
}

	
/** changes all newlines, tabs and spaces. Used in minifying HTML
 * 
 * @param array $array preg_replace_callback-array
 * @return string Hardened string.
 * @see unharden_characters
 */
function harden_characters($array)
{
	
	$safe=preg_replace("/\n/", "%0A", $array[0]);
	$safe=preg_replace("/\t/", "%09", $safe);
	
	return preg_replace("/\s/", "&nbsp;", $safe);
	
}
	

/** Opposite of {@link harden_characters}. Used in minifying HTML .
 * 
 * @param array $array preg_replace_callback-array
 * @return string Unhardened string.
 * @see harden_characters
 */
function unharden_characters($array)
{
	
	$safe=preg_replace('/%0A/', "\n", $array[0]);
	$safe=preg_replace('/%09/', "\t", $safe);
	
	return preg_replace('/&nbsp;/', " ", $safe);
	
}											