<div class="sidebar">
    <h3><i class="fa fa-book"></i> BookHive Admin</h3>
    <ul class="nav nav-pills nav-stacked">
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : '' ?>">
            <a href="admin.php"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'inventory_crud.php' ? 'active' : '' ?>">
            <a href="inventory_crud.php"><i class="fa fa-book"></i> Inventory</a></li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'staff_crud.php' ? 'active' : '' ?>">
            <a href="staff_crud.php"><i class="fa fa-user-secret"></i> Manage Staff</a></li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'customer_crud.php' ? 'active' : '' ?>">
            <a href="customer_crud.php"><i class="fa fa-users"></i> Customer Accounts</a></li>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'reports.php' ? 'active' : '' ?>">
            <a href="reports.php"><i class="fa fa-bar-chart"></i> Reports</a></li>
        <li><a href="login.php" style="color:#e74c3c; margin-top:20px;"><i class="fa fa-sign-out"></i> Logout</a></li>
    </ul>
</div>