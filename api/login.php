<?php
session_start();
header('Content-Type: application/json');
require_once '../models/User.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['error' => 'Phương thức không được hỗ trợ']);
    exit;
}

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['phone_number']) || empty($data['phone_number'])) {
    echo json_encode(['error' => 'Số điện thoại là bắt buộc']);
    exit;
}

$phone_number = $data['phone_number'];

$userModel = new User();
$user = $userModel->findByPhoneNumber($phone_number);

// If user doesn't exist, return error (user should use verification flow)
if (!$user) {
    echo json_encode(['error' => 'Người dùng không tồn tại']);
    exit;
}

// Set user session
$_SESSION['user_id'] = $user['id'];
$_SESSION['phone_number'] = $user['phone_number'];
$_SESSION['name'] = $user['name'];

echo json_encode($user);
