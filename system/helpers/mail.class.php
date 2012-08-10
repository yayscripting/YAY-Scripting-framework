<?php
/**
 * @author YAY!Scripting
 * @package files
 */

/**
 * Include SWIFT
 */
require_once 'system/external/swift/swift_required.php';

/** Mail-helper
 * 
 * This helper can be used for sending e-mails.
 *
 * @name mail
 * @package helpers
 * @subpackage mail
 */
class YSH_Mail extends YS_Helper
{

	/** Contains the mail-message
	 * 
	 * @access public
	 * @var object $message
	 */
	public $message		= null;
	
	/** Contains the smtp-transport
	 * 
	 * @access private
	 * @var object $transport
	 */
	private $transport	= null;
	
	/** contains the swift mailer.
	 * 
	 * @access private
	 * @var object $mailer
	 */
	private $mailer		= null;
	
	private $connected	= false;
	
	/** Connects with the SMTP-server 
	 * 
	 * null-values are picked out of the config-file
	 * 
	 * @access public
	 * @param string $server Mailhost
	 * @param int $port Port
	 * @param string $username Username
	 * @param string $password Password
	 * @param string $security Used Security type, e.g. SSL
	 * @return void
	 * @throws HelperException with errorType 1
	 * 	If the connection could not be made.
	 */
	public function smtp_connect($server = null, $port = null, $username = null, $password = null, $security = '')
	{
	
		// check connected
		if($this->connected == false)
			$this->connected = true;
		else
			return;
			
		
		// default values
		if (is_null($server))
			$server	  = $this->config->mail->host;
		
		if (is_null($port))
			$port 	  = $this->config->mail->port;
		
		if (is_null($username))
			$username = $this->config->mail->username;
		
		if (is_null($password))
			$password = $this->config->mail->password;
		
		// check
		if(is_null($server)){
		
			// connect
			try {
					
				// transport
				$this->transport = Swift_SendmailTransport::newInstance('sendmail -bs');
				
				// load mailer
				$this->mailer = Swift_Mailer::newInstance($this->transport);
				
			} catch (Swift_Connection_Exception $ex) {
				
				throw new HelperException(1, 'Could not connect to the Sendmail-transport');
				
			}
		
		}else{
		
			// connect
			try {
					
				// transport
				$this->transport = Swift_SmtpTransport::newInstance($server, $port, $security);
								
				// login
				if(!empty($username))
					$this->transport->setUsername($username);
					
				if(!empty($password))	
					$this->transport->setPassword($password);
				
				// load mailer
				$this->mailer = Swift_Mailer::newInstance($this->transport);
				
			} catch (Swift_Connection_Exception $ex) {
				
				throw new HelperException(1, 'Could not connect to the SMTP-server: '.$ex);
				
			}
			
		}
		
	}
	
	/** Creates the email-object.
	 * 
	 * @access public
	 * @throws HelperException with errorType 1
	 * 	If the connection has not been made.
	 * @return void
	 */
	public function prepare_message()
	{
		
		// check transport
		if(is_null($this->transport) || is_null($this->mailer))
			throw new HelperException(1, 'No SMTP-connection.');
	
		// create message
		$this->message = Swift_Message::newInstance();
		
	}
	
	/** Prepares a e-mail to get send.
	 * 
	 * @access public
	 * @param string $content Message body.
	 * @param string $subject Subject.
	 * @param array $to Receivers, (mail => name[, mail => name;...]).
	 * @param array $from Sender, (mail => name).
	 * @return void
	 * @throws HelperException with errorType 2
	 * 	If no sender has been given
	 */
	public function prepare_details($content, $subject, array $to, array $from)
	{
	
		// check transport
		if(is_null($this->message))
			$this->prepare_message();
		
		// set subject
		$this->message->setSubject($subject);
		
		// check from
		if(!is_array($from))
			throw new HelperException(2, 'No sender has been selected.');
		
		// set sender
		$this->message->setFrom($from);
		
		// set to
		$this->message->setTo($to);
			
		// get content-type
		$type = $this->message->getHeaders()->get('Content-Type');
		
		// set html + utf8
		$type->setValue('text/html');
		$type->setParameter('charset', 'utf-8');
		
		// set message
		$this->message->addPart($content, 'text/html');
		  
	}
	
	/** Sends a message through SMTP
	 * 
	 * @access public
	 * @return void
	 * @throws HelperException with errorType 1
	 * 	If smtp_connect or prepare_message has not been called.
	 * @throws HelperException with errorType 2
	 * 	If the connection could not be made.
	 */
	public function smtp_send()
	{
	
		// check if mailer and message are ready
		if (is_object($this->mailer) && is_object($this->message)) {
			
			try {
				
				// send
				return $this->mailer->batchSend($this->message);
			
			} catch (Swift_TransportException $ex) {
				
				throw new HelperException(2, 'Could not connect to the SMTP-server (1001): <hr />'.nl2br($ex));
				
			}
			
		}
		
		throw new HelperException(1, 'smtp_connect or prepare_message has not been called.');		
	
	}
	

	/** Sends a mail from a template.
	 * 
	 * Smarty is allowed to be used in all templates.
	 * $variables is assigned in smarty. So be warned, those variables will appear in your layout also.
	 * 
	 * @access public
	 * @param string $template Template name, located in views/mails/content/%NAME%.tpl.
	 * @param string $subject Subject.
	 * @param mixed $to Receivers, single string or array(email => name[, email => name)-format.
	 * @param mixed $from Sender, single string or array(email => name)-format
	 * @param string $header Header name, located in views/mails/headers/%NAME%.tpl.
	 * @param string $footer Footer name, located in views/mails/footers/%NAME%.tpl.
	 * @param array $attachment Attachments, format: array(array(src=>,name=>), array())
	 * @return void
	 */
	public function send($template, $subject, $to, $from, $variables = array(), $header = 'default', $footer = 'default', $attachments = array(), $langDir = null)
	{	
		// globals
		$layout = YS_Layout::Load();
		
		// assign variables
		foreach ($variables as $key => $replacement) 
			$layout->assign($key, $replacement);
			
		if (is_null ($langDir)) $langDir = YS_Language::getDir();
		if (is_null($langDir) == false && substr($langDir, -1, 1) != '/') $langDir .= '/';
		
		// assign headers
		$layout->assign('_headers', array(
			'template' => $template,
			'subject' => $subject,
			'to' => $to,
			'from' => $from,
			'header' => $header,
			'footer' => $footer,
			'langDir' => $langDir,
			'lang' => trim(str_replace('/', '', trim($langDir)))
		));
		
		// get content
		if(!empty($header)){ $tpl_header = $layout->fetch('application/views/'.$langDir.'mails/headers/' . $header . '.tpl'); }
		if(!empty($footer)){ $tpl_footer = $layout->fetch('application/views/'.$langDir.'mails/footers/' . $footer . '.tpl'); }
		$content = $layout->fetch('application/views/'.$langDir.'mails/content/'.$template.'.tpl');
		
		// glue
		$content = $tpl_header."<!-- start content -->".$content."<!-- end content -->".$tpl_footer;
			
		// fetch to
		if(!is_array($to))
			$to = array($to => $to);
		
	 	// fetch from
		if(!is_array($from))
			$from = array($from => $from);
		
		// create mail-object
		$this->prepare_message();
		
		// attachments
		if (!empty($attachments)) {
			
			foreach ($attachments as $attachment) {
		
				$this->message->attach(
				
					Swift_Attachment::fromPath($attachment['src'])
						->setFileName($attachment['name'])
					
				);
				
			}
			
		}
					
		// prepare mail
		$this->prepare_details($content, $subject, $to, $from);
		
		// send
		return $this->smtp_send();
		
	}
}