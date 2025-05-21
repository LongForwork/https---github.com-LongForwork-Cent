<?php
session_start();
header('Content-Type: application/json');
require_once '../models/OtpVerification.php';
require_once '../utils/email_service.php';
require_once '../utils/helpers.php';

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(createError('Phương thức không được hỗ trợ'));
    exit;
}

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra dữ liệu
if (!isset($data['email']) || empty($data['email'])) {
    echo json_encode(createError('Email là bắt buộc'));
    exit;
}

$email = $data['email'];

// Kiểm tra email hợp lệ
if (!isValidEmail($email)) {
    echo json_encode(createError('Email không hợp lệ'));
    exit;
}

// Kiểm tra số lần gửi OTP
$otpModel = new OtpVerification();
$attempts = $otpModel->countRecentAttempts($email, 60); // Kiểm tra trong 60 phút

if ($attempts >= 5) {
    echo json_encode(createError('Bạn đã gửi quá nhiều yêu cầu. Vui lòng thử lại sau.'));
    exit;
}

// Tạo mã OTP
$otp_code = generateOTP(6);

// Lưu mã OTP vào cơ sở dữ liệu
$otpModel->create($email, $otp_code);

// Gửi email với mã OTP
$email_sent = sendEmail($email, $otp_code);

if (!$email_sent) {
    echo json_encode(createError('Không thể gửi mã xác nhận. Vui lòng thử lại sau.'));
    exit;
}

echo json_encode([
    'message' => 'Mã xác nhận đã được gửi đến email của bạn',
    'debug_code' => $otp_code // Xóa dòng này trong môi trường production
]);
