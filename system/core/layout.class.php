<?php
/**
 * @author YAY!Scripting
 * @package files
 */

/** 
 * Loads parent class
 */
require_once('system/external/Smarty/Smarty.class.php');

/** Core
 * 
 * This class loads the right page and handles exceptions.
 *
 * @name Router
 * @package core
 * @subpackage Controller
 */
class YS_Layout Extends YS_Singleton
{
	
	/** Smarty
	 * 
	 * @access private
	 * @var object
	 */
	private $smarty; 
	
	/** Config
	 * 
	 * @access private
	 * @var array
	 */
	private $config;
	
	/** Helpers
	 * 
	 * @access private
	 * @var YS_Helpers
	 */
	private $helpers;
	
	/** All head elements
	 * 
	 * @access private
	 * @var array
	 */
	private $headers;
	
	/** Using Javascript?
	 * 
	 * @access private
	 * @var bool
	 */
	private $use_js;
	
	/** Environment
	 * 
	 * @access private
	 * @var YS_Environment
	 */
 	private $environment;
 	
 	/** Mold
 	 * 
 	 * @access private
 	 * @var string
 	 */
 	private $mold = 'layout';
 	/** Title 
	 *  
	 * @access private 
	 * @var string 
	 */ 
	 private $pageTitle;
 	
	/** Constructor
	 * 
	 * @access public
	 * @return YS_Layout
	 */
	public function __construct()
	{
		
		// check singleton
		parent::__construct();
		
		// get config/helpers
		$this->config  = YS_Config::Load();
		$this->helpers = YS_Helpers::Load();
		
		// get smarty
		$this->smarty = new Smarty();
		
		// smarty debug
		$this->smarty->debugging = (($this->config->script->debug_mode === true) ? ((!empty($_GET['debug'])) ? true : false) : false);
		
		// set compile dir
		$this->smarty->compile_dir = $this->config->cache->directory;
	
		// javascript use
		$this->use_js	= false;
		$this->headers	= array();
		
		// environment
		$this->environment = YS_Environment::Load();
		
	}
	
	/** Catches all functions, and sending them through to smarty.
	 * 
	 * @access public
	 * @param string $func Functionname
	 * @param mixed $arguments Parameters
	 * @return mixed Return value of the particulary function
	 */
	public function __call($func, $arguments = null)
	{
		
		return call_user_func_array(array($this->smarty, $func), $arguments);
		
	}
	
	
	/** Loads a view
	 * 
	 * Loads a view, if you've got an admin-page loaded, the view is loaded from views/admin/, else the view is loaded from views/pages/ .
	 * 
	 * @access public
	 * @param string $view name of the view
	 * @return void
	 */
	public function view($view)
	{
		
		// get lang folder
		$langFolder = YS_Language::Load()->getDir();
		
		if (!is_null($langFolder))
			$langFolder .= '/';
		
		// assign config
		$this->smarty->assign('_config', $this->config );
		
		// assign javascript
		$this->smarty->assign('use_js', $this->use_js);
		
		// box
		$this->smarty->assign('_box', $_SESSION['system']['_box']);
		unset($_SESSION['system']['_box']);
	
		// check view
		if(!empty($view)){
	
			// get path
			if (substr($view, 0, 7) != 'errors/') {
				
				$path = ($this->environment->get() !== false ? 'application/views/'.$langFolder.$this->environment->folder.'/' : 'application/views/'.$langFolder.'pages/').$view.'.tpl';
			
			} else {
				
				$path = 'application/views/'.$langFolder.$view;
			
			}
			
			// check exists
			if(file_exists($path)){
		
				// fetch current view
				$content = $this->smarty->fetch($path);
		
			}else{
				
				throw new LoadException('Error loading page: '.$path);
				
			}
			
		}
		
		// assign content to smarty
		$this->smarty->assign('title', $this->pageTitle);
		$this->smarty->assign('headers', $this->build_headers());
		$this->smarty->assign('content', $content);
		
		// display layout
		if ($this->environment->get() !== false) {
			
			$this->smarty->display('application/views/'.$langFolder.'molds/'.$this->environment->folder.'.tpl');
		
		} else {
			
			$this->smarty->display('application/views/'.$langFolder.'molds/'.$this->mold.'.tpl');
			
		}
	
	}
	
	/** Shows an error message
	 * 
	 * @access public
	 * @param string $title Error title.
	 * @param string $description Error description.
	 * @return void
	 * @see show_success
	 * @see display_box
	 */
	public function show_error($title, $description)
	{
	
		$this->display_box('error', $title, $description);
	
	}
	
	/** Sets a diffrent mold
	 * 
	 * @access public
	 * @param string $mold Mold
	 * @return void
	 */
	public function set_mold($mold)
	{
	
		$this->mold = $mold;
	
	}
	
	/** Shows an success message
	 * 
	 * @access public
	 * @param string $title Success title.
	 * @param string $description Success description.
	 * @return void
	 * @see show_error
	 * @see display_box
	 */
	public function show_success($title, $description)
	{
	
		$this->display_box('success', $title, $description);
	
	}
	
	/** Adds a javascript header
	 * 
	 * The resource will be loaded asynchroniously by default.
	 * Filename is without .js, and from /resources/javascript/ if $base equals false
	 * 
	 * @access public
	 * @param string $name Filename 
	 * @param bool $async Load asynchroniously?
	 * @param bool $base Complete url or filename?
	 * @return void
	 * @see style
	 * @see set_header
	 */
	public function javascript($name, $async = true, $base = false) 
	{
	
		// check for javascript used before
		if($this->use_js == false){
		
			// set true
			$this->use_js = true;
			
			// check auto include mootools
			if($this->config->javascript->mootools_path !== false){
			
				// include mootools
				$this->set_header("script", array("type" => "text/javascript", "async"=>"", "src" => $this->config->javascript->mootools_path), true);
			
			}
		
		}
		
		$name = ($base) ? $name : "/application/resources/javascript/".$name.".js";
		
		// attributes
		$attributes = array("type" => "text/javascript", "src" => $name);
		if ($async !== true)
			$attributes['async'] = false;
		
		// add
		$this->set_header("script", $attributes, true);
		
	}
	
	/** Loads a style(css) file.
	 * 
	 * If an admin-page is loaded, style is loaded from /resources/style/admin/, if not: /resources/style/pages/ is used.
	 * Filename is from /resources/style/XXX/, without .css if $base equals false
	 * 
	 * @access public
	 * @param string $name Filename 
	 * @param bool $base Complete url or filename?
	 * @return void
	 * @see javascript
	 * @see set_header
	 */
	public function style($name, $base = false)
	{
		
		$prefix = "/application/resources/style/".(($this->environment->get() !== false) ? $this->environment->folder . "/" : "pages/");		
		$this->set_header("link", array("rel" => "stylesheet", "type" => "text/css", "href" => (!$base ? $prefix : '') .$name.".css"));
		
	}
	
	/** Sets a new page-title.
	 * 
	 * Still needs to be parsed at the mold ({$title})
	 * 
	 * @access public
	 * @param string $title New title
	 * @return void
	 * 
	 */
 	public function setTitle($title)
 	{
 		
 		$this->pageTitle = $title;
 		
	}
	
	/** Sets a new head-element
	 * 
	 * @access public
	 * @param string $type Element type.
	 * @param array $options All attributes.
	 * @param bool $noshort Short-close (</>).
	 * @return void
	 * @see javascript
	 * @see style
	 */
	public function set_header($type, $options, $noshort = false)
	{
		
		if (array_search(array('type' => $type, 'options' => $options, 'noshort' => $noshort), $this->headers, false) === false)
			$this->headers[] = array('type' => $type, 'options' => $options, 'noshort' => $noshort);
		
	}
	
	/** Generate HTML-code of the head-elements
	 * 
	 * Javascript-files are loaded last.
	 * 
	 * @access private
	 * @return string HTML-code
	 * @see build_header
	 * @see sort
	 */
	private function build_headers()
	{
		
		// empty check
		if (empty($this->headers))
			return "";
			
		//sort
		//usort($this->headers, array($this, "sort"));
		
		// create string
		$string = "";
		foreach($this->headers as $header) {
			
			$string .= $this->build_header($header['type'], $header['options'], $header['noshort']) . "\n";	
			
		}
		
		// return string
		return trim($string);
		
	}
	/* OUT OF ORDER, BUGGING SYSTEMS
	/** Sort-function
	 * 
	 * Used in {@link build_headers}.
	 * 
	 * @access private
	 * @param array $el_1 First element.
	 * @param array $el_2 Second element.
	 * @return int is_script_item
	 * @see build_headers
	 * /
	private function sort($el_1, $el_2)
	{
		var_dump($el_1, $el_2);
		echo '<br /><br /><br />';
		
		if ($el_1['type'] != 'script' && $el_2['type'] == 'script')
			return 1;
			
		if ($el_1['type'] == 'script' && $el_2['type'] != 'script')
			return -1;
			
		return 0;
		
	}*/
	
	/** Builds a single head-element into HTML
	 * 
	 * @access private
	 * @param string $type Element type.
	 * @param array $options All attributes.
	 * @param bool $noshort Short-close (</>).
	 * @return string HTML-code
	 * @see build_headers
	 */
	private function build_header($type, $options, $noshort = false)
	{
		
		// check for javascript async-loading
		if ($type == 'script' && isset($options['async']) == false)
			$options['async'] = 'true';
		
		// loop options
		$atrs = "";
		foreach($options as $attribute => $value){
			
			if ($value == false) // 0, false, ""
				continue;
			
			// set
			$atrs .= " ".$attribute."=\"".$value."\"";
		
		}
	
		// return
		return "<".$type."".$atrs."".($noshort ? '' : ' /').">".($noshort ? '</'.$type.'>' : '');
	
	}
	
	/** Creates pagination
	 * 
	 * Appends to the 'pagination'-variable
	 * This function does also assign the $page and $total-variable.
	 * 
	 * Smarty implention: 
	 * <code>
	 * {foreach $pagination as $i => $bool}
	 * 
	 * 	{if $bool !== null}
	 * 	<a class="{if $bool}selected{else}unselected{/if}" href="/downloads/{$type}/pagina_{$i}.html">{$i}</a>
	 * 	{else}
	 * 	...
	 * 	{/if}
	 * 
	 * {/foreach}
	 * </code>
	 * 
	 * More detailled: 
	 * <code>
	 * <div id="pagination">
	 * 
	 * 	<div id="center">
	 * 	
	 * 		<div id="pageNumber">Page {$page} of {$total}</div>
	 *	 	<div id="ranking">
	 * 		{foreach $pagination as $i => $bool}
	 * 			
	 * 			<div class="block{if $bool} selected{/if}">
	 * 			
	 * 				{if $bool !== null}
	 * 				<a href="/downloads/{$type}/pagina_{$i}.html">{$i}</a>
	 * 				{else}
	 * 				<span>...</span>
	 * 				{/if}
	 * 				
	 * 			</div>
	 * 			
	 * 		{/foreach}
	 * 		</div>
	 * 		
	 * 	</div>
	 * 
	 * </div>
	 * </code>
	 * 
	 * @access public
	 * @param int $page Current page
	 * @param int $total Total number of pages
	 * @return void
	 */
	public function pagination($page, $total)
	{
		
		// in this function: false->button, true->selected button, null->dots
		
		// prepare
		$buttons = array();
		
		// calculate
		$below = ($page - 4 <= 2);
		$upper = ($page + 4 >= $total - 1);
		
		// always on
		$buttons[$total] 	= false;
		$buttons[1]	 	= false;
		$buttons[$total - 1]	= false;
		$buttons[2]	 	= false;
		
		// current page
		$buttons[$page] = true;
		
		// 2 up/down
		$buttons[$page - 1] = false;
		$buttons[$page + 1] = false;
		$buttons[$page - 2] = false;
		$buttons[$page + 2] = false;
	
		$buttons[$page - 3] = ($below) ? false : null;
		$buttons[$page + 3] = ($upper) ? false : null;
		
		// check for unreal keys
		foreach ($buttons as $i => $bool) {
			
			if ($i <= 0 || $i > $total)
				unset($buttons[$i]);			
			
			
		}
		
		ksort($buttons);
		
		$this->smarty->assign('pagination', $buttons);
		$this->smarty->assign('total', $total);
		$this->smarty->assign('page', $page);
		
	}
	
	
	/** Builds the error/success box
	 * 
	 * @access private
	 * @param string $type 'error' or 'success'.
	 * @param string $title Error title.
	 * @param string $description Error description.
	 * @return void
	 * @see show_error
	 * @see show_success
	 */
	private function display_box($type, $title, $description)
	{
	
		// get lang folder
		$langFolder = YS_Language::Load()->getDir();
		
		if (!is_null($langFolder))
			$langFolder .= '/';
			
			
			
		// assign box content
		$this->smarty->assign('_box_type', $type);
	
		// assign box content
		$this->smarty->assign('_box_title', $title);
	
		// assign box content
		$this->smarty->assign('_box_content', $description);
		
		// fetch box into _box
		$_SESSION['system']['_box'] = $this->smarty->fetch('application/views/'.$langFolder.'elements/box.tpl');
	
	}

}