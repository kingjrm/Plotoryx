<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $title = $_POST['title'];
    $status = $_POST['status'];
    $rating = $_POST['rating'] ?: null;
    $remarks = $_POST['remarks'];
    $userId = $_SESSION['user_id'];

    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = uploadImage($_FILES['image']);
    }

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
}
?>