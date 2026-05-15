<?php
// friend_challenges.php
//session_start();
require_once '../config/config.php';
require_once '../app/controller/challenge_controller.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION['user_id'];
$data = handle_challenges($pdo, $user_id);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Friend Challenges</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #f5faff;
      padding: 40px;
    }
    .container {
      max-width: 900px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 10px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2, h3 {
      color: #2e7d32;
    }
    input, textarea, button, select {
      width: 100%;
      padding: 10px;
      margin: 8px 0;
      font-size: 14px;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    th, td {
      border: 1px solid #ccc;
      padding: 10px;
      text-align: center;
    }
    .inline-btns a {
      margin: 0 5px;
      text-decoration: none;
      color: #1976d2;
      font-weight: bold;
    }
    #popupModal {
      display: none;
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.5);
      z-index: 9999;
    }
    #popupModal .content {
      background: #fff;
      max-width: 600px;
      margin: 80px auto;
      padding: 20px;
      border-radius: 8px;
      position: relative;
    }
    #popupModal button.close {
      position: absolute;
      top: 10px;
      right: 15px;
      font-size: 18px;
    }
  </style>
</head>
<body>

<div class="container">
  <h2>🏆 Friend Challenge Board</h2>

  <h3>Create New Challenge</h3>
  <form method="POST">
    <input type="hidden" name="create_challenge" value="1">
    <input type="text" name="title" placeholder="Challenge Title" required>
    <textarea name="description" placeholder="Challenge Description"></textarea>
    <input type="number" name="target_steps" placeholder="Target Steps" required>
    <input type="date" name="start_date" required>
    <input type="date" name="end_date" required>
    <button type="submit">Create</button>
  </form>

  <h3>Available Challenges</h3>
  <table>
    <tr><th>Title</th><th>Target</th><th>Duration</th><th>Status</th></tr>
    <?php foreach ($data['challenges'] as $c): ?>
      <tr>
        <td><?php echo htmlspecialchars($c['title']); ?></td>
        <td><?php echo $c['target_steps']; ?> steps</td>
        <td><?php echo $c['start_date']; ?> to <?php echo $c['end_date']; ?></td>
        <td>
          <?php if (in_array($c['id'], $data['joined_ids'])): ?>
            ✅ Joined<br>
          <?php else: ?>
            <form method="POST" class="inline">
              <input type="hidden" name="challenge_id" value="<?php echo $c['id']; ?>">
              <button type="submit" name="join_challenge">Join</button>
            </form>
          <?php endif; ?>
          <div class="inline-btns">
            <a href="#" onclick="openModal('leaderboard', <?php echo $c['id']; ?>)">🏑 Leaderboard</a>
            <a href="#" onclick="openModal('cheer', <?php echo $c['id']; ?>)">💬 Cheer</a>
          </div>
        </td>
      </tr>
    <?php endforeach; ?>
  </table>
</div>

<div id="popupModal">
  <div class="content">
    <button class="close" onclick="closeModal()">❌</button>
    <div id="modalContent">Loading...</div>
  </div>
</div>

<script>
function openModal(type, challengeId) {
  const modal = document.getElementById('popupModal');
  const content = document.getElementById('modalContent');
  modal.style.display = 'block';
  content.innerHTML = 'Loading...';

  fetch(`ajax_challenge.php?type=${type}&challenge_id=${challengeId}`)
    .then(res => res.text())
    .then(html => content.innerHTML = html);
}

function closeModal() {
  document.getElementById('popupModal').style.display = 'none';
}

function sendCheer(e) {
  e.preventDefault();
  const form = e.target;
  const formData = new FormData(form);

  fetch("cheer.php", {
    method: "POST",
    body: formData
  }).then(() => {
    alert("Cheer sent! 🎉");
    closeModal();
  });
  return false;
}
</script>

</body>
</html>
