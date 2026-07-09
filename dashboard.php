<?php
session_start();
include 'config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: index.php");
    exit();
}

$total_products = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$total_customers = $conn->query("SELECT COUNT(*) AS total FROM customers")->fetch_assoc()['total'];
$total_bills = $conn->query("SELECT COUNT(*) AS total FROM bills")->fetch_assoc()['total'];

$today_sales = $conn->query("SELECT SUM(grand_total) AS total FROM bills WHERE DATE(bill_date) = CURDATE()")->fetch_assoc()['total'];
$today_sales = $today_sales ? $today_sales : 0;

$monthly_sales = $conn->query("SELECT SUM(grand_total) AS total FROM bills WHERE MONTH(bill_date) = MONTH(CURDATE()) AND YEAR(bill_date) = YEAR(CURDATE())")->fetch_assoc()['total'];
$monthly_sales = $monthly_sales ? $monthly_sales : 0;

$low_stock = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity > 0 AND stock_quantity < min_quantity")->fetch_assoc()['total'];
$out_of_stock = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity = 0")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Dashboard - Zakriya Store</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php $base = ""; include 'includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Dashboard</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div class="cards-grid">
        <div class="card">
            <div class="accentbar" style="background:#B5432B;"></div>
            <div class="label">Total Products</div>
            <div class="value"><?php echo $total_products; ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#0F6E67;"></div>
            <div class="label">Total Customers</div>
            <div class="value"><?php echo $total_customers; ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#C97A1A;"></div>
            <div class="label">Total Bills</div>
            <div class="value"><?php echo $total_bills; ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#0F6E67;"></div>
            <div class="label">Today's Sales</div>
            <div class="value"><?php echo number_format($today_sales, 0); ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#B5432B;"></div>
            <div class="label">Monthly Sales</div>
            <div class="value"><?php echo number_format($monthly_sales, 0); ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#C97A1A;"></div>
            <div class="label">Low Stock Products</div>
            <div class="value"><?php echo $low_stock; ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#B02A2A;"></div>
            <div class="label">Out of Stock Products</div>
            <div class="value"><?php echo $out_of_stock; ?></div>
        </div>
    </div>

</div>
</body>
</html>
