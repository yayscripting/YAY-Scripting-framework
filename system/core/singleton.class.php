<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Singleton
 * 
 * Extend this class to create a singleton-class.
 *
 * @name Singleton
 * @package core
 * @subpackage Specialities
 */
class YS_Singleton
{
	
	/** Remembers all instances
	 * 
	 * @access private
	 * @staticvar $instances
	 */
	static private $instances;
	
	/** Creates a new instance
	 * 
	 * @access public
	 * @static
	 * @return YS_Singleton-childclass
	 */
	final static public function Load()
	{
		
		$class = self::_get_called_class();
		
		if (empty(self::$instances))
			self::$instances = array();
		
		if (empty(self::$instances[$class])) {
			
			self::$instances[$class] = new $class();
			
		}
		
		return self::$instances[$class];
		
	}
 	
 	/** Returns the called class
 	 * 
 	 * @access private
 	 * @static
 	 * @return string Classname, or false on error.
 	 */
	final private static function _get_called_class()
	{
		
		// < PHP 5.3.0
		if(!function_exists('get_called_class')) {
			
			// Thanks to laurence <laurence@sol1.com.au>
			// http://www.php.net/manual/en/function.get-called-class.php#93799
			// this function is slightly modified
			function get_called_class($bt = false, $l = 1) 
			{
				
				if (!$bt) $bt = debug_backtrace();
				if (!isset($bt[$l])) throw new SingletonException("Cannot find called class -> stack level too deep.");
				if (!isset($bt[$l]['type'])) {
					
					throw new SingletonException ('type not set');
				
				} else {
				
					switch ($bt[$l]['type']) {
						
			        		case '::':
			        		
			            			$lines = file($bt[$l]['file']);
			            			$i = 0;
			            			$callerLine = '';
			            			
							do {
								$i++;
								$callerLine = $lines[$bt[$l]['line']-$i] . $callerLine;
							} while (stripos($callerLine,$bt[$l]['function']) === false);
							
			            			preg_match('/([a-zA-Z0-9\_]+)::'.strtolower($bt[$l]['function']).'/', strtolower($callerLine), $matches);
			            			
							if (!isset($matches[1]))
								throw new SingletonException ("Could not find caller class: originating method call is obscured.");
			            
							switch ($matches[1]) {
								
								case 'self':
								case 'parent':
									return get_called_class($bt,$l+1);
								default:
									return $matches[1];
								
			            			}
			            						            			
			            	// won't get here.
			        		case '->': 
							switch ($bt[$l]['function']) {
								
			                			case '__get':
			                   				 if (!is_object($bt[$l]['object'])) throw new SingletonException ("Edge case fail. __get called on non object.");
			                    				return get_class($bt[$l]['object']);
			                			default: 
									return $bt[$l]['class'];
									
			            			}
			
			        		default: 
							throw new SingletonException ("Unknown backtrace method type");
							
					}
					
				}
				
			}
			
		}
		
		return get_called_class();
		
	}
	
	/** Constructor
	 * 
	 * Checks if the class is registered with ::Load instead of new().
	 * @access protected
	 * @return void
	 * @throws SingletonException when the class is created twice.
	 */
	protected function __construct()
	{
		
		if (empty(self::$instances[get_class($this)]) !== true && self::$instances[get_class($this)] !== $this)
			throw new SingletonException("The class '".get_class($this)."' is a singleton class, and can one be created once.");
		
		if (empty(self::$instances[get_class($this)]))
			self::$instances[get_class($this)] = $this;
		
	}
	
}