<?php 

require_once "vendor/autoload.php";
require_once('config.php');

$basicAuthUserName = API_KEY; // The username to use with basic authentication
$basicAuthPassword = API_SECRET; // The password to use with basic authentication

$client = new MessageMediaSigningKeysLib\MessageMediaSigningKeysClient($basicAuthUserName, $basicAuthPassword);
$signatureKeyManagement = $client->getSignatureKeyManagement();

$key = new MessageMediaSigningKeysLib\Models\CreateSignatureKeyRequest();
$key->digest = "SHA224";
$key->cipher = "RSA";

$result = $signatureKeyManagement->createSignatureKey($key);

// Save the public key to a file
var_dump($result);
@unlink('signing-key.pem');
file_put_contents('signing-key.pem', "-----BEGIN PUBLIC KEY-----\n" . $result->publicKey . "\n-----END PUBLIC KEY-----");

// Enable the key
$keyToEnable = new MessageMediaSigningKeysLib\Models\EnableSignatureKeyRequest();
$keyToEnable->keyId = $result->keyId;

$result = $signatureKeyManagement->updateEnableSignatureKey($keyToEnable);
var_dump($result);