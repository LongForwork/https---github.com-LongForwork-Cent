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

// Check if user exists
$userModel = new User();
$user = $userModel->findByPhoneNumber($phone_number);

if ($user) {
    // User exists, no need for verification
    // Set user session
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['phone_number'] = $user['phone_number'];
    $_SESSION['name'] = $user['name'];
    
    echo json_encode([
        'exists' => true,
        'user' => $user
    ]);
    exit;
}

// Generate a random 6-digit code
$verification_code = sprintf("%06d", mt_rand(100000, 999999));

// Store the code in session
$_SESSION['verification'] = [
    'phone_number' => $phone_number,
    'code' => $verification_code,
    'expires' => time() + 300, // 5 minutes expiry
    'attempts' => 0
];

// In a real application, you would send an SMS here
// For this example, we'll just return the code in the response
// In production, you should use a service like Twilio, Vonage, etc.

// Simulate sending SMS
// sendSMS($phone_number, "Mã xác nhận của bạn là: $verification_code");

echo json_encode([
    'exists' => false,
    'message' => 'Mã xác nhận đã được gửi đến số điện thoại của bạn',
    'debug_code' => $verification_code // Remove this in production
]);

// Function to send SMS (placeholder)
function sendSMS($phone_number, $message) {
    // In a real application, you would integrate with an SMS API here
    // Example with Twilio:
    /*
    $account_sid = 'YOUR_TWILIO_SID';
    $auth_token = 'YOUR_TWILIO_TOKEN';
    $twilio_number = 'YOUR_TWILIO_NUMBER';
    
    $client = new Client($account_sid, $auth_token);
    $client->messages->create(
        $phone_number,
        [
            'from' => $twilio_number,
            'body' => $message
        ]
    );
    */
    
    // For now, we'll just log it
    error_log("SMS to $phone_number: $message");
    return true;
}
