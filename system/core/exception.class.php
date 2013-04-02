<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Exception;

/** Core Error
 *
 * @package core
 * @subpackage Error
 */
class Core extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}


/** Database Error
 *
 * @package core
 * @subpackage Error
 */
class Database extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		if(\System\Config::Load()->script->debug_mode === true){
		
			return $this->getMessage();
			
		}else{
		
			return;
		
		}
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		if(\System\Config::Load()->script->debug_mode === true){
		
			// error
			return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
			
		}else{
		
			return;
		
		}
		
	}
	
}


/** Query Error
 *
 * @package core
 * @subpackage Error
 */
class Query extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		if(\System\Config::Load()->script->debug_mode === true){
		
			return $this->getMessage();
			
		}else{
		
			return;
		
		}
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		if(\System\Config::Load()->script->debug_mode === true){
		
			// error
			return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
			
		}else{
		
			return;
		
		}
		
	}
	
}

/** Config Error
 *
 * @package core
 * @subpackage Error
 */
class Config extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}


/** Model Error
 *
 * This exception has got an integer variable, $errorType, which can be used to identify different errors
 * 
 * @package core
 * @subpackage Error
 */
class Model extends \Exception
{
	
	/** ErrorType
	 * 
	 * @access public
	 * @var int $errorType Error Identifier.
	 */
	public $errorType;
	
	/** Constructor
	 * 
	 * @access public
	 * @param int $errorType Error identifier.
	 * @param string $errorMessage Error Message.
	 * @return HelperException
	 */
	public function __construct($errorType, $errorMessage) 
	{
		
		parent::__construct($errorMessage);
		$this->errorType = $errorType;
		
	}
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
			
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b><br />ErrorType: <b>'.$this->errorType.'</b>';
		
	}
	
}

/** Form Error
 *
 * @package core
 * @subpackage Error
 */
class Form extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}

/** Duplicate singleton class.
 *
 * @package core
 * @subpackage Error
 */
class Singleton extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}


/** Load Error
 *
 * @package core
 * @subpackage Error
 */
class Load extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}


/** Helper Error
 *
 * This exception has got an integer variable, $errorType, which can be used to identify different errors
 * 
 * @package core
 * @subpackage Error
 */
class Helper extends \Exception
{
	
	/** ErrorType
	 * 
	 * @access public
	 * @var int $errorType Error Identifier.
	 */
	public $errorType;
	
	/** Constructor
	 * 
	 * @access public
	 * @param int $errorType Error identifier.
	 * @param string $errorMessage Error Message.
	 * @return HelperException
	 */
	public function __construct($errorType, $errorMessage) 
	{
		
		parent::__construct($errorMessage);
		$this->errorType = $errorType;
		
	}
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
			
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b><br />ErrorType: <b>'.$this->errorType.'</b>';
		
	}
	
}

/** Translate error
 *
 * @package core
 * @subpackage Error
 */
class Translate extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}
	
}

/** Route error
 *
 * @package core
 * @subpackage Error
 */
class Route extends \Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
		// error
		return $this->getMessage();
		
	}
	
	/** Shows full errorMessage, inclusive linenumber and filename.
	 * 
	 * @access public
	 * @return string Full errorMessage.
	 */
	public function fullMessage()
	{
		
		// error
		return 'Error on line '.$this->getLine().' in '.$this->getFile().'.<br /> <b>'.$this->getMessage().'</b>';
		
	}

}