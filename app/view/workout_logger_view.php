<!DOCTYPE html>
<html>
<head>
  <title>Workout Logger</title>
  <style>
    body { font-family: Arial; background: #e3f2fd; padding: 40px; }
    .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
    input, select, button { width: 100%; padding: 10px; margin-top: 10px; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    .msg { background: #d0f0c0; padding: 10px; border-radius: 5px; color: #2e7d32; margin-top: 10px; }
  </style>
</head>
<body>

<div class="container">
  <h2>🏋️ Log Your Workout</h2>

  <?php if (!empty($log_msg)): ?>
    <div class="msg"><?= htmlspecialchars($log_msg) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="log_workout" value="1">

    <label for="type_select">Workout Type</label>
    <select name="type_select" id="type_select" onchange="toggleCustomInput()" required>
      <option value="">-- Select Type --</option>
      <option value="strength">Strength</option>
      <option value="cardio">Cardio</option>
      <option value="custom">Custom</option>
    </select>

    <input type="text" name="custom_type" id="custom_type" placeholder="Enter custom workout" style="display: none;">

    <input type="number" name="duration" placeholder="Duration (in minutes)" required min="5">
    <button type="submit">Log Workout</button>
  </form>

  <h3>🕒 Recent Workouts</h3>
  <table>
    <tr><th>Type</th><th>Duration</th><th>Date</th></tr>
    <?php foreach ($recent_workouts as $w): ?>
      <tr>
        <td><?= htmlspecialchars($w["type"]) ?></td>
        <td><?= htmlspecialchars($w["duration"]) ?> min</td>
        <td><?= htmlspecialchars($w["date"]) ?></td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<script>
function toggleCustomInput() {
  const select = document.getElementById('type_select');
  const custom = document.getElementById('custom_type');
  custom.style.display = (select.value === 'custom') ? 'block' : 'none';
}
</script>

</body>
</html>
