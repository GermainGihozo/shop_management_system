<?php
require 'includes/db.php';

$stmt = $conn->query("SELECT category_name FROM categories ORDER BY category_name ASC");
echo json_encode($stmt->fetchAll(PDO::FETCH_COLUMN));
