<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign Up – Fitness Tracker</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f4f7f9;
      padding: 40px;
      margin: 0;
    }

    .auth-card {
      max-width: 460px;
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

    input, select, button {
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
    <h2>Create Your Account</h2>
    <div id="message" class="message"></div>

    <form id="signupForm">
      <div>
        <label for="name">Full Name</label>
        <input id="name" name="name" type="text" autocomplete="name" required>
      </div>

      <div>
        <label for="email">Email</label>
        <input id="email" name="email" type="email" autocomplete="email" required>
      </div>

      <div>
        <label for="password">Password</label>
        <input id="password" name="password" type="password" required>
      </div>

      <div>
        <label for="confirm">Confirm Password</label>
        <input id="confirm" name="confirm" type="password" required>
      </div>

      <div>
        <label for="age">Age</label>
        <input id="age" name="age" type="number" min="18" required>
      </div>

      <div>
        <label for="gender">Gender</label>
        <select id="gender" name="gender" required>
          <option value="">Select Gender</option>
          <option value="Male">Male</option>
          <option value="Female">Female</option>
          <option value="Other">Other</option>
        </select>
      </div>

      <button type="submit">Sign Up</button>
    </form>

    <p class="meta">
      Already have an account? <a href="login.php">Log in</a>
    </p>
  </div>

  <script>
    const signupForm = document.getElementById('signupForm');
    const messageBox = document.getElementById('message');
    let csrfToken = '';

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

    signupForm.addEventListener('submit', async (event) => {
      event.preventDefault();

      const formData = new FormData(signupForm);
      const payload = {
        action: 'signup',
        name: formData.get('name')?.toString().trim() || '',
        email: formData.get('email')?.toString().trim() || '',
        password: formData.get('password')?.toString() || '',
        confirm: formData.get('confirm')?.toString() || '',
        age: Number(formData.get('age') || 0),
        gender: formData.get('gender')?.toString() || ''
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
          const message = Array.isArray(result.data?.errors)
            ? result.data.errors.join(' ')
            : (result.message || 'Registration failed.');

          showMessage(message, 'error');
          return;
        }

        showMessage(result.message || 'Account created successfully.', 'success');
        signupForm.reset();
        setTimeout(() => {
          const requestedPage = new URLSearchParams(window.location.search).get('next');
          const destination = requestedPage
            ? './login.php?next=' + encodeURIComponent(requestedPage)
            : './login.php';
          window.location.href = destination;
        }, 1200);
      } catch (error) {
        showMessage('Network error. Please try again.', 'error');
      }
    });
  </script>
</body>
</html>
