<?php
/*
	At first, i would like to say that this way of creating a form still is quite dirty, maybe we will improve this kind of notation.
*/

/* 
	Create a new form, $this is working because this file is included in the controller-class. 
	This is a required statement.
*/
$form = $this->helpers->form->newForm();

	/* Create a new input, with type 'text' and name 'username'. */
	$form->input('text', 'username')
		/* 
			Note the missing semi-column(;) at the previous line, 
			this is because all input-functions(even as almost all of the setting-attribute functions return the 
			input itself. 
			
			The function on the next line is making sure that there will be thrown an error if they leave this input blank.
		*/
		->setRequired(true, __('RequireUsername'));
		
	/* Creating the password-input */
	$form->input('password', 'password')
		/* Using a regex to validate the input. (multiple are allowed, see documentation)*/
		->setValidator(array('/^[a-zA-Z0-9]+$/' => __('RequirePassword')));
		

/* Returns the form, so it will be send correctly to the controller. */
return $form;