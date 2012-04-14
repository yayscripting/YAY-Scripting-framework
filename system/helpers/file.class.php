<?php
/**
 * @author YAY!Scripting
 * @package files
 */


/** File-helper
 * 
 * This helper can be used to check the file's mime-type, or determine if a file is an image.
 *
 * @name file
 * @package helpers
 * @subpackage file
 */
class YSH_File extends YS_Helper
{

	/** Determine the MIME-type of an file
	 * 
	 * Gets the MIME-type of a file, with a lot of fallbacks to other functions.
	 * This is an modified version of the function getFileMimeType made by deceze on Stackoverflow
	 * {@link http://stackoverflow.com/questions/1232769/how-to-get-the-content-type-of-a-file-in-php?answertab=votes#tab-top}
	 * Please note that this function can use the file-extension to determine the MIME-type, if not enough functions are available.
	 * 
	 * @access public
	 * @param string $file Path to the file.
	 * @param string $extension True fileextension, if null $file's extension is taken
	 * @return string MIME-type
	 */
	public function getMimeType($file, $extension = null) 
	{
	
		if (function_exists('finfo_file')) {
		
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$type = finfo_file($finfo, $file);
			finfo_close($finfo);
	
		} else {
			
			if (file_Exists('upgradephp/ext/mime.php')) {
			
				require_once 'upgradephp/ext/mime.php';
				
			}
			
			if (function_exists('mime_content_type'))
				$type = mime_content_type($file);
	
		}
	
		if (!$type || $type == 'application/octet-stream') {
	 
			@$secondOpinion = exec('file -b --mime-type ' . escapeshellarg($file), $foo, $returnCode);
	
			if ($returnCode == '0' && $secondOpinion) {
	
				$type = $secondOpinion;
	
			}
	  
		}
	
		if (!$type || $type == 'application/octet-stream') {
	        	
	        	if (function_exists('exif_imagetype'))
				$exifImageType = exif_imagetype($file);
	        
			if ($exifImageType !== false) {
	        	
				$type = image_type_to_mime_type($exifImageType);
	            
	 		}
	        
		}
		
		if (!$type || $type == 'application/octet-stream') {
			
			if (function_exists('getimagesize')) {
				
				$size = getimagesize($file);
				
				if ($size) {
					
					if ($size['mime'] != 'application/octet-stream' && $size['mime'] != '') {
					
						$type = $size['mime'];
					
					} else if (function_exists('image_type_to_mime_type')) {
					
						$type = image_type_to_mime_type($size[2]);
					
					}
					
				}
			
			}
			
		}
		
		if (!$type)
			$type = 'application/octet-stream';
			
		// extension fallback
		if ($type == 'application/octet-stream' && (strrpos($file, '.') >= 0 || $extension !== null)) {
			
			$ext = ($extension === null) ? strtolower(substr($file, strrpos($file, '.') + 1)) : strtolower($extension);
			
			switch ($ext) {
				
				/* DOCUMENT */
				case 'doc':	return 'application/msword';
				case 'docx':	return 'application/vnd.openxmlformats-officedocument.wordprocessingml.document';
				case 'pdf':	return 'application/pdf';
				
				/* IMAGE */
				case 'png':	return 'image/png';
				case 'bmp':	return 'image/bmp';
				case 'jpeg':	
				case 'jpg':	return 'image/jpg';
				case 'gif':	return 'image/gif';
				
				/* AUDIO */
				case 'mp3':	return 'audio/mpeg';
				case 'wav':	return 'audio/wav';
				case 'mid':
				case 'rmi':	return 'audio/mid';
				case 'au':
				case 'snd':	return 'audio/basic';
				
				/* COMPRESSED FILES */
				case 'zip':	return 'application/zip';
				
			}
			
		}
		
		// mime replacements
		switch (strtolower($ext)) {
			
			case 'application/x-zip':	
			case 'application/x-compressed':
			case 'application/compressed':	return 'application/zip';
			
			case 'image/jpeg': return 'image/jpg';
			
		}
	
		return strtolower($type);
	    
	}
	
	/** Gets the EXIF-information of an image.
	 * 
	 * Cache the output of this function, this is a time consuming proces. Please note that the imagic-implementation is not funtional yet.
	 * 
	 * @access public
	 * @param string $src Path to image
	 * @return mixed Array on success, false if the file is not an JPEG-image or no EXIF-data is present.
	 * @throws HelperException with errorType 1: When neither the exif-module nor the ImageMagick-module is installed.
	 */
	public function getEXIF($src)
	{
		
		$mime = $this->getMimeType($src);
		if ($mime != 'image/jpg' && $mime != 'image/jpeg')
			return false;
		
		// exif- function
		if (function_exists('exif_read_data')) {
			
			return exif_read_data($src, 'IFD0,EXIF', false);
			
		}
		
		/* Not implented yet 
		// imagick
		if (class_exists('imagick', false)) {
			
			$imagick = new imagick($src);
			var_dump($imagick);
			return $imagick->getImageProperties( "exif:*" );
			
		}
		*/
		
		// nothing is supported
		throw new HelperException(1, 'Neither the exif-module nor the ImageMagick-module is installed');
		
	}
	
	/** Gets the Dimensions of an image.
	 * 
	 * @access public
	 * @param string $src Path to image
	 * @return object object(['width']=>width,['height']=>height) on success, false on failure.
	 * @throws HelperException with errorType 1: when file is not an image.
	 */
	public function getDimensions($src)
	{
		
		if (!$this->isImage($src))
			throw new HelperException(1, 'Given file('.$src.') is not an image.');
		
		$sizes = getimagesize($src);
		
		return (object)array('width' => $sizes[0], 'height' => $sizes[1]);
		
	}
	 
	
	/** Check if a file is an image.
	 * 
	 * This function uses {@link getMimeType} to determine the MIME-type, and with that MIME-type if the file is an image.
	 * 
	 * @access public
	 * @param string $file Path to the file.
	 * @return bool
	 * @see getMimeType
	 */
	public function isImage($file)
	{
		
		// base on MIME-type
		$mime = $this->getMimeType($file);
		
		switch ($mime) {
			
			case 'image/png':
			case 'image/jpg':
			case 'image/jpeg':
			case 'image/bmp';
			case 'image/gif';
				return true;
			
		}
		
		return false;
		
	}
	
	/** Gets the correct Mtime, even on windows servers.
	 * 
	 * http://www.php.net/manual/en/function.filemtime.php#100692, by Dustin Oprea.
	 * 
	 * @access public
	 * @param string $filePath Path to the file
	 * @return int timestamp
	 */
	public function mtime($filePath) 
	{ 

		$time = filemtime($filePath); 

		$isDST = (date('I', $time) == 1); 
		$systemDST = (date('I') == 1); 

		$adjustment = 0; 

		if($isDST == false && $systemDST == true) 
			$adjustment = 3600; 
    
		else if($isDST == true && $systemDST == false) 
			$adjustment = -3600; 

		else 
			$adjustment = 0; 

		return ($time + $adjustment); 
		
	} 

}