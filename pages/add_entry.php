<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

// Get type from URL parameter to pre-select
$preselectedType = isset($_GET['type']) ? $_GET['type'] : '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $type = $_POST['type'];
    $title = trim($_POST['title']);
    $status = $_POST['status'];
    $rating = $_POST['rating'] ?: null;
    $remarks = trim($_POST['remarks']);
    $image = '';

    // Validation
    if (empty($title)) {
        $error = "Title is required";
    } elseif (!in_array($type, ['manhwa', 'movie'])) {
        $error = "Invalid type";
    } elseif (!in_array($status, ['ongoing', 'completed'])) {
        $error = "Invalid status";
    } elseif ($rating && (!is_numeric($rating) || $rating < 1 || $rating > 10)) {
        $error = "Rating must be between 1 and 10";
    } else {
        if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
            $uploadResult = uploadImage($_FILES['image']);
            if (isset($uploadResult['error'])) {
                $error = $uploadResult['error'];
            } else {
                $image = $uploadResult['success'];
            }
        }

        if (!isset($error)) {
            try {
                $stmt = $pdo->prepare("INSERT INTO entries (user_id, type, title, image, status, rating, remarks) VALUES (?, ?, ?, ?, ?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $type, $title, $image, $status, $rating, $remarks]);
                header("Location: ../dashboard.php?page=" . ($type == 'manhwa' ? 'manhwa' : 'movies'));
                exit();
            } catch (PDOException $e) {
                $error = "Failed to add entry: " . $e->getMessage();
            }
        }
    }

    // If there's an error, redirect back to the dashboard with error message
    if (isset($error)) {
        header("Location: ../dashboard.php?page=" . ($type == 'manhwa' ? 'manhwa' : 'movies') . "&error=" . urlencode($error));
        exit();
    }
}