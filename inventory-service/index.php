<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/config.php'; // Local copy for Docker
$conn = get_db_connection("erp_inventory");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $query = "SELECT b.id, b.isbn, b.title, b.price, b.stock_quantity, a.full_name AS author, c.name AS category 
              FROM books b 
              LEFT JOIN authors a ON b.author_id = a.id 
              LEFT JOIN categories c ON b.category_id = c.id";
    $result = $conn->query($query);
    $books = [];
    while($row = $result->fetch_assoc()) { $books[] = $row; }
    echo json_encode(["status" => "success", "data" => $books]);
}
?>