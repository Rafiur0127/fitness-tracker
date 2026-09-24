<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

function getDashboardUserName(PDO $pdo, int $userId): string
{
    $statement = $pdo->prepare(
        'SELECT CONCAT_WS(" ", first_name, last_name) AS name FROM users WHERE id = ?'
    );
    $statement->execute([$userId]);
    $name = $statement->fetchColumn();

    return is_string($name) && $name !== '' ? $name : 'User';
}

function getDashboardLifetimeStats(PDO $pdo, int $userId): array
{
    $statement = $pdo->prepare(
        'SELECT COUNT(*) AS workout_count, COALESCE(SUM(duration), 0) AS workout_minutes
         FROM workout_sessions WHERE user_id = ?'
    );
    $statement->execute([$userId]);
    $workout = $statement->fetch(PDO::FETCH_ASSOC) ?: [];

    $statement = $pdo->prepare(
        'SELECT COALESCE(SUM(amount_ml), 0) FROM water_logs WHERE user_id = ?'
    );
    $statement->execute([$userId]);
    $water = $statement->fetchColumn();

    $statement = $pdo->prepare(
        "SELECT COUNT(*) FROM goals WHERE user_id = ? AND status = 'completed'"
    );
    $statement->execute([$userId]);
    $goals = $statement->fetchColumn();

    return [
        'total_workouts' => (int) ($workout['workout_count'] ?? 0),
        'total_minutes' => (int) ($workout['workout_minutes'] ?? 0),
        'total_water_ml' => (int) $water,
        'goals_completed' => (int) $goals,
    ];
}

function getDashboardTrophies(array $stats): array
{
    $trophies = [];
    if ($stats['total_workouts'] >= 10) {
        $trophies[] = '🏋️‍♂️ Logged 10+ Workouts';
    }
    if ($stats['total_minutes'] >= 500) {
        $trophies[] = '⏱️ 500+ Mins Exercised';
    }
    if ($stats['total_water_ml'] >= 10000) {
        $trophies[] = '💧 Drank 10L+ Water';
    }
    if ($stats['goals_completed'] >= 3) {
        $trophies[] = '🎯 3+ Goals Crushed';
    }

    return $trophies;
}

function getDashboardWeeklyMinutes(PDO $pdo, int $userId): array
{
    $statement = $pdo->prepare(
        "SELECT DATE_FORMAT(date, '%a') AS day, type, SUM(duration) AS total
         FROM workout_sessions
         WHERE user_id = ? AND WEEK(date) = WEEK(CURDATE())
         GROUP BY day, type"
    );
    $statement->execute([$userId]);

    $data = [];
    foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $day) {
        $data[$day] = ['strength' => 0, 'cardio' => 0];
    }

    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $day = $row['day'];
        $type = strtolower((string) $row['type']);
        if (isset($data[$day][$type])) {
            $data[$day][$type] = (int) $row['total'];
        }
    }

    return $data;
}

function getDashboardMonthlyTrends(PDO $pdo, int $userId): array
{
    $statement = $pdo->prepare(
        "SELECT WEEK(date) AS week_num, type, SUM(duration) AS total
         FROM workout_sessions
         WHERE user_id = ? AND date >= DATE_SUB(CURDATE(), INTERVAL 28 DAY)
         GROUP BY week_num, type
         ORDER BY week_num ASC"
    );
    $statement->execute([$userId]);

    $weeks = [];
    foreach ($statement->fetchAll(PDO::FETCH_ASSOC) as $row) {
        $week = 'Week ' . $row['week_num'];
        $type = strtolower((string) $row['type']);
        $weeks[$week] ??= ['strength' => 0, 'cardio' => 0];
        if (isset($weeks[$week][$type])) {
            $weeks[$week][$type] = (int) $row['total'];
        }
    }

    return $weeks;
}

function getDashboardRecentWorkouts(PDO $pdo, int $userId, int $limit = 10): array
{
    $statement = $pdo->prepare(
        'SELECT type, duration, date FROM workout_sessions
         WHERE user_id = ? ORDER BY date DESC, id DESC LIMIT ?'
    );
    $statement->bindValue(1, $userId, PDO::PARAM_INT);
    $statement->bindValue(2, $limit, PDO::PARAM_INT);
    $statement->execute();

    return $statement->fetchAll(PDO::FETCH_ASSOC);
}

function getDashboardTodayWater(PDO $pdo, int $userId): int
{
    $statement = $pdo->prepare(
        'SELECT COALESCE(SUM(amount_ml), 0) FROM water_logs
         WHERE user_id = ? AND DATE(tracked_at) = CURDATE()'
    );
    $statement->execute([$userId]);

    return (int) $statement->fetchColumn();
}

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    sendJson(null, 405, 'Method not allowed.');
}

$userId = requireLogin();

try {
    $name = getDashboardUserName($pdo, $userId);
    $lifetime = getDashboardLifetimeStats($pdo, $userId);
    $trophies = getDashboardTrophies($lifetime);
    $weekly = getDashboardWeeklyMinutes($pdo, $userId);
    $monthly = getDashboardMonthlyTrends($pdo, $userId);
    $recent = getDashboardRecentWorkouts($pdo, $userId);
    $today_water_ml = getDashboardTodayWater($pdo, $userId);

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
