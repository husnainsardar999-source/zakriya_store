<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';

if ($filter == 'low') {
    $sql = "SELECT * FROM products WHERE stock_quantity > 0 AND stock_quantity < min_quantity ORDER BY id DESC";
} elseif ($filter == 'out') {
    $sql = "SELECT * FROM products WHERE stock_quantity = 0 ORDER BY id DESC";
} else {
    $sql = "SELECT * FROM products ORDER BY id DESC";
}

$result = $conn->query($sql);

$in_stock_count = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity >= min_quantity")->fetch_assoc()['total'];
$low_stock_count = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity > 0 AND stock_quantity < min_quantity")->fetch_assoc()['total'];
$out_stock_count = $conn->query("SELECT COUNT(*) AS total FROM products WHERE stock_quantity = 0")->fetch_assoc()['total'];
?>
<!DOCTYPE html>
<html>
<head>
    <title>Stock Management</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Stock</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div style="display:flex; gap:16px; margin-bottom:20px;">
        <a href="view_stock.php?filter=all" style="flex:1;">
            <div class="card" style="cursor:pointer;">
                <div class="label">In Stock</div>
                <div class="value"><?php echo $in_stock_count; ?></div>
            </div>
        </a>
        <a href="view_stock.php?filter=low" style="flex:1;">
            <div class="card" style="cursor:pointer;">
                <div class="label">Low Stock</div>
                <div class="value"><?php echo $low_stock_count; ?></div>
            </div>
        </a>
        <a href="view_stock.php?filter=out" style="flex:1;">
            <div class="card" style="cursor:pointer;">
                <div class="label">Out of Stock</div>
                <div class="value"><?php echo $out_stock_count; ?></div>
            </div>
        </a>
    </div>

    <div class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:10px;">
            <h3 style="margin:0; font-size:16px;">Stock Overview</h3>
            <a href="add_stock.php"><button type="button" style="margin-top:0;">+ Add New Stock</button></a>
        </div>

        <table>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Details</th>
                <th>Stock Quantity</th>
                <th>Min Quantity</th>
                <th>Status</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td>
                        <?php
                        if ($row['category'] == 'Paint') {
                            echo $row['brand'] . " - " . $row['paint_type'] . " - " . $row['pack_size'] . " - " . $row['color_name'];
                        } else {
                            echo $row['product_name'];
                        }
                        ?>
                    </td>
                    <td><?php echo $row['stock_quantity']; ?></td>
                    <td><?php echo $row['min_quantity']; ?></td>
                    <td>
                        <?php
                        if ($row['stock_quantity'] == 0) {
                            echo "<span class='pill pill-danger'>Out of Stock</span>";
                        } elseif ($row['stock_quantity'] < $row['min_quantity']) {
                            echo "<span class='pill pill-warn'>Low Stock</span>";
                        } else {
                            echo "<span class='pill pill-ok'>In Stock</span>";
                        }
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
</body>
</html>