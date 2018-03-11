<?php
require('config.php'); // load config values

// Verify signature
$xHubSignature = $request->getHeader('X-Hub-Signature');
echo "Received xHubSignature:" . $xHubSignature;

if (hash_hmac( 'sha1', $xHubSignature, $gitHubSecret) ) {
  echo "Signature OK!";
} else {
  echo "Signature not okay. Exiting.";
  die();
}

?>
