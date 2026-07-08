<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $product_id = $_POST['product_id'];
    $quantity_received = $_POST['quantity_received'];
    $purchase_price = $_POST['purchase_price'];
    $date_received = $_POST['date_received'];

    $stmt = $conn->prepare("INSERT INTO stock_history (product_id, quantity_received, purchase_price, date_received) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iids", $product_id, $quantity_received, $purchase_price, $date_received);
    $stmt->execute();

    $conn->query("UPDATE products SET stock_quantity = stock_quantity + $quantity_received, purchase_price = $purchase_price WHERE id = $product_id");

    $message = "Stock added successfully!";
}

$products = $conn->query("SELECT * FROM products ORDER BY category, id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add New Stock</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Add New Stock</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <p><a href="view_stock.php">Back to Stock</a></p>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label>Select Product:</label><br>
        <select name="product_id" required>
            <?php while ($p = $products->fetch_assoc()) {
                $label = $p['category'] == 'Paint'
                    ? $p['brand'] . " - " . $p['paint_type'] . " - " . $p['pack_size'] . " - " . $p['color_name']
                    : $p['product_name'];
            ?>
                <option value="<?php echo $p['id']; ?>">
                    <?php echo $p['category'] . ": " . $label . " (Current Stock: " . $p['stock_quantity'] . ")"; ?>
                </option>
            <?php } ?>
        </select><br><br>

        <label>Quantity Received:</label><br>
        <input type="number" name="quantity_received" min="1" required><br><br>

        <label>Purchase Price:</label><br>
        <input type="number" step="0.01" name="purchase_price" required><br><br>

        <label>Date Received:</label><br>
        <input type="date" name="date_received" required><br><br>

        <button type="submit">Add Stock</button>
    </form>

</div>
</body>
</html>