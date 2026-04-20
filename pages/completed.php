<?php
global $pdo;
$userId = $_SESSION['user_id'];

$query = "SELECT * FROM entries WHERE user_id = ? AND status = 'completed' ORDER BY created_at DESC";
$stmt = $pdo->prepare($query);
$stmt->execute([$userId]);
$entries = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (empty($entries)): ?>
    <div class="text-center py-12">
        <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Nothing completed yet</h3>
        <p class="text-gray-500 mb-6">Mark entries as completed to see them here</p>
        <a href="?page=dashboard" class="inline-flex items-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 transition-colors">
            Browse Content
        </a>
    </div>
<?php else: ?>
    <div class="mb-6 text-sm text-gray-500">
        <?php echo count($entries); ?> completed
    </div>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
        <?php foreach ($entries as $entry): ?>
            <div class="bg-white rounded-lg shadow overflow-hidden relative group">
                <!-- Favorite button -->
                <form method="POST" action="/Plotoryx/toggle_favorite.php" class="absolute top-2 right-2 z-10">
                    <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                    <button type="submit" class="p-1.5 rounded-full <?php echo $entry['favorite'] ? 'bg-yellow-100 text-yellow-600 hover:bg-yellow-200' : 'bg-white/80 text-gray-400 hover:text-yellow-600 hover:bg-yellow-50'; ?> transition-colors">
                        <svg class="w-4 h-4" fill="<?php echo $entry['favorite'] ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                    </button>
                </form>
                <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-40 object-cover">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-2">
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium <?php echo $entry['type'] == 'manhwa' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                            <?php echo ucfirst($entry['type']); ?>
                        </span>
                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            Completed
                        </span>
                    </div>
                    <h3 class="font-medium text-sm mb-1"><?php echo htmlspecialchars($entry['title']); ?></h3>
                    <?php if ($entry['rating']): ?>
                        <div class="flex items-center text-xs text-gray-600">
                            <svg class="w-3 h-3 mr-1 text-yellow-400" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                            </svg>
                            <?php echo $entry['rating']; ?>/10
                        </div>
                    <?php endif; ?>
                    <?php if ($entry['date_started']): ?>
                        <p class="text-xs text-gray-600">Started: <?php echo date('M j, Y', strtotime($entry['date_started'])); ?></p>
                    <?php endif; ?>
                    <?php if ($entry['date_ended']): ?>
                        <p class="text-xs text-gray-600">Ended: <?php echo date('M j, Y', strtotime($entry['date_ended'])); ?></p>
                    <?php endif; ?>
                    <?php if ($entry['link']): ?>
                        <p class="text-xs text-gray-600"><a href="<?php echo htmlspecialchars($entry['link']); ?>" target="_blank" class="text-blue-600 hover:text-blue-800">View Link</a></p>
                    <?php endif; ?>
                    <?php if ($entry['remarks']): ?>
                        <p class="text-xs text-gray-600 mt-2"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 40)); ?><?php echo strlen($entry['remarks']) > 40 ? '...' : ''; ?></p>
                    <?php endif; ?>
                    <div class="flex space-x-2 mt-3">
                        <button onclick="openModal(this)" 
                                data-id="<?php echo $entry['id']; ?>" 
                                data-title="<?php echo htmlspecialchars($entry['title']); ?>" 
                                data-status="<?php echo $entry['status']; ?>" 
                                data-rating="<?php echo $entry['rating'] ?: ''; ?>" 
                                data-remarks="<?php echo htmlspecialchars($entry['remarks']); ?>"
                                data-date-started="<?php echo $entry['date_started'] ?: ''; ?>"
                                data-date-ended="<?php echo $entry['date_ended'] ?: ''; ?>"
                                data-link="<?php echo htmlspecialchars($entry['link'] ?: ''); ?>"
                                class="flex-1 px-3 py-1.5 text-xs bg-blue-500 text-white rounded hover:bg-blue-600 transition-colors">
                            Edit
                        </button>
                        <form method="POST" action="delete_entry.php" class="flex-1">
                            <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">
                            <button type="submit" class="w-full px-3 py-1.5 text-xs bg-red-500 text-white rounded hover:bg-red-600 transition-colors" onclick="return confirm('Are you sure?')">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>