<?php

function log_water_intake($pdo, $user_id, $amount_ml) {
    $stmt = $pdo->prepare("INSERT INTO water_logs (user_id, amount_ml) VALUES (?, ?)");
    $stmt->execute([$user_id, $amount_ml]);
}

function get_today_total($pdo, $user_id) {
    $stmt = $pdo->prepare("
        SELECT COALESCE(SUM(amount_ml), 0)
        FROM water_logs
        WHERE user_id = ? AND DATE(tracked_at) = CURDATE()
    ");
    $stmt->execute([$user_id]);   // ← was missing; caused always-null return
    return (int) $stmt->fetchColumn();
}
