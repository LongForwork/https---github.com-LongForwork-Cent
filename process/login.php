<?php
session_start();
include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = trim($_POST['phone']);
    
    // Validate phone number (simple validation)
    if (empty($phone) || !preg_match('/^[0-9]{10}$/', $phone)) {
        $_SESSION['error'] = 'Số điện thoại không hợp lệ. Vui lòng nhập 10 chữ số.';
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
    
    // Check if user exists
    $user = getUserByPhone($phone);
    
    if (!$user) {
        // Create new user
        $userId = createUser($phone);
        $user = [
            'id' => $userId,
            'phone' => $phone,
            'name' => null
        ];
    }
    
    // Set user session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_phone'] = $user['phone'];
    $_SESSION['user_name'] = $user['name'];
    
    // Redirect back to previous page
    header('Location: ' . $_SERVER['HTTP_REFERER']);
    exit;
}
