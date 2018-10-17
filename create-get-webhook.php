<?php
require_once('vendor/autoload.php');
require_once('config.php');

$basicAuthUserName = API_KEY; // The username to use with basic authentication
$basicAuthPassword = API_SECRET; // The password to use with basic authentication

$client = new MessageMediaWebhooksLib\MessageMediaWebhooksClient($basicAuthUserName, $basicAuthPassword);

$webhooks = $client->getWebhooks();

$body = new MessageMediaWebhooksLib\Models\CreateWebhookRequest();
$body->url = NGROK_URL . '?contentOriginal=$mtContent&contentReply=$moContent&addressRecipeint=$destinationAddress&addressSender=$sourceAddress';
$body->method = "GET";
$body->encoding = "JSON";
$body->headers = array("X-Custom-Header" => "Hello world!");
$body->events = array("RECEIVED_SMS");

try {
	$result = $webhooks->createWebhook($body);
} catch (Exception $e) {
	var_dump($e);
}

var_dump($result);