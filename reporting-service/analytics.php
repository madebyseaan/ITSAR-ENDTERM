<?php
// reporting-service/analytics.php
header('Content-Type: application/json');
require_once __DIR__ . '/config.php'; 

// Reporting service aggregates data from the Orders database
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");

$query = "SELECT DATE(created_at) as date, SUM(total_amount) as daily_revenue FROM orders WHERE status_id = 2 GROUP BY DATE(created_at) ORDER BY date DESC LIMIT 7";
$result = $conn->query($query);

$revenue_data = [];
while ($row = $result->fetch_assoc()) {
    $revenue_data[] = $row;
}

echo json_encode(["status" => "success", "module" => "Reporting & Analytics", "data" => $revenue_data]);
?>