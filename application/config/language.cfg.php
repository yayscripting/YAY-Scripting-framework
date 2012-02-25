<?php
return array(

	'language_on' => true,
	'default_language' => 'en',		// default dir prefix. If this value is null, you use /application/views/pages, if not: /application/views/{current_lang|default_language}/pages
	'return_keyword_on_error' => false,	// If this option is active, the language-system will automatically return the keyword given to the __-function, when it is the current language.
	'return_default_on_error' => true,	// If this option is active, the language system will automatically return the default variant, if the translation is not present yet.
	'use_slash' => true, 			// this option lets the language only be seperated with a / if it is the only parameter.
	
);