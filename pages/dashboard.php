<?php
global $pdo;
$userId = $_SESSION['user_id'];

try {
    // Get comprehensive stats
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

    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND favorite = 1");
    $stmt->execute([$userId]);
    $favorites = $stmt->fetch()['count'];

    // Recent entries (last 6)
    $stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? ORDER BY created_at DESC LIMIT 6");
    $stmt->execute([$userId]);
    $recentEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Favorite entries (last 4)
    $stmt = $pdo->prepare("SELECT * FROM entries WHERE user_id = ? AND favorite = 1 ORDER BY created_at DESC LIMIT 4");
    $stmt->execute([$userId]);
    $favoriteEntries = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Get average ratings
    $stmt = $pdo->prepare("SELECT AVG(rating) as avg_rating FROM entries WHERE user_id = ? AND rating > 0");
    $stmt->execute([$userId]);
    $avgRating = round($stmt->fetch()['avg_rating'], 1);

    // Get completion rate
    $totalEntries = $totalManhwa + $totalMovies;
    $completionRate = $totalEntries > 0 ? round(($completed / $totalEntries) * 100) : 0;

} catch (PDOException $e) {
    $error = "Failed to load dashboard data: " . $e->getMessage();
    $totalManhwa = $totalMovies = $ongoing = $completed = $favorites = 0;
    $recentEntries = $favoriteEntries = [];
    $avgRating = 0;
    $completionRate = 0;
}
?>

<!-- Welcome Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 mb-8">
    <div class="flex items-center justify-between">
        <div class="flex items-center space-x-4">
            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-500 rounded-full flex items-center justify-center">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
            </div>
            <div>
                <h1 class="text-lg font-bold text-gray-900">Welcome back!</h1>
                <p class="text-gray-600">Here's what's happening with your collection today.</p>
            </div>
        </div>
        <div class="hidden md:flex items-center space-x-6">
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900"><?php echo $totalEntries; ?></div>
                <div class="text-sm text-gray-500">Total Entries</div>
            </div>
            <div class="w-px h-12 bg-gray-200"></div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-900"><?php echo $completionRate; ?>%</div>
                <div class="text-sm text-gray-500">Complete</div>
            </div>
        </div>
    </div>
</div>

<?php if (isset($error)): ?>
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
        <?php echo htmlspecialchars($error); ?>
    </div>
<?php endif; ?>

<!-- Stats Overview -->
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center">
            <div class="p-3 bg-blue-100 rounded-lg">
                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Manhwas</h3>
                <p class="text-2xl font-bold text-gray-900"><?php echo $totalManhwa; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center">
            <div class="p-3 bg-green-100 rounded-lg">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Movies</h3>
                <p class="text-2xl font-bold text-gray-900"><?php echo $totalMovies; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center">
            <div class="p-3 bg-yellow-100 rounded-lg">
                <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Favorites</h3>
                <p class="text-2xl font-bold text-gray-900"><?php echo $favorites; ?></p>
            </div>
        </div>
    </div>

    <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-100">
        <div class="flex items-center">
            <div class="p-3 bg-purple-100 rounded-lg">
                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-sm font-medium text-gray-500">Completed</h3>
                <p class="text-2xl font-bold text-gray-900"><?php echo $completed; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Progress & Activity Section -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
    <!-- Progress Overview -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
            Your Progress
        </h2>

        <div class="space-y-4">
            <!-- Completion Rate -->
            <div>
                <div class="flex justify-between text-sm mb-1">
                    <span class="text-gray-600">Overall Completion</span>
                    <span class="font-medium"><?php echo $completionRate; ?>%</span>
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" style="width: <?php echo $completionRate; ?>%"></div>
                </div>
            </div>

            <!-- Average Rating -->
            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-yellow-500 mr-2" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                    <span class="text-sm text-gray-600">Average Rating</span>
                </div>
                <span class="font-semibold"><?php echo $avgRating > 0 ? $avgRating : 'N/A'; ?>/10</span>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-2 gap-4">
                <div class="text-center p-3 bg-blue-50 rounded-lg">
                    <div class="text-lg font-bold text-blue-600"><?php echo $ongoing; ?></div>
                    <div class="text-xs text-blue-600">In Progress</div>
                </div>
                <div class="text-center p-3 bg-green-50 rounded-lg">
                    <div class="text-lg font-bold text-green-600"><?php echo $totalEntries - $completed; ?></div>
                    <div class="text-xs text-green-600">Remaining</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            Recent Activity
        </h2>

        <div class="space-y-3">
            <?php if (empty($recentEntries)): ?>
                <div class="text-center py-8 text-gray-500">
                    <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                    </svg>
                    <p>No entries yet. Start by adding your first manhwa or movie!</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($recentEntries, 0, 5) as $entry): ?>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                        <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/40x60'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-10 h-14 object-cover rounded">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($entry['title']); ?></p>
                            <p class="text-xs text-gray-500"><?php echo ucfirst($entry['type']); ?> • Added <?php echo date('M j', strtotime($entry['created_at'])); ?></p>
                        </div>
                        <div class="flex items-center">
                            <?php if ($entry['favorite']): ?>
                                <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Quick Actions & Favorites -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    <!-- Quick Actions -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
            </svg>
            Quick Actions
        </h2>

        <div class="space-y-3">
            <button onclick="openAddModal('manhwa')" class="w-full flex items-center p-3 bg-blue-50 hover:bg-blue-100 rounded-lg transition-colors group">
                <div class="p-2 bg-blue-100 rounded-lg mr-3 group-hover:bg-blue-200 transition-colors">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-blue-900">Add Manhwa</div>
                    <div class="text-sm text-blue-600">Start reading something new</div>
                </div>
            </button>

            <button onclick="openAddModal('movie')" class="w-full flex items-center p-3 bg-green-50 hover:bg-green-100 rounded-lg transition-colors group">
                <div class="p-2 bg-green-100 rounded-lg mr-3 group-hover:bg-green-200 transition-colors">
                    <svg class="w-4 h-4 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-green-900">Add Movie</div>
                    <div class="text-sm text-green-600">Watch something new</div>
                </div>
            </button>

            <a href="?page=favorites" class="w-full flex items-center p-3 bg-yellow-50 hover:bg-yellow-100 rounded-lg transition-colors group">
                <div class="p-2 bg-yellow-100 rounded-lg mr-3 group-hover:bg-yellow-200 transition-colors">
                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                    </svg>
                </div>
                <div class="text-left">
                    <div class="font-medium text-yellow-900">View Favorites</div>
                    <div class="text-sm text-yellow-600">Your favorite entries</div>
                </div>
            </a>
        </div>
    </div>

    <!-- Favorite Entries -->
    <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-gray-100 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
            <svg class="w-5 h-5 mr-2 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
            </svg>
            Your Favorites
        </h2>

        <?php if (empty($favoriteEntries)): ?>
            <div class="text-center py-8 text-gray-500">
                <svg class="w-12 h-12 mx-auto mb-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
                <p>No favorites yet. Mark some entries as favorites to see them here!</p>
            </div>
        <?php else: ?>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <?php foreach ($favoriteEntries as $entry): ?>
                    <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg border border-yellow-100">
                        <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/60x80'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-12 h-16 object-cover rounded">
                        <div class="flex-1 min-w-0">
                            <h3 class="font-medium text-sm text-gray-900 truncate"><?php echo htmlspecialchars($entry['title']); ?></h3>
                            <p class="text-xs text-gray-500"><?php echo ucfirst($entry['type']); ?> • <?php echo ucfirst($entry['status']); ?></p>
                            <?php if ($entry['rating']): ?>
                                <div class="flex items-center mt-1">
                                    <svg class="w-3 h-3 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    <span class="text-xs text-gray-600"><?php echo $entry['rating']; ?>/10</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Recent Entries Section -->
<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <div class="flex items-center justify-between mb-6">
        <h2 class="text-lg font-semibold text-gray-900 flex items-center">
            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            Recently Added
        </h2>
        <a href="?page=manhwa" class="text-sm text-blue-600 hover:text-blue-800 font-medium">View all →</a>
    </div>

    <?php if (empty($recentEntries)): ?>
        <div class="text-center py-12">
            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <h3 class="text-lg font-medium text-gray-900 mb-2">No entries yet</h3>
            <p class="text-gray-500 mb-4">Start building your collection by adding your first manhwa or movie.</p>
            <div class="flex justify-center space-x-3">
                <button onclick="openAddModal('manhwa')" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add Manhwa
                </button>
                <button onclick="openAddModal('movie')" class="inline-flex items-center px-4 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    Add Movie
                </button>
            </div>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <?php foreach ($recentEntries as $entry): ?>
                <div class="bg-gray-50 rounded-lg overflow-hidden hover:shadow-md transition-shadow">
                    <div class="relative">
                        <img src="<?php echo $entry['image'] ? $entry['image'] : 'https://via.placeholder.com/300x200'; ?>" alt="<?php echo htmlspecialchars($entry['title']); ?>" class="w-full h-32 object-cover">
                        <div class="absolute top-2 right-2">
                            <?php if ($entry['favorite']): ?>
                                <div class="bg-yellow-100 p-1 rounded-full">
                                    <svg class="w-4 h-4 text-yellow-600" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                </svg>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="p-4">
                        <h3 class="font-medium text-sm text-gray-900 mb-1"><?php echo htmlspecialchars($entry['title']); ?></h3>
                        <div class="flex items-center justify-between text-xs text-gray-500">
                            <span><?php echo ucfirst($entry['type']); ?> • <?php echo ucfirst($entry['status']); ?></span>
                            <?php if ($entry['rating']): ?>
                                <span class="flex items-center">
                                    <svg class="w-3 h-3 text-yellow-500 mr-1" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                                    </svg>
                                    <?php echo $entry['rating']; ?>/10
                                </span>
                            <?php endif; ?>
                        </div>
                        <?php if ($entry['date_started'] || $entry['date_ended']): ?>
                            <div class="text-xs text-gray-500 mt-1">
                                <?php if ($entry['date_started']): ?>Started: <?php echo date('M j, Y', strtotime($entry['date_started'])); ?><?php endif; ?>
                                <?php if ($entry['date_started'] && $entry['date_ended']): ?> • <?php endif; ?>
                                <?php if ($entry['date_ended']): ?>Ended: <?php echo date('M j, Y', strtotime($entry['date_ended'])); ?><?php endif; ?>
                            </div>
                        <?php endif; ?>
                        <?php if ($entry['link']): ?>
                            <div class="text-xs text-blue-600 mt-1">
                                <a href="<?php echo htmlspecialchars($entry['link']); ?>" target="_blank">View Link</a>
                            </div>
                        <?php endif; ?>
                        <?php if ($entry['remarks']): ?>
                            <p class="text-xs text-gray-600 mt-2 line-clamp-2"><?php echo htmlspecialchars(substr($entry['remarks'], 0, 60)); ?>...</p>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>