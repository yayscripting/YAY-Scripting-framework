<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Helper;

/** gAuth-helper
 * 
 * This helper can be used to perform verify two-factor authentications.
 *
 * @name gAuth
 * @package helpers
 * @subpackage gAuth
 */
class Gauth extends \System\Helper
{

	// checked out this site: http://www.brool.com/index.php/using-google-authenticator-for-your-website
	public function verify($pass, $key = null)
	{
		
		$temp = date_default_timezone_get();
		$time = (time() - time()%30) / 30;
		date_default_timezone_set($temp);
		
		$pass = preg_replace('/^0+?([1-9][0-9]+?)$/', '\1', $pass);
		
		$secretKey = !is_null($key) ? $key : $this->config->security->gAuthKey;
		$secretKey = strtoupper($secretKey);
		$secretKey = preg_replace('/[^A-Z2-7\=]+?/', '', $secretKey);
		$secretKey = $this->base32_decode($secretKey);
		
		// -1 => +1 for timelapse
		for($i = -1; $i <= 1; $i++) {
			
			$result = pack('N', 0) . pack('N', $time+$i);
			$result = hash_hmac('sha1', $result, $secretKey, true);
			
			$offset = ord(substr($result, -1)) & 0x0F;
			$sub = substr($result, $offset, 4);
			
			$unpack = unpack('N', $sub);
			$unpack = $unpack[1] & 0x7FFFFFFF;
			
			if ($unpack % pow(10,6)/*(10^6)*/ == $pass)
				return true;
				
			
		}
		
		return false;
		
	}
	
	public function generateKey()
	{
		
		$array = array_merge(range('A', 'Z'), range('2', '7'));
		$result = '';
		
		for ($i = 0; $i < 16; $i++)
			$result .= $array[rand(0, count($array) - 1)];
			
		return $result;
		
	}
	
	public function QRcode($key, $name = null)
	{
		
		$name = !is_null($name) ? $name : $this->config->security->gAuthName;
		
		return 'https://chart.googleapis.com/chart?chs=500x500&cht=qr&chl=otpauth://totp/'.urlencode($name).'?secret='.urlencode($key);
		
	}
	
	// Thanks Phil ~ idontplaydarts.com
	private function base32_decode($b32) {
		
		$lut = array(
			"A" => 0,       "B" => 1,
			"C" => 2,       "D" => 3,
			"E" => 4,       "F" => 5,
			"G" => 6,       "H" => 7,
			"I" => 8,       "J" => 9,
			"K" => 10,      "L" => 11,
			"M" => 12,      "N" => 13,
			"O" => 14,      "P" => 15,
			"Q" => 16,      "R" => 17,
			"S" => 18,      "T" => 19,
			"U" => 20,      "V" => 21,
			"W" => 22,      "X" => 23,
			"Y" => 24,      "Z" => 25,
			"2" => 26,      "3" => 27,
			"4" => 28,      "5" => 29,
			"6" => 30,      "7" => 31
		);
	 
		$b32    = strtoupper($b32);
		$l      = strlen($b32);
		$n      = 0;
		$j      = 0;
		$binary = "";
	 
		for ($i = 0; $i < $l; $i++) {
	 
			$n = $n << 5;
			$n = $n + $lut[$b32[$i]];      
			$j = $j + 5;
	 
			if ($j >= 8) {
				$j = $j - 8;
				$binary .= chr(($n & (0xFF << $j)) >> $j);
			}
		}
	 
		return $binary;
		
	}
		
}