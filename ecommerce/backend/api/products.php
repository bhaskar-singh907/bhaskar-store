<?php
require_once __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $stmt = $pdo->query("SELECT p.id,p.name,c.name AS category,p.cost_price AS cost,p.selling_price AS price,p.orig_price AS origPrice,p.stock,p.image_url AS image,p.is_active AS active FROM products p JOIN categories c ON c.id=p.category_id WHERE p.is_active=1 ORDER BY p.id DESC");
    respond($stmt->fetchAll());
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $d=body();
    foreach(['name','category','cost','price','origPrice','stock','image'] as $k) if (!isset($d[$k]) || $d[$k]==='') respond(['error'=>"Missing $k"],400);
    $cat=$pdo->prepare('SELECT id FROM categories WHERE name=?'); $cat->execute([$d['category']]); $categoryId=$cat->fetchColumn();
    if (!$categoryId) respond(['error'=>'Invalid category'],400);
    if (!empty($d['id'])) {
        $s=$pdo->prepare('UPDATE products SET name=?,category_id=?,cost_price=?,selling_price=?,orig_price=?,stock=?,image_url=? WHERE id=?');
        $s->execute([$d['name'],$categoryId,$d['cost'],$d['price'],$d['origPrice'],$d['stock'],$d['image'],$d['id']]);
        respond(['success'=>true,'message'=>'Product updated','id'=>(int)$d['id']]);
    }
    $s=$pdo->prepare('INSERT INTO products(name,category_id,cost_price,selling_price,orig_price,stock,image_url) VALUES(?,?,?,?,?,?,?)');
    $s->execute([$d['name'],$categoryId,$d['cost'],$d['price'],$d['origPrice'],$d['stock'],$d['image']]);
    respond(['success'=>true,'message'=>'Product created','id'=>(int)$pdo->lastInsertId()]);
}
respond(['error'=>'Method not allowed'],405);
