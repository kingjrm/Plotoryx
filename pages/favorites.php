<?php
global $pdo;
$userId = $_SESSION['user_id'];

// For now, favorites not implemented, so show all
$query = "SELECT * FROM entries WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="flex justify-between items-center mb-6">
    <h2 class="text-lg font-semibold">Favorites</h2>
    <p class="text-sm text-gray-500">Favorites feature coming soon</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
    <?php foreach ($entries as $entry): ?>
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-40 object-cover">
            <div class="p-4">
                <h3 class="font-medium text-sm"><?php echo htmlspecialchars($entry['title']); ?></h3>
                <p class="text-xs text-gray-500"><?php echo ucfirst($entry['type']); ?> • <?php echo ucfirst($entry['status']); ?></p>
                <?php if ($entry['rating']): ?>
                    <p class="text-xs">Rating: <?php echo $entry['rating']; ?>/10</p>
                <?php endif; ?>
                <?php if ($entry['remarks']): ?>
                    <p class="text-xs text-gray-600 mt-1"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 30)); ?>...</p>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; ?>
</div>