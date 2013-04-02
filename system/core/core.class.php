<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace system;

/** Core
 * 
 * This class loads config, errors, helpers.
 * Sends default headers, and opens output buffering and sessions.
 *
 * @name Core
 * @package core
 * @subpackage Router
 */
class Core
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
		
		// set correct headers, prevent showing vurnable information
		header('Server: YAY!Scripting/Apache');
		header('X-Powered-By: YAY!Scripting_Framework');
		header("Content-type: text/html; charset=utf-8");
		header('X-UA-Compatible: IE=Edge,chrome=1');
		
		// make sure not to quit if the user stops executing a page
		ignore_user_abort(true);
	
		// load required core 'functions'
		require_once 'system/core/singleton.class.php';
		
		// load exceptions and error handlers
		require_once 'system/core/exception.class.php';
		require_once 'system/core/error.class.php';
		
		
		// load config
		$this->load_config();
		
		// handle sessions/output buffer
		session_set_cookie_params(3600 * 4);
		ob_start();
		
		// permanent session?
		if ($this->config->script->permanent_session) {
			
			if (empty($_COOKIE['ysf_session'])) {
				
				session_start();
				setcookie('ysf_session', session_id(), time() + 126147624/*+4years*/); 
				
			} else {
				
				session_id($_COOKIE['ysf_session']);
				session_start();
				
			}
			
		} else {
			
			session_start();
			
		}
		
		
		// load real core files
		require_once 'system/core/controller.class.php';
		require_once 'system/core/helpers.class.php';
		require_once 'system/core/models.class.php';
		
		// environment
		require_once 'system/core/environment.class.php';
		
		// language
		require_once 'system/core/language.class.php';
		
		// layout
		require_once 'system/core/layout.class.php';
		
		// event handler
		require_once 'system/core/event.class.php';
		
		
		// license check
		if (empty($this->config->license->license)) {
		
			header('X-YAY-License: unlicensed');
		
		}
		else if (!preg_match('/^([A-Z]{4}\-){3}[A-Z]{4}$/', $this->config->license->license)) {
		
			header('X-YAY-License: invalid license');
		
		} else {
		
			header('X-YAY-License: '.strtoupper(dechex(sprintf('%u',(crc32($this->config->license->license))))));
		
		}
		
		// load helpers
		$this->helpers = Helpers::Load();
		
		// system check
		$this->systemCheck();
		
	}
	
	/** checks if permissions are set correctly.
	 * 
	 * 
	 */
 	public function systemCheck()
 	{
 		if (substr(decoct(fileperms('cache')),1) != '0777')
 			die("CHMOD 'cache/' to 0777");
 		
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
		$this->config = Config::Load();
	
	}
		
}
?>