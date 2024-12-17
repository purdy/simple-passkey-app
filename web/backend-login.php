<?php

session_save_path('/app/web/sessions');
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

$domain = "lndo.site";
$webauthn = new WebAuthn("Simple Passkey App", $domain);

$crendential_data = json_decode($_POST['credential'], true);
$credential_id = bin2hex(base64_decode($crendential_data['id']));
$unique_id = bin2hex(base64_decode($crendential_data['user']));

try {
  // Look for passkey in the database using the credential id.
    // This is a database query that returns the matching row.
    $passkey = get_passkey($credential_id, $unique_id);
    $client = base64_decode($crendential_data['client']);
    $auth = base64_decode($crendential_data['auth']);
    $sig = base64_decode($crendential_data['sig']);
    $valid = $webauthn->processGet(
      $client,
      $auth,
      $sig,
      $passkey['public_key'],
      $_SESSION['challenge']
    );

    if ($valid) {
      unset($_SESSION['challenge']);
      // Let's save the user id to the session, logging them in.
      $_SESSION['user_id'] = $passkey['user_id'];
      echo json_encode(['result' => 'success']);
    }
    else {
      echo json_encode(['result' => 'invalid']);
    }

}
catch (WebAuthnException $e) {
  echo json_encode(['result' => 'error', 'error' => 'WebAuthn error: ' . $e->getMessage()]);
}
catch (Exception $e) {
  echo json_encode(['result' => 'error', 'error' => 'General exception error: ' . $e->getMessage()]);
}


function get_passkey($credential_id, $unique_id) {
  $dsn = 'mysql:host=database;dbname=lamp';
  $username = 'lamp';
  $password = 'lamp';

  $pdo = new PDO($dsn, $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $stmt = $pdo->prepare('SELECT * FROM passkey WHERE credential_id = :credential_id AND unique_id = :unique_id');
  $stmt->bindParam(':credential_id', $credential_id);
  $stmt->bindParam(':unique_id', $unique_id);
  $stmt->execute();

  return $stmt->fetch(PDO::FETCH_ASSOC);
}
