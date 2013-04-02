<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Helper;

/** Date-helper
 *
 * @name date
 * @package helpers
 * @subpackage date
 */

// create class
class Date extends \System\Helper
{
	
	/** names of the days, 0=monday.
	 * @access public
	 * @var array
	 */
	public $dayOfWeek;
	
	/** Names of the month, 0=january.
	 * @access public
	 * @var array
	 */
	public $month;
	
	/** Constructor
	* 
	* Loads {@link $dayOfWeek} and {@link $month}.
	* 
	* @access public
	* @return YSH_Date
	*/
	public function __construct()
	{
	 	
	 	// set core
	 	parent::__construct();
	 	
	 	// set dutch day names and months
	 	$this->dayOfWeek = array(
	 		1 => 'maandag',
	 		2 => 'dinsdag',
	 		3 => 'woensdag',
	 		4 => 'donderdag',
	 		5 => 'vrijdag',
	 		6 => 'zaterdag',
	 		7 => 'zondag'
	 	);
	 	
	 	$this->month = array(
	 		1 => 'januari',
	 		2 => 'februari',
	 		3 => 'maart',
	 		4 => 'april',
	 		5 => 'mei',
	 		6 => 'juni',
	 		7 => 'juli',
	 		8 => 'augustus',
	 		9 => 'september',
	 		10 => 'oktober',
	 		11 => 'november',
	 		12 => 'december'	 	
	 	);

	}
	
	/** Creates human-readable date(or time)-format.
	 * 
	 * Based on the format given in settings.
	 * @access public
	 * @param string $date date to transform. Accepts same input as strtotime().
	 * @param string $type type to transform, 'date' or 'datetime'.
	 * @returns string formatted string.
	 */
	public function parse($date, $type = 'date'){
	
		// to timestamp
		$timestamp = strtotime($date);
		
		// return in right format
		return date($this->config->date->{(($type == 'date') ? 'date_format' : 'datetime_format')}, $timestamp);
	
	}
	
	/** Humanizes a timestamp.
	 * 
	 * Also adds 'vandaag' or 'gisteren'.
	 * 
	 * @access public
	 * @param int $timestamp timestamp to transform
	 * @param string $type type to transform, 'date' or 'datetime'.
	 * @returns string formatted string.
	 */
	public function humanize($timestamp, $type = 'datetime'){
	
		if($type == 'date'){
		
			return $this->prefix($timestamp);
			
		}else{
		
			$prefix = $this->prefix($timestamp);
			
			return $prefix . " om " . date("H:i:s", $timestamp);
		
		}
	
	}
	
	/** transforms a timestamp into a date (or today/yesterday).
	 * 
	 * @access private
	 * @param int $timestamp timestamp to transform.
	 * @return string formatted date.
	 */
	private function prefix($timestamp){
	
		// init
		$prefix = false;
	
		// check if is today
		if(date("dmY", $timestamp) == date("dmY")){
		
			// set today
			$prefix = "vandaag";
			
		}else
		if(date("dmY", $timestamp) == date("dmY", strtotime("yesterday"))){
		
			// set yesterday
			$prefix = "gisteren";
		
		}
		
		// check prefix
		if(!$prefix){
		
			$prefix = $this->dayOfWeek[date("N", $timestamp)] . " " . date("j", $timestamp) ." ". $this->month[date("N", $timestamp)] ." ". date("Y", $timestamp);
		
		}
		
		return $prefix;
	
	}
	
	
}