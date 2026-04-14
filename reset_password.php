<?php
require_once 'includes/auth.php';

$email = $_GET['email'] ?? '';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $otp = trim($_POST['otp']);
    $newPassword = $_POST['new_password'];
    $confirmPassword = $_POST['confirm_password'];

    if (empty($otp) || empty($newPassword) || empty($confirmPassword)) {
        $error = "All fields are required";
    } elseif (!is_numeric($otp) || strlen($otp) != 6) {
        $error = "Invalid OTP format";
    } elseif (strlen($newPassword) < 6) {
        $error = "Password must be at least 6 characters";
    } elseif ($newPassword !== $confirmPassword) {
        $error = "Passwords do not match";
    } else {
        if (verifyOTP($email, $otp)) {
            if (resetPassword($email, $newPassword)) {
                $success = "Password reset successfully! You can now log in with your new password.";
                // Redirect to login after 3 seconds
                header("refresh:3;url=login.php");
            } else {
                $error = "Failed to reset password. Please try again.";
            }
        } else {
            $error = "Invalid or expired OTP";
        }
    }
}

// Resend OTP
if (isset($_POST['resend'])) {
    $otp = generateOTP();
    $_SESSION['otp_code'] = $otp;
    $_SESSION['otp_email'] = $email;
    $_SESSION['otp_expiry'] = time() + 600;
    $_SESSION['otp_purpose'] = 'reset';

    if (sendOTPEmail($email, $otp)) {
        $success = "OTP resent to your email.";
    } else {
        $error = "Failed to resend OTP.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plotoryx - Reset Password</title>
    <link rel="icon" type="image/png" href="image.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; }
        .bg-gradient-custom { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
    </style>
</head>
<body class="bg-gradient-custom min-h-screen flex items-center justify-center p-4">
    <div class="bg-white p-8 rounded-2xl shadow-2xl w-full max-w-md transform transition-all duration-300 hover:scale-105">
        <div class="text-center mb-6">
            <h1 class="text-3xl font-bold text-gray-900">Reset Password</h1>
            <p class="text-gray-600 mt-2">Enter the code and your new password</p>
        </div>

        <?php if (!empty($error)): ?>
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-6">
                <?php echo htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <form action="reset_password.php?email=<?php echo urlencode($email); ?>" method="POST" class="space-y-6">
            <div>
                <label for="otp" class="block text-sm font-medium text-gray-700 mb-2">OTP Code</label>
                <input type="text" id="otp" name="otp" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200 text-center text-2xl tracking-widest"
                       placeholder="000000" maxlength="6">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-2">New Password</label>
                <input type="password" id="new_password" name="new_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                       placeholder="Enter new password">
            </div>
            <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required
                       class="w-full px-4 py-3 border border-gray-300 rounded-lg shadow-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 transition-all duration-200"
                       placeholder="Confirm new password">
            </div>
            <button type="submit"
                    class="w-full bg-indigo-600 text-white py-3 px-4 rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 transform hover:scale-105">
                Reset Password
            </button>
        </form>

        <form action="reset_password.php?email=<?php echo urlencode($email); ?>" method="POST" class="mt-4">
            <button type="submit" name="resend" class="w-full text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200">
                Resend OTP
            </button>
        </form>

        <div class="mt-6 text-center">
            <p class="text-sm text-gray-600">
                <a href="forget_password.php" class="text-indigo-600 hover:text-indigo-500 font-medium transition-colors duration-200">
                    Back to Forgot Password
                </a>
            </p>
        </div>
    </div>
</body>
</html>