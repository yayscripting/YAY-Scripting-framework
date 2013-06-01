<?php
/**
 * @author YAY!Scripting
 * @package files
 */
 
namespace System\Helper;

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
		
		$node = new \SimpleXMLElement('<'.$nodeName.'></'.$nodeName.'>');
		$node = $this->treeAdd($array, $node);
		
		return $node;
		
	}
	
	private function treeAdd($array, $parent)
	{
		
		if (isset($array['@attributes']) && isset($array['@values'])) {
			
			foreach ($array['@attributes'] as $aKey => $aValue) {
				
				$parent->addAttribute($aKey, $aValue);
						
			}
			
			if (is_array($array['@values'])) {
				
				$array = $array['@values'];
			
			}else {
				
				$text = $parent->createTextNode($array['@values']);
				$parent->addChild($text);
				return;
				
			}
			
		}
		
		foreach ($array as $key => $value) {
			
			if (is_Array($value)) {
				
				// @attributes/@value
				if (isset($value['@attributes']) && isset($value['@values'])) {
					
					if (is_array($value['@values'])) {
						
						$child = $parent->addChild($key);
						$this->treeAdd($value['@values'], $child);
						
					}else {
						
						if (is_bool($value))
							$value = ($value) ? 'true' : 'false';
						
						$child = $parent->addChild($key, $value['@values']);
						
					}
					
					foreach ($value['@attributes'] as $aKey => $aValue) {
				
						$child->addAttribute($aKey, $aValue);
								
					}
					
					
				// associatieve array
				}else if ($this->is_assoc($value)) {
					
					$child = $parent->addChild($key);
					$this->treeAdd($value, $child);
				
				// numeric array	
				} else {
					
					foreach ($value as $nValue) {
						
						$child = $parent->addChild($key);
						$this->treeAdd($nValue, $child);
						
					}
					
				}
				
			} else {
				
				if (is_bool($value))
					$value = ($value) ? 'true' : 'false';
				
				$parent->addChild($key, $value);
				
			}
			
		}
		
		return $parent;
		
	}
	
	// Thanks Captain kurO, StackOverflow
	private function is_assoc(array $array) {
		
		return (bool)count(array_filter(array_keys($array), 'is_string'));
		
	}
	
	
}