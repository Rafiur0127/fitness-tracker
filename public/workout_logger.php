<?php
require_once '../config/config.php';

if (!isset($_SESSION["user_id"])) {
  header("Location: login.php");
  exit;
}

$user_id = $_SESSION["user_id"];

require_once '../app/controller/workout_logger_controller.php';
require_once '../app/view/workout_logger_view.php';
