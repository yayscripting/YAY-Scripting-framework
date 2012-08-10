<?php
/**
 * PayPal successfull payment return
 *
 * @version 1.0
 * @author Martin Maly - http://www.php-suit.com
 * @copyright (C) 2008 martin maly
 */
 
 /*
* Copyright (c) 2008 Martin Maly - http://www.php-suit.com
* All rights reserved.
*/

require_once('../classes/paypal.php'); //when needed
require_once('../classes/httprequest.php'); //when needed

//Use this form for production server 
//$r = new PayPal(true);

//Use this form for sandbox tests
$r = new PayPal();


/*
$token = $_GET['token'];
$d = $r->getCheckoutDetails($token);
print_r($d);
die();
*/

$final = $r->doPayment();

if ($final['ACK'] == 'Success') {
	echo 'Succeed!';
} else {
	print_r($final);
}

die();
?>