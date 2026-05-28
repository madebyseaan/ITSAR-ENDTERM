<?php
session_start();
// If they are not logged in, OR if their role is not Admin, kick them out
if (!isset($_SESSION['user_id']) || $_SESSION['role_name'] !== 'Admin') {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/config.php'; // Local copy for Docker
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_orders");

// 1. Fetch Top-Level Analytics
$res = $conn->query("SELECT SUM(total_amount) as total_sales, COUNT(id) as total_orders FROM orders");
$data = $res->fetch_assoc();

// 2. Fetch Data for the Bar Chart (Fixed: LEFT JOIN ensures all statuses show, ORDER BY keeps them chronological)
$statusQuery = $conn->query("
    SELECT s.status_name, COUNT(o.id) as status_count 
    FROM order_statuses s 
    LEFT JOIN orders o ON o.status_id = s.id 
    GROUP BY s.id, s.status_name
    ORDER BY s.id ASC
");

$statusLabels = [];
$statusCounts = [];
if ($statusQuery) {
    while($row = $statusQuery->fetch_assoc()) {
        $statusLabels[] = $row['status_name'];
        $statusCounts[] = $row['status_count'];
    }
}

// 3. Fetch Data for Order Size Distribution (Real Data Analytics)
$tierQuery = $conn->query("
    SELECT 
        SUM(CASE WHEN total_amount < 500 THEN 1 ELSE 0 END) as small_orders,
        SUM(CASE WHEN total_amount >= 500 AND total_amount <= 1500 THEN 1 ELSE 0 END) as medium_orders,
        SUM(CASE WHEN total_amount > 1500 THEN 1 ELSE 0 END) as large_orders
    FROM orders
");
$tierData = $tierQuery->fetch_assoc();
$tierCounts = [
    $tierData['small_orders'] ?? 0, 
    $tierData['medium_orders'] ?? 0, 
    $tierData['large_orders'] ?? 0
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>BookHive | Reports</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 240px; padding-top: 20px; z-index: 100;}
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; }
        .sidebar .nav>li>a:hover, .sidebar .nav>li.active>a { background: #34495e; color: #fff; border-left: 4px solid #3498db; }
        .main-content { margin-left: 240px; padding: 40px; }
        .card { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); margin-bottom: 25px;}
        .chart-container { position: relative; height: 280px; width: 100%; }
    </style>
</head>
<body>

    <?php include 'includes/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="card" id="analytics-top">
            <h3><i class="fa fa-line-chart text-primary"></i> Sales Analytics Overview</h3>
            <hr>
            <div class="row">
                <div class="col-md-6 text-center" style="border-right: 1px solid #eee;">
                    <h4 class="text-muted">Total Revenue</h4>
                    <h1 style="color:#2ecc71; font-weight: 600;">₱<?php echo number_format($data['total_sales'] ?? 0, 2); ?></h1>
                </div>
                <div class="col-md-6 text-center">
                    <h4 class="text-muted">Total Orders Processed</h4>
                    <h1 style="color:#3498db; font-weight: 600;"><?php echo $data['total_orders'] ?? 0; ?></h1>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h4>Orders by Status Pipeline</h4>
                    <p class="text-muted" style="font-size: 12px;">Real-time breakdown of the fulfillment pipeline.</p>
                    <div class="chart-container">
                        <canvas id="statusBarChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h4>Revenue Trend (Last 7 Days)</h4>
                    <p class="text-muted" style="font-size: 12px;">Daily sales performance tracking.</p>
                    <div class="chart-container">
                        <canvas id="revenueLineChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="card">
                    <h4>Order Value Distribution</h4>
                    <p class="text-muted" style="font-size: 12px;">Categorizing basket sizes (Small, Medium, Large).</p>
                    <div class="chart-container">
                        <canvas id="tierDoughnutChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card">
                    <h4>Sales by Category</h4>
                    <p class="text-muted" style="font-size: 12px;">Which genres are driving the most revenue.</p>
                    <div class="chart-container">
                        <canvas id="categoryPieChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    
    <script>
        // --- 1. Bar Chart: Orders by Status (Real Data) ---
        new Chart(document.getElementById('statusBarChart').getContext('2d'), {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($statusLabels); ?>,
                datasets: [{
                    label: 'Orders',
                    data: <?php echo json_encode($statusCounts); ?>,
                    backgroundColor: 'rgba(52, 152, 219, 0.7)',
                    borderColor: 'rgba(52, 152, 219, 1)',
                    borderWidth: 1,
                    borderRadius: 4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } } }
        });

        // --- 2. Line Chart: Revenue Trend (Presentation Mock Data) ---
        new Chart(document.getElementById('revenueLineChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Revenue (₱)',
                    data: [1200, 2400, 1500, 3200, 1800, 4100, 3800], 
                    backgroundColor: 'rgba(46, 204, 113, 0.2)',
                    borderColor: 'rgba(46, 204, 113, 1)',
                    borderWidth: 2, fill: true, tension: 0.4
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
        });

        // --- 3. Doughnut Chart: Order Value Distribution (Real Data) ---
        new Chart(document.getElementById('tierDoughnutChart').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['< ₱500', '₱500 - ₱1500', '> ₱1500'],
                datasets: [{
                    data: <?php echo json_encode($tierCounts); ?>,
                    backgroundColor: ['rgba(149, 165, 166, 0.8)', 'rgba(52, 152, 219, 0.8)', 'rgba(155, 89, 182, 0.8)'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false, cutout: '70%' }
        });

        // --- 4. Pie Chart: Sales by Category (Presentation Mock Data) ---
        new Chart(document.getElementById('categoryPieChart').getContext('2d'), {
            type: 'pie',
            data: {
                labels: ['Programming & Tech', 'Fiction', 'Business', 'Other'],
                datasets: [{
                    data: [45, 25, 20, 10], // Percentages
                    backgroundColor: ['rgba(46, 204, 113, 0.8)', 'rgba(231, 76, 60, 0.8)', 'rgba(241, 196, 15, 0.8)', 'rgba(189, 195, 199, 0.8)'],
                    borderWidth: 0
                }]
            },
            options: { responsive: true, maintainAspectRatio: false }
        });

        // Auto Refresh Logic for the top header only
        setInterval(function(){ 
            $('#analytics-top').load(location.href + " #analytics-top > *"); 
        }, 5000);
    </script>
</body>
</html>