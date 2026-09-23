<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $limit = max(1, min(50, (int) ($_GET['limit'] ?? 10)));
        $stmt = $pdo->prepare(
            'SELECT id, type, duration, date
             FROM workout_sessions
             WHERE user_id = ?
             ORDER BY date DESC, id DESC
             LIMIT ?'
        );
        $stmt->bindValue(1, $userId, PDO::PARAM_INT);
        $stmt->bindValue(2, $limit, PDO::PARAM_INT);
        $stmt->execute();

        sendJson(['workouts' => $stmt->fetchAll(PDO::FETCH_ASSOC)], 200, 'Workouts retrieved.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(null, 405, 'Method not allowed.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        sendJson(null, 400, 'Invalid JSON request body.');
    }

    $type = trim((string) ($input['type'] ?? ''));
    $duration = filter_var($input['duration'] ?? null, FILTER_VALIDATE_INT);

    if ($type === '' || $duration === false || $duration < 1 || mb_strlen($type) > 50) {
        sendJson(null, 400, 'Workout type and a positive duration are required.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO workout_sessions (user_id, type, duration, date)
         VALUES (?, ?, ?, CURDATE())'
    );
    $stmt->execute([$userId, $type, $duration]);

    sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Workout logged successfully.');
} catch (PDOException $e) {
    error_log('Workouts API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
