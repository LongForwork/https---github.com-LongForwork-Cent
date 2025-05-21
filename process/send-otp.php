<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Email không hợp lệ. Vui lòng nhập đúng định dạng email.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Generate OTP
    $otp = generateOTP();
    
    // Save OTP to database
    if (saveOTP($email, $otp)) {
        // Prepare email content
        $subject = 'Mã xác thực đăng nhập - Beauty Cent';
        $message = '
        <html>
        <head>
            <title>Mã xác thực đăng nhập</title>
        </head>
        <body>
            <div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto;">
                <div style="background-color: #e65100; color: white; padding: 20px; text-align: center;">
                    <h1 style="margin: 0;">Beauty Cent\'</h1>
                </div>
                <div style="padding: 20px; border: 1px solid #ddd; border-top: none;">
                    <p>Xin chào,</p>
                    <p>Dưới đây là mã xác thực (OTP) để đăng nhập vào tài khoản Beauty Cent\' của bạn:</p>
                    <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 24px; letter-spacing: 5px; font-weight: bold; margin: 20px 0;">
                        '.$otp.'
                    </div>
                    <p>Mã này có hiệu lực trong vòng 15 phút.</p>
                    <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
                    <p>Trân trọng,<br>Đội ngũ Beauty Cent\'</p>
                </div>
                <div style="background-color: #f5f5f5; padding: 15px; text-align: center; font-size: 12px; color: #777;">
                    <p>© '.date('Y').' Beauty Cent\'. Tất cả các quyền được bảo lưu.</p>
                </div>
            </div>
        </body>
        </html>
        ';
        
        // Send OTP via email
        if (sendEmail($email, $subject, $message)) {
            $_SESSION['email_for_otp'] = $email;
            $_SESSION['success'] = 'Mã xác thực đã được gửi đến email của bạn.';
            header('Location: ../verify-otp.php');
        } else {
            $_SESSION['error'] = 'Không thể gửi email. Vui lòng thử lại sau.';
            header('Location: ' . $_SERVER['HTTP_REFERER']);
        }
    } else {
        $_SESSION['error'] = 'Có lỗi xảy ra. Vui lòng thử lại sau.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }
    exit;
}

// If not a valid request, redirect to homepage
header('Location: ../index.php');
exit;
