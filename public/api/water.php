<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT COALESCE(SUM(amount_ml), 0)
             FROM water_logs
             WHERE user_id = ? AND DATE(tracked_at) = CURDATE()'
        );
        $stmt->execute([$userId]);

        sendJson(['today_ml' => (int) $stmt->fetchColumn()], 200, 'Water intake retrieved.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(null, 405, 'Method not allowed.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        sendJson(null, 400, 'Invalid JSON request body.');
    }

    requireCsrf($input);

    $amount = filter_var($input['amount_ml'] ?? null, FILTER_VALIDATE_INT);
    if ($amount === false || $amount < 1 || $amount > 10000) {
        sendJson(null, 400, 'Water amount must be between 1 and 10000 ml.');
    }

    $stmt = $pdo->prepare('INSERT INTO water_logs (user_id, amount_ml) VALUES (?, ?)');
    $stmt->execute([$userId, $amount]);

    sendJson(['id' => (int) $pdo->lastInsertId(), 'amount_ml' => $amount], 201, 'Water intake logged successfully.');
} catch (PDOException $e) {
    error_log('Water API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
