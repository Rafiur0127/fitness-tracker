<h3>🏁 Leaderboard</h3>
<table>
  <tr><th>Rank</th><th>User</th><th>Steps</th></tr>
  <?php $rank = 1; foreach ($data as $row): ?>
    <tr>
      <td><?= $rank++ ?></td>
      <td><?= htmlspecialchars($row['username']) ?></td>
      <td><?= $row['current_steps'] ?></td>
    </tr>
  <?php endforeach; ?>
</table>
