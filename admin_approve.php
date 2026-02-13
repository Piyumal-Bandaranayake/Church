<?php
include 'includes/db.php';
// Update all to approved for testing
try {
    $sql = "UPDATE candidates SET status = 'approved' WHERE status = 'pending'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    echo "All pending candidates approved! <a href='login.php'>Go to Login</a>";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
