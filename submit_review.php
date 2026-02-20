<?php
session_start();
include 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['review_name'] ?? '');
    $description = trim($_POST['review_description'] ?? '');
    
    if (empty($name) || empty($description)) {
        header("Location: candidates.php?error=empty_fields");
        exit();
    }

    $upload_dir = 'uploads/reviews/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $image_paths = [null, null, null, null, null];

    for ($i = 1; $i <= 5; $i++) {
        $input_name = 'review_image' . $i;
        if (isset($_FILES[$input_name]) && $_FILES[$input_name]['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES[$input_name]['name'], PATHINFO_EXTENSION);
            $filename = uniqid('review' . $i . '_') . '.' . $ext;
            $target_path = $upload_dir . $filename;
            if (move_uploaded_file($_FILES[$input_name]['tmp_name'], $target_path)) {
                $image_paths[$i-1] = $target_path;
            }
        }
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO reviews (name, description, image1, image2, image3, image4, image5) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $params = array_merge([$name, $description], $image_paths);
        $stmt->execute($params);
        header("Location: candidates.php?success=review_submitted");
    } catch (PDOException $e) {
        header("Location: candidates.php?error=db_error");
    }
    exit();
}
?>
