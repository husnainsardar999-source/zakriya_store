<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

try {
    $sql = "DELETE FROM customers WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: view_customers.php");
    exit();
} catch (mysqli_sql_exception $e) {
    echo "<div style='font-family:sans-serif; padding:30px; max-width:500px; margin:60px auto; background:#FBEBE8; border-radius:10px; color:#B02A2A;'>";
    echo "<h3>Cannot Delete Customer</h3>";
    echo "<p>This customer has bills recorded in the system, so they can't be deleted. You can keep the customer on record instead, since deleting them would break their billing history.</p>";
    echo "<a href='view_customers.php' style='color:#B02A2A; font-weight:600;'>Back to Customers</a>";
    echo "</div>";
    exit();
}
?>
