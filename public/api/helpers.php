<?php

declare(strict_types=1);

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, X-CSRF-Token');

/**
 * Finish an API request with the project's standard JSON envelope.
 *
 * @param mixed $data
 */
function sendJson(mixed $data = null, int $code = 200, string $message = ''): never
{
    http_response_code($code);

    $response = [
        'success' => $code >= 200 && $code < 300,
        'data' => $data,
        'message' => $message,
    ];

    $json = json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    if ($json === false) {
        http_response_code(500);
        echo '{"success":false,"data":null,"message":"Unable to encode API response."}';
        exit;
    }

    echo $json;
    exit;
}

/**
 * End an OPTIONS request without invoking endpoint business logic.
 */
function handleCorsPreflight(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
        http_response_code(204);
        exit;
    }
}

/**
 * Return the authenticated user's ID or terminate with a JSON 401 response.
 */
function requireLogin(): int
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $userId = $_SESSION['user_id'] ?? null;

    if (!is_int($userId) && !ctype_digit((string) $userId)) {
        sendJson(null, 401, 'Authentication required.');
    }

    return (int) $userId;
}

function getApiCsrfToken(): string
{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return (string) $_SESSION['csrf_token'];
}

/**
 * Reject state-changing requests without the session's CSRF token.
 *
 * @param array<string, mixed> $payload
 */
function requireCsrf(array $payload = []): void
{
    $provided = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $payload['csrf_token'] ?? '';
    $expected = getApiCsrfToken();

    if (!is_string($provided) || !hash_equals($expected, $provided)) {
        sendJson(null, 403, 'Invalid CSRF token.');
    }
}

handleCorsPreflight();
