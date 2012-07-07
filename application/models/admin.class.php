<?php
/*
	The prefix YS_ is required to prevent collisions.
*/
class YS_Admin extends YS_ModelController
{

	public function __construct($sql)
	{
	
		// prepare parameters
		$parameters = array();
		$parameters['table'] 		= 'admin'; // The name of the table
		$parameters['primaryKeyField']	= 'id';	  // Primary key column name
		$parameters['fields']		= array('id', 'username', 'password'); // All columns in the table, including the primary key.
		
		/* 
			By only adding these lines, the password will be stored encrypted (Not hashed! You should still do this, but this will be explained in the example-controller.)
			Adding these lines means that you cant perform a order by or where-clause with this colum.
			If you add the iv-parameter (you can manually generate an IV with the function 
			$this->helpers->encryption->create_iv()), you will be able to perform a search with a where-clause.
			However, adding an IV may slightly decrease the strength of the encryption.
			
			You can use this encryption for any kind of sensitive material that must still be decrypted (like creditcard credentials, or personal information).
		*/
		$parameters['encryption']['password']	= array(
		
			'key'	 => 'wqhoMmaTz=E_JFU0.I[zM.-dYyRRl2v"?~eeLMO&sNC2JIPuIp,wM<#Dv-1HD?', // just some random characters
			'salt'	 => '0RTh()Ys1TO@TY!wpowqpZJieW~oQ$(&[s$l!L97uyBG7t=|=VZ|CZrVC,=Y|CMt@?5lf9l^5'// the same.
			
			);
		
		// call parent
		parent::__construct($sql, $parameters);
	
	}

}