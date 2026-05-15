<?php

function add_goal($pdo, $user_id, $title, $target) {
    $stmt = $pdo->prepare("INSERT INTO goals (user_id, title, target_value) VALUES (?, ?, ?)");
    return $stmt->execute([$user_id, $title, $target]);
}

function get_goals($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT * FROM goals WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function update_goal_progress($pdo, $goal_id, $amount) {
    $stmt = $pdo->prepare("UPDATE goals SET current_value = current_value + ? WHERE id = ?");
    return $stmt->execute([$amount, $goal_id]);
}

function auto_complete_goals($pdo, $user_id) {
    $stmt = $pdo->prepare("UPDATE goals SET status = 'completed' WHERE user_id = ? AND current_value >= target_value AND status = 'ongoing'");
    $stmt->execute([$user_id]);
}
