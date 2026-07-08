<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$conn->query("CREATE TABLE IF NOT EXISTS shop_info (
    id INT AUTO_INCREMENT PRIMARY KEY,
    shop_name VARCHAR(150),
    address VARCHAR(255),
    phone VARCHAR(50)
)");

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $shop_name = $_POST['shop_name'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];

    $existing = $conn->query("SELECT id FROM shop_info LIMIT 1");
    if ($existing->num_rows > 0) {
        $id = $existing->fetch_assoc()['id'];
        $stmt = $conn->prepare("UPDATE shop_info SET shop_name=?, address=?, phone=? WHERE id=?");
        $stmt->bind_param("sssi", $shop_name, $address, $phone, $id);
    } else {
        $stmt = $conn->prepare("INSERT INTO shop_info (shop_name, address, phone) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $shop_name, $address, $phone);
    }
    $stmt->execute();
    $message = "Shop information saved!";
}

$shop = $conn->query("SELECT * FROM shop_info LIMIT 1")->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Settings</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<?php $base = "../"; include '../includes/sidebar.php'; ?>

    <div class="topbar">
        <h2>Settings</h2>
        <div class="date-badge"><?php echo date("l, j F Y"); ?></div>
    </div>

    <?php if ($message != "") { ?>
        <p style="color:#0B534E; font-weight:600; background:#E7F3EF; padding:10px 14px; border-radius:8px; max-width:520px;"><?php echo $message; ?></p>
    <?php } ?>

    <div style="display:flex; gap:20px; flex-wrap:wrap; align-items:flex-start;">

        <div style="flex:1; min-width:320px;">
            <h3>Shop Information</h3>
            <form method="POST" action="">
                <label>Shop Name:</label><br>
                <input type="text" name="shop_name" value="<?php echo $shop['shop_name'] ?? ''; ?>"><br><br>

                <label>Address:</label><br>
                <input type="text" name="address" value="<?php echo $shop['address'] ?? ''; ?>"><br><br>

                <label>Phone:</label><br>
                <input type="text" name="phone" value="<?php echo $shop['phone'] ?? ''; ?>"><br><br>

                <button type="submit">Save Shop Info</button>
            </form>
        </div>

        <div style="flex:1; min-width:320px;">
            <h3>Backup & Restore</h3>
            <div class="panel" style="padding:20px;">
                <p style="color:var(--ink-soft); font-size:13.8px;">Download a full backup of your products, customers, bills and stock. Keep it safe — you can restore it anytime.</p>
                <a href="backup.php"><button type="button" style="background:var(--teal);">Backup Database</button></a>

                <p style="color:var(--ink-soft); font-size:13.8px; margin-top:20px;">Restore from a previously downloaded backup file.</p>
                <form method="POST" action="restore.php" enctype="multipart/form-data" style="box-shadow:none; border:none; padding:0; max-width:none; background:transparent;">
                    <input type="file" name="backup_file" accept=".sql" required><br><br>
                    <button type="submit" style="background:transparent; color:var(--ink); border:1px solid var(--border);">Restore Database</button>
                </form>
            </div>
        </div>

    </div>

</div>
</body>
</html>