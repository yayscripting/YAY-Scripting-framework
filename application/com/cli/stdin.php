<?php

/** Example of STDIN controlling via CLI */

namespace Application\Cli;

class Stdin Extends \System\Controller 
{

	public function index()
	{
		
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		
		var_dump(json_decode($line, true));
				
	}
	
}