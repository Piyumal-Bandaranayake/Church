<?php
include 'includes/db.php';
$stmt = $pdo->query("SHOW COLUMNS FROM candidates");
foreach ($stmt as $row) {
    echo $row['Field'] . "\n";
}
?>
