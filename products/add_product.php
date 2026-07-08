<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST['category'];
    $purchase_price = $_POST['purchase_price'];
    $sale_price = $_POST['sale_price'];
    $stock_quantity = $_POST['stock_quantity'];
    $min_quantity = $_POST['min_quantity'];

    if ($category == "Paint") {
        $brand = $_POST['brand'];
        $paint_type = $_POST['paint_type'];
        $pack_size = $_POST['pack_size'];
        $color_name = $_POST['color_name'];
        $product_name = null;

        $sql = "INSERT INTO products (category, brand, paint_type, pack_size, color_name, purchase_price, sale_price, stock_quantity, min_quantity)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssddii", $category, $brand, $paint_type, $pack_size, $color_name, $purchase_price, $sale_price, $stock_quantity, $min_quantity);

    } else {
        $product_name = $_POST['product_name'];

        $sql = "INSERT INTO products (category, product_name, purchase_price, sale_price, stock_quantity, min_quantity)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddii", $category, $product_name, $purchase_price, $sale_price, $stock_quantity, $min_quantity);
    }

    if ($stmt->execute()) {
        $message = "Product added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Add Product</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label>Category:</label><br>
        <select name="category" id="category" onchange="toggleFields()" required>
            <option value="">-- Select Category --</option>
            <option value="Paint">Paint</option>
            <option value="Hardware">Hardware</option>
            <option value="Interior">Interior</option>
        </select><br><br>

        <div id="paint_fields" style="display:none;">
            <label>Brand:</label><br>
            <select name="brand">
                <option value="Brighto">Brighto</option>
                <option value="Andrew">Andrew</option>
                <option value="Buxson">Buxson</option>
            </select><br><br>

            <label>Paint Type:</label><br>
            <input type="text" name="paint_type"><br><br>

            <label>Pack Size:</label><br>
            <select name="pack_size">
                <option value="Quarter">Quarter</option>
                <option value="Gallon">Gallon</option>
                <option value="Bucket">Bucket</option>
            </select><br><br>

            <label>Color Name:</label><br>
            <input type="text" name="color_name"><br><br>
        </div>

        <div id="other_fields" style="display:none;">
            <label>Product Name:</label><br>
            <input type="text" name="product_name"><br><br>
        </div>

        <label>Purchase Price:</label><br>
        <input type="number" step="0.01" name="purchase_price" required><br><br>

        <label>Sale Price:</label><br>
        <input type="number" step="0.01" name="sale_price" required><br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock_quantity" required><br><br>

        <label>Minimum Quantity (for low stock alert):</label><br>
        <input type="number" name="min_quantity" value="5" required><br><br>

        <button type="submit">Add Product</button>
    </form>

    <script>
        function toggleFields() {
            var category = document.getElementById("category").value;
            var paintFields = document.getElementById("paint_fields");
            var otherFields = document.getElementById("other_fields");

            if (category === "Paint") {
                paintFields.style.display = "block";
                otherFields.style.display = "none";
            } else if (category === "Hardware" || category === "Interior") {
                paintFields.style.display = "none";
                otherFields.style.display = "block";
            } else {
                paintFields.style.display = "none";
                otherFields.style.display = "none";
            }
        }
    </script>

</div>
</body>
</html>