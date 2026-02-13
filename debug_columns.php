<?php
include 'includes/db.php';
try {
    $stmt = $pdo->query("DESCRIBE admins");
    $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "Columns in 'admins' table:\n";
    foreach ($columns as $col) {
        echo "- " . $col['Field'] . "\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
