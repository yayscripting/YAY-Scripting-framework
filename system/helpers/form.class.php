<?php
/**
 * @author YAY!Scripting
 * @package files
 */

/** 
 * @global regex FILTER_ALPHA FORM_Validator-constant, upper/lowercase characters only.
 */
define('FILTER_ALPHA', '/^[a-zA-Z]+$/');

/** 
 * @global regex FILTER_LOWER FORM_Validator-constant, lowercase characters only.
 */
define('FILTER_LOWER', '/^[a-z]+$/');

/** 
 * @global regex FILTER_UPPER FORM_Validator-constant, uppercase characters only.
 */
define('FILTER_UPPER', '/^[A-Z]+$/');

/** 
 * @global regex FILTER_ALNUM FORM_Validator-constant, letters and numbers only.
 */
define('FILTER_ALNUM', '/^[a-zA-Z0-9]+$/');

/** 
 * @global regex FILTER_PHONE FORM_Validator-constant, phone-numbers only.
 */
define('FILTER_PHONE', '/^([0-9]{10}|00[0-9]{2}[0-9]{9})$/');

/** 
 * @global regex FILTER_EMAIL FORM_Validator-constant, email-addresses only.
 */
define('FILTER_EMAIL', '/^[a-zA-Z0-9_\.\-]{3,255}@[a-zA-Z0-9\.\-]{3,255}\.[a-z]{2,4}$/');

/** 
 * @global regex FILTER_IP FORM_Validator-constant, ip-addresses only.
 */
define('FILTER_IP', '/^(([0-9]|[1-9][0-9]|[12][0-9][0-9])\.){3}([0-9]|[1-9][0-9]|[12][0-9][0-9])$/');

/** 
 * @global regex FILTER_DATE_HUMAN FORM_Validator-constant, d-m-Y format.
 */
define('FILTER_DATE_HUMAN', '/^(0[1-9]|[12][0-9]|3[01])\-([0][1-9]|[1][012])\-(2[0-9]{3}|19[0-9]{2})$/');

/** 
 * @global regex FILTER_DATE_SYSTEM FORM_Validator-constant, Y-m-d format.
 */
define('FILTER_DATE_SYSTEM', '/^(2[0-9]{3}|19[0-9]{2})\-([0][1-9]|[1][012])\-(0[1-9]|[12][0-9]|3[01])$/');

/** 
 * @global regex FILTER_URL FORM_Validator-constant, for Links, http:// is required
 */
define('FILTER_URL', '/^http:\/\/([a-zA-Z0-9\-_]{1,}\.){1,}\.[a-zA-Z]{2,4}$/');

/** Form-helper
 * 
 * This helper can be used to load a new form.
 *
 * @name Form_loader
 * @package helpers
 * @subpackage forms
 */
class YSH_Form extends YS_Helper
{
	
	/**
	 * @access private
	 * @var int $forms Counts how many forms are loaded in one page, and makes sure that the form-identifiers are unique.
	 */
	private $forms = 0;
	
	/** Loads a new form
	 * 
	 * @access public
	 * @param string $method Method of the form, either POST or GET.
	 * @param string $title Title of the form.
	 * @param bool $upload Do you want to handle file-uploads also?
	 * @return HTML_Form New form.
	 */
	public function newForm($method = "POST", $title = "", $upload = false)
	{
		$this->forms++;
		
		return new HTML_Form($this->forms, $title, $method, $upload);
		
	}
	
}

/** Form-child-element
 * 
 * This class contains a new child-element of the form, such as an input, or a textarea.
 * Do never create this class this way. Always use {@link HTML_Form::input} or {@link HTML_Form::textarea} or {@link HTML_Form::append}.
 *
 * @name Form-child-element
 * @package helpers
 * @subpackage forms
 */
class HTML_Element
{
	
	/**#@+
	 * @access protected
	 */
	/**
	 * @var string $type Type of the element, e.g. input or textarea.
 	 */
	protected $type;
	
	/**
	 * @var bool $close Is this a closed element? (e.g. input)
 	 */
	protected $close;
	
	/**
	 * @var array $attributes All attributes
 	 */
	protected $attributes = array();
	
	/**
	 * @var bool $required Is this element required to fill?
 	 */
	protected $required = false;
	
	/**
	 * @var bool $remember Remember data when the form has not passed the validation?
 	 */
	protected $remember = true;
	
	/**
	 * @var FORM_Validator(or child) $validator to check when the form is beeing validated
 	 */
	protected $validator = null;
	
	/**
	 * @var string $errorMessage Message to show when the field is not filled out.
 	 */
	protected $errorMessage = "";
	
	/**
	 * @var HTML_Form $parent Parent-form
	 */
 	protected $parent;
	/**#@-*/
	
 	/**
 	 * @access public
 	 * @var int $nameIndex Index of the element.
 	 */
 	public $nameIndex = null;
	
	/** Constructor
	 * 
	 * sets the given values
	 * 
	 * @access public
	 * @param string $type Type of the element, e.g. input or textarea.
	 * @param bool $close Does this element needs to be closed in the same tag? e.g. br or input.
	 * @return HTML_Element
	 */
	public function __construct($type, $parent, $close = false)
	{
		
		// remember
		$this->type = $type;
		$this->parent = $parent;
		$this->close = $close;
		
	}
	
	/** Returns the type of an element
	 * 
	 * If the type is 'input', this functions returns the attribute type, which will be things like text, password or file.
	 * 
	 * @access public
	 * @return string Type
	 */
	public function getType()
	{
		
		if ($this->type == 'input')
			return strtolower($this->getAttribute('type'));
			
		return strtolower($this->type);
		
	}
	
	/** Is this a closed element, like input or br?
	 * 
	 * This function exists because you can not touch the {@link $close} variable.
	 * 
	 * @access public
	 * @return bool Closed
	 */
	public function getClosed()
	{
		
		return $this->close;
		
	}
	
	/** Sets an attribute
	 * 
	 * This functions sets an attribute-value that will be shown in the generated html
	 * Same as {@link setAttribute}. Except the return value.
	 * 
	 * @access public
	 * @param string $name Name of the attribute.
	 * @param string $value Value of the attribute.
	 * @return void
	 * @see setAttribute
	 * @see __get
	 */
	public function __set($name, $value)
	{
		
		// name enchantment
		if (strtolower($name) == 'name') {
			
			if (strpos($value, '[]') !== false) {
				
				$this->nameIndex = $this->parent->getNameIndex($value);
				
			}
			
		}
		
		// set
		$this->attributes[$name] = $value;
		
	}
	
	/** Sets an attribute
	 * 
	 * This functions sets an attribute-value that will be shown in the generated html
	 * Same as {@link __set}. Except the return value.
	 * 
	 * @access public
	 * @param string $name Name of the attribute.
	 * @param string $value Value of the attribute.
	 * @return HTML_Element $this
	 * @see set
	 * @see setAttributes
	 * @see getAttribute
	 */
	public function setAttribute($name, $value)
	{
		
		$this->__set($name, $value);
		return $this;
		
	}
	
	/** Sets multiple attributes
	 * 
	 * This functions sets a multiple attributes that will be shown in the generated HTML
	 * Example:
	 * <code>
	 * $element->setAttributes(array(
	 * 	"name" => "password",
	 * 	"class"=> "glow"
	 * 	));
	 * </code>
	 * 
	 * @access public
	 * @param string $name Name of the attribute.
	 * @param string $value Value of the attribute.
	 * @return HTML_Element
	 * @see setAttribute
	 * @see getAttribute
	 */
	public function setAttributes(array $values)
	{
		
		foreach($values as $key => $value) {
			
			$this->__set($key, $value);
			
		}
		
		return $this;
		
	}
	
	/** Returns the value of an attribute.
	 * 
	 * Alias of {@link __get}.
	 * 
	 * @access public
	 * @param string $name Name of the attribute.
	 * @return string Attribute value
	 */
	public function getAttribute($name)
	{
		
		return $this->__get($name);
		
	}
	
	/** Returns the value of an attribute.
	 * 
	 *  Alias of {@link getattribute}.
	 * 
	 * @access public
	 * @param string $name Name of attribute.
	 * @return string Attribute value
	 */
	public function __get($name)
	{
		
		if (empty($this->attributes[$name]))
			return false;
			
		return $this->attributes[$name];
		
	}
	
	/** Returns the index of this element beeing in the array created by {@link HTML_Form::build}
	 * 
	 * @access public
	 * @return int Index
	 */
	public function getIndex()
	{
		
		if (is_null($this->nameIndex))
			return 0;
			
		return $this->nameIndex;	
		
	}
	
	/** Sets the remember state.
	 * 
	 * If this element is validated, it will set its value to the posted one.
	 * If setRemembered is false, the value will remain the default.
	 * 
	 * @access public
	 * @param bool $bool State
	 * @return void
	 */
	public function setRemember($bool = true)
	{
		
		$this->remember = $bool;
		
		return $this;
		
	}	
	
	/** Set this element to required.
	 * 
	 * If this element is not filled in, and $required is true, the validation failed.
	 * 
	 * @access public
	 * @param bool $required Is it required?
	 * @param string $errorMSG The errormessage to display on error.
	 * @return HTML_Element $this
	 */
	public function setRequired($required, $errorMSG = "")
	{
		
		if (empty($this->attributes['name']) && $required)
			throw new FormException("You have to set a 'name' before you set the required-variable");
		
		
		$this->validator= null;
		$this->required = $required;
		$this->errorMessage = $errorMSG;
		
		return $this;
		
	}
	
	/** Sets this element to required, and register (multiple) validators
	 * 
	 * This function accepts multiple types of input.
	 * Example:
	 * <code>
	 * // using a single validator
	 * $element->setValidator(new FORM_Validator("/^[0-9]$/", "You can only enter numbers"));
	 * 
	 * // using multiple validators
	 * $element->setValidator(array(
	 * 		new FORM_Validator("/^[0-9]$/", "You can only enter numbers"),
	 * 		new FORM_Validator("/^.{5,}$/", "You need to enter at least 5 characters")
	 * 	));
	 * 
	 * // using single string validator
	 * $element->setValidator(array("/^[0-9]$/" => "You can only enter numbers"));
	 * 
	 * // using multiple string validators
	 * $element->setValidator(array(
	 * 		"/^[0-9]$/" => "You can only enter numbers",
	 * 		"/^.{5,}$/" => "You need to enter at least 5 characters"
	 * 	));
	 * </code>
	 * 
	 * @access public
	 * @param mixed $validator Validators
	 * @return HTML_Element $this
	 */
	public function setValidator($validator)
	{
		
		// fool-proof
		if (empty($this->attributes['name']))
			throw new FormException("You have to set a 'name' before you set the validator");
		
		// set required
		$this->required = true;
		
		// validator, or more?
		if (is_array($validator)) {
			
			$this->validator = array();
			
			foreach($validator as $key => $val) {
				
				$this->validator[] = (is_string($key) && is_string($val)) ? new FORM_Validator($key, $val) : $val;
				
			}
			
		} else {
			
			$this->validator = array($validator);
			
		}
		
		return $this;
		
	}
	
	/** Sets a new value, based on the container-variable.
	 * 
	 * This functions checks the $container-variable, which has to be formatted, just like the $_POST/$_GET-variables. 
	 * And sets the value (checked at radio's, innerHTML on textarea's)  of the element as it should be, according to the $container-variable.
	 * You will most likely never call this function, but the FORM-version of it.
	 * Example:
	 * <code>
	 * $element->fill($_POST);
	 * </code>
	 * 
	 * @access public
	 * @param array $container The container-variable
	 * @return HTML_Element $this
	 */
	public function fill($container)
	{
		
		// no name?
		if ($this->getAttribute('name') == false) return $this;
		
		// check container
		$empty = $this->isEmpty($container);
		
		// check if given
		if ($empty == false) {
			
			// get container value
			$value = $this->getPostValue($container);
			
			if ($this->getType() == 'radio' || $this->getType() == 'checkbox') {
				
				if ($value == $this->getAttribute('value')) {
					
					$this->setAttribute('checked', 'checked');
					
				} else {
					
					unset($this->attributes['checked']);
					
				}
				
			} else if ($this->getType() == 'select') {
				
				$this->setAttribute('value', $value);
				
			} else if (!$this->close) {
				
				$this->setAttribute('innerHTML', $value);
				
			} else {
				
				$this->setAttribute('value', $value);
				
			}
			
			$this->setRemember(false);			
			
		}
		
		return $this;		
		
	}
	
	/** Returns the posted value, works well with 1D dimension.
	 * 
	 * @access public
	 * @param array $container Container, null = $_POST
	 * @return mixed POST-data
	 */
	public function getPostValue($container = null)
	{
		
		if (is_null($container))	
			$container = $_POST;
		
		if ($this->nameIndex === null) {
			
			return $container[preg_replace('/[^a-zA-Z0-9_]/', '', $this->getAttribute('name'))]; 
			
		} 
		
		$name = preg_replace('/[^a-zA-Z0-9_]/', '', $this->getAttribute("name"));
		
		return (eval('return $container[$name][$this->nameIndex];'));
		
	}
	
	/** Executes an empty()-function on the POST-data, works well with 1D dimension.
	 * 
	 * @access public
	 * @param array $container Container, null = $_POST
	 * @return mixed POST-data
	 */
	public function isEmpty($container = null)
	{
		
		if (is_null($container))
			$container = $_POST;
		
		if ($this->nameIndex === null) {
			
			return empty($container[preg_replace('/[^a-zA-Z0-9_]/', '', $this->getAttribute('name'))]);
			
		}
		
		
		$name = preg_replace('/[^a-zA-Z0-9_]/', '', $this->getAttribute("name"));
		
		return (eval('return empty($container[$name][$this->nameIndex]);'));
		
	}
	
	/** Builds the HTML-code
	 * 
	 * You will probably never call this function, use the function of the FORM_Element instead.
	 * 
	 * @access public
	 * @param string $tabs Prefix for every new line
	 * @return string HTML-code
	 */
	public function build($tabs = "")
	{
		
		// fill
		if ($this->isEmpty($_POST) == false && $this->remember)
			$this->fill($_POST);
			
		// opening tag
		$html = $tabs . '<' . $this->type;
		
		
		if (!empty($this->attributes)) {
			
			// check for name-attribute
			if (!empty($this->attributes['name'])) {
				
				if ($this->nameIndex !== null && strpos($this->attributes['name'], '[]') !== false) {
					
					$this->attributes['name'] = preg_replace('/[^a-zA-Z0-9_]/', '', $this->attributes['name']) . '[' . $this->nameIndex . ']';
					
				}
				
			}
			
			// set attributes
			foreach($this->attributes as $key => $value) {
				
				if ($key != "innerHTML" && $value !== false && $value !== null)
					$html .= ' '.htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
					
			}
					
					
		}
		
		$html .= ($this->close) ? " />\n" : ">";
		
		if (empty($this->attributes['innerHTML']) == false && $this->close == false)
			$html .= $this->attributes['innerHTML'];
		
		// close tag?
		if (!$this->close)
			$html .= "</".$this->type.">\n";
		
		return $html;
		
	}
	
	/** Validate element
	 * 
	 * Checks if this element's value is filled in correctly.
	 * The $containing should be either $_POST or $_GET.
	 * You will probably never use this function, but you'll use {@link FORM_Element::validate} instead.
	 * 
	 * @access public
	 * @param array $containing Container.
	 * @return mixed true on success, errorMessage on failure.
	 */
	public function validate($containing)
	{
		
		// check himself
		if ($this->required) {
			
			$empty = $this->isEmpty($containing);
			
			if ($this->validator === null) {
				
				// empty??
				if ($empty)
					return $this->errorMessage;
					
					
			} else {
				
				// use validator
				$data = $this->getPostValue($containing);
				$name = preg_replace('/[^a-zA-Z0-9_]/', '', $this->getAttribute('name'));
				
				foreach ($this->validator as $validator) {
					
					$result = $validator->validate($name, $data);
					if ($result !== true)
						return $result;
					
				}
					
			}
			
		}
		
		return true;
		
	}
}

/** Form-element
 * 
 * This class contains several functions that are specific for forms.
 *
 * @name Form-element
 * @package helpers
 * @subpackage forms
 */
class HTML_Form extends HTML_Element
{
	
	/**#@+
	 * @access private
	 */
	 
	/** Used to assign errors.
	 * 
	 * @var YS_Layout
	 */
	private $layout;
	
	/** Used to identify forms in POST-data.
	 * 
	 * @var string Form_ID
	 */
	private $form_ID;
	
	/** All form-elements
	 * 
	 * @var array $elements
	 */
	private $elements = array();
	
	/** All upload-elements
	 * 
	 * @var array $uploads
	 */
	private $uploads  = array();
	
	/** Upload state
	 * 
	 * @var bool $upload
	 */
	private $upload	  = false;
	
	/** Contains the number of same indexes that have been passed
	 * 
	 * @var array $nameIndexes
	 */
	protected $nameIndexes = array();
	
	/**#@-*/
	
	/** Constructor
	 * 
	 * Sets all values correctly.
	 * $instance and $name are used to generate the FormID
	 * 
	 * @access public
	 * @param int $instance Number of forms that have already preceed this form.
	 * @param string $name Name of the form.
	 * @param string $method Form Method, either GET or POST.
	 * @param bool $upload Does this form support file-upload?
	 * @return HTML_Form
	 */
	public function __construct($instance = 0, $name = "", $method = "post", $upload = false)
	{
		
		// get layout-engine
		$this->layout = YS_Layout::Load();
		
		// get form_ID
		$this->form_ID = sha1($name . $instance . $method);
		
		// create element
		parent::__construct('form', false);
		
		// upload
		$this->upload = $upload;
		
		// set attributes
		$this->attributes['method'] = $method;
		if ($upload)
			$this->attributes['enctype'] = 'multipart/form-data';
			
		 
	} 
	
	/** Appends a new form-child-element.
	 * 
	 * @access private
	 * @param string $type Type of element, e.g. input or textarea.
	 * @param bool $close Close element in openening tag, e.g. input or br.
	 * @return HTML_Element
	 */ 
	private function append($type, $close = false)
	{
		
		$element = new HTML_Element($type, $this, $close);	
		$this->elements[] = $element;
		
		// return element
		return $element;
		
	}
	
	/** Appends a new input-element.
	 * 
	 * @access public
	 * @param string $type Type, e.g. password or text.
	 * @param string $name Name of the element.
	 * @return HTML_Element
	 */
	public function input($type, $name)
	{
		
		$type = strtolower($type);
		
		$el = $this->append('input', true);
		$el->setAttribute('type', $type);
		$el->setAttribute('name', $name);
			
		return $el;
		
	}
	
	/** Appends a new select-element
	 * 
	 * @access public
	 * @param string $name Name of the element
	 * @param string $default Default message to show on error
	 * @return HTML_Select
	 */
	public function select($name, $default)
	{
		
		// new upload-element
		$element = new HTML_Select('select', $this, false);
		$element->setAttribute('name', $name);
		$element->setDefault($default);
		
		// save element
		$this->elements[] = $element;
		
		return $element;
		
	}
	
	/** Appends a new upload-element.
	 * 
	 * @access public
	 * @param string $name Name of the element.
	 * @param int $maxSize Maximum size of the file in bytes.
	 * @throws HelperException with errorType 1.
	 * 	If the form does not support uploading.
	 * @return HTML_Upload
	 */
	public function upload($name, $maxSize = 1000000)
	{
		
		// error?
		if (!$this->upload)
			throw new HelperException(1, "Uw formulier heeft het uploaden uitgeschakeld.");
		
		
		// new upload-element
		$element = new HTML_Upload($name, $this, $maxSize);
		
		// save element
		$this->elements[] = $element;
		$this->uploads[$name] = $element;
		
		return $element;
		
	}
	
	/** Appends a new textarea.
	 * 
	 * @access public
	 * @param string $name Name of the element.
	 * @return HTML_Element
	 */
	public function textarea($name)
	{
		
		$el = $this->append('textarea', false);
		$el->name = $name;
			
		return $el;
		
	}
	
	/** Find all element with a specific name.
	 * 
	 * @access public
	 * @param string $name Name to search for.
	 * @return array Results
	 */
	public function elements($name)
	{
		
		$results = array();
		
		foreach($this->elements as $element)
		{
			
			if (preg_replace('/[^a-zA-Z0-9_]/', '', $element->name) == $name)
				$results[] = $element;
			
			
		}
		
		return $results;
		
	}
	
	/** Find the Xth element with a specific name.
	 * 
	 * $index starts at zero.
	 * 
	 * @access public
	 * @param string $name Name to search for.
	 * @param int $index Index of the element
	 * @return HTML_Element HTML_Element on success, false on failure.
	 */
	public function element($name, $index)
	{
		
		$count = 0;
		
		foreach($this->elements as $element) {
			
			if (preg_replace('/[^a-zA-Z0-9_]/', '', $element->name) == $name) {
				
				if ($index == $count)
					return $element;
					
				$index++;
				
			}
			
		}
		
		return false;
		
	}
	
	/** Gets the next index of a name, used in the element-classes.
	 * 
	 * Do not use this function yourself, this functions exists only for the element-classes
	 * 
	 * @access public
	 * @param string $name Name to check
	 * @return int Index
	 */
	public function getNameIndex($name)
	{
		
		if (!isset($this->nameIndexes[$name]))
			$this->nameIndexes[$name] = -1;
			
		$this->nameIndexes[$name]++;
		return $this->nameIndexes[$name];
		
	}
	
	/** Resets all values
	 * 
	 * Sets all values and innerHTML's to ""
	 * 
	 * @access public
	 * @return void
	 */
	public function reset()
	{
		
		foreach ($this->elements as $element) {
			
			$element->{$element->getClosed() ? 'value' : 'innerHTML'} = '';
			$element->setRemember(false);
			
		}
		
	}
	
	/** Checks if all elements are filled in correctly and, if not, registers error messages.
	 * 
	 * Example:
	 * <code>
	 * $form = $this->form('form');
	 * 
	 * if ($form->validate('An error has occured')) {
	 * 
	 * 	echo "Form has been processed correctly.";
	 * 
	 * }
	 * </code>
	 * 
	 * @access public
	 * @param string $errorMSG Error title to show, in case of an error.
	 * @return bool Validated (returns void if form is not submitted).
	 */
	public function validate($errorMSG)
	{
		
		// not the right request-type
		if ($_SERVER['REQUEST_METHOD'] != strtoupper($this->attributes['method']))
			return null;
		
		// validate
		$container = (strtolower($this->attributes['method']) == 'post') ? $_POST : $_GET;
		
		if (empty($container[$this->form_ID]))
			return;
			
		$errors = array();
		
		foreach($this->elements as $element) {
			
			$error = $element->validate($container);
			if ($error !== true)
				$errors[] = $error;
			
		}
		
		// no errors
		if (empty($errors))
			return true;
			
			
		// has errors
		$this->layout->show_error($errorMSG, "<ul><li>".implode("</li><li>", $errors)."</li></ul>");
		return false;
		
	}
	
	/** Builds the form(-elements)
	 * 
	 * Returns an array, based on the names.
	 * The last element of the array is 'form', this is a hidden input and is it required to be within the form.
	 * e.g.
	 * You've got 3 elements, with the names 'full_name', 'sex' and 'sex'(2x radio-button).
	 * the return value of this function is:
	 * <code>
	 * array (
	 * 	'full_name' => array ( [0] => "full_name-HTML" ),
	 * 	'sex' => array (
	 * 		[0] => "sex_1-HTML",
	 * 		[1] => "sex_2-HTML"
	 * 	)
	 * 	'form' => "FORM_Identifier HTML"
	 * )
	 * </code>
	 * 
	 * @access public
	 * @param int $indent Number of tabs to preceed the form.
	 * @return mixed See description.
	 */
	public function build($indent = 0)
	{
		
		// check tabs
		if (is_numeric($indent)) {
			
			$temp = "";
			for($i = 0; $i < $indent; $i++)
				$temp .= "\t";
			
			$indent = $temp;
			
		}
			
		// create array
		$elements = array();
		
		foreach($this->elements as $element) {
			
			$name = preg_replace('/[^a-zA-Z0-9_]/', '', $element->name);
			$temp = $element->build($indent);
			
			$index = $element->getIndex();
			while (!empty($elements[$name][$index]))
				$index++;
				
			
			$elements[$name][$index] = $temp;
			
		}
		
		$elements['form'] = "<input type=\"hidden\" name=\"".$this->form_ID."\" value=\"1\" />\n";
		
		return $elements;
		
	}
	
	/** Sets all elements to the values given by $array
	 * 
	 * $array can also be an object.
	 * 
	 * @access public
	 * @param mixed $array Associative array like $_POST or $_GET
	 * @return void
	 */
	public function fill($array)
	{
		
		// convert to array
		if (is_object($array))
			$array = (array) $array;
			
		
		// append
		foreach($this->elements as $element)
		{
			
			$element->fill($array);			
			
		}
		
	}
	
	/** Gets all relevant POST-data.
	 * 
	 * @access public
	 * @return array Relevant POST-data
	 */
	public function values()
	{
		
		// strip slashes
		require_once 'system/functions/controller.handlePOST.inc.php';
		
		// prepare
		$values = array();
		$container = (strtolower($this->attributes['method']) == 'post') ? $_POST : $_GET;
		
		foreach($this->elements as $element) {
			
			if ($element->getAttribute('name') != false) {
				
				if (empty($this->uploads[$element->getAttribute('name')])) {
					
					// get value
					$value = (!$element->isEmpty($container)) ? $element->getPostValue($container) : "";
				
					// assign value
					if (is_null($element->nameIndex)) {
						
						$values[$element->getAttribute('name')] = $value;
						
					} else {
						
						$values[str_replace('[]', '', $element->getAttribute('name'))][$element->nameIndex] = $value;
						
					}
					
					
				} else {
					
					$value = $this->uploads[$element->getAttribute('name')]->value();
					
					// assign value
					if (is_null($element->nameIndex)) {
						
						$values[$element->getAttribute('name')] = $value;
						
					} else {
						
						$values[str_replace('[]', '', $element->getAttribute('name'))][$element->nameIndex] = $value;
						
					}
					
				}
				
			}
			
		}
		
		return $values;
		
	}
	
}

/** Select-element
 * 
 * This class contains a new select-element of the form.
 * Do never create this class this way. Always use {@link HTML_Form::select}.
 *
 * @name Select-element
 * @package helpers
 * @subpackage forms
 */
class HTML_Select extends HTML_Element
{
	
	/** Contains all options
	 * 
	 * @access protected
	 * @see option
	 */
	protected $options;
	
	/** contains the default option
	 * 
	 * @access protected
	 */
	protected $default = null;
	
	/** Sets a new option
	 * 
	 * @access public
	 * @param string $name Name of the option
	 * @param string $value Value of the option
	 * @return HTML_Select this
	 */
	public function option($name, $value = null)
	{
		
		$this->options[] = (object) array('name' => $name, 'value' => $value);
		return $this;
		
	}
	
	/** Sets the default value
	 * 
	 * if $name equals null, the default will be unset.
	 * 
	 * @access public
	 * @param string $name Name of the option
	 * @return HTML_Select this
	 */
	public function setDefault($name = null)
	{
		
		$this->default = $name;
		
	}
	
	/** Validate element
	 * 
	 * Checks if this element's value is filled in correctly.
	 * The $containing should be either $_POST or $_GET.
	 * You will probably never use this function, but you'll use {@link FORM_Element::validate} instead.
	 * 
	 * @access public
	 * @param array $containing Container.
	 * @return mixed true on success, errorMessage on failure.
	 */
	public function validate($containing)
	{
		
		if (!$this->required)
			return true;
		
		// get value value
		$value = ($this->isEmpty($containing) ? '' : $this->getPostValue($containing));
		
		if ($value == sha1($this->default))
			return $this->errorMessage;
		
		// check values
		foreach($this->options as $option) {
			
			if ($option->value === null) {
				
				if ($option->name == $value)
					return true;
				
				
			} else {
				
				if ($option->value == $value)
					return true;
					
				
			}
			
		}	
			
		// it failed
		return $this->errorMessage;
		
	}
	
	/** 
	 * mule-function, should not be working at this element
	 */
	public function setValidator() 
	{
		
		throw new FormException(1, 'You can not use the setValidator()-function, this does not work.');
		
	}
	
	/** Builds the HTML-code
	 * 
	 * You will probably never call this function, use the function of the FORM_Element instead.
	 * 
	 * @access public
	 * @param string $tabs Prefix for every new line
	 * @return string HTML-code
	 */
	public function build($tabs = "")
	{
		
		// empty?		
		$empty = $this->isEmpty($_POST);;

		// POST?
		if ($empty == false && $this->remember) {
			
			$this->setAttribute('value', $this->getPostValue($_POST));
			
		}
		
		// opening tag
		$html = $tabs . '<' . $this->type;
		
		if (!empty($this->attributes)) {
			
			// check for name-attribute
			if (!empty($this->attributes['name'])) {
				
				if ($this->nameIndex !== null && strpos($this->attributes['name'], '[]') !== false) {
					
					$this->attributes['name'] = str_replace('[]', '['.$this->nameIndex.']', $this->attributes['name']);
					
				}
				
			}
			
			// set attributes
			foreach($this->attributes as $key => $value) {
				
				if ($key != "innerHTML" && $key != 'value' && $value !== false && $value !== null)
					$html .= ' '.htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
					
					
			}
					
		}
		
		// close tag
		$html .=  ">\n";
		
		if ($this->default !== null)
			$html .= '<option value="'.sha1($this->default).'">'.(htmlspecialchars($this->default)).'</option>'."\n";

		// options
		if (!empty($this->options)) 
			foreach($this->options as $option) {
				
				$html .= '<option'.(($option->value !== null) ? ' value="'.htmlspecialchars($option->value).'"' : '').((($option->value !== null && $option->value == $this->getAttribute('value')) || $option->name == $this->getAttribute('value')) ? ' selected="selected"' : '').'>'.$option->name."</option>\n";
				
			}
		
		// close tag
		$html .= "</".$this->type.">\n";
		
		return $html;
		
	}
	
	
}


/** Upload-element
 * 
 * This class contains a new upload-element of the form.
 * Do never create this class this way. Always use {@link HTML_Form::upload}.
 *
 * @name Upload-element
 * @package helpers
 * @subpackage forms
 */
class HTML_Upload extends HTML_Element
{
	
	/** Max size of the file
	 * 
	 * @access private
	 * @var int $maxSize
	 */
	private $maxSize;
	
	/** All error-messages
	 * 
	 * @access private
	 * @var array errors
	 */
	private $errors;
	
	/** Constructor
	 * 
	 * @access public
	 * @param string $name Name of the input.
	 * @param HTML_Form $parent Parent element.
	 * @param int $maxSize Maximum size of the file in bytes.
	 * @return HTML_Upload
	 */
	public function __construct($name, $parent, $maxSize)
	{
		
		parent::__construct('input', $parent, true);
		$this->setAttribute('type', 'file');
		$this->setAttribute('name', $name);
		
		$this->maxSize = $maxSize;
		
	}
	
	/** Set this element to required.
	 * 
	 * If this element is not filled in, and $required is true, the validation failed.
	 * 
	 * @access public
	 * @param bool $required Is it required?
	 * @param string $noUpload ErrorMessage to show when no file is uploaded.
	 * @param string $toBig ErrorMessage to show when the uploaded file is to big.
	 * @param string $unknownError ErrorMessage to show when another error has occured.
	 * @return HTML_Element $this
	 */
	public function setRequired($required, $noUpload = "You do need to upload a file.", $toBig = "This file is to big.", $unknownError = "An error has occured, please try again later.")
	{
		
		if (empty($this->attributes['name']) && $required)
			throw new FormException("You have to set a 'name' before you set the required-variable");
		
		
		$this->validator= null;
		$this->required = $required;
		$this->errors	= array (
				
				"noUpload" => $noUpload,
				"toBig"	   => $toBig,
				"unknown"  => $unknownError
			);
		
		return $this;
		
	}
	
	/** Sets this element to required, and register (multiple) validators
	 * 
	 * This function accepts multiple types of input.
	 * Example:
	 * <code>
	 * // using a single validator
	 * $element->setValidator(new UPLOAD_Validator(array("image/jpg"), "You can only upload JPG's"));
	 * 
	 * // using multiple validators
	 * $element->setValidator(array(
	 * 		new UPLOAD_Validator(array("image/jpg"), "You can only enter JPG's or JPEG's"),
	 * 		new SPECIAL_UPLOAD_Validator(array("image/jpeg"), "You can only enter JPG's or JPEG's")
	 * 	));
	 * 
	 * // using single array validator
	 * $element->setValidator(array("You can upload JPG's" => array("image/jpg")));
	 * 
	 * // using single string validator
	 * $element->setValidator(array("You can upload JPG's" => "image/jpg"));
	 * 
	 * // using multiple array validators
	 * $element->setValidator(array("You can only upload JPG's or BMP's" => array("image/jpg", "image/bmp")));
	 * 
	 * // using multiple string validators
	 * $element->setValidator(array(
	 * 	"You can only upload JPG's or BMP's" => "image/jpg",
	 * 	"You can only upload JPG's or BMP's" => "image/bmp"
	 * ));
	 * </code>
	 * 
	 * If the second parameter($noUpload) is equal to null, than is not uploading allowed.
	 * Example:
	 * <code>
	 * $element->setValidator(array('You can only upload JPG-images' => 'image/jpg'), null);
	 * // This element will validate if you upload a JPG-image, or if you do not upload at all.
	 * </code>
	 * 
	 * @access public
	 * @param mixed $validator Validators
	 * @param string $noUpload ErrorMessage to show when no file is uploaded.
	 * @param string $toBig ErrorMessage to show when the uploaded file is to big.
	 * @param string $unknownError ErrorMessage to show when another error has occured.
	 * @return HTML_Element $this
	 */
	public function setValidator($validator, $noUpload = "You do need to upload a file.", $toBig = "This file is to big.", $unknownError = "An error has occured, please try again later.", $hacker = "Please stop trying to find vurnabilities on this website.")
	{
				
		// fool-proof
		if (empty($this->attributes['name']))
			throw new FormException("You have to set a 'name' before you set the validator");
		
		
		// set required and errors
		$this->required = true;
		$this->errors	= array (
				"noUpload" => $noUpload,
				"toBig"	   => $toBig,
				"unknown"  => $unknownError,
				"hacker"   => $hacker
			);
		
		// validator, or more?
		if (is_array($validator)) {
			
			$this->validator = array();
			
			foreach($validator as $key => $val) {
				
				if (is_string($val))
					$val = array($val);
				
				$this->validator[] = (is_string($key) && is_array($val)) ? new UPLOAD_Validator($val, $key) : $val;
				
			}
			
		} else {
			
			$this->validator = array($validator);
			
		}
		
		return $this;
		
	}
	
	/** Validates input
	 * 
	 * Checks if the upload has succeed
	 * 
	 * @access public
	 * @param void $containing Just for compatibility methods.
	 * @return mixed True on success, errormessage on error.
	 */
	public function validate($containing)
	{
		
		// check himself
		if ($this->required) {
			
			$name = $this->getAttribute('name');
			
			// validate upload
			if (empty($_FILES[$name]['name'])) {
				
				if (!is_null($this->errors['noUpload']))
					return $this->errors['noUpload'];
				
				// validated
				return true;
				
			}
				
			// check size
			if ($_FILES[$name]['size'] > $this->maxSize)
				return $this->errors['toBig'];
				
			// check error-code
			if ($_FILES[$name]['error'] != UPLOAD_ERR_OK)
				return $this->errors['unknown'];
				
			// 0x00-byte exploit
			if (stristr($_FILES[$name]['name'], "\0") !== false)
				return $this->errors['hacker'];
			
			
			// need to validate?	
			if ($this->validator !== null) {
				
				// get MIME-type
				$mime = YS_Helpers::Load()->file->getMimeType($_FILES[$name]['tmp_name'], substr($_FILES[$name]['name'], strrpos($_FILES[$name]['name'], '.') + 1));
	
				// unknown mime-type, lets trust the client :+)
				if ($mime == 'application/octet-stream')
					$mime = $_FILES[$name]['type'];
				
				// remember new MIME-type
				else
					$_FILES[$name]['type'] = $mime;
				
				foreach ($this->validator as $validator) {
				
					// use validator
					$val = $validator->validate($name, $mime);
					if ($val !== true)
						return $val;
						
				}
					
			}
			
		}
		
		return true;
		
	}
	
	/** Returns the right part of the $_FILES-array
	 * 
	 * @access public
	 * @return array
	 */
	public function value()
	{
		
		if (empty($_FILES[$this->getAttribute('name')]))
			return array();
		
		return $_FILES[$this->getAttribute('name')];
		
	}
	
	/** Generates the right HTML-code
	 * 
	 * @access public
	 * @param string $tabs suffix for newline
	 * @return string HTML-code
	 */
	public function build($tabs = "")
	{
		
		// opening tag
		//$html  = $tabs . '<input type="hidden" name="MAX_FILE_SIZE" value="'.htmlspecialchars($this->maxSize).'" />' . "\n";
		$html = $tabs . '<' . $this->type;
		
		if (!empty($this->attributes))
			foreach($this->attributes as $key => $value)
				if ($key != "innerHTML" && $value != '' && is_string($value))
					$html .= ' '.htmlspecialchars($key).'="'.htmlspecialchars($value).'"';
					
		
		$html .= ($this->close) ? " />\n" : ">";
		
		// close tag?
		if (!$this->close)
			$html .= "</".$this->type.">\n";
		
		return $html;
		
	}
	
}

/** Validates the POST-data of an element
 * 
 * You can create your own version of this validator, that only works if $_POST['type'] is 'business' this way:
 * <code>
 * class Special_validator extends FORM_Validator
 * {
 * 	
 * 	public function validate($name, $input)
 * 	{
 * 		// return false for everything that is not a business
 * 		if ($_POST['type'] != 'business')
 * 			return false;
 *		
 * 		// let the parent find out
 * 		return parent::validate($name, $input);		
 * 
 * 	}
 * 
 * }
 * </code>
 *
 * @name Form-validator
 * @package helpers
 * @subpackage forms
 */
class FORM_Validator
{
	
	/** Regex to match the input
	 * 
	 * @access protected
	 * @var regex $regex
	 */ 
	protected $regex;
	
	/** Errormessage to show in case the validation fails.
	 * 
	 * @access protected
	 * @var string $errorMessage
	 */
	protected $errorMessage;
	
	/** Constructor
	 * 
	 * @access public
	 * @param regex $regex Regex-pattern to match.
	 * @param string $errorMessage Message to show when the validation fails.
	 * @return FORM_Validator 
	 */
	public function __construct($regex, $errorMessage)
	{
		
		$this->regex = $regex;
		$this->errorMessage = $errorMessage;
		
	}
	
	/** Validate input
	 * 
	 * This function is called by the corresponding HTML_Element instance.
	 * 
	 * @access public
	 * @param string $name Name of the element.
	 * @param string $input Value to match.
	 * @return mixed True on success, errorMessage on failure.
	 */
	public function validate($name, $input)
	{
		
		if (preg_match($this->regex, $input) == 1)
			return true;
		
		return $this->errorMessage;
		
	}
	
}

/** Validates the Upload of an element
 * 
 * You can create your own version of this validator, that only works if $_POST['type'] is 'business' this way:
 * <code>
 * class Special_upload_validator extends UPLOAD_Validator
 * {
 * 	
 * 	public function validate($name, $mimeType)
 * 	{
 * 		// return false for everything that is not a business
 * 		if ($_POST['type'] != 'business')
 * 			return false;
 *		
 * 		// let the parent find out
 * 		return parent::validate($name, $mimeType);		
 * 
 * 	}
 * 
 * }
 * </code>
 *
 * @name Upload-validator
 * @package helpers
 * @subpackage forms
 */
class UPLOAD_Validator
{
	
	/** Mimetypes that are allowed to upload
	 * 
	 * @access protected
	 * @var array $mimeTypes
	 */ 
	protected $mimeTypes;
	
	/** Errormessage to show in case the validation fails.
	 * 
	 * @access protected
	 * @var string $errorMessage
	 */
	protected $errorMessage;
	
	/** Constructor
	 * 
	 * @access public
	 * @param array $mimeTypes Mimetypes that are allowed to upload.
	 * @param string $errorMessage Message to show when the validation fails.
	 * @return UPLOAD_Validator 
	 */
	public function __construct(array $mimeTypes, $errorMessage)
	{
		
		$this->mimeTypes = $mimeTypes;
		$this->errorMessage = $errorMessage;
		
	}
	
	/** Validate input
	 * 
	 * This function is called by the corresponding HTML_Upload instance.
	 * 
	 * @access public
	 * @param string $name Name of the element.
	 * @param string $mimeType File's MIME-type
	 * @return mixed True on success, errorMessage on failure.
	 */
	public function validate($name, $mimeType)
	{
		
		 if (in_array($mimeType, $this->mimeTypes))
		 	return true;
		 	
	 	return $this->errorMessage;
		
	}	
	
}
