<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if (empty($_SESSION['cart'])) {
    header("Location: new_bill.php");
    exit();
}

$customer_name = $_POST['customer_name'];
$phone_number = $_POST['phone_number'];

// Calculate grand total
$grand_total = 0;
foreach ($_SESSION['cart'] as $item) {
    $grand_total += $item['line_total'];
}

// Check if customer already exists by name, else create new
$customer_id = null;
$check = $conn->query("SELECT id FROM customers WHERE customer_name = '$customer_name' LIMIT 1");
if ($check->num_rows > 0) {
    $customer_id = $check->fetch_assoc()['id'];
} else {
    $stmt = $conn->prepare("INSERT INTO customers (customer_name, phone_number) VALUES (?, ?)");
    $stmt->bind_param("ss", $customer_name, $phone_number);
    $stmt->execute();
    $customer_id = $conn->insert_id;
}

// Insert the bill
$stmt = $conn->prepare("INSERT INTO bills (customer_id, customer_name, phone_number, grand_total) VALUES (?, ?, ?, ?)");
$stmt->bind_param("issd", $customer_id, $customer_name, $phone_number, $grand_total);
$stmt->execute();
$bill_id = $conn->insert_id;

// Insert each item and reduce stock
foreach ($_SESSION['cart'] as $item) {
    $stmt = $conn->prepare("INSERT INTO bill_items (bill_id, product_id, product_description, quantity, unit_price, line_total) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisidd", $bill_id, $item['product_id'], $item['description'], $item['quantity'], $item['unit_price'], $item['line_total']);
    $stmt->execute();

    // Reduce stock automatically
    $conn->query("UPDATE products SET stock_quantity = stock_quantity - {$item['quantity']} WHERE id = {$item['product_id']}");
}

// Clear the cart
$_SESSION['cart'] = [];

header("Location: print_bill.php?id=$bill_id");
exit();
?>