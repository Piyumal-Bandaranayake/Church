<?php
include 'includes/db.php';
try {
    $stmt = $pdo->query("SHOW TABLES LIKE 'reviews'");
    if ($stmt->rowCount() > 0) {
        echo "Table reviews exists.\n";
        $stmt = $pdo->query("SELECT COUNT(*) FROM reviews");
        echo "Count: " . $stmt->fetchColumn() . "\n";
        $stmt = $pdo->query("DESCRIBE reviews");
        print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
    } else {
        echo "Table reviews does NOT exist.\n";
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
