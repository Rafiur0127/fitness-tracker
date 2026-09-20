<!DOCTYPE html>
<html>
<head>
  <title>Workout Plans</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #e3f2fd, #fce4ec);
      margin: 0;
      padding: 40px;
    }

    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    }

    h2, h3 {
      color: #2e7d32;
    }

    input, select, textarea, button {
      width: 100%;
      padding: 10px;
      margin: 10px 0;
      font-size: 14px;
    }

    .draggable {
      padding: 10px;
      background: #e3f2fd;
      border: 1px solid #90caf9;
      border-radius: 5px;
      cursor: grab;
      margin-bottom: 5px;
    }

    #plans {
      display: flex;
      flex-wrap: wrap;
      gap: 10px;
    }

    #calendar {
      display: flex;
      flex-wrap: wrap;
      gap: 15px;
      margin-top: 30px;
    }

    .day {
      flex: 1 1 calc(33.333% - 20px);
      background: #f0f0f0;
      border: 2px dashed #bbb;
      padding: 10px;
      min-height: 120px;
      border-radius: 8px;
      box-sizing: border-box;
    }

    .day strong {
      display: block;
      margin-bottom: 10px;
      font-size: 16px;
    }

    .dropzone {
      min-height: 60px;
    }

    .dropzone > .draggable {
      background: #c8e6c9;
      border: 1px solid #66bb6a;
    }

    table {
      width: 100%;
      margin-top: 30px;
      border-collapse: collapse;
    }

    th, td {
      padding: 10px;
      border: 1px solid #ccc;
      text-align: center;
    }

    form.inline {
      display: inline;
    }
  </style>
</head>
<body>
<div class="container">
  <h2>🏋️ Workout Plans</h2>

  <h3>➕ Create New Plan</h3>
  <form method="POST">
    <input type="hidden" name="create_plan" value="1">
    <input type="text" name="name" placeholder="Plan Name" required>
    <input type="number" name="weeks" placeholder="Duration in weeks" min="4" max="12" required>
    <textarea name="description" placeholder="Short Description"></textarea>
    <button type="submit">Create Plan</button>
  </form>

  <h3>🗂 Available Plans (Drag one below ⬇️)</h3>
  <div id="plans">
    <?php foreach ($data["programs"] as $p): ?>
      <div class="draggable" draggable="true"
           data-program-id="<?= $p['id'] ?>"
           data-name="<?= htmlspecialchars($p['name']) ?>">
        <?= htmlspecialchars($p['name']) ?> (<?= $p['duration_weeks'] ?>w)
      </div>
    <?php endforeach; ?>
  </div>

  <h3>📅 Weekly Planner (Drop plans here)</h3>
  <div id="calendar">
    <?php foreach (['Mon','Tue','Wed','Thu','Fri','Sat','Sun'] as $day): ?>
      <div class="day" data-day="<?= $day ?>">
        <strong><?= $day ?></strong>
        <div class="dropzone" ondrop="drop(event)" ondragover="allowDrop(event)"></div>
      </div>
    <?php endforeach; ?>
  </div>

  <h3>📆 Full Schedule</h3>
  <table>
    <tr><th>Day</th><th>Activity</th><th>Plan</th><th>Action</th></tr>
    <?php foreach ($data["schedule"] as $s): ?>
      <tr>
        <td><?= $s['day_of_week'] ?></td>
        <td><?= htmlspecialchars($s['activity']) ?></td>
        <td><?= htmlspecialchars($s['program_name']) ?></td>
        <td>
          <form method="POST" class="inline">
            <input type="hidden" name="schedule_id" value="<?= $s['id'] ?>">
            <button type="submit" name="delete_schedule">❌</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<script>
function allowDrop(ev) {
  ev.preventDefault();
}

function drop(ev) {
  ev.preventDefault();
  const data = JSON.parse(ev.dataTransfer.getData("text"));
  const zone = ev.target.closest('.dropzone');

  // Visually add
  const clone = document.createElement('div');
  clone.className = 'draggable';
  clone.innerText = data.name;
  zone.appendChild(clone);

  // Submit to PHP
  const formData = new FormData();
  formData.append("assign_schedule", 1);
  formData.append("program_id", data.id);
  formData.append("activity", data.name);
  formData.append("day", zone.parentElement.dataset.day);

  fetch("", {
    method: "POST",
    body: formData
  }).then(() => console.log("Saved: " + data.name));
}

document.querySelectorAll('.draggable').forEach(elem => {
  elem.addEventListener('dragstart', (ev) => {
    const payload = {
      id: elem.dataset.programId,
      name: elem.dataset.name
    };
    ev.dataTransfer.setData("text", JSON.stringify(payload));
  });
});
</script>

</body>
</html>
