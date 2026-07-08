<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

$customer_result = $conn->query("SELECT * FROM customers WHERE id = $id");
$customer = $customer_result->fetch_assoc();

$bills_result = $conn->query("SELECT * FROM bills WHERE customer_id = $id ORDER BY bill_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Customer History</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Purchase History: <?php echo $customer['customer_name']; ?></h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <p><a href="view_customers.php">Back to Customers</a></p>
    <p>Phone: <?php echo $customer['phone_number'] ? $customer['phone_number'] : '-'; ?></p>

    <div class="panel">
        <table>
            <tr>
                <th>Bill ID</th>
                <th>Date</th>
                <th>Grand Total</th>
                <th>Action</th>
            </tr>

            <?php if ($bills_result->num_rows == 0) { ?>
                <tr><td colspan="4" style="text-align:center; color:var(--ink-soft);">No bills yet.</td></tr>
            <?php } ?>

            <?php while ($row = $bills_result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['bill_date']; ?></td>
                    <td><?php echo $row['grand_total']; ?></td>
                    <td><a href="../billing/print_bill.php?id=<?php echo $row['id']; ?>">View Bill</a></td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
</body>
</html>