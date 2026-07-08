<?php
session_start();
include '../config/db.php';

if (!isset($_SESSION['loggedin'])) {
    header("Location: ../index.php");
    exit();
}

$tables = ['users', 'products', 'customers', 'bills', 'bill_items', 'stock_history', 'shop_info'];
$sql_dump = "-- Zakriya Store Backup - " . date("Y-m-d H:i:s") . "\n\n";

foreach ($tables as $table) {
    $result = $conn->query("SELECT * FROM $table");
    if (!$result) continue;

    $sql_dump .= "-- Table: $table\n";
    while ($row = $result->fetch_assoc()) {
        $columns = array_keys($row);
        $values = array_map(function($v) use ($conn) {
            return $v === null ? "NULL" : "'" . $conn->real_escape_string($v) . "'";
        }, array_values($row));

        $sql_dump .= "INSERT INTO $table (" . implode(", ", $columns) . ") VALUES (" . implode(", ", $values) . ");\n";
    }
    $sql_dump .= "\n";
}

$filename = "zakriya_store_backup_" . date("Y-m-d_His") . ".sql";

header("Content-Type: application/octet-stream");
header("Content-Disposition: attachment; filename=\"$filename\"");
echo $sql_dump;
exit();
?>