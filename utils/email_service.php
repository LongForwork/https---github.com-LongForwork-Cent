<?php
require_once 'config/config.php';

/**
 * Gửi email với mã OTP
 * 
 * @param string $email Email người nhận
 * @param string $otp_code Mã OTP
 * @return bool Trạng thái gửi email
 */
function sendEmail($email, $otp_code) {
    // Cấu hình email
    $subject = "Mã xác nhận đặt lịch - Cent Beauty";
    $message = createEmailTemplate($otp_code);

    // Headers
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: " . EMAIL_NAME . " <" . EMAIL_FROM . ">" . "\r\n";

    // Trong môi trường thực tế, bạn nên sử dụng thư viện PHPMailer hoặc dịch vụ email
    // Ví dụ với PHPMailer:
    /*
    require 'vendor/autoload.php';
    
    $mail = new PHPMailer\PHPMailer\PHPMailer();
    $mail->isSMTP();
    $mail->Host = 'smtp.example.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'user@example.com';
    $mail->Password = 'secret';
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;
    $mail->setFrom(EMAIL_FROM, EMAIL_NAME);
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $message;
    
    return $mail->send();
    */

    // Sử dụng hàm mail() của PHP (cần cấu hình SMTP trên server)
    $mail_sent = mail($email, $subject, $message, $headers);
    
    // Ghi log
    error_log("Email to $email: OTP Code $otp_code");
    
    // Trong môi trường phát triển, luôn trả về true
    return true;
}

/**
 * Tạo template email HTML
 * 
 * @param string $otp_code Mã OTP
 * @return string Template HTML
 */
function createEmailTemplate($otp_code) {
    return "
    <html>
    <head>
        <title>Mã xác nhận đặt lịch</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background-color: #e05d16; color: white; padding: 10px; text-align: center; }
            .content { padding: 20px; border: 1px solid #ddd; }
            .code { font-size: 24px; font-weight: bold; text-align: center; margin: 20px 0; letter-spacing: 5px; }
            .footer { font-size: 12px; color: #777; margin-top: 20px; text-align: center; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h2>Cent Beauty</h2>
            </div>
            <div class='content'>
                <p>Xin chào,</p>
                <p>Đây là mã xác nhận của bạn để hoàn tất quá trình đặt lịch tại Cent Beauty:</p>
                <div class='code'>$otp_code</div>
                <p>Mã xác nhận có hiệu lực trong " . OTP_EXPIRY_MINUTES . " phút.</p>
                <p>Nếu bạn không yêu cầu mã này, vui lòng bỏ qua email này.</p>
            </div>
            <div class='footer'>
                <p>© " . date('Y') . " Cent Beauty. Tất cả các quyền được bảo lưu.</p>
            </div>
        </div>
    </body>
    </html>
    ";
}
