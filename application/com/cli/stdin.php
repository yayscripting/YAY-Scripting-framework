<?php

/** Example of STDIN controlling via CLI */

namespace Application\Controller;

class Stdin Extends \System\Controller 
{

	public function index()
	{
		
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		
		var_dump(json_decode($line, true));
				
	}
	
}