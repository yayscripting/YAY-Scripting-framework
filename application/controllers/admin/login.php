<?php
/*	
	This controller will be called after visiting /admin.html or one of it childs like /admin/home.html while not beeing logged in.
	This route has been defined in /config/environment.cfg.php.
*/
namespace Application\Controller;

class Login Extends \System\Controller  
{
	
	public function index()
	{
		
		// redirect to /home.html, because there is where you need to login
		$this->helpers->http->redirect($this->language->getLang().'/home.html');
		
	}
	
}