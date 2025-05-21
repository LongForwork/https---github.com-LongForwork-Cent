<?php
/**
 * Gửi SMS đến số điện thoại
 * 
 * @param string $phone_number Số điện thoại nhận SMS
 * @param string $message Nội dung tin nhắn
 * @return bool Trạng thái gửi tin nhắn
 */
function sendSMS($phone_number, $message) {
    // Trong môi trường thực tế, bạn sẽ tích hợp với dịch vụ SMS như Twilio, Vonage, SpeedSMS, v.v.
    // Dưới đây là ví dụ với Twilio:
    
    /*
    // Cài đặt thư viện Twilio qua Composer
    // composer require twilio/sdk
    
    require_once __DIR__ . '/../vendor/autoload.php';
    
    $account_sid = 'YOUR_TWILIO_SID';
    $auth_token = 'YOUR_TWILIO_TOKEN';
    $twilio_number = 'YOUR_TWILIO_NUMBER';
    
    $client = new Twilio\Rest\Client($account_sid, $auth_token);
    
    try {
        $message = $client->messages->create(
            $phone_number,
            [
                'from' => $twilio_number,
                'body' => $message
            ]
        );
        
        return true;
    } catch (Exception $e) {
        error_log("SMS Error: " . $e->getMessage());
        return false;
    }
    */
    
    // Giả lập gửi SMS thành công trong môi trường phát triển
    error_log("SMS to $phone_number: $message");
    return true;
}
