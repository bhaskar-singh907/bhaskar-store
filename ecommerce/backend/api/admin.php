<?php
require_once __DIR__.'/bootstrap.php';
if($_SERVER['REQUEST_METHOD']!=='GET') respond(['error'=>'Method not allowed'],405);
$stats=$pdo->query("SELECT (SELECT COUNT(*) FROM products WHERE is_active=1) products,(SELECT COUNT(*) FROM users WHERE role='Customer') customers,(SELECT COUNT(*) FROM orders) orders,(SELECT COALESCE(SUM(total_amount),0) FROM orders WHERE status<>'Cancelled') revenue")->fetch();
$orders=$pdo->query("SELECT order_id,customer_name,total_amount,status,created_at FROM orders ORDER BY created_at DESC LIMIT 20")->fetchAll();
respond(['stats'=>$stats,'orders'=>$orders]);
