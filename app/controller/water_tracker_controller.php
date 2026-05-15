<?php
require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../model/water_tracker_model.php';

$user_id = $_SESSION['user_id'] ?? null;

if (!$user_id) {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount_ml'])) {
    verify_csrf();
    $amount = (int) $_POST['amount_ml'];
    if ($amount > 0) {
        log_water_intake($pdo, $user_id, $amount);
    }
}

$total_today = get_today_total($pdo, $user_id);
require_once __DIR__ . '/../view/water_tracker_view.php';
