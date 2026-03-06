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
    $his_name = $_POST['his_name'] ?? '';
    $partner_name = $_POST['partner_name'] ?? '';
    $mobile_number = $_POST['mobile_number'] ?? '';
    $partner_reg_number = $_POST['partner_reg_number'] ?? '';

    // Insert into the new partner_found_reports table
    $insert_stmt = $pdo->prepare("INSERT INTO partner_found_reports (user_id, his_name, partner_name, message, mobile_number, partner_reg_number) VALUES (?, ?, ?, ?, ?, ?)");
    $insert_stmt->execute([$user_id, $his_name, $partner_name, $message, $mobile_number, $partner_reg_number]);

    // Keep updating the candidates table for the legacy dashboard queries
    $stmt = $pdo->prepare("UPDATE candidates SET partner_found = 1, partner_message = ? WHERE id = ?");
    $stmt->execute([$message, $user_id]);

    header("Location: my_profile.php?notified=1");
    exit();
} else {
    header("Location: index.php");
    exit();
}
?>
