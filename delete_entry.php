<?php
require_once 'includes/auth.php';
require_once 'includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $userId = $_SESSION['user_id'];

    try {
        $stmt = $pdo->prepare("DELETE FROM entries WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        // Redirect back
        $referer = $_SERVER['HTTP_REFERER'];
        header("Location: $referer");
        exit();
    } catch (PDOException $e) {
        $error = "Failed to delete entry: " . $e->getMessage();
        $referer = $_SERVER['HTTP_REFERER'];
        header("Location: $referer?error=" . urlencode($error));
        exit();
    }
}
?>