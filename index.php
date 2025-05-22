<?php
session_start();
require_once 'config/config.php';
require_once 'models/Branch.php';
require_once 'utils/helpers.php';

// Lấy danh sách chi nhánh
$branchModel = new Branch();
$branches = $branchModel->getAllBranches();

// Kiểm tra thông báo lỗi
$error_message = '';
if (isset($_GET['error'])) {
    switch ($_GET['error']) {
        case 'not_logged_in':
            $error_message = 'Vui lòng đăng nhập để đặt lịch';
            break;
        case 'invalid_request':
            $error_message = 'Yêu cầu không hợp lệ';
            break;
        case 'missing_fields':
            $error_message = 'Vui lòng điền đầy đủ thông tin';
            break;
        case 'booking_failed':
            $error_message = 'Đặt lịch thất bại. Vui lòng thử lại';
            break;
        default:
            $error_message = 'Đã xảy ra lỗi. Vui lòng thử lại';
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cent Beauty - Đặt lịch</title>
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
                <div id="flag-logged-in">
                <?php if (isset($_SESSION['user_id'])): ?>
                <div class="voucher">
                    <img src="assets/images/ticket.png" alt="Voucher">
                    <div class="dropdown">
                        <span><?php echo $_SESSION['email']; ?></span>
                        <ul class="dropdown-content">
                            <li>Infor</li>
                            <li>Vouchers</li>
                            <li>
                                <a class="dropdown-item" href="process/logout.php">Logout</a>
                            </li>
                        </ul>
                    </div>
                </div>
                <?php endif; ?> 
                </div>
            </div>

            <!-- Progress -->
            <div class="progress-bar">
                Đặt lịch 3 bước
            </div>

            <!-- Error message -->
            <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
            <?php endif; ?>

            <!-- Form -->
            <div class="form-content">
                <form id="booking-form" action="process/process_booking.php" method="POST">
                    <!-- Step 1 -->
                    <div class="step" id="step-1">
                        <div class="step-header">
                            <div class="step-number">1</div>
                            <h3 class="step-title">Chọn chi nhánh</h3>
                        </div>

                        <div class="branch-list">
                            <?php foreach ($branches as $branch): ?>
                            <div class="branch-item">
                                <input type="radio" name="branch_id" id="branch-<?php echo $branch['id']; ?>" value="<?php echo $branch['id']; ?>" class="branch-radio">
                                <label for="branch-<?php echo $branch['id']; ?>" class="branch-label">
                                    <?php echo $branch['address']; ?>
                                </label>
                            </div>
                            <?php endforeach; ?>
                        </div>
                        
                        <!-- Continue button for step 1 -->
                        <button type="button" id="continue-step-1" class="continue-button" disabled>Tiếp tục</button>
                    </div>

                    <!-- Step 2 (initially hidden) -->
                    <div class="step hidden" id="step-2">
                        <div class="step-header">
                            <div class="step-number">2</div>
                            <h3 class="step-title">Chọn dịch vụ</h3>
                        </div>
                        <div id="services-container" class="service-list">
                            <!-- Services will be loaded here via AJAX -->
                        </div>
                        
                        <!-- Continue button for step 2 -->
                        <button type="button" id="continue-step-2" class="continue-button" disabled>Tiếp tục</button>
                    </div>

                    <!-- Step 3 (initially hidden) -->
                    <div class="step hidden" id="step-3">
                        <div class="step-header">
                            <div class="step-number">3</div>
                            <h3 class="step-title">Chọn thời gian</h3>
                        </div>
                        
                        <div class="date-time-container">
                            <div class="date-selection">
                                <label class="date-label">
                                    <img src="assets/images/calendar.png" alt="Calendar">
                                    <span>Chọn ngày</span>
                                </label>
                                <div class="date-list" id="date-list">
                                    <!-- Dates will be generated via JavaScript -->
                                </div>
                            </div>

                            <div class="time-selection hidden" id="time-selection">
                                <label class="time-label">
                                    <img src="assets/images/clock.png" alt="Clock">
                                    <span>Chọn giờ</span>
                                </label>
                                <div class="time-list" id="time-list">
                                    <!-- Times will be generated via JavaScript -->
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Notes -->
                    <div class="notes-section">
                        <label for="notes" class="notes-label">Ghi chú</label>
                        <textarea id="notes" name="notes" placeholder="Nhập ghi chú của bạn tại đây" class="notes-textarea"></textarea>
                    </div>

                    <!-- Book button -->
                    <button type="submit" id="book-button" class="book-button" disabled>Đặt lịch</button>
                    
                    <!-- Hidden fields for form submission -->
                    <input type="hidden" name="user_id" id="user_id" value="<?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : ''; ?>">
                    <input type="hidden" name="service_id" id="service_id" value="">
                    <input type="hidden" name="booking_date" id="booking_date" value="">
                    <input type="hidden" name="booking_time" id="booking_time" value="">
                    <input type="hidden" name="csrf_token" value="<?php echo generateCsrfToken(); ?>">
                </form>
            </div>
        </div>
    </div>

    <!-- Login Dialog -->
    <div id="login-dialog" class="dialog hidden">
        <div class="dialog-content">
            <div class="dialog-header">
                <h2 class="dialog-title">Đăng nhập</h2>
                <button type="button" class="close-button" id="close-dialog">&times;</button>
            </div>
            <div class="dialog-body">
                <div id="email-step">
                    <p class="dialog-text">Email của bạn là gì?</p>
                    <input type="email" id="user-email" placeholder="Nhập email của bạn" class="dialog-input">
                    <input type="text" id="user-name" placeholder="Tên của bạn (không bắt buộc)" class="dialog-input">
                    <button type="button" id="login-button" class="dialog-button">Đăng nhập</button>
                </div>
                <div id="verification-step" class="hidden">
                    <p class="dialog-text">Nhập mã xác nhận 6 số đã được gửi đến email của bạn</p>
                    <div class="verification-code-container">
                        <input type="text" id="verification-code" placeholder="Nhập mã xác nhận" class="dialog-input" maxlength="6">
                    </div>
                    <button type="button" id="verify-button" class="dialog-button">Xác nhận</button>
                    <p class="resend-text">
                        Không nhận được mã? <a href="#" id="resend-code">Gửi lại</a>
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div id="loading-overlay" class="loading-overlay hidden">
        <div class="loading-spinner"></div>
    </div>

    <script src="assets/js/script.js"></script>
</body>

<!-- node flag logged in -->
<div data-name="flag-logged-in" class="hidden">
    <div class="voucher">
        <img src="assets/images/ticket.png" alt="Voucher">
        <div class="dropdown">
            <span data-name="email"></span>
            <ul class="dropdown-content">
                <li>Infor</li>
                <li>Vouchers</li>
                <li>
                    <a class="dropdown-item" href="process/logout.php">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</div>
</html>
