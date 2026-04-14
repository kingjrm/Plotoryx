<?php
session_start();
require_once __DIR__ . '/db.php';

// Load environment variables
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
    return true;
}

loadEnv(__DIR__ . '/../.env');

// Function to register user
function registerUser($name, $email, $password, $profilePicture) {
    global $pdo;
    try {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (name, email, password, profile_picture, email_verified) VALUES (?, ?, ?, ?, FALSE)");
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
            if (!$user['email_verified']) {
                return ['error' => 'Please verify your email before logging in.'];
            }
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

    // Return user data from session for better performance and realtime updates
    return [
        'id' => $_SESSION['user_id'],
        'name' => $_SESSION['user_name'] ?? '',
        'email' => $_SESSION['user_email'] ?? '',
        'profile_picture' => $_SESSION['profile_picture'] ?? null,
        'created_at' => null // We don't store this in session, but it's not used in the UI
    ];
}

// Function to upload image
function uploadImage($file, $targetDir = null, $subdir = '') {
    if ($targetDir === null) {
        $targetDir = __DIR__ . '/../uploads/';
    }
    if (!empty($subdir)) {
        $targetDir .= $subdir . '/';
        if (!is_dir($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
    }
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
        // Return path relative to web root
        return ['success' => 'uploads/' . (!empty($subdir) ? $subdir . '/' : '') . $fileName];
    }
    return ['error' => 'Failed to move uploaded file'];
}

// Function to generate OTP
function generateOTP() {
    return rand(100000, 999999);
}

// Function to send OTP email
function sendOTPEmail($email, $otp) {
    // Check if PHPMailer exists
    $phpmailerPath = __DIR__ . '/PHPMailer.php';
    if (file_exists($phpmailerPath)) {
        // Use PHPMailer
        require_once __DIR__ . '/PHPMailer.php';
        require_once __DIR__ . '/SMTP.php';
        require_once __DIR__ . '/Exception.php';

        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host = getenv('EMAIL_HOST');
            $mail->SMTPAuth = true;
            $mail->Username = getenv('EMAIL_USERNAME');
            $mail->Password = getenv('EMAIL_PASSWORD');
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = getenv('EMAIL_PORT');

            // Recipients
            $mail->setFrom(getenv('EMAIL_FROM_ADDRESS'), getenv('EMAIL_FROM_NAME'));
            $mail->addAddress($email);

            // Content
            $mail->isHTML(false);
            $mail->Subject = 'Your OTP for Plotoryx';
            $mail->Body = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("PHPMailer Error: " . $mail->ErrorInfo);
            return false;
        }
    } else {
        // Fallback to mail() function (requires sendmail configuration)
        $subject = "Your OTP for Plotoryx";
        $message = "Your OTP code is: $otp\n\nThis code will expire in 10 minutes.";
        $headers = "From: " . getenv('EMAIL_FROM_ADDRESS') . "\r\n";
        $headers .= "Reply-To: " . getenv('EMAIL_FROM_ADDRESS') . "\r\n";
        $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

        if (mail($email, $subject, $message, $headers)) {
            return true;
        } else {
            return false;
        }
    }
}

// Function to verify OTP
function verifyOTP($email, $otp) {
    if (isset($_SESSION['otp_code']) && isset($_SESSION['otp_email']) && isset($_SESSION['otp_expiry'])) {
        if ($_SESSION['otp_email'] == $email && $_SESSION['otp_code'] == $otp && time() < $_SESSION['otp_expiry']) {
            // Clear OTP
            unset($_SESSION['otp_code'], $_SESSION['otp_email'], $_SESSION['otp_expiry']);
            return true;
        }
    }
    return false;
}

// Function to verify user email
function verifyUserEmail($email) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("UPDATE users SET email_verified = TRUE WHERE email = ?");
        $stmt->execute([$email]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}

// Function to reset password
function resetPassword($email, $newPassword) {
    global $pdo;
    try {
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
        $stmt->execute([$hashedPassword, $email]);
        return true;
    } catch (PDOException $e) {
        return false;
    }
}
?>