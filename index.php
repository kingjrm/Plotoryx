<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plotoryx - Login</title>
    <link rel="icon" type="image/png" href="image.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md border border-gray-100">
        <!-- Logo Section -->
        <div class="text-center mb-8">
            <img src="image.png" alt="Plotoryx Logo" class="w-16 h-16 mx-auto mb-4">
            <h1 class="text-3xl font-bold text-gray-900">Plotoryx</h1>
            <p class="text-gray-600 mt-2 text-lg">Track your favorite manhwa and movies</p>
        </div>
        
        <!-- Login Form -->
        <div class="mb-6">
            <h2 class="text-xl font-semibold text-gray-800 mb-4 text-center">Sign In</h2>
            <form action="login.php" method="POST" enctype="multipart/form-data" class="space-y-4">
                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                           placeholder="Enter your email">
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                    <input type="password" id="password" name="password" required 
                           class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                           placeholder="Enter your password">
                </div>
                <button type="submit" 
                        class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 font-medium">
                    Sign In
                </button>
            </form>
        </div>
        
        <div class="text-center">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="register.php" class="text-indigo-600 hover:text-indigo-500 font-medium">
                    Create one here
                </a>
            </p>
        </div>
    </div>
</body>
</html>
</body>
</html>