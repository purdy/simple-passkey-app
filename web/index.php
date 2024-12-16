<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Passkeys demo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  </head>
  <body>
    <div class="container">
      <div class="row">
        <div class="col">
          <h1>Passkeys Demo</h1>
          <p>This is a simple demo web application with a form below to create an account or log in with passkeys.</p>
        </div>
      </div>
      <div class="row mb-4">
        <div class="col">
          <form action="backend-signup.php" method="post">
            <div class="mb-3">
              <label for="username" class="form-label">Your email address</label>
              <input type="email" class="form-control" id="email" name="email" required="required">
            </div>
            <button type="submit" id="create-passkey" class="btn btn-primary">Create Passkey</button>
          </form>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <p>Or if you've already created a passkey, log in below:</p>
          <a href="backend-login.php" class="btn btn-primary">Log in with passkey</a>
        </div>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script src="/js/passkeys.js"></script>
  </body>
</html>
