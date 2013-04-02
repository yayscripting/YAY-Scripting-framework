<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Helper;

/** Debug-helper
 * 
 * This helper can be used to check the values of some variables, handled well by the layout.
 *
 * @name debug
 * @package helpers
 * @subpackage debug
 */
class Debug extends \System\Helper
{
	
	/** Shows the content of a variable, in var_dump-style.
	 * 
	 * This functions does also send PRE-tags, and puts the debug in the right spot of the layout.
	 * 
	 * @access public
	 * @param mixed $variable Variable to check
	 * @return void
	 */
	public function var_dump($variable)
	{
		
		$this->dump($variable, false);
		
	}
	
	/** Shows the content of a array, in print_r-style.
	 * 
	 * This functions does also send PRE-tags, and puts the debug in the right spot of the layout.
	 * 
	 * @access public
	 * @param array $variable array to check
	 * @return void
	 */
	public function print_r($variable)
	{
		
		$this->dump($variable, true);
		
	}
	
 	/** Shows a dump of a variable.
 	 * 
 	 * This functions does also send PRE-tags, and puts the debug in the right spot of the layout.
 	 * 
 	 * @access private
 	 * @param mixed $variable Variable to show.
 	 * @param bool $print_r Use print_r instead of var_dump
 	 * @return void
 	 */
	private function dump($variable, $print_r = false)
	{
				
		// remember old
		$prev = ob_get_clean();
		ob_start();
		
		// dump
		if ($print_r)
			print_r($variable);
		else
			var_dump($variable);
		
		// catch dump
		$dump = "<pre>".htmlspecialchars(ob_get_clean())."</pre><hr /><br /><br />";
		ob_start();
		
		// echo remembered
		echo $prev;
		
		// get debug var
		$all_tpl_vars = \System\Layout::Load()->getTemplateVars();
		
		// assign dump
		\System\Layout::Load()->assign('debug', $all_tpl_vars['debug'].$dump);
	
	}
}