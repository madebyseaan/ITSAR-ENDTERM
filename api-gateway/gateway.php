<?php
// api-gateway/gateway.php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once 'jwt_helper.php';
require_once __DIR__ . '/config.php'; // Local copy for Docker

// --- DYNAMIC URL SETTINGS ---
// If $is_production is true (in config.php), we use internal Docker service names
$inv_base = $is_production ? 'http://inventory-service' : 'http://localhost/bookstore-erp/inventory-service';
$ord_base = $is_production ? 'http://order-service' : 'http://localhost/bookstore-erp/order-service';
$rpt_base = $is_production ? 'http://reporting-service' : 'http://localhost/bookstore-erp/reporting-service';

$route = $_GET['route'] ?? '';

// 1. PUBLIC ROUTES (No JWT required)
if ($route == 'login') {
    $auth_base = $is_production ? 'http://auth-service' : 'http://localhost/bookstore-erp/auth-service';
    $ch = curl_init($auth_base . '/login.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)) { echo json_encode(["status" => "error", "message" => "Auth Service unreachable"]); }
    else { echo $response; }
    curl_close($ch);
    exit;
} elseif ($route == 'register') {
    $auth_base = $is_production ? 'http://auth-service' : 'http://localhost/bookstore-erp/auth-service';
    $ch = curl_init($auth_base . '/register.php');
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)) { echo json_encode(["status" => "error", "message" => "Auth Service unreachable"]); }
    else { echo $response; }
    curl_close($ch);
    exit;
} elseif ($route == 'books') {
    $ch = curl_init($inv_base . '/index.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    if(curl_errno($ch)) { echo json_encode(["status" => "error", "message" => "Inventory Service unreachable"]); }
    else { echo $response; }
    curl_close($ch);
    exit;
}

// 2. PROTECTED ROUTES: Require JWT
$headers = apache_request_headers();
$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? $headers['Authorization'] ?? '';
$token = str_replace('Bearer ', '', $auth_header);

$user_data = verify_jwt($token);
if (!$user_data) {
    http_response_code(401);
    echo json_encode(["status" => "error", "message" => "Unauthorized. Invalid or missing JWT."]);
    exit;
}

// Proceed with routing using dynamic URLs
if ($route == 'checkout') {
    $ch = curl_init($ord_base . '/checkout.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, file_get_contents('php://input'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    echo curl_exec($ch);
    curl_close($ch);
} elseif ($route == 'update_order') {
    $ch = curl_init($ord_base . '/update_status.php');
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_POST));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    echo curl_exec($ch);
    curl_close($ch);
} elseif ($route == 'reports') {
    echo file_get_contents($rpt_base . '/analytics.php');
} else {
    echo json_encode(["status" => "error", "message" => "Route not found"]);
}
?>