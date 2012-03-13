<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** Intranet-helper
 *
 * These functions do only work in a local intranet, over the internet this helper is useless.
 * 
 * @name intranet
 * @package helpers
 * @subpackage intranet
 */
class YSH_Intranet extends YS_Helper
{
	
	/** Gets the MAC-address of the device, connection to a local IP-address.
	 * 
	 * @access public
	 * @param string $ip Local IP-Address.
	 * @returns string MAC-address (12:34:45:56:67:ab), or false on failure.
	 */
	public function getMAC($ip)
	{
		// gather information
		$os = php_uname('s');
		$mac = '';
		
		// check right os/get mac
		switch ($os) {
			case 'Linux': // not sure, im guessing this uses the same command as MAC
			case 'Darwin':
				$list = `arp $ip`;
				$mac = preg_replace("/^.+?((([0-9a-f]{1,2}):){5}[0-9a-f]{1,2}).+?$/s", '\1', $list);		
				break;
			
			case 'Windows NT':
				$list = `arp -a $ip`;
				$mac  = preg_replace("/^.+?(([0-9a-f]{2}\-){5}[0-9a-f]{2}).+?$/s", '\1', $list, 1);
				$mac = str_replace("-", ":", $mac);
					
				break;
				
		}

		// trim
		$mac = trim($mac);
		
		// verify
		if (!preg_match("/^((([0-9a-f]{1,2}):){5}[0-9a-f]{1,2})$/", $mac))
			return false;
			
		return $mac;
		
	}
	
}