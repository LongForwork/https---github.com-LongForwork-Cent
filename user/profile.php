<?php
session_start();
include '../config/database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    $_SESSION['error'] = 'Vui lòng đăng nhập để xem trang này.';
    header('Location: ../login.php');
    exit;
}

// Get user information
$userId = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

// Handle profile update
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $phone = trim($_POST['phone']);
    
    // Validate phone number (simple validation)
    if (!empty($phone) && !preg_match('/^[0-9]{10}$/', $phone)) {
        $error = 'Số điện thoại không hợp lệ. Vui lòng nhập 10 chữ số.';
    } else {
        // Update user information
        $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, updated_at = NOW() WHERE id = ?");
        if ($stmt->execute([$name, $phone, $userId])) {
            // Update session
            $_SESSION['user_name'] = $name;
            $success = 'Thông tin tài khoản đã được cập nhật thành công.';
            
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$userId]);
            $user = $stmt->fetch();
        } else {
            $error = 'Có lỗi xảy ra khi cập nhật thông tin. Vui lòng thử lại.';
        }
    }
}

// Get user's appointments
$stmt = $pdo->prepare("
    SELECT a.*, b.name as branch_name, s.name as service_name 
    FROM appointments a
    JOIN branches b ON a.branch_id = b.id
    JOIN services s ON a.service_id = s.id
    WHERE a.user_id = ?
    ORDER BY a.date DESC, a.time_slot DESC
    LIMIT 5
");
$stmt->execute([$userId]);
$appointments = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tài khoản của tôi - Beauty Cent'</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="booking-container">
        <header class="booking-header">
            <div class="logo">
                <span class="logo-prefix">beauty</span>
                <span class="logo-main">cent'</span>
            </div>
            <div class="header-actions">
                <div class="user-dropdown">
                    <button class="dropdown-toggle">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                        <span><?php echo $user['name'] ? $user['name'] : 'Tài khoản'; ?></span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="dropdown-menu">
                        <a href="profile.php" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                                <circle cx="12" cy="7" r="4"></circle>
                            </svg>
                            Tài khoản
                        </a>
                        <a href="vouchers.php" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <polyline points="20 12 20 22 4 22 4 12"></polyline>
                                <rect x="2" y="7" width="20" height="5"></rect>
                                <line x1="12" y1="22" x2="12" y2="7"></line>
                                <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"></path>
                                <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"></path>
                            </svg>
                            Ví voucher
                        </a>
                        <a href="../process/logout.php" class="dropdown-item">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Đăng xuất
                        </a>
                    </div>
                </div>
            </div>
        </header>

        <div class="profile-container">
            <div class="profile-header">
                <h1>Tài khoản của tôi</h1>
                <a href="../index.php" class="back-to-home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Trang chủ
                </a>
            </div>

            <?php if (!empty($success)): ?>
                <div class="success-message"><?php echo $success; ?></div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="error-message"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="profile-content">
                <div class="profile-section">
                    <h2>Thông tin cá nhân</h2>
                    <form class="profile-form" method="POST" action="">
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" disabled>
                            <p class="field-note">Email không thể thay đổi</p>
                        </div>
                        
                        <div class="form-group">
                            <label for="name">Họ và tên</label>
                            <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name'] ?? ''); ?>" placeholder="Nhập họ và tên của bạn">
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Số điện thoại</label>
                            <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Nhập số điện thoại của bạn">
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">Cập nhật thông tin</button>
                        </div>
                    </form>
                </div>
                
                <div class="profile-section">
                    <h2>Lịch hẹn gần đây</h2>
                    <?php if (count($appointments) > 0): ?>
                        <div class="appointments-list">
                            <?php foreach ($appointments as $appointment): ?>
                                <div class="appointment-item">
                                    <div class="appointment-date">
                                        <div class="date-day"><?php echo date('d', strtotime($appointment['date'])); ?></div>
                                        <div class="date-month"><?php echo date('m/Y', strtotime($appointment['date'])); ?></div>
                                    </div>
                                    <div class="appointment-details">
                                        <div class="appointment-service"><?php echo htmlspecialchars($appointment['service_name']); ?></div>
                                        <div class="appointment-branch"><?php echo htmlspecialchars($appointment['branch_name']); ?></div>
                                        <div class="appointment-time">
                                            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <circle cx="12" cy="12" r="10"></circle>
                                                <polyline points="12 6 12 12 16 14"></polyline>
                                            </svg>
                                            <?php echo $appointment['time_slot']; ?>
                                        </div>
                                    </div>
                                    <div class="appointment-status status-<?php echo $appointment['status']; ?>">
                                        <?php 
                                        $statusLabels = [
                                            'pending' => 'Chờ xác nhận',
                                            'confirmed' => 'Đã xác nhận',
                                            'completed' => 'Hoàn thành',
                                            'cancelled' => 'Đã hủy'
                                        ];
                                        echo $statusLabels[$appointment['status']];
                                        ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="view-all-appointments">
                            <a href="appointments.php" class="btn-secondary">Xem tất cả lịch hẹn</a>
                        </div>
                    <?php else: ?>
                        <div class="no-appointments">
                            <p>Bạn chưa có lịch hẹn nào.</p>
                            <a href="../index.php?step=1" class="btn-primary">Đặt lịch ngay</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
</body>
</html>
