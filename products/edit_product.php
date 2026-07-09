<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];
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

        $sql = "UPDATE products SET category=?, brand=?, paint_type=?, pack_size=?, color_name=?, product_name=NULL, purchase_price=?, sale_price=?, stock_quantity=?, min_quantity=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssddiii", $category, $brand, $paint_type, $pack_size, $color_name, $purchase_price, $sale_price, $stock_quantity, $min_quantity, $id);

    } else {
        $product_name = $_POST['product_name'];

        $sql = "UPDATE products SET category=?, product_name=?, brand=NULL, paint_type=NULL, pack_size=NULL, color_name=NULL, purchase_price=?, sale_price=?, stock_quantity=?, min_quantity=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssddiii", $category, $product_name, $purchase_price, $sale_price, $stock_quantity, $min_quantity, $id);
    }

    if ($stmt->execute()) {
        $message = "Product updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

$result = $conn->query("SELECT * FROM products WHERE id = $id");
$product = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Product</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Edit Product</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label>Category:</label><br>
        <select name="category" id="category" onchange="toggleFields()" required>
            <option value="Paint" <?php if ($product['category']=='Paint') echo 'selected'; ?>>Paint</option>
            <option value="Hardware" <?php if ($product['category']=='Hardware') echo 'selected'; ?>>Hardware</option>
            <option value="Interior" <?php if ($product['category']=='Interior') echo 'selected'; ?>>Interior</option>
        </select><br><br>

        <div id="paint_fields">
            <label>Brand:</label><br>
            <input type="text" name="brand" value="<?php echo $product['brand']; ?>" placeholder="e.g. Brighto, Nippon, Master, etc."><br><br>

            <label>Paint Type:</label><br>
            <input type="text" name="paint_type" value="<?php echo $product['paint_type']; ?>"><br><br>

            <label>Pack Size:</label><br>
            <select name="pack_size">
                <option value="Quarter" <?php if ($product['pack_size']=='Quarter') echo 'selected'; ?>>Quarter</option>
                <option value="Gallon" <?php if ($product['pack_size']=='Gallon') echo 'selected'; ?>>Gallon</option>
                <option value="Bucket" <?php if ($product['pack_size']=='Bucket') echo 'selected'; ?>>Bucket</option>
            </select><br><br>

            <label>Color Name:</label><br>
            <input type="text" name="color_name" value="<?php echo $product['color_name']; ?>"><br><br>
        </div>

        <div id="other_fields">
            <label>Product Name:</label><br>
            <input type="text" name="product_name" value="<?php echo $product['product_name']; ?>"><br><br>
        </div>

        <label>Purchase Price:</label><br>
        <input type="number" step="0.01" name="purchase_price" value="<?php echo $product['purchase_price']; ?>" required><br><br>

        <label>Sale Price:</label><br>
        <input type="number" step="0.01" name="sale_price" value="<?php echo $product['sale_price']; ?>" required><br><br>

        <label>Stock Quantity:</label><br>
        <input type="number" name="stock_quantity" value="<?php echo $product['stock_quantity']; ?>" required><br><br>

        <label>Minimum Quantity:</label><br>
        <input type="number" name="min_quantity" value="<?php echo $product['min_quantity']; ?>" required><br><br>

        <button type="submit">Update Product</button>
    </form>

    <script>
        function toggleFields() {
            var category = document.getElementById("category").value;
            var paintFields = document.getElementById("paint_fields");
            var otherFields = document.getElementById("other_fields");

            if (category === "Paint") {
                paintFields.style.display = "block";
                otherFields.style.display = "none";
            } else {
                paintFields.style.display = "none";
                otherFields.style.display = "block";
            }
        }
        window.onload = toggleFields;
    </script>

</div>
</body>
</html>
