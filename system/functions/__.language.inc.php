<?php
/** This function translates words into other languages
 * 
 * @static
 * @param string $keyword The word to translate
 * @param string $lang The language to translate the keyword to, default is the current one (null).
 */
function __($keyword, $lang = null)
{
	
	// load language class
	static $language = null;
	if (is_null($language))
		$language = \System\Language::Load();
	
	
	return $language->translate($keyword, $lang);
	
}