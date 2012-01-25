<?php

/*	
	The default controller is home,
	Adding new controllers just requires you to add a new file to the controllers-directory, with the name 'NAME.php'
	This file should contain a class with the name of that controller with only the first letter being capitalized.
	
	While testing this, make sure that you have filled in /config/database.cfg.php correctly, 
	and that you have created a table with this structure:
		title: admin
		field 1: id - int(11)
		field 2: username - varchar(255)
		field 3: password - text
	and after that visited /home/preparedatabase.html once.
	
*/
class Home Extends YS_Controller 
{
	
	/*
		 The constructor isn't really neccesary in this case. 
		 But using this function could be usefull if you've got multiple functions which all require a specific model by loading that specific model and saving it as a object variable.
 	*/
	public function __construct()
	{
			
		// parent constructor
		parent::__construct();	
		
	}
	
	/* 
		This is the default method to be called, if there is no specific method given in the url.
		This controller is called by visiting /home.html or /home/index.html or the homepage itself(/).
	*/
	public function index()
	{
		
		/* 
			Example of loading a form.
			The form being loaded right here is described in /forms/login.php
		*/
		$form = $this->form('login');
		
		/* 
			Checks if someone has filled in the form correctly.
			The given parameter is the title of the error-message to show if anything went wrong.
		*/
		if ($form->validate('Something went wrong.')) {
			
			/* Loads a model (/model/admin.class.php) */
			$admin = $this->models->admin;
			
			/* Get the filled in values, this only contains the values which were defined in the form-file. */
			$values = $form->values();
			
			/* Selects the row with the login credentials */
			$selection = $admin->select(array('username', 'password'), array('username' => $values['username']));
			
			/* checks if there is a row selected. */
			if ($selection->num_rows > 0) {
				
				/* hash the password with PBKDF2 to match it against the one found in the database. */
				$values['password'] = $this->helpers->encryption->pbkdf2($values['password'], 'aSecr3tKeyToHashWith(usually 128/256 bytes long)', 256, 2000, 'sha512');
				/*
					Some more comment on the above line:
					The part of the code $this->helpers->encryption->pbkdf2 loads the class YSH_Encryption in the file /system/helpers/encryption.class.php.
					We are inviting you to create your own helpers if you are missing some functionalities. 
					And please, if you do so, send them to us so we could review them, and possibily even add it to the default helpers-package.
					
					Helpers are only loaded if they are beeing accessed, so adding more of them to the basic package is not slowing the framework in any matter.
					
				*/
				
				/* Opens the first row, if you have multiple rows you could loop through them like this:
				
					while ($selection->fetch()) {
						
						echo $selection->data->row;
						
					}
				*/
				$selection->fetch();
				
				/* Match data against hashed password */
				if ($selection->data->password == $values['password']) {
					
					// log in into the environment
					$this->environment->login('admin');
					
					// redirect to admin
					$this->helpers->http->redirect('/admin.html');
					
				} else {
					
					/* Wrong password */
					$this->layout->show_error('Something went wrong', "You've entered the wrong password");
					
				}
				
			} else {
				
				/* The username was nog found in the database */
				$this->layout->show_error('Something went wrong.', 'That username does not exist.');
				
			}
			
		}
		
		/* Assign the current state of the form (with remembered values, etc. to the template. */
		$this->layout->assign('form', $form->build());
		
		/* Loads a template: views/pages/home.tpl */
		$this->layout->view('home');
		
	}
	
	/*
		This method is being called if the url /home/information.html has been called.
	*/
	public function information()
	{
		
		echo 'loaded /home/information.html<br /><br />';
		echo '$this->get[\'a\'] = \'home\', and $this->get[\'b\'] = \'information\'<br />';
		echo 'The maximum of the GET-parameters given as a path is 8.';
		
	}
	
	// run this function to enter a new username/password into the database, make sure the table exists like defined in /models/admin.class.php
	// run by visiting /home/preparedatabase.html
	public function preparedatabase()
	{
		
		$username = 'admin';
		$password = 'password';
		
		$this->models->admin->insert(array(
		
			'username' => $username, 
			'password' => $this->helpers->encryption->pbkdf2($password, 'aSecr3tKeyToHashWith(usually 128/256 bytes long)', 256, 2000, 'sha512')
			
			)
		);
		
	}
	
}