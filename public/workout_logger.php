<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Workout Logger</title>
  <style>
    body { font-family: Arial, sans-serif; background: #e3f2fd; padding: 40px; }
    .container { max-width: 600px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
    input, select, button { width: 100%; padding: 10px; margin-top: 10px; box-sizing: border-box; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { border: 1px solid #ccc; padding: 8px; text-align: center; }
    #message { margin-top: 10px; padding: 10px; border-radius: 5px; }
  </style>
</head>
<body>
<div class="container">
  <h2>Log Your Workout</h2>
  <div id="message"></div>
  <form id="workoutForm">
    <label for="type">Workout Type</label>
    <select id="type" required>
      <option value="">-- Select Type --</option>
      <option value="strength">Strength</option>
      <option value="cardio">Cardio</option>
      <option value="custom">Custom</option>
    </select>
    <input type="text" id="customType" placeholder="Enter custom workout" hidden>
    <input type="number" id="duration" placeholder="Duration (minutes)" min="1" required>
    <button type="submit">Log Workout</button>
  </form>

  <h3>Recent Workouts</h3>
  <table>
    <thead><tr><th>Type</th><th>Duration</th><th>Date</th></tr></thead>
    <tbody id="workouts"></tbody>
  </table>
</div>
<script>
const form = document.getElementById('workoutForm');
const type = document.getElementById('type');
const customType = document.getElementById('customType');
const message = document.getElementById('message');
let csrfToken = '';
async function getCsrfToken() {
  if (csrfToken) return csrfToken;
  const response = await fetch('./api/auth.php', { credentials: 'same-origin' });
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message || 'Unable to initialize security token.');
  csrfToken = result.data.csrf_token;
  return csrfToken;
}
type.addEventListener('change', () => {
  customType.hidden = type.value !== 'custom';
  customType.required = type.value === 'custom';
});
function showMessage(text, error = false) {
  message.textContent = text;
  message.style.background = error ? '#ffe6e6' : '#d0f0c0';
}
function renderWorkouts(items) {
  const tbody = document.getElementById('workouts');
  tbody.innerHTML = '';
  if (!items.length) {
    tbody.innerHTML = '<tr><td colspan="3">No workouts yet.</td></tr>';
    return;
  }
  items.forEach((item) => {
    const row = document.createElement('tr');
    [item.type, `${item.duration} min`, item.date].forEach((value) => {
      const cell = document.createElement('td');
      cell.textContent = value;
      row.appendChild(cell);
    });
    tbody.appendChild(row);
  });
}
async function loadWorkouts() {
  const response = await fetch('./api/workouts.php', { credentials: 'same-origin' });
  if (response.status === 401) { window.location.href = './login.php'; return; }
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message);
  renderWorkouts(result.data.workouts);
}
form.addEventListener('submit', async (event) => {
  event.preventDefault();
  const workoutType = type.value === 'custom' ? customType.value.trim() : type.value;
  try {
    const response = await fetch('./api/workouts.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': await getCsrfToken() },
      body: JSON.stringify({ type: workoutType, duration: Number(document.getElementById('duration').value) })
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message);
    showMessage(result.message);
    form.reset();
    customType.hidden = true;
    customType.required = false;
    await loadWorkouts();
  } catch (error) { showMessage(error.message || 'Unable to save workout.', true); }
});
loadWorkouts().catch((error) => showMessage(error.message || 'Unable to load workouts.', true));
</script>
</body>
</html>
