<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$report = isset($_GET['report']) ? $_GET['report'] : 'daily';

function tabBtn($report, $current, $label) {
    $active = ($report == $current);
    $style = $active
        ? "background:var(--brick); color:#fff; border:none;"
        : "background:#fff; color:var(--ink); border:1px solid var(--border);";
    echo "<a href='reports.php?report=$report'><button type='button' style='margin-top:0; $style'>$label</button></a>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Reports</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Reports</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:10px;">
        <?php
        tabBtn('daily', $report, 'Daily');
        tabBtn('weekly', $report, 'Weekly');
        tabBtn('monthly', $report, 'Monthly');
        ?>
    </div>
    <div style="display:flex; gap:10px; flex-wrap:wrap; margin-bottom:24px;">
        <?php
        tabBtn('best_selling', $report, 'Best Selling');
        tabBtn('stock', $report, 'Stock Report');
        tabBtn('low_stock', $report, 'Low Stock');
        tabBtn('out_of_stock', $report, 'Out of Stock');
        ?>
    </div>

    <?php if ($report == 'daily') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Daily Sales Report (Today)</h3></div>
            <?php $result = $conn->query("SELECT * FROM bills WHERE DATE(bill_date) = CURDATE() ORDER BY bill_date DESC"); ?>
            <table>
                <tr><th>Bill #</th><th>Customer</th><th>Time</th><th>Total</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="4" style="text-align:center; color:var(--ink-soft);">No sales today.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr><td><?php echo $row['id']; ?></td><td><?php echo $row['customer_name']; ?></td><td><?php echo $row['bill_date']; ?></td><td><?php echo $row['grand_total']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'weekly') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Weekly Sales Report (Last 7 Days)</h3></div>
            <?php $result = $conn->query("SELECT * FROM bills WHERE bill_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) ORDER BY bill_date DESC"); ?>
            <table>
                <tr><th>Bill #</th><th>Customer</th><th>Date</th><th>Total</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="4" style="text-align:center; color:var(--ink-soft);">No sales this week.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr><td><?php echo $row['id']; ?></td><td><?php echo $row['customer_name']; ?></td><td><?php echo $row['bill_date']; ?></td><td><?php echo $row['grand_total']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'monthly') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Monthly Sales Report (This Month)</h3></div>
            <?php $result = $conn->query("SELECT * FROM bills WHERE MONTH(bill_date) = MONTH(CURDATE()) AND YEAR(bill_date) = YEAR(CURDATE()) ORDER BY bill_date DESC"); ?>
            <table>
                <tr><th>Bill #</th><th>Customer</th><th>Date</th><th>Total</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="4" style="text-align:center; color:var(--ink-soft);">No sales this month.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr><td><?php echo $row['id']; ?></td><td><?php echo $row['customer_name']; ?></td><td><?php echo $row['bill_date']; ?></td><td><?php echo $row['grand_total']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'best_selling') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Best Selling Products</h3></div>
            <?php $result = $conn->query("SELECT product_description, SUM(quantity) AS total_qty, SUM(line_total) AS total_sales FROM bill_items GROUP BY product_description ORDER BY total_qty DESC LIMIT 20"); ?>
            <table>
                <tr><th>Product</th><th>Total Quantity Sold</th><th>Total Sales</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="3" style="text-align:center; color:var(--ink-soft);">No sales yet.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr><td><?php echo $row['product_description']; ?></td><td><?php echo $row['total_qty']; ?></td><td><?php echo $row['total_sales']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'stock') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Full Stock Report</h3></div>
            <?php $result = $conn->query("SELECT * FROM products ORDER BY category, id"); ?>
            <table>
                <tr><th>Category</th><th>Details</th><th>Stock</th></tr>
                <?php while ($row = $result->fetch_assoc()) {
                    $label = $row['category'] == 'Paint' ? $row['brand'] . " - " . $row['paint_type'] . " - " . $row['pack_size'] . " - " . $row['color_name'] : $row['product_name'];
                ?>
                    <tr><td><?php echo $row['category']; ?></td><td><?php echo $label; ?></td><td><?php echo $row['stock_quantity']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'low_stock') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Low Stock Report</h3></div>
            <?php $result = $conn->query("SELECT * FROM products WHERE stock_quantity > 0 AND stock_quantity < min_quantity"); ?>
            <table>
                <tr><th>Category</th><th>Details</th><th>Stock</th><th>Min Qty</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="4" style="text-align:center; color:var(--ink-soft);">No low stock items.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) {
                    $label = $row['category'] == 'Paint' ? $row['brand'] . " - " . $row['paint_type'] . " - " . $row['pack_size'] . " - " . $row['color_name'] : $row['product_name'];
                ?>
                    <tr><td><?php echo $row['category']; ?></td><td><?php echo $label; ?></td><td><?php echo $row['stock_quantity']; ?></td><td><?php echo $row['min_quantity']; ?></td></tr>
                <?php } ?>
            </table>
        </div>

    <?php } elseif ($report == 'out_of_stock') { ?>
        <div class="panel">
            <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);"><h3 style="margin:0; font-size:16px;">Out of Stock Report</h3></div>
            <?php $result = $conn->query("SELECT * FROM products WHERE stock_quantity = 0"); ?>
            <table>
                <tr><th>Category</th><th>Details</th></tr>
                <?php if ($result->num_rows == 0) { ?><tr><td colspan="2" style="text-align:center; color:var(--ink-soft);">No out-of-stock items.</td></tr><?php } ?>
                <?php while ($row = $result->fetch_assoc()) {
                    $label = $row['category'] == 'Paint' ? $row['brand'] . " - " . $row['paint_type'] . " - " . $row['pack_size'] . " - " . $row['color_name'] : $row['product_name'];
                ?>
                    <tr><td><?php echo $row['category']; ?></td><td><?php echo $label; ?></td></tr>
                <?php } ?>
            </table>
        </div>
    <?php } ?>

</div>
</body>
</html>