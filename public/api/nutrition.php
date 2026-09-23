<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT id, meal_name, calories, protein, carbs, fat, meal_time
             FROM nutrition_logs
             WHERE user_id = ?
             ORDER BY meal_time DESC, id DESC
             LIMIT 50'
        );
        $stmt->execute([$userId]);
        $logs = $stmt->fetchAll(PDO::FETCH_ASSOC);

        sendJson(['logs' => $logs], 200, 'Nutrition logs retrieved.');
    }

    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        sendJson(null, 405, 'Method not allowed.');
    }

    $input = json_decode(file_get_contents('php://input'), true);
    if (!is_array($input)) {
        sendJson(null, 400, 'Invalid JSON request body.');
    }

    requireCsrf($input);

    $mealName = trim((string) ($input['meal_name'] ?? ''));
    $calories = filter_var($input['calories'] ?? null, FILTER_VALIDATE_INT);
    $protein = filter_var($input['protein'] ?? 0, FILTER_VALIDATE_FLOAT);
    $carbs = filter_var($input['carbs'] ?? 0, FILTER_VALIDATE_FLOAT);
    $fat = filter_var($input['fat'] ?? 0, FILTER_VALIDATE_FLOAT);

    if ($mealName === '' || mb_strlen($mealName) > 100 || $calories === false || $calories < 0 ||
        $protein === false || $protein < 0 || $carbs === false || $carbs < 0 || $fat === false || $fat < 0) {
        sendJson(null, 400, 'Meal name and non-negative nutrition values are required.');
    }

    $stmt = $pdo->prepare(
        'INSERT INTO nutrition_logs (user_id, meal_name, calories, protein, carbs, fat)
         VALUES (?, ?, ?, ?, ?, ?)'
    );
    $stmt->execute([$userId, $mealName, $calories, $protein, $carbs, $fat]);

    sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Nutrition log added successfully.');
} catch (PDOException $e) {
    error_log('Nutrition API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
