<?php
session_start();
include 'config/db.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $_SESSION['loggedin'] = true;
        $_SESSION['username'] = $username;
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Invalid Username or Password";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Zakriya Store - Login</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="login-page">
    <div class="login-box">
    <h2>Zakriya Paint, Hardware & Interior Management System</h2>
    <h3>Login to your account</h3>

    <?php if ($error != "") { ?>
        <p style="color:#B02A2A; font-weight:600; background:#FBEBE8; padding:10px 12px; border-radius:8px; font-size:13px;"><?php echo $error; ?></p>
    <?php } ?>

    <form method="POST" action="" style="box-shadow:none; border:none; padding:0; max-width:none;">
        <label>Username:</label><br>
        <input type="text" name="username" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <button type="submit" style="width:100%; justify-content:center; padding:12px; font-size:15px;">Login</button>
    </form>
    </div>
</body>
</html>