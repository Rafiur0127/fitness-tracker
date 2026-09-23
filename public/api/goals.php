<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT id, title, description, target_value, current_value, unit, status, created_at
             FROM goals
             WHERE user_id = ?
             ORDER BY id DESC'
        );
        $stmt->execute([$userId]);

        sendJson(['goals' => $stmt->fetchAll(PDO::FETCH_ASSOC)], 200, 'Goals retrieved.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(null, 405, 'Method not allowed.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        sendJson(null, 400, 'Invalid JSON request body.');
    }

    requireCsrf($input);

    $action = (string) ($input['action'] ?? '');

    if ($action === 'add') {
        $title = trim((string) ($input['title'] ?? ''));
        $target = filter_var($input['target_value'] ?? null, FILTER_VALIDATE_INT);
        $description = trim((string) ($input['description'] ?? ''));
        $unit = trim((string) ($input['unit'] ?? ''));

        if ($title === '' || mb_strlen($title) > 255 || $target === false || $target < 1 || mb_strlen($unit) > 50) {
            sendJson(null, 400, 'A title and positive target value are required.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO goals (user_id, title, description, target_value, unit)
             VALUES (?, ?, ?, ?, ?)'
        );
        $stmt->execute([$userId, $title, $description !== '' ? $description : null, $target, $unit]);

        sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Goal added successfully.');
    }

    if ($action === 'progress') {
        $goalId = filter_var($input['goal_id'] ?? null, FILTER_VALIDATE_INT);
        $progress = filter_var($input['progress'] ?? null, FILTER_VALIDATE_INT);

        if ($goalId === false || $goalId < 1 || $progress === false || $progress < 1) {
            sendJson(null, 400, 'A valid goal and positive progress value are required.');
        }

        $stmt = $pdo->prepare(
            'UPDATE goals
             SET current_value = current_value + ?,
                 status = CASE
                     WHEN current_value + ? >= target_value THEN \'completed\'
                     ELSE status
                 END
             WHERE id = ? AND user_id = ?'
        );
        $stmt->execute([$progress, $progress, $goalId, $userId]);

        if ($stmt->rowCount() === 0) {
            sendJson(null, 404, 'Goal not found.');
        }

        sendJson(['goal_id' => $goalId], 200, 'Goal progress updated.');
    }

    sendJson(null, 400, 'Unsupported goal action.');
} catch (PDOException $e) {
    error_log('Goals API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
