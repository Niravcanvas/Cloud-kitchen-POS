<?php
session_start();
include __DIR__ . '/../config/dbcon.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$id = intval($data['id'] ?? 0);

if($id <= 0){
    echo json_encode(['success'=>false, 'error'=>'Invalid item ID']);
    exit();
}

$stmt = $conn->prepare("UPDATE items SET is_active=0 WHERE id=?");
$stmt->bind_param("i", $id);

if($stmt->execute()){
    echo json_encode(['success'=>true]);
} else {
    echo json_encode(['success'=>false, 'error'=>'Failed to delete item']);
}
?>