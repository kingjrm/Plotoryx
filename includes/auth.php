<?php
session_start();
require_once 'db.php';

// Function to register user
function registerUser($name, $email, $password, $profilePicture) {
    global $pdo;
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, profile_picture) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$name, $email, $hashedPassword, $profilePicture]);
}

// Function to login user
function loginUser($email, $password) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['profile_picture'] = $user['profile_picture'];
        return true;
    }
    return false;
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
    if ($file['error'] == 0) {
        $fileName = uniqid() . '_' . basename($file['name']);
        $targetFile = $targetDir . $fileName;
        if (move_uploaded_file($file['tmp_name'], $targetFile)) {
            return $targetFile;
        }
    }
    return false;
}
?>