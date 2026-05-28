<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

// 1. Connect to configuration
require_once __DIR__ . '/config.php'; 

// 2. Connect directly to the Orders database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Database connection failed."]);
    exit;
}

$order_id = $_POST['order_id'] ?? null;
$status_id = $_POST['status_id'] ?? null;

if ($order_id && $status_id) {
    // 3. Update the order status
    $stmt = $conn->prepare("UPDATE orders SET status_id = ? WHERE id = ?");
    $stmt->bind_param("ii", $status_id, $order_id);
    
    if ($stmt->execute()) {
        echo json_encode([
            "status" => "success", 
            "message" => "Order #$order_id updated to status $status_id."
        ]);
    } else {
        echo json_encode([
            "status" => "error", 
            "message" => "Failed to update database."
        ]);
    }
    $stmt->close();
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Missing order_id or status_id in the request."
    ]);
}

$conn->close();
?>