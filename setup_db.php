<?php
include 'includes/db.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS candidates (
        id INT AUTO_INCREMENT PRIMARY KEY,
        email VARCHAR(255) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        denomination ENUM('Catholic', 'Christian') DEFAULT 'Christian',
        catholic_by_birth ENUM('Yes', 'No') DEFAULT NULL,
        nic_number VARCHAR(20) NOT NULL UNIQUE,
        christianization_year INT DEFAULT NULL,
        sacraments_received TEXT, /* Catholic specifically */
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
        children_details TEXT,
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
    );";

    $sql .= "CREATE TABLE IF NOT EXISTS churches (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL UNIQUE,
        pastor_name VARCHAR(255) DEFAULT NULL,
        location VARCHAR(255) DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    );";

    $pdo->exec($sql);
    echo "Table 'candidates' checked/created successfully.<br>";

    // Migration logic for existing tables
    $columns_to_add = [
        'denomination' => "ENUM('Catholic', 'Christian') DEFAULT 'Christian' AFTER password",
        'catholic_by_birth' => "ENUM('Yes', 'No') DEFAULT NULL AFTER denomination",
        'nic_number' => "VARCHAR(20) NOT NULL UNIQUE AFTER catholic_by_birth",
        'christianization_year' => "INT DEFAULT NULL AFTER nic_number",
        'sacraments_received' => "TEXT AFTER christianization_year",
        'children_details' => "TEXT AFTER children"
    ];

    foreach ($columns_to_add as $column => $definition) {
        $check = $pdo->query("SHOW COLUMNS FROM candidates LIKE '$column'");
        if ($check->rowCount() == 0) {
            $pdo->exec("ALTER TABLE candidates ADD COLUMN $column $definition");
            echo "Column '$column' added successfully.<br>";
        }
    }

    echo "<br><b>Database Update Complete!</b> You can now use the new fields.";
}
catch (PDOException $e) {
    echo "Error processing database: " . $e->getMessage();
}
?>
