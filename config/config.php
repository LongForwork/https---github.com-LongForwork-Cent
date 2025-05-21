<?php
/**
 * Cấu hình hệ thống
 */

// Cấu hình cơ sở dữ liệu
define('DB_HOST', 'localhost');
define('DB_NAME', 'cent_beauty');
define('DB_USER', 'root');
define('DB_PASS', '');

// Cấu hình đường dẫn
define('BASE_URL', 'http://localhost/cent_beauty');

// Cấu hình email
define('EMAIL_FROM', 'no-reply@centbeauty.com');
define('EMAIL_NAME', 'Cent Beauty');

// Cấu hình OTP
define('OTP_EXPIRY_MINUTES', 5);
define('OTP_MAX_ATTEMPTS', 3);

// Cấu hình thời gian
define('TIMEZONE', 'Asia/Ho_Chi_Minh');
date_default_timezone_set(TIMEZONE);

// Cấu hình hiển thị lỗi (tắt trong môi trường production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
