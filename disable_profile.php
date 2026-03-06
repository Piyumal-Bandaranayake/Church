<?php
session_start();
include 'includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if already requested or disabled
$stmt = $pdo->prepare("SELECT is_disabled, disable_requested FROM candidates WHERE id = ?");
$stmt->execute([$user_id]);
$candidate = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$candidate) {
    header("Location: logout.php");
    exit();
}

if ($candidate['is_disabled']) {
    $message = "Your profile is already disabled.";
} elseif ($candidate['disable_requested']) {
    $message = "Your request to disable the profile is already pending with the administrator.";
} else {
    // Send request to admin
    $stmt = $pdo->prepare("UPDATE candidates SET disable_requested = 1 WHERE id = ?");
    $stmt->execute([$user_id]);
    $message = "Your request to disable the profile has been sent to the administrator for review.";
}

header("Location: my_profile.php?action_success=1&message=" . urlencode($message));
exit();
?>
