<?php
/**
 * Tập hợp các hàm tiện ích
 */

/**
 * Tạo mã OTP ngẫu nhiên
 * 
 * @param int $length Độ dài mã OTP
 * @return string Mã OTP
 */
function generateOTP($length = 6) {
    return sprintf("%0{$length}d", mt_rand(0, pow(10, $length) - 1));
}

/**
 * Kiểm tra email hợp lệ
 * 
 * @param string $email Email cần kiểm tra
 * @return bool Kết quả kiểm tra
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Định dạng giá tiền
 * 
 * @param float $price Giá tiền
 * @param string $currency Đơn vị tiền tệ
 * @return string Giá tiền đã định dạng
 */
function formatPrice($price, $currency = 'đ') {
    return number_format($price, 0, ',', '.') . $currency;
}

/**
 * Định dạng thời gian
 * 
 * @param string $datetime Thời gian (format: Y-m-d H:i:s)
 * @param string $format Định dạng đầu ra
 * @return string Thời gian đã định dạng
 */
function formatDateTime($datetime, $format = 'H:i - d/m/Y') {
    return date($format, strtotime($datetime));
}

/**
 * Tạo thông báo lỗi
 * 
 * @param string $message Nội dung thông báo
 * @return array Thông báo lỗi
 */
function createError($message) {
    return ['error' => $message];
}

/**
 * Tạo thông báo thành công
 * 
 * @param string $message Nội dung thông báo
 * @param array $data Dữ liệu kèm theo
 * @return array Thông báo thành công
 */
function createSuccess($message, $data = []) {
    return array_merge(['success' => true, 'message' => $message], $data);
}

/**
 * Chuyển hướng đến URL
 * 
 * @param string $url URL đích
 * @return void
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Lấy URL hiện tại
 * 
 * @return string URL hiện tại
 */
function getCurrentUrl() {
    return (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
}

/**
 * Tạo CSRF token
 * 
 * @return string CSRF token
 */
function generateCsrfToken() {
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Kiểm tra CSRF token
 * 
 * @param string $token Token cần kiểm tra
 * @return bool Kết quả kiểm tra
 */
function validateCsrfToken($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}
