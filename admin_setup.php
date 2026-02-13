<?php
include 'includes/db.php';

try {
    // 1. Add Role Column if it doesn't exist
    // We check if column exists first to avoid error
    $checkCol = $pdo->query("SHOW COLUMNS FROM candidates LIKE 'role'");
    if ($checkCol->rowCount() == 0) {
        $sql = "ALTER TABLE candidates ADD COLUMN role ENUM('admin', 'user') DEFAULT 'user'";
        $pdo->exec($sql);
        echo "Column 'role' added successfully.<br>";
    } else {
        echo "Column 'role' already exists.<br>";
    }

    // 2. Create Admin User
    $username = 'admin';
    $password = 'admin123'; // Default password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $fullname = 'Administrator';
    $sex = 'Male'; // Dummy
    $dob = '2000-01-01'; // Dummy
    $age = 25; // Dummy
    $status = 'approved';
    $role = 'admin';

    // Check if admin exists
    $stmt = $pdo->prepare("SELECT * FROM candidates WHERE username = ?");
    $stmt->execute([$username]);
    
    if ($stmt->rowCount() == 0) {
        // We need to fill required fields to satisfy schema constraints
        $sql = "INSERT INTO candidates (username, password, fullname, sex, dob, age, nationality, language, address, hometown, district, province, height, occupation, marital_status, church, pastor_name, pastor_phone, parent_phone, my_phone, status, role) 
                VALUES (?, ?, ?, ?, ?, ?, 'Admin', 'En', 'Admin Address', 'Admin City', 'Admin Dist', 'Admin Prov', '0', 'Admin', 'Unmarried', 'Admin Church', 'Admin Pastor', '000', '000', '000', ?, ?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$username, $hashed_password, $fullname, $sex, $dob, $age, $status, $role]);
        echo "Admin user created successfully.<br>";
        echo "<strong>Username:</strong> admin<br>";
        echo "<strong>Password:</strong> admin123<br>";
    } else {
        echo "Admin user already exists.<br>";
    }

    echo "<br><a href='login.php'>Go to Login</a>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
