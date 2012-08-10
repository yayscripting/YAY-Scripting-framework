<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** HTTP-helper
 * 
 * This helper can be used to perform cURL-requests, force downloads or to redirect.
 *
 * @name HTTP
 * @package helpers
 * @subpackage HTTP
 */
class YSH_Http extends YS_Helper
{
		
	/** Perform a cURL-POST-request
	 * 
	 * @access public
	 * @param string $url Url to visit.
	 * @param array $post POST-data
	 * @param array $options Additional cURL-options
	 * @return string Response
	 */
	public function curl_post($url, array $post = NULL, array $options = array()) 
	{
	    $defaults = array(
	        CURLOPT_POST => 1,
	        CURLOPT_HEADER => 0,
	        CURLOPT_URL => $url,
	        CURLOPT_FRESH_CONNECT => 1,
	        CURLOPT_RETURNTRANSFER => 1,
	        CURLOPT_FORBID_REUSE => 1,
	        CURLOPT_TIMEOUT => 4,
	        CURLOPT_POSTFIELDS => http_build_query($post)
	    );
	
	    $ch = curl_init();
	    curl_setopt_array($ch, ($options + $defaults));
	    if( ! $result = @curl_exec($ch))
	    	Throw new HelperException(1, curl_error($ch));
	    
	    curl_close($ch);
	    return $result;
	    
	}
	
	/** Perform a cURL-GET-request
	 * 
	 * @access public
	 * @param string $url Url to visit.
	 * @param array $get GET-data
	 * @param array $options Additional cURL-options
	 * @return string Response
	 */
	public function curl_get($url, array $get = NULL, array $options = array()) 
	{   
	    $defaults = array(
	        CURLOPT_URL => $url. (strpos($url, '?') === FALSE ? '?' : ''). http_build_query($get),
	        CURLOPT_HEADER => 0,
	        CURLOPT_RETURNTRANSFER => TRUE,
	        CURLOPT_TIMEOUT => 4
	    );
	   
	    $ch = curl_init();
	    curl_setopt_array($ch, ($options + $defaults));
	    if( ! $result = @curl_exec($ch))
	    	Throw new HelperException(1, curl_error($ch));
	        
	    curl_close($ch);
	    return $result;
	}
	
	/** Reloads the current page
	 * 
	 * @access public
	 * @return void
	 */
	public function reload()
	{
		
		$this->redirect($_SERVER['REQUEST_URI']);
		
	}
	
	/** Redirect to another page
	 * 
	 * This page will be saved in $_SESSION['HTTP_REFERER'] for future redirects. This page stops the script executing
	 * 
	 * @access public
	 * @param string $url Page to redirect to. Default is the saved $_SESSION['HTTP_REFERER']. If that does not exists, you'll be redirected to '/'
	 * @param int $statuscode any redirect code between 300 and 307.
	 * @return void
	 */
	public function redirect($url = null, $statuscode = 302)
	{
		
		// needs referer?
		if ($url === null)	
			$url = empty($_SESSION['HTTP_REFERER']) ? "/" : $_SESSION['HTTP_REFERER'];
		
		// save referrer
		$_SESSION['HTTP_REFERER'] = $_SERVER['REQUEST_URI'];
		
		// statuscodes
		$statuscodes = array();
		
		$statuscodes[300] = "300 Multiple Choices";		//
		$statuscodes[301] = "301 Moved Permanently";		//
		$statuscodes[302] = "302 Found";			// default
		$statuscodes[303] = "303 See Other";			//
		$statuscodes[304] = "304 Not Modified";			//
		$statuscodes[305] = "305 Use Proxy";			//
		$statuscodes[307] = "307 Temporary Redirect";		//
		
		// set status
		header("HTTP/1.1 ".$statuscodes[$statuscode]);
		
		// redirect
		header("Location: ".$url);
		
		// stop script running
		exit();
	
	}
	
	/** Force to download a file
	 * 
	 * If $mimeType is null, {@link YSH_File::getMimeType} will be used to determine the MIME-type.
	 * 
	 * @access public
	 * @param string $file Path to the file
	 * @param string $mimeType MIME-type of the file
	 * @param string $fileName Filename to show
	 * @return void
	 */
	public function forceDownload($file, $mimeType = null, $fileName = null)
	{
		
		if (file_exists($file)) {
			
			$info = pathinfo($file);
			
			header('Content-Description: File Transfer');
			header('Content-Type: '.(($mimeType === null) ? $this->helpers->file->getMimeType($file) : $mimeType));
	 		header('Content-Disposition: attachment; filename="'.(($fileName === null) ? $info['basename'] : preg_replace('["\/\\\\]', '', $fileName)).'"');
			header('Content-Transfer-Encoding: binary');
			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Pragma: private');
			header('Content-Length: ' . filesize($file));
			
			ob_clean();
			ob_start();
			echo file_get_contents($file);
			
			exit(254);
			
		}
		
		throw new HelperException(1, "File doesn't exist!");
		
	}
	
	/** Closes the connection
	 * 
	 * Lets the client stop loading, while PHP continues executing
	 * 
	 * @access public
	 * @return void
	 */
	public function closeConnection()
	{
		
		// send close-header
		header("Content-Length: " . ob_get_length());
    		header("Content-Encoding: none");
		header('Connection: close');
		 
		// flush all output
		while (@ob_end_flush());
		flush();
		 
		// close current session
		if (session_id()) session_write_close();
		
	}
	
}