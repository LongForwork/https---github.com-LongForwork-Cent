<?php
session_start();
header('Content-Type: application/json');
require_once '../models/User.php';
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

// Kiểm tra người dùng tồn tại
$userModel = new User();
$user = $userModel->findByEmail($email);

if ($user) {
    // Người dùng tồn tại, thiết lập session và trả về thông tin
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['email'] = $user['email'];
    $_SESSION['name'] = $user['name'];
    
    echo json_encode([
        'exists' => true,
        'user' => $user
    ]);
} else {
    // Người dùng không tồn tại
    echo json_encode([
        'exists' => false
    ]);
}
