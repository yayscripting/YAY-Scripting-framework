<?php

class MultiSafepay {
  var $plugin_name = 'MSPClass';
  var $version = '2.3';

  // test or live api
  var $test = false;

  // merchant data
  var $merchant = array(
    'account_id'       => '', // required
    'site_id'          => '', // required
    'site_code'        => '', // required
    'notification_url' => '',
    'cancel_url'       => '',
    'redirect_url'     => '',
    'close_window'     => '',
  );

  // customer data
  var $customer = array(
    'locale'           => '', // advised
    'ipaddress'        => '',
    'forwardedip'      => '',
    'firstname'        => '',
    'lastname'         => '',
    'address1'         => '',
    'address2'         => '',
    'housenumber'      => '',
    'zipcode'          => '',
    'city'             => '',
    'state'            => '',
    'country'          => '',
    'phone'            => '',
    'email'            => '', // advised
  );
  
  // transaction data
  var $transaction = array(
    'id'               => '', // required
    'currency'         => '', // required
    'amount'           => '', // required
    'description'      => '', // required
    'var1'             => '',
    'var2'             => '',
    'var3'             => '',
    'items'            => '',
    'manual'           => 'false',
    'gateway'          => '',
    'daysactive'       => '',
  );


  // signature
  var $signature;

  // return vars
  var $api_url;
  var $request_xml;
  var $reply_xml;
  var $payment_url;
  var $status;
  var $error_code;
  var $error;
  
  var $parsed_xml;
  var $parsed_root;

  
  /*
   * Check the settings before using them
   */
  function checkSettings(){
    // trim any spaces
    $this->merchant['account_id']  = trim($this->merchant['account_id']);
    $this->merchant['site_id']     = trim($this->merchant['site_id']);
    $this->merchant['site_code']   = trim($this->merchant['site_code']);
  }


  /*
   * Starts a transaction and returns the payment url
   */
  function startTransaction(){
    $this->checkSettings();

    $this->setIp();
    $this->createSignature();
    
    // create request
    $this->request_xml = $this->createTransactionRequest();

    // post request and get reply
    $this->api_url   = $this->getApiUrl();
    $this->reply_xml = $this->xmlPost($this->api_url, $this->request_xml);
    
    // communication error
    if (!$this->reply_xml)
      return false;
    
    // parse xml
    $rootNode = $this->parseXmlResponse($this->reply_xml);
    if (!$rootNode)
      return false;
    
    // return payment url
    $this->payment_url = $this->xmlUnescape($rootNode['transaction']['payment_url']['VALUE']);
    return $this->payment_url;
  }
  

  /*
   * Return the status for the specified transactionid
   */
  function getStatus(){
    $this->checkSettings();
    
    // generate request
    $this->request_xml = $this->createStatusRequest();

    // post request and get reply
    $this->api_url   = $this->getApiUrl();
    $this->reply_xml = $this->xmlPost($this->api_url, $this->request_xml);

    // communication error
    if (!$this->reply_xml)
      return false;
    
    // parse xml
    $rootNode = $this->parseXmlResponse($this->reply_xml);
    if (!$rootNode)
      return false;

    // return status
    $this->status = $rootNode['ewallet']['status']['VALUE'];
    return $this->status;
  }
  

  /*
   * Returns an associative array with the ids and the descriptions of the available gateways
   */
  function getGateways(){
    $this->checkSettings();
    
    // generate request
    $this->request_xml = $this->createGatewaysRequest();

    // post request and get reply
    $this->api_url   = $this->getApiUrl();
    $this->reply_xml = $this->xmlPost($this->api_url, $this->request_xml);

    // communication error
    if (!$this->reply_xml)
      return false;
    
    // parse xml
    $rootNode = $this->parseXmlResponse($this->reply_xml);
    if (!$rootNode)
      return false;

    // get gatesways
    $gateways = array();
    foreach($rootNode['gateways']['gateway'] as $xml_gateway){
      $gateway = array();
      $gateway['id'] = $xml_gateway['id']['VALUE'];
      $gateway['description'] = $xml_gateway['description']['VALUE'];
      
      // issuers
      if (isset($xml_gateway['issuers'])){
        $issuers = array();
        
        foreach($xml_gateway['issuers']['issuer'] as $xml_issuer){
          $issuer = array();
          $issuer['id'] = $xml_issuer['id']['VALUE'];
          $issuer['description'] = $xml_issuer['description']['VALUE'];
          $issuers[$issuer['id']] = $issuer;
        }

        $gateway['issuers'] = $issuers;
      }
      
      $gateways[$gateway['id']] = $gateway;
    }
    
    // return
    return $gateways;
  }
  
  
  /*
   * Create the transaction request xml
   */
  function createTransactionRequest(){
    // issuer attribute
    $issuer = "";
    if (!empty($this->issuer)){
      $issuer =' issuer="'.$this->xmlEscape($this->issuer).'"';
    }
  
    $request = '<?xml version="1.0" encoding="UTF-8"?>
    <redirecttransaction ua="' . $this->plugin_name . ' ' . $this->version . '">
      <merchant>
        <account>' .          $this->xmlEscape($this->merchant['account_id']) . '</account>
        <site_id>' .          $this->xmlEscape($this->merchant['site_id']) . '</site_id>
        <site_secure_code>' . $this->xmlEscape($this->merchant['site_code']) . '</site_secure_code>
        <notification_url>' . $this->xmlEscape($this->merchant['notification_url']) . '</notification_url>
        <cancel_url>' .       $this->xmlEscape($this->merchant['cancel_url']) . '</cancel_url>
        <redirect_url>' .     $this->xmlEscape($this->merchant['redirect_url']) . '</redirect_url>
        <close_window>' .     $this->xmlEscape($this->merchant['close_window']) . '</close_window>
      </merchant>
      <customer>
        <locale>' .           $this->xmlEscape($this->customer['locale']) . '</locale>
        <ipaddress>' .        $this->xmlEscape($this->customer['ipaddress']) . '</ipaddress>
        <forwardedip>' .      $this->xmlEscape($this->customer['forwardedip']) . '</forwardedip>
        <firstname>' .        $this->xmlEscape($this->customer['firstname']) . '</firstname>
        <lastname>' .         $this->xmlEscape($this->customer['lastname']) . '</lastname>
        <address1>' .         $this->xmlEscape($this->customer['address1']) . '</address1>
        <address2>' .         $this->xmlEscape($this->customer['address2']) . '</address2>
        <housenumber>' .      $this->xmlEscape($this->customer['housenumber']) . '</housenumber>
        <zipcode>' .          $this->xmlEscape($this->customer['zipcode']) . '</zipcode>
        <city>' .             $this->xmlEscape($this->customer['city']) . '</city>
        <state>' .            $this->xmlEscape($this->customer['state']) . '</state>
        <country>' .          $this->xmlEscape($this->customer['country']) . '</country>
        <phone>' .            $this->xmlEscape($this->customer['phone']) . '</phone>
        <email>' .            $this->xmlEscape($this->customer['email']) . '</email>
      </customer>
      <transaction>
        <id>' .               $this->xmlEscape($this->transaction['id']) . '</id>
        <currency>' .         $this->xmlEscape($this->transaction['currency']) . '</currency>
        <amount>' .           $this->xmlEscape($this->transaction['amount']) . '</amount>
        <description>' .      $this->xmlEscape($this->transaction['description']) . '</description>
        <var1>' .             $this->xmlEscape($this->transaction['var1']) . '</var1>
        <var2>' .             $this->xmlEscape($this->transaction['var2']) . '</var2>
        <var3>' .             $this->xmlEscape($this->transaction['var3']) . '</var3>
        <items>' .            $this->xmlEscape($this->transaction['items']) . '</items>
        <manual>' .           $this->xmlEscape($this->transaction['manual']) . '</manual>
        <gateway'.$issuer.'>'.$this->xmlEscape($this->transaction['gateway']) . '</gateway>
      </transaction>
      <signature>' .          $this->xmlEscape($this->signature) . '</signature>
    </redirecttransaction>';
    
    return $request;
  }


  /*
   * Create the status request xml
   */
  function createStatusRequest(){
    $request = '<?xml version="1.0" encoding="UTF-8"?>
    <status ua="' . $this->plugin_name . ' ' . $this->version . '">
      <merchant>
        <account>' .          $this->xmlEscape($this->merchant['account_id']) . '</account>
        <site_id>' .          $this->xmlEscape($this->merchant['site_id']) . '</site_id>
        <site_secure_code>' . $this->xmlEscape($this->merchant['site_code']) . '</site_secure_code>
      </merchant>
      <transaction>
        <id>' .               $this->xmlEscape($this->transaction['id']) . '</id>
      </transaction>
    </status>';
    
    return $request;
  }
  
  
  /*
   * Create the gateway request xml
   */
  function createGatewaysRequest(){
    $request = '<?xml version="1.0" encoding="UTF-8"?>
    <gateways ua="' . $this->plugin_name . ' ' . $this->version . '">
      <merchant>
        <account>' .          $this->xmlEscape($this->merchant['account_id']) . '</account>
        <site_id>' .          $this->xmlEscape($this->merchant['site_id']) . '</site_id>
        <site_secure_code>' . $this->xmlEscape($this->merchant['site_code']) . '</site_secure_code>
      </merchant>
      <customer>
        <country>' .          $this->xmlEscape($this->transaction['id']) . '</country>
      </customer>
    </gateways>';
    
    return $request;
  }
  

  /*
   * Creates the signature
   */
  function createSignature(){
    $this->signature = md5(
      $this->transaction['amount'] .
      $this->transaction['currency'] .
      $this->merchant['account_id'] .
      $this->merchant['site_id'] .
      $this->transaction['id']
      );
  }


  /*
   * Sets the customers ip variables
   */
  function setIp(){
    $this->customer['ipaddress'] = $_SERVER['REMOTE_ADDR'];

    if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])){
      $this->customer['forwardedip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
  }


  /*
   * Parses and sets customer address
   */
  function parseCustomerAddress($street_address){
    list($address, $apartment) = $this->parseAddress($street_address);
    $this->customer['address1'] = $address;
    $this->customer['housenumber'] = $apartment;
  }
  
  
  /*
   * Parses and splits up an address in street and housenumber
   */
  function parseAddress($street_address){
    $address    = $street_address;
    $apartment  = "";

    $offset = strlen($street_address);

    while (($offset = $this->rstrpos($street_address, ' ', $offset)) !== false) {
      if ($offset < strlen($street_address)-1 && is_numeric($street_address[$offset + 1])) {
        $address   = trim(substr($street_address, 0, $offset));
        $apartment = trim(substr($street_address, $offset + 1));
        break;
      }
    }

    if (empty($apartment) && strlen($street_address) > 0 && is_numeric($street_address[0])) {
      $pos = strpos($street_address, ' ');

      if ($pos !== false) {
        $apartment = trim(substr($street_address, 0, $pos), ", \t\n\r\0\x0B");
        $address   = trim(substr($street_address, $pos + 1));
      }
    }

    return array($address, $apartment);
  }


  /*
   * Returns the api url
   */
  function getApiUrl(){
    if ($this->test){
      return "https://testapi.multisafepay.com/ewx/";
    }else{
      return "https://api.multisafepay.com/ewx/";
    }
  }
  
  
  /*
   * Parse an xml response
   */
  function parseXmlResponse($response){
    // strip xml line
    $response = preg_replace('#</\?xml[^>]*>#is', '', $response);

    // parse
    $parser = new msp_gc_xmlparser($response);
    $this->parsed_xml = $parser->GetData();
    $this->parsed_root = $parser->GetRoot();
    $rootNode = $this->parsed_xml[$this->parsed_root];
    
    // check if valid response?
    
    // check for error
    $result = $this->parsed_xml[$this->parsed_root]['result'];
    if ($result != "ok"){
      $this->error_code = $rootNode['error']['code']['VALUE'];
      $this->error      = $rootNode['error']['description']['VALUE'];
      return false;
    }
    
    return $rootNode;
  }
  

  /*
   * Returns the string escaped for use in XML documents
   */
  function xmlEscape($str){
    return htmlspecialchars($str,ENT_COMPAT, "UTF-8");
  }

  /*
   * Returns the string with all XML escaping removed
   */
  function xmlUnescape($str){
    return html_entity_decode($str,ENT_COMPAT, "UTF-8");
  }

  /*
   * Post the supplied XML data and return the reply
   */
  function xmlPost($url, $request_xml, $verify_peer = false){
    $curl_available = extension_loaded("curl");

    // generate request
    $header = array();

    if (!$curl_available) {
      $url = parse_url($url);

      if (empty($url['port'])) {
        $url['port'] = $url['scheme'] == "https" ? 443 : 80;
      }

      $header[] = "POST " . $url['path'] . "?" . $url['query'] . " HTTP/1.1";
      $header[] = "Host: " . $url['host'] . ":" . $url['port'];
      $header[] = "Content-Length: " . strlen($request_xml);
    }

    $header[] = "Content-Type: text/xml";
    $header[] = "Connection: close";

    // issue request
    if ($curl_available) {
      $ch = curl_init($url);

      curl_setopt($ch, CURLOPT_POST,           true);
      curl_setopt($ch, CURLOPT_HTTPHEADER,     $header);
      curl_setopt($ch, CURLOPT_POSTFIELDS,     $request_xml);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_TIMEOUT,        30);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, $verify_peer);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
      curl_setopt($ch, CURLOPT_MAXREDIRS,      5);
      curl_setopt($ch, CURLOPT_HEADER,         true);
      //curl_setopt($ch, CURLOPT_HEADER_OUT,     true);

      $reply_data = curl_exec($ch);
    } else {
      $request_data  = implode("\r\n", $header);
      $request_data .= "\r\n\r\n";
      $request_data .= $request_xml;
      $reply_data    = "";

      $errno  = 0;
      $errstr = "";

      $fp = fsockopen(($url['scheme'] == "https" ? "ssl://" : "") . $url['host'], $url['port'], $errno, $errstr, 30);

      if ($fp) {
        if (function_exists("stream_context_set_params")) {
          stream_context_set_params($fp, array(
          'ssl' => array(
            'verify_peer'       => $verify_peer,
            'allow_self_signed' => $verify_peer
            )
            ));
        }

        fwrite($fp, $request_data);
        fflush($fp);

        while (!feof($fp)) {
          $reply_data .= fread($fp, 1024);
        }

        fclose($fp);
      }
    }

    // check response
    if ($curl_available) {
      if (curl_errno($ch)) {
        $this->error_code = -1;
        $this->error      = "curl error: " . curl_errno($ch);
        return false;
      }

      $reply_info = curl_getinfo($ch);
      curl_close($ch);
    } else {
      if ($errno) {
        $this->error_code = -1;
        $this->error      = "connection error: " . $errno;
        return false;
      }

      $header_size  = strpos($reply_data, "\r\n\r\n");
      $header_data  = substr($reply_data, 0, $header_size);
      $header       = explode("\r\n", $header_data);
      $status_line  = explode(" ", $header[0]);
      $content_type = "application/octet-stream";

      foreach ($header as $header_line) {
        $header_parts = explode(":", $header_line);

        if (strtolower($header_parts[0]) == "content-type") {
          $content_type = trim($header_parts[1]);
          break;
        }
      }

      $reply_info = array(
        'http_code'    => (int) $status_line[1],
        'content_type' => $content_type,
        'header_size'  => $header_size + 4
        );
    }

    if ($reply_info['http_code'] != 200) {
      $this->error_code = -1;
      $this->error      = "http error: " . $reply_info['http_code'];
      return false;
    }

    if (strstr($reply_info['content_type'], "/xml") === false) {
      $this->error_code = -1;
      $this->error      = "content type error: " . $reply_info['content_type'];
      return false;
    }

    // split header and body    
    $reply_header = substr($reply_data, 0, $reply_info['header_size'] - 4);
    $reply_xml    = substr($reply_data, $reply_info['header_size']);
    
    if (empty($reply_xml)){
      $this->error_code = -1;
      $this->error      = "received empty response";
      return false;
    }

    return $reply_xml;
  }

  // From http://www.php.net/manual/en/function.strrpos.php#78556
  function rstrpos($haystack, $needle, $offset = null){
    $size = strlen($haystack);

    if (is_null($offset)) {
      $offset = $size;
    }

    $pos = strpos(strrev($haystack), strrev($needle), $size - $offset);

    if ($pos === false) {
      return false;
    }

    return $size - $pos - strlen($needle);
  }
}

/**
 * Classes used to parse xml data
 */
class msp_gc_xmlparser {

  var $params = array(); //Stores the object representation of XML data
  var $root = NULL;
  var $global_index = -1;
  var $fold = false;

 /* Constructor for the class
  * Takes in XML data as input( do not include the <xml> tag
  */
  function msp_gc_xmlparser($input, $xmlParams=array(XML_OPTION_CASE_FOLDING => 0)) {
  
    // XML PARSE BUG: http://bugs.php.net/bug.php?id=45996
    $input = str_replace('&amp;', '[msp-amp]', $input);
    //
    
    $xmlp = xml_parser_create();
    foreach($xmlParams as $opt => $optVal) {
      switch( $opt ) {
        case XML_OPTION_CASE_FOLDING:
          $this->fold = $optVal;
         break;
        default:
         break;
      }
      xml_parser_set_option($xmlp, $opt, $optVal);
    }
    
    if(xml_parse_into_struct($xmlp, $input, $vals, $index)) {
      $this->root = $this->_foldCase($vals[0]['tag']);
      $this->params = $this->xml2ary($vals);
    }
    xml_parser_free($xmlp);
  }
  
  function _foldCase($arg) {
    return( $this->fold ? strtoupper($arg) : $arg);
  }

  /*
  * Credits for the structure of this function
  * http://mysrc.blogspot.com/2007/02/php-xml-to-array-and-backwards.html
  * 
  * Adapted by Ropu - 05/23/2007 
  * 
  */
  function xml2ary($vals) {

      $mnary=array();
      $ary=&$mnary;
      foreach ($vals as $r) {
          $t=$r['tag'];
          if ($r['type']=='open') {
              if (isset($ary[$t]) && !empty($ary[$t])) {
                  if (isset($ary[$t][0])){
                    $ary[$t][]=array(); 
                  }
                  else {
                    $ary[$t]=array($ary[$t], array());
                  } 
                  $cv=&$ary[$t][count($ary[$t])-1];
              }
              else {
                $cv=&$ary[$t];
              }
              $cv=array();
              if (isset($r['attributes'])) { 
                foreach ($r['attributes'] as $k=>$v) {
                  $cv[$k]=$v;
                }
              }
              
              $cv['_p']=&$ary;
              $ary=&$cv;
  
          } else if ($r['type']=='complete') {
              if (isset($ary[$t]) && !empty($ary[$t])) { // same as open
                  if (isset($ary[$t][0])) {
                    $ary[$t][]=array();
                  }
                  else {
                    $ary[$t]=array($ary[$t], array());
                  } 
                  $cv=&$ary[$t][count($ary[$t])-1];
              }
              else {
                $cv=&$ary[$t];
              } 
              if (isset($r['attributes'])) {
                foreach ($r['attributes'] as $k=>$v) {
                  $cv[$k]=$v;
                }
              }
              $cv['VALUE'] = (isset($r['value']) ? $r['value'] : '');
              
              // XML PARSE BUG: http://bugs.php.net/bug.php?id=45996
              $cv['VALUE'] = str_replace('[msp-amp]', '&amp;', $cv['VALUE']);
              //
  
          } elseif ($r['type']=='close') {
              $ary=&$ary['_p'];
          }
      }    
      
      $this->_del_p($mnary);
      return $mnary;
  }
  
  // _Internal: Remove recursion in result array
  function _del_p(&$ary) {
      foreach ($ary as $k=>$v) {
          if ($k==='_p') {
            unset($ary[$k]);
          }
          else if(is_array($ary[$k])) {
            $this->_del_p($ary[$k]);
          }
      }
  }

  /* Returns the root of the XML data */
  function GetRoot() {
    return $this->root; 
  }

  /* Returns the array representing the XML data */
  function GetData() {
    return $this->params; 
  }
}

?>