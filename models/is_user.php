<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$CMSNT = new DB();
require_once 'libs/session.php';
 

if (isSecureCookie('user_login') != true) {
    redirect(base_url('client/logout'));
} else {
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_COOKIE['user_login'])."'  ");
    // chuyển hướng đăng nhập khi thông tin login không tồn tại
    if (!$getUser) {
        // Rate limit
        checkBlockIP('LOGIN');
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
        checkBlockIP('LOGIN');
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
    // khoá tài khoản trường hợp âm tiền, tránh bug
    if ($getUser['money'] < -500) {
        $User = new users();
        $User->Banned($getUser['id'], 'Tài khoản âm tiền, ghi vấn bug');
        require_once(__DIR__.'/../views/common/banned.php');
        exit();
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

    // Set cấp bậc khi đủ điều kiện
    updateUserRank($getUser['id'], $getUser['total_money']);
}


  