<?php

/**
 * Simple backend for the web application.
 */

session_save_path('/app/web/sessions');
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use lbuchs\WebAuthn\WebAuthn;
use lbuchs\WebAuthn\WebAuthnException;

$domain = "lndo.site";
$webauthn = new WebAuthn("Simple Passkey App", $domain);

$credential_json = $_POST['credential'];
$credential = json_decode($credential_json, true);

$client_data = base64_decode($credential['client']);
$attestation_data = base64_decode($credential['attest']);
try {
  $credential = $webauthn->processCreate(
    $client_data,
    $attestation_data,
    $_SESSION['challenge']
  );
  // If you got here, the passkey was created successfully and is valid.
  // Let's create the user account.
  $user_id = create_user($_SESSION['email']);
  // Let's create the passkey.
  $nickname = $_SERVER['HTTP_USER_AGENT'] . ' - ' . $_SESSION['email'];
  $passkey = create_passkey($user_id, $credential, $nickname);

  // Let's clean up the session.
  session_unset();

  // Let's save the user id to the session, logging them in.
  $_SESSION['user_id'] = $user_id;
  session_write_close();

  // Let's inform the client that the passkey was created successfully.
  echo json_encode(['success' => 'Passkey created successfully']);
}
catch (WebAuthnException $e) {
  echo json_encode(['error' => $e->getMessage()]);
  exit;
}

function create_user($email) {
  $dsn = 'mysql:host=database;dbname=lamp';
  $username = 'lamp';
  $password = 'lamp';

  try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO user (email, created_at, modified_at) VALUES (:email, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())');
    $stmt->bindParam(':email', $email);
    $stmt->execute();

    return $pdo->lastInsertId();
  }
  catch (PDOException $e) {
    echo $e->getMessage();
    return false;
  }
}

function create_passkey($user_id, $credential, $nickname) {
  $dsn = 'mysql:host=database;dbname=lamp';
  $username = 'lamp';
  $password = 'lamp';

  try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare('INSERT INTO passkey (user_id, unique_id, nickname, credential_id, public_key, created_at, modified_at) VALUES (:userid, :uniqueid, :nick, :credid, :publickey, UNIX_TIMESTAMP(), UNIX_TIMESTAMP())');
    $stmt->bindParam(':userid', $user_id);
    $stmt->bindParam(':uniqueid', $_SESSION['unique_id']);
    $stmt->bindParam(':nick', $nickname);
    $credential_id = bin2hex($credential->credentialId);
    $stmt->bindParam(':credid', $credential_id);
    $stmt->bindParam(':publickey', $credential->credentialPublicKey);
    $stmt->execute();

    return $pdo->lastInsertId();

  }
  catch (PDOException $e) {
    echo $e->getMessage();
    return false;
  }
}
