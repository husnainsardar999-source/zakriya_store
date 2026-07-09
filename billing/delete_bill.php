<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

// Restore stock for each item in this bill before deleting
$items = $conn->query("SELECT product_id, quantity FROM bill_items WHERE bill_id = $id");
while ($item = $items->fetch_assoc()) {
    $conn->query("UPDATE products SET stock_quantity = stock_quantity + {$item['quantity']} WHERE id = {$item['product_id']}");
}

// Delete the bill items first (they reference the bill)
$conn->query("DELETE FROM bill_items WHERE bill_id = $id");

// Now delete the bill itself
$conn->query("DELETE FROM bills WHERE id = $id");

header("Location: ../sales_history/view_bills.php");
exit();
?>
