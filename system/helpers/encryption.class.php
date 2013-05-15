<?php
/**
 * @author YAY!Scripting
 * @package files
 */
namespace System\Helper;

/** Encryption-helper
 * 
 * This helper can be used for hashing and encoding with functions which aren't supported in php.
 *
 * @name encryption
 * @package helpers
 * @subpackage encryption
 */
class Encryption extends \System\Helper
{
	
	/** Encryption function.
	 * 
	 * Using the Mcrypt-module of PHP to encrypt the data.
	 *
	 * @access	public
	 * @param	string	$password	  Password.
	 * @param	string	$salt		  Salt.
	 * @param	string	$key	  	  Key using to decrypt.
	 * @param	int	$algorithm	  Algorithm constant.
	 * @param	int	$mode	  	  Algorithm-type.
	 * @param	string	$genKeyAlgorithm  Algorithm to use for the hashing.
	 * @param	string  $iv		  Base64-encoded IV-string, do not use the same IV to often
	 * @param	int	$multiplyConstant Value which represents the strength of the second KEY, between 1 and algorithm keysize. How lower this value is, the stronger the password is.
	 * @return	string encrypted data.
	 * @see	decrypt
	 * @see create_iv
	 */
	public function encrypt($password, $salt, $key, $algorithm = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC, $genKeyAlgorithm = null, $iv = null, $multiplyConstant = 16)
	{
		
		// generate Module
		$module = mcrypt_module_open($algorithm, '', $mode, '');
		srand();
		
		// determine genkeyAlgorithm
		if (is_null($genKeyAlgorithm))
			$genKeyAlgorithm = $this->config->security->encryption_hash_algorithm;
		
		// generate key/IV
		$key = $this->pbkdf2($key, $salt, mcrypt_enc_get_key_size($module),  2000 + ( mcrypt_enc_get_iv_size($module) * ( 1 + ceil(abs((mcrypt_enc_get_key_size($module) / $multiplyConstant))))), $genKeyAlgorithm);
		$iv  = ($iv == null) ? mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND) : base64_decode($iv);
		
		// prepare Encoding
		mcrypt_generic_init($module, $key, $iv);
		
		// encode
		$data = $this->fill32($password);
		$data = mcrypt_generic($module, $data);
		
		// remember IV
		$data = substr(base64_encode($iv), 0, -1) . base64_encode($data);
		
		// close off
		mcrypt_generic_deinit($module);
		mcrypt_module_close($module);
		
		// return
		return $data; 
		
	}
	
	/** Decryption function.
	 * 
	 * Using the Mcrypt-module of PHP to decrypt the data.
	 *
	 * @param	string	$string		  Encrypted data.
	 * @param	string	$salt		  Salt.
	 * @param	string	$key	  	  Key using to decrypt.
	 * @param	int	$algorithm	  Algorithm constant.
	 * @param	int	$mode	  	  Algorithm-type.
	 * @param	string	$genKeyAlgorithm  Algorithm to use for the hashing.
	 * @param	int	$multiplyConstant Value which represents the strength of the second KEY, between 1 and algorithm keysize. How lower this value is, the stronger the password is.
	 * @return string decrypted data.
	 * @see encrypt
	 */
	public function decrypt($string, $salt, $key, $algorithm = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC, $genKeyAlgorithm = null, $multiplyConstant = 16)
	{
		
		// empty-check
		if (trim($string) == "" || $string == null)
			return "";
		
		// generate Module
		$module = mcrypt_module_open($algorithm, '', $mode, '');
		
		// determine genkeyAlgorithm
		if (is_null($genKeyAlgorithm))
			$genKeyAlgorithm = $this->config->security->encryption_hash_algorithm;
		
		// split IV from encrypted data
		$iv	= "";
		for ($i = 0; $i < mcrypt_enc_get_iv_size($module); $i++)
			$iv .= " ";
		$length = strlen(base64_encode($iv)) - 1;
		$iv	= base64_decode(substr($string, 0, $length) . '=');
		$data	= base64_decode(substr($string, $length));
		
		if (strlen($iv) != mcrypt_enc_get_iv_size($module))
			return "";
		
		// generate key
		srand();
		$key = $this->pbkdf2($key, $salt, mcrypt_enc_get_key_size($module),  2000 + ( mcrypt_enc_get_iv_size($module) * ( 1 + ceil(abs((mcrypt_enc_get_key_size($module) / $multiplyConstant))))), $genKeyAlgorithm);
		
		// prepare encoding
		mcrypt_generic_init($module, $key, $iv);
		
		// decode
		$password = mdecrypt_generic($module, $data);
		$password = $this->strip32($password);
		
		// close down
		mcrypt_generic_deinit($module);
		mcrypt_module_close($module);
		
		return $password;
		
	}
	
	/** Generates an IV.
	 * 
	 * Using the Mcrypt-module of PHP to generate an base64-encoded IV
	 *	
	 * @access public
	 * @param int Algorithm	$algorithm Algorithm-type: constant MCRYPT_XXXXXXX_XXX.
	 * @param int Mode	$mode Encryption-mode: contant MCRYPT_MODE_XXX-value.
	 * @return IV(base-64 encoded)
	 */
	public function create_iv($algorithm = MCRYPT_RIJNDAEL_256, $mode = MCRYPT_MODE_CBC)
	{
		
		// generate Module
		$module = mcrypt_module_open($algorithm, '', $mode, '');
		srand();
		
		// generate IV
		$iv  = mcrypt_create_iv(mcrypt_enc_get_iv_size($module), MCRYPT_RAND);
		
		// close off
		mcrypt_module_close($module);
		return base64_encode($iv);
		
	}
	
	/** fills the length string up to a plural of 32.
	 * 
	 * @access private
	 * @param string $input string to fill up.
	 * @return string filled string.
	 * @see strip32
	 */
	private function fill32($input)
	{
		
		// get length which has to be added
		$length = 32 - strlen($input) % 32;
		
		// create the right length
		if ($length < 2)
			$length += 32;
			
		// add length as prefix
		$output = (($length < 10) ? '0' . $length : $length). $input;
		$length -= 2;
		
		// add whitespace to end
		for ($i = 0; $i <  $length; $i++)
			$output .= 0x00;
		
		return $output;
		
	}
	
	/** Does the opposite of {@link fill32}.
	 * 
	 * @access private
	 * @param string $input string to strip.
	 * @return string stripped value.
	 * @see fill32
	 */
	private function strip32($input)
	{
		
		// get first 2 bytes to determine length
		$length = intval(substr($input, 0, 2)) - 2;
		$output = substr($input, 2);
		
		// get output
		$output = substr($output, 0, strlen($output) - $length);
		
		// return output
		return $output;
		
	}
	
	/** Hash (PBKDF2).
	 *
	 * Uses "Password Based Key Derivation Function v2".
	 * Described in RFC 2898(http://www.ietf.org/rfc/rfc2829.txt) in paragraph 5.2.
	 * 
	 * @access public
	 * @param string	string	$password	password to encrypt.
	 * @param string	string	$salt		salt to use with the encryption.
	 * @param int		int	$length		desired length of the return value.
	 * @param int		int	$loop		number of times to loop (the stronger, the better and slower).
	 * @param string	string	$algorithm	algorithm-type(e.g. 'sha256').
	 * @throws HelperException with errorType 2.
	 * 	If $loop is not higher than zero.
	 * @throws HelperException with errorType 1
	 * 	If the $length is to high.
	 * @return binary encrypted password (save in BLOB or base64-encode this).
	 */
	public function pbkdf2($password, $salt, $length, $loop = 2000, $algorithm = null)
	{
		
		// default hash-type
		if (is_null($algorithm))
			$algorithm = $this->config->security->hash_algorithm;
		
		// get hashlength
		$Hlength = strlen(hash($algorithm, null, true));
		$blockC  = ceil($length / $Hlength);
		$key	 = "";
		
		// verify input
		if ($length / $Hlength > pow(2, 32) - 1)
			throw new \System\Exception\Helper(1, 'Given key length is to big, max is: (2^32 - 1) * '.$Hlength.' ( = '.((pow(2, 32) - 1) * $Hlength).').');
		
		
		if ($loop <= 0)
			throw new \System\Exception\Helper(2, 'Loop-count must be higher than zero.');
		
		// loop through all blocks
		for ($block = 1; $block <= $blockC; $block++) {
			
			// prefix for blockdata
			$hmac = hash_hmac($algorithm, $salt . pack("N", $block), $password, true);
			$data = $hmac;
			
			// all iterations
			for($i = 1; $i < $loop; $i++) {
				
				$data ^= $hmac = hash_hmac($algorithm, $hmac, $password, true);
				
			}
			
			// add to string
			$key .= $data;
			
		}
		
		// return the substring
		return substr($key, 0, $length);
		
	}
	
	/** return all possible hash algoritms on this system.
	 * 
	 * This function exists for development purposes only.
	 * 
	 * @access public
	 * @return array all hash algoritms.
	 */
	public function getHashAlgoritmes()
	{
		
		return hash_algos();	
		
	}
	
	/** generates a random key or salt of a given length.
	 * 
	 * This function exists for development purposes only.
	 * 
	 * @access public
	 * @param int $length length of the return value.
	 * @return string A generated key or salt.
	 */
	public function getKey($length)
	{
		
		// prepare
		$possibilities = str_split("abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789~!@#$%^&*()_-+=/?.,<>;:\"[]{}|");
		$string = "";
		
		// fill
		for($i = 0; $i < $length; $i++)
			$string .= $possibilities[array_rand($possibilities)];
			
		// return
		return $string;		
		
	}
	
}