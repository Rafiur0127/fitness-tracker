<?php
session_start();
require_once '../config/config.php';
require_once '../app/controller/dashboard_controller.php';

if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION["user_id"];
$dashboard = get_dashboard_data($pdo, $user_id);
include '../app/view/dashboard_view.php';
