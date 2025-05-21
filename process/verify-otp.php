<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_SESSION['email_for_otp'] ?? '';
    $otp = trim($_POST['otp']);
    
    if (empty($email)) {
        $_SESSION['error'] = 'Phiên đăng nhập đã hết hạn. Vui lòng thử lại.';
        header('Location: ../login.php');
        exit;
    }
    
    if (empty($otp) || strlen($otp) !== 6 || !is_numeric($otp)) {
        $_SESSION['error'] = 'Mã OTP không hợp lệ. Vui lòng nhập 6 chữ số.';
        header('Location: ../verify-otp.php');
        exit;
    }
    
    // Verify OTP
    if (verifyOTP($email, $otp)) {
        // Check if user exists
        $user = getUserByEmail($email);
        
        if (!$user) {
            // Create new user
            $userId = createUserWithEmail($email);
            $user = [
                'id' => $userId,
                'email' => $email,
                'name' => null
            ];
        }
        
        // Set user session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        
        // Clear OTP session
        unset($_SESSION['email_for_otp']);
        
        // Redirect to homepage or previous page
        $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
        unset($_SESSION['redirect_after_login']);
        
        header('Location: ../' . $redirect);
    } else {
        $_SESSION['error'] = 'Mã OTP không đúng hoặc đã hết hạn. Vui lòng thử lại.';
        header('Location: ../verify-otp.php');
    }
    exit;
}

// If not a valid request, redirect to homepage
header('Location: ../index.php');
exit;
