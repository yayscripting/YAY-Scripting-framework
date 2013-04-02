<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System;

/** Controller
 * 
 * This class loads corefiles, models and forms.
 *
 * @name Controller
 * @package core
 * @subpackage Controller
 */
class Controller
{

	/** Helpers
	 * 
	 * @access public
	 * @var YS_Helpers $helpers
	 */
	public $helpers;
	
	/** Models
	 * 
	 * @access public
	 * @var YS_Models $models
	 */
	public $models;
	
	/** Config
	 * 
	 * @access public
	 * @var array $config
	 */
	public $config;
	
	/** GET-vars
	 * 
	 * @access public
	 * @var array $get
	 */
	public $get; 
	
	/** References
	 * 
	 * All loaded core-files
	 * 
	 * @access private
	 * @var array $references
	 */
	private $references = array();
	
	/** Environment
	 * 
	 * @access protected => EDITED for 123c
	 * @var YS_Environment
	 */
 	public $environment;
 	
	/** Error
	 * 
	 * @access protected
	 * @var YS_Error
	 */
 	protected $error;
 	
	/** Event handler
	 * 
	 * @access protected
	 * @var YS_Event
	 */
 	protected $event;
 	
	/** Lanugage handler
	 * 
	 * @access protected => EDITED for 123c
	 * @var YS_Language
	 */
 	public $language;
 	
 	/** The route
 	 * 
 	 * @access protected => EDITED for 123c
 	 * @var array
 	 */
 	public $route;
 	
	/** Constructor
	 * 
	 * Loads layout, helpers and config.
	 * This function calls the 'runtime' event
	 * 
	 * @access public
	 * @return YS_Controller
	 */
	public function __construct()
	{
		
		$this->helpers = Helpers::Load();
		$this->config  = Config::Load();
		$this->models  = Models::Load();
		
		// environment
		$this->environment = Environment::Load();
		
		// language
		$this->language = Language::Load();
			
		// escape POST
		require_once 'system/functions/controller.handlePOST.inc.php';
			
		// set 
		$this->get = $_GET;
		$this->post = $_POST;
		
		// error
		$this->error = Error::Load();
		
		// load smarty
		try {
			
			$this->layout = Layout::Load();
		
		} catch (exception $ex) {
			
			die("De layout-parser kon niet geladen worden.<br /><br /><em>".$ex->errorMessage()."</em>");
			
		}
		
		// loads the event module
		$this->event = Events::Load();
		$this->event->injectController($this);
		Events::Load()->fire('runtime');
		
	}
	
	/** Injects the route into the controller
	 * 
	 * @access public
	 * @param array $route The route
	 * @return void
	 */
	final public function injectRoute(array $route)
	{
		
		$this->route = $route;
		
	}
	
	/** Loads a class-file
	 * 
	 * Class found in /application/classes/NAME.class.php. As a constructor-parameter this class has been given, to contact the helpers/config/models.
	 * Class name should be the lowerstring version of the first parameter, but only with the first letter being capitalized.
	 * 
	 * Calling this function triggers the event 'loadClass'.
	 * 
	 * @access public
	 * @param string $className Name of the class(file)
	 * @return object Created class
	 */
 	final public function loadClass($className)
 	{
 		
 		// fire event
 		$this->event->fire('loadClass', $className);
 		
 		// include class
 		require 'application/classes/'.$className.'.class.php';
 		
 		
 		// create new object
 		$object = ucfirst(strtolower($className));
 		
 		return new $object($this);
 		
	}
	
	/** Loads a core-file
	 * 
	 * This function accepts any number of parameters, which are the names of the model.
	 * Example in a controller:
	 * <code>
	 * // load a corefile
	 * $this->load('database');
	 * 
	 * // load two corefiles
	 * $this->load('database', 'layout');
	 * 
	 * // the corefile can be accessed using '$this->' in a controller.
	 * $this->database->query("SELECT * FROM table");
	 * </code>
	 * 
	 * If you'ld rather use ->sql to access the database, you can use this:
	 * <code>
	 * // load the corefile
	 * $this->load(array('database', 'sql'));
	 * 
	 * // access corefile
	 * $this->sql->query("SELECT * FROM table");
	 * </code>
	 * 
	 * @access public
	 * @param mixed $input input
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @param mixed $input [input]
	 * @return void
	 */
	final public function load()
	{
	
		$this->load_real(func_get_args(), 'load');
	
	}
	
	/** Loads a form
	 * 
	 * Calling this function triggers the event 'loadForm'.
	 * 
	 * @access public
	 * @param string $title FormFile title.
	 * @return HTML_Form Form
	 * @throws FormException when $form does not exists or could not be loaded.
	 */
	final public function form($title)
	{
		
		// load form helper
		require_once 'system/core/form.class.php';
		
		// fire event
		$this->event->fire('loadForm', $title);
		
		try {
			
			$form = require('application/forms/'.$title.'.php');
			return $form;
			
		} catch(Exception $ex) {
			
			// form_exception?
			if (get_class($ex) == '/System/Exception/Form')
				throw $ex;
			
			// ordinair exception
			if (!file_exists('application/forms/'.$title.'.php')) {
				
				throw new Exception\Form("Form '".'application/forms/'.$title.'.php'."' doesn't exist.");
				
			}
			
			// thrown exception
			throw new Exception\Form("Form '".'application/forms/'.$title.'.php'."' could not be loaded: <br />".$ex->fullMessage());
			
		}
		
	}
	
	/** Loads the models or corefiles.
	 * 
	 * @access private
	 * @param array $array Parameters
	 * @param string $type Type of file.
	 * @return bool Success
	 */
	final private function load_real($array, $type = 'load')
	{
	
		// generate prefix path
		if($type == 'model'){
		
			// set prefix path
			$prefix =  'application/models/';
			
			// set parameters
			$parameter = "\$this->references['database']";
			
			$nSpace = '\Application\Model\\';
		
		}else
		if($type == 'load'){
		
			// set prefix path
			$prefix =  'system/core/';
			
			// set parameters
			$parameter = "";
			
			$nSpace = '\System\\';
		
		}else{
		
			return false;
		
		}
	
		// get arguments
		$a = count($array);
		
		for($i = 0; $i < $a; $i++){
		
			// get argument real
			$argument = $array[$i];
			
			// check array
			if(is_array($argument)){
			
				// set file
				$file	= $argument[0];
				
				// set output variable
				$out	= $argument[1];
			
			}else{
			
			
				// set output and file
				$file = $out = $argument;
			
			}
			
			// prepare
			$sfile = $nSpace.ucfirst($file);
			
			// check already defined
			if(! class_exists($sfile)){
			
				// set path
				$path = $prefix.$file.'.class.php';
		
				// check if file exists
				if(file_exists($path)){
					
					// config files only may be once included
					require_once($path);
					
					// load
					eval('$this->'.$out.' = new '.$sfile.'('.$parameter.');');
				
				}else{
				
					throw new Exception\Load('Trying to initialize a '.$type.'. File does not exist at '.$path);
				
				}
				
			}else{
			
				// check if output exists
				if(!is_a($out, $sfile)){
				
					// load
					eval('$this->'.$out.' = new '.$sfile.'('.$parameter.');');
				
				}
			
			}
			
			
			// check some things
			if ($type == 'load')
				$this->references[strtolower($file)] = $this->$out;	
			
		}
		
		
		// return value
		return true;
	
	}
	
}
?>