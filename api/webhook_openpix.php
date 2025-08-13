<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../libs/database/users.php");
require_once(__DIR__."/../config.php");
$CMSNT = new DB();


// Kiểm tra xem cổng thanh toán OpenPix có được kích hoạt không
if($CMSNT->site('openpix_status') != 1){
    die('Cổng thanh toán này chưa được kích hoạt');
}

// Lấy headers và nội dung của request
$body = file_get_contents('php://input');
$signature = check_string($_SERVER['HTTP_X_OPENPIX_SIGNATURE'] ?? '');


// Giải mã dữ liệu JSON từ body trước để xác định loại sự kiện
$data = json_decode($body, true);

// Kiểm tra xem có phải JSON hợp lệ không
if (json_last_error() !== JSON_ERROR_NONE) {
    http_response_code(400);
    die('Invalid JSON data');
}

// Xác định khóa HMAC dựa trên loại sự kiện
$secretKeyOnOpenpixPlatform = $CMSNT->site('openpix_HMAC_key_completed');
if (isset($data['event'])) {
    if ($data['event'] == 'OPENPIX:CHARGE_COMPLETED') {
        // Sử dụng khóa cho giao dịch thành công
        $secretKeyOnOpenpixPlatform = $CMSNT->site('openpix_HMAC_key_completed');
    } else if ($data['event'] == 'OPENPIX:CHARGE_EXPIRED') {
        // Sử dụng khóa cho giao dịch hết hạn
        $secretKeyOnOpenpixPlatform = $CMSNT->site('openpix_HMAC_key');
    } else {
        // Sự kiện không được hỗ trợ
        http_response_code(400);
        die('Unsupported event type');
    }
} else {
    // Không tìm thấy trường event
    http_response_code(400);
    die('Event field not found');
}

// Thuật toán hash
$algorithm = 'sha1'; // algoritmo de hash

// Tạo chữ ký HMAC
$hmac = base64_encode(hash_hmac($algorithm, $body, $secretKeyOnOpenpixPlatform, true));


// Kiểm tra chữ ký HMAC
if($hmac === $signature) {
    // Xử lý sự kiện khi giao dịch hoàn tất
    if ($data['event'] == 'OPENPIX:CHARGE_COMPLETED') {
        $transactionID = check_string($data['charge']['transactionID']);
        $status = check_string($data['charge']['status']);
        $amount = check_string($data['charge']['value']);
        $userCorrelationID = check_string($data['charge']['correlationID']);

        // Kiểm tra trạng thái giao dịch
        if ($status == 'COMPLETED') {
            if ($row = $CMSNT->get_row("SELECT * FROM `payment_openpix` WHERE `trans_id` = '$userCorrelationID' AND `status` = 0")) {
                $user = new users;
                $isCong = $user->AddCredits($row['user_id'], $row['price'], __('Recharge OpenPix').' #'.$userCorrelationID, 'TOPUP_openpix_'.$userCorrelationID);
                if ($isCong) {
                    $CMSNT->update('payment_openpix', [
                        'status' => 1,
                        'updated_at' => gettime()
                    ], " `id` = '".$row['id']."' ");

                    // LẤY THÔNG TIN USER
                    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$row['user_id']."' ");
                    // CỘNG HOA HỒNG
                    if($CMSNT->site('affiliate_status') == 1 && $getUser['ref_id'] != 0){
                        $ck = $CMSNT->site('affiliate_ck');
                        if(getRowRealtime('users', $getUser['ref_id'], 'ref_ck') != 0){
                            $ck = getRowRealtime('users', $getUser['ref_id'], 'ref_ck');
                        }
                        $price_ref = $row['price'] * $ck / 100;
                        $user->AddCommission($getUser['ref_id'], $getUser['id'], $price_ref, __('Hoa hồng thành viên'.' '.$getUser['username']));
                    }
                    // XỬ LÝ TIỀN NỢ NẾU CÓ
                    debit_processing($getUser['id']);

                    // Tạo log giao dịch gần đây
                    $CMSNT->insert('deposit_log', [
                        'user_id' => $row['user_id'],
                        'method' => 'OpenPix',
                        'amount' => $amount,
                        'received' => $row['price'],
                        'create_time' => time(),
                        'is_virtual' => 0
                    ]);

                    // Gửi thông báo cho admin
                    $my_text = $CMSNT->site('noti_recharge');
                    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
                    $my_text = str_replace('{username}', getRowRealtime('users', $row['user_id'], 'username'), $my_text);
                    $my_text = str_replace('{method}', 'OpenPix', $my_text);
                    $my_text = str_replace('{amount}', $amount, $my_text);
                    $my_text = str_replace('{price}', format_currency($row['price']), $my_text);
                    $my_text = str_replace('{time}', gettime(), $my_text);
                    sendMessAdmin($my_text);
                }
            }
        }
    } else if ($data['event'] == 'OPENPIX:CHARGE_EXPIRED') {
        $transactionID = check_string($data['charge']['transactionID']);
        $status = check_string($data['charge']['status']);
        $amount = check_string($data['charge']['value']);
        $userCorrelationID = check_string($data['charge']['correlationID']);

        // Xử lý sự kiện khi giao dịch hết hạn
        if ($status == 'EXPIRED') {
            if ($row = $CMSNT->get_row("SELECT * FROM `payment_openpix` WHERE `trans_id` = '$userCorrelationID' AND `status` = 0")) {
                $CMSNT->update('payment_openpix', [
                    'status' => 2, // 2 = expired
                    'updated_at' => gettime()
                ], " `id` = '".$row['id']."' ");
            }
        }
    }
    // Trả về mã trạng thái 200
    http_response_code(200);
} else {
    // Thông báo chữ ký HMAC không hợp lệ
    echo 'Invalid HMAC';
    
    // Ghi log lỗi
    $CMSNT->insert('logs', [
        'user_id'       => 0,
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => 'HMAC Error: hmac = '.$hmac.' | x-openpix-signature = '.$signature.' | secretKeyOnOpenpixPlatform = '.$secretKeyOnOpenpixPlatform
    ]);
    
    // Trả về mã trạng thái 401
    http_response_code(401);
}

