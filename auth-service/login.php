<?php
// CRITICAL FIX: Start the session before doing anything else
session_start();
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/config.php'; 
$conn = get_db_connection("erp_auth");

$login_input = $_POST['username'] ?? ''; 
$password_input = $_POST['password'] ?? '';

$stmt = $conn->prepare("
    SELECT users.*, roles.role_name 
    FROM users 
    JOIN roles ON users.role_id = roles.id 
    WHERE (users.email = ? OR users.username = ?) 
      AND (users.password = ? OR users.password_hash = ?)
");

$stmt->bind_param("ssss", $login_input, $login_input, $password_input, $password_input);
$stmt->execute();
$res = $stmt->get_result();

if ($row = $res->fetch_assoc()) {
    // CRITICAL FIX: Save the user's details into the active session
    $_SESSION['user_id'] = $row['id'];
    $_SESSION['role_name'] = $row['role_name'];

    // --- JWT GENERATION ADDED HERE ---
    require_once __DIR__ . '/jwt_helper.php';
    $token = generate_jwt($row['id'], $row['role_name']);
    // ---------------------------------

    echo json_encode([
        "status" => "success",
        "role" => $row['role_name'],
        "user_id" => $row['id'],
        "token" => $token // <-- The frontend is now receiving the token!
    ]);
} else {
    echo json_encode([
        "status" => "error", 
        "message" => "Invalid email/username or password."
    ]);
}
?>