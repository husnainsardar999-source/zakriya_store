<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

if (isset($_FILES['backup_file']) && $_FILES['backup_file']['error'] == 0) {
    $sql_content = file_get_contents($_FILES['backup_file']['tmp_name']);

    // Run each INSERT statement one by one
    $queries = explode(";\n", $sql_content);
    $success = 0;
    $failed = 0;

    foreach ($queries as $query) {
        $query = trim($query);
        if ($query == "" || strpos($query, "--") === 0) continue;

        if ($conn->query($query)) {
            $success++;
        } else {
            $failed++;
        }
    }

    echo "<p>Restore complete. $success statements executed successfully, $failed failed.</p>";
    echo "<p><a href='settings.php'>Back to Settings</a></p>";
} else {
    echo "<p>No file uploaded or an error occurred.</p>";
    echo "<p><a href='settings.php'>Back to Settings</a></p>";
}
?>