<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// Local copies for Docker
require_once __DIR__ . '/config.php'; 
require_once __DIR__ . '/logger.php'; 

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");

$input = file_get_contents('php://input');
$data = json_decode($input, true);

$customer_id = $data['customer_id'] ?? 1; 
$total = $data['total'] ?? 0;
$items = $data['items'] ?? [];

$stmt = $conn->prepare("INSERT INTO orders (customer_id, total_amount, status_id) VALUES (?, ?, 1)");
$stmt->bind_param("id", $customer_id, $total);

if ($stmt->execute()) {
    $order_id = $conn->insert_id;
    $item_stmt = $conn->prepare("INSERT INTO order_items (order_id, book_id, quantity, subtotal) VALUES (?, ?, ?, ?)");
    
    foreach ($items as $item) {
        $qty = $item['qty'] ?? 1;
        $subtotal = $item['price'] * $qty;
        $item_stmt->bind_param("iiid", $order_id, $item['id'], $qty, $subtotal);
        $item_stmt->execute();
        
        // Use internal Docker service name if in production
        global $is_production;
        $inv_url = $is_production ? 'http://inventory-service/deduct.php' : 'http://localhost/bookstore-erp/inventory-service/deduct.php';
        @file_get_contents($inv_url . "?book_id=" . $item['id'] . "&qty=" . $qty);
    }

    logSystemActivity("New order placed (Order ID: " . $order_id . ")");
    echo json_encode(["status" => "success", "order_id" => $order_id, "message" => "Order placed successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Failed to process order."]);
}
?>