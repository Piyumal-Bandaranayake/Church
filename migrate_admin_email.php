<?php
include 'includes/db.php';
try {
    $pdo->exec("ALTER TABLE admins ADD COLUMN email VARCHAR(255) NOT NULL UNIQUE AFTER username");
    echo "Added 'email' column to 'admins' table successfully.";
    
    // Update the default admin to have an email
    $pdo->exec("UPDATE admins SET email = 'admin@gracechurch.org' WHERE username = 'admin'");
    echo "\nUpdated default admin email.";
} catch (PDOException $e) {
    if ($e->getCode() == '42S21') {
        echo "Column 'email' already exists.";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
