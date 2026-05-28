<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BookHive | Admin Dashboard</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 240px; padding-top: 20px; z-index: 100;}
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; border-radius: 0; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #34495e; color: #fff; border-left: 4px solid #3498db; }
        
        .main-content { margin-left: 240px; padding: 30px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px; }
        
        #stats-container .col-md-3 { float: left !important; width: 25% !important; }
        
        .stats-card { text-align: center; border-radius: 12px; }
        .activity-feed { list-style: none; padding: 0; }
        .activity-feed li { padding: 15px 0; border-bottom: 1px solid #eee; font-size: 14px; }
        .chart-container { position: relative; height: 250px; width: 100%; }
    </style>
</head>
<body>

<?php include 'includes/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="card">
            <h2 style="margin:0;">Dashboard Overview</h2>
            <p class="text-muted">Welcome back, Admin. Here is your bookstore's health status.</p>
        </div>

        <div class="row" id="stats-container">
            <div class="col-md-3"><div class="card stats-card"><h3>₱5,420</h3><p class="text-muted">Total Sales</p></div></div>
            <div class="col-md-3"><div class="card stats-card"><h3>1,240</h3><p class="text-muted">Books Sold</p></div></div>
            <div class="col-md-3"><div class="card stats-card"><h3 class="text-danger">5</h3><p class="text-muted">Low Stock Alerts</p></div></div>
            <div class="col-md-3"><div class="card stats-card"><h3>28</h3><p class="text-muted">Pending Orders</p></div></div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <h4>Weekly Revenue Overview</h4>
                    <div class="chart-container">
                        <canvas id="dashboardChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-8">
                <div class="card" id="orders-container">
                    <h4>Recent Customer Orders</h4>
                    <table class="table table-hover" style="margin-top:15px;">
                        <thead><tr><th>Customer</th><th>Book</th><th>Total</th><th>Status</th></tr></thead>
                        <tbody>
                            <tr><td>Maria Cruz</td><td>PHP for Beginners</td><td>₱25.00</td><td><span class="label label-success">Paid</span></td></tr>
                            <tr><td>Juan Santos</td><td>Advanced Microservices</td><td>₱40.00</td><td><span class="label label-warning">Pending</span></td></tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card" id="feed-container">
                    <h4>System Activity Feed</h4>
                    <ul class="activity-feed">
                        <?php
                        require_once __DIR__ . '/config.php'; // Local copy
                        $logConn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_auth");
                        $logQuery = "SELECT action_message, created_at FROM system_logs ORDER BY id DESC LIMIT 5";
                        $logRes = $logConn->query($logQuery);
                        if ($logRes && $logRes->num_rows > 0) {
                            while ($log = $logRes->fetch_assoc()) {
                                $timeAgo = date("M j, g:i a", strtotime($log['created_at']));
                                echo "<li>{$log['action_message']} <small class='text-muted pull-right'>{$timeAgo}</small></li>";
                            }
                        } else {
                            echo "<li class='text-muted text-center'>No recent activity.</li>";
                        }
                        $logConn->close();
                        ?>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/jquery.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const token = localStorage.getItem('jwt_token');
            console.log("1. Checking Token:", token ? "Token Found!" : "MISSING TOKEN!");

            if (!token) {
                console.error("Halting Chart: No JWT Token found in localStorage. You must log out and log back in.");
                return;
            }

            console.log("2. Fetching data from API Gateway...");
            fetch('/api/gateway.php?route=reports', {
                method: 'GET',
                headers: { 
                    'Authorization': 'Bearer ' + token,
                    'Content-Type': 'application/json'
                }
            })
            .then(response => {
                console.log("3. Gateway Response Status:", response.status);
                if (!response.ok) throw new Error("Gateway blocked the request. Status: " + response.status);
                return response.json();
            })
            .then(result => {
                console.log("4. Data Received:", result);
                if (result.status === 'success' && result.data.length > 0) {
                    const labels = result.data.map(item => item.date).reverse();
                    const revenues = result.data.map(item => parseFloat(item.daily_revenue)).reverse();

                    const ctx = document.getElementById('dashboardChart').getContext('2d');
                    new Chart(ctx, {
                        type: 'line',
                        data: {
                            labels: labels,
                            datasets: [{
                                label: 'Revenue (₱)',
                                data: revenues,
                                borderColor: 'rgba(52, 152, 219, 1)',
                                backgroundColor: 'rgba(52, 152, 219, 0.2)',
                                borderWidth: 2,
                                fill: true,
                                tension: 0.4
                            }]
                        },
                        options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } } }
                    });
                    console.log("5. Chart rendered successfully!");
                } else {
                    console.warn("API returned success, but the data array is empty.");
                }
            })
            .catch(error => console.error("CRITICAL ERROR:", error));

            setInterval(function(){
                let t = new Date().getTime();
                $('#stats-container').load(location.href + " #stats-container > *");
                $('#orders-container').load(location.href + " #orders-container > *");
                $('#feed-container').load(location.href + " #feed-container > *");
            }, 5000);
        });
    </script>
</body>
</html>