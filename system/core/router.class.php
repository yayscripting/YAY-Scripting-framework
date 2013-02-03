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

	/** Mode
	 * 
	 * CLI, Cronjob, COM or Browser
	 * 
	 * @access public
	 * @var string $mode
	 */
	public $mode;

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

		// set mode
		$this->setMode();

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

		// minify HTML
		require $this->cwd . '/system/functions/router.minifyHTML.inc.php';
		
		// call timer
		if ($this->config->script->show_render_time === true && $this->config->script->debug_mode === true && $this->mode == 'browser')
			echo '<!-- RENDER TIME: ' . substr((STRING) abs(microtime(true) - $this->_timer), 0, 6) . ' seconds -->';

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
		
		// fire event
		YS_Events::Load()->fire('router');

		// get route
		$route = $this->parseRoute();

		// check for error-'controller'
		if (isset($route[0]) && $route[0] == 'error') {

			$error = intval((substr($route[1], 0, 8) == 'trigger_') ? substr($route[1], 8) : $route[1]);

			$this->error->http_error($error, true);
			return;

		}

		// get env
		$env = YS_Environment::Load();
		$environment = $env->get();
		
		// determine controller name/position
		$controller = (!empty($route[0])) ? $route[0] : (($environment === false) ? $this->config->script->default_controller : $env->default_controller);
		$controller = strtolower($this->helpers->string->url_safe($controller));

		// check SEO
		if($this->config->script->force_seo === true && $controller == $this->config->script->default_controller && empty($route[0]) == false && $route[0] == $controller && empty($route[1])){

			// check postdata
			if($_SERVER['REQUEST_METHOD'] != "POST"){

				// redirect
				$this->helpers->http->redirect('/', 301);

			}

		}

		// get controller folder
		// admin?
		if($environment !== false) {

			$prefix = $env->folder . '/';

			// check if he's not logged in.
			if ($env->login && $env->loggedin() == false) {

				$controller = $route[0] = $env->login_controller;
				$route[1] = 'index';

				// save referrer
				if ($route[0] != 'error')
					$_SESSION['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];

			}

		} else {

			$prefix = '';

		}

		$folder = $this->getControllerFolder();
		
		// verify
		if (file_exists('application/' . $folder.$prefix.$controller.'.php')) {

			// load			
			require_once('application/' . $folder.$prefix.$controller.'.php');

			$method = (!empty($route[1])) ? $route[1] : 'index';
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
			$class->injectRoute($route);
			if (method_exists(ucfirst($controller), $method) === false)
				eval('$class->__call("'.$method.'", null);');
			else
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

	/** Parses the route into segments, and handles the environment/language system
	 * 
	 * @access private
	 * @return array Routesegments
	 */
	private function parseRoute()
	{

		// cut into segments
		$route = (empty($_GET['ys_route'])) ? array() : $routes = explode('/', $_GET['ys_route']);
		
		// parse language
		if ($this->config->language->language_on && $this->config->language->default_language != null && ($this->mode == 'browser' || $this->mode == 'com')) {
		
		
			// lang exists?
			if (isset($_GET['ys_lang']) && !file_exists('application/language/'.preg_replace('/[^a-zA-Z]/s', '', $_GET['ys_lang']).'.lang.php')) {
				
				YS_Language::Load()->setRoute(array($this->config->language->default_language));
				$this->error->http_error(404);
				
			}
			
			// parse use_slash
			if ($this->config->language->use_slash && empty($_GET['ys_lang']) == false) {

				if (file_exists('application/language/'.preg_replace('/[^a-zA-Z]/s', '', $_GET['ys_lang']).'.lang.php'))
					$route = array($_GET['ys_lang']);
				else
					$this->error->http_error(404);


			}
			else if ($this->config->language->use_slash == false && isset($_GET['ys_lang'])) {

				$this->error->http_error(404);

			}
  			
			// non-existing language
			if ($this->config->language->language_on && false === file_exists('application/language/'.preg_replace('/[^a-zA-Z]/s', '', $route[0]).'.lang.php')) {

				if ($route[0] != $this->config->language->default_language && false == is_null($this->config->language->default_language)) {
				
					if (YS_Events::Load()->fire('determineLanguage') === false)  {
					
						exit;
					
					} else {
						
						// detect default language
						$browser_language = preg_replace('/[^a-zA-Z]/s', '', strtolower(substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2)));
						
						if(file_exists('application/language/'.$browser_language.'.lang.php'))
							$default_language = $browser_language;
						else					
							$default_language = $this->config->language->default_language;
						
						// redirect
						if (empty($route))
							$this->helpers->http->redirect('/'.$default_language . ($this->config->language->use_slash ? '/' : '.html'));
	
						$this->helpers->http->redirect('/'.$default_language.implode('/', array_merge(array(''), $route)).'.html');
					
					}
					
				}

				// 500-error
				$route[0] = null;
				$route[1] = 'error';
				$route[2] = '500';

			} else if ($this->config->language->language_on && empty($_GET['ys_lang']) && count($route) == 1 && $this->config->language->use_slash) {

				$this->helpers->http->redirect('/'.$route[0].'/');

			}


			if ($this->config->language->language_on) {		

				// inform the language-class
				$lang = YS_Language::Load();
				$lang->setRoute($route);

				// delete language-prefix
				array_shift($route);

			}

		}

		// get environment
		$env = YS_Environment::Load();
		$env->setRoute($route);
		$environment = $env->get();
		
		if ($environment !== false)
			array_shift($route);

		if (empty($route[0])) {

			$route[0] = ($environment === false) ? $this->config->script->default_controller : $env->default_controller;

		}

		if (empty($route[1]))
			$route[1] = 'index';		

		return $route;

	}

	/** Sets the core in the right mode
	 * 
	 * @access private
	 * @return void
	 */
	private function setMode()
	{

		// modes
		$modes = array('cli', 'com', 'cronjob', 'browser');

		// com check
		if(in_array($_GET['ys_mode'], $modes)){

			// set mode
			$this->mode = $_GET['ys_mode'];

			// check cli
			if(PHP_SAPI == 'cli' && ($this->mode != 'cli' && $this->mode != 'cronjob')){

				exit("Invalid PHP mode. [0]");

			}else
			if(PHP_SAPI != 'cli' && ($this->mode == 'cli' || $this->mode == 'cronjob')){

				exit("Invalid PHP mode. [1]");

			}

		}else{

			// set
			$this->mode = 'browser';

		}

	}

	/** Gets the controller folder
	 * 
	 * @access private
	 * @return string
	 */
	private function getControllerFolder()
	{

		// set folders
		$folders = array(
			'cli'		=> 'com/cli/',
			'com'		=> 'com/',
			'cronjob'	=> 'com/cronjob/',
			'browser'	=> 'controllers/'	
		);

		// return
		return (!empty($folders[$this->mode])) ? $folders[$this->mode] : $folders['browser'];

	}

}