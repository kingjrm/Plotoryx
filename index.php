<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plotoryx - Track Your Favorite Manhwa & Movies</title>
    <meta name="description" content="Never lose track of your favorite manhwa and movies. Organize your entertainment library with Plotoryx.">
    <link rel="icon" type="image/png" href="image.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }

        /* Intro Animation Styles */
        #intro {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 1s ease-out;
        }

        #intro.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .logo-container {
            text-align: center;
            animation: logoIntro 2s ease-out forwards;
        }

        .logo-image {
            width: 120px;
            height: 120px;
            margin: 0 auto 20px;
            animation: logoPulse 2s ease-in-out infinite;
        }

        .logo-text {
            font-size: 3rem;
            font-weight: 700;
            color: white;
            margin-bottom: 10px;
            opacity: 0;
            animation: textFadeIn 1s ease-out 1s forwards;
        }

        .tagline {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.8);
            opacity: 0;
            animation: textFadeIn 1s ease-out 1.5s forwards;
        }

        @keyframes logoIntro {
            0% {
                transform: scale(0.5) rotate(-10deg);
                opacity: 0;
            }
            50% {
                transform: scale(1.1) rotate(5deg);
            }
            100% {
                transform: scale(1) rotate(0deg);
                opacity: 1;
            }
        }

        @keyframes logoPulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }

        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Main content initially hidden */
        #main-content {
            opacity: 0;
            transform: translateY(20px);
            transition: all 1s ease-out;
        }

        #main-content.show {
            opacity: 1;
            transform: translateY(0);
        }
    </style>
</head>
<body class="bg-white">
    <!-- Intro Animation -->
    <div id="intro">
        <div class="logo-container">
            <img src="image.png" alt="Plotoryx Logo" class="logo-image">
            <div class="logo-text">Plotoryx</div>
            <div class="tagline">Your Entertainment Tracker</div>
        </div>
    </div>

    <!-- Main Content -->
    <div id="main-content">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center py-4">
                    <div class="flex items-center">
                        <img src="image.png" alt="Plotoryx Logo" class="h-8 w-8 mr-3">
                        <span class="text-xl font-bold text-gray-900">Plotoryx</span>
                    </div>
                    <div class="flex items-center space-x-4">
                        <a href="#features" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Features</a>
                        <a href="login.php" class="text-gray-600 hover:text-gray-900 px-3 py-2 rounded-md text-sm font-medium transition-colors">Sign In</a>
                        <a href="register.php" class="bg-indigo-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">Get Started</a>
                    </div>
                </div>
            </div>
        </nav>

    <!-- Hero Section -->
    <section class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center">
                <h1 class="text-5xl md:text-6xl font-bold text-gray-900 mb-6">
                    Track Your
                    <span class="text-indigo-600">Favorite</span>
                    <br>Manhwa & Movies
                </h1>
                <p class="text-xl text-gray-600 mb-8 max-w-3xl mx-auto">
                    Never lose track of what you've read or watched. Organize your entertainment library,
                    discover new favorites, and keep your watching/reading list perfectly organized.
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="register.php" class="bg-indigo-600 text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-indigo-700 transition-all duration-200 transform hover:scale-105 shadow-lg">
                        Start Tracking Free
                    </a>
                    <a href="#features" class="border-2 border-gray-300 text-gray-700 px-8 py-4 rounded-lg text-lg font-semibold hover:border-gray-400 hover:bg-gray-50 transition-all duration-200">
                        Learn More
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                    Everything You Need to Stay Organized
                </h2>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">
                    Powerful features designed to help you manage your entertainment library effortlessly.
                </p>
            </div>

            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- Feature 1 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-book-open text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Manhwa Library</h3>
                    <p class="text-gray-600">
                        Keep track of all your favorite manhwa with detailed information, ratings, and reading progress.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-film text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Movie Collection</h3>
                    <p class="text-gray-600">
                        Organize your movie watchlist with genres, ratings, and personal reviews for every film.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Personal Ratings</h3>
                    <p class="text-gray-600">
                        Rate and review everything you watch or read. Build your personal entertainment database.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-heart text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Favorites List</h3>
                    <p class="text-gray-600">
                        Mark your absolute favorites and create curated lists for easy access and rediscovery.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-search text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Search</h3>
                    <p class="text-gray-600">
                        Quickly find any manhwa or movie in your collection with powerful search and filter options.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="bg-gray-50 p-8 rounded-xl hover:shadow-lg transition-shadow duration-300">
                    <div class="w-12 h-12 bg-red-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-chart-line text-red-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Progress Tracking</h3>
                    <p class="text-gray-600">
                        Monitor your reading/watching progress and never lose your place in any story.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="bg-indigo-600 py-16">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">
                Ready to Organize Your Entertainment?
            </h2>
            <p class="text-xl text-indigo-100 mb-8">
                Join thousands of users who never miss an episode or chapter again.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="register.php" class="bg-white text-indigo-600 px-8 py-4 rounded-lg text-lg font-semibold hover:bg-gray-50 transition-all duration-200 transform hover:scale-105 shadow-lg">
                    Create Free Account
                </a>
                <a href="login.php" class="border-2 border-white text-white px-8 py-4 rounded-lg text-lg font-semibold hover:bg-white hover:text-indigo-600 transition-all duration-200">
                    Sign In
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-white py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="flex items-center mb-4 md:mb-0">
                    <img src="image.png" alt="Plotoryx Logo" class="h-8 w-8 mr-3">
                    <span class="text-xl font-bold">Plotoryx</span>
                </div>
                <div class="text-gray-400 text-sm">
                    © 2026 Plotoryx. All rights reserved.
                </div>
            </div>
        </div>
    </footer>
    </div> <!-- End main-content -->

    <script>
        // Intro animation and transition
        document.addEventListener('DOMContentLoaded', function() {
            const intro = document.getElementById('intro');
            const mainContent = document.getElementById('main-content');

            // Show intro for 3 seconds, then transition
            setTimeout(function() {
                intro.classList.add('hidden');

                // Show main content after intro fades out
                setTimeout(function() {
                    mainContent.classList.add('show');
                }, 500); // Wait for intro fade to complete
            }, 3000); // Show intro for 3 seconds
        });

        // Add smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });
    </script>
</body>
</html>