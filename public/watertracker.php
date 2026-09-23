<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Water Tracker</title>
  <style>
    body { font-family: sans-serif; text-align: center; margin-top: 50px; }
    .tracker { padding: 20px; background: #e0f7fa; border-radius: 10px; display: inline-block; }
    input, button { padding: 8px; margin-top: 10px; }
    #message { margin-top: 12px; }
  </style>
</head>
<body>
  <div class="tracker">
    <h2>Water Intake Tracker</h2>
    <form id="waterForm">
      <label for="amount">Water Drank (ml): </label>
      <input id="amount" type="number" required min="50" max="2000" step="50">
      <br>
      <button type="submit">Log</button>
    </form>
    <p id="total"><strong>Total today:</strong> Loading...</p>
    <p id="message"></p>
  </div>
<script>
const form = document.getElementById('waterForm');
const total = document.getElementById('total');
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
async function loadWater() {
  const response = await fetch('./api/water.php', { credentials: 'same-origin' });
  if (response.status === 401) { window.location.href = './login.php'; return; }
  const result = await response.json();
  if (!response.ok || !result.success) throw new Error(result.message);
  total.textContent = `Total today: ${result.data.today_ml} ml`;
}
form.addEventListener('submit', async (event) => {
  event.preventDefault();
  try {
    const response = await fetch('./api/water.php', {
      method: 'POST',
      credentials: 'same-origin',
      headers: { 'Content-Type': 'application/json', 'Accept': 'application/json', 'X-CSRF-Token': await getCsrfToken() },
      body: JSON.stringify({ amount_ml: Number(document.getElementById('amount').value) })
    });
    const result = await response.json();
    if (!response.ok || !result.success) throw new Error(result.message);
    message.textContent = result.message;
    form.reset();
    await loadWater();
  } catch (error) { message.textContent = error.message || 'Unable to save water intake.'; }
});
loadWater().catch((error) => { message.textContent = error.message || 'Unable to load water intake.'; });
</script>
</body>
</html>
