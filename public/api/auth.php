<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/../../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    sendJson(['csrf_token' => getApiCsrfToken()], 200, 'CSRF token retrieved.');
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    sendJson(null, 405, 'Method not allowed.');
}

$rawInput = file_get_contents('php://input');
$payload = !empty($rawInput) ? json_decode($rawInput, true) : [];
if (!is_array($payload)) {
    $payload = [];
}

requireCsrf($payload);

$action = $_POST['action'] ?? $payload['action'] ?? null;
$action = is_string($action) ? trim($action) : null;

if ($action === 'login') {
    $email = trim((string) ($_POST['email'] ?? $payload['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? $payload['password'] ?? '');

    if ($email === '' || $password === '') {
        sendJson(null, 400, 'Email and password are required.');
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        sendJson(null, 400, 'Please enter a valid email address.');
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, (string) ($user['password'] ?? ''))) {
        sendJson(null, 401, 'Invalid email or password.');
    }

    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = trim((string) ($user['first_name'] ?? '') . ' ' . (string) ($user['last_name'] ?? ''));

    sendJson([
        'user' => [
            'id' => (int) $user['id'],
            'name' => $_SESSION['user_name'],
            'email' => $user['email'],
        ],
    ], 200, 'Login successful.');
}

if ($action === 'signup') {
    $name = trim((string) ($_POST['name'] ?? $payload['name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? $payload['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? $payload['password'] ?? '');
    $confirm = (string) ($_POST['confirm'] ?? $payload['confirm'] ?? '');
    $age = isset($_POST['age']) ? (int) $_POST['age'] : (int) ($payload['age'] ?? 0);
    $gender = trim((string) ($_POST['gender'] ?? $payload['gender'] ?? ''));

    $errors = [];

    if ($name === '' || $email === '' || $password === '' || $confirm === '' || $age <= 0 || $gender === '') {
        $errors[] = 'All fields are required.';
    }

    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Enter a valid email address.';
    }

    if ($age < 18) {
        $errors[] = 'Age must be 18 or older.';
    }

    if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $password)) {
        $errors[] = 'Password must be at least 8 characters and include uppercase, lowercase, and a number.';
    }

    if ($password !== '' && $confirm !== '' && $password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if ($email !== '') {
        $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ? LIMIT 1');
        $stmt->execute([$email]);
        if ($stmt->fetchColumn()) {
            $errors[] = 'Email is already registered.';
        }
    }

    if (!empty($errors)) {
        sendJson(['errors' => $errors], 400, 'Validation failed.');
    }

    $nameParts = preg_split('/\s+/', $name, 2);
    $firstName = trim((string) ($nameParts[0] ?? ''));
    $lastName = trim((string) ($nameParts[1] ?? ''));
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare(
        'INSERT INTO users (first_name, last_name, email, password, age, gender) VALUES (?, ?, ?, ?, ?, ?)'
    );

    $result = $stmt->execute([$firstName, $lastName !== '' ? $lastName : null, $email, $hashedPassword, $age, $gender]);

    if (!$result) {
        sendJson(null, 500, 'Something went wrong while creating your account.');
    }

    sendJson([
        'user' => [
            'email' => $email,
            'name' => $name,
        ],
    ], 201, 'Account created successfully.');
}

sendJson(null, 400, 'Unsupported action.');
