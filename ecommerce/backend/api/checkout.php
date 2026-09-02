<?php
require_once __DIR__ . '/bootstrap.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') respond(['error'=>'Method not allowed'],405);
$d=body(); $username=clean($d['username']??''); $name=clean($d['name']??''); $phone=clean($d['phone']??''); $address=clean($d['address']??''); $pay=clean($d['payMode']??''); $items=$d['items']??[];
if(!$username||!$name||!$phone||!$address||!in_array($pay,['UPI','Card','COD'],true)||!is_array($items)||count($items)===0) respond(['error'=>'Complete delivery details and add at least one item'],400);
if (!loggedIn()) respond(['error'=>'Please sign in before checkout'],401);
$s=$pdo->prepare('SELECT id,username,is_blocked FROM users WHERE id=? LIMIT 1'); $s->execute([currentUserId()]); $user=$s->fetch(); if(!$user) respond(['error'=>'Session expired. Please sign in again.'],401); if((int)$user['is_blocked']) respond(['error'=>'Account blocked'],403);
$username = $user['username'];
try {
 $pdo->beginTransaction(); $verified=[]; $total=0; $cost=0;
 $q=$pdo->prepare('SELECT id,name,cost_price,selling_price,stock FROM products WHERE id=? FOR UPDATE');
 foreach($items as $item){ $id=(int)($item['id']??0); $qty=(int)($item['qty']??0); if($id<=0||$qty<=0) throw new Exception('Invalid cart item'); $q->execute([$id]); $p=$q->fetch(); if(!$p||$p['stock']<$qty) throw new Exception("Insufficient stock for product ID: $id"); $line=(float)$p['selling_price']*$qty; $lineCost=(float)$p['cost_price']*$qty; $total+=$line; $cost+=$lineCost; $verified[]=['id'=>$p['id'],'name'=>$p['name'],'price'=>$p['selling_price'],'cost'=>$p['cost_price'],'qty'=>$qty,'line'=>$line]; }
 $orderId='ORD-'.date('ymdHis').'-'.strtoupper(bin2hex(random_bytes(2))); $txnId='TXN-'.strtoupper(bin2hex(random_bytes(5))); $profit=$total-$cost;
 $o=$pdo->prepare("INSERT INTO orders(order_id,user_id,customer_name,customer_phone,delivery_address,total_amount,total_cost,gross_profit,status) VALUES(?,?,?,?,?,?,?,?,'Confirmed')"); $o->execute([$orderId,$user['id'],$name,$phone,$address,$total,$cost,$profit]);
 $oi=$pdo->prepare('INSERT INTO order_items(order_id,product_id,product_name,unit_price,cost_price,quantity,line_subtotal) VALUES(?,?,?,?,?,?,?)'); $up=$pdo->prepare('UPDATE products SET stock=stock-? WHERE id=?');
 foreach($verified as $v){$oi->execute([$orderId,$v['id'],$v['name'],$v['price'],$v['cost'],$v['qty'],$v['line']]);$up->execute([$v['qty'],$v['id']]);}
 $payq=$pdo->prepare('INSERT INTO payments(txn_id,order_id,payment_channel,amount,status) VALUES(?,?,?,?,"Completed")'); $payq->execute([$txnId,$orderId,$pay,$total]);
 $pdo->commit(); respond(['success'=>true,'message'=>'Order placed successfully','orderId'=>$orderId,'txnId'=>$txnId,'total'=>$total]);
} catch(Throwable $e){ if($pdo->inTransaction())$pdo->rollBack(); respond(['error'=>$e->getMessage()],400); }
