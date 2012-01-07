<?php
return array(

	'register_error_handler'     => true,
	'error_handler_types' 	     => E_ALL ^ ( E_DEPRECATED  | E_USER_DEPRECATED  | E_NOTICE  ), //E_WARNING causes even errors when MySQL-server is offline, so make sure that is not selected.
	'register_exception_handler' => true,
	'fatal_error_handler' 	     => true
	
);