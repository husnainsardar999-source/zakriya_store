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
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];

    $sql = "UPDATE customers SET customer_name = ?, phone_number = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $customer_name, $phone_number, $id);

    if ($stmt->execute()) {
        $message = "Customer updated successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}

$result = $conn->query("SELECT * FROM customers WHERE id = $id");
$customer = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit Customer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Edit Customer</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label>Customer Name:</label><br>
        <input type="text" name="customer_name" value="<?php echo $customer['customer_name']; ?>" required><br><br>

        <label>Phone Number:</label><br>
        <input type="text" name="phone_number" value="<?php echo $customer['phone_number']; ?>"><br><br>

        <button type="submit">Update Customer</button>
    </form>

</div>
</body>
</html>