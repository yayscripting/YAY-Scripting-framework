<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Core
 * 
 * This class loads config, errors, helpers.
 * Sends default headers, and opens output buffering and sessions.
 *
 * @name Core
 * @package core
 * @subpackage Router
 */
class YS_Core
{
	
	/** All config-data
	 * 
	 * @access public
	 * @var array $config Config
	 */
	public $config;
	
	/** All helpers
	 * 
	 * @access public
	 * @var YS_Helpers Helpers
	 */
	public $helpers;
	
	/** Constructor
	 * 
	 * Loads everything, sends headers.
	 * 
	 * @access public
	 * @return YS_Core
	 */
	public function __construct() 
	{
		
		// set correct headers, prevention showing vurnable information
		header('Server: YAY!Scripting/Apache');
		header('X-Powered-By: YAY!Scripting_Framework');
		header("Content-type: text/html; charset=utf-8");
		
		// make sure not to quit if the user stops executing a page
		ignore_user_abort(true);
	
		// start session, buffer
		session_start();
		ob_start();
		
		// load required core 'functions'
		require_once 'system/core/singleton.class.php';
		
		// load exceptions and error handlers
		require_once 'system/core/exception.class.php';
		require_once 'system/core/error.class.php';
		
		// load real core files
		require_once 'system/core/environment.class.php';
		require_once 'system/core/controller.class.php';
		require_once 'system/core/helpers.class.php';
		
		// load config
		$this->load_config();
		
		// load controller basic-class
		$this->helpers = YS_Helpers::Load();
		
	}
	
	/** Loads config
	 * 
	 * @access public
	 * @return array Config
	 */
	public function load_config()
	{
	
		// load config
		require_once('system/core/config.class.php');
				
		// save
		$this->config = YS_Config::Load();
		
		// make it global. 
		global $_config;
		$_config = $this->config;
	
	}
		
}
?>