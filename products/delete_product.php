<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

try {
    $sql = "DELETE FROM products WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: view_products.php");
    exit();
} catch (mysqli_sql_exception $e) {
    echo "<div style='font-family:sans-serif; padding:30px; max-width:500px; margin:60px auto; background:#FBEBE8; border-radius:10px; color:#B02A2A;'>";
    echo "<h3>Cannot Delete Product</h3>";
    echo "<p>This product has been sold in one or more bills, so it can't be deleted. You can edit it or set its stock to 0 instead, since deleting it would break past billing records.</p>";
    echo "<a href='view_products.php' style='color:#B02A2A; font-weight:600;'>Back to Products</a>";
    echo "</div>";
    exit();
}
?>
