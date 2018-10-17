<?php 

echo "Are you sure you want to delete your signature keys?" . PHP_EOL;
$handle = fopen ("php://stdin","r");
$line = fgets($handle);
if(trim($line) != 'yes'){
    echo "Cancelled!" . PHP_EOL;
    exit;
}
fclose($handle);

require_once('vendor/autoload.php');
require_once('config.php');

$basicAuthUserName = API_KEY; // The username to use with basic authentication
$basicAuthPassword = API_SECRET; // The password to use with basic authentication

$client = new MessageMediaSigningKeysLib\MessageMediaSigningKeysClient($basicAuthUserName, $basicAuthPassword);
$signatureKeyManagement = $client->getSignatureKeyManagement();

$result = $signatureKeyManagement->getSignatureKeyList();

foreach($result as $sigKey) {
	echo 'Deleting sig key: ' . $sigKey->keyId . PHP_EOL;
	$signatureKeyManagement->deleteSignatureKey($sigKey->keyId);
}