(function () {
  'use strict';

  const pages = {
    'dashboard.html': 'dashboard.html',
    'workout_logger.html': 'workout_logger.html',
    'watertracker.html': 'watertracker.html',
    'goals.html': 'goals.html',
    'nutrition.html': 'nutrition.html',
    'workout_plans.html': 'workout_plans.html',
    'friend_challenges.html': 'friend_challenges.html'
  };
  const currentPage = window.location.pathname.split('/').pop().toLowerCase();
  let csrfToken = '';

  async function getCsrfToken() {
    if (csrfToken) return csrfToken;
    const response = await fetch('/api/auth.php', {
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

  async function apiFetch(path, options) {
    const request = options || {};
    request.credentials = 'same-origin';
    request.headers = Object.assign({ Accept: 'application/json' }, request.headers || {});
    if (request.body && !request.headers['Content-Type']) {
      request.headers['Content-Type'] = 'application/json';
    }
    const response = await fetch(path, request);
    if (response.status === 401) {
      const next = currentPage || 'dashboard.html';
      window.location.assign('/login.html?next=' + encodeURIComponent(next));
      return null;
    }
    return response;
  }

  function redirectToLogin() {
    const next = currentPage || 'dashboard.html';
    window.location.assign('/login.html?next=' + encodeURIComponent(next));
  }

  window.FitnessApp = { apiFetch, getCsrfToken, redirectToLogin };

  if (!pages[currentPage]) return;

  const styles = document.createElement('style');
  styles.textContent = `
    .app-shell { max-width:1100px; margin:0 auto 24px; padding:0 16px; }
    .app-nav { display:flex; align-items:center; justify-content:space-between; gap:16px;
      flex-wrap:wrap; padding:14px 18px; background:#17324d; border-radius:10px;
      box-shadow:0 4px 14px rgba(23,50,77,.18); }
    .app-brand { color:#fff; font-weight:700; text-decoration:none; white-space:nowrap; }
    .app-links { display:flex; align-items:center; justify-content:center; gap:8px; flex-wrap:wrap; }
    .app-links a, .app-logout { color:#e8f1f8; background:transparent; border:0; border-radius:6px;
      padding:7px 9px; font:inherit; font-size:.92rem; text-decoration:none; cursor:pointer; }
    .app-links a:hover, .app-links a.active, .app-logout:hover { color:#17324d; background:#fff; }
    .app-logout { border:1px solid rgba(255,255,255,.45); }
    @media (max-width:700px) { .app-nav { align-items:stretch; } .app-links { justify-content:flex-start; } }
  `;
  document.head.appendChild(styles);

  const shell = document.createElement('div');
  shell.className = 'app-shell';
  shell.innerHTML = `
    <nav class="app-nav" aria-label="Main navigation">
      <a class="app-brand" href="/dashboard.html">Fitness Tracker</a>
      <div class="app-links">
        <a data-page="dashboard.html" href="/dashboard.html">Dashboard</a>
        <a data-page="workout_logger.html" href="/workout_logger.html">Workouts</a>
        <a data-page="watertracker.html" href="/watertracker.html">Water</a>
        <a data-page="goals.html" href="/goals.html">Goals</a>
        <a data-page="nutrition.html" href="/nutrition.html">Nutrition</a>
        <a data-page="workout_plans.html" href="/workout_plans.html">Plans</a>
        <a data-page="friend_challenges.html" href="/friend_challenges.html">Challenges</a>
        <button class="app-logout" type="button">Logout</button>
      </div>
    </nav>`;
  document.body.insertBefore(shell, document.body.firstChild);
  const active = shell.querySelector('[data-page="' + currentPage + '"]');
  if (active) { active.classList.add('active'); active.setAttribute('aria-current', 'page'); }

  shell.querySelector('.app-logout').addEventListener('click', async function () {
    this.disabled = true;
    try {
      const response = await fetch('/api/auth.php', {
        method: 'POST', credentials: 'same-origin',
        headers: { 'Content-Type': 'application/json', Accept: 'application/json', 'X-CSRF-Token': await getCsrfToken() },
        body: JSON.stringify({ action: 'logout' })
      });
      if (!response.ok) throw new Error('Logout failed.');
      window.location.assign('/login.html');
    } catch (error) {
      this.disabled = false;
      window.alert(error.message || 'Unable to log out. Please try again.');
    }
  });
})();
