<?php
session_start();
// If they are not logged in, OR if their role is not Admin, kick them out
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BookHive | Customer Accounts</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 240px; padding-top: 20px; z-index: 100;}
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #34495e; color: #fff; border-left: 4px solid #3498db; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

    <?php include 'includes/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="card">
            <h3>Registered Customers</h3>
            <p class="text-muted">Customers register themselves. You can view or remove accounts here.</p>
            
            <table class="table table-striped table-hover" style="margin-top: 20px;">
                <thead><tr><th>Full Name</th><th>Email</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php
                require_once __DIR__ . '/config.php'; // Local copy for Docker
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
                
                // FIXED SQL: We must JOIN the roles table because 'users' only contains role_id, not role_name
                $query = "SELECT users.*, roles.role_name 
                          FROM users 
                          JOIN roles ON users.role_id = roles.id 
                          WHERE roles.role_name = 'Customer'";
                          
                $res = $conn->query($query);
                
                if ($res && $res->num_rows > 0) {
                    while($row = $res->fetch_assoc()) {
                        // Smart fallbacks to prevent errors if a user is missing a name
                        $name = $row['fullname'] ?? $row['username'] ?? 'N/A';
                        $email = $row['email'] ?? 'N/A';
                        $id = $row['id'];
                        
                        echo "<tr>
                                <td>{$name}</td>
                                <td>{$email}</td>
                                <td><span class='label label-success'>Active</span></td>
                                <td>
                                    <a href='../api/user_handler.php?action=delete&id={$id}' class='btn btn-xs btn-danger' onclick='return confirm(\"Are you sure you want to delete this customer?\");'>Delete Account</a>
                                </td>
                              </tr>";
                    }
                } else {
                    echo "<tr><td colspan='4' class='text-center text-muted' style='padding: 20px;'>No registered customers found.</td></tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>