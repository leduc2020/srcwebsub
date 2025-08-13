<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../config.php");
require_once(__DIR__."/../libs/database/users.php");
$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();

if(isset($_POST['order_id']) && isset($_POST['billcode'])){

    $order_id = check_string($_POST['order_id']);
    $billcode = check_string($_POST['billcode']);
    $status = check_string($_POST['status']);
 

    if($row = $CMSNT->get_row(" SELECT * FROM `payment_toyyibpay` WHERE `trans_id` = '$order_id' AND `status` = 0 AND `BillCode` = '$billcode' ")){

        if($status == 1){
            $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$row['user_id']."' ");
            $isUpdate = $CMSNT->update('payment_toyyibpay', [
                'status'    => 1,
                'update_gettime'   => gettime()
            ], " `id` = '".$row['id']."' ");
            if($isUpdate){
                $amount = $amount / 100;
                $received = $row['amount'] * $CMSNT->site('toyyibpay_rate');
                $User->AddCredits($row['user_id'], $received, 'Automatic top-up via Malaysian bank #'.$billcode);
                 
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

                // TẠO LOG GIAO DỊCH GẦN ĐÂY
                $CMSNT->insert('deposit_log',[
                    'user_id'       => $row['user_id'],
                    'method'        => 'Toyyibpay',
                    'amount'        => $received,
                    'received'      => $received,
                    'create_time'   => time(),
                    'is_virtual'    => 0
                ]);
                /** SEND NOTI CHO ADMIN */
                $my_text = $CMSNT->site('noti_recharge');
                $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
                $my_text = str_replace('{username}', getRowRealtime('users', $row['user_id'], 'username'), $my_text);
                $my_text = str_replace('{method}', 'Toyyibpay', $my_text);
                $my_text = str_replace('{amount}', $received, $my_text);
                $my_text = str_replace('{price}', $received, $my_text);
                $my_text = str_replace('{time}', gettime(), $my_text);
                sendMessAdmin($my_text);
            }
        }else if($status == 3){
            $CMSNT->update('payment_toyyibpay', [
                'status'    => 2,
                'update_gettime'   => gettime()
            ], " `id` = '".$row['id']."' ");
        }

    }   


 }

