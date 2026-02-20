<?php
include 'includes/db.php';
try {
    $stmt = $pdo->exec("UPDATE reviews SET status = 'approved' WHERE status = 'pending'");
    echo "Approved " . $stmt . " reviews.\n";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
