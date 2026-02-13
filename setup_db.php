<?php
include 'includes/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        fullname VARCHAR(100) NOT NULL,
        sex ENUM('Male', 'Female') NOT NULL,
        dob DATE NOT NULL,
        age INT NOT NULL,
        nationality VARCHAR(50) NOT NULL,
        language VARCHAR(50) NOT NULL,
        address TEXT NOT NULL,
        hometown VARCHAR(50) NOT NULL,
        district VARCHAR(50) NOT NULL,
        province VARCHAR(50) NOT NULL,
        height VARCHAR(20) NOT NULL,
        occupation VARCHAR(100) NOT NULL,
        edu_qual TEXT,
        add_qual TEXT,
        marital_status ENUM('Unmarried', 'Divorced', 'Widowed') NOT NULL,
        children ENUM('Yes', 'No') DEFAULT 'No',
        illness TEXT,
        habits TEXT, /* Stored as comma separated string */
        church VARCHAR(100) NOT NULL,
        pastor_name VARCHAR(100) NOT NULL,
        pastor_phone VARCHAR(20) NOT NULL,
        parent_phone VARCHAR(20) NOT NULL,
        my_phone VARCHAR(20) NOT NULL,
        photo_path VARCHAR(255),
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );

    $sql .= "CREATE TABLE IF NOT EXISTS churches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        pastor_name VARCHAR(255) DEFAULT NULL,
        location VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";
    
    $pdo->exec($sql);
    echo "Table 'candidates' created successfully.";
} catch(PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
