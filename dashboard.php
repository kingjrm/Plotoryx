<?php
require_once 'includes/auth.php';
if (!isLoggedIn()) {
    header("Location: index.php");
    exit();
}

$user = getCurrentUser();
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plotoryx - Dashboard</title>
    <link rel="icon" type="image/png" href="image.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-gray-50">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-sm border-r border-gray-200">
            <!-- Logo Section -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <img src="image.png" alt="Plotoryx Logo" class="w-8 h-8 mr-3">
                    <h1 class="text-xl font-bold text-gray-900">Plotoryx</h1>
                </div>
            </div>

            <!-- Profile Card -->
            <div class="p-4 mx-2 mt-4 bg-gray-50 rounded-lg">
                <div class="flex items-center">
                    <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/40'; ?>" alt="Profile" class="w-10 h-10 rounded-full mr-3 border-2 border-white shadow-sm">
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-medium text-gray-900 truncate"><?php echo htmlspecialchars($user['name']); ?></p>
                        <p class="text-xs text-gray-500 truncate"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-2">
                <div class="space-y-1">
                    <a href="?page=dashboard" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?php echo $page == 'dashboard' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo $page == 'dashboard' ? 'text-white' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                        </svg>
                        Overview
                    </a>

                    <a href="?page=manhwa" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?php echo $page == 'manhwa' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo $page == 'manhwa' ? 'text-white' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Manhwa
                    </a>

                    <a href="?page=movies" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?php echo $page == 'movies' ? 'bg-gray-900 text-white' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo $page == 'movies' ? 'text-white' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                        </svg>
                        Movies
                    </a>

                    <div class="pt-4 pb-2">
                        <p class="px-3 text-xs font-semibold text-gray-500 uppercase tracking-wider">Quick Access</p>
                    </div>

                    <a href="?page=favorites" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?php echo $page == 'favorites' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo $page == 'favorites' ? 'text-yellow-600' : 'text-gray-400'; ?>" fill="<?php echo $page == 'favorites' ? 'currentColor' : 'none'; ?>" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"/>
                        </svg>
                        Favorites
                    </a>

                    <a href="?page=completed" class="group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg <?php echo $page == 'completed' ? 'bg-green-100 text-green-800 border border-green-200' : 'text-gray-700 hover:bg-gray-100'; ?>">
                        <svg class="w-5 h-5 mr-3 <?php echo $page == 'completed' ? 'text-green-600' : 'text-gray-400'; ?>" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Completed
                    </a>
                </div>

                <!-- Logout -->
                <div class="mt-8 pt-4 border-t border-gray-200">
                    <a href="logout.php" class="group flex items-center px-3 py-2.5 text-sm font-medium text-gray-700 rounded-lg hover:bg-red-50 hover:text-red-700">
                        <svg class="w-5 h-5 mr-3 text-gray-400 group-hover:text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                        </svg>
                        Logout
                    </a>
                </div>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 overflow-hidden flex flex-col">
            <!-- Top Navigation Bar -->
            <header class="bg-white shadow-sm border-b border-gray-200">
                <div class="px-6 py-4">
                    <div class="flex items-center justify-between">
                        <!-- Breadcrumbs -->
                        <nav class="flex items-center space-x-2 text-sm text-gray-500">
                            <a href="?page=dashboard" class="hover:text-gray-700 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"/>
                                </svg>
                            </a>
                            <span>/</span>
                            <?php
                            $breadcrumbs = [];
                            switch ($page) {
                                case 'dashboard':
                                    $breadcrumbs = ['Dashboard'];
                                    break;
                                case 'manhwa':
                                    $breadcrumbs = ['Dashboard', 'Manhwa'];
                                    break;
                                case 'movies':
                                    $breadcrumbs = ['Dashboard', 'Movies'];
                                    break;
                                case 'favorites':
                                    $breadcrumbs = ['Dashboard', 'Favorites'];
                                    break;
                                case 'completed':
                                    $breadcrumbs = ['Dashboard', 'Completed'];
                                    break;
                                case 'settings':
                                    $breadcrumbs = ['Dashboard', 'Settings'];
                                    break;
                                default:
                                    $breadcrumbs = ['Dashboard'];
                                    break;
                            }

                            foreach ($breadcrumbs as $index => $crumb) {
                                $isLast = $index === count($breadcrumbs) - 1;
                                if ($isLast) {
                                    echo '<span class="text-gray-900 font-medium">' . htmlspecialchars($crumb) . '</span>';
                                } else {
                                    echo '<a href="?page=dashboard" class="hover:text-gray-700 transition-colors">' . htmlspecialchars($crumb) . '</a>';
                                    echo '<span>/</span>';
                                }
                            }
                            ?>
                        </nav>

                        <!-- Top Navigation Actions -->
                        <div class="flex items-center space-x-4">
                            <!-- Search Bar -->
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                <input type="text" placeholder="Search entries..." class="block w-64 pl-10 pr-3 py-2 border border-gray-300 rounded-lg leading-5 bg-white placeholder-gray-500 focus:outline-none focus:placeholder-gray-400 focus:ring-1 focus:ring-blue-500 focus:border-blue-500 text-sm">
                            </div>

                            <!-- Quick Actions -->
                            <div class="flex items-center space-x-2">
                                <button onclick="openAddModal('manhwa')" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    Add Manhwa
                                </button>
                                <button onclick="openAddModal('movie')" class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                    Add Movie
                                </button>
                            </div>

                            <!-- User Menu -->
                            <div class="relative">
                                <button id="userMenuButton" class="flex items-center space-x-2 text-sm rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                    <img class="h-8 w-8 rounded-full border-2 border-white shadow-sm" src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/32'; ?>" alt="Profile">
                                    <span class="hidden md:block text-gray-700"><?php echo htmlspecialchars($user['name']); ?></span>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>

                                <!-- User Dropdown Menu -->
                                <div id="userMenu" class="hidden absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 border border-gray-200">
                                    <a href="?page=settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Settings
                                    </a>
                                    <div class="border-t border-gray-100"></div>
                                    <a href="logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                        </svg>
                                        Logout
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto bg-gray-50">
                <div class="p-8">
                    <?php if (isset($_GET['error'])): ?>
                        <div class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <?php echo htmlspecialchars($_GET['error']); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php
                    switch ($page) {
                        case 'dashboard':
                            include 'pages/dashboard.php';
                            break;
                        case 'manhwa':
                            include 'pages/manhwa.php';
                            break;
                        case 'movies':
                            include 'pages/movies.php';
                            break;
                        case 'favorites':
                            include 'pages/favorites.php';
                            break;
                        case 'completed':
                            include 'pages/completed.php';
                            break;
                        case 'settings':
                            include 'pages/settings.php';
                            break;
                        default:
                            include 'pages/dashboard.php';
                            break;
                    }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Modal for editing (will be populated by JS) -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Edit Entry</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-2">
                    <form id="editForm" action="edit_entry.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="editId" name="id">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column - Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label for="editTitle" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                    <input type="text" id="editTitle" name="title" required
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                                </div>

                                <div>
                                    <label for="editStatus" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative">
                                            <input type="radio" name="status" value="ongoing" id="editStatusOngoing" class="sr-only peer">
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
                                            <input type="radio" name="status" value="completed" id="editStatusCompleted" class="sr-only peer">
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
                                    <label for="editRating" class="block text-sm font-medium text-gray-700 mb-2">Rating (1-10)</label>
                                    <select id="editRating" name="rating" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
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

                                <div>
                                    <label for="editDateStarted" class="block text-sm font-medium text-gray-700 mb-2">Date Started</label>
                                    <input type="date" id="editDateStarted" name="date_started"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                                </div>

                                <div>
                                    <label for="editDateEnded" class="block text-sm font-medium text-gray-700 mb-2">Date Ended</label>
                                    <input type="date" id="editDateEnded" name="date_ended"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                                </div>
                            </div>

                            <!-- Right Column - Remarks and Image -->
                            <div class="space-y-4">
                                <div>
                                    <label for="editRemarks" class="block text-sm font-medium text-gray-700 mb-2">Personal Notes</label>
                                    <textarea id="editRemarks" name="remarks" rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors resize-none text-sm"
                                              placeholder="Add your thoughts, reviews, or notes about this entry..."></textarea>
                                </div>

                                <div>
                                    <label for="editLink" class="block text-sm font-medium text-gray-700 mb-2">Reading/Watching Link</label>
                                    <input type="url" id="editLink" name="link"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm"
                                           placeholder="https://example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                                    <div class="border border-dashed border-gray-300 rounded p-4 text-center hover:border-gray-400 transition-colors">
                                        <input type="file" id="editImage" name="image" accept="image/*" class="hidden" onchange="previewEditImage(event)">
                                        <label for="editImage" class="cursor-pointer">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-gray-600 text-sm mb-1">Click to upload new image</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        </label>
                                    </div>
                                    <div id="editImagePreview" class="mt-3 hidden">
                                        <img id="editPreviewImg" src="" alt="Preview" class="w-full h-32 object-cover rounded border">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                    Cancel
                                </button>
                                <button type="submit" class="px-6 py-2 bg-gray-900 text-white rounded hover:bg-gray-800 focus:ring-1 focus:ring-gray-900 focus:ring-offset-1 transition-colors flex items-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Entry Modal -->
    <div id="addModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-full max-w-2xl shadow-lg rounded-md bg-white max-h-[90vh] overflow-y-auto">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Add New Entry</h3>
                    <button onclick="closeAddModal()" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
                <div class="px-2">
                    <form id="addForm" action="pages/add_entry.php" method="POST" enctype="multipart/form-data">
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            <!-- Left Column - Basic Info -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Entry Type</label>
                                    <div class="grid grid-cols-2 gap-3">
                                        <label class="relative">
                                            <input type="radio" name="type" value="manhwa" id="addTypeManhwa" required class="sr-only peer">
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
                                            <input type="radio" name="type" value="movie" id="addTypeMovie" required class="sr-only peer">
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
                                    <label for="addTitle" class="block text-sm font-medium text-gray-700 mb-2">Title</label>
                                    <input type="text" id="addTitle" name="title" required
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
                                    <label for="addRating" class="block text-sm font-medium text-gray-700 mb-2">Rating (1-10)</label>
                                    <select id="addRating" name="rating" class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
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

                                <div>
                                    <label for="addDateStarted" class="block text-sm font-medium text-gray-700 mb-2">Date Started</label>
                                    <input type="date" id="addDateStarted" name="date_started"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                                </div>

                                <div>
                                    <label for="addDateEnded" class="block text-sm font-medium text-gray-700 mb-2">Date Ended</label>
                                    <input type="date" id="addDateEnded" name="date_ended"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm">
                                </div>
                            </div>

                            <!-- Right Column - Image and Remarks -->
                            <div class="space-y-4">
                                <div>
                                    <label for="addLink" class="block text-sm font-medium text-gray-700 mb-2">Reading/Watching Link</label>
                                    <input type="url" id="addLink" name="link"
                                           class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors text-sm"
                                           placeholder="https://example.com">
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700 mb-2">Cover Image</label>
                                    <div class="border border-dashed border-gray-300 rounded p-4 text-center hover:border-gray-400 transition-colors">
                                        <input type="file" id="addImage" name="image" accept="image/*" class="hidden" onchange="previewAddImage(event)">
                                        <label for="addImage" class="cursor-pointer">
                                            <svg class="w-8 h-8 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                            </svg>
                                            <p class="text-gray-600 text-sm mb-1">Click to upload cover image</p>
                                            <p class="text-xs text-gray-500">PNG, JPG, GIF up to 5MB</p>
                                        </label>
                                    </div>
                                    <div id="addImagePreview" class="mt-3 hidden">
                                        <img id="addPreviewImg" src="" alt="Preview" class="w-full h-32 object-cover rounded border">
                                    </div>
                                </div>

                                <div>
                                    <label for="addRemarks" class="block text-sm font-medium text-gray-700 mb-2">Personal Notes</label>
                                    <textarea id="addRemarks" name="remarks" rows="4"
                                              class="w-full px-3 py-2 border border-gray-300 rounded focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors resize-none text-sm"
                                              placeholder="Add your thoughts, reviews, or notes about this entry..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="mt-6 pt-4 border-t border-gray-200">
                            <div class="flex justify-end space-x-3">
                                <button type="button" onclick="closeAddModal()" class="px-4 py-2 border border-gray-300 rounded text-gray-700 hover:bg-gray-50 transition-colors text-sm">
                                    Cancel
                                </button>
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
        </div>
    </div>

    <script>
        function openModal(button) {
            const id = button.getAttribute('data-id');
            const title = button.getAttribute('data-title');
            const status = button.getAttribute('data-status');
            const rating = button.getAttribute('data-rating');
            const remarks = button.getAttribute('data-remarks');
            const dateStarted = button.getAttribute('data-date-started');
            const dateEnded = button.getAttribute('data-date-ended');
            const link = button.getAttribute('data-link');

            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;

            // Handle status radio buttons
            document.getElementById('editStatusOngoing').checked = false;
            document.getElementById('editStatusCompleted').checked = false;
            if (status === 'ongoing') {
                document.getElementById('editStatusOngoing').checked = true;
            } else if (status === 'completed') {
                document.getElementById('editStatusCompleted').checked = true;
            }

            document.getElementById('editRating').value = rating || '';
            document.getElementById('editRemarks').value = remarks || '';
            document.getElementById('editDateStarted').value = dateStarted || '';
            document.getElementById('editDateEnded').value = dateEnded || '';
            document.getElementById('editLink').value = link || '';

            // Reset image preview
            document.getElementById('editImagePreview').classList.add('hidden');
            document.getElementById('editImage').value = '';

            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }

        function openAddModal(type) {
            // Reset form
            document.getElementById('addForm').reset();
            document.getElementById('addImagePreview').classList.add('hidden');

            // Pre-select the type
            if (type === 'manhwa') {
                document.getElementById('addTypeManhwa').checked = true;
            } else if (type === 'movie') {
                document.getElementById('addTypeMovie').checked = true;
            }

            document.getElementById('addModal').classList.remove('hidden');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
        }

        function previewAddImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('addImagePreview');
            const previewImg = document.getElementById('addPreviewImg');

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

        function previewEditImage(event) {
            const file = event.target.files[0];
            const preview = document.getElementById('editImagePreview');
            const previewImg = document.getElementById('editPreviewImg');

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

        // Handle form submission to stay on current page
        document.getElementById('addForm').addEventListener('submit', function(e) {
            // Let the form submit normally, but we might want to handle success/error
            // For now, just let it submit to add_entry.php
        });

        // User menu dropdown toggle
        document.getElementById('userMenuButton').addEventListener('click', function() {
            const menu = document.getElementById('userMenu');
            menu.classList.toggle('hidden');
        });

        // Close user menu when clicking outside
        document.addEventListener('click', function(event) {
            const userMenu = document.getElementById('userMenu');
            const userMenuButton = document.getElementById('userMenuButton');

            if (!userMenuButton.contains(event.target) && !userMenu.contains(event.target)) {
                userMenu.classList.add('hidden');
            }
        });
    </script>
</body>
</html>