<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $user_id = $_SESSION['user_id'];
    $message = $_POST['message'];

    $stmt = $pdo->prepare("UPDATE candidates SET partner_found = 1, partner_message = ? WHERE id = ?");
    $stmt->execute([$message, $user_id]);

    header("Location: profile.php?id=$user_id&notified=1");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
