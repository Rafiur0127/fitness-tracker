(function () {
  'use strict';

  const page = window.location.pathname.split('/').pop().toLowerCase();
  const protectedPages = [
    'dashboard.php',
    'workout_logger.php',
    'watertracker.php',
    'goals.php',
    'nutrition.php',
    'workout_plans.php',
    'friend_challenges.php'
  ];

  if (!protectedPages.includes(page)) return;

  const styles = document.createElement('style');
  styles.textContent = `
    .app-shell {
      max-width: 1100px;
      margin: 0 auto 24px;
      padding: 0 16px;
    }
    .app-nav {
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 16px;
      flex-wrap: wrap;
      padding: 14px 18px;
      background: #17324d;
      border-radius: 10px;
      box-shadow: 0 4px 14px rgba(23, 50, 77, 0.18);
    }
    .app-brand {
      color: #fff;
      font-weight: 700;
      text-decoration: none;
      white-space: nowrap;
    }
    .app-links {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      flex-wrap: wrap;
    }
    .app-links a,
    .app-logout {
      color: #e8f1f8;
      background: transparent;
      border: 0;
      border-radius: 6px;
      padding: 7px 9px;
      font: inherit;
      font-size: 0.92rem;
      text-decoration: none;
      cursor: pointer;
    }
    .app-links a:hover,
    .app-links a.active,
    .app-logout:hover {
      color: #17324d;
      background: #fff;
    }
    .app-logout {
      border: 1px solid rgba(255, 255, 255, 0.45);
    }
    @media (max-width: 700px) {
      .app-nav { align-items: stretch; }
      .app-links { justify-content: flex-start; }
    }
  `;
  document.head.appendChild(styles);

  const shell = document.createElement('div');
  shell.className = 'app-shell';
  shell.innerHTML = `
    <nav class="app-nav" aria-label="Main navigation">
      <a class="app-brand" href="./dashboard.php">Fitness Tracker</a>
      <div class="app-links">
        <a data-page="dashboard.php" href="./dashboard.php">Dashboard</a>
        <a data-page="workout_logger.php" href="./workout_logger.php">Workouts</a>
        <a data-page="watertracker.php" href="./watertracker.php">Water</a>
        <a data-page="goals.php" href="./goals.php">Goals</a>
        <a data-page="nutrition.php" href="./nutrition.php">Nutrition</a>
        <a data-page="workout_plans.php" href="./Workout_plans.php">Plans</a>
        <a data-page="friend_challenges.php" href="./friend_challenges.php">Challenges</a>
        <button class="app-logout" type="button">Logout</button>
      </div>
    </nav>
  `;
  document.body.insertBefore(shell, document.body.firstChild);

  const activePage = page === 'workout_plans.php' ? 'workout_plans.php' : page;
  const activeLink = shell.querySelector('[data-page="' + activePage + '"]');
  if (activeLink) {
    activeLink.classList.add('active');
    activeLink.setAttribute('aria-current', 'page');
  }

  let csrfToken = '';
  async function getCsrfToken() {
    if (csrfToken) return csrfToken;
    const response = await fetch('./api/auth.php', {
      credentials: 'same-origin',
      headers: { Accept: 'application/json' }
    });
    const result = await response.json();
    if (!response.ok || !result.success) {
      throw new Error(result.message || 'Unable to initialize security token.');
    }
    csrfToken = result.data.csrf_token;
    return csrfToken;
  }

  shell.querySelector('.app-logout').addEventListener('click', async function () {
    const button = this;
    button.disabled = true;
    try {
      const response = await fetch('./api/auth.php', {
        method: 'POST',
        credentials: 'same-origin',
        headers: {
          'Content-Type': 'application/json',
          Accept: 'application/json',
          'X-CSRF-Token': await getCsrfToken()
        },
        body: JSON.stringify({ action: 'logout' })
      });
      if (!response.ok) {
        throw new Error('Logout failed.');
      }
    } catch (error) {
      button.disabled = false;
      window.alert(error.message || 'Unable to log out. Please try again.');
      return;
    }
    window.location.href = './login.php';
  });
})();
