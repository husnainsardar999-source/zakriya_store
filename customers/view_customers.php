<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$result = $conn->query("SELECT * FROM customers ORDER BY id DESC");
?>
<!DOCTYPE html>
<html>
<head>
    <title>All Customers</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Customers</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <div class="panel">
        <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 20px; border-bottom:1px solid var(--border-soft); flex-wrap:wrap; gap:10px;">
            <h3 style="margin:0; font-size:16px;">All Customers</h3>
            <div style="display:flex; gap:10px;">
                <input type="text" id="searchBox" onkeyup="filterTable()" placeholder="Search by name or phone..." style="padding:9px 12px; border:1px solid var(--border); border-radius:8px; font-size:13.5px; min-width:220px;">
                <a href="add_customer.php"><button type="button" style="margin-top:0;">+ Add Customer</button></a>
            </div>
        </div>

        <table id="customersTable">
            <tr>
                <th>ID</th>
                <th>Customer Name</th>
                <th>Phone Number</th>
                <th>Action</th>
            </tr>

            <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['customer_name']; ?></td>
                    <td><?php echo $row['phone_number'] ? $row['phone_number'] : '-'; ?></td>
                    <td>
                        <a href="edit_customer.php?id=<?php echo $row['id']; ?>">Edit</a> |
                        <a href="delete_customer.php?id=<?php echo $row['id']; ?>" onclick="return confirm('Are you sure you want to delete this customer?')">Delete</a> |
                        <a href="customer_history.php?id=<?php echo $row['id']; ?>">View History</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
    </div>

    <script>
        function filterTable() {
            var input = document.getElementById("searchBox");
            var filter = input.value.toUpperCase();
            var table = document.getElementById("customersTable");
            var rows = table.getElementsByTagName("tr");

            for (var i = 1; i < rows.length; i++) {
                var rowText = rows[i].textContent || rows[i].innerText;
                if (rowText.toUpperCase().indexOf(filter) > -1) {
                    rows[i].style.display = "";
                } else {
                    rows[i].style.display = "none";
                }
            }
        }
    </script>

</div>
</body>
</html>