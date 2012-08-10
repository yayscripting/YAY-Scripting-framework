<?php
/**
 * Pay with PayPal
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


$ret = ($r->doExpressCheckout(10, 'Access to source code library'));

//An error occured. The auxiliary information is in the $ret array

print_r($ret);

?>