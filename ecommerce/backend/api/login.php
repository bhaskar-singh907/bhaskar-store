<?php
require_once __DIR__ . '/bootstrap.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    respond(['success' => false, 'error' => 'Method not allowed'], 405);
}

$data = body();
$username = clean($data['username'] ?? '');
$password = (string)($data['password'] ?? '');

if ($username === '' || $password === '') {
    respond(['success' => false, 'error' => 'Username and password are required'], 400);
}

$stmt = $pdo->prepare(
    'SELECT id, username, password_hash, role, full_name, phone, address, is_blocked
     FROM users WHERE username = ? LIMIT 1'
);
$stmt->execute([$username]);
$user = $stmt->fetch();

if (!$user) {
    respond(['success' => false, 'error' => 'Invalid username or password'], 401);
}

if ((int)$user['is_blocked'] === 1) {
    respond(['success' => false, 'error' => 'Your account has been blocked'], 403);
}

$storedHash = (string)$user['password_hash'];
$valid = false;

// Existing demo accounts in ecommerce.sql use SHA-256.
if (hash_equals(hash('sha256', $password), $storedHash)) {
    $valid = true;
}
// Newly registered accounts use password_hash().
elseif (str_starts_with($storedHash, '$') && password_verify($password, $storedHash)) {
    $valid = true;
}

if (!$valid) {
    respond(['success' => false, 'error' => 'Invalid username or password'], 401);
}

// Create a real PHP login session.
session_regenerate_id(true);
$_SESSION['logged_in'] = true;
$_SESSION['user_id'] = (int)$user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['role'] = $user['role'];
$_SESSION['full_name'] = $user['full_name'];

unset($user['password_hash'], $user['is_blocked']);

respond([
    'success' => true,
    'message' => 'Login successful',
    'user' => $user
]);
