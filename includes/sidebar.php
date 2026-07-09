<button class="mobile-toggle" onclick="document.querySelector('.sidebar').classList.toggle('open')">☰</button>
<div class="sidebar">
    <div class="brand">
        <div class="brand-mark"></div>
        <div class="brand-text">Zakriya Paint<span>Hardware & Interior</span></div>
    </div>

    <p><a href="<?php echo $base; ?>dashboard.php">Dashboard</a></p>

    <strong>Products</strong>
    <a href="<?php echo $base; ?>products/view_products.php">View Products</a>
    <a href="<?php echo $base; ?>products/add_product.php">Add Product</a>

    <strong>Customers</strong>
    <a href="<?php echo $base; ?>customers/view_customers.php">View Customers</a>
    <a href="<?php echo $base; ?>customers/add_customer.php">Add Customer</a>

    <strong>Billing</strong>
    <a href="<?php echo $base; ?>billing/new_bill.php?clear=1">New Bill</a>

    <strong>Stock</strong>
    <a href="<?php echo $base; ?>stock/view_stock.php">View Stock</a>
    <a href="<?php echo $base; ?>stock/add_stock.php">Add Stock</a>

    <strong>Sales History</strong>
    <a href="<?php echo $base; ?>sales_history/view_bills.php">View Bills</a>

    <strong>Reports</strong>
    <a href="<?php echo $base; ?>reports/reports.php">Reports</a>

    <strong>Settings</strong>
    <a href="<?php echo $base; ?>settings/settings.php">Settings</a>

    <div class="sidebar-footer">
        <div class="who">Logged in as <b><?php echo $_SESSION['username']; ?></b></div>
        <a href="<?php echo $base; ?>logout.php" class="logout-btn">Log Out</a>
    </div>
</div>

<div class="main-content">
