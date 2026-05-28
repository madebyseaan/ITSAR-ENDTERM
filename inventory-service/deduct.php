<?php
require_once __DIR__ . '/config.php'; // Local copy for Docker
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_inventory");

$book_id = $_GET['book_id'] ?? 0;
$qty = $_GET['qty'] ?? 1;

if ($book_id > 0) {
    // Only subtract stock if the stock is greater than or equal to the amount being requested
    $stmt = $conn->prepare("UPDATE books SET stock_quantity = stock_quantity - ? WHERE id = ? AND stock_quantity >= ?");
    $stmt->bind_param("iii", $qty, $book_id, $qty);
    $stmt->execute();
}
?>