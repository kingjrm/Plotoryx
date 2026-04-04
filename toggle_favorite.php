<?php
require_once '../includes/auth.php';
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    $userId = $_SESSION['user_id'];

    try {
        // Get current favorite status
        $stmt = $pdo->prepare("SELECT favorite FROM entries WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
        $entry = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($entry) {
            // Toggle favorite status
            $newFavoriteStatus = $entry['favorite'] ? 0 : 1;
            $stmt = $pdo->prepare("UPDATE entries SET favorite = ? WHERE id = ? AND user_id = ?");
            $stmt->execute([$newFavoriteStatus, $id, $userId]);

            // Redirect back
            $referer = $_SERVER['HTTP_REFERER'];
            header("Location: $referer");
            exit();
        }
    } catch (PDOException $e) {
        // Handle error
        $referer = $_SERVER['HTTP_REFERER'];
        header("Location: $referer");
        exit();
    }
}

// If not POST or no ID, redirect back
$referer = $_SERVER['HTTP_REFERER'] ?? 'dashboard.php';
header("Location: $referer");
exit();
?>