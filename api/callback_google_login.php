<?php

define("IN_SITE", true);
require_once(__DIR__."/../libs/db.php");
require_once(__DIR__."/../libs/lang.php");
require_once(__DIR__."/../libs/session.php");
require_once(__DIR__."/../libs/helper.php");
require_once(__DIR__."/../config.php");
require_once(__DIR__."/../libs/sendEmail.php");
require_once(__DIR__."/../libs/database/users.php");

$CMSNT = new DB();
use PragmaRX\Google2FAQRCode\Google2FA;

// Máy chủ phải được bật NTP synchronized mới có thể nhận callback từ google

$client = new Google_Client();
$client->setClientId($CMSNT->site('google_login_client_id')); // Client ID của bạn
$client->setClientSecret($CMSNT->site('google_login_client_secret')); // Client Secret của bạn
$client->setRedirectUri(base_url('api/callback_google_login.php')); // URL callback
$client->addScope("email");
$client->addScope("profile");

// Xử lý callback sau khi Google redirect
if (isset($_GET['code'])) {

    // Lấy token từ Google
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
    if (isset($token['error'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Đăng nhập thất bại, vui lòng thử lại sau!')]));
    }

    // Xác minh id_token để đảm bảo token hợp lệ
    $payload = $client->verifyIdToken($token['id_token']);
    if (!$payload) {
        die(json_encode(['status' => 'error', 'msg' => __('Token Google không hợp lệ')]));
    }

    // Lấy thông tin từ payload
    $email = $payload['email'];
    $name = $payload['name'];
    $google_id = $payload['sub']; // ID duy nhất từ Google


    // Kiểm tra email có tồn tại trong hệ thống không
    $getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `email` = '".check_string($email)."' ");
    if ($getUser) {
        // Nếu email đã tồn tại -> Đăng nhập
        if ($getUser['banned'] == 1) {
            die(json_encode(['status' => 'error', 'msg' => __('Tài khoản của bạn đã bị khoá truy cập')]));
        }
        if($getUser['status_otp_mail'] == 1){
            $otp_mail = random('QWERTYUOPASDFGHJKZXCVBNM0126456789', 6);
            $token_otp_mail = md5(uniqid()).md5(random('QWERTYUOPASDFGHJKZXCVBNM0126456789', 12));
            $CMSNT->update('users', [
                'token_otp_mail'    => $token_otp_mail,
                'otp_mail'          => $otp_mail,
                'limit_otp_mail'    => 0
            ], " `id` = '".$getUser['id']."' ");
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => '[Warning] '.__('Đăng nhập thành công - đang tiến hành đến bước xác minh OTP Mail')
            ]);
            // THÔNG BÁO VỀ MAIL KHI LOGIN
            if($CMSNT->site('email_temp_subject_otp_mail') != ''){
                $replacements = [
                    '{domain}' => check_string($_SERVER['SERVER_NAME']),
                    '{title}' => $CMSNT->site('title'),
                    '{username}' => $getUser['username'],
                    '{otp}' => $otp_mail,
                    '{ip}' => myip(),
                    '{device}' => getUserAgent(),
                    '{time}' => gettime()
                ];
                
                $content = str_replace(array_keys($replacements), array_values($replacements), $CMSNT->site('email_temp_content_otp_mail'));
                $subject = str_replace(array_keys($replacements), array_values($replacements), $CMSNT->site('email_temp_subject_otp_mail'));
                
                sendCSM($getUser['email'], $getUser['username'], $subject, $content, $CMSNT->site('title'));
            }
            redirect(base_url('?action=verify_otp&token='.$token_otp_mail));
        }
        if($getUser['status_2fa'] == 1){
            $token_2fa = md5(random('qwertyuiopasdfghjklzxcvbnm0123456789', 55)).md5(uniqid());
            $CMSNT->update('users', [
                'token_2fa' => $token_2fa,
                'limit_2fa' => 0
            ], " `id` = '".$getUser['id']."' ");
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => '[Warning] '.__('Đăng nhập thành công - đang tiến hành đến bước xác minh 2FA')
            ]);
            redirect(base_url('?action=verify_2fa&token='.$token_2fa));
        }
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => '[Warning] '.__('Thực hiện đăng nhập vào website')
        ]);
        $remember_token = generateRememberToken($getUser['remember_token'], $getUser['ip']);
        $CMSNT->update("users", [
            'remember_token'    => $remember_token,
            'token_2fa'         => NULL,
            'limit_2fa'         => 0,
            'token_otp_mail'    => NULL,
            'limit_otp_mail'    => 0,
            'otp_mail'          => NULL,
            'ip'                => myip(),
            'time_request'      => time(),
            'time_session'      => time(),
            'device'            => getUserAgent()
        ], " `id` = '".$getUser['id']."' ");

        // THÔNG BÁO VỀ MAIL KHI ĐĂNG NHẬP
        if($getUser['status_noti_login_to_mail'] == 1 && $CMSNT->site('email_temp_subject_warning_login') != ''){
            $replacements = [
                '{domain}' => check_string($_SERVER['SERVER_NAME']),
                '{title}' => $CMSNT->site('title'),
                '{username}' => $getUser['username'],
                '{ip}' => myip(),
                '{device}' => getUserAgent(),
                '{time}' => gettime()
            ];
            $content = str_replace(array_keys($replacements), array_values($replacements), $CMSNT->site('email_temp_content_warning_login'));
            $subject = str_replace(array_keys($replacements), array_values($replacements), $CMSNT->site('email_temp_subject_warning_login'));
            sendCSM($getUser['email'], $getUser['username'], $subject, $content, $CMSNT->site('title'));
        }

        // THÔNG BÁO VỀ TELEGRAM KHI LOGIN
        if($CMSNT->site('telegram_noti_login_user') != '' && $CMSNT->site('telegram_status') == 1 && $getUser['telegram_chat_id'] != '' && $getUser['telegram_notification'] == 1){
            $content = $CMSNT->site('telegram_noti_login_user');
            $content = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $content);
            $content = str_replace('{title}', $CMSNT->site('title'), $content);
            $content = str_replace('{username}', $getUser['username'], $content);
            $content = str_replace('{ip}', myip(), $content);  
            $content = str_replace('{device}', getUserAgent(), $content);    
            $content = str_replace('{time}', gettime(), $content);
            sendMessTelegram($content, $CMSNT->site('telegram_token'), $getUser['telegram_chat_id']);
        }

        // Tạo phiên đăng nhập
        createSession($getUser['id'], $getUser['token']);

        // Chuyển hướng về trang Order
        redirect(base_url('client/order'));

    } else {
        // Nếu email chưa tồn tại -> Đăng ký tài khoản mới
        $google2fa = new Google2FA();
        $new_token = random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 64).time().md5(random('QWERTYUIOPASDGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 32));
        $isCreate = $CMSNT->insert("users", [
            'ref_id'        => isset($_COOKIE['aff']) ? check_string($_COOKIE['aff']) : 0,
            'utm_source'    => isset($_COOKIE['utm_source']) ? check_string($_COOKIE['utm_source']) : 'web',
            'token'         => $new_token,
            'username'      => $email,
            'email'         => $email,
            'password'      => generateApiKey(),
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'create_date'   => gettime(),
            'update_date'   => gettime(),
            'time_session'  => time(),
            'api_key'       => generateApiKey(),
            'SecretKey_2fa' => $google2fa->generateSecretKey(),
            'status_noti_login_to_mail' => 1
        ]);

        if ($isCreate) {
            $CMSNT->insert("logs", [
                'user_id'       => $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '$new_token' ")['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Đăng ký tài khoản mới')
            ]);

            // Tạo phiên đăng nhập
            createSession($CMSNT->get_row("SELECT `id` FROM `users` WHERE `token` = '$new_token' ")['id'], $new_token);

            // Chuyển hướng về trang Order
            redirect(base_url('client/order'));
        } else {
            die(json_encode(['status' => 'error', 'msg' => __('Không thể tạo tài khoản mới')]));
        }
    }
}