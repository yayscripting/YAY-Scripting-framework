<?php

include('MultiSafepay.class.php');
include('MultiSafepay.config.php');

$msp = new MultiSafepay();

// transaction id (same as the transaction->id given in the transaction request)
$transactionid = $_GET['transactionid'];

// (notify.php?type=initial is used as notification_url and should output a link)
$initial       = ($_GET['type'] == "initial");

/* 
 * Merchant Settings
 */
$msp->test                         = MSP_TEST_API;
$msp->merchant['account_id']       = MSP_ACCOUNT_ID;
$msp->merchant['site_id']          = MSP_SITE_ID;
$msp->merchant['site_code']        = MSP_SITE_CODE;

/* 
 * Transaction Details
 */
$msp->transaction['id']            = $transactionid; 


// returns the status
$status = $msp->getStatus();

if ($msp->error && !$initial){ // only show error if we dont need to display the link
  echo "Error " . $msp->error_code . ": " . $msp->error;
  exit();
}

switch ($status) {
  case "initialized": // waiting
    break;
  case "completed":   // payment complete
    break;
  case "uncleared":   // waiting (credit cards or direct debit)
    break;
  case "void":        // canceled
    break;
  case "declined":    // declined
    break;
  case "refunded":    // refunded
    break;
  case "expired":     // expired
    break;
  default:
}

if ($initial){ 
  // displayed at the last page of the transaction proces (if no redirect_url is set)
  echo '<a href="' . BASE_URL . 'return.php">Return to shop</a>';
}else{
  // link to notify.php for MultiSafepay back-end (for delayed payment notifications)
  // backend expects an "ok" if no error occurred
  echo "ok";
}
?>
