<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = trim($_POST['title']);
    $status = $_POST['status'];
    $rating = $_POST['rating'] ?: null;
    $remarks = trim($_POST['remarks']);
    $userId = $_SESSION['user_id'];

    // Validation
    if (empty($title)) {
        $error = "Title is required";
    } elseif (!in_array($status, ['ongoing', 'completed'])) {
        $error = "Invalid status";
    } elseif ($rating && (!is_numeric($rating) || $rating < 1 || $rating > 10)) {
        $error = "Rating must be between 1 and 10";
    } else {
        $image = '';
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
                if ($image) {
                    $stmt = $pdo->prepare("UPDATE entries SET title = ?, status = ?, rating = ?, remarks = ?, image = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$title, $status, $rating, $remarks, $image, $id, $userId]);
                } else {
                    $stmt = $pdo->prepare("UPDATE entries SET title = ?, status = ?, rating = ?, remarks = ? WHERE id = ? AND user_id = ?");
                    $stmt->execute([$title, $status, $rating, $remarks, $id, $userId]);
                }
                // Redirect back
                $referer = $_SERVER['HTTP_REFERER'];
                header("Location: $referer");
                exit();
            } catch (PDOException $e) {
                $error = "Failed to update entry: " . $e->getMessage();
            }
        }
    }

    // If there's an error, redirect back with error
    if (isset($error)) {
        $referer = $_SERVER['HTTP_REFERER'];
        header("Location: $referer?error=" . urlencode($error));
        exit();
    }
}
?>