<?php
global $pdo;
$userId = $_SESSION['user_id'];

// Get stats
$stmt = $pdo->prepare("SELECT type, COUNT(*) as count FROM entries WHERE user_id = ? GROUP BY type");
$stmt->execute([$userId]);
$stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

$totalManhwa = 0;
$totalMovies = 0;
foreach ($stats as $stat) {
    if ($stat['type'] == 'manhwa') $totalManhwa = $stat['count'];
    if ($stat['type'] == 'movie') $totalMovies = $stat['count'];
}

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND status = 'ongoing'");
$stmt->execute([$userId]);
$ongoing = $stmt->fetch()['count'];

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND status = 'completed'");
$stmt->execute([$userId]);
$completed = $stmt->fetch()['count'];

// Recent entries
$stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
$stmt->execute([$userId]);
$recentEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500">Total Manhwas</h3>
        <p class="text-2xl font-semibold"><?php echo $totalManhwa; ?></p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500">Total Movies</h3>
        <p class="text-2xl font-semibold"><?php echo $totalMovies; ?></p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500">Ongoing</h3>
        <p class="text-2xl font-semibold"><?php echo $ongoing; ?></p>
    </div>
    <div class="bg-white p-4 rounded-lg shadow">
        <h3 class="text-sm font-medium text-gray-500">Completed</h3>
        <p class="text-2xl font-semibold"><?php echo $completed; ?></p>
    </div>
</div>

<h2 class="text-lg font-semibold mb-4">Recently Added</h2>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <?php foreach ($recentEntries as $entry): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-32 object-cover">
            <div class="p-4">
                <h3 class="font-medium"><?php echo htmlspecialchars($entry['title']); ?></h3>
                <p class="text-sm text-gray-500"><?php echo ucfirst($entry['type']); ?> • <?php echo ucfirst($entry['status']); ?></p>
                <?php if ($entry['rating']): ?>
                    <p class="text-sm">Rating: <?php echo $entry['rating']; ?>/10</p>
                <?php endif; ?>
                <?php if ($entry['remarks']): ?>
                    <p class="text-sm text-gray-600 mt-2"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 50)); ?>...</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>