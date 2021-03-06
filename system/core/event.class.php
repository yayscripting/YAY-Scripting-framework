<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System;

/** Load the actual event holder */
require_once 'application/events.class.php';

/** Event controller
 * 
 * This triggers all events.
 *
 * @name Event
 * @package core
 * @subpackage Event
 */
class Events extends Singleton
{
	
	/** Contains the event
	 * 
	 * @access private
	 * @var object
	 */
	private $event;
	
	/** Constructor, loads the event
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{

		$this->event = new \Application\Event();
		
	}
	
	/** Fires an event
	 * 
	 * While fireing an event, you can pass serveral parameters like this:
	 * <code>
	 * $this->event->fire('runtime', 'Parameter_1', array('parameter_2'), 0xBEEF);
	 * </code>
	 * 
	 * @param string $event Name of the event to call
	 * @param mixed $param Optional parameter to pass to the function. There are unlimited parameters to send.
	 * @access public
	 * @return void
	 */
	public function fire($event)
	{
		
		// get args
		$args = func_get_args();
		unset($args[0]);
		sort($args);
		if (empty($args))
			$args = array();
		
		// system functions:
		if (method_exists($this->event, '__'.$event))
			call_user_func_array(array($this->event, '__'.$event), $args);
		
		
		// user functions
		if (method_exists($this->event, $event))
			return call_user_func_array(array($this->event, $event), $args);
		
		
	}
	
	/** Passes the controller to the actual event
	 * 
	 * @access public
	 * @param object $controller The actual controller
	 * @return void
	 */
	public function injectController($controller)
	{
		
		$this->event->injectController($controller);
		
	}

}

/** Event data
 * 
 * This class provides access to all data, so it can be accessed in the actual event(by extending).
 *
 * @name EventData
 * @package core
 * @subpackage Event
 */
class Event
{
	
	/** Config
	 * 
	 * @access protected
	 * @var object
	 */
	protected $config;
	
	/** Helpers
	 * 
	 * @access protected
	 * @var object
	 */
	protected $helpers;
	
	/** Models
	 * 
	 * @access protected
	 * @var object
	 */
	protected $models;
	
	/** Layout
	 * 
	 * @access protected
	 * @var object
	 */
	protected $layout;
	
	/** Environments
	 * 
	 * @access protected
	 * @var object
	 */
	protected $environment;
	
	/** Controller
	 * 
	 * @access protected
	 * @var object
	 */
	protected $controller = null;
	
	/** Constructor, loads all data and assigns them to object variables.
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		
		$this->helpers = Helpers::Load();
		$this->models = Models::Load();
		$this->config = Config::Load();
		$this->layout = Layout::Load();
		$this->environment = Environment::Load();
		
		$this->controller = (object) array();
		
	}
	
	/** Adds the controller to the container, for setting variable purposes
	 * 
	 * @access public
	 * @param object $controller The controller
	 * @return void
	 * @final
	 */
	final public function injectController($controller)
	{
		
		$values = $this->controller;
		$this->controller = $controller;
		
		if (!empty($values))
			foreach ($values as $key => $value)
				$this->controller->$key = $value;
		
		
	}
	
	final public function __runtime()
	{
		
		$this->layout->assign('lang', Language::Load()->getLang());
		$this->layout->assign('language', Language::Load()->getLang());
		$this->layout->assign('langURL', Language::Load()->getURL());
		
	}
	
	final public function __done()
	{
		
		$lang = $this->controller->language->getUrl();
		if (is_null($lang)) {
			
			$lang = '/';
			
		} else {
			
			$lang .= '/';
			
		}
		
		$env = $this->controller->environment->get();
		if ($env  === false) {
			
			$env = '';
			
		} else {
			
			$env = $env . '/';
			
		}
		
		$_SESSION['LAST_LOADED'] = $lang.$env.implode('/',$this->controller->route) . '.html';
		
	}

}
