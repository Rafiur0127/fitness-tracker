

<!DOCTYPE html>
<html>
<head>
  <title>Dashboard</title>
  <style>
    body { font-family: Arial; background: #f4f7fa; margin: 0; padding: 0; }
    .wrapper { display: flex; min-height: 100vh; }
    .sidebar {
      width: 30%;
      background: #ffffff;
      padding: 30px;
      box-shadow: 2px 0 8px rgba(0,0,0,0.05);
    }
    .sidebar h2 { margin-top: 0; color: #2e7d32; }
    .nav { display: flex; flex-direction: column; gap: 10px; margin-top: 20px; }
    .nav a {
      text-decoration: none;
      background: #e3f2fd;
      padding: 12px;
      text-align: center;
      border-radius: 6px;
      color: #0d47a1;
      font-weight: bold;
      transition: 0.3s;
    }
    .nav a:hover { background: #bbdefb; }
    .main-content {
      width: 70%;
      padding: 30px;
      overflow-y: auto;
      background: #fefefe;
    }
    .section { margin-bottom: 30px; }
    canvas { max-width: 100%; height: 200px; }
    .comparison {
      display: flex;
      justify-content: space-between;
      background: #e8f5e9;
      padding: 10px 20px;
      border-radius: 8px;
      font-size: 14px;
      flex-wrap: wrap;
      gap: 10px;
    }
    .achievements {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
      margin-top: 10px;
    }
    .trophy {
      background: #fff3e0;
      border: 1px solid #ffb74d;
      padding: 10px;
      border-radius: 8px;
      flex: 1 1 45%;
      text-align: center;
    }
  </style>
</head>
<body>

<div class="wrapper">
  <div class="sidebar">
    <h2>👋 Hi, <?= htmlspecialchars($dashboard["name"]) ?></h2>
    <p>Select a module:</p>
    <div class="nav">
      <a href="workout_logger.php">🏋️ Workout Logger</a>
      <a href="water_tracker.php">💧 Water Tracker</a>
      <a href="goals.php">🎯 Goal Manager</a>
      <a href="workout_plans.php">📅 Workout Plans</a>
      <a href="friend_challenges.php">👥 Friend Challenges</a>
      <a href="logout.php">🚪 Logout</a>
    </div>
  </div>

  <div class="main-content">
    <div class="section">
      <h3>📊 Lifetime Summary</h3>
      <div class="comparison">
        <div>🏋️ Total Workouts: <strong><?= $dashboard["stats"]["total_workouts"] ?></strong></div>
        <div>⏱️ Total Minutes: <strong><?= $dashboard["stats"]["total_minutes"] ?></strong></div>
        <div>💧 Total Water: <strong><?= round($dashboard["stats"]["total_water_ml"] / 1000, 1) ?> L</strong></div>
        <div>🎯 Goals Completed: <strong><?= $dashboard["stats"]["goals_completed"] ?></strong></div>
      </div>
    </div>

    <div class="section">
      <h3>🏆 Achievement Gallery</h3>
      <div class="achievements">
        <?php if (count($dashboard["achievements"]) > 0): ?>
          <?php foreach ($dashboard["achievements"] as $trophy): ?>
            <div class="trophy"><?= $trophy ?></div>
          <?php endforeach; ?>
        <?php else: ?>
          <div class="trophy">⏳ Keep going to unlock achievements!</div>
        <?php endif; ?>
      </div>
    </div>

    <div class="section">
      <h3>📈 Weekly Strength & Cardio</h3>
      <canvas id="progressChart"></canvas>
    </div>

    <div class="section">
      <h3>📆 4-Week Progress Overview</h3>
      <canvas id="monthlyChart"></canvas>
    </div>
  </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
const ctx = document.getElementById('progressChart').getContext('2d');
const days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
const strengthData = [
  <?= implode(',', array_map(fn($d) => $dashboard['workout'][$d]['strength'], $days)) ?>
];
const cardioData = [
  <?= implode(',', array_map(fn($d) => $dashboard['workout'][$d]['cardio'], $days)) ?>
];

new Chart(ctx, {
  type: 'line',
  data: {
    labels: days,
    datasets: [
      {
        label: 'Strength (mins)',
        data: strengthData,
        borderColor: '#43a047',
        backgroundColor: 'rgba(67, 160, 71, 0.1)',
        tension: 0.4
      },
      {
        label: 'Cardio (mins)',
        data: cardioData,
        borderColor: '#1e88e5',
        backgroundColor: 'rgba(30, 136, 229, 0.1)',
        tension: 0.4
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>

<script>
const trendCtx = document.getElementById('monthlyChart').getContext('2d');
const weekLabels = <?= json_encode(array_keys($dashboard["monthly_trends"])) ?>;
const strengthSeries = <?= json_encode(array_map(fn($w) => $w['strength'], $dashboard["monthly_trends"])) ?>;
const cardioSeries = <?= json_encode(array_map(fn($w) => $w['cardio'], $dashboard["monthly_trends"])) ?>;

new Chart(trendCtx, {
  type: 'bar',
  data: {
    labels: weekLabels,
    datasets: [
      {
        label: 'Strength (mins)',
        data: strengthSeries,
        backgroundColor: '#81c784'
      },
      {
        label: 'Cardio (mins)',
        data: cardioSeries,
        backgroundColor: '#64b5f6'
      }
    ]
  },
  options: {
    responsive: true,
    plugins: { legend: { position: 'top' } },
    scales: { y: { beginAtZero: true } }
  }
});
</script>

</body>
</html>
