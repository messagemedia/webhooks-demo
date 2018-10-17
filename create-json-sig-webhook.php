<?php
require_once('vendor/autoload.php');
require_once('config.php');

$basicAuthUserName = API_KEY; // The username to use with basic authentication
$basicAuthPassword = API_SECRET; // The password to use with basic authentication

$client = new MessageMediaWebhooksLib\MessageMediaWebhooksClient($basicAuthUserName, $basicAuthPassword);

$webhooks = $client->getWebhooks();

$body = new MessageMediaWebhooksLib\Models\CreateWebhookRequest();
$body->url = NGROK_URL . '/webhooks-demo/signed-webhook-handler.php';
$body->method = "POST";
$body->encoding = "JSON";
$body->headers = array("X-Custom-Header" => "Hello world!");
$body->events = array("RECEIVED_SMS");
$body->template = '{
	"content": {
		"original": "$mtContent",
		"reply": "$moContent"
	},
	"addresses": {
		"recipient": "$destinationAddress",
		"sender": "$sourceAddress"
	},
	"service_type": "$type",
	"message_id": "$mtId",
	"reply_id": "$moId"
}';

try {
	$result = $webhooks->createWebhook($body);
} catch (Exception $e) {
	var_dump($e);
}

var_dump($result);