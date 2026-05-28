<?php
// Environment-aware configuration
$is_production = (getenv('APP_ENV') === 'production') || file_exists('/.dockerenv'); 

if ($is_production) {
    define('DB_HOST', getenv('DB_HOST') ?: 'database');
    define('DB_USER', getenv('DB_USER') ?: 'root');
    define('DB_PASS', getenv('DB_PASS') ?: 'BookHive123!'); // Using your set password
} else {
    define('DB_HOST', '127.0.0.1');
    define('DB_USER', 'root');
    define('DB_PASS', '');
}

// RESILIENT CONNECTION HELPER
function get_db_connection($dbname) {
    $attempts = 0;
    $max_attempts = 5;
    
    while ($attempts < $max_attempts) {
        $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, $dbname);
        if (!$conn->connect_error) {
            return $conn;
        }
        $attempts++;
        sleep(2); // Wait 2 seconds before retrying
    }
    
    // If all fail, return JSON error and exit
    header('Content-Type: application/json');
    echo json_encode(["status" => "error", "message" => "Database '$dbname' is currently unavailable. Please refresh in a moment."]);
    exit;
}

// Suppress HTML errors in production to keep JSON clean
if ($is_production) {
    ini_set('display_errors', 0);
    error_reporting(0);
}
?>