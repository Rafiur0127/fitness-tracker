<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../app/model/dashboard_model.php';
require_once __DIR__ . '/../../app/model/workout_logger_model.php';
require_once __DIR__ . '/../../app/model/water_tracker_model.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(null, 405, 'Method not allowed.');
}

$userId = requireLogin();

try {
    $name = get_user_name($pdo, $userId);
    $lifetime = get_lifetime_stats($pdo, $userId);
    $trophies = get_user_achievements_from_totals($lifetime);
    $weekly = get_weekly_workout_minutes($pdo, $userId);
    $monthly = get_monthly_workout_trends($pdo, $userId);
    $recent = get_recent_workouts($pdo, $userId, 10);
    $today_water_ml = get_today_total($pdo, $userId);

    $data = [
        'user' => [
            'id' => $userId,
            'name' => $name,
        ],
        'stats' => $lifetime,
        'trophies' => $trophies,
        'weekly' => $weekly,
        'monthly' => $monthly,
        'recent_workouts' => $recent,
        'today_water_ml' => $today_water_ml,
    ];

    sendJson($data, 200, 'Dashboard data retrieved.');
} catch (Throwable $e) {
    error_log('Dashboard API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
