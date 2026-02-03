<?php
session_start();
include __DIR__ . '/../config/dbcon.php';
header('Content-Type: application/json');

// --- Check DB connection ---
if (!$conn) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed']);
    exit();
}

// --- Check user session ---
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

// --- Get JSON input ---
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['order']) || empty($data['order'])) {
    echo json_encode(['success' => false, 'error' => 'No items in order']);
    exit();
}

// --- Customer details ---
$customerData = $data['customer'] ?? [];
$customerName = trim($customerData['name'] ?? 'Guest');
$customerEmail = trim($customerData['email'] ?? '');
$customerMobile = trim($customerData['mobile'] ?? '');

try {
    // --- Check if customer exists ---
    $stmt_cust = $conn->prepare("SELECT id FROM customers WHERE name=? AND email=? LIMIT 1");
    $stmt_cust->bind_param("ss", $customerName, $customerEmail);
    $stmt_cust->execute();
    $result = $stmt_cust->get_result();

    if ($result->num_rows > 0) {
        $customer_id = $result->fetch_assoc()['id'];
    } else {
        $stmt_new = $conn->prepare("INSERT INTO customers (name, email, mobile) VALUES (?, ?, ?)");
        $stmt_new->bind_param("sss", $customerName, $customerEmail, $customerMobile);
        $stmt_new->execute();
        $customer_id = $stmt_new->insert_id;
    }

    // --- Calculate total ---
    $total = 0;
    foreach ($data['order'] as $item) {
        $total += $item['price'] * $item['qty'];
    }

    // --- Payment details ---
    $paymentData = $data['payment'] ?? [];
    $paymentMode = $paymentData['mode'] ?? 'Cash';
    $amountTaken = floatval($paymentData['taken'] ?? $total);
    $change = max(0, $amountTaken - $total);

// --- Insert order ---
$stmt = $conn->prepare("INSERT INTO orders 
    (user_id, customer_id, total, payment_mode, amount_taken, change_amount) 
    VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param("iidsdd", $user_id, $customer_id, $total, $paymentMode, $amountTaken, $change);
$stmt->execute();
$order_id = $stmt->insert_id;


    // --- Insert order items ---
    $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, item_id, quantity, price, total) VALUES (?, ?, ?, ?, ?)");
    foreach ($data['order'] as $item) {
        $subtotal = $item['qty'] * $item['price'];
        $stmt_items->bind_param("iiidd", $order_id, $item['id'], $item['qty'], $item['price'], $subtotal);
        $stmt_items->execute();
    }

    // --- Success response ---
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'total' => $total,
        'amount_taken' => $amountTaken,
        'change' => $change
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    exit();
}
?>