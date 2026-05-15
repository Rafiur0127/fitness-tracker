<?php
session_start();
require_once '../config/config.php';
require_once '../app/controller/challenge_controller.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$data = handle_challenges($pdo, $user_id);
include '../app/view/challenge_view.php';