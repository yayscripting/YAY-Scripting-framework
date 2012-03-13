<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Config loader
 * 
 * This class loads all config files.
 *
 * @name ConfigLoader
 * @package core
 * @subpackage config
 */
class YS_Config extends YS_Singleton
{
	
	/** Config container
	 * 
	 * @access private
	 * @var object
	 */
	private $config = null;
	
	/** remember the CWD, because this will be reset after the script-flow has been finished.
	 * 
	 * @access private
	 * @var string
	 */
	private $cwd;
	 
	/** Checks the singleton
	 * 
	 * @access public
	 * @return YS_Config Config-class
	 */
	public function __construct() 
	{
		
		parent::__construct();
		
		$this->config = (object)array();// new stdClass()
		$this->cwd    = getcwd();
		
	}
	
	/** Magic function - Returns or loads the config
	 * 
	 * Loads the config from the /application/config-directory.
	 * 
	 * @access public
	 * @param string $name Name of the config-file, converted to lowercase automatically.
	 * @return object Config-data
	 * 
	 */
	public function __get($name)
	{
		
		if (empty($this->config->{strtolower($name)})) {
			
			if (!file_Exists($this->cwd . '/application/config/'.strtolower($name).'.cfg.php')) {
				
				throw (new ConfigException('Could not load config file: '.strtolower($name).'.'));
				
			}
				
			$this->config->$name = (object)require($this->cwd . '/application/config/'.strtolower($name).'.cfg.php');
			
		}
		
		return $this->config->{strtolower($name)};
		
	}
	
}
?>