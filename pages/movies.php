<?php
global $pdo;
$userId = $_SESSION['user_id'];

$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM entries WHERE user_id = ? AND type = 'movie'";
$params = [$userId];

if ($filter == 'ongoing') {
    $query .= " AND status = 'ongoing'";
} elseif ($filter == 'completed') {
    $query .= " AND status = 'completed'";
}

if ($search) {
    $query .= " AND title LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = "Failed to load entries: " . $e->getMessage();
    $entries = [];
}
?>

<!-- Quick Filters -->
<div class="mb-6">
    <div class="flex items-center space-x-1 bg-white p-1 rounded-lg border border-gray-200 w-fit">
        <a href="?page=movies&filter=all" class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?php echo $filter == 'all' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            All
        </a>
        <a href="?page=movies&filter=ongoing" class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?php echo $filter == 'ongoing' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            Watching
        </a>
        <a href="?page=movies&filter=completed" class="px-4 py-2 text-sm font-medium rounded-md transition-colors <?php echo $filter == 'completed' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
            Watched
        </a>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($entries as $entry): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden relative">
            <!-- Favorite button in upper right -->
            <form method="POST" action="/Plotoryx/toggle_favorite.php" class="absolute top-2 right-2 z-10">
                <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                <button type="submit" class="p-1.5 rounded-full <?php echo (isset($entry['favorite']) && $entry['favorite']) ? 'bg-yellow-100 text-yellow-600' : 'bg-white/80 text-gray-400'; ?> hover:bg-yellow-50 transition-colors">
                    <svg class="w-4 h-4" fill="<?php echo (isset($entry['favorite']) && $entry['favorite']) ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </button>
            </form>
            <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-40 object-cover">
            <div class="p-4">
                <h3 class="font-medium text-sm"><?php echo htmlspecialchars($entry['title']); ?></h3>
                <p class="text-xs text-gray-500"><?php echo ucfirst($entry['status']); ?></p>
                <?php if ($entry['rating']): ?>
                    <p class="text-xs">Rating: <?php echo $entry['rating']; ?>/10</p>
                <?php endif; ?>
                <?php if ($entry['date_started']): ?>
                    <p class="text-xs">Started: <?php echo date('M j, Y', strtotime($entry['date_started'])); ?></p>
                <?php endif; ?>
                <?php if ($entry['date_ended']): ?>
                    <p class="text-xs">Ended: <?php echo date('M j, Y', strtotime($entry['date_ended'])); ?></p>
                <?php endif; ?>
                <?php if ($entry['link']): ?>
                    <p class="text-xs"><a href="<?php echo htmlspecialchars($entry['link']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">View Link</a></p>
                <?php endif; ?>
                <?php if ($entry['remarks']): ?>
                    <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 30)); ?>...</p>
                <?php endif; ?>
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-gray-100">
                    <div class="flex space-x-1">
                        <button onclick="openModal(this)" 
                                data-id="<?php echo $entry['id']; ?>" 
                                data-title="<?php echo htmlspecialchars($entry['title']); ?>" 
                                data-status="<?php echo $entry['status']; ?>" 
                                data-rating="<?php echo $entry['rating'] ?: ''; ?>" 
                                data-remarks="<?php echo htmlspecialchars($entry['remarks']); ?>"
                                data-date-started="<?php echo $entry['date_started'] ?: ''; ?>"
                                data-date-ended="<?php echo $entry['date_ended'] ?: ''; ?>"
                                data-link="<?php echo htmlspecialchars($entry['link'] ?: ''); ?>"
                                class="flex items-center px-3 py-1.5 text-xs bg-gray-100 text-gray-700 rounded-md hover:bg-gray-200 transition-colors">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Edit
                        </button>
                        <form method="POST" action="mark_completed.php" class="inline">
                            <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                            <button type="submit" class="flex items-center px-3 py-1.5 text-xs bg-green-50 text-green-700 rounded-md hover:bg-green-100 transition-colors">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Complete
                            </button>
                        </form>
                    </div>
                    <form method="POST" action="delete_entry.php" class="inline">
                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                        <button type="submit" class="flex items-center px-3 py-1.5 text-xs bg-red-50 text-red-700 rounded-md hover:bg-red-100 transition-colors" onclick="return confirm('Are you sure you want to delete this entry?')">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>