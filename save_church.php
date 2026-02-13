<?php
include 'includes/db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $pastor = trim($_POST['pastor'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if (empty($name)) {
        echo json_encode(['success' => false, 'message' => 'Church name is required.']);
        exit;
    }

    try {
        // Ensure columns exist (optional but safe)
        try {
            $pdo->exec("ALTER TABLE churches ADD COLUMN IF NOT EXISTS pastor_name VARCHAR(255) DEFAULT NULL");
            $pdo->exec("ALTER TABLE churches ADD COLUMN IF NOT EXISTS location VARCHAR(255) DEFAULT NULL");
        } catch (Exception $e) {
            // Ignore if columns already exist (some MySQL versions might not support IF NOT EXISTS in ALTER)
        }

        $stmt = $pdo->prepare("INSERT INTO churches (name, pastor_name, location) VALUES (?, ?, ?)");
        $stmt->execute([$name, $pastor, $location]);

        echo json_encode(['success' => true, 'message' => 'Church saved successfully!']);
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) {
            echo json_encode(['success' => false, 'message' => 'This church already exists in our records.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
        }
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}
?>
