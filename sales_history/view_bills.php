<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$where = "1=1";

if (!empty($_GET['bill_id'])) {
    $bill_id = intval($_GET['bill_id']);
    $where .= " AND id = $bill_id";
}

if (!empty($_GET['customer_name'])) {
    $customer_name = $conn->real_escape_string($_GET['customer_name']);
    $where .= " AND customer_name LIKE '%$customer_name%'";
}

if (!empty($_GET['bill_date'])) {
    $bill_date = $conn->real_escape_string($_GET['bill_date']);
    $where .= " AND DATE(bill_date) = '$bill_date'";
}

$result = $conn->query("SELECT * FROM bills WHERE $where ORDER BY bill_date DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Sales History</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Sales History</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div class="panel">
        <div style="padding:16px 20px; border-bottom:1px solid var(--border-soft);">
            <form method="GET" action="" style="box-shadow:none; border:none; padding:0; max-width:none; margin:0; display:flex; gap:10px; flex-wrap:wrap; align-items:flex-end; background:transparent;">
                <div>
                    <label>Bill Number</label><br>
                    <input type="text" name="bill_id" value="<?php echo isset($_GET['bill_id']) ? $_GET['bill_id'] : ''; ?>" style="margin-top:6px;">
                </div>
                <div>
                    <label>Customer Name</label><br>
                    <input type="text" name="customer_name" value="<?php echo isset($_GET['customer_name']) ? $_GET['customer_name'] : ''; ?>" style="margin-top:6px;">
                </div>
                <div>
                    <label>Date</label><br>
                    <input type="date" name="bill_date" value="<?php echo isset($_GET['bill_date']) ? $_GET['bill_date'] : ''; ?>" style="margin-top:6px;">
                </div>
                <button type="submit" style="margin-top:0;">Search</button>
                <a href="view_bills.php"><button type="button" style="margin-top:0; background:transparent; color:var(--ink); border:1px solid var(--border);">Clear</button></a>
            </form>
        </div>

        <table>
            <tr>
                <th>Bill #</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Date</th>
                <th>Grand Total</th>
                <th>Action</th>
            </tr>

            <?php if ($result->num_rows == 0) { ?>
                <tr><td colspan="6" style="text-align:center; color:var(--ink-soft);">No bills found.</td></tr>
            <?php } ?>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['phone_number'] ? $row['phone_number'] : '-'; ?></td>
                    <td><?php echo $row['bill_date']; ?></td>
                    <td><?php echo $row['grand_total']; ?></td>
                    <td>
                        <a href="../billing/print_bill.php?id=<?php echo $row['id']; ?>">View / Reprint</a> |
                        <a href="../billing/delete_bill.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Delete this bill? This will also restore the stock for its items.')">Delete</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

</div>
</body>
</html>
