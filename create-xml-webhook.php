<?php
require_once('vendor/autoload.php');
require_once('config.php');

$basicAuthUserName = API_KEY; // The username to use with basic authentication
$basicAuthPassword = API_SECRET; // The password to use with basic authentication

$client = new MessageMediaWebhooksLib\MessageMediaWebhooksClient($basicAuthUserName, $basicAuthPassword);

$webhooks = $client->getWebhooks();

$body = new MessageMediaWebhooksLib\Models\CreateWebhookRequest();
$body->url = NGROK_URL . '/webhooks-demo/index.php';
$body->method = "POST";
$body->encoding = "XML";
$body->headers = array("X-Custom-Header" => "Hello world!");
$body->events = array("RECEIVED_SMS");
$body->template = '<message>
    <content>
        <original>$mtContent</original>
        <reply>$moContent</reply>
    </content>
    <addresses>
        <recipient>$destinationAddress</recipient>
        <sender>$sourceAddress</sender>
    </addresses>
    <serviceType>$type</serviceType>
    <messageId>$mtId</messageId>
    <replyId>$moId</replyId>
</message>';

try {
	$result = $webhooks->createWebhook($body);
} catch (Exception $e) {
	var_dump($e);
}

var_dump($result);