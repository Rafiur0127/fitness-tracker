<?php
require_once __DIR__ . '/../model/user_model.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    verify_csrf();

    $name     = trim($_POST["name"]   ?? '');
    $email    = trim($_POST["email"]  ?? '');
    $password = $_POST["password"]    ?? '';
    $confirm  = $_POST["confirm"]     ?? '';
    $age      = intval($_POST["age"]  ?? 0);
    $weight   = floatval($_POST["weight"] ?? 0);
    $gender   = $_POST["gender"]      ?? '';

    if (!$name || !$email || !$password || !$confirm || !$age || !$weight || !$gender) {
        $errors[] = "All fields are required.";
    }

    // Standard RFC-5321 email validation — no artificial .com restriction
    if ($email && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Enter a valid email address.";
    }

    if ($age < 18) {
        $errors[] = "Age must be 18 or older.";
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $errors[] = "Password must be at least 8 characters and include uppercase, lowercase, and a number.";
    }

    if ($password !== $confirm) {
        $errors[] = "Passwords do not match.";
    }

    if ($email && is_email_taken($pdo, $email)) {
        $errors[] = "Email is already registered.";
    }

    if (empty($errors)) {
        if (register_user($pdo, $name, $email, $password, $age, $weight, $gender)) {
            header("Location: login.php?registered=1");
            exit;
        } else {
            $errors[] = "Something went wrong. Please try again.";
        }
    }
}
