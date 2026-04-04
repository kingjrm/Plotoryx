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

<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
        <!-- Header -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-base font-semibold text-gray-900 flex items-center">
                <svg class="w-4 h-4 mr-2 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Entry
            </h2>
            <p class="text-xs text-gray-600 mt-1">Add a new manhwa or movie to your collection</p>
        </div>

        <?php if (isset($error)): ?>
            <div class="mx-6 mt-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded">
                <div class="flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                    </svg>
                    <span class="text-sm"><?php echo htmlspecialchars($error); ?></span>
                </div>
            </div>
        <?php endif; ?>

        <form action="add_entry.php" method="POST" enctype="multipart/form-data" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Left Column - Basic Info -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Entry Type</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative">
                                <input type="radio" name="type" value="manhwa" required class="sr-only peer">
                                <div class="p-3 border border-gray-300 rounded cursor-pointer peer-checked:border-gray-900 peer-checked:bg-gray-50 hover:border-gray-400 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Manhwa</span>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="type" value="movie" required class="sr-only peer">
                                <div class="p-3 border border-gray-300 rounded cursor-pointer peer-checked:border-gray-900 peer-checked:bg-gray-50 hover:border-gray-400 transition-colors">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Movie</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                        <input type="text" id="title" name="title" required
                               class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm"
                               placeholder="Enter the title...">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                        <div class="grid grid-cols-2 gap-3">
                            <label class="relative">
                                <input type="radio" name="status" value="ongoing" checked class="sr-only peer">
                                <div class="p-2 border border-gray-300 rounded cursor-pointer peer-checked:border-gray-900 peer-checked:bg-gray-50 hover:border-gray-400 transition-colors">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Ongoing</span>
                                    </div>
                                </div>
                            </label>
                            <label class="relative">
                                <input type="radio" name="status" value="completed" class="sr-only peer">
                                <div class="p-2 border border-gray-300 rounded cursor-pointer peer-checked:border-gray-900 peer-checked:bg-gray-50 hover:border-gray-400 transition-colors">
                                    <div class="flex items-center justify-center">
                                        <svg class="w-4 h-4 text-gray-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-900">Completed</span>
                                    </div>
                                </div>
                            </label>
                        </div>
                    </div>

                    <div>
                        <label for="rating" class="block text-sm font-medium text-gray-700 mb-2">Rating (1-10)</label>
                        <select id="rating" name="rating" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                            <option value="">No rating</option>
                            <option value="1">1 - Poor</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5 - Average</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                            <option value="9">9</option>
                            <option value="10">10 - Excellent</option>
                        </select>
                    </div>
                </div>

                <!-- Right Column - Image and Remarks -->
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                        <div class="border border-dashed border-gray-300 rounded p-4 text-center hover:border-gray-400 transition-colors">
                            <input type="file" id="image" name="image" accept="image/*" class="hidden" onchange="previewImage(event)">
                            <label for="image" class="cursor-pointer">
                                <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="text-gray-600 text-sm mb-1">Click to upload cover image</p>
                                <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                            </label>
                        </div>
                        <div id="imagePreview" class="mt-3 hidden">
                            <img id="previewImg" src="" alt="Preview" class="w-full h-32 object-cover rounded border">
                        </div>
                    </div>

                    <div>
                        <label for="remarks" class="block text-sm font-medium text-gray-700 mb-2">Personal Notes</label>
                        <textarea id="remarks" name="remarks" rows="4"
                                  class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors resize-none text-sm"
                                  placeholder="Add your thoughts, reviews, or notes about this entry..."></textarea>
                    </div>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="mt-6 pt-4 border-t border-gray-200">
                <div class="flex justify-end space-x-3">
                    <a href="../dashboard.php" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                        Cancel
                    </a>
                    <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 focus:ring-1 focus:ring-gray-900 focus:ring-offset-1 transition-colors flex items-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add Entry
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const previewImg = document.getElementById('previewImg');

    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            preview.classList.remove('hidden');
        };
        reader.readAsDataURL(file);
    } else {
        preview.classList.add('hidden');
    }
}
</script>