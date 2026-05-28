<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
require_once __DIR__ . '/config.php'; // Local copy for Docker
$conn = get_db_connection("erp_auth");

// Get the data sent from the frontend
$fullname = $_POST['fullname'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (!$email || !$password) {
    echo json_encode(["status" => "error", "message" => "Missing fields."]);
    exit;
}

// 1. Check if the email already exists
$check = $conn->prepare("SELECT id FROM users WHERE username=?");
$check->bind_param("s", $email);
$check->execute();
if ($check->get_result()->num_rows > 0) {
    echo json_encode(["status" => "error", "message" => "Email is already registered!"]);
    exit;
}

// 2. Ensure the 'Customer' role exists in the database
$role_result = $conn->query("SELECT id FROM roles WHERE role_name='Customer'");
if($role_result->num_rows == 0) {
    $conn->query("INSERT INTO roles (role_name, description) VALUES ('Customer', 'Online Shopper')");
    $role_id = $conn->insert_id;
} else {
    $role_id = $role_result->fetch_assoc()['id'];
}

// 3. Insert the new user (We use their Email as their Username)
$stmt = $conn->prepare("INSERT INTO users (username, password_hash, role_id) VALUES (?, ?, ?)");
$stmt->bind_param("ssi", $email, $password, $role_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "Account created successfully!"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error."]);
}
?>