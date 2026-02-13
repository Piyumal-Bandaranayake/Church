<?php
include 'includes/db.php';

try {
    // 1. Check if 'email' column exists
    $stmt = $pdo->query("SHOW COLUMNS FROM candidates LIKE 'email'");
    $email_exists = $stmt->fetch();

    if ($email_exists) {
        echo "The 'email' column already exists.<br>";
    } else {
        // 2. Check if 'username' column exists to rename it
        $stmt = $pdo->query("SHOW COLUMNS FROM candidates LIKE 'username'");
        $username_exists = $stmt->fetch();

        if ($username_exists) {
            $pdo->exec("ALTER TABLE candidates CHANGE username email VARCHAR(255) NOT NULL UNIQUE");
            echo "Successfully renamed 'username' to 'email'.<br>";
        } else {
            // 3. If neither exists, something is wrong, add email column
            $pdo->exec("ALTER TABLE candidates ADD email VARCHAR(255) NOT NULL UNIQUE AFTER id");
            echo "Added new 'email' column.<br>";
        }
    }
    
    echo "<strong>Database is now correctly configured.</strong><br>";
    echo "<a href='register.php'>Go to Registration</a>";

} catch (PDOException $e) {
    echo "Fatal Error: " . $e->getMessage();
}
?>
