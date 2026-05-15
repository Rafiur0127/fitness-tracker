<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Fitness Tracker</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f0f4f8; padding: 40px; }
    form { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
    button { background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #357abd; }
    .error { background: #ffe6e6; padding: 10px; color: #b30000; margin-bottom: 10px; border-radius: 5px; }
    .success { background: #e6ffe6; padding: 10px; color: #006600; margin-bottom: 10px; border-radius: 5px; }
  </style>
</head>
<body>

<h2 style="text-align:center;">Login to Your Account</h2>

<form method="POST">
  <?php csrf_field(); ?>

  <?php if (!empty($login_errors)): ?>
    <div class="error">
      <ul><?php foreach ($login_errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <?php if (isset($_GET['registered'])): ?>
    <div class="success">Account created! You can now log in.</div>
  <?php endif; ?>

  <input type="email" name="email" placeholder="Email" required autocomplete="email">
  <input type="password" name="password" placeholder="Password" required autocomplete="current-password">
  <button type="submit">Login</button>
</form>

<p style="text-align:center; margin-top:15px;">
  Don't have an account? <a href="signup.php">Sign up</a>
</p>

</body>
</html>
