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
} elseif ($filter == 'favorites') {
    // Assuming we add a favorites field later
}

if ($search) {
    $query .= " AND title LIKE ?";
    $params[] = "%$search%";
}

$query .= " ORDER BY created_at DESC";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">My Movies</h2>
    <div class="flex space-x-2">
        <a href="?page=movies&filter=all" class="px-3 py-1 text-sm bg-gray-200 rounded <?php echo $filter == 'all' ? 'bg-indigo-600 text-white' : ''; ?>">All</a>
        <a href="?page=movies&filter=ongoing" class="px-3 py-1 text-sm bg-gray-200 rounded <?php echo $filter == 'ongoing' ? 'bg-indigo-600 text-white' : ''; ?>">Ongoing</a>
        <a href="?page=movies&filter=completed" class="px-3 py-1 text-sm bg-gray-200 rounded <?php echo $filter == 'completed' ? 'bg-indigo-600 text-white' : ''; ?>">Completed</a>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($entries as $entry): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-40 object-cover">
            <div class="p-4">
                <h3 class="font-medium text-sm"><?php echo htmlspecialchars($entry['title']); ?></h3>
                <p class="text-xs text-gray-500"><?php echo ucfirst($entry['status']); ?></p>
                <?php if ($entry['rating']): ?>
                    <p class="text-xs">Rating: <?php echo $entry['rating']; ?>/10</p>
                <?php endif; ?>
                <?php if ($entry['remarks']): ?>
                    <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 30)); ?>...</p>
                <?php endif; ?>
                <div class="flex space-x-2 mt-3">
                    <button onclick="openModal(<?php echo $entry['id']; ?>, '<?php echo addslashes($entry['title']); ?>', '<?php echo $entry['status']; ?>', '<?php echo $entry['rating']; ?>', '<?php echo addslashes($entry['remarks']); ?>')" class="px-2 py-1 text-xs bg-blue-500 text-white rounded">Edit</button>
                    <form method="POST" action="delete_entry.php" class="inline">
                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                        <button type="submit" class="px-2 py-1 text-xs bg-red-500 text-white rounded" onclick="return confirm('Are you sure?')">Delete</button>
                    </form>
                    <form method="POST" action="mark_completed.php" class="inline">
                        <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                        <button type="submit" class="px-2 py-1 text-xs bg-green-500 text-white rounded">Complete</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>