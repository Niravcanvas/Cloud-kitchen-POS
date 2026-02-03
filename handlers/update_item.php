<?php
session_start();
include __DIR__ . '/../config/dbcon.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);
$name = trim($data['name'] ?? '');
$price = floatval($data['price'] ?? 0);

if($id <= 0 || !$name || $price <= 0){
    echo json_encode(['success'=>false, 'error'=>'Invalid data']);
    exit();
}

$stmt = $conn->prepare("UPDATE items SET name=?, price=? WHERE id=? AND is_active=1");
$stmt->bind_param("sdi", $name, $price, $id);

if($stmt->execute()){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Failed to update item']);
}
?>