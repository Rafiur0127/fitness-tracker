<?php
function create_plan($pdo, $name, $weeks, $desc) {
    $stmt = $pdo->prepare("INSERT INTO programs (title, duration_weeks, description) VALUES (?, ?, ?)");
    $stmt->execute([$name, $weeks, $desc]);
}

function assign_schedule($pdo, $user_id, $program_id, $day, $activity) {
    $days = [
        'Mon' => 'Monday',
        'Tue' => 'Tuesday',
        'Wed' => 'Wednesday',
        'Thu' => 'Thursday',
        'Fri' => 'Friday',
        'Sat' => 'Saturday',
        'Sun' => 'Sunday',
    ];
    $day = $days[$day] ?? $day;
    $stmt = $pdo->prepare("SELECT id FROM workouts WHERE program_id = ? ORDER BY id LIMIT 1");
    $stmt->execute([$program_id]);
    $workout_id = $stmt->fetchColumn();
    if (!$workout_id) {
        return false;
    }
    $stmt = $pdo->prepare("INSERT INTO schedule (user_id, program_id, workout_id, week_number, day_of_week) VALUES (?, ?, ?, 1, ?)");
    return $stmt->execute([$user_id, $program_id, $workout_id, $day]);
}

function delete_schedule($pdo, $user_id, $schedule_id) {
    $stmt = $pdo->prepare("DELETE FROM schedule WHERE id = ? AND user_id = ?");
    return $stmt->execute([$schedule_id, $user_id]);
}

function get_all_programs($pdo) {
    return $pdo->query("SELECT id, title AS name, duration_weeks, description FROM programs")->fetchAll();
}

function get_user_schedule($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT s.id, s.day_of_week, w.workout_name AS activity, p.title AS program_name
      FROM schedule s
      JOIN programs p ON s.program_id = p.id
      JOIN workouts w ON s.workout_id = w.id
      WHERE s.user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll();
}
