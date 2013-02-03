<?php

/** Example of STDIN controlling via CLI */

class Stdin Extends YS_Controller 
{

	public function index()
	{
		
		$handle = fopen ("php://stdin","r");
		$line = fgets($handle);
		
		var_dump(json_decode($line, true));
		
		
		var_dump($this->language->getLang());
				
	}
	
}