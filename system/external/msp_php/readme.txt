General MultiSafepay class to be used for custom implementations.


MultiSafepay.class.php
  Class file. 
    startTransaction()   // does a transaction request (returns payment_url)
    getStatus()          // does a status request      (returns status)
    getGateways()        // does a gateway request     (returns array with gateways (id => name)


MultiSafepay.config.php
  Configuration values


index.php
  Contains a link to pay.php


pay.php
  Uses the MultiSafepay class to start a transaction


notify.php (used in pay.php)
  Use this for payment notifications.
  
  There are two variations:
    - notify.php?type=initial
      this is the notification_url in the transaction request (called at the end of the transaction)
      output will be a link back to the shop
      
    - notify.php
      link for the MultiSafepay back-end for delayed payment notifications
      output should "ok", or an error message if something went wrong

  Generally both variations should update the status in the local database
  
  
return.php (used in notify.php)
  Simple return page.
