<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../config.php");
require_once(__DIR__."/../libs/database/users.php");

$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
 

if(empty($_GET['request_id'])){
    die('request_id empty');
}
if(empty($_GET['token'])){
    die('token empty');
}
if(empty($_GET['status'])){
    die('status empty');
}

// DỮ LIỆU CALLBACK VỀ
$request_id  = isset($_GET['request_id']) ? check_string($_GET['request_id']) : NULL; // REQUEST ID XÁC MINH GIAO DỊCH
$token = isset($_GET['token']) ? check_string($_GET['token']) : NULL; // TOKEN XÁC MINH ĐỊA CHỈ CÓ PHẢI CỦA BẠN HAY KHÔNG
$received = isset($_GET['received']) ? check_string($_GET['received']) : NULL; // SỐ TIỀN NHẬN ĐƯỢC
$status = isset($_GET['status']) ? check_string($_GET['status']) : NULL; // TRẠNG THÁI HOÁ ĐƠN
$from_address = isset($_GET['from_address']) ? check_string($_GET['from_address']) : NULL; // ĐỊA CHỈ NGƯỜI GỬI
$transaction_id = isset($_GET['transaction_id']) ? check_string($_GET['transaction_id']) : NULL; // MÃ GIAO DỊCH TRÊN BLOCKTRAIN
     
if($token != $CMSNT->site('crypto_token')){
    die('Token xác minh không chính xác');
}

if(!$row = $CMSNT->get_row(" SELECT * FROM `payment_crypto` WHERE `request_id` = '$request_id' ")){
    die('Hoá đơn không tồn tại');
}
$amount = $row['received'];
// xử lý khuyến mãi
$received = checkPromotion($amount);
$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$row['user_id']."' ");

// HOÁ ĐƠN ĐÃ CỘNG TIỀN SẼ KHÔNG THAY ĐỔI TRẠNG THÁI
if($row['status'] == 'completed'){
    die('Hoá đơn này đã được xử lý rồi');
}

// XỬ LÝ HOÁ ĐƠN HẾT HẠN
if($status == 'expired'){
    $CMSNT->update('payment_crypto', [
        'status'            => 'expired',
        'update_gettime'    => gettime()
    ], " `id` = '" . $row['id'] . "' ");
    die('cập nhật trạng thái expired');
}

// XỬ LÝ HOÁ ĐƠN HOÀN TẤT
if($status == 'completed'){
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
            
            /** SEND NOTI CHO ADMIN */
            $my_text = $CMSNT->site('noti_recharge');
            $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
            $my_text = str_replace('{username}', $getUser['username'], $my_text);
            $my_text = str_replace('{method}', 'Crypto', $my_text);
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
            die('Cập nhật trạng thái completed thành công!');
        }else{
            die('Hóa đơn này đã được cộng tiền rồi');
        }
    }
}