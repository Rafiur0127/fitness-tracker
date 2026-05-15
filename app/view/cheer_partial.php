<h3>🎉 Cheer a Friend</h3>
<form method="POST" onsubmit="return sendCheer(event)">
  <select name="to_user_id">
    <?php foreach ($data['participants'] as $p): ?>
      <?php if ($p['user_id'] != $_SESSION['user_id']): ?>
        <option value="<?= $p['user_id'] ?>"><?= htmlspecialchars($p['username']) ?></option>
      <?php endif; ?>
    <?php endforeach; ?>
  </select>
  <textarea name="message" placeholder="🔥 Great job! Keep going!"></textarea>
  <input type="hidden" name="challenge_id" value="<?= $_GET['challenge_id'] ?>">
  <button type="submit">Send</button>
</form>

<div>
  <h4>📣 Cheers</h4>
  <?php foreach ($data['cheers'] as $cheer): ?>
    <p><strong><?= htmlspecialchars($cheer['from_name']) ?> ➤ <?= htmlspecialchars($cheer['to_name']) ?>:</strong>
    <?= htmlspecialchars($cheer['message']) ?> <small>(<?= $cheer['sent_at'] ?>)</small></p>
  <?php endforeach; ?>
</div>
