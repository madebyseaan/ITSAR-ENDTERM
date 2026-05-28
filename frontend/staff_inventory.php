<?php
session_start();
$role = $_SESSION['role_name'] ?? '';
// Kick out anyone who isn't a Stock Clerk or Supervisor
if ($role !== 'Stock_Clerk' && $role !== 'Supervisor') {
    header("Location: login.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Stock Viewer | BookHive</title>
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
            <h3><i class="fa fa-search"></i> Inventory & Stock Viewer</h3>
            <table class="table table-striped table-hover" style="margin-top: 20px;">
                <thead><tr><th>ISBN</th><th>Title</th><th>Author</th><th>Price</th><th>Stock Status</th></tr></thead>
                <tbody>
                <?php
                require_once __DIR__ . '/config.php'; // Local copy for Docker
                $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_inventory");
                $res = $conn->query("SELECT * FROM books");
                while($row = $res->fetch_assoc()) {
                    $stockClass = $row['stock_quantity'] > 5 ? 'color: #2ecc71;' : 'color: #e74c3c; font-weight:bold;';
                    echo "<tr>
                            <td>{$row['isbn']}</td>
                            <td>{$row['title']}</td>
                            <td>{$row['author']}</td>
                            <td>₱{$row['price']}</td>
                            <td style='{$stockClass}'>{$row['stock_quantity']} in stock</td>
                          </tr>";
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>