<?php

/**
 * Simple backend for the web application.
 */

session_save_path('/app/web/sessions');
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use lbuchs\WebAuthn\WebAuthn;

$domain = "lndo.site";
$webauthn = new WebAuthn("Simple Passkey App", $domain);
$_SESSION['email'] = strtolower(trim($_POST['email']));
$_SESSION['unique_id'] = bin2hex(random_bytes(8));

$response = $webauthn->getCreateArgs(\hex2bin($_SESSION['unique_id']), $_SESSION['email'], $_SESSION['email']);
$_SESSION['challenge'] = ($webauthn->getChallenge())->getBinaryString();

echo json_encode($response);

session_write_close();
