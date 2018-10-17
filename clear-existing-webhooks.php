<?php

echo "Are you sure you want to delete your custom webhooks?" . PHP_EOL;
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

$client = new MessageMediaWebhooksLib\MessageMediaWebhooksClient($basicAuthUserName, $basicAuthPassword);

$webhooks = $client->getWebhooks();

$page  =  0;
$pageSize  =  10;

$result  =  $webhooks->retrieveWebhook($page, $pageSize);
foreach($result->pageData as $webhook) {
	echo 'Deleting webhook: ' . $webhook->id . PHP_EOL;
	$webhooks->deleteWebhook($webhook->id);
} 