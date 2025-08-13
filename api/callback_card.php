<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../config.php");
require_once(__DIR__."/../libs/database/users.php");
$User = new users();
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

if ($CMSNT->site('status') != 1 && isSecureCookie('admin_login') != true) {
    die('status_website_off');
}
if ($CMSNT->site('card_status') != 1) {
    die('status_card_off');
}
/** CALLBACK */
if(isset($_GET['request_id']) && isset($_GET['callback_sign'])){
    $status = check_string($_GET['status']);
    $message = check_string($_GET['message']);
    $request_id = check_string($_GET['request_id']); // request id
    $declared_value = check_string($_GET['declared_value']); //Giá trị khai báo
    $value = check_string($_GET['value']); //Giá trị thực của thẻ
    $amount = check_string($_GET['amount']); //Số tiền nhận được
    $code = check_string($_GET['code']);
    $serial = check_string($_GET['serial']);
    $telco = check_string($_GET['telco']);
    $trans_id = check_string($_GET['trans_id']); //Mã giao dịch bên chúng tôi
    $callback_sign = check_string($_GET['callback_sign']);

    if($callback_sign != md5($CMSNT->site('card_partner_key').$code.$serial)){
        die('callback_sign_error');
    }
    if (!$row = $CMSNT->get_row(" SELECT * FROM `cards` WHERE `trans_id` = '$request_id' AND `status` = 'pending' ")) {
        die('request_id_error');
    }
    if (!$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$row['user_id']."' AND `banned` = 0 ")) {
        die('user không hợp lệ');
    }
    if($status == 1){
        if($CMSNT->site('card_ck') == 0){
            $price = $amount;
        }else{
            $price = $value - $value * $CMSNT->site('card_ck') / 100;
        }
        $CMSNT->update("cards", array(
            'status'        => 'completed',
            'price'         => $price,
            'update_date'    => gettime()
        ), " `id` = '".$row['id']."' ");
        $isCong = $User->AddCredits($row['user_id'], $price, "Nạp thẻ cào Seri ".$row['serial']." - Pin ".$row['pin'], 'TOPUP_CARD_'.$row['pin']);
        if($isCong){

            // CỘNG HOA HỒNG
            if($CMSNT->site('affiliate_status') == 1 && $getUser['ref_id'] != 0){
                $ck = $CMSNT->site('affiliate_ck');
                if(getRowRealtime('users', $getUser['ref_id'], 'ref_ck') != 0){
                    $ck = getRowRealtime('users', $getUser['ref_id'], 'ref_ck');
                }
                $price_ref = $price * $ck / 100;
                $user->AddCommission($getUser['ref_id'], $getUser['id'], $price_ref, __('Hoa hồng thành viên'.' '.$getUser['username']));
            }
            // XỬ LÝ TIỀN NỢ NẾU CÓ
            debit_processing($getUser['id']);
          

            /** SEND NOTI CHO ADMIN */
            $my_text = $CMSNT->site('noti_recharge');
            $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
            $my_text = str_replace('{username}', $getUser['username'], $my_text);
            $my_text = str_replace('{method}', $telco, $my_text);
            $my_text = str_replace('{amount}', format_currency($amount), $my_text);
            $my_text = str_replace('{price}', format_currency($price), $my_text);
            $my_text = str_replace('{time}', gettime(), $my_text);
            sendMessAdmin($my_text);

            // TẠO LOG GIAO DỊCH GẦN ĐÂY
            $CMSNT->insert('deposit_log',[
                'user_id'       => $getUser['id'],
                'method'        => 'Thẻ cào',
                'amount'        => $value,
                'received'      => $price,
                'create_time'   => time(),
                'is_virtual'    => 0
            ]);
            die('payment.success');
        }else{
            die('thẻ này đã được cộng tiền rồi');
        }
    }
    else{
        $CMSNT->update("cards", array(
            'status'        => 'error',
            'price'         => 0,
            'update_date'   => gettime(),
            'reason'        => 'Thẻ cào không hợp lệ hoặc đã được sử dụng'
        ), " `id` = '".$row['id']."' ");
        exit('payment.error');
    }
}