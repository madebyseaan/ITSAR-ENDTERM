<?php
require_once __DIR__ . '/../config.php'; // Adjust the path to config.php as needed
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
$action = $_REQUEST['action'] ?? '';

// HELPER: Get Role ID
function getRoleId($conn, $role_name) {
    $stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
    $stmt->bind_param("s", $role_name);
    $stmt->execute();
    $res = $stmt->get_result();
    return ($row = $res->fetch_assoc()) ? $row['id'] : 2; 
}

// 1. LOGIN LOGIC (Returns JSON for smooth AJAX redirection)
if ($action == 'login') {
    $login_input = $_POST['username'] ?? ''; 
    $password_input = $_POST['password'] ?? '';

    $stmt = $conn->prepare("SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE (users.email = ? OR users.username = ?) AND (users.password = ? OR users.password_hash = ?)");
    $stmt->bind_param("ssss", $login_input, $login_input, $password_input, $password_input);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($row = $res->fetch_assoc()) {
        // Add these three lines to remember the user!
        session_start();
        $_SESSION['user_id'] = $row['id'];
        $_SESSION['role_name'] = $row['role_name'];

        echo json_encode(["status" => "success", "role" => $row['role_name']]);
    } else {
        echo json_encode(["status" => "error", "message" => "Invalid credentials. Please try again."]);
    }
    exit;
}

// 2. CUSTOMER REGISTRATION
if ($action == 'register') {
    $role_id = getRoleId($conn, 'Customer'); // Automatically assigns the Customer role
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $_POST['fullname'], $_POST['email'], $_POST['password'], $role_id);
    $stmt->execute();
    
    // Redirect to login with a success flag
    header("Location: ../frontend/login.php?registered=true");
    exit;
}

// 3. FORGOT PASSWORD (Mockup for Capstone Defense)
if ($action == 'reset') {
    // In a live system, this would trigger an SMTP email. For a capstone, a UI message is sufficient.
    header("Location: ../frontend/login.php?reset=true");
    exit;
}
?>