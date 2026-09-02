<?php
require_once __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(['error'=>'Method not allowed'],405);
$d=body(); $username=clean($d['username']??''); $password=(string)($d['password']??''); $name=clean($d['full_name']??$d['name']??''); $phone=clean($d['phone']??''); $address=clean($d['address']??'');
if(!$username||strlen($password)<6||!$name) respond(['error'=>'Username, full name and a password of at least 6 characters are required'],400);
$exists=$pdo->prepare('SELECT id FROM users WHERE username=?'); $exists->execute([$username]); if($exists->fetch()) respond(['error'=>'Username already exists'],409);
$hash=password_hash($password,PASSWORD_DEFAULT); $s=$pdo->prepare('INSERT INTO users(username,password_hash,role,full_name,phone,address) VALUES(?,?,?,?,?,?)'); $s->execute([$username,$hash,'Customer',$name,$phone,$address]);
respond(['success'=>true,'message'=>'Registration successful','userId'=>(int)$pdo->lastInsertId()],201);
