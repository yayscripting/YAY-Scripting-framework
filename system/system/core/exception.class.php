<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Core Error
 *
 * @package core
 * @subpackage Error
 */
class CoreException extends Exception
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
class DatabaseException extends Exception
{
	
	/** Show error-message
	 * 
	 * @access public
	 * @return string ErrorMessage
	 */
	public function errorMessage()
	{
		
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


/** Query Error
 *
 * @package core
 * @subpackage Error
 */
class QueryException extends Exception
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

/** Config Error
 *
 * @package core
 * @subpackage Error
 */
class ConfigException extends Exception
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
class ModelException extends Exception
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
class FormException extends Exception
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
class SingletonException extends Exception
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
class LoadException extends Exception
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
class HelperException extends Exception
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
class TranslateException extends Exception
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