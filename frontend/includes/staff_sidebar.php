<?php
// Start the session so we know who is logged in
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// If the Admin somehow gets here, kick them back to admin.php
if (isset($_SESSION['role_name']) && $_SESSION['role_name'] === 'Admin') {
    header("Location: admin.php");
    exit;
}

$user_role = $_SESSION['role_name'] ?? 'Unknown'; 
?>

<div class="sidebar" style="background-color: #34495e;">
    <div style="padding: 15px; text-align: center; border-bottom: 1px solid #2c3e50; margin-bottom: 20px;">
        <h3 style="margin-top: 10px;"><i class="fa fa-desktop"></i> Staff Portal</h3>
        <span class="label label-info">Role: <?php echo htmlspecialchars($user_role); ?></span>
    </div>
    
    <ul class="nav nav-pills nav-stacked">
        <?php if ($user_role === 'Supervisor'): ?>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : '' ?>">
            <a href="staff.php"><i class="fa fa-home"></i> Staff Dashboard</a>
        </li>
        <?php endif; ?>

        <?php if ($user_role === 'Cashier' || $user_role === 'Supervisor'): ?>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'staff_pos.php' ? 'active' : '' ?>">
            <a href="staff_pos.php"><i class="fa fa-calculator"></i> POS Register</a>
        </li>
        <?php endif; ?>

        <?php if ($user_role === 'Fulfillment' || $user_role === 'Supervisor'): ?>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'staff_orders.php' ? 'active' : '' ?>">
            <a href="staff_orders.php"><i class="fa fa-truck"></i> Order Fulfillment</a>
        </li>
        <?php endif; ?>

        <?php if ($user_role === 'Stock_Clerk' || $user_role === 'Supervisor'): ?>
        <li class="<?= basename($_SERVER['PHP_SELF']) == 'staff_inventory.php' ? 'active' : '' ?>">
            <a href="staff_inventory.php"><i class="fa fa-search"></i> Stock Viewer</a>
        </li>
        <?php endif; ?>

        <li>
            <a href="login.php?logout=true" style="color:#e74c3c; margin-top:20px;">
                <i class="fa fa-sign-out"></i> End Shift
            </a>
        </li>
    </ul>
</div>