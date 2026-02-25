<?php
include 'includes/db.php';

try {
    $columns_to_add = [
        'partner_found' => "TINYINT(1) DEFAULT 0",
        'partner_message' => "TEXT DEFAULT NULL"
    ];

    foreach ($columns_to_add as $column => $definition) {
        $check = $pdo->query("SHOW COLUMNS FROM candidates LIKE '$column'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE candidates ADD COLUMN $column $definition");
            echo "Column '$column' added successfully.<br>";
        }
    }
    echo "<b>Database updated for Partner Found feature!</b>";
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
