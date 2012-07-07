<?php

class Logout extends YS_Controller 
{
	
	
	public function index()
	{
		
		// logout from environment
		$this->environment->logout('admin');
		
		// redirect to login
		$this->helpers->http->redirect($this->language->getLang().'/home.html');
		
	}
	
	
}