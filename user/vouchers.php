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

// For demonstration, we'll create some sample vouchers
$vouchers = [
    [
        'id' => 1,
        'code' => 'WELCOME50',
        'discount' => '50.000đ',
        'min_order' => '200.000đ',
        'expires' => '31/12/2023',
        'status' => 'active'
    ],
    [
        'id' => 2,
        'code' => 'BEAUTY20',
        'discount' => '20%',
        'min_order' => '500.000đ',
        'expires' => '15/06/2023',
        'status' => 'expired'
    ],
    [
        'id' => 3,
        'code' => 'NEWYEAR100',
        'discount' => '100.000đ',
        'min_order' => '300.000đ',
        'expires' => '31/01/2024',
        'status' => 'active'
    ]
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ví voucher - Beauty Cent'</title>
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

        <div class="vouchers-container">
            <div class="vouchers-header">
                <h1>Ví voucher</h1>
                <a href="../index.php" class="back-to-home">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"></path>
                        <polyline points="9 22 9 12 15 12 15 22"></polyline>
                    </svg>
                    Trang chủ
                </a>
            </div>

            <div class="vouchers-content">
                <div class="voucher-tabs">
                    <button class="tab-button active" data-tab="active">Có thể sử dụng</button>
                    <button class="tab-button" data-tab="expired">Đã hết hạn</button>
                </div>

                <div class="voucher-list tab-content active" id="active-vouchers">
                    <?php 
                    $hasActiveVouchers = false;
                    foreach ($vouchers as $voucher): 
                        if ($voucher['status'] === 'active'):
                            $hasActiveVouchers = true;
                    ?>
                        <div class="voucher-item">
                            <div class="voucher-left">
                                <div class="voucher-logo">
                                    <span class="logo-prefix">beauty</span>
                                    <span class="logo-main">cent'</span>
                                </div>
                            </div>
                            <div class="voucher-middle">
                                <div class="voucher-discount"><?php echo $voucher['discount']; ?></div>
                                <div class="voucher-condition">Đơn tối thiểu <?php echo $voucher['min_order']; ?></div>
                                <div class="voucher-expiry">HSD: <?php echo $voucher['expires']; ?></div>
                            </div>
                            <div class="voucher-right">
                                <div class="voucher-code"><?php echo $voucher['code']; ?></div>
                                <button class="btn-use-voucher" data-code="<?php echo $voucher['code']; ?>">Sử dụng</button>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    
                    if (!$hasActiveVouchers):
                    ?>
                        <div class="no-vouchers">
                            <p>Bạn chưa có voucher nào có thể sử dụng.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="voucher-list tab-content" id="expired-vouchers">
                    <?php 
                    $hasExpiredVouchers = false;
                    foreach ($vouchers as $voucher): 
                        if ($voucher['status'] === 'expired'):
                            $hasExpiredVouchers = true;
                    ?>
                        <div class="voucher-item expired">
                            <div class="voucher-left">
                                <div class="voucher-logo">
                                    <span class="logo-prefix">beauty</span>
                                    <span class="logo-main">cent'</span>
                                </div>
                            </div>
                            <div class="voucher-middle">
                                <div class="voucher-discount"><?php echo $voucher['discount']; ?></div>
                                <div class="voucher-condition">Đơn tối thiểu <?php echo $voucher['min_order']; ?></div>
                                <div class="voucher-expiry">Đã hết hạn: <?php echo $voucher['expires']; ?></div>
                            </div>
                            <div class="voucher-right">
                                <div class="voucher-code"><?php echo $voucher['code']; ?></div>
                                <div class="voucher-expired-label">Đã hết hạn</div>
                            </div>
                        </div>
                    <?php 
                        endif;
                    endforeach; 
                    
                    if (!$hasExpiredVouchers):
                    ?>
                        <div class="no-vouchers">
                            <p>Bạn không có voucher nào đã hết hạn.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script src="../assets/js/script.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Tab switching
            const tabButtons = document.querySelectorAll('.tab-button');
            const tabContents = document.querySelectorAll('.tab-content');
            
            tabButtons.forEach(button => {
                button.addEventListener('click', () => {
                    // Remove active class from all buttons and contents
                    tabButtons.forEach(btn => btn.classList.remove('active'));
                    tabContents.forEach(content => content.classList.remove('active'));
                    
                    // Add active class to clicked button and corresponding content
                    button.classList.add('active');
                    const tabId = button.getAttribute('data-tab');
                    document.getElementById(`${tabId}-vouchers`).classList.add('active');
                });
            });
            
            // Use voucher buttons
            const useVoucherButtons = document.querySelectorAll('.btn-use-voucher');
            useVoucherButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const code = button.getAttribute('data-code');
                    alert(`Mã voucher ${code} đã được sao chép!`);
                    
                    // Copy to clipboard
                    navigator.clipboard.writeText(code).then(() => {
                        // Redirect to booking page
                        window.location.href = '../index.php?step=1';
                    }).catch(err => {
                        console.error('Không thể sao chép mã: ', err);
                    });
                });
            });
        });
    </script>
</body>
</html>
