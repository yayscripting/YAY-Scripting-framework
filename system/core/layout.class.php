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
class YS_Layout Extends Smarty 
{
	
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
 	
	/** Constructor
	 * 
	 * @access public
	 * @return YS_Layout
	 */
	public function __construct()
	{
		
		// call parent
		parent::__construct();
		
		// get config/helpers
		global $_config;
		global $_helpers;
		
		$this->config  = $_config;
		$this->helpers = $_helpers;
		
		// smarty debug
		$this->debugging = (($this->config->script->debug_mode === true) ? ((!empty($_GET['debug'])) ? true : false) : false);
		
		// set compile dir
		$this->compile_dir = $this->config->cache->directory;
	
		// javascript use
		$this->use_js	= false;
		$this->headers	= array();
		
		// environment
		$this->environment = YS_Environment::Load();
		
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
		
		// assign config
		$this->assign('_config', $this->config );
		
		// assign javascript
		$this->assign('use_js', $this->use_js);
	
		// check view
		if(!empty($view)){
	
			// get path
			if (substr($view, 0, 7) != 'errors/') {
				
				$path = ($this->environment->get() !== false ? 'application/views/'.$this->environment->folder.'/' : 'application/views/pages/').$view.'.tpl';
			
			} else {
				
				$path = 'application/views/'.$view;
			
			}
	
			// check exists
			if(file_exists($path)){
		
				// fetch current view
				$content = $this->fetch($path);
		
			}else{
				
				throw new LoadException('Error loading page: '.$path);
				
			}
			
		}
		
		// assign content to smarty
		$this->assign('headers', $this->build_headers());
		$this->assign('content', $content);
		
		// display layout
		if ($this->environment->get() !== false) {
			
			$this->display('application/views/molds/'.$this->environment->folder.'.tpl');
		
		} else {
			
			$this->display('application/views/molds/'.$this->mold.'.tpl');
			
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
		usort($this->headers, array($this, "sort"));
		
		// create string
		$string = "";
		foreach($this->headers as $header) {
			
			$string .= $this->build_header($header['type'], $header['options'], $header['noshort']) . "\n";	
			
		}
		
		// return string
		return trim($string);
		
	}
	
	/** Sort-function
	 * 
	 * Used in {@link build_headers}.
	 * 
	 * @access private
	 * @param array $el_1 First element.
	 * @param array $el_2 Second element.
	 * @return int is_script_item
	 * @see build_headers
	 */
	private function sort($el_1, $el_2)
	{
		if ($el_1['type'] == 'script')
			return 1;
		
		return 0;
		
	}
	
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
		
		$this->assign('pagination', $buttons);
		$this->assign('total', $total);
		$this->assign('page', $page);
		
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
	
		// assign box content
		$this->assign('_box_type', $type);
	
		// assign box content
		$this->assign('_box_title', $title);
	
		// assign box content
		$this->assign('_box_content', $description);
		
		// fetch box into _box
		$this->assign('_box', $this->fetch('application/views/elements/box.tpl'));
		
	
	}

}