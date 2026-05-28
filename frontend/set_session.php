<?php
session_start();
header('Content-Type: application/json');

$user_id = $_POST['user_id'] ?? null;
$role_name = $_POST['role_name'] ?? null;
$token = $_POST['token'] ?? null;

if ($user_id && $role_name) {
    $_SESSION['user_id'] = $user_id;
    $_SESSION['role_name'] = $role_name;
    $_SESSION['jwt_token'] = $token;
    echo json_encode(["status" => "success"]);
} else {
    echo json_encode(["status" => "error", "message" => "Missing session parameters"]);
}
?>
