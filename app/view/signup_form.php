<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up – Fitness Tracker</title>
  <style>
    body { font-family: Arial, sans-serif; background: #f4f7f9; padding: 40px; }
    form { max-width: 400px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input, select, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
    button { background: #4a90e2; color: white; border: none; border-radius: 5px; cursor: pointer; }
    button:hover { background: #357abd; }
    .error { background: #ffe6e6; padding: 10px; color: #b30000; margin-bottom: 10px; border-radius: 5px; }
  </style>
</head>
<body>

<h2 style="text-align:center;">Create Your Account</h2>

<form method="POST">
  <?php csrf_field(); ?>

  <?php if (!empty($errors)): ?>
    <div class="error">
      <ul><?php foreach ($errors as $err): ?><li><?= htmlspecialchars($err) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <input type="text"   name="name"     placeholder="Full Name"       required autocomplete="name">
  <input type="email"  name="email"    placeholder="Email"           required autocomplete="email">
  <input type="password" name="password" placeholder="Password (8+ chars, upper & lower & number)" required>
  <input type="password" name="confirm"  placeholder="Confirm Password" required>
  <input type="number" name="age"      placeholder="Age"    min="18"  required>
  <select name="gender" required>
    <option value="">Select Gender</option>
    <option value="Male">Male</option>
    <option value="Female">Female</option>
    <option value="Other">Other</option>
  </select>
  <button type="submit">Sign Up</button>
</form>

<p style="text-align:center; margin-top:15px;">
  Already have an account? <a href="login.php">Log in</a>
</p>

</body>
</html>
