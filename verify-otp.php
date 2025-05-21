<?php
session_start();
include 'config/database.php';

// If user is already logged in, redirect to homepage
if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

// If no email is stored for OTP, redirect to login
if (!isset($_SESSION['email_for_otp'])) {
    header('Location: login.php');
    exit;
}

$email = $_SESSION['email_for_otp'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Xác thực OTP - Beauty Cent'</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="logo">
                <span class="logo-prefix">beauty</span>
                <span class="logo-main">cent'</span>
            </div>
            <h1>Xác thực OTP</h1>
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
        
        <div class="otp-info">
            <p>Chúng tôi đã gửi mã xác thực đến email: <strong><?php echo htmlspecialchars($email); ?></strong></p>
            <p>Vui lòng kiểm tra hộp thư đến và nhập mã 6 số dưới đây.</p>
        </div>
        
        <form class="login-form" action="process/verify-otp.php" method="POST">
            <div class="form-group">
                <label for="otp">Mã xác thực (OTP)</label>
                <input type="text" id="otp" name="otp" placeholder="Nhập mã 6 số" maxlength="6" pattern="[0-9]{6}" required>
            </div>
            
            <div class="form-actions">
                <button type="submit" class="btn-primary">Xác thực</button>
            </div>
        </form>
        
        <div class="login-footer">
            <p>Chưa nhận được mã?</p>
            <form action="process/send-otp.php" method="POST">
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($email); ?>">
                <button type="submit" class="btn-link">Gửi lại mã</button>
            </form>
            <a href="login.php" class="back-link">Thay đổi email</a>
        </div>
    </div>
</body>
</html>
