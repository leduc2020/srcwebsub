<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../libs/database/users.php');
require_once(__DIR__."/../../libs/sendEmail.php");


$Mobile_Detect = new Mobile_Detect();

if ($CMSNT->site('status') != 1) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Hệ thống đang bảo trì!')
    ]);
    die($data);
}
if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}
if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
}


if($_POST['action'] == 'WithdrawCommission'){
    if ($CMSNT->site('status_demo') != 0) {
        die(json_encode(['status' => 'error', 'msg' => __('This function cannot be used because this is a demo site')]));
    }
    if($CMSNT->site('affiliate_status') != 1){
        die(json_encode(['status' => 'error', 'msg' => __('Chức năng này đang được bảo trì')]));
    }
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (empty($_POST['bank'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ngân hàng cần rút')]));
    }
    if (empty($_POST['stk'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập số tài khoản cần rút')]));
    }
    if (empty($_POST['name'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập tên chủ tài khoản')]));
    }
    if (empty($_POST['amount'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập số tiền cần rút')]));
    }
    if($_POST['amount'] < $CMSNT->site('affiliate_min')){
        die(json_encode(['status' => 'error', 'msg' => __('Số tiền rút tối thiểu phải là').' '.format_currency($CMSNT->site('affiliate_min'))]));
    }
    if($getUser['ref_price'] < $_POST['amount']){
        die(json_encode(['status' => 'error', 'msg' => __('Số dư hoa hồng khả dụng của bạn không đủ')]));
    }
    $amount = check_string($_POST['amount']);
    $trans_id = random('123456789QWERTYUIOPASDFGHJKLZXCVBNM', 6);

    $User = new users();

    $isTru = $User->RemoveCommission($getUser['id'], $amount, __('Rút số dư hoa hồng').' #'.$trans_id);
    if($isTru){
        if(getRowRealtime('users', $getUser['id'], 'ref_price') < -5){
            $User->Banned($getUser['id'], __('Gian lận khi rút số dư hoa hồng'));
            die(json_encode(['status' => 'error', 'msg' => __('Tài khoản của bạn đã bị khóa vì gian lận')]));
        }
        $isInsert = $CMSNT->insert('aff_withdraw', [
            'trans_id'  => $trans_id,
            'user_id'   => $getUser['id'],
            'bank'      => check_string($_POST['bank']),
            'stk'       => check_string($_POST['stk']),
            'name'      => check_string($_POST['name']),
            'amount'    => check_string($_POST['amount']),
            'status'    => 'pending',
            'create_gettime'    => gettime(),
            'update_gettime'    => gettime(),
            'reason'    => NULL
        ]);
        if($isInsert){
            /** NOTE ACTION */
            $my_text = $CMSNT->site('noti_affiliate_withdraw');
            $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
            $my_text = str_replace('{username}', $getUser['username'], $my_text);
            $my_text = str_replace('{bank}', check_string($_POST['bank']), $my_text);
            $my_text = str_replace('{account_number}', check_string($_POST['stk']), $my_text);
            $my_text = str_replace('{account_name}', check_string($_POST['name']), $my_text);
            $my_text = str_replace('{amount}', format_currency(check_string($_POST['amount'])), $my_text);
            $my_text = str_replace('{ip}', myip(), $my_text);    
            $my_text = str_replace('{time}', gettime(), $my_text);
            sendMessTelegram($my_text, '', $CMSNT->site('affiliate_chat_id_telegram'));
            
            die(json_encode(['status' => 'success', 'msg' => __('Yêu cầu rút tiền được tạo thành công, vui lòng đợi ADMIN xử lý')]));
        }
        die(json_encode(['status' => 'error', 'msg' => 'ERROR 1 - '.__('System error')]));
    }else{
        die(json_encode(['status' => 'error', 'msg' => 'ERROR 2 - '.__('System error')]));
    }

}


die(json_encode([
    'status'    => 'error',
    'msg'       => __('Request does not exist')
]));