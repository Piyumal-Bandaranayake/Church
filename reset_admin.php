<?php
include 'includes/db.php';

try {
    // 1. Force Reset Admins Table
    $pdo->exec("DROP TABLE IF EXISTS admins");
    
    $sql = "CREATE TABLE admins (
        id INT AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $pdo->exec($sql);
    echo "Table 'admins' reset successfully.<br>";

    // 2. Insert Default Admin User
    $username = 'admin';
    $password = 'admin123';
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $hashed_password]);
    
    echo "<div style='background-color: #d1fae5; color: #065f46; padding: 20px; border-radius: 8px; margin-top: 20px;'>";
    echo "<strong>Success!</strong><br>";
    echo "Admin user reset to default.<br>";
    echo "Username: <strong>admin</strong><br>";
    echo "Password: <strong>admin123</strong><br>";
    echo "</div>";

    echo "<br><a href='login.php' style='display: inline-block; background-color: #2563eb; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;'>Go to Login</a>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
