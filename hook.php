<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require('./config.php'); // load config values

// Template by https://gist.github.com/milo/daed6e958ea534e4eba3
$rawPost = NULL;

// Validation:
if ($gitHubSecret !== NULL) {
	if (!isset($_SERVER['HTTP_X_HUB_SIGNATURE'])) {
    echo "HTTP header 'X-Hub-Signature' is missing.";
    die();
	} elseif (!extension_loaded('hash')) {
		echo "Missing 'hash' extension to check the secret code validity.";
	}
	list($algo, $hash) = explode('=', $_SERVER['HTTP_X_HUB_SIGNATURE'], 2) + array('', '');
	if (!in_array($algo, hash_algos(), TRUE)) {
		echo "Hash algorithm '$algo' is not supported.";
    die();
	}
	$rawPost = file_get_contents('php://input');
	if ($hash !== hash_hmac($algo, $rawPost, $gitHubSecret)) {
		echo 'Hook secret does not match.';
    die();
	}
};

$json = $rawPost ?: file_get_contents('php://input');

# Payload structure depends on triggered event
# https://developer.github.com/v3/activity/events/types/
$payload = json_decode($json);

if ($payload->{'ref'} === "refs/heads/master") {
  echo "master push!\n";

  // download required files:
  echo shell_exec("curl " . $downloadUrl . "resume_data.xml > " . $tmpFolder . "resume_data.xml");
  echo shell_exec("curl " . $downloadUrl . "template_de.tex > " . $tmpFolder . "template_de.tex");
  echo shell_exec("curl " . $downloadUrl . "template_en.tex > " . $tmpFolder . "template_en.tex");
} else {
  echo "not-master push:" . $payload->{'ref'};
}


echo "done."

?>
