<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../libs/database/users.php");
require_once(__DIR__."/../config.php");
$CMSNT = new DB();

$Mobile_Detect = new Mobile_Detect();

if ($CMSNT->site('status') != 1) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Hệ thống đang bảo trì!')
    ]);
    die($data);
}
if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('This function cannot be used because this is a demo site')]));
}

if (empty($_REQUEST['key'])) {
    die(json_encode(['status' => 'error', 'msg' => __('API key không chính xác')]));
}
if (empty($_REQUEST['path_admin'])) {
    die(json_encode(['status' => 'error', 'msg' => __('Path admin không chính xác')]));
}
if (empty($_REQUEST['order_id'])) {
    die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập ID đơn hàng')]));
}
if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 AND `admin` > 0 ")) {
    checkBlockIP('API', 5);
    die(json_encode(['status' => 'error', 'msg' => __('API key không chính xác')]));
}
if($CMSNT->site('path_admin') != check_string($_REQUEST['path_admin'])){
    die(json_encode(['status' => 'error', 'msg' => __('Path admin không chính xác')]));
}
// Kiểm tra quyền sử dụng tính năng
if (checkPermission($getUser['admin'], 'request_api') != true) {
    die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
}
if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '".check_string($_REQUEST['order_id'])."' OR `trans_id` = '".check_string($_REQUEST['order_id'])."' ")){
    die(json_encode(['status' => 'error', 'msg' => __('Đơn hàng không tồn tại')]));
}
if($order['status'] == 'Completed' || $order['status'] == 'In progress' || $order['status'] == 'Processing'){
    die(json_encode(['status' => 'error', 'msg' => __('Đơn hàng đã hoàn thành, không thể cập nhật')]));
}
if($order['status'] == 'Cancelled'){
    die(json_encode(['status' => 'error', 'msg' => __('Đơn hàng đã hoàn tiền, không thể cập nhật')]));
}

// Nếu API không đưa remains thì sử dụng remains cũ
$remains = !empty($_REQUEST['remains']) ? intval(check_string($_REQUEST['remains'])) : $order['remains'];


$isUpdate = $CMSNT->update("orders", [
    'status'        => !empty($_REQUEST['status']) ? check_string($_REQUEST['status']) : $order['status'],
    'reason'        => !empty($_REQUEST['reason']) ? check_string($_REQUEST['reason']) : $order['reason'],
    'remains'       => $remains,
    'start_count'   => !empty($_REQUEST['start_count']) ? check_string($_REQUEST['start_count']) : $order['start_count']
], "`id` = '".check_string($_REQUEST['order_id'])."'");

if($isUpdate){  

    if(isset($_REQUEST['status'])){
        if($_REQUEST['status'] == 'Cancelled'){
            // Hoàn tiền lại User nếu đơn bị hủy
            $User = new users();
            $isRefund = $User->RefundCredits($order['user_id'], $order['pay'], 
                '[Admin] '.sprintf(__("Hoàn tiền đơn hàng #%s"), $order['trans_id']), 
                'Canceled_'.$order['trans_id']
            );
            if($isRefund){
                die(json_encode(['status' => 'success', 'msg' => __('Hoàn tiền đơn hàng cho user thành công!')]));
            }else{
                die(json_encode(['status' => 'error', 'msg' => __('Hoàn tiền đơn hàng cho user thất bại!')]));
            }
        }
        else if($_REQUEST['status'] == 'Partial'){
            // Hoàn tiền một phần
            $User = new users();

            $pricePerUnit = $order['pay'] / $order['quantity']; // Giá trên mỗi đơn vị
            $refundAmount = $pricePerUnit * $remains;
            $isRefund = $User->RefundCredits($order['user_id'], 
                $refundAmount, 
                '[Admin] '.sprintf(__("Hoàn tiền một phần cho đơn hàng #%s (Số lượng còn lại: %d)"), $order['trans_id'], $remains), 
                'Partial_'.$order['trans_id']
            );
            if($isRefund){
                die(json_encode(['status' => 'success', 'msg' => __('Hoàn tiền đơn hàng cho user thành công!')]));
            }else{
                die(json_encode(['status' => 'error', 'msg' => __('Hoàn tiền đơn hàng cho user thất bại!')]));
            }
        }
    }
    die(json_encode(['status' => 'success', 'msg' => __('Cập nhật đơn hàng thành công')]));
}else{
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật đơn hàng thất bại')]));
}