<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

$userId = requireLogin();

try {
    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $stmt = $pdo->prepare(
            'SELECT c.id, c.title, c.description, c.target_steps, c.start_date, c.end_date,
                    c.created_by,
                    CASE WHEN cp.id IS NULL THEN 0 ELSE 1 END AS joined
             FROM challenges c
             LEFT JOIN challenge_participants cp
               ON cp.challenge_id = c.id AND cp.user_id = ?
             ORDER BY c.start_date DESC, c.id DESC'
        );
        $stmt->execute([$userId]);

        sendJson(['challenges' => $stmt->fetchAll(PDO::FETCH_ASSOC)], 200, 'Challenges retrieved.');
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
        $title = trim((string) ($input['title'] ?? ''));
        $description = trim((string) ($input['description'] ?? ''));
        $target = filter_var($input['target_steps'] ?? null, FILTER_VALIDATE_INT);
        $start = (string) ($input['start_date'] ?? '');
        $end = (string) ($input['end_date'] ?? '');

        if ($title === '' || $description === '' || $target === false || $target < 1 ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $start) ||
            !preg_match('/^\d{4}-\d{2}-\d{2}$/', $end) || $end <= $start) {
            sendJson(null, 400, 'Valid challenge fields and an end date after the start date are required.');
        }

        $stmt = $pdo->prepare(
            'INSERT INTO challenges (title, description, target_steps, start_date, end_date, created_by)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([$title, $description, $target, $start, $end, $userId]);

        sendJson(['id' => (int) $pdo->lastInsertId()], 201, 'Challenge created.');
    }

    if ($action === 'join') {
        $challengeId = filter_var($input['challenge_id'] ?? null, FILTER_VALIDATE_INT);
        if ($challengeId === false || $challengeId < 1) {
            sendJson(null, 400, 'A valid challenge ID is required.');
        }

        $stmt = $pdo->prepare('SELECT id FROM challenges WHERE id = ?');
        $stmt->execute([$challengeId]);
        if (!$stmt->fetchColumn()) {
            sendJson(null, 404, 'Challenge not found.');
        }

        $stmt = $pdo->prepare(
            'INSERT IGNORE INTO challenge_participants (challenge_id, user_id) VALUES (?, ?)'
        );
        $stmt->execute([$challengeId, $userId]);

        sendJson(['challenge_id' => $challengeId], 200, 'Challenge joined.');
    }

    sendJson(null, 400, 'Unsupported challenge action.');
} catch (PDOException $e) {
    error_log('Challenges API error: ' . $e->getMessage());
    sendJson(null, 500, 'Server error.');
}
