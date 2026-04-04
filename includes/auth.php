<?php
session_start();
require_once __DIR__ . '/db.php';

// Function to register user
function registerUser($name, $email, $password, $profilePicture) {
    global $pdo;
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $hashedPassword, $profilePicture]);
        return ['success' => true];
    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            return ['error' => 'Email already exists'];
        }
        return ['error' => 'Registration failed: ' . $e->getMessage()];
    }
}

// Function to login user
function loginUser($email, $password) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['profile_picture'] = $user['profile_picture'];
            return ['success' => true];
        }
        return ['error' => 'Invalid email or password'];
    } catch (PDOException $e) {
        return ['error' => 'Login failed: ' . $e->getMessage()];
    }
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to logout
function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Function to get current user
function getCurrentUser() {
    if (!isLoggedIn()) return null;
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Function to upload image
function uploadImage($file, $targetDir = 'uploads/') {
    if (!isset($file) || $file['error'] != 0) {
        return ['error' => 'No file uploaded or upload error'];
    }

    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed'];
    }

    if ($file['size'] > 5 * 1024 * 1024) { // 5MB limit
        return ['error' => 'File size too large. Maximum 5MB allowed'];
    }

    $fileName = uniqid() . '_' . basename($file['name']);
    $targetFile = $targetDir . $fileName;

    if (move_uploaded_file($file['tmp_name'], $targetFile)) {
        return ['success' => $targetFile];
    }
    return ['error' => 'Failed to move uploaded file'];
}
?>