<?php
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/db.php';

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
}
?>

<div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow">
    <h2 class="text-lg font-semibold mb-4">Add New Entry</h2>
    <?php if (isset($error)): ?>
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>
    <form action="add_entry.php" method="POST" enctype="multipart/form-data">
        <div class="mb-4">
            <label for="type" class="block text-sm font-medium text-gray-700">Type</label>
            <select id="type" name="type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="manhwa">Manhwa</option>
                <option value="movie">Movie</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" id="title" name="title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div class="mb-4">
            <label for="image" class="block text-sm font-medium text-gray-700">Cover Image</label>
            <input type="file" id="image" name="image" accept="image/*" class="mt-1 block w-full">
        </div>
        <div class="mb-4">
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select id="status" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                <option value="ongoing">Ongoing</option>
                <option value="completed">Completed</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="rating" class="block text-sm font-medium text-gray-700">Rating (1-10)</label>
            <input type="number" id="rating" name="rating" min="1" max="10" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
        </div>
        <div class="mb-6">
            <label for="remarks" class="block text-sm font-medium text-gray-700">Remarks</label>
            <textarea id="remarks" name="remarks" rows="3" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
        </div>
        <button type="submit" class="w-full bg-indigo-600 text-white py-2 px-4 rounded-md hover:bg-indigo-700">Add Entry</button>
    </form>
</div>