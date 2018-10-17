<?php
  $publicKey = openssl_pkey_get_public(file_get_contents('./signing-key.pem'));
  $body = file_get_contents('php://input');

  // An example of what each field should look like has been added next to the corresponding field
  @$signature = base64_decode($_SERVER['HTTP_X_MESSAGEMEDIA_SIGNATURE']); 
  @$date = $_SERVER['HTTP_DATE']; // "Wed, 18 Jul 2018 06:33:52 GMT";
  
  $requestLine = sprintf(
    "%s %s %s",
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
    $_SERVER['SERVER_PROTOCOL']
  );
  
  $data = $requestLine.$date.$body;
  
  $ok = openssl_verify($data, $signature, $publicKey, OPENSSL_ALGO_SHA224);
  if ($ok == 1) {
      echo "Verification Successful";
      file_put_contents('good-requests/' . uniqid(), $data);
      http_response_code (200);
  } elseif ($ok == 0) {
      echo "Verification Failed";
      file_put_contents('bad-requests/' . uniqid(), $data);
      http_response_code (401);
  } else {
      echo "Error whilst checking request signature.";
  }
?>