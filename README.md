# MessageMedia Webhooks Demo Library
This library contains a set of scripts to demonstrate the use of MessageMedia's Webhooks for demonstration purposes.

## Setup

Firstly, copy the `example_config.php` file to `config.php` and update constants in that file with your MessageMedia API Key, API Secret, the phone number you wish to use for the purpose of testing your Webhooks and a base URL for which all Webhooks should be configured to point to. This library assumes you are using Ngrok to tunnel requests to your local installation of this library, hence the name of the constant, however if this code is checked out on a publicly accessible machine then you can use that URL within this configuration file.

If you are using Ngrok, be sure to setup your tunnel using `ngrok http 80` and then update the `config.php` file with your Ngrok URL.

Run `php composer install` to install all the required dependencies, including the MessageMedia SDKs used in this demo.

Note, this library should be checked out in your system where the files can be served by a running web server.

## Demonstration #1 - Use predefined Webhooks via the Messages API

Run `php predefined-webhook.php` which will send a message to the configured phone number and use the Message APIs `callback_url` parameter to specify the URL to which Webhooks will be pushed to. Inspecting the Webhooks via the Ngrok Web Interface, normally http://localhost:4040. You should see two Webhooks received one for an ENROUTE delivery receipt and one for a SUBMITTED delivery receipt. If you reply to the message received on your phone, you should see a third Webhook with the details of the reply.

[For more information on the Messages API, check out the documentation](https://developers.messagemedia.com/code/messages-api-documentation/)

## Demonstration #2 - Create a custom Webhook via the Webhooks API

To make this demo library nice and reusable, there is a script which will clear custom Webhooks from your account - use it carefully, it will delete all Webhooks that have been configured on your account - **don't** use this script on your production accounts!!!

Now that you've been warned, run `php clear-existing-webhooks.php` to clean up any existing Webhooks on your account, type `yes` when prompted (case sensitive).

### Custom JSON Webhook

Next, run `php create-json-webhook.php` to configure a Webhook on your account which will fire whenever a reply is received to any messages sent from your MessageMedia account. This Webhook will have the following structure:

```json
{
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
}
```

It will also include a header `X-Custom-Header` with the value `Hello world!`.

Once this Webhook has been created, run `php send-message.php` which will send a message to the configured phone number. Reply to this message and you should see a Webhook containing details of the reply message. Again use the Ngrok Web Interface to inspect the details of the Webhook.

### Custom XML Webhook

Clean up the last Webhook by running `php clear-existing-webhooks.php` and then run `php create-xml-webhook.php` to configure a new Webhook, this time which contains an XML formatted body as follows:

```xml
<message>
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
</message>
```

It will also include a header `X-Custom-Header` with the value `Hello world!`.

Once this Webhook has been created, run `php send-message.php` which will send a message to the configured phone number. Reply to this message and you should see a Webhook containing details of the reply message. Again use the Ngrok Web Interface to inspect the details of the Webhook.

### Custom GET Webhook

Clean up the last Webhook by running `php clear-existing-webhooks.php` and then run `php create-get-webhook.php` to configure a new Webhook. This Webhook won't contain a body (as it will be a GET request), instead the details about the reply are included as query parameters.

Once this Webhook has been created, run `php send-message.php` which will send a message to the configured phone number. Reply to this message and you should see a Webhook containing details of the reply message. Again use the Ngrok Web Interface to inspect the details of the Webhook.

Clean up after ourselves by running `php clear-existing-webhooks.php` before moving onto the next demo.

[For more information on the Webhooks API, check out the documentation](https://developers.messagemedia.com/code/webhooks-api-documentation)

## Demonstration #3 - Validate a Signed Webhook using Enterprise Webhooks and the Signature Keys API

MessageMedia's Enterprise Webhooks feature signs all requests with a cryptographic signature which can then be verified to ensure the Webhook originated from MessageMedia's servers. Enterprise Webhooks is not enabled by default, please contact MessageMedia Support to have this feature enabled on your account - note that there are costs associated with this feature.

### Create signing key pair

Enterprise Webhooks uses a public / private key pair to sign and verify requests. The private key is used to sign the request, and the public key is used to verify the request. To begin, you'll need to create your own key pair. Run `php create-sig-key.php` which will create a new key pair, save the public key to `signing-key.pem` and then enable that key for use in your account.

### Create Webhook

Once this key has been created, create a Webhook which points to the `signed-webhook-handler.php` script in the library. This script contains the logic that will verify the signature included in the request. To do this run `php create-json-sig-webhook.php` (this is very similar to the `create-json-webhook.php` script we ran before).

### Send a message

Now our key pair and Webhook are all setup it's time to test and observe what happens. Run `php send-message.php` which will send a message to the configured phone. Send a reply to the message and then check the Ngrok Admin Interface. A Webhook should have been caught. Open up the Headers tab and you'll notice that there are 5 new headers included in the request, `X-Messagemedia-Cipher-Type `, `X-Messagemedia-Digest-Type`, `X-Messagemedia-Key-Id`, `X-Messagemedia-Signature` and `Date`. These headers contain the signature, and details around how the signature was constructed. 

The response to the Webhook from our script `signed-webhook-handler.php` should be a HTTP 200 OK response, and the body of the response should contain the text _Verification Successful_. To assist with debugging, we should also see a folder created called good-requests with a file inside that folder which contains the details of our request.

Let's now check what happens when a request is received to our Webhook handler that doesn't have the appropriate signature. Using your browser, make a request to https://yourngrokurl.ngrok.io/webhooks-demo/signed-webhooks-handler.php. You should notice that the page shows _Verification Failed_ and if you dig in a bit further you'll notice that the page returned a HTTP 401 response. Finally, a bad-requests folder should have been created, with a file inside it which contains details of the request you just made from your browser.

Finally, there is a signature cleanup script to clean up the keys you created after this demo. Again, this will delete all signature keys created on your account - **don't** run this on your production account!

To clean up your signature keys, run `php clear-sig-keys.php`.

For more information check out the [Signature Keys API documentation](https://developers.messagemedia.com/code/signature-key-management-api-documentation) and the [Enterprise Webhooks documentation](https://developers.messagemedia.com/code/enterprise-webhooks-api-documentation).

Happy Webhooking!
