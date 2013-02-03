<?php 

	header("Content-type: text/javascript"); 
	if (empty($_GET['wysiwyg_ID'])) exit; 
	
	$config = (object)require '../../../application/config/form.cfg.php';
	$config = $config->wysiwyg;

?>

if (typeof(xinha_editors) !== 'object') {

	var xinha_editors = ['<?php echo str_replace("'", '', htmlspecialchars($_GET['wysiwyg_ID']));?>'];

} else {

	xinha_editors[xinha_editors.length] = '<?php echo str_replace("'", '', htmlspecialchars($_GET['wysiwyg_ID']));?>';

}

var xinha_init    = null;
var xinha_config  = null;
var xinha_plugins = null;
var first = (!xinha_init);

xinha_init = xinha_init ? xinha_init : function()
{
 
	xinha_plugins = xinha_plugins ? xinha_plugins :
 	[
 <?php 
	 	foreach ($config->plugins as $i => $plugin) {
	 		
	 		if ($i < count($config->plugins) - 1)
	 			echo "\t '".htmlspecialchars($plugin)."',\n";
 			else 
	 			echo "\t '".htmlspecialchars($plugin)."'\n";
 		}
?>
	];
	 
	 // THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
	 if(!Xinha.loadPlugins(xinha_plugins, xinha_init)) return;
	  
	  
	  xinha_editors = xinha_editors ? xinha_editors : [];
	  xinha_config = xinha_config ? xinha_config : new Xinha.Config();
	  
	xinha_config.toolbar =
	[
<?php	
		foreach ($config->toolbar as $i => $toolbar) {
	 		
	 		echo "\t\t[";
	 		
	 		foreach ($toolbar as $j => $toolitem) {
	 			
	 			echo '"'.htmlspecialchars($toolitem).'"'.($j < count($toolbar) - 1 ?', ':'');
	 				 			
 			}				 				 				 	 			
	 			 		
	 		if ($i < count($config->toolbar) - 1)
	 			echo "],\n";
 			else 
	 			echo "]\n";
	 					 			 			 	 		
 		}
?>
	];
	  
	xinha_config.formatblock =
	{
 <?php 
 		end($config->formats);
 		$last = key($config->formats);
	 	foreach ($config->formats as $format => $tag) {
	 		
	 		if ($format != $last)
	 			echo "\t\t".'"'.$format.'": "'.$tag.'",'."\n";
 			else
	 			echo "\t\t".'"'.$format.'": "'.$tag.'"'."\n";
	 			 		
 		}
?>
	};
	
	xinha_config.pageStyleSheets = 
 	[
 <?php 
	 	foreach ($config->css_files as $i => $file) {
	 		
	 		if ($i < count($config->css_files) - 1)
	 			echo "\t '".htmlspecialchars($file)."',\n";
 			else 
	 			echo "\t '".htmlspecialchars($file)."'\n";
 		}
?>
	];
	 
	  
	xinha_config.showLoading = true;
	xinha_config.statusBar = false;
	xinha_config.stripBaseHref = true;
	xinha_config.baseHref = "<?php print 'http://'. $_SERVER['SERVER_NAME'] ?>";
	
	 
	<?php require_once 'contrib/php-xinha.php'; ?>
	if (xinha_config.Linker)
	{
		with(xinha_config.Linker)
		{
			<?php 
				xinha_pass_to_php_backend (
				      array(
				      'dir'          => '/system/external/xinha/',
				      'include'      => '/\.(php|shtml|html|htm|shtm|cgi|txt|doc|pdf|rtf|xls|csv)$/', // Regex or null
				      'exclude'      => null, // Regex or null
				      'dirinclude'   => null, // Regex or null
				      'direxclude'   => null // Regex or null
				      )
	      			);
	      		?>
	    	}
	}
	    
	xinha_config.ExtendedFileManager.use_linker = true;
	  
	if (xinha_config.ExtendedFileManager) {
		with (xinha_config.ExtendedFileManager)
	        {
	        	<?php
	
	        	// define backend configuration for the plugin
	        	$IMConfig = array();
	        	// the directories have to be writeable for php (that means 777 under linux)
	        	$IMConfig['max_foldersize_mb'] = 150;
	        	$IMConfig['files_dir'] = '../../../../../application/uploads';
	        	$IMConfig['images_dir'] = '../../../../../application/uploads';
	        	$IMConfig['files_url'] = '/application/uploads/';
	       		$IMConfig['images_url'] = '/application/uploads/';
	      		$IMConfig['images_enable_styling'] = false;
	       		$IMConfig['max_filesize_kb_image'] = (int)($config->max_image_size / 1024); // max image size in kilobytes
	       		// we can use the value 'max' to allow the maximium upload size defined in php_ini
	       		$IMConfig['max_filesize_kb_link'] = 'max';
        		$IMConfig['allowed_link_extensions'] = array("jpg","gif","js","pdf","zip","txt","psd","png","html","swf","xml","xls");
	
	            	xinha_pass_to_php_backend($IMConfig);
	
	            	?>
	        }
	}
	if (xinha_config.ImageManager) {
		with (xinha_config.ImageManager)
		{
			<?php
			xinha_pass_to_php_backend(
				array(
					'images_dir' => '../../../../../application/uploads',
					'images_url' => '/application/uploads/',
					'allow_upload' => (bool)$config->upload
				)
			);
			?>
		}
	}
	  
	xinha_editors   = Xinha.makeEditors(xinha_editors, xinha_config, xinha_plugins);
	  
	Xinha.startEditors(xinha_editors);
	
}

if (first)
	Xinha._addEvent(window,"load",xinha_init);
	
	