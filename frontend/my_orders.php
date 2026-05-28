<?php
session_start();
// Kick out unauthenticated users
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>My Orders | BookHive</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .navbar-custom { background-color: #ffffff; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
        .card { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-top: 40px;}
    </style>
</head>
<body>
    <nav class="navbar navbar-custom">
      <div class="container">
        <div class="navbar-header"><a class="navbar-brand" style="color: #2c3e50; font-weight:600;" href="shop.php"><i class="fa fa-book text-primary"></i> BookHive</a></div>
        <ul class="nav navbar-nav navbar-right">
          <li><a href="shop.php"><i class="fa fa-shopping-bag"></i> Continue Shopping</a></li>
          <li><a href="login.php"><i class="fa fa-sign-out"></i> Logout</a></li>
        </ul>
      </div>
    </nav>

    <div class="container">
        <div class="card">
            <h3>Order History</h3>
            <p class="text-muted">Track the status of your recent purchases.</p>
            <table class="table table-striped" style="margin-top:20px;">
                <thead><tr><th>Order ID</th><th>Total Amount</th><th>Status</th></tr></thead>
                <tbody>
                    <?php
                    // Use the actual logged-in user's ID
                    require_once __DIR__ . '/config.php'; // Local copy
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");
                    $customer_id = $_SESSION['user_id'];
                    $res = $conn->query("SELECT o.id, o.total_amount, s.status_name FROM orders o JOIN order_statuses s ON o.status_id = s.id WHERE o.customer_id = $customer_id ORDER BY o.id DESC");
                    
                    if($res->num_rows > 0) {
                        while($row = $res->fetch_assoc()) {
                            $statusClass = $row['status_name'] == 'Shipped' ? 'label-success' : 'label-warning';
                            echo "<tr>
                                    <td>#00{$row['id']}</td>
                                    <td>₱{$row['total_amount']}</td>
                                    <td><span class='label {$statusClass}'>{$row['status_name']}</span></td>
                                  </tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center text-muted'>You haven't placed any orders yet.</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>