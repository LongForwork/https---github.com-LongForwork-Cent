<?php
session_start();
header('Content-Type: application/json');
require_once '../models/User.php';
require_once '../models/OtpVerification.php';
require_once '../utils/helpers.php';

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(createError('Phương thức không được hỗ trợ'));
    exit;
}

// Lấy dữ liệu từ request
$data = json_decode(file_get_contents('php://input'), true);

// Kiểm tra dữ liệu
if (!isset($data['otp_code']) || empty($data['otp_code']) || !isset($data['email']) || empty($data['email'])) {
    echo json_encode(createError('Mã xác nhận và email là bắt buộc'));
    exit;
}

$otp_code = $data['otp_code'];
$email = $data['email'];
$name = isset($data['name']) ? $data['name'] : null;

// Kiểm tra email hợp lệ
if (!isValidEmail($email)) {
    echo json_encode(createError('Email không hợp lệ'));
    exit;
}

// Xác thực mã OTP
$otpModel = new OtpVerification();
$is_valid = $otpModel->verify($email, $otp_code);

if (!$is_valid) {
    echo json_encode(createError('Mã xác nhận không hợp lệ hoặc đã hết hạn'));
    exit;
}

// Mã OTP hợp lệ, tạo người dùng mới
$userModel = new User();
$user_id = $userModel->create($email, $name);

if (!$user_id) {
    echo json_encode(createError('Không thể tạo người dùng mới'));
    exit;
}

$user = [
    'id' => $user_id,
    'email' => $email,
    'name' => $name
];

// Thiết lập session
$_SESSION['user_id'] = $user['id'];
$_SESSION['email'] = $user['email'];
$_SESSION['name'] = $user['name'];

// Xóa mã OTP đã xác thực
$otpModel->deleteByEmail($email);

echo json_encode($user);
