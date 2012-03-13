<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Helpers loader
 * 
 * This class loads all helpers.
 *
 * @name HelperLoader
 * @package core
 * @subpackage helpers
 */
class YS_Helpers extends YS_Singleton
{
		
	/**
	 * @access private
	 * @var array $config Config
	 */
	private $config;
	
	/** 
	 * @access private	 
	 * @staticvar array $helpers All helpers
	 */
 	private $helpers = null;
			
	/** Constructor
	 * 
	 * load config and helpers
	 * 
	 * @access public
	 * @return void
	 */
	public function __construct()
	{
		
		// config
		$this->config = YS_Config::Load();
		
	}
	
	/** Magic function - selects an helper
	 * 
	 * @access public
	 * @param string $helper Name of the helper.
	 * @throws HelperException with errorcode 1 when the helper is not loaded.
	 * @return YS_Helper(or child)
	 */
	public function __get($helper)
	{
	
		// load helper
		if (empty($this->helpers->$helper)) {
			
			require_once('system/helpers/'.strtolower($helper).'.class.php');
			eval('$this->helpers->'.strtolower($helper).' = new YSH_'.ucfirst(strtolower($helper)).'();');	
			
		}
		
		// call helper
		if (is_object($this->helpers->$helper))
			return $this->helpers->$helper;
			
			
		throw new HelperException(1, 'Couldn\'t load the helper: '.$helper.'.');
		
	}
	
}

/** Helper parent
 * 
 * This class initializes the helpers
 *
 * @name Helper
 * @package core
 * @subpackage helpers
 */
class YS_Helper
{
	
	/**
	 * @access public
	 * @var array $config Config
	 */
	public $config;
	
	/**
	 * @access public
	 * @var YS_Helpers $helpers All helpers
	 */
	public $helpers;
	
	/**
	 * 
	 * @access private
	 * @staticvar array $instance
	 */
 	private static $instance = array();
	
	/** Constructor
	 * 
	 * Loads all helpers and config.
	 * 
	 * @access public
	 * @return Child
	 */
	public function __construct()
	{
		
		// load config/helpers
		$this->config = YS_Config::Load();
		$this->helpers = YS_Helpers::Load();
		
	}
	
	/** Makes sure that you do only load one class of this helper.
	 * 
	 * @access protected
	 * @throws HelperException with errorcode 1 when the class is loaded for the second time.
	 * @return void
	 */
	final protected function singleton()
	{
		
		if (empty(self::$instance[get_class($this)])) {
			
			self::$instance[get_class($this)] = $this;
			return;
			
		}
		
		throw new HelperException(1, 'Cannot load a helper twice: '.get_class($this).'.');
		
	}
	
}
?>