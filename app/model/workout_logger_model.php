<?php

function log_workout($pdo, $user_id, $type, $duration) {
    $stmt = $pdo->prepare("
        INSERT INTO workout_sessions (user_id, type, duration, date)
        VALUES (?, ?, ?, CURDATE())
    ");
    return $stmt->execute([$user_id, $type, $duration]);
}

function get_recent_workouts($pdo, $user_id, $limit = 10) {
    // Use bindValue with explicit int type — avoids string interpolation in SQL
    $stmt = $pdo->prepare("
        SELECT type, duration, date
        FROM workout_sessions
        WHERE user_id = ?
        ORDER BY date DESC, id DESC
        LIMIT ?
    ");
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, (int) $limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
