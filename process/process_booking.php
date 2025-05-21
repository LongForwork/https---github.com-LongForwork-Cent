<?php
session_start();
require_once '../models/Booking.php';
require_once '../utils/helpers.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    redirect('../index.php?error=not_logged_in');
}

// Kiểm tra phương thức request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('../index.php?error=invalid_request');
}

// Kiểm tra dữ liệu
if (
    !isset($_POST['branch_id']) || empty($_POST['branch_id']) ||
    !isset($_POST['service_id']) || empty($_POST['service_id']) ||
    !isset($_POST['booking_date']) || empty($_POST['booking_date']) ||
    !isset($_POST['booking_time']) || empty($_POST['booking_time'])
) {
    redirect('../index.php?error=missing_fields');
}

// Lấy dữ liệu từ form
$user_id = $_SESSION['user_id'];
$branch_id = $_POST['branch_id'];
$service_id = $_POST['service_id'];
$booking_date = $_POST['booking_date'];
$booking_time = $_POST['booking_time'];
$notes = isset($_POST['notes']) ? $_POST['notes'] : null;

// Kết hợp ngày và giờ
$date_time = $booking_date . ' ' . $booking_time . ':00';

// Tạo đặt lịch
$bookingModel = new Booking();
$booking_id = $bookingModel->create($user_id, $branch_id, $service_id, $date_time, $notes);

if ($booking_id) {
    // Đặt lịch thành công
    redirect('../booking_success.php?id=' . $booking_id);
} else {
    // Đặt lịch thất bại
    redirect('../index.php?error=booking_failed');
}
