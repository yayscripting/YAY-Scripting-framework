<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** String-helper
 * 
 * This helper can be used to manipulate or validate strings with different kind of patterns
 *
 * @name string
 * @package helpers
 * @subpackage string
 */
class YSH_String extends YS_Helper
{
	
	/** Manipulate string to make it url-safe.
	 * 
	 * After the string has gone through this function, it will no longer contain any kind of dots or slashes.
	 * 
	 * @access public
	 * @param string $string input
	 * @return string Safe string
	 */
	public function url_safe($string)
	{
		
		// return
		return str_replace('-', '_', preg_replace("/(\.|\/|\\\)/", '', $string));
	
	}
	
	/** Create a Search Engine Optimized and url-safe string.
	 * 
	 * This function will create a Search Engine Optimized string that does not contain slashes or dots.
	 * 
	 * @access public
	 * @param string $string String to get optimized.
	 * @return string Optimized string
	 */
	public function seo($string){
		
		// replace wrong entities
		$string = preg_replace("`\[.*\]`U","",$string);
		$string = preg_replace('`&(amp;)?#?[a-z0-9]+;`i','-',$string);
		$string = htmlentities($string, ENT_COMPAT, 'utf-8');
		$string = preg_replace( "`&([a-z])(acute|uml|circ|grave|ring|cedil|slash|tilde|caron|lig|quot|rsquo);`i","\\1", $string );
		$string = preg_replace( array("`[^a-z0-9]`i","`[-]+`") , "-", $string);
		
		// trim and lower string, then return
		return strtolower(trim($string, '-'));
		
	}
	
	/** Strip PHP-added slashes from an array
	 * 
	 * This function is recursive, and does handle all values of an array.
	 * 
	 * @access public
	 * @param array &$array
	 * @return void
	 */
	public function strip_slashes_deep(&$array){
	
		if(get_magic_quotes_gpc()){
				
			foreach($array as $k => &$p){
			
				// check array
				if(is_array($p)){
				
					$this->strip_slashes_deep(&$array[$k]);
				
				}else{
			
					// strip
					$p = stripslashes($p);
				}
			}
			
		}
			
	}
	
	/** Checks if a string is a valid dir name
	 * 
	 * This function does only accept letters, numbers, - and _. And accepts a max of 30 characters
	 * 
	 * @access public
	 * @param string $name Name to validate
	 * @return bool
	 */
	public function is_valid_dir_name($name)
	{
		
		return preg_match("/^[a-zA-Z0-9_-]{1,30}$/", $name);
		
		
	}
	
}