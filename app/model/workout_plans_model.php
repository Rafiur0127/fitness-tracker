<?php
function create_plan($pdo, $name, $weeks, $desc) {
    $stmt = $pdo->prepare("INSERT INTO workout_programs (name, duration_weeks, description) VALUES (?, ?, ?)");
    $stmt->execute([$name, $weeks, $desc]);
}

function assign_schedule($pdo, $user_id, $program_id, $day, $activity) {
    $stmt = $pdo->prepare("INSERT INTO user_schedule (user_id, program_id, day_of_week, activity) VALUES (?, ?, ?, ?)");
    $stmt->execute([$user_id, $program_id, $day, $activity]);
}

function delete_schedule($pdo, $user_id, $schedule_id) {
    $stmt = $pdo->prepare("DELETE FROM user_schedule WHERE id = ? AND user_id = ?");
    $stmt->execute([$schedule_id, $user_id]);
}

function get_all_programs($pdo) {
    return $pdo->query("SELECT * FROM workout_programs")->fetchAll();
}

function get_user_schedule($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT us.id, us.day_of_week, us.activity, wp.name AS program_name
      FROM user_schedule us
      JOIN workout_programs wp ON us.program_id = wp.id
      WHERE us.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
