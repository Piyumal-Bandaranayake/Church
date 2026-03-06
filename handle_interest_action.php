<?php
session_start();
include 'includes/db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'candidate') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized.']);
    exit();
}

$user_id = $_SESSION['user_id'];
$interest_id = isset($_POST['interest_id']) ? (int)$_POST['interest_id'] : 0;
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($interest_id <= 0 || !in_array($action, ['accept', 'reject', 'delete'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request.']);
    exit();
}

try {
    if ($action === 'delete') {
        // Person who SENT the interest can delete it
        $stmt = $pdo->prepare("DELETE FROM interests WHERE id = ? AND sender_id = ?");
        $stmt->execute([$interest_id, $user_id]);
        echo json_encode(['success' => true, 'message' => 'Interest withdrawn.']);
    } else {
        // Person who RECEIVED the interest can accept or reject it
        $status = ($action === 'accept') ? 'accepted' : 'rejected';
        $stmt = $pdo->prepare("UPDATE interests SET status = ? WHERE id = ? AND receiver_id = ?");
        $stmt->execute([$status, $interest_id, $user_id]);
        
        $msg = ($action === 'accept') ? 'Interest accepted!' : 'Interest declined.';
        echo json_encode(['success' => true, 'message' => $msg, 'status' => $status]);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Database error.']);
}
?>
