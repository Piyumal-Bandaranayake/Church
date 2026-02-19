<?php
include 'includes/db.php';
try {
    $stmt = $pdo->query("SELECT * FROM reviews");
    $reviews = $stmt->fetchAll(PDO::FETCH_ASSOC);
    print_r($reviews);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
