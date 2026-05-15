<?php
session_start();
require_once '../config/config.php';
require_once '../app/controller/challenge_controller.php';

if (!isset($_SESSION['user_id'])) {
  http_response_code(403);
  exit("Login required");
}

$cid = $_GET['challenge_id'] ?? 0;
$type = $_GET['type'] ?? '';

if ($type === 'leaderboard') {
  $data = get_leaderboard_data($pdo, $cid);
  include '../app/view/leaderboard_partial.php';
} elseif ($type === 'cheer') {
  $data = handle_cheer_interface($pdo, $_SESSION['user_id'], $cid);
  include '../app/view/cheer_partial.php';
} else {
  echo "Invalid request.";
}
