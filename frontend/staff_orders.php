<?php
session_start();
$role = $_SESSION['role_name'] ?? '';
// Kick out anyone who isn't Fulfillment or Supervisor
if ($role !== 'Fulfillment' && $role !== 'Supervisor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Order Fulfillment | BookHive</title>
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
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
    </style>
</head>
<body>
    <?php include 'includes/staff_sidebar.php'; ?>

    <div class="main-content">
        <div class="card">
            <h3><i class="fa fa-truck"></i> Online Order Fulfillment</h3>
            <p class="text-muted">Pack items and update statuses for OTC collection.</p>
            
            <table class="table table-hover" style="margin-top: 20px;">
                <thead><tr><th>Order ID</th><th>Customer Name</th><th>Total</th><th>Status</th><th>Action</th></tr></thead>
                <tbody>
                <?php
                    require_once __DIR__ . '/config.php'; // Local copy for Docker
                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");

                    // 1. Fetch orders directly
                    $query = "SELECT id, customer_id, total_amount, status_id FROM orders ORDER BY id DESC";
                    $res = $conn->query($query);

                    while($row = $res->fetch_assoc()) {
                        // Fetch status name
                        $statusRes = $conn->query("SELECT status_name FROM order_statuses WHERE id = " . $row['status_id']);
                        $statusRow = $statusRes->fetch_assoc();
                        $statusName = $statusRow['status_name'];
                        $statusClass = $statusName == 'Completed' ? 'label-success' : 'label-warning';

                        // 2. DIRECT FETCH: Pull name from erp_auth explicitly
                        $customerName = "Walk-in Customer";
                        if ($row['customer_id'] > 1) {
                            $userConn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
                            $userRes = $userConn->query("SELECT fullname FROM users WHERE id = " . $row['customer_id']);
                            if ($userRow = $userRes->fetch_assoc()) {
                                $customerName = $userRow['fullname'];
                            }
                            $userConn->close();
                        }

                        $btn = $statusName == 'Pending' 
                               ? "<button class='btn btn-xs btn-success update-btn' data-id='{$row['id']}' style='border-radius:4px;'>Mark Completed</button>" 
                               : "<span class='text-muted'><i class='fa fa-check'></i> Done</span>";

                        echo "<tr>
                                <td>#00{$row['id']}</td>
                                <td>{$customerName}</td>
                                <td>₱{$row['total_amount']}</td>
                                <td><span class='label {$statusClass}'>{$statusName}</span></td>
                                <td>{$btn}</td>
                            </tr>";
                    }
                    $conn->close();
                ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="js/jquery.js"></script>
    <script>
        $('.update-btn').click(function(){
            let id = $(this).data('id');
            let btn = $(this);
            btn.html('<i class="fa fa-spinner fa-spin"></i>');
            
            // --- FIX: Grab the JWT token from localStorage ---
            let token = localStorage.getItem('jwt_token');
            // -------------------------------------------------

            // --- FIX: Route through Gateway with Headers ---
            $.ajax({
                url: "/api/gateway.php?route=update_order",
                type: "POST",
                headers: {
                    'Authorization': 'Bearer ' + token
                },
                data: { order_id: id, status_id: 2 },
                success: function(res) {
                    location.reload();
                },
                error: function(xhr) {
                    console.error("Error updating order:", xhr.responseText);
                    alert("Failed to update status. Check console.");
                    btn.html('Mark Completed');
                }
            });
            // -----------------------------------------------
        });
    </script>
</body>
</html>