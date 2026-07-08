<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $customer_name = $_POST['customer_name'];
    $phone_number = $_POST['phone_number'];

    $sql = "INSERT INTO customers (customer_name, phone_number) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $customer_name, $phone_number);

    if ($stmt->execute()) {
        $message = "Customer added successfully!";
    } else {
        $message = "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Customer</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Add Customer</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <form method="POST" action="">
        <label>Customer Name:</label><br>
        <input type="text" name="customer_name" required><br><br>

        <label>Phone Number (Optional):</label><br>
        <input type="text" name="phone_number"><br><br>

        <button type="submit">Add Customer</button>
    </form>

</div>
</body>
</html>