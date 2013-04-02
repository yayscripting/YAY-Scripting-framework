<?php
/** strips slashes of the POST-variable
 * 
 * @author YAY!Scripting
 * @package functions
 * @subpackage controller
 */

if (!function_exists('strip_slashes')) {
	

	/** Recursive function, deletes slashes even in a array
	 * 
	 * @param array $array
	 * @return array $modified
	 */
	function strip_slashes(array $array)
	{
		
		foreach ($array as &$item) {
			
			if (is_array($item)) {
				
				$item = strip_slashes($item);
			
			} else {
				
				$item = stripslashes($item);
				
			}
			
		}
		
		return $array;
		
	}
}
 
// check if magic-quotes is enabled
if (get_magic_quotes_gpc())
	$_POST = strip_slashes($_POST);
