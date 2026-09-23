<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $programs = $pdo->query(
            'SELECT id, title AS name, duration_weeks, description
             FROM programs
             ORDER BY id'
        )->fetchAll(PDO::FETCH_ASSOC);

        $stmt = $pdo->prepare(
            'SELECT s.id, s.day_of_week, w.workout_name AS activity,
                    p.id AS program_id, p.title AS program_name
             FROM schedule s
             JOIN programs p ON s.program_id = p.id
             JOIN workouts w ON s.workout_id = w.id
             WHERE s.user_id = ?
             ORDER BY s.id DESC'
        );
        $stmt->execute([$userId]);

        sendJson([
            'programs' => $programs,
            'schedule' => $stmt->fetchAll(PDO::FETCH_ASSOC),
        ], 200, 'Workout plans retrieved.');
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

    if ($action === 'create') {
        $name = trim((string) ($input['name'] ?? ''));
        $weeks = filter_var($input['weeks'] ?? null, FILTER_VALIDATE_INT);
        $description = trim((string) ($input['description'] ?? ''));

        if ($name === '' || mb_strlen($name) > 100 || $weeks === false || $weeks < 4 || $weeks > 12) {
            sendJson(null, 400, 'Plan name and a duration between 4 and 12 weeks are required.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO programs (title, duration_weeks, description) VALUES (?, ?, ?)'
        );
        $stmt->execute([$name, $weeks, $description]);

        sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Workout plan created.');
    }

    if ($action === 'assign') {
        $programId = filter_var($input['program_id'] ?? null, FILTER_VALIDATE_INT);
        $day = (string) ($input['day'] ?? '');
        $days = [
            'Mon' => 'Monday', 'Tue' => 'Tuesday', 'Wed' => 'Wednesday',
            'Thu' => 'Thursday', 'Fri' => 'Friday', 'Sat' => 'Saturday', 'Sun' => 'Sunday',
        ];

        if ($programId === false || $programId < 1 || !isset($days[$day])) {
            sendJson(null, 400, 'A valid plan and weekday are required.');
        }

        $stmt = $pdo->prepare('SELECT id FROM workouts WHERE program_id = ? ORDER BY id LIMIT 1');
        $stmt->execute([$programId]);
        $workoutId = $stmt->fetchColumn();
        if (!$workoutId) {
            sendJson(null, 404, 'No workout is available for this plan.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO schedule (user_id, program_id, workout_id, week_number, day_of_week)
             VALUES (?, ?, ?, 1, ?)'
        );
        $stmt->execute([$userId, $programId, $workoutId, $days[$day]]);

        sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Plan assigned to schedule.');
    }

    if ($action === 'delete') {
        $scheduleId = filter_var($input['schedule_id'] ?? null, FILTER_VALIDATE_INT);
        if ($scheduleId === false || $scheduleId < 1) {
            sendJson(null, 400, 'A valid schedule ID is required.');
        }

        $stmt = $pdo->prepare('DELETE FROM schedule WHERE id = ? AND user_id = ?');
        $stmt->execute([$scheduleId, $userId]);
        if ($stmt->rowCount() === 0) {
            sendJson(null, 404, 'Schedule entry not found.');
        }

        sendJson(null, 200, 'Schedule entry deleted.');
    }

    sendJson(null, 400, 'Unsupported plan action.');
} catch (PDOException $e) {
    error_log('Plans API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
