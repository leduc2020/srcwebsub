<?php

use GuzzleHttp\Promise\Is;

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../libs/database/users.php');

if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}


if($_POST['action'] == 'changeLanguage'){
    if (empty($_POST['id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Data does not exist')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ");
    if (!$row) {
        die(json_encode(['status' => 'error', 'msg' => __('Data does not exist')]));
    }
    $isUpdate = setLanguage($id);
    if ($isUpdate) {
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Change language successfully')
        ]);
        die($data);
    }
}

if($_POST['action'] == 'changeCurrency'){
    if (empty($_POST['id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Data does not exist')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$id' ");
    if (!$row) {
        die(json_encode(['status' => 'error', 'msg' => __('Data does not exist')]));
    }
    $isUpdate = setCurrency($id);
    if ($isUpdate) {
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Successful currency change')
        ]);
        die($data);
    }
}


if ($CMSNT->site('status_demo') != 0) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('This function cannot be used because this is a demo site')
    ]);
    die($data);
}
// CHỨC NĂNG KHÔNG DÙNG ĐƯỢC TẠI TRANG WEB DEMO

 

 


die(json_encode([
    'status'    => 'error',
    'msg'       => __('Invalid data')
]));