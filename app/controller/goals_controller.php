<?php
require_once '../config/config.php';
require_once '../app/model/goals_model.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header("Location: login.php");
    exit;
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["add_goal"])) {
        $title = trim($_POST["title"]);
        $target = intval($_POST["target"]);
        if ($title && $target > 0) {
            add_goal($pdo, $user_id, $title, $target);
            $message = "Goal added!";
        }
    }

    if (isset($_POST["update_progress"])) {
        $goal_id = intval($_POST["goal_id"]);
        $progress = intval($_POST["progress"]);
        if ($goal_id && $progress > 0) {
            update_goal_progress($pdo, $goal_id, $progress);
            $message = "Progress updated!";
        }
    }
}

auto_complete_goals($pdo, $user_id);
$goals = get_goals($pdo, $user_id);
require_once '../app/view/goals_view.php';
