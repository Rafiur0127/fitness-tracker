<?php
require_once __DIR__ . '/../model/challenge_model.php';

function handle_challenges($pdo, $user_id) {
    $challenge_errors = [];

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        verify_csrf();

        if (isset($_POST['create_challenge'])) {
            $title       = trim($_POST['title']        ?? '');
            $description = trim($_POST['description']  ?? '');
            $target      = (int) ($_POST['target_steps'] ?? 0);
            $start       = $_POST['start_date']        ?? '';
            $end         = $_POST['end_date']          ?? '';

            if (!$title || !$description || $target <= 0 || !$start || !$end) {
                $challenge_errors[] = "All challenge fields are required and target must be positive.";
            } elseif ($end <= $start) {
                $challenge_errors[] = "End date must be after start date.";
            } else {
                create_challenge($pdo, $title, $description, $target, $start, $end, $user_id);
            }
        }

        if (isset($_POST['join_challenge'])) {
            $challenge_id = (int) ($_POST['challenge_id'] ?? 0);
            if ($challenge_id > 0) {
                join_challenge($pdo, $challenge_id, $user_id);
            }
        }
    }

    $challenges  = get_all_challenges($pdo);
    $joined_ids  = get_joined_challenge_ids($pdo, $user_id);

    return [
        'challenges'       => $challenges,
        'joined_ids'       => $joined_ids,
        'challenge_errors' => $challenge_errors,
    ];
}
