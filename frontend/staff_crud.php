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
    <title>BookHive | Manage Staff</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 240px; padding-top: 20px; }
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #34495e; color: #fff; border-left: 4px solid #3498db; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .btn-modern { border-radius: 8px; padding: 10px 20px; border: none; background: #3498db; color: white; }
    </style>
</head>
<body>
    <?php include 'includes/admin_sidebar.php'; ?>
    <div class="main-content">
        <div class="card">
            <h3>Staff Accounts Management</h3>
            <button class="btn btn-modern" data-toggle="modal" data-target="#addStaffModal">+ Add New Staff</button>
            
            <table class="table table-striped table-hover" style="margin-top: 20px;">
                <thead><tr><th>Full Name</th><th>Email</th><th>Assigned Role</th><th>Action</th></tr></thead>
                <tbody>
                <?php
require_once __DIR__ . '/config.php'; // Local copy for Docker
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
$res = $conn->query("SELECT users.*, roles.role_name FROM users JOIN roles ON users.role_id = roles.id WHERE roles.role_name != 'Customer'");

if ($res) {
    while($row = $res->fetch_assoc()) {
        // Smart fallbacks: If fullname/email don't exist yet, it won't crash
        $id = $row['id'] ?? 0;
        $name = $row['fullname'] ?? $row['username'] ?? 'N/A'; 
        $email = $row['email'] ?? 'N/A';
        $role = $row['role_name'] ?? 'Unknown';
        
        echo "<tr>
                <td>{$name}</td>
                <td>{$email}</td>
                <td><span class='label label-info'>{$role}</span></td>
                <td>
                    <button class='btn btn-xs btn-warning' data-toggle='modal' data-target='#editModal{$id}'>Edit</button>
                    <a href='../api/user_handler.php?action=delete&id={$id}' class='btn btn-xs btn-danger'>Revoke Access</a>
                </td>
              </tr>";
              
        // Edit Modal (Fixed the double quote issue inside the echo string here)
        echo "<div class='modal fade' id='editModal{$id}' tabindex='-1'>
                <div class='modal-dialog'><div class='modal-content'>
                    <form action='../api/user_handler.php' method='POST'>
                        <div class='modal-header' style='background:#2c3e50; color:white;'>
                            <button type='button' class='close' data-dismiss='modal' style='color:white; opacity:1;'>&times;</button>
                            <h4 class='modal-title'>Edit Staff: {$name}</h4>
                        </div>
                        <div class='modal-body'>
                            <input type='hidden' name='action' value='edit'>
                            <input type='hidden' name='id' value='{$id}'>
                            <div class='form-group'><label>Full Name</label><input type='text' name='fullname' class='form-control' value='{$name}' required></div>
                            <div class='form-group'><label>Email / Username</label><input type='text' name='email' class='form-control' value='{$email}' required></div>
                            <div class='form-group'><label>Role</label>
                                <select name='role_name' class='form-control'>
                                <option value='Cashier'>Cashier (POS & Search)</option>
                                <option value='Fulfillment'>Order Fulfillment (Packing & Shipping)</option>
                                <option value='Stock_Clerk'>Stock Clerk (Inventory Management)</option>
                                <option value='Supervisor'>Shift Supervisor (Full Floor Access)</option>
                                <option value='Admin'>System Administrator</option>
                            </select>
                            </div>
                        </div>
                        <div class='modal-footer'><button type='submit' class='btn btn-warning'>Update Staff</button></div>
                    </form>
                </div></div>
            </div>";
    }
}
?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addStaffModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form action="../api/user_handler.php" method="POST">
                <div class="modal-header" style="background:#2c3e50; color:white;">
                    <button type="button" class="close" data-dismiss="modal" style="color:white; opacity:1;">&times;</button>
                    <h4 class="modal-title">Create Staff Account</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group"><label>Full Name</label><input type="text" name="fullname" class="form-control" required></div>
                    <div class="form-group"><label>Email</label><input type="email" name="email" class="form-control" required></div>
                    <div class="form-group"><label>Temporary Password</label><input type="password" name="password" class="form-control" required></div>
                    <div class="form-group"><label>Assign Role</label>
                        <select name="role" class="form-control">
                            <option value="Cashier">Cashier</option>
                            <option value="Fulfillment">Fulfillment</option>
                            <option value="Stock_Clerk">Stock Clerk</option>
                            <option value="Supervisor">Supervisor</option>
                            <option value="Admin">Administrator</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success" style="background:#2ecc71; border:none;">Create Account</button></div>
            </form>
        </div></div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
</body>
</html>