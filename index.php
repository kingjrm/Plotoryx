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
            background: white;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            opacity: 1;
            transition: opacity 0.8s ease-out;
        }

        #intro.hidden {
            opacity: 0;
            pointer-events: none;
        }

        .logo-container {
            text-align: center;
            animation: logoIntro 1.5s ease-out forwards;
        }

        .logo-image {
            width: 100px;
            height: 100px;
            margin: 0 auto 20px;
            animation: logoFade 2s ease-in-out infinite;
        }

        .logo-text {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 8px;
            opacity: 0;
            animation: textFadeIn 0.8s ease-out 0.5s forwards;
        }

        .tagline {
            font-size: 1rem;
            color: #6b7280;
            opacity: 0;
            animation: textFadeIn 0.8s ease-out 1s forwards;
        }

        @keyframes logoIntro {
            0% {
                transform: scale(0.8);
                opacity: 0;
            }
            100% {
                transform: scale(1);
                opacity: 1;
            }
        }

        @keyframes logoFade {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.7;
            }
        }

        @keyframes textFadeIn {
            from {
                opacity: 0;
                transform: translateY(15px);
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
            transition: all 0.8s ease-out;
        }

        #main-content.show {
            opacity: 1;
            transform: translateY(0);
        }

        /* Floating Elements */
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            pointer-events: none;
            z-index: 1;
        }

        .shape {
            position: absolute;
            opacity: 0.1;
            animation: float 6s ease-in-out infinite;
        }

        .shape:nth-child(1) {
            top: 10%;
            left: 10%;
            animation-delay: 0s;
        }

        .shape:nth-child(2) {
            top: 20%;
            right: 15%;
            animation-delay: 2s;
        }

        .shape:nth-child(3) {
            bottom: 20%;
            left: 20%;
            animation-delay: 4s;
        }

        .shape:nth-child(4) {
            bottom: 10%;
            right: 10%;
            animation-delay: 1s;
        }

        .shape:nth-child(5) {
            top: 50%;
            left: 5%;
            animation-delay: 3s;
        }

        .shape:nth-child(6) {
            top: 30%;
            right: 5%;
            animation-delay: 5s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        /* Feature Cards Hover Effects */
        .feature-card {
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .feature-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .feature-card:hover::before {
            left: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        }

        /* Hero Visual Elements */
        .hero-visual {
            position: relative;
        }

        .hero-illustration {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            opacity: 0.8;
            z-index: 2;
        }

        .hero-illustration.left {
            left: -100px;
        }

        .hero-illustration.right {
            right: -100px;
        }

        @media (max-width: 768px) {
            .hero-illustration {
                display: none;
            }
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
        <nav class="sticky top-0 bg-white shadow-sm border-b border-gray-100 z-50">
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
    <section class="bg-gradient-to-br from-indigo-50 via-white to-purple-50 py-20 relative overflow-hidden">
        <!-- Floating Shapes Background -->
        <div class="floating-shapes">
            <div class="shape text-indigo-300">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
            <div class="shape text-purple-300">
                <svg width="40" height="40" viewBox="0 0 24 24" fill="currentColor">
                    <circle cx="12" cy="12" r="10"/>
                </svg>
            </div>
            <div class="shape text-pink-300">
                <svg width="50" height="50" viewBox="0 0 24 24" fill="currentColor">
                    <rect x="3" y="3" width="18" height="18" rx="2" ry="2"/>
                </svg>
            </div>
            <div class="shape text-blue-300">
                <svg width="45" height="45" viewBox="0 0 24 24" fill="currentColor">
                    <polygon points="12,2 22,8.5 22,15.5 12,22 2,15.5 2,8.5"/>
                </svg>
            </div>
            <div class="shape text-indigo-400">
                <svg width="35" height="35" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2L13.09 8.26L22 9L16 14.74L17.18 21.02L12 17.77L6.82 21.02L8 14.74L2 9L10.91 8.26L12 2Z"/>
                </svg>
            </div>
            <div class="shape text-purple-400">
                <svg width="55" height="55" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center hero-visual">
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
    <section id="features" class="py-20 bg-white relative overflow-hidden">
        <!-- Floating Stats Badges -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-20 left-10 bg-indigo-100 text-indigo-800 px-4 py-2 rounded-full text-sm font-semibold shadow-lg animate-pulse">
                <i class="fas fa-users mr-2"></i>10K+ Users
            </div>
            <div class="absolute top-40 right-20 bg-purple-100 text-purple-800 px-4 py-2 rounded-full text-sm font-semibold shadow-lg animate-pulse" style="animation-delay: 1s;">
                <i class="fas fa-book mr-2"></i>50K+ Manhwa
            </div>
            <div class="absolute bottom-40 left-20 bg-green-100 text-green-800 px-4 py-2 rounded-full text-sm font-semibold shadow-lg animate-pulse" style="animation-delay: 2s;">
                <i class="fas fa-film mr-2"></i>100K+ Movies
            </div>
            <div class="absolute bottom-20 right-10 bg-orange-100 text-orange-800 px-4 py-2 rounded-full text-sm font-semibold shadow-lg animate-pulse" style="animation-delay: 0.5s;">
                <i class="fas fa-star mr-2"></i>4.9★ Rating
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
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
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
                    <div class="w-12 h-12 bg-indigo-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-book-open text-indigo-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Manhwa Library</h3>
                    <p class="text-gray-600">
                        Keep track of all your favorite manhwa with detailed information, ratings, and reading progress.
                    </p>
                </div>

                <!-- Feature 2 -->
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
                    <div class="w-12 h-12 bg-purple-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-film text-purple-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Movie Collection</h3>
                    <p class="text-gray-600">
                        Organize your movie watchlist with genres, ratings, and personal reviews for every film.
                    </p>
                </div>

                <!-- Feature 3 -->
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
                    <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-star text-green-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Personal Ratings</h3>
                    <p class="text-gray-600">
                        Rate and review everything you watch or read. Build your personal entertainment database.
                    </p>
                </div>

                <!-- Feature 4 -->
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
                    <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-heart text-blue-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Favorites List</h3>
                    <p class="text-gray-600">
                        Mark your absolute favorites and create curated lists for easy access and rediscovery.
                    </p>
                </div>

                <!-- Feature 5 -->
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
                    <div class="w-12 h-12 bg-orange-100 rounded-lg flex items-center justify-center mb-4">
                        <i class="fas fa-search text-orange-600 text-xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-3">Smart Search</h3>
                    <p class="text-gray-600">
                        Quickly find any manhwa or movie in your collection with powerful search and filter options.
                    </p>
                </div>

                <!-- Feature 6 -->
                <div class="feature-card bg-gray-50 p-8 rounded-xl">
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
    <section class="bg-indigo-600 py-16 relative overflow-hidden">
        <!-- Floating CTA Elements -->
        <div class="absolute inset-0 pointer-events-none">
            <div class="absolute top-10 left-10 text-white opacity-10 animate-bounce" style="animation-delay: 0s;">
                <i class="fas fa-star text-4xl"></i>
            </div>
            <div class="absolute top-20 right-20 text-white opacity-10 animate-bounce" style="animation-delay: 1s;">
                <i class="fas fa-heart text-3xl"></i>
            </div>
            <div class="absolute bottom-20 left-20 text-white opacity-10 animate-bounce" style="animation-delay: 2s;">
                <i class="fas fa-book-open text-4xl"></i>
            </div>
            <div class="absolute bottom-10 right-10 text-white opacity-10 animate-bounce" style="animation-delay: 0.5s;">
                <i class="fas fa-film text-3xl"></i>
            </div>
        </div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
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
    <footer class="bg-gray-900 text-white py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Company Info -->
                <div class="col-span-1 md:col-span-2">
                    <div class="flex items-center mb-4">
                        <img src="image.png" alt="Plotoryx Logo" class="h-10 w-10 mr-3">
                        <span class="text-2xl font-bold">Plotoryx</span>
                    </div>
                    <p class="text-gray-300 mb-4 max-w-md">
                        Never lose track of your favorite manhwa and movies. Organize your entertainment library with ease.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-twitter text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-discord text-xl"></i>
                        </a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">
                            <i class="fab fa-github text-xl"></i>
                        </a>
                    </div>
                </div>

                <!-- Product -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Product</h3>
                    <ul class="space-y-2">
                        <li><a href="#features" class="text-gray-400 hover:text-white transition-colors">Features</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">How it Works</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Pricing</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">API</a></li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-lg font-semibold mb-4">Support</h3>
                    <ul class="space-y-2">
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Help Center</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Contact Us</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy Policy</a></li>
                        <li><a href="#" class="text-gray-400 hover:text-white transition-colors">Terms of Service</a></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="border-t border-gray-800 mt-12 pt-8">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <div class="text-gray-400 text-sm mb-4 md:mb-0">
                        © 2026 Plotoryx. All rights reserved.
                    </div>
                    <div class="flex space-x-6 text-sm">
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Privacy</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Terms</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Cookies</a>
                        <a href="#" class="text-gray-400 hover:text-white transition-colors">Sitemap</a>
                    </div>
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

            // Show intro for 2.5 seconds, then transition
            setTimeout(function() {
                intro.classList.add('hidden');

                // Show main content after intro fades out
                setTimeout(function() {
                    mainContent.classList.add('show');
                }, 400); // Wait for intro fade to complete
            }, 2500); // Show intro for 2.5 seconds
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