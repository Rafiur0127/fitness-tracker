<!DOCTYPE html>
<html>
<head>
  <title>Goals</title>
  <style>
    body { font-family: Arial; background: #f0f4c3; padding: 40px; }
    .container { max-width: 700px; margin: auto; background: white; padding: 30px; border-radius: 10px; }
    input, button { padding: 8px; width: 100%; margin-top: 10px; }
    table { width: 100%; margin-top: 20px; border-collapse: collapse; }
    th, td { padding: 8px; border: 1px solid #ccc; text-align: center; }
    form.inline { display: inline-block; }
    .completed { background: #dcedc8; }
    .message { background: #e8f5e9; padding: 10px; border-radius: 5px; margin-bottom: 15px; }
  </style>
</head>
<body>

<div class="container">
  <h2>🎯 Set a Goal</h2>

  <?php if ($message): ?>
    <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>

  <form method="POST">
    <input type="hidden" name="add_goal" value="1">
    <input type="text" name="title" placeholder="Goal title (e.g. Run 10km)" required>
    <input type="number" name="target" placeholder="Target Value (e.g. 10000 steps)" required min="1">
    <button type="submit">➕ Add Goal</button>
  </form>

  <h3>📋 Your Goals</h3>
  <table>
    <tr><th>Title</th><th>Progress</th><th>Target</th><th>Status</th><th>Update</th></tr>
    <?php foreach ($goals as $g): ?>
      <tr class="<?= $g['status'] === 'completed' ? 'completed' : '' ?>">
        <td><?= htmlspecialchars($g['title']) ?></td>
        <td><?= $g['current_value'] ?></td>
        <td><?= $g['target_value'] ?></td>
        <td><?= $g['status'] === 'completed' ? '🏆 Completed' : '⏳ Ongoing' ?></td>
        <td>
          <?php if ($g['status'] === 'ongoing'): ?>
            <form method="POST" class="inline">
              <input type="hidden" name="goal_id" value="<?= $g['id'] ?>">
              <input type="number" name="progress" placeholder="+Progress" required min="1">
              <button type="submit" name="update_progress">➕</button>
            </form>
          <?php else: ?>✔️<?php endif; ?>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

</body>
</html>
