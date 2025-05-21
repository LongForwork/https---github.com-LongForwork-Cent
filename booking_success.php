<?php
session_start();
require_once 'config/config.php';
require_once 'models/Booking.php';
require_once 'models/Service.php';
require_once 'models/Branch.php';
require_once 'utils/helpers.php';

// Kiểm tra đăng nhập
if (!isset($_SESSION['user_id'])) {
    redirect('index.php?error=not_logged_in');
}

// Kiểm tra ID đặt lịch
if (!isset($_GET['id']) || empty($_GET['id'])) {
    redirect('index.php?error=no_booking');
}

$booking_id = $_GET['id'];

// Lấy thông tin đặt lịch
$bookingModel = new Booking();
$booking = $bookingModel->getBookingById($booking_id);

// Kiểm tra đặt lịch tồn tại và thuộc về người dùng hiện tại
if (!$booking || $booking['user_id'] != $_SESSION['user_id']) {
    redirect('index.php?error=invalid_booking');
}

// Lấy thông tin chi nhánh và dịch vụ
$branchModel = new Branch();
$serviceModel = new Service();

$branch = $branchModel->getBranchById($booking['branch_id']);
$service = $serviceModel->getServiceById($booking['service_id']);
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch thành công - Cent Beauty</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="container">
        <div class="booking-form">
            <!-- Header -->
            <div class="header">
                <div class="brand">
                    <div class="brand-sub">beauty</div>
                    <div class="brand-main">cent'</div>
                </div>
                <div class="voucher">
                    <img src="assets/images/ticket.png" alt="Voucher">
                    <span>Ví voucher</span>
                </div>
            </div>

            <!-- Success message -->
            <div class="success-message">
                <div class="success-icon">✓</div>
                <h2>Đặt lịch thành công!</h2>
                <p>Cảm ơn bạn đã đặt lịch tại Cent Beauty. Chúng tôi sẽ liên hệ với bạn để xác nhận lịch hẹn.</p>
                
                <div class="booking-details">
                    <h3>Thông tin đặt lịch</h3>
                    <p>Mã đặt lịch: <strong>#<?php echo $booking_id; ?></strong></p>
                    <p>Chi nhánh: <strong><?php echo $branch['address']; ?></strong></p>
                    <p>Dịch vụ: <strong><?php echo $service['name']; ?></strong></p>
                    <p>Thời gian: <strong><?php echo formatDateTime($booking['date_time']); ?></strong></p>
                    <?php if (!empty($booking['notes'])): ?>
                    <p>Ghi chú: <strong><?php echo $booking['notes']; ?></strong></p>
                    <?php endif; ?>
                </div>
                
                <a href="index.php" class="back-button">Quay lại trang chủ</a>
            </div>
        </div>
    </div>
</body>
</html>
