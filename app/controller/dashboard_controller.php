<?php
require_once __DIR__ . '/../model/dashboard_model.php';

function get_dashboard_data($pdo, $user_id) {
  $user_name = get_user_name($pdo, $user_id);
  $stats = get_lifetime_stats($pdo, $user_id);
  $trophies = get_user_achievements_from_totals($stats);
  $this_week = get_weekly_workout_minutes($pdo, $user_id);         // ✅ Weekly chart
  $monthly_trends = get_monthly_workout_trends($pdo, $user_id);    // ✅ 4-week trend

  return [
    "name" => $user_name,
    "stats" => $stats,
    "achievements" => $trophies,
    "workout" => $this_week,
    "monthly_trends" => $monthly_trends
  ];
}
