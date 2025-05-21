<?php
session_start();
include 'config/database.php';

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// Store the current page as redirect after login
if (isset($_SERVER['HTTP_REFERER']) && !strpos($_SERVER['HTTP_REFERER'], 'login.php') && !strpos($_SERVER['HTTP_REFERER'], 'verify-otp.php')) {
    $_SESSION['redirect_after_login'] = basename($_SERVER['HTTP_REFERER']);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Beauty Cent'</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <span class="logo-prefix">beauty</span>
                <span class="logo-main">cent'</span>
            </div>
            <h1>Đăng nhập</h1>
        </div>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="error-message">
                <?php 
                echo $_SESSION['error']; 
                unset($_SESSION['error']);
                ?>
            </div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="success-message">
                <?php 
                echo $_SESSION['success']; 
                unset($_SESSION['success']);
                ?>
            </div>
        <?php endif; ?>
        
        <form class="login-form" action="process/send-otp.php" method="POST">
            <div class="form-group">
                <label for="email">Email của bạn</label>
                <input type="email" id="email" name="email" placeholder="Nhập email của bạn" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Gửi mã xác thực</button>
            </div>
        </form>
        
        <div class="login-footer">
            <a href="index.php" class="back-link">Quay lại trang chủ</a>
        </div>
    </div>
</body>
</html>
