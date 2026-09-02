<?php
require_once __DIR__ . '/bootstrap.php';

if (!loggedIn()) {
    respond(['success' => false, 'loggedIn' => false], 401);
}

$stmt = $pdo->prepare(
    'SELECT id, username, role, full_name, phone, address, is_blocked
     FROM users WHERE id = ? LIMIT 1'
);
$stmt->execute([currentUserId()]);
$user = $stmt->fetch();

if (!$user || (int)$user['is_blocked'] === 1) {
    $_SESSION = [];
    session_destroy();
    respond(['success' => false, 'loggedIn' => false], 401);
}

unset($user['is_blocked']);
respond(['success' => true, 'loggedIn' => true, 'user' => $user]);
