<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/helper.php");
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

$data = [];
foreach($CMSNT->get_list("SELECT * FROM `orders` WHERE `status` = 'pending' OR `status` = 'In progress' OR `status` = 'Processing' AND `supplier_id` = 0 ") as $order){
    $data[] = [
        'id'            => $order['id'],
        'trans_id'      => $order['trans_id'],
        'service_id'    => $order['service_id'],
        'link'          => $order['link'],
        'comment'       => $order['comment'],
        'quantity'      => $order['quantity'],
        'start_count'   => $order['start_count'],
        'remains'       => $order['remains'],
        'status'        => $order['status'],
        'created_at'    => $order['created_at'],
        'updated_at'    => $order['updated_at'],
    ];
}

echo json_encode($data);