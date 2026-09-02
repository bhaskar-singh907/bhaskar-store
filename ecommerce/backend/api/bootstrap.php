<?php
// Common API bootstrap for the Ecommerce PHP backend.
// All API requests are served from the same localhost origin as the frontend.

session_name('ECOMMERCE_SESSION');
session_start();

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/../config/config.php';

function body(): array
{
    $raw = file_get_contents('php://input');
    if ($raw === false || trim($raw) === '') {
        return [];
    }

    $data = json_decode($raw, true);
    return is_array($data) ? $data : [];
}

function respond(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
}

function clean($value): string
{
    return trim((string)$value);
}

function loggedIn(): bool
{
    return !empty($_SESSION['logged_in']) && !empty($_SESSION['user_id']);
}

function currentUserId(): ?int
{
    return loggedIn() ? (int)$_SESSION['user_id'] : null;
}

function currentUser(): ?array
{
    if (!loggedIn()) {
        return null;
    }

    return [
        'id' => (int)$_SESSION['user_id'],
        'username' => $_SESSION['username'] ?? '',
        'role' => $_SESSION['role'] ?? 'Customer',
        'full_name' => $_SESSION['full_name'] ?? ''
    ];
}
