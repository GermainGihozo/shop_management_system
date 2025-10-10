<?php
$host = 'sql101.infinityfree.com';
$db   = 'if0_38863482_shop_db'; // your actual DB name
$user = 'if0_38863482';
$pass = 'RIQS3QENbnO';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";

try {
    $conn = new PDO($dsn, $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}
?>
