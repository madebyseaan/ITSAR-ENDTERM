<?php
// Use robust path inclusions that work both inside Docker and Local XAMPP
if (file_exists(dirname(__DIR__) . '/config.php')) {
    require_once dirname(__DIR__) . '/config.php';
} else {
    require_once dirname(__DIR__, 2) . '/config.php';
}


function logSystemActivity($message) {
    // We store all logs in the central auth database
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
    
    if ($conn->connect_error) return;

    $stmt = $conn->prepare("INSERT INTO system_logs (action_message) VALUES (?)");
    $stmt->bind_param("s", $message);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}
?>