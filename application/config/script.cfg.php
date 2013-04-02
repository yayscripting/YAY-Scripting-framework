<?php
return array(

	'debug_mode' => true,		// enable smarty debugging
	'show_render_time' => true,	// Show render time at end of output (only works in browser mode, ignored in com, cronjob and cli mode | only works in debug mode)
	'default_controller' => 'home',	// Which controller will be used when accessing /
	'force_seo' => true,		// Force SEO (accessing the default_controller won't work via e.g. home.html)
	'permanent_session' => true	// Stores session-id in cookies and loads on re-visit. This function will only work if the session-files do not expire in this period. DO NOT FULLY RELY ON THIS FUNCTIONALITY, COOKIES CAN BE DELETED!
	
);