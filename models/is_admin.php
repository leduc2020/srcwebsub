<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$CMSNT = new DB();
require_once __DIR__.'/../libs/session.php';
 

if (isSecureCookie('user_login') != true) {
    redirect(base_url('client/logout'));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_COOKIE['user_login'])."' AND `admin` > 0  ");
    // chuyển hướng đăng nhập khi thông tin login không tồn tại
    if (!$getUser) {
        // Rate limit
        checkBlockIP('ADMIN');
        redirect(base_url('client/logout'));
    }

    // Kiểm tra phiên đăng nhập trong active_sessions
    $deviceToken = getOrCreateDeviceToken();
    $activeSession = $CMSNT->get_row("SELECT * FROM `active_sessions` 
        WHERE `user_id` = '".$getUser['id']."' 
        AND `session_token` = '".check_string($_COOKIE['user_login'])."'
        AND `device_token` = '".$deviceToken."'");
    
    if (!$activeSession) {
        // Rate limit
        checkBlockIP('ADMIN');
        redirect(base_url('client/logout'));
    }

    // Cập nhật thời gian hoạt động của phiên
    $CMSNT->update("active_sessions", [
        'last_activity' => gettime(),
        'ip_address' => myip(),
        'user_agent' => getUserAgent()
    ], " `id` = '".$activeSession['id']."' ");

    // Chuyển hướng khi bị khoá tài khoản
    if ($getUser['banned'] != 0) {
        require_once(__DIR__.'/../views/common/banned.php');
        exit();
    }
    if ($getUser['admin'] <= 0) {
        // Rate limit
        checkBlockIP('ADMIN');
        redirect(base_url('client/logout'));
    }
    // nếu phát hiện người dùng đang online thì ngăn chặn khôi phục mật khẩu
    if(!empty($getUser['token_forgot_password'])){
        $CMSNT->update('users', [
            'token_forgot_password' => NULL
        ], " `id` = '".$getUser['id']."' ");
    }
    /* cập nhật thời gian online */
    $CMSNT->update("users", [
        'time_session'  => time()
    ], " `id` = '".$getUser['id']."' ");
}

  