<?php

session_save_path('/app/web/sessions');
session_start();

if (!isset($_SESSION['user_id']) || !$_SESSION['user_id']) {
  header('Location: /');
  exit;
}

$dsn = 'mysql:host=database;dbname=lamp';
$username = 'lamp';
$password = 'lamp';

$pdo = new PDO($dsn, $username, $password);
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$stmt = $pdo->prepare('SELECT * FROM user WHERE id = :id');
$stmt->execute(['id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

if (!$user) {
  header('Location: /');
  exit;
}

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Logged-in Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>Your Dashboard</h1>
          <p>Welcome, <?php echo $user['email']; ?>!</p>
          <p><a href="/logout.php">Logout</a></p>
        </div>
        <div class="col">
          <h2>Your Passkeys:</h2>
          <ul>
            <?php
            $stmt = $pdo->prepare('SELECT * FROM passkey WHERE user_id = :user_id');
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $passkeys = $stmt->fetchAll();
            foreach ($passkeys as $passkey) {
              echo '<li>' . $passkey['nickname'] . '</li>';
            }
            ?>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  </body>
</html>
