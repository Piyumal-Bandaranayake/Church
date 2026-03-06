<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    echo json_encode(['success' => false, 'message' => 'Please login as a candidate to express interest.']);
    exit();
}

$sender_id = $_SESSION['user_id'];
$receiver_id = isset($_POST['receiver_id']) ? (int)$_POST['receiver_id'] : 0;

if ($receiver_id <= 0 || $sender_id === $receiver_id) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

try {
    // Check if already interested
    $stmt = $pdo->prepare("SELECT id FROM interests WHERE sender_id = ? AND receiver_id = ?");
    $stmt->execute([$sender_id, $receiver_id]);
    $existing = $stmt->fetch();

    if ($existing) {
        // Remove interest if already exists (toggle)
        $delete = $pdo->prepare("DELETE FROM interests WHERE id = ?");
        $delete->execute([$existing['id']]);
        echo json_encode(['success' => true, 'action' => 'removed', 'message' => 'Interest removed.']);
    } else {
        // Add interest
        $insert = $pdo->prepare("INSERT INTO interests (sender_id, receiver_id) VALUES (?, ?)");
        $insert->execute([$sender_id, $receiver_id]);
        echo json_encode(['success' => true, 'action' => 'added', 'message' => 'Interest sent successfully!']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error occurred.']);
}
?>
