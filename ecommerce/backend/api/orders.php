<?php
require_once __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
 $username=clean($_GET['username']??''); if(!$username) respond(['error'=>'Username required'],400);
 $s=$pdo->prepare('SELECT id FROM users WHERE username=?');$s->execute([$username]);$uid=$s->fetchColumn();if(!$uid)respond(['error'=>'User not found'],404);
 $s=$pdo->prepare('SELECT order_id,customer_name,customer_phone,delivery_address,total_amount,status,created_at FROM orders WHERE user_id=? ORDER BY created_at DESC');$s->execute([$uid]);respond($s->fetchAll());
}
respond(['error'=>'Method not allowed'],405);
