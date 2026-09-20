<?php
require_once __DIR__ . '/../model/user_model.php';

$login_errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $email    = trim($_POST["email"]    ?? '');
    $password = $_POST["password"]      ?? '';

    if (!$email || !$password) {
        $login_errors[] = "Email and password are required.";
    } else {
        $user = login_user($pdo, $email);

        if ($user && password_verify($password, $user["password"])) {
            session_regenerate_id(true);   // prevent session fixation
            $_SESSION["user_id"]   = $user["id"];
            $_SESSION["user_name"] = trim(($user["first_name"] ?? '') . ' ' . ($user["last_name"] ?? ''));
            header("Location: dashboard.php");
            exit;
        } else {
            $login_errors[] = "Invalid email or password.";
        }
    }
}
