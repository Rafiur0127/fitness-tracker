<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login – Fitness Tracker</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f0f4f8;
      padding: 40px;
      margin: 0;
    }

    .auth-card {
      max-width: 420px;
      margin: 40px auto;
      background: #fff;
      border-radius: 10px;
      box-shadow: 0 0 12px rgba(0,0,0,0.08);
      padding: 30px;
    }

    h2 {
      text-align: center;
      margin-top: 0;
    }

    label {
      display: block;
      margin-top: 14px;
      font-weight: bold;
      font-size: 14px;
    }

    input, button {
      width: 100%;
      padding: 12px 10px;
      margin-top: 8px;
      box-sizing: border-box;
      border-radius: 6px;
      border: 1px solid #d5d9df;
      font-size: 15px;
    }

    button {
      background: #4a90e2;
      color: white;
      border: none;
      cursor: pointer;
      margin-top: 18px;
      font-weight: bold;
    }

    button:hover {
      background: #357abd;
    }

    .message {
      padding: 10px 12px;
      border-radius: 6px;
      margin-bottom: 16px;
      display: none;
    }

    .message.error {
      background: #ffe6e6;
      color: #b30000;
      display: block;
    }

    .message.success {
      background: #e6ffe6;
      color: #006600;
      display: block;
    }

    .meta {
      text-align: center;
      margin-top: 16px;
    }
  </style>
</head>
<body>
  <div class="auth-card">
    <h2>Login to Your Account</h2>
    <div id="message" class="message"></div>

    <form id="loginForm">
      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required>
      </div>

      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" autocomplete="current-password" required>
      </div>

      <button type="submit">Login</button>
    </form>

    <p class="meta">
      Don't have an account? <a href="signup.php" id="signupLink">Sign up</a>
    </p>
  </div>

  <script>
    const loginForm = document.getElementById('loginForm');
    const messageBox = document.getElementById('message');
    const requestedPage = new URLSearchParams(window.location.search).get('next');
    let csrfToken = '';

    if (requestedPage) {
      document.getElementById('signupLink').href = './signup.php?next=' + encodeURIComponent(requestedPage);
    }

    function showMessage(text, type) {
      messageBox.textContent = text;
      messageBox.className = `message ${type}`;
    }

    async function getCsrfToken() {
      if (csrfToken) return csrfToken;
      const response = await fetch('./api/auth.php', {
        credentials: 'same-origin',
        headers: { 'Accept': 'application/json' }
      });
      const result = await response.json();
      if (!response.ok || !result.success) throw new Error(result.message || 'Unable to initialize security token.');
      csrfToken = result.data.csrf_token;
      return csrfToken;
    }

    loginForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formData = new FormData(loginForm);
      const payload = {
        action: 'login',
        email: formData.get('email')?.toString().trim() || '',
        password: formData.get('password')?.toString() || ''
      };

      try {
        const token = await getCsrfToken();
        const response = await fetch('./api/auth.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-Token': token
          },
          body: JSON.stringify(payload)
        });

        const result = await response.json();

        if (!response.ok || result.success === false) {
          const message = result.message || 'Login failed.';
          showMessage(message, 'error');
          return;
        }

        showMessage(result.message || 'Login successful.', 'success');
        const allowedPages = new Set([
          'dashboard.php',
          'workout_logger.php',
          'watertracker.php',
          'goals.php',
          'nutrition.php',
          'Workout_plans.php',
          'friend_challenges.php'
        ]);
        const destination = allowedPages.has(requestedPage) ? requestedPage : 'dashboard.php';
        window.location.href = './' + destination;
      } catch (error) {
        showMessage('Network error. Please try again.', 'error');
      }
    });
  </script>
</body>
</html>
