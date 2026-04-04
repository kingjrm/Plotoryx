<?php
global $pdo;
$userId = $_SESSION['user_id'];

// Get current user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

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
                    $uploadDir = 'uploads/profile_pictures/';
                    if (!is_dir($uploadDir)) {
                        mkdir($uploadDir, 0755, true);
                    }

                    $fileName = uniqid() . '_' . basename($_FILES['profile_picture']['name']);
                    $uploadFile = $uploadDir . $fileName;

                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (in_array($_FILES['profile_picture']['type'], $allowedTypes)) {
                        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
                            $profilePicture = $uploadFile;
                        } else {
                            $message = 'Failed to upload profile picture.';
                            $messageType = 'error';
                        }
                    } else {
                        $message = 'Invalid file type. Please upload a JPEG, PNG, GIF, or WebP image.';
                        $messageType = 'error';
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
    }
}
?>

<div class="max-w-4xl mx-auto space-y-6">
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

    <!-- Profile Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Profile Settings</h2>
            <p class="text-sm text-gray-600">Update your personal information and profile picture.</p>
        </div>

        <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
            <!-- Profile Picture -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-3">Profile Picture</label>
                <div class="flex items-center space-x-4">
                    <img src="<?php echo $user['profile_picture'] ? $user['profile_picture'] : 'https://via.placeholder.com/80'; ?>"
                         alt="Profile Picture"
                         class="w-20 h-20 rounded-full border-4 border-white shadow-lg">
                    <div>
                        <input type="file" id="profile_picture" name="profile_picture" accept="image/*" class="hidden">
                        <label for="profile_picture" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 cursor-pointer transition-colors">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                            </svg>
                            Change Picture
                        </label>
                        <p class="text-xs text-gray-500 mt-1">JPG, PNG, GIF or WebP. Max size 5MB.</p>
                    </div>
                </div>
            </div>

            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors">
            </div>

            <!-- Email -->
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors">
            </div>

            <!-- Account Created -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Account Created</label>
                <p class="text-sm text-gray-600"><?php echo date('F j, Y', strtotime($user['created_at'])); ?></p>
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" name="update_profile" class="inline-flex items-center px-6 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:ring-1 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                    </svg>
                    Save Changes
                </button>
            </div>
        </form>
    </div>

    <!-- Password Settings -->
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Change Password</h2>
            <p class="text-sm text-gray-600">Update your password to keep your account secure.</p>
        </div>

        <form method="POST" class="p-6 space-y-6">
            <!-- Current Password -->
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-2">Current Password</label>
                <input type="password" id="current_password" name="current_password" required
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors">
            </div>

            <!-- New Password -->
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors">
                <p class="text-xs text-gray-500 mt-1">Must be at least 6 characters long.</p>
            </div>

            <!-- Confirm New Password -->
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6"
                       class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-1 focus:ring-gray-900 focus:border-gray-900 transition-colors">
            </div>

            <!-- Submit Button -->
            <div class="pt-4 border-t border-gray-200">
                <button type="submit" name="change_password" class="inline-flex items-center px-6 py-2 bg-gray-900 text-white text-sm font-medium rounded-lg hover:bg-gray-800 focus:ring-1 focus:ring-gray-900 focus:ring-offset-1 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                    </svg>
                    Change Password
                </button>
            </div>
        </form>
    </div>

    <!-- Danger Zone -->
    <div class="bg-white rounded-lg shadow border border-red-200">
        <div class="px-6 py-4 border-b border-red-200">
            <h2 class="text-lg font-semibold text-red-900">Danger Zone</h2>
            <p class="text-sm text-red-600">Irreversible and destructive actions.</p>
        </div>

        <div class="p-6">
            <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                <h3 class="text-sm font-medium text-red-900 mb-2">Delete Account</h3>
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