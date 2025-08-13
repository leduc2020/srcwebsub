<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}



/**
 * Lấy device_token từ cookie nếu có, hoặc tạo mới và lưu vào cookie.
 *
 * @param int $byteLength Độ dài byte ngẫu nhiên (ví dụ: 32).
 * @param int $cookieExpirySeconds Thời gian sống của cookie tính bằng giây (ví dụ: 1 năm).
 * @return string Device token (dạng hex).
 * @throws RuntimeException Nếu không thể tạo token an toàn.
 */
function getOrCreateDeviceToken(int $byteLength = 32, int $cookieExpirySeconds = 31536000 /* 1 năm */): string {
    $cookieName = 'device_token';
    $expectedHexLength = $byteLength * 2;

    // 1. Kiểm tra xem cookie device_token hợp lệ đã tồn tại chưa
    if (isset($_COOKIE[$cookieName])) {
        $token = $_COOKIE[$cookieName];
        // Kiểm tra cơ bản: đúng độ dài và là chuỗi hex (an toàn hơn)
        if (strlen($token) === $expectedHexLength && ctype_xdigit($token)) {
             // Token có vẻ hợp lệ, sử dụng token hiện có
             return $token;
        }
        // Nếu không hợp lệ, sẽ đi tiếp để tạo token mới
    }

    // 2. Nếu không có hoặc không hợp lệ, tạo token mới
    try {
        $randomBytes = random_bytes($byteLength);
        $deviceToken = bin2hex($randomBytes); // Chuyển sang dạng hex

        // 3. Đặt cookie mới với thời hạn dài và các cờ bảo mật
        $cookieOptions = [
            'expires' => time() + $cookieExpirySeconds,
            'path' => '/',
            'domain' => '', // Để trống cho domain hiện tại
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off', // True nếu đang dùng HTTPS
            'httponly' => true, // Quan trọng: Ngăn JS truy cập
            'samesite' => 'Lax' // Quan trọng: Chống CSRF
        ];

        // Đặt cookie
        setcookie($cookieName, $deviceToken, $cookieOptions);

        // Cập nhật biến $_COOKIE cho lần sử dụng ngay trong script hiện tại
        // vì setcookie chỉ gửi header cho trình duyệt, không cập nhật $_COOKIE ngay lập tức
        $_COOKIE[$cookieName] = $deviceToken;

        return $deviceToken;

    } catch (Exception $e) {
        // Xử lý lỗi nếu random_bytes() thất bại (rất hiếm)
        error_log("FATAL: Could not generate secure random bytes for device token: " . $e->getMessage());
        // Bạn cần quyết định cách xử lý ở đây:
        // - Hiển thị lỗi nghiêm trọng
        // - Ghi log và thử lại
        // - Throw exception để dừng thực thi
        throw new RuntimeException("Không thể tạo định danh thiết bị an toàn.");
    }
}


function createSession($user_id, $session_token){
    global $CMSNT;


    // Lấy device token
    $deviceToken = getOrCreateDeviceToken();

    // Cập nhật device token vào database
    $CMSNT->update("users", [
        'device_token' => $deviceToken
    ], " `id` = '$user_id' ");

    // Kiểm tra xem phiên đã tồn tại chưa
    $existingSession = $CMSNT->get_row("SELECT * FROM `active_sessions` 
        WHERE `user_id` = '$user_id' 
        AND `device_token` = '$deviceToken'");

    if ($existingSession) {
        // Nếu phiên đã tồn tại thì chỉ cập nhật
        $CMSNT->update("active_sessions", [
            'session_token' => $session_token,
            'ip_address'    => myip(),
            'user_agent'    => getUserAgent(),
            'last_activity' => gettime()
        ], " `id` = '".$existingSession['id']."' ");
    } else {
        // Nếu chưa tồn tại thì tạo mới
        $CMSNT->insert("active_sessions", [
            'user_id'       => $user_id,
            'session_token' => $session_token,
            'device_token'  => $deviceToken,
            'ip_address'    => myip(),
            'user_agent'    => getUserAgent(),
            'last_activity' => gettime(),
            'created_at'    => gettime()
        ]);
    }

    // Lưu Cookie
    setcookie('user_login', $session_token, time() + $CMSNT->site('session_login'), "/", "", false, true);
}
