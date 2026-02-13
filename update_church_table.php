<?php
include 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE churches ADD COLUMN IF NOT EXISTS pastor_name VARCHAR(255) DEFAULT NULL");
    $pdo->exec("ALTER TABLE churches ADD COLUMN IF NOT EXISTS location VARCHAR(255) DEFAULT NULL");
    echo "Successfully updated churches table with pastor and location columns.<br>";
    echo "<a href='register.php'>Back to Registration</a>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
