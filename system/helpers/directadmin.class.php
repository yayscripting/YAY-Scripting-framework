<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Helper;

/**
 * Include DirectAdmin
 */
require_once 'system/external/directadmin/DirectAdmin.php';

/** DirectAdmin-helper
 * 
 * This helper can be user for a connection with DirectAdmin
 *
 * @name mail
 * @package helpers
 * @subpackage mail
 */
class Directadmin extends \System\Helper
{
	
	public function login($username, $password, $host)
	{
	
		$this->daClass = new \DirectAdmin('http://'.$username.':'.$password.'@'.$host);
	
	}
	
	public function restartApache($username, $password, $host)
	{
		if(!is_object($this->daClass)){
		
			$this->login($username, $password, $host);
		
		}
	
		$retrieved = $this->daClass->retrieve(
			array(
				'method' => 'GET',
				'command' => 'CMD_API_SERVICE',
				'data' => array('action' => 'restart', 'service' => 'httpd')
			)
		);
		
		return true;
	}
	
}