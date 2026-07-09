<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$period = isset($_GET['period']) ? $_GET['period'] : 'daily';

if ($period == 'weekly') {
    $dateCondition = "b.bill_date >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
    $title = "Weekly Profit Report (Last 7 Days)";
} elseif ($period == 'monthly') {
    $dateCondition = "MONTH(b.bill_date) = MONTH(CURDATE()) AND YEAR(b.bill_date) = YEAR(CURDATE())";
    $title = "Monthly Profit Report (This Month)";
} else {
    $dateCondition = "DATE(b.bill_date) = CURDATE()";
    $title = "Daily Profit Report (Today)";
}

// Handle "Clear All" for this period
if (isset($_GET['clear_all'])) {
    $conn->query("
        UPDATE bill_items bi
        JOIN bills b ON bi.bill_id = b.id
        SET bi.profit_cleared = 1
        WHERE $dateCondition
    ");
    header("Location: profit.php?period=$period");
    exit();
}

// Handle clearing a single product description for this period
if (isset($_GET['clear_product'])) {
    $desc = $conn->real_escape_string($_GET['clear_product']);
    $conn->query("
        UPDATE bill_items bi
        JOIN bills b ON bi.bill_id = b.id
        SET bi.profit_cleared = 1
        WHERE $dateCondition AND bi.product_description = '$desc'
    ");
    header("Location: profit.php?period=$period");
    exit();
}

$sql = "
    SELECT
        bi.product_description,
        p.purchase_price,
        SUM(bi.quantity) AS total_qty,
        SUM(bi.line_total) AS total_revenue,
        SUM(bi.quantity * p.purchase_price) AS total_cost,
        SUM(bi.line_total - (bi.quantity * p.purchase_price)) AS total_profit
    FROM bill_items bi
    JOIN bills b ON bi.bill_id = b.id
    JOIN products p ON bi.product_id = p.id
    WHERE $dateCondition AND bi.profit_cleared = 0
    GROUP BY bi.product_description, p.purchase_price
    ORDER BY total_profit DESC
";

$result = $conn->query($sql);

$grand_revenue = 0;
$grand_cost = 0;
$grand_profit = 0;
$rows = [];

while ($row = $result->fetch_assoc()) {
    $rows[] = $row;
    $grand_revenue += $row['total_revenue'];
    $grand_cost += $row['total_cost'];
    $grand_profit += $row['total_profit'];
}

function tabBtn($p, $current, $label) {
    $active = ($p == $current);
    $style = $active
        ? "background:var(--brick); color:#fff; border:none;"
        : "background:#fff; color:var(--ink); border:1px solid var(--border);";
    echo "<a href='profit.php?period=$p'><button type='button' style='margin-top:0; $style'>$label</button></a>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Profit</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Profit</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px; margin-bottom:24px;">
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
            <?php
            tabBtn('daily', $period, 'Daily');
            tabBtn('weekly', $period, 'Weekly');
            tabBtn('monthly', $period, 'Monthly');
            ?>
        </div>

        <?php if (count($rows) > 0) { ?>
            <a href="profit.php?period=<?php echo $period; ?>&clear_all=1" onclick="return confirm('Clear ALL profit entries for this period? This will hide them from this report (your bills and stock are not affected).')">
                <button type="button" style="margin-top:0; background:var(--teal);">Clear All</button>
            </a>
        <?php } ?>
    </div>

    <div class="cards-grid" style="grid-template-columns: repeat(auto-fit, minmax(190px, 1fr)); margin-bottom:24px;">
        <div class="card">
            <div class="accentbar" style="background:#0F6E67;"></div>
            <div class="label">Total Revenue</div>
            <div class="value">Rs <?php echo number_format($grand_revenue, 0); ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#C97A1A;"></div>
            <div class="label">Total Cost</div>
            <div class="value">Rs <?php echo number_format($grand_cost, 0); ?></div>
        </div>
        <div class="card">
            <div class="accentbar" style="background:#B5432B;"></div>
            <div class="label">Total Profit</div>
            <div class="value">Rs <?php echo number_format($grand_profit, 0); ?></div>
        </div>
    </div>

    <div class="panel">
        <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);">
            <h3 style="margin:0; font-size:16px;"><?php echo $title; ?> — Product Breakdown</h3>
        </div>

        <table>
            <tr>
                <th>Product</th>
                <th>Qty Sold</th>
                <th>Purchase Price</th>
                <th>Revenue</th>
                <th>Cost</th>
                <th>Profit</th>
                <th>Action</th>
            </tr>

            <?php if (count($rows) == 0) { ?>
                <tr><td colspan="7" style="text-align:center; color:var(--ink-soft);">No sales in this period.</td></tr>
            <?php } ?>

            <?php foreach ($rows as $row) { ?>
                <tr>
                    <td><?php echo $row['product_description']; ?></td>
                    <td><?php echo $row['total_qty']; ?></td>
                    <td><?php echo number_format($row['purchase_price'], 2); ?></td>
                    <td><?php echo number_format($row['total_revenue'], 2); ?></td>
                    <td><?php echo number_format($row['total_cost'], 2); ?></td>
                    <td style="color:<?php echo $row['total_profit'] >= 0 ? '#0B534E' : '#B02A2A'; ?>; font-weight:600;">
                        <?php echo number_format($row['total_profit'], 2); ?>
                    </td>
                    <td>
                        <a href="profit.php?period=<?php echo $period; ?>&clear_product=<?php echo urlencode($row['product_description']); ?>" onclick="return confirm('Clear this product from the profit report?')">Clear</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
</body>
</html>
