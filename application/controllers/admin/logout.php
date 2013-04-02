<?php
namespace Application\Controller;


class Logout extends \System\Controller  
{
	
	
	public function index()
	{
		
		// logout from environment
		$this->environment->logout('admin');
		
		// redirect to login
		$this->helpers->http->redirect('/'.$this->language->getLang().'/home.html');
		
	}
	
	
}