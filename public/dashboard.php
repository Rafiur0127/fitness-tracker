<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Dashboard – Fitness Tracker</title>
  <style>
    body { font-family: Arial, sans-serif; background:#f7f9fb; margin:0; padding:24px; }
    .container { max-width:1100px; margin:0 auto; }
    .top { display:flex; justify-content:space-between; align-items:center; }
    .card { background:#fff; padding:16px; border-radius:8px; box-shadow:0 6px 18px rgba(33,41,51,0.06); }
    .stats { display:flex; gap:12px; margin-top:16px; }
    .stat { flex:1; padding:12px; text-align:center; }
    h1 { margin:0 0 8px 0; }
    table { width:100%; border-collapse:collapse; margin-top:12px; }
    th, td { padding:8px 10px; text-align:left; border-bottom:1px solid #eef2f6; }
    .trophies { margin-top:12px; }
  </style>
</head>
<body>
  <div class="container">
    <div class="top">
      <div>
        <h1 id="greeting">Dashboard</h1>
        <div id="sub" style="color:#666;">Loading...</div>
      </div>
      <div>
        <a href="login.php" id="logoutLink">Logout</a>
      </div>
    </div>

    <div class="stats" id="statsRow">
      <!-- stats cards inserted here -->
    </div>

    <div style="display:flex; gap:16px; margin-top:18px;">
      <div style="flex:2;" class="card">
        <h3>Recent Workouts</h3>
        <table id="workoutsTable">
          <thead><tr><th>Date</th><th>Type</th><th>Duration (min)</th></tr></thead>
          <tbody><tr><td colspan="3">Loading...</td></tr></tbody>
        </table>
      </div>

      <div style="flex:1;" class="card">
        <h3>Today</h3>
        <div style="margin-top:8px;">
          <strong>Water intake:</strong>
          <div id="waterToday">Loading...</div>
        </div>

        <div class="trophies card" style="margin-top:12px; background:transparent; box-shadow:none; padding:0;">
          <h4>Trophies</h4>
          <ul id="trophiesList"><li>Loading...</li></ul>
        </div>
      </div>
    </div>
  </div>

<script>
async function loadDashboard() {
  try {
    const resp = await fetch('./api/dashboard.php', { method: 'GET', credentials: 'same-origin', headers: { 'Accept': 'application/json' } });

    if (resp.status === 401) {
      window.location.href = './login.php';
      return;
    }

    const payload = await resp.json();
    if (!resp.ok || payload.success === false) {
      document.getElementById('sub').textContent = payload.message || 'Unable to load dashboard.';
      return;
    }

    const data = payload.data;
    document.getElementById('greeting').textContent = `Welcome, ${data.user.name.split(' ')[0] || 'User'}`;
    document.getElementById('sub').textContent = `Member ID ${data.user.id}`;

    const statsRow = document.getElementById('statsRow');
    statsRow.innerHTML = '';
    const stats = data.stats || {};
    const statItems = [
      {label: 'Workouts', value: stats.total_workouts || 0},
      {label: 'Minutes', value: stats.total_minutes || 0},
      {label: 'Water (ml)', value: stats.total_water_ml || 0},
      {label: 'Goals Done', value: stats.goals_completed || 0}
    ];

    for (const s of statItems) {
      const el = document.createElement('div');
      el.className = 'card stat';
      el.innerHTML = `<div style="font-size:18px; font-weight:700;">${s.value}</div><div style="color:#666;">${s.label}</div>`;
      statsRow.appendChild(el);
    }

    // Recent workouts
    const tbody = document.querySelector('#workoutsTable tbody');
    tbody.innerHTML = '';
    const recent = data.recent_workouts || [];
    if (recent.length === 0) {
      tbody.innerHTML = '<tr><td colspan="3">No workouts yet.</td></tr>';
    } else {
      for (const r of recent) {
        const tr = document.createElement('tr');
        const date = document.createElement('td'); date.textContent = r.date;
        const type = document.createElement('td'); type.textContent = r.type;
        const dur = document.createElement('td'); dur.textContent = r.duration;
        tr.appendChild(date); tr.appendChild(type); tr.appendChild(dur);
        tbody.appendChild(tr);
      }
    }

    // Water today
    document.getElementById('waterToday').textContent = (data.today_water_ml || 0) + ' ml';

    // Trophies
    const tlist = document.getElementById('trophiesList');
    tlist.innerHTML = '';
    const trophies = data.trophies || [];
    if (trophies.length === 0) {
      tlist.innerHTML = '<li>No trophies yet.</li>';
    } else {
      for (const t of trophies) {
        const li = document.createElement('li'); li.textContent = t; tlist.appendChild(li);
      }
    }

  } catch (err) {
    document.getElementById('sub').textContent = 'Network error while loading dashboard.';
  }
}

loadDashboard();
</script>
</body>
</html>
