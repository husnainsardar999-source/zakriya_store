<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
if (!isset($_SESSION['bill_customer_name'])) {
    $_SESSION['bill_customer_name'] = '';
}
if (!isset($_SESSION['bill_phone_number'])) {
    $_SESSION['bill_phone_number'] = '';
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['customer_name'])) {
        $_SESSION['bill_customer_name'] = $_POST['customer_name'];
    }
    if (isset($_POST['phone_number'])) {
        $_SESSION['bill_phone_number'] = $_POST['phone_number'];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['add_item'])) {
    $product_id = $_POST['product_id'];
    $quantity = $_POST['quantity'];
    $unit_price = $_POST['unit_price'];

    $product = $conn->query("SELECT * FROM products WHERE id = $product_id")->fetch_assoc();

    if ($product['category'] == 'Paint') {
        $description = $product['brand'] . " - " . $product['paint_type'] . " - " . $product['pack_size'] . " - " . $product['color_name'];
    } else {
        $description = $product['product_name'];
    }

    $line_total = $quantity * $unit_price;

    $_SESSION['cart'][] = [
        'product_id' => $product_id,
        'description' => $description,
        'quantity' => $quantity,
        'unit_price' => $unit_price,
        'line_total' => $line_total
    ];
}

if (isset($_GET['remove'])) {
    $index = $_GET['remove'];
    unset($_SESSION['cart'][$index]);
    $_SESSION['cart'] = array_values($_SESSION['cart']);
    header("Location: new_bill.php");
    exit();
}

if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
    $_SESSION['bill_customer_name'] = '';
    $_SESSION['bill_phone_number'] = '';
    header("Location: new_bill.php");
    exit();
}

$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['line_total'];
}

$products = $conn->query("SELECT * FROM products ORDER BY category, id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>New Bill</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>New Bill</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <p><a href="new_bill.php?clear=1">Start New Bill</a></p>

    <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start;">

        <div style="flex:1; min-width:320px;">
            <h3>Add Product to Bill</h3>
            <form method="POST" action="new_bill.php">
                <input type="hidden" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['bill_customer_name']); ?>">
                <input type="hidden" name="phone_number" value="<?php echo htmlspecialchars($_SESSION['bill_phone_number']); ?>">

                <label>Select Product:</label><br>
                <select name="product_id" id="product_select" onchange="updatePrice()" required>
                    <option value="">-- Select Product --</option>
                    <?php while ($p = $products->fetch_assoc()) {
                        $label = $p['category'] == 'Paint'
                            ? $p['brand'] . " - " . $p['paint_type'] . " - " . $p['pack_size'] . " - " . $p['color_name']
                            : $p['product_name'];
                    ?>
                        <option value="<?php echo $p['id']; ?>" data-price="<?php echo $p['sale_price']; ?>">
                            <?php echo $p['category'] . ": " . $label . " (Stock: " . $p['stock_quantity'] . ")"; ?>
                        </option>
                    <?php } ?>
                </select><br><br>

                <label>Quantity:</label><br>
                <input type="number" name="quantity" id="quantity" min="1" required><br><br>

                <label>Unit Price:</label><br>
                <input type="number" step="0.01" name="unit_price" id="unit_price" required><br><br>

                <button type="submit" name="add_item">+ Add Item</button>
            </form>
        </div>

        <div style="flex:1.3; min-width:340px;">
            <h3>Bill Details</h3>
            <form method="POST" action="save_bill.php">
                <label>Customer Name:</label><br>
                <input type="text" name="customer_name" value="<?php echo htmlspecialchars($_SESSION['bill_customer_name']); ?>" required><br><br>

                <label>Phone Number:</label><br>
                <input type="text" name="phone_number" value="<?php echo htmlspecialchars($_SESSION['bill_phone_number']); ?>"><br><br>

                <table>
                    <tr>
                        <th>Product</th>
                        <th>Qty</th>
                        <th>Price</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                    <?php if (count($_SESSION['cart']) == 0) { ?>
                        <tr><td colspan="5" style="text-align:center; color:#6B655C;">No items added yet</td></tr>
                    <?php } ?>
                    <?php foreach ($_SESSION['cart'] as $index => $item) { ?>
                        <tr>
                            <td><?php echo $item['description']; ?></td>
                            <td><?php echo $item['quantity']; ?></td>
                            <td><?php echo $item['unit_price']; ?></td>
                            <td><?php echo $item['line_total']; ?></td>
                            <td><a href="new_bill.php?remove=<?php echo $index; ?>">Remove</a></td>
                        </tr>
                    <?php } ?>
                </table>

                <div style="background:#232019; color:#fff; padding:14px 18px; border-radius:8px; margin:16px 0; display:flex; justify-content:space-between; align-items:center;">
                    <span style="text-transform:uppercase; font-size:12px; letter-spacing:.03em; color:#B7AF9F;">Grand Total</span>
                    <span style="font-family:'IBM Plex Mono',monospace; font-size:20px; font-weight:600;">Rs <?php echo number_format($grand_total, 0); ?></span>
                </div>

                <?php if (count($_SESSION['cart']) > 0) { ?>
                    <button type="submit">Save Bill</button>
                <?php } ?>
            </form>
        </div>

    </div>

    <script>
        function updatePrice() {
            var select = document.getElementById("product_select");
            var selectedOption = select.options[select.selectedIndex];
            var price = selectedOption.getAttribute("data-price");
            document.getElementById("unit_price").value = price ? price : "";
        }
    </script>

</div>
</body>
</html>