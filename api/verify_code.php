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

if (!isset($data['code']) || empty($data['code']) || !isset($data['phone_number']) || empty($data['phone_number'])) {
    echo json_encode(['error' => 'Mã xác nhận và số điện thoại là bắt buộc']);
    exit;
}

$code = $data['code'];
$phone_number = $data['phone_number'];
$name = isset($data['name']) ? $data['name'] : null;

// Check if verification exists and is valid
if (!isset($_SESSION['verification']) || 
    $_SESSION['verification']['phone_number'] !== $phone_number ||
    $_SESSION['verification']['code'] !== $code ||
    $_SESSION['verification']['expires'] < time()) {
    
    // Increment attempts if verification exists
    if (isset($_SESSION['verification']) && $_SESSION['verification']['phone_number'] === $phone_number) {
        $_SESSION['verification']['attempts'] = ($_SESSION['verification']['attempts'] ?? 0) + 1;
        
        // If too many attempts, invalidate the code
        if ($_SESSION['verification']['attempts'] >= 5) {
            unset($_SESSION['verification']);
            echo json_encode(['error' => 'Quá nhiều lần thử không thành công. Vui lòng yêu cầu mã mới.']);
            exit;
        }
    }
    
    echo json_encode(['error' => 'Mã xác nhận không hợp lệ hoặc đã hết hạn']);
    exit;
}

// Verification successful, create new user
$userModel = new User();
$user_id = $userModel->create($phone_number, $name);

if (!$user_id) {
    echo json_encode(['error' => 'Không thể tạo người dùng mới']);
    exit;
}

$user = [
    'id' => $user_id,
    'phone_number' => $phone_number,
    'name' => $name
];

// Set user session
$_SESSION['user_id'] = $user['id'];
$_SESSION['phone_number'] = $user['phone_number'];
$_SESSION['name'] = $user['name'];

// Clear verification data
unset($_SESSION['verification']);

echo json_encode($user);
