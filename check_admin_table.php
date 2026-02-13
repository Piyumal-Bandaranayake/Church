<?php
include 'includes/db.php';
try {
    $stmt = $pdo->prepare("SELECT * FROM admins LIMIT 1");
    $stmt->execute();
    echo "Admins table exists.";
} catch (PDOException $e) {
    echo "Admins table does NOT exist. Error: " . $e->getMessage();
    
    // Proactively create the table if it's missing to help the user
    $sql = "CREATE TABLE IF NOT EXISTS admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "\nAdmins table created successfully.";
    
    // Add a default admin if none exists
    $password = password_hash('admin123', PASSWORD_DEFAULT);
    $pdo->exec("INSERT IGNORE INTO admins (username, email, password) VALUES ('admin', 'admin@gracechurch.org', '$password')");
    echo "\nDefault admin (admin / admin123) added.";
}
?>
