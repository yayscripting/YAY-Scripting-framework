<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Core
 * 
 * This class loads the right page and handles exceptions.
 *
 * @name Router
 * @package core
 * @subpackage Router
 */
class YS_Router Extends YS_Core
{
	
	/** Startime
	 * 
	 * Used to check loadTime
	 * 
	 * @access public
	 * @var int $_timer
	 */
	public $_timer;
	
	/** Working directory, is deleted on shutdown
	 * 
	 * @access private
	 * @var string $cwd
	 */
 	private $cwd;
 	
 	/** Error handler
 	 * 
 	 * @access private
 	 * @var $error
 	 */
	private $error;
	 	
	/** Constructor
	 * 
	 * @access public
	 * @return YS_Router
	 */
	public function __construct() 
	{
		// set things to remember
		$this->_timer = microtime(true);
		$this->cwd = getcwd();

		// config+helpers
		parent::__construct();
		
		// load errorhandler
		$this->error = YS_Error::Load();
		
		// load the right controller
		$this->load_controller();
		
	}
	
	/** Minifies outputted HTML
	 * 
	 * Calls on exit.
	 * 
	 * @access public
	 * @return void
	 */
	public function __destruct()
	{
		
		// fire event
		YS_Events::Load()->fire('shutdown');
		
		// call timer
		if ($this->config->script->show_render_time === true)
			echo '<!-- RENDER TIME: ' . substr((STRING) abs(microtime(true) - $this->_timer), 0, 6) . ' seconds-->';
		
		// minify HTML
		require $this->cwd . '/system/functions/router.minifyHTML.inc.php';
		
	}
	/** Loads the right controller
	 * 
	 * This function does also catch all exceptions described in errors.class.php
	 * 
	 * Calling this function triggers the event 'router' before it loads a controller.
	 * At script shutdown the event 'shutdown' will be called.
	 * 
	 * @access private
	 * @return void
	 */
	private function load_controller() 
	{
		
		// get environment
		$env = YS_Environment::Load();
		$environment = $env->get();
		
		// fire event
		YS_Events::Load()->fire('router');
		
		// check for error-'controller'
		if (isset($_GET['a']) && $_GET['a'] == 'error') {
			
			$error = intval((substr($_GET['b'], 0, 8) == 'trigger_') ? substr($_GET['b'], 8) : $_GET['b']);
			
			$this->error->http_error($error, true);
			return;
			
		}
		
		// switch all GET-(location-)vars 1 spot. (a/b/d/f/e/f/g/h)
		if ($environment !== false)
			require_once 'system/functions/router.handleGET.inc.php';
		
		// determine controller name/position
		$controller = (!empty($_GET['a'])) ? $_GET['a'] : (($environment === false) ? $this->config->script->default_controller : $env->default_controller);
		$controller = strtolower($this->helpers->string->url_safe($controller));
		$folder = (!empty($_GET['com'])) ? (!empty($_GET['cronjob']) ? 'com/cronjob/' : 'com/') : 'controllers/';
		
		// admin?
		if($environment !== false) {
			
			$prefix = $env->folder . '/';
			
			// check if he's not logged in.
			if ($env->login && $env->loggedin() == false) {
				
				$controller = $_GET['a'] = $env->login_controller;
				$_GET['b'] = 'index';
				
				// save referrer
				if (substr(strtolower($_SERVER['REQUEST_URI']), strlen($_SERVER['REQUEST_URI']) - strlen($controller) - 5) != $controller . '.html' && substr(strtolower($_SERVER['REQUEST_URI']), strlen($_SERVER['REQUEST_URI']) - 14) != 'uitloggen.html')
					$_SESSION['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];
					
			}
		
		} else {
		
			$prefix = '';
		
		}
		
		// verify
		if (file_exists('application/' . $folder.$prefix.$controller.'.php')) {
			
			// load			
			require_once('application/' . $folder.$prefix.$controller.'.php');
						
			$method = (!empty($_GET['b'])) ? $_GET['b'] : 'index';
			$method = strtolower($this->helpers->string->url_safe($method));
			
			// verify
			if (method_exists(ucfirst($controller), $method) === false && method_exists(ucfirst($controller), '__call') === false) {
				
				$this->error->routerError();
				
			}
			
		} else {
			
			$this->error->routerError();
		
		}
		
		// load controller
		try {
			
			eval('$class = new '.ucfirst($controller).'();');
			eval('$class->'.$method.'();');
			
		}
		
		// error handling
		catch (CoreException	  $ex)	{ }
		catch (DatabaseException  $ex)	{ }
		catch (QueryException	  $ex)	{ }
		catch (ConfigException	  $ex)	{ }
		catch (LoadException	  $ex)	{ }
		catch (FormException	  $ex)	{ }
		catch (HelperException	  $ex)	{ }
		catch (SingletonException $ex)	{ }
		
		if (isset($ex))
			$this->error->http_error(500, true, $ex->errorMessage().'<br /><br /><small>'.$ex->fullMessage().'</small>');
		
		
	}
	
}