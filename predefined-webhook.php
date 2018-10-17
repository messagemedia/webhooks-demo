<?php
require_once "vendor/autoload.php";
require_once "config.php";

use MessageMediaMessagesLib\MessageMediaMessagesClient;
use MessageMediaMessagesLib\APIHelper;

$authUserName = API_KEY; // The API key to use with basic/HMAC authentication
$authPassword = API_SECRET; // The API secret to use with basic/HMAC authentication
$useHmacAuthentication = false; // Change to true if you are using HMAC keys

$client = new MessageMediaMessagesLib\MessageMediaMessagesClient($authUserName, $authPassword, $useHmacAuthentication);

$messages = $client->getMessages();

$bodyValue = '{
   "messages":[
      {
         "content":"Predefined Webhooks demo",
         "destination_number":"' . MY_PHONE . '",
         "callback_url": "' . NGROK_URL . '/webhooks-demo/index.php"
      }
   ]
}';


$body = MessageMediaMessagesLib\APIHelper::deserialize($bodyValue);

$result = $messages->createSendMessages($body);

var_dump($result);

?>