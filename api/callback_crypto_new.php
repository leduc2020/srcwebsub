<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../config.php");
require_once(__DIR__."/../libs/database/users.php");
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

// Nhận request từ callback
$request_data = $_GET;


// Lọc chuỗi để tránh SQL Injection và XSS
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Kiểm tra các tham số callback nhận được
if (!isset($request_data['request_id'], $request_data['merchant_id'], $request_data['api_key'], $request_data['received'], $request_data['status'])) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => 'Thiếu tham số callback.'
    ]);
    exit;
}

$request_id = sanitize_input($request_data['request_id']);      // Mã giao dịch bí mật của hệ thống bạn gửi lên API dùng để phân biệt giao dịch (Không được để lộ ra ngoài)
$merchant_id = sanitize_input($request_data['merchant_id']);    // ID cửa hàng tạo hóa đơn (Không được để lộ ra ngoài)
$api_key = sanitize_input($request_data['api_key']);            // API KEY của cửa hàng tạo hóa đơn (Không được để lộ ra ngoài)
$received = sanitize_input($request_data['received']);          // Số lượng USDT thực nhận được
$status = sanitize_input($request_data['status']);              // Trạng thái giao dịch bao gồm: waiting, expired, completed
$from_address = isset($request_data['from_address']) ? sanitize_input($request_data['from_address']) : null;        // Địa chỉ USDT gửi tiền trong trường hợp hóa đơn completed
$transaction_id = isset($request_data['transaction_id']) ? sanitize_input($request_data['transaction_id']) : null;  // Mã giao dịch trong blockchain trong trường hợp hóa đơn completed

// Xác minh tính hợp lệ của callback tránh giả mạo callback
$expected_merchant_id = $CMSNT->site('crypto_merchant_id'); // Thay bằng Merchant ID của bạn 
$expected_api_key = $CMSNT->site('crypto_api_key'); // Thay bằng API Key của bạn
if ($merchant_id !== $expected_merchant_id || $api_key !== $expected_api_key) {
    http_response_code(403);
    echo json_encode([
        'status' => 'error',
        'message' => 'Merchant ID hoặc API Key không hợp lệ.'
    ]);
    exit;
}
if(!$row = $CMSNT->get_row(" SELECT * FROM `payment_crypto` WHERE `request_id` = '$request_id' ")){
    echo json_encode([
        'status' => 'error',
        'message' => 'Hóa đơn không tồn tại'
    ]);
    exit;
}
$amount = $row['received'];
// xử lý khuyến mãi
$received = checkPromotion($amount);
$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$row['user_id']."' ");

// HOÁ ĐƠN ĐÃ CỘNG TIỀN SẼ KHÔNG THAY ĐỔI TRẠNG THÁI
if($row['status'] == 'completed'){
    echo json_encode([
        'status' => 'error',
        'message' => 'Hoá đơn này đã được xử lý rồi'
    ]);
    exit;
}

// Xử lý trạng thái giao dịch
switch ($status) {
    case 'waiting':
        // Giao dịch đang chờ thực hiện
        // Thêm code để xử lý trạng thái đang chờ
        break;
    case 'expired':
        // Giao dịch đã hết hạn
        $CMSNT->update('payment_crypto', [
            'status'            => 'expired',
            'update_gettime'    => gettime()
        ], " `id` = '" . $row['id'] . "' ");
        break;
    case 'completed':
        // Giao dịch đã hoàn tất
        $isUpdate = $CMSNT->update('payment_crypto', [
            'status'            => 'completed',
            'update_gettime'    => gettime()
        ], " `id` = '" . $row['id'] . "' ");
        if($isUpdate){
            $User = new users();
            $isCong = $User->AddCredits($row['user_id'], $received, "Crypto Recharge #".$row['trans_id'], 'TOPUP_CRYPTO_'.$row['trans_id']);
            if($isCong){
                // CỘNG HOA HỒNG
                if($CMSNT->site('affiliate_status') == 1 && $getUser['ref_id'] != 0){
                    $ck = $CMSNT->site('affiliate_ck');
                    if(getRowRealtime('users', $getUser['ref_id'], 'ref_ck') != 0){
                        $ck = getRowRealtime('users', $getUser['ref_id'], 'ref_ck');
                    }
                    $price_ref = $received * $ck / 100;
                    $user->AddCommission($getUser['ref_id'], $getUser['id'], $price_ref, __('Hoa hồng thành viên'.' '.$getUser['username']));
                }
                // XỬ LÝ TIỀN NỢ NẾU CÓ
                debit_processing($getUser['id']);

                // GỬI THÔNG BÁO CHO ADMIN
                $my_text = $CMSNT->site('noti_recharge');
                $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                $my_text = str_replace('{trans_id}', $row['trans_id'], $my_text);
                $my_text = str_replace('{username}', $getUser['username'], $my_text);
                $my_text = str_replace('{method}', 'USDT', $my_text);
                $my_text = str_replace('{amount}', format_currency($amount), $my_text);
                $my_text = str_replace('{price}', format_currency($received), $my_text);
                $my_text = str_replace('{time}', gettime(), $my_text);
                sendMessAdmin($my_text);
   
                // TẠO LOG GIAO DỊCH GẦN ĐÂY
                $CMSNT->insert('deposit_log',[
                    'user_id'       => $getUser['id'],
                    'method'        => 'USDT',
                    'amount'        => $amount,
                    'received'      => $received,
                    'create_time'   => time(),
                    'is_virtual'    => 0
                ]);
            }
        }
        break;
    default:
        http_response_code(400);
        echo json_encode([
            'status' => 'error',
            'message' => 'Trạng thái giao dịch không hợp lệ.'
        ]);
        exit;
}

// Phản hồi callback thành công
http_response_code(200);
echo json_encode([
    'status' => 'success',
    'message' => 'Callback đã được xử lý thành công.'
]);