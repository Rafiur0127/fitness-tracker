<?php
require_once __DIR__ . '/../model/workout_logger_model.php';

$log_msg = "";
$recent_workouts = get_recent_workouts($pdo, $user_id);

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["log_workout"])) {
  $duration = (int) $_POST["duration"];
  $type = '';

  // Handle custom or predefined type
  if ($_POST["type_select"] === "custom") {
    $type = trim($_POST["custom_type"]);
  } else {
    $type = $_POST["type_select"];
  }

  // Validate and log
  if ($type !== "" && $duration > 0) {
    if (log_workout($pdo, $user_id, $type, $duration)) {
      $log_msg = "✅ Workout logged: " . htmlspecialchars($type) . " ($duration min)";
      $recent_workouts = get_recent_workouts($pdo, $user_id); // refresh list
    } else {
      $log_msg = "❌ Failed to log workout.";
    }
  } else {
    $log_msg = "❌ Please provide valid workout type and duration.";
  }
}
