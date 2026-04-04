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
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-lg fixed h-full">
            <div class="p-4 border-b">
                <div class="flex items-center">
                    <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/40'; ?>" alt="Profile" class="w-10 h-10 rounded-full mr-3">
                    <div>
                        <p class="text-sm font-medium"><?php echo htmlspecialchars($user['name']); ?></p>
                        <p class="text-xs text-gray-500"><?php echo htmlspecialchars($user['email']); ?></p>
                    </div>
                </div>
            </div>
            <nav class="mt-4">
                <a href="?page=dashboard" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'dashboard' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v2H8V5z"></path></svg>
                    Dashboard
                </a>
                <a href="?page=manhwa" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'manhwa' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    My Manhwas
                </a>
                <a href="?page=movies" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'movies' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                    My Movies
                </a>
                <a href="?page=add" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'add' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Entry
                </a>
                <a href="?page=favorites" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'favorites' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                    Favorites
                </a>
                <a href="?page=completed" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'completed' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    Completed
                </a>
                <a href="?page=settings" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100 <?php echo $page == 'settings' ? 'bg-gray-200' : ''; ?>">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    Settings
                </a>
                <a href="logout.php" class="flex items-center px-4 py-2 text-gray-700 hover:bg-gray-100">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    Logout
                </a>
            </nav>
        </div>

        <!-- Main content -->
        <div class="flex-1 ml-64">
            <!-- Top Navbar -->
            <header class="bg-white shadow-sm border-b">
                <div class="flex items-center justify-between px-6 py-4">
                    <h1 class="text-lg font-semibold text-gray-900"><?php echo ucfirst($page); ?></h1>
                    <div class="flex items-center space-x-4">
                        <input type="text" placeholder="Search..." class="px-3 py-1 border border-gray-300 rounded-md text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <div class="relative">
                            <button class="flex items-center text-sm text-gray-700 hover:text-gray-900">
                                <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/32'; ?>" alt="Profile" class="w-8 h-8 rounded-full mr-2">
                                <?php echo htmlspecialchars($user['name']); ?>
                            </button>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="p-6">
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
                    case 'add':
                        include 'pages/add_entry.php';
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
                }
                ?>
            </main>
        </div>
    </div>

    <!-- Modal for editing (will be populated by JS) -->
    <div id="editModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden" id="my-modal">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900" id="modalTitle">Edit Entry</h3>
                <div class="mt-2 px-7 py-3">
                    <form id="editForm" action="edit_entry.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="editId" name="id">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Title</label>
                            <input type="text" id="editTitle" name="title" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Status</label>
                            <select id="editStatus" name="status" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                                <option value="ongoing">Ongoing</option>
                                <option value="completed">Completed</option>
                            </select>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Rating (1-10)</label>
                            <input type="number" id="editRating" name="rating" min="1" max="10" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Remarks</label>
                            <textarea id="editRemarks" name="remarks" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md"></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700">Cover Image</label>
                            <input type="file" id="editImage" name="image" accept="image/*" class="mt-1 block w-full">
                        </div>
                        <div class="flex items-center px-4 py-3">
                            <button type="submit" class="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-300">Save</button>
                            <button type="button" onclick="closeModal()" class="ml-3 px-4 py-2 bg-gray-300 text-gray-900 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">Cancel</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function openModal(id, title, status, rating, remarks) {
            document.getElementById('editId').value = id;
            document.getElementById('editTitle').value = title;
            document.getElementById('editStatus').value = status;
            document.getElementById('editRating').value = rating;
            document.getElementById('editRemarks').value = remarks;
            document.getElementById('editModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('editModal').classList.add('hidden');
        }
    </script>
</body>
</html>