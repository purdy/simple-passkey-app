<?php

session_save_path('/app/web/sessions');
session_start();

require_once __DIR__ . '/../vendor/autoload.php';

use lbuchs\WebAuthn\WebAuthn;

$domain = "lndo.site";
$webauthn = new WebAuthn("Simple Passkey App", $domain);
$args = $webauthn->getGetArgs();
$_SESSION['challenge'] = ($webauthn->getChallenge())->getBinaryString();
echo json_encode($args);
