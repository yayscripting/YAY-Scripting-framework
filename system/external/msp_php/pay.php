<?php

include('MultiSafepay.class.php');
include('MultiSafepay.config.php');

$msp = new MultiSafepay();

/* 
 * Merchant Settings
 */
$msp->test                         = MSP_TEST_API;
$msp->merchant['account_id']       = MSP_ACCOUNT_ID;
$msp->merchant['site_id']          = MSP_SITE_ID;
$msp->merchant['site_code']        = MSP_SITE_CODE;
$msp->merchant['notification_url'] = BASE_URL . 'notify.php?type=initial';
$msp->merchant['cancel_url']       = BASE_URL . 'index.php';
// optional automatic redirect back to the shop:
// $msp->merchant['redirect_url']     = BASE_URL . 'return.php'; 

/* 
 * Customer Details
 */
$msp->customer['locale']           = 'nl';
$msp->customer['firstname']        = 'Jan';
$msp->customer['lastname']         = 'Modaal';
$msp->customer['zipcode']          = '1234AB';
$msp->customer['city']             = 'Amsterdam';
$msp->customer['country']          = 'NL';
$msp->customer['phone']            = '012-3456789';
$msp->customer['email']            = 'test@example.com';

$msp->parseCustomerAddress('Teststraat 21');
// or 
// $msp->customer['address1']         = 'Teststraat';
// $msp->customer['housenumber']      = '21';

/* 
 * Transaction Details
 */
$msp->transaction['id']            = rand(100000000,999999999); // generally the shop's order ID is used here
$msp->transaction['currency']      = 'EUR';
$msp->transaction['amount']        = '1000'; // cents
$msp->transaction['description']   = 'Order #' . $msp->transaction['id'];
$msp->transaction['items']         = '<br/><ul><li>1 x Item1</li><li>2 x Item2</li></ul>';

// returns a payment url
$url = $msp->startTransaction();

if ($msp->error){
  echo "Error " . $msp->error_code . ": " . $msp->error;
  exit();
}

// redirect
header('Location: ' . $url);

?>