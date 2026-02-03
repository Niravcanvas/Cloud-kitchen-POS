<?php
session_start();
include __DIR__ . '/../config/dbcon.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$name = trim($data['name'] ?? '');
$price = floatval($data['price'] ?? 0);

if(!$name || $price <= 0){
    echo json_encode(['success'=>false, 'error'=>'Invalid name or price']);
    exit();
}

$stmt = $conn->prepare("INSERT INTO items (name, price, is_active) VALUES (?, ?, 1)");
$stmt->bind_param("sd", $name, $price);

if($stmt->execute()){
    echo json_encode(['success'=>true, 'id'=>$stmt->insert_id]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Failed to save item']);
}
?>