<?php
require_once __DIR__ . '/../config.php'; // Adjust the path to config.php as needed
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
$action = $_REQUEST['action'] ?? '';

// Helper function to safely get the correct role_id from the roles table
function getRoleId($conn, $role_name) {
    $stmt = $conn->prepare("SELECT id FROM roles WHERE role_name = ?");
    $stmt->bind_param("s", $role_name);
    $stmt->execute();
    $res = $stmt->get_result();
    if($row = $res->fetch_assoc()) {
        return $row['id'];
    }
    return 2; // Default fallback ID if something goes wrong
}

// 1. ADD STAFF
if ($action == 'add') {
    // Convert the text role from the form into a role_id number
    $role_id = getRoleId($conn, $_POST['role']);
    
    // Notice we use role_id here now, NOT role!
    $stmt = $conn->prepare("INSERT INTO users (fullname, email, password, role_id) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $_POST['fullname'], $_POST['email'], $_POST['password'], $role_id);
    $stmt->execute();
    
    header("Location: ../frontend/staff_crud.php");
}

// 2. EDIT STAFF
if ($action == 'edit') {
    // Convert the text role from the form into a role_id number
    $role_id = getRoleId($conn, $_POST['role_name']);
    
    // Notice we use role_id here now, NOT role!
    $stmt = $conn->prepare("UPDATE users SET fullname=?, email=?, role_id=? WHERE id=?");
    $stmt->bind_param("ssii", $_POST['fullname'], $_POST['email'], $role_id, $_POST['id']);
    $stmt->execute();
    
    header("Location: ../frontend/staff_crud.php");
}

// 3. DELETE USER
if ($action == 'delete') {
    $conn->query("DELETE FROM users WHERE id = " . $_GET['id']);
    // This sends you back to whichever page you clicked delete from (Staff or Customer)
    header("Location: " . $_SERVER['HTTP_REFERER']); 
}
?>