<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$result = $conn->query("SELECT * FROM products ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Products</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Products</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:10px;">
            <h3 style="margin:0; font-size:16px;">All Products</h3>
            <div style="display:flex; gap:10px;">
                <input type="text" id="searchBox" onkeyup="filterTable()" placeholder="Search by name, brand, color..." style="padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; min-width:220px;">
                <a href="add_product.php"><button type="button" style="margin-top:0;">+ Add Product</button></a>
            </div>
        </div>

        <table id="productsTable">
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Details</th>
                <th>Purchase Price</th>
                <th>Sale Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Action</th>
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
                    <td><?php echo $row['purchase_price']; ?></td>
                    <td><?php echo $row['sale_price']; ?></td>
                    <td><?php echo $row['stock_quantity']; ?></td>
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
                    <td>
                        <a href="edit_product.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_product.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this product?')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        function filterTable() {
            var input = document.getElementById("searchBox");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("productsTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var rowText = rows[i].textContent || rows[i].innerText;
                if (rowText.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>

</div>
</body>
</html>