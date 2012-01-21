<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Controller
 * 
 * This class loads corefiles, models and forms.
 *
 * @name Controller
 * @package core
 * @subpackage Controller
 */
class YS_Controller
{

	/** Helpers
	 * 
	 * @access public
	 * @var YS_Helpers $helpers
	 */
	public $helpers;
	
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
	 * @access protected
	 * @var YS_Environment
	 */
 	protected $environment;
 	
	/** Error
	 * 
	 * @access protected
	 * @var YS_Error
	 */
 	protected $error;
 	
	/** Constructor
	 * 
	 * Loads layout, helpers and config. And includes runtime.php
	 * 
	 * @access public
	 * @return YS_Controller
	 */
	public function __construct()
	{
		
		$this->helpers = YS_Helpers::Load();
		$this->config  = YS_Config::Load();;
		
		// environment
		$this->environment = YS_Environment::Load();
		
		// switch all GET-(location-)vars 1 spot. (a/b/d/f/e/f/g/h)
		if ($this->environment->get() !== false)
			require_once 'system/functions/router.handleGET.inc.php';
			
		require_once 'system/functions/controller.handlePOST.inc.php';
			
		// set 
		$this->get = $_GET;
		$this->post = $_POST;
		
		// error
		$this->error = YS_Error::Load();
		
		// load smarty
		try {
			
			$this->load('layout');
		
		} catch (exception $ex) {
			
			die("De layout-parser kon niet geladen worden.<br /><br /><em>".$ex->errorMessage()."</em>");
			
		}
		
		// load layout-parser
		global $_layout;
		$_layout = $this->layout;
		
		// executes the runtime
		if (file_exists('application/runtime.php'))
			require 'application/runtime.php';
		
		
	}
	
	/** Loads a model
	 * 
	 * This function accepts any number of parameters, which are the names of the model.
	 * If a model is loaded, automatically the coreFile database will be loaded under the name of "sql"
	 * Example in a controller:
	 * <code>
	 * // load 2 models
	 * $this->model('client', 'money');
	 * 
	 * // the models can be reached using '$this->' in a controller.
	 * $this->client->delete(1);
	 * $this->money->book(1500);
	 * </code>
	 * 
	 * @access public
	 * @param string Modelname (multiple times)
	 * @return void
	 */
	public function model()
	{
		
		// model-controller
		require_once('system/core/model.class.php');
		
		// require database
		$this->load(array('database', 'sql'));
		
		// model
		$this->load_real(func_get_args(), 'model');
		
	}
	
	/** Loads a class-file
	 * 
	 * Class found in /application/classes/NAME.class.php. As a constructor-parameter this class has been given, to contact the helpers/config/models.
	 * Class name should be the lowerstring version of the first parameter, but only with the first letter being capitalized.
	 * 
	 * @access public
	 * @param string $className Name of the class(file)
	 * @return object Created class
	 */
 	public function loadClass($className)
 	{
 		
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
	public function load()
	{
	
		$this->load_real(func_get_args(), 'load');
	
	}
	
	/** Loads a form
	 * 
	 * @access public
	 * @param string $title FormFile title.
	 * @return HTML_Form Form
	 * @throws FormException when $form does not exists or could not be loaded.
	 */
	public function form($title)
	{
		
		try {
			
			$form = require('application/forms/'.$title.'.php');
			return $form;
			
		} catch(Exception $ex) {
			
			// form_exception?
			if (get_class($ex) == 'FormException')
				throw $ex;
			
			// ordinair exception
			if (!file_exists('forms/'.$title.'.php'))
				throw new FormException("Form '".'application/forms/'.$title.'.php'."' doesn't exist.");
			else
				throw new FormException("Form '".'application/forms/'.$title.'.php'."' could not be loaded: <br />".$ex->fullMessage());
			
		}
		
	}
	
	/** Loads the models or corefiles.
	 * 
	 * @access private
	 * @param array $array Parameters
	 * @param string $type Type of file.
	 * @return bool Success
	 */
	private function load_real($array, $type = 'load')
	{
	
		// generate prefix path
		if($type == 'model'){
		
			// set prefix path
			$prefix =  'application/models/';	
			
			// set parameters
			$parameter = "\$this->references['database']";
		
		}else
		if($type == 'load'){
		
			// set prefix path
			$prefix =  'system/core/';
			
			// set parameters
			$parameter = "";
		
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
			$sfile = 'YS_'.ucfirst($file);
			
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
				
					throw new LoadException('Trying to initialize a '.$type.'. File does not exist at '.$path);
				
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