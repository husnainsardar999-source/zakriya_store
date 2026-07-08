<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$id = $_GET['id'];

$bill = $conn->query("SELECT * FROM bills WHERE id = $id")->fetch_assoc();
$items = $conn->query("SELECT * FROM bill_items WHERE bill_id = $id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bill #<?php echo $id; ?></title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Bill #<?php echo $bill['id']; ?></h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <p><a href="new_bill.php?clear=1">New Bill</a> | <button onclick="window.print()" style="margin-top:0;">Print</button></p>

    <div class="panel" style="padding:24px; max-width:700px;">
        <p><strong>Customer:</strong> <?php echo $bill['customer_name']; ?></p>
        <p><strong>Phone:</strong> <?php echo $bill['phone_number'] ? $bill['phone_number'] : '-'; ?></p>
        <p><strong>Date:</strong> <?php echo $bill['bill_date']; ?></p>

        <table>
            <tr>
                <th>Product</th>
                <th>Qty</th>
                <th>Unit Price</th>
                <th>Line Total</th>
            </tr>
            <?php while ($item = $items->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $item['product_description']; ?></td>
                    <td><?php echo $item['quantity']; ?></td>
                    <td><?php echo $item['unit_price']; ?></td>
                    <td><?php echo $item['line_total']; ?></td>
                </tr>
            <?php } ?>
        </table>

        <div style="background:var(--charcoal); color:#fff; padding:14px 18px; border-radius:8px; margin-top:16px; display:flex; justify-content:space-between; align-items:center;">
            <span style="text-transform:uppercase; font-size:12px; letter-spacing:.03em; color:#B7AF9F;">Grand Total</span>
            <span style="font-family:'IBM Plex Mono',monospace; font-size:20px; font-weight:600;">Rs <?php echo number_format($bill['grand_total'], 0); ?></span>
        </div>
    </div>

</div>
</body>
</html>