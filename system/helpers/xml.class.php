<?php
/**
 * @author YAY!Scripting
 * @package files
 */
 
namespace System\Helper;

require_once 'system/external/xml.php';
\System\External\Xml\Array2XML::init();
\System\External\Xml\XML2Array::init();

/** Xml-helper
 * 
 * This helper can be used to transform XML to arrays and vica-versa
 *
 * @name Xml
 * @package helpers
 * @subpackage Xml
 */
class Xml extends \System\Helper
{
	
	// See http://www.lalit.org/lab/convert-php-array-to-xml-with-attributes
	public function Array2XML($array, $nodeName = 'Document')
	{
		
		$xml = \System\External\Xml\Array2XML::createXML($nodeName, $array);
		return $xml;
		
	}
	
	// See http://www.lalit.org/lab/convert-xml-to-array-in-php-xml2array
	public function XML2Array($xml)
	{
		
		$array = \System\External\Xml\Array2XML::createArray($xml);
		return $array;
		
	}
	
}