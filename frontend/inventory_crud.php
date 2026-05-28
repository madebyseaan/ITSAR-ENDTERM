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
    <title>BookHive | Inventory Management</title>
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/font-awesome.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background: #f4f7f6; margin: 0; }
        .sidebar { height: 100vh; background: #2c3e50; color: white; position: fixed; width: 240px; padding-top: 20px; z-index: 100;}
        .sidebar h3 { text-align: center; color: #fff; margin-bottom: 30px; font-weight: 600; }
        .sidebar .nav>li>a { color: #bdc3c7; padding: 15px 25px; font-weight: 500; transition: 0.3s; border-radius: 0; }
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
            <h3>Inventory Management</h3>
            <button class="btn btn-modern" data-toggle="modal" data-target="#addModal">+ Add New Book</button>
            
            <table class="table table-striped table-hover" style="margin-top: 20px;">
                <thead>
                    <tr><th>Title</th><th>Author</th><th>Price</th><th>Stock</th><th>Action</th></tr>
                </thead>
                <tbody id="inventoryTable">
                    <?php
                    require_once __DIR__ . '/config.php'; // Local copy for Docker
                    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, "erp_inventory");
                    $res = $conn->query("SELECT * FROM books");
                    while($row = $res->fetch_assoc()) {
                        echo "<tr>
                                <td>{$row['title']}</td>
                                <td>{$row['author']}</td>
                                <td>₱{$row['price']}</td>
                                <td>{$row['stock_quantity']}</td>
                                <td>
                                    <button class='btn btn-xs btn-warning' data-toggle='modal' data-target='#editModal{$row['id']}'>Edit</button>
                                    <a href='../api/inventory_handler.php?action=delete&id={$row['id']}' class='btn btn-xs btn-danger delete-btn'>Delete</a>
                                </td>
                              </tr>";
                        
                        echo "<div class='modal fade' id='editModal{$row['id']}' tabindex='-1'>
                                <div class='modal-dialog'><div class='modal-content'>
                                    <form class='ajax-form' action='../api/inventory_handler.php' method='POST'>
                                        <div class='modal-header' style='background:#2c3e50; color:white;'>
                                            <button type='button' class='close' data-dismiss='modal' style='color:white; opacity:1;'>&times;</button>
                                            <h4 class='modal-title'>Edit Book</h4>
                                        </div>
                                        <div class='modal-body'>
                                            <input type='hidden' name='action' value='edit'>
                                            <input type='hidden' name='id' value='{$row['id']}'>
                                            <div class='form-group'><label>Title</label><input type='text' name='title' class='form-control' value='{$row['title']}' required></div>
                                            <div class='form-group'><label>Author</label><input type='text' name='author' class='form-control' value='{$row['author']}' required></div>
                                            <div class='row'>
                                                <div class='col-md-6'><div class='form-group'><label>Price</label><input type='number' name='price' class='form-control' value='{$row['price']}' step='0.01' required></div></div>
                                                <div class='col-md-6'><div class='form-group'><label>Stock</label><input type='number' name='stock' class='form-control' value='{$row['stock_quantity']}' required></div></div>
                                            </div>
                                        </div>
                                        <div class='modal-footer'><button type='submit' class='btn btn-warning'>Update Changes</button></div>
                                    </form>
                                </div></div>
                            </div>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="addModal" tabindex="-1">
        <div class="modal-dialog"><div class="modal-content">
            <form class="ajax-form" action="../api/inventory_handler.php" method="POST">
                <div class="modal-header" style="background:#2c3e50; color:white;">
                    <button type="button" class="close" data-dismiss='modal' style='color:white; opacity:1;'>&times;</button>
                    <h4 class="modal-title">Add New Book</h4>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="action" value="add">
                    <div class="form-group"><label>Title</label><input type='text' name='title' class='form-control' required></div>
                    <div class="form-group"><label>Author</label><input type='text' name='author' class='form-control' required></div>
                    <div class="form-group"><label>Price</label><input type='number' name='price' class='form-control' step='0.01' required></div>
                    <div class="form-group"><label>Stock</label><input type='number' name='stock' class='form-control' required></div>
                </div>
                <div class="modal-footer"><button type="submit" class="btn btn-success" style="background:#2ecc71; border:none;">Save Book</button></div>
            </form>
        </div></div>
    </div>

    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script>
        // Real-time AJAX Submission
        $('.ajax-form').submit(function(e){
            e.preventDefault();
            $.post($(this).attr('action'), $(this).serialize(), function(){
                location.reload(); 
            });
        });

        // Real-time AJAX Deletion
        $('.delete-btn').click(function(e){
            e.preventDefault();
            if(confirm("Are you sure you want to delete this book?")) {
                $.get($(this).attr('href'), function(){
                    location.reload();
                });
            }
        });
    </script>
</body>
</html>