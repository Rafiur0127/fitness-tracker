<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Workout Plans</title>
  <style>
    body { font-family: Arial, sans-serif; background: #eef7ff; padding: 30px; }
    .container { max-width: 900px; margin: auto; background: #fff; padding: 25px; border-radius: 10px; }
    input, textarea, button { width: 100%; padding: 9px; margin: 6px 0; box-sizing: border-box; }
    #plans, #calendar { display: flex; flex-wrap: wrap; gap: 10px; }
    .plan, .day { padding: 12px; border-radius: 7px; }
    .plan { background: #e3f2fd; cursor: grab; }
    .day { flex: 1 1 22%; min-height: 90px; background: #f4f4f4; border: 2px dashed #bbb; }
    .dropzone { min-height: 55px; }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; }
    th, td { border: 1px solid #ddd; padding: 8px; text-align: center; }
    #message { padding: 8px; margin: 8px 0; }
  </style>
</head>
<body>
<div class="container">
  <h2>Workout Plans</h2>
  <div id="message"></div>
  <form id="planForm">
    <input id="name" placeholder="Plan name" required>
    <input id="weeks" type="number" min="4" max="12" placeholder="Duration in weeks" required>
    <textarea id="description" placeholder="Description"></textarea>
    <button type="submit">Create Plan</button>
  </form>
  <h3>Available Plans</h3>
  <div id="plans"></div>
  <h3>Weekly Planner</h3>
  <div id="calendar"></div>
  <h3>Full Schedule</h3>
  <table>
    <thead><tr><th>Day</th><th>Activity</th><th>Plan</th><th>Action</th></tr></thead>
    <tbody id="schedule"></tbody>
  </table>
</div>
<script src="./assets/app-shell.js"></script>
<script>
let csrfToken = '';
let plans = [];
const days = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
const message = document.getElementById('message');
async function getCsrfToken() {
  if (csrfToken) return csrfToken;
  const response = await fetch('./api/auth.php', { credentials: 'same-origin' });
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message);
  csrfToken = result.data.csrf_token;
  return csrfToken;
}
function showMessage(text, error = false) {
  message.textContent = text; message.style.background = error ? '#ffe6e6' : '#e8f5e9';
}
async function request(body) {
  const response = await fetch('./api/plans.php', {
    method: 'POST', credentials: 'same-origin',
    headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': await getCsrfToken() },
    body: JSON.stringify(body)
  });
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message);
  return result;
}
function render(data) {
  plans = data.programs || [];
  const planBox = document.getElementById('plans'); planBox.innerHTML = '';
  plans.forEach((plan) => {
    const item = document.createElement('div');
    item.className = 'plan'; item.draggable = true;
    item.textContent = `${plan.name} (${plan.duration_weeks} weeks)`;
    item.addEventListener('dragstart', (event) => event.dataTransfer.setData('text/plain', plan.id));
    planBox.appendChild(item);
  });
  const calendar = document.getElementById('calendar'); calendar.innerHTML = '';
  days.forEach((day) => {
    const box = document.createElement('div'); box.className = 'day';
    box.innerHTML = `<strong>${day}</strong><div class="dropzone"></div>`;
    const zone = box.querySelector('.dropzone');
    zone.addEventListener('dragover', (event) => event.preventDefault());
    zone.addEventListener('drop', async (event) => {
      event.preventDefault();
      try { await request({ action: 'assign', program_id: Number(event.dataTransfer.getData('text/plain')), day }); await load(); }
      catch (error) { showMessage(error.message, true); }
    });
    calendar.appendChild(box);
  });
  const schedule = document.getElementById('schedule'); schedule.innerHTML = '';
  (data.schedule || []).forEach((entry) => {
    const row = document.createElement('tr');
    [entry.day_of_week, entry.activity, entry.program_name].forEach((value) => {
      const cell = document.createElement('td'); cell.textContent = value; row.appendChild(cell);
    });
    const action = document.createElement('td'); const button = document.createElement('button');
    button.textContent = 'Delete'; button.addEventListener('click', async () => {
      try { await request({ action: 'delete', schedule_id: Number(entry.id) }); await load(); }
      catch (error) { showMessage(error.message, true); }
    });
    action.appendChild(button); row.appendChild(action); schedule.appendChild(row);
  });
}
async function load() {
  const response = await fetch('./api/plans.php', { credentials: 'same-origin' });
  if (response.status === 401) { window.location.href = './login.php?next=' + encodeURIComponent('Workout_plans.php'); return; }
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message);
  render(result.data);
}
document.getElementById('planForm').addEventListener('submit', async (event) => {
  event.preventDefault();
  try {
    await request({
      action: 'create',
      name: document.getElementById('name').value.trim(),
      weeks: Number(document.getElementById('weeks').value),
      description: document.getElementById('description').value.trim()
    });
    showMessage('Workout plan created.'); event.target.reset(); await load();
  } catch (error) { showMessage(error.message, true); }
});
load().catch((error) => showMessage(error.message || 'Unable to load plans.', true));
</script>
</body>
</html>
