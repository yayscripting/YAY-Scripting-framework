<?php
/*	
	This controller will be called after visiting /admin.html while beeing logged in.
	Visiting this url as default has been defined in /config/environment.cfg.php.
*/
class Home Extends YS_Controller 
{
	
	
	public function index()
	{
		
		/* Changes the title set in runtime.php */
		$this->layout->setTitle('Admin panel | YAY!Scripting Framework');
		
		
		/* 
			When a view will be called right now, the mold admin.tpl will be used(/views/molds/admin.tpl). 
			The view /views/admin/home.tpl will be loaded (defined in /config/environment/cfg.php)
		*/
		$this->layout->view('home');
		
	}
	
}