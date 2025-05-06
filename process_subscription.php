<?php
require 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['email'])) {
    $email = trim($_POST['email']);

    // Check if already subscribed
    $check = $conn->prepare("SELECT * FROM subscriptions WHERE email = ?");
    $check->execute([$email]);

    if ($check->rowCount() == 0) {
        $stmt = $conn->prepare("INSERT INTO subscriptions (email, status) VALUES (?, 'pending')");
        $stmt->execute([$email]);
        echo "Subscription request submitted. Payment step coming soon.";
    } else {
        echo "This email has already been used to subscribe.";
    }
} else {
    echo "Invalid request.";
}
?>
