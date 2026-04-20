<?php
global $pdo;
$userId = $_SESSION['user_id'];

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Get user preferences (from database)
$userPreferences = getUserPreferences($userId);

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        // Handle profile update
        $name = trim($_POST['name']);
        $email = trim($_POST['email']);

        if (empty($name) || empty($email)) {
            $message = 'Name and email are required.';
            $messageType = 'error';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = 'Please enter a valid email address.';
            $messageType = 'error';
        } else {
            // Check if email is already taken by another user
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->execute([$email, $userId]);
            if ($stmt->fetch()) {
                $message = 'This email is already taken by another user.';
                $messageType = 'error';
            } else {
                // Handle profile picture upload
                $profilePicture = $user['profile_picture'];
                if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
                    $uploadResult = uploadImage($_FILES['profile_picture'], null, 'profile_pictures');
                    if (isset($uploadResult['error'])) {
                        $message = $uploadResult['error'];
                        $messageType = 'error';
                    } else {
                        $profilePicture = $uploadResult['success'];
                    }
                }

                if (empty($message)) {
                    // Update user profile
                    $stmt = $pdo->prepare("UPDATE users SET name = ?, email = ?, profile_picture = ? WHERE id = ?");
                    if ($stmt->execute([$name, $email, $profilePicture, $userId])) {
                        $message = 'Profile updated successfully!';
                        $messageType = 'success';

                        // Update session data
                        $_SESSION['user_name'] = $name;
                        $_SESSION['user_email'] = $email;
                        $_SESSION['profile_picture'] = $profilePicture;

                        // Refresh user data
                        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
                        $stmt->execute([$userId]);
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    } else {
                        $message = 'Failed to update profile.';
                        $messageType = 'error';
                    }
                }
            }
        }
    } elseif (isset($_POST['change_password'])) {
        // Handle password change
        $currentPassword = $_POST['current_password'];
        $newPassword = $_POST['new_password'];
        $confirmPassword = $_POST['confirm_password'];

        if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
            $message = 'All password fields are required.';
            $messageType = 'error';
        } elseif ($newPassword !== $confirmPassword) {
            $message = 'New passwords do not match.';
            $messageType = 'error';
        } elseif (strlen($newPassword) < 6) {
            $message = 'New password must be at least 6 characters long.';
            $messageType = 'error';
        } elseif (!password_verify($currentPassword, $user['password'])) {
            $message = 'Current password is incorrect.';
            $messageType = 'error';
        } else {
            // Update password
            $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
            if ($stmt->execute([$hashedPassword, $userId])) {
                $message = 'Password changed successfully!';
                $messageType = 'success';
            } else {
                $message = 'Failed to change password.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['delete_account'])) {
        // Handle account deletion
        $confirmEmail = trim($_POST['confirm_email']);

        if ($confirmEmail !== $user['email']) {
            $message = 'Email confirmation does not match your account email.';
            $messageType = 'error';
        } else {
            // Delete all user entries first (due to foreign key constraint)
            $stmt = $pdo->prepare("DELETE FROM entries WHERE user_id = ?");
            $stmt->execute([$userId]);

            // Delete user account
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            if ($stmt->execute([$userId])) {
                // Log out and redirect
                session_destroy();
                header("Location: index.php?message=Account deleted successfully");
                exit();
            } else {
                $message = 'Failed to delete account.';
                $messageType = 'error';
            }
        }
    } elseif (isset($_POST['update_preferences'])) {
        // Handle preferences update
        $defaultEntryType = $_POST['default_entry_type'] ?? 'manhwa';
        $itemsPerPage = (int)($_POST['items_per_page'] ?? 12);
        $theme = $_POST['theme'] ?? 'light';
        $showRatings = isset($_POST['show_ratings']) ? 1 : 0;
        $autoSave = isset($_POST['auto_save']) ? 1 : 0;

        // Store preferences in database
        $preferences = [
            'default_entry_type' => $defaultEntryType,
            'items_per_page' => (string)$itemsPerPage,
            'theme' => $theme,
            'show_ratings' => (string)$showRatings,
            'auto_save' => (string)$autoSave
        ];

        if (setUserPreferences($userId, $preferences)) {
            $message = 'Preferences updated successfully!';
            $messageType = 'success';
            // Update the userPreferences array
            $userPreferences = getUserPreferences($userId);
        } else {
            $message = 'Failed to update preferences.';
            $messageType = 'error';
        }
    }
}
?>

<?php
// Get active tab from URL or default to profile
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>

<div class="max-w-6xl mx-auto space-y-8">
    <!-- Success/Error Messages -->
    <?php if ($message): ?>
        <div class="bg-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-50 border border-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-200 text-<?php echo $messageType === 'success' ? 'green' : 'red'; ?>-700 px-4 py-3 rounded-lg">
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="<?php echo $messageType === 'success' ? 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z'; ?>"/>
                </svg>
                <?php echo htmlspecialchars($message); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Settings Navigation -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-6 py-4 bg-gradient-to-r from-gray-50 to-white border-b border-gray-100">
            <h1 class="text-2xl font-bold text-gray-900">Settings</h1>
            <p class="text-gray-600 mt-1">Manage your account preferences and settings</p>
        </div>

        <div class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <a href="?page=settings&tab=profile" class="group p-4 rounded-lg border-2 border-gray-200 hover:border-gray-900 transition-all duration-200 <?php echo $activeTab === 'profile' ? 'border-gray-900 bg-gray-50' : ''; ?>">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-gray-900 transition-colors">
                            <svg class="w-6 h-6 <?php echo $activeTab === 'profile' ? 'text-white' : 'text-gray-600 group-hover:text-white'; ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-gray-900">Profile</h3>
                        <p class="text-sm text-gray-500 mt-1">Personal info</p>
                    </div>
                </a>

                <a href="?page=settings&tab=security" class="group p-4 rounded-lg border-2 border-gray-200 hover:border-gray-900 transition-all duration-200 <?php echo $activeTab === 'security' ? 'border-gray-900 bg-gray-50' : ''; ?>">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-gray-900 transition-colors">
                            <svg class="w-6 h-6 <?php echo $activeTab === 'security' ? 'text-white' : 'text-gray-600 group-hover:text-white'; ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-gray-900">Security</h3>
                        <p class="text-sm text-gray-500 mt-1">Password & access</p>
                    </div>
                </a>

                <a href="?page=settings&tab=preferences" class="group p-4 rounded-lg border-2 border-gray-200 hover:border-gray-900 transition-all duration-200 <?php echo $activeTab === 'preferences' ? 'border-gray-900 bg-gray-50' : ''; ?>">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-gray-900 transition-colors">
                            <svg class="w-6 h-6 <?php echo $activeTab === 'preferences' ? 'text-white' : 'text-gray-600 group-hover:text-white'; ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-gray-900 group-hover:text-gray-900">Preferences</h3>
                        <p class="text-sm text-gray-500 mt-1">App settings</p>
                    </div>
                </a>

                <a href="?page=settings&tab=account" class="group p-4 rounded-lg border-2 border-red-200 hover:border-red-500 transition-all duration-200 <?php echo $activeTab === 'account' ? 'border-red-500 bg-red-50' : ''; ?>">
                    <div class="flex flex-col items-center text-center">
                        <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mb-3 group-hover:bg-red-500 transition-colors">
                            <svg class="w-6 h-6 <?php echo $activeTab === 'account' ? 'text-white' : 'text-red-600 group-hover:text-white'; ?> transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                        <h3 class="font-semibold text-red-900 group-hover:text-red-900">Account</h3>
                        <p class="text-sm text-red-600 mt-1">Management</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Profile Settings Card -->
    <div id="profile" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden <?php echo $activeTab !== 'profile' ? 'hidden' : ''; ?>">
        <div class="px-6 py-4 bg-gradient-to-r from-blue-50 to-indigo-50 border-b border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Profile Information</h2>
                    <p class="text-gray-600">Update your personal information and profile picture</p>
                </div>
            </div>
        </div>

        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <!-- Profile Picture -->
            <div class="flex items-start space-x-6">
                <div class="flex-shrink-0">
                    <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/100'; ?>"
                         alt="Profile Picture"
                         class="w-24 h-24 rounded-full border-4 border-white shadow-lg">
                </div>
                <div class="flex-1">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Profile Picture</label>
                    <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                    <label for="profile_picture" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                        </svg>
                        Change Picture
                    </label>
                    <p class="text-xs text-gray-500 mt-2">JPG, PNG, GIF or WebP. Max size 5MB.</p>
                </div>
            </div>

            <!-- Name and Email -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                </div>
            </div>

            <!-- Account Created -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Created</label>
                <p class="text-sm text-gray-600 bg-gray-50 px-3 py-2 rounded-lg"><?php echo date('F j, Y \a\t g:i A', strtotime($user['created_at'])); ?></p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" name="update_profile" class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Security Settings Card -->
    <div id="security" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden <?php echo $activeTab !== 'security' ? 'hidden' : ''; ?>">
        <div class="px-6 py-4 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Security Settings</h2>
                    <p class="text-gray-600">Manage your password and security preferences</p>
                </div>
            </div>
        </div>

        <form method="POST" class="p-6 space-y-6">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters long</p>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" name="change_password" class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change Password
                </button>
            </div>
        </form>
    </div>

    <!-- Preferences Settings Card -->
    <div id="preferences" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden <?php echo $activeTab !== 'preferences' ? 'hidden' : ''; ?>">
        <div class="px-6 py-4 bg-gradient-to-r from-purple-50 to-pink-50 border-b border-gray-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">App Preferences</h2>
                    <p class="text-gray-600">Customize your Plotoryx experience</p>
                </div>
            </div>
        </div>

        <form method="POST" class="p-6 space-y-6">
            <!-- Default Entry Type -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">Default Entry Type</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative">
                        <input type="radio" name="default_entry_type" value="manhwa" <?php echo $userPreferences['default_entry_type'] === 'manhwa' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition-all">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <div>
                                    <span class="font-medium text-gray-900">Manhwa</span>
                                    <p class="text-sm text-gray-500">Korean comics</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="default_entry_type" value="movie" <?php echo $userPreferences['default_entry_type'] === 'movie' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition-all">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                </svg>
                                <div>
                                    <span class="font-medium text-gray-900">Movie</span>
                                    <p class="text-sm text-gray-500">Film entries</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Items Per Page -->
            <div>
                <label for="items_per_page" class="block text-sm font-medium text-gray-700 mb-2">Items Per Page</label>
                <select id="items_per_page" name="items_per_page" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                    <option value="6" <?php echo $userPreferences['items_per_page'] == '6' ? 'selected' : ''; ?>>6 items</option>
                    <option value="12" <?php echo $userPreferences['items_per_page'] == '12' ? 'selected' : ''; ?>>12 items</option>
                    <option value="24" <?php echo $userPreferences['items_per_page'] == '24' ? 'selected' : ''; ?>>24 items</option>
                    <option value="48" <?php echo $userPreferences['items_per_page'] == '48' ? 'selected' : ''; ?>>48 items</option>
                </select>
            </div>

            <!-- Theme -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-4">Theme</label>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <label class="relative">
                        <input type="radio" name="theme" value="light" <?php echo $userPreferences['theme'] === 'light' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition-all">
                            <div class="flex items-center">
                                <svg class="w-6 h-4 text-yellow-500 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                </svg>
                                <div>
                                    <span class="font-medium text-gray-900">Light</span>
                                    <p class="text-sm text-gray-500">Bright theme</p>
                                </div>
                            </div>
                        </div>
                    </label>
                    <label class="relative">
                        <input type="radio" name="theme" value="dark" <?php echo $userPreferences['theme'] === 'dark' ? 'checked' : ''; ?> class="sr-only peer">
                        <div class="p-4 border-2 border-gray-200 rounded-lg cursor-pointer peer-checked:border-purple-500 peer-checked:bg-purple-50 hover:border-purple-300 transition-all">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-gray-600 mr-3" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                                </svg>
                                <div>
                                    <span class="font-medium text-gray-900">Dark</span>
                                    <p class="text-sm text-gray-500">Dark theme</p>
                                </div>
                            </div>
                        </div>
                    </label>
                </div>
            </div>

            <!-- Display Options -->
            <div class="space-y-4">
                <label class="block text-sm font-medium text-gray-700">Display Options</label>

                <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                    <input type="checkbox" id="show_ratings" name="show_ratings" <?php echo $userPreferences['show_ratings'] === '1' ? 'checked' : ''; ?> class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="show_ratings" class="ml-3 block text-sm text-gray-900">
                        <span class="font-medium">Show ratings on entry cards</span>
                        <p class="text-gray-500">Display star ratings on your entries</p>
                    </label>
                </div>

                <div class="flex items-center p-4 border border-gray-200 rounded-lg">
                    <input type="checkbox" id="auto_save" name="auto_save" <?php echo $userPreferences['auto_save'] === '1' ? 'checked' : ''; ?> class="h-4 w-4 text-purple-600 focus:ring-purple-500 border-gray-300 rounded">
                    <label for="auto_save" class="ml-3 block text-sm text-gray-900">
                        <span class="font-medium">Auto-save form data</span>
                        <p class="text-gray-500">Automatically save your progress (coming soon)</p>
                    </label>
                </div>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" name="update_preferences" class="inline-flex items-center px-6 py-2 bg-purple-600 text-white text-sm font-medium rounded-lg hover:bg-purple-700 focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Preferences
                </button>
            </div>
        </form>
    </div>

    <!-- Account Settings Card -->
    <div id="account" class="bg-white rounded-xl shadow-sm border border-red-200 overflow-hidden <?php echo $activeTab !== 'account' ? 'hidden' : ''; ?>">
        <div class="px-6 py-4 bg-gradient-to-r from-red-50 to-pink-50 border-b border-red-100">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-red-900">Account Management</h2>
                    <p class="text-red-600">Manage your account settings and data</p>
                </div>
            </div>
        </div>

        <div class="p-6 space-y-6">
            <!-- Account Statistics -->
            <div class="bg-gray-50 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Statistics</h3>
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <?php
                    // Get user stats
                    $stmt = $pdo->prepare("SELECT type, COUNT(*) as count FROM entries WHERE user_id = ? GROUP BY type");
                    $stmt->execute([$userId]);
                    $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                    $totalManhwa = $totalMovies = 0;
                    foreach ($stats as $stat) {
                        if ($stat['type'] == 'manhwa') $totalManhwa = $stat['count'];
                        if ($stat['type'] == 'movie') $totalMovies = $stat['count'];
                    }

                    $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND favorite = 1");
                    $stmt->execute([$userId]);
                    $favorites = $stmt->fetch()['count'];
                    ?>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-blue-600"><?php echo $totalManhwa; ?></div>
                        <div class="text-sm text-gray-600">Manhwa</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-green-600"><?php echo $totalMovies; ?></div>
                        <div class="text-sm text-gray-600">Movies</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-yellow-600"><?php echo $favorites; ?></div>
                        <div class="text-sm text-gray-600">Favorites</div>
                    </div>
                    <div class="text-center">
                        <div class="text-2xl font-bold text-purple-600"><?php echo floor((time() - strtotime($user['created_at'])) / (60*60*24)); ?></div>
                        <div class="text-sm text-gray-600">Days</div>
                    </div>
                </div>
            </div>

            <!-- Data Export -->
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-blue-900 mb-2">Export Your Data</h3>
                        <p class="text-blue-700 mb-4">Download a copy of all your data in JSON format for backup or migration.</p>
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Data
                        </button>
                    </div>
                </div>
            </div>

            <!-- Danger Zone -->
            <div class="bg-red-50 border border-red-200 rounded-lg p-6">
                <div class="flex items-start">
                    <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-4">
                        <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-red-900 mb-2">Delete Account</h3>
                        <p class="text-red-700 mb-4">
                            Once you delete your account, there is no going back. This will permanently delete your account
                            and remove all your data from our servers.
                        </p>

                        <form method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')" class="space-y-4">
                            <div>
                                <label for="confirm_email" class="block text-sm font-medium text-red-700 mb-2">
                                    Type your email address to confirm: <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                                </label>
                                <input type="email" id="confirm_email" name="confirm_email" required
                                       placeholder="Enter your email address"
                                       class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors">
                            </div>

                            <button type="submit" name="delete_account" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
                        <h4 class="text-sm font-medium text-gray-900 mb-3">Account Statistics</h4>
                        <div class="grid grid-cols-2 gap-4 text-sm">
                            <?php
                            // Get user stats
                            $stmt = $pdo->prepare("SELECT type, COUNT(*) as count FROM entries WHERE user_id = ? GROUP BY type");
                            $stmt->execute([$userId]);
                            $stats = $stmt->fetchAll(PDO::FETCH_ASSOC);

                            $totalManhwa = $totalMovies = 0;
                            foreach ($stats as $stat) {
                                if ($stat['type'] == 'manhwa') $totalManhwa = $stat['count'];
                                if ($stat['type'] == 'movie') $totalMovies = $stat['count'];
                            }

                            $stmt = $pdo->prepare("SELECT COUNT(*) as count FROM entries WHERE user_id = ? AND favorite = 1");
                            $stmt->execute([$userId]);
                            $favorites = $stmt->fetch()['count'];
                            ?>
                            <div>
                                <span class="text-gray-500">Total Manhwa:</span>
                                <span class="font-medium"><?php echo $totalManhwa; ?></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Total Movies:</span>
                                <span class="font-medium"><?php echo $totalMovies; ?></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Favorites:</span>
                                <span class="font-medium"><?php echo $favorites; ?></span>
                            </div>
                            <div>
                                <span class="text-gray-500">Account Age:</span>
                                <span class="font-medium"><?php echo floor((time() - strtotime($user['created_at'])) / (60*60*24)); ?> days</span>
                            </div>
                        </div>
                    </div>

                    <!-- Data Export -->
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Export Your Data</h4>
                        <p class="text-sm text-blue-700 mb-3">Download a copy of all your data in JSON format.</p>
                        <button class="inline-flex items-center px-4 py-2 bg-blue-600 text-white text-sm font-medium rounded hover:bg-blue-700 transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Export Data
                        </button>
                    </div>

                    <!-- Danger Zone -->
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="text-sm font-medium text-red-900 mb-2">Delete Account</h4>
                        <p class="text-sm text-red-700 mb-4">
                            Once you delete your account, there is no going back. This will permanently delete your account
                            and remove all your data from our servers.
                        </p>

                        <form method="POST" onsubmit="return confirm('Are you absolutely sure you want to delete your account? This action cannot be undone.')" class="space-y-4">
                            <div>
                                <label for="confirm_email" class="block text-sm font-medium text-red-700 mb-2">
                                    Type your email address to confirm: <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                                </label>
                                <input type="email" id="confirm_email" name="confirm_email" required
                                       placeholder="Enter your email address"
                                       class="w-full px-3 py-2 border border-red-300 rounded-lg focus:ring-1 focus:ring-red-500 focus:border-red-500 transition-colors">
                            </div>

                            <button type="submit" name="delete_account" class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:ring-1 focus:ring-red-500 focus:ring-offset-1 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Account
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>