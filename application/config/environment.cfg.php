<?php
return array(
	
	/* Add more, for more environments. */
	'admin' => (object) array
	(
	
		'enabled' => true,		// obvious
	 	'trigger' => 'admin',		// prefix in the url to access the admin-area
	 	'folder'  => 'admin',		// folder(in /controllers) where the controller are found.
	 	'default_controller' => 'home',	// default controller to open
	 	'login' => true,		// is logging in required?
	 	'login_controller' => 'login'	// if not logged in, redirect to this controller.
	
	)

);