<?php
/* Encryption settings
 *
 * A hash-algorithm should be slow to avoid bruteforce-attacks.
 * However, if you use a hash in an encryption-method, the algorithm should be fast because of performance-matters
 * 
 * Check this list for speed-information:
 * http://nl.php.net/manual/en/function.hash.php#89574
*/

return array(

	'hash_algorithm' => 'whirlpool',
	'encryption_hash_algorithm' => 'sha256'
	
);