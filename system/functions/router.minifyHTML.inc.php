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
			if (substr($header, 0, 13) == 'Content-type:' && trim(preg_replace('/^Content\-type:(.+?)(;.+?)?$/', '\\1', $header)) !== 'text/html')
				return;
				
		}
	
		// get minify exceptions
		$exceptions = array('textarea', 'pre', 'script');
		
		// get current buffer
		$html = ob_get_clean();
		
		// minify!
		$html = preg_replace('#(?ix)(?>[\ ]{2,})(?=(?:(?:[^<]++|<(?!/?(?:'.implode('|', $exceptions).')\b))*+)(?:<(?>'.implode('|', $exceptions).')\b|\z))#', ' ', $html);
		$html = preg_replace('#(?ix)(?>[^\S ]\s*|\s{2,})(?=(?:(?:[^<]++|<(?!/?(?:'.implode('|', $exceptions).')\b))*+)(?:<(?>'.implode('|', $exceptions).')\b|\z))#', '', $html);
		
		// last but not least, delete comments
		$html = preg_replace("/<!--(.+?)-->/", "", $html);
		
		echo $html;
		
	}

} catch (ConfigException $ex) {
	
	YS_Error::exceptionHandler($ex);
	
}