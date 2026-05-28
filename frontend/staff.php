<?php
session_start();
// If they are not logged in, OR if they are just a Customer, kick them out
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] === 'Customer') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Staff Dashboard | BookHive</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #34495e; color: white; position: fixed; width: 240px; padding-top: 20px; }
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #2c3e50; color: #fff; border-left: 4px solid #3498db; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 20px;}
    </style>
</head>
<body>
    <?php include 'includes/staff_sidebar.php'; ?>

    <div class="main-content">
        <div class="card">
            <h2>Welcome to your Shift</h2>
            <p class="text-muted">Here are your pending tasks for today.</p>
        </div>
        
        <div class="row">
            <?php
            // Pull pending orders count
            require_once __DIR__ . '/config.php'; // Local copy for Docker
            $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");
            $res = $conn->query("SELECT COUNT(*) as pending FROM orders WHERE status_id = 1");
            $pending = $res->fetch_assoc()['pending'];
            ?>
            <div class="col-md-6">
                <div class="card text-center" style="border-top: 4px solid #e67e22;">
                    <h3><i class="fa fa-box text-warning"></i> <?php echo $pending; ?> Pending Orders</h3>
                    <p class="text-muted">Online orders waiting to be packed.</p>
                    <a href="staff_orders.php" class="btn btn-warning">Go to Fulfillment</a>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center" style="border-top: 4px solid #3498db;">
                    <h3><i class="fa fa-calculator text-primary"></i> POS Register</h3>
                    <p class="text-muted">Process walk-in customer purchases.</p>
                    <a href="staff_pos.php" class="btn btn-primary">Open Register</a>
                </div>
            </div>
        </div>
    </div>
</body>
</html>