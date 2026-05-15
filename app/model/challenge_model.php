<?php
function create_challenge($pdo, $title, $description, $target, $start, $end, $user_id) {
    $stmt = $pdo->prepare("INSERT INTO challenges (title, description, target_steps, start_date, end_date, created_by)
                           VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$title, $description, $target, $start, $end, $user_id]);
}

function join_challenge($pdo, $challenge_id, $user_id) {
    $stmt = $pdo->prepare("SELECT id FROM challenge_participants WHERE challenge_id = ? AND user_id = ?");
    $stmt->execute([$challenge_id, $user_id]);
    if (!$stmt->fetch()) {
        $stmt = $pdo->prepare("INSERT INTO challenge_participants (challenge_id, user_id) VALUES (?, ?)");
        $stmt->execute([$challenge_id, $user_id]);
    }
}

function get_all_challenges($pdo) {
    return $pdo->query("SELECT * FROM challenges ORDER BY start_date DESC")->fetchAll();
}

function get_joined_challenge_ids($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT challenge_id FROM challenge_participants WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return array_column($stmt->fetchAll(), 'challenge_id');
}
