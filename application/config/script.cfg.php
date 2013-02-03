<?php
return array(

	'debug_mode' => false,		// enable smarty debugging
	'show_render_time' => true,	// Show render time at end of output (only works in browser mode, ignored in com, cronjob and cli mode | only works in debug mode)
	'default_controller' => 'home',	// Which controller will be used when accessing /
	'force_seo' => true		// Force SEO (accessing the default_controller won't work via e.g. home.html)
	
);