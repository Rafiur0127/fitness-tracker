<?php

function get_user_name($pdo, $user_id) {
  $stmt = $pdo->prepare("SELECT CONCAT_WS(' ', first_name, last_name) AS name FROM users WHERE id = ?");
  $stmt->execute([$user_id]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  return $row ? $row["name"] : "User";
}

function get_lifetime_stats($pdo, $user_id) {
  $stmt = $pdo->prepare("SELECT COUNT(*) AS workout_count, COALESCE(SUM(duration), 0) AS workout_minutes FROM workout_sessions WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $workout = $stmt->fetch();

  $stmt = $pdo->prepare("SELECT COALESCE(SUM(amount_ml), 0) AS water_ml FROM water_logs WHERE user_id = ?");
  $stmt->execute([$user_id]);
  $water = $stmt->fetch();

  $stmt = $pdo->prepare("SELECT COUNT(*) AS goals_done FROM goals WHERE user_id = ? AND status = 'completed'");
  $stmt->execute([$user_id]);
  $goals = $stmt->fetch();

  return [
    "total_workouts" => $workout["workout_count"] ?? 0,
    "total_minutes" => $workout["workout_minutes"] ?? 0,
    "total_water_ml" => $water["water_ml"] ?? 0,
    "goals_completed" => $goals["goals_done"] ?? 0,
  ];
}

function get_user_achievements_from_totals($stats) {
  $trophies = [];
  if ($stats['total_workouts'] >= 10) $trophies[] = "🏋️‍♂️ Logged 10+ Workouts";
  if ($stats['total_minutes'] >= 500) $trophies[] = "⏱️ 500+ Mins Exercised";
  if ($stats['total_water_ml'] >= 10000) $trophies[] = "💧 Drank 10L+ Water";
  if ($stats['goals_completed'] >= 3) $trophies[] = "🎯 3+ Goals Crushed";
  return $trophies;
}

function get_weekly_workout_minutes($pdo, $user_id, $weekOffset = 0) {
  $stmt = $pdo->prepare("
    SELECT DATE_FORMAT(date, '%a') AS day, type, SUM(duration) AS total
    FROM workout_sessions
    WHERE user_id = ? AND WEEK(date) = WEEK(CURDATE()) - ?
    GROUP BY day, type
  ");
  $stmt->execute([$user_id, $weekOffset]);

  $days = ['Mon','Tue','Wed','Thu','Fri','Sat','Sun'];
  $data = [];
  foreach ($days as $day) {
    $data[$day] = ['strength' => 0, 'cardio' => 0];
  }

  foreach ($stmt->fetchAll() as $row) {
    $day = $row['day'];
    $type = strtolower($row['type']);
    if (isset($data[$day][$type])) {
      $data[$day][$type] = (int) $row['total'];
    }
  }

  return $data;
}

function get_monthly_workout_trends($pdo, $user_id) {
  $stmt = $pdo->prepare("
    SELECT WEEK(date) AS week_num, type, SUM(duration) AS total
    FROM workout_sessions
    WHERE user_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
    GROUP BY week_num, type
    ORDER BY week_num ASC
  ");
  $stmt->execute([$user_id]);

  $weeks = [];
  foreach ($stmt->fetchAll() as $row) {
    $week = "Week " . $row['week_num'];
    $type = strtolower($row['type']);
    if (!isset($weeks[$week])) {
      $weeks[$week] = ["strength" => 0, "cardio" => 0];
    }
    if (isset($weeks[$week][$type])) {
      $weeks[$week][$type] = (int) $row["total"];
    }
  }

  return $weeks;
}
