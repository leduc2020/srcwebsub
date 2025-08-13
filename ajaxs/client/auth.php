<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/session.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../libs/database/users.php');
require_once(__DIR__."/../../libs/sendEmail.php");

$CMSNT = new DB();
$Mobile_Detect = new Mobile_Detect();
use PragmaRX\Google2FAQRCode\Google2FA;


if (isset($_POST['action'])) {

    if ($CMSNT->site('status') != 1 && isSecureCookie('admin_login') != true) {
        die(json_encode(['status' => 'error', 'msg' => __('The system is under maintenance, please come back later!')]));
    }

    // ĐĂNG NHẬP
    if($_POST['action'] == 'Login'){
        $username = check_string($_POST['username']);
        $password = check_string($_POST['password']);
        if (empty($username = check_string($_POST['username']))) {
            die(json_encode([
                'status'    => 'error',
                'msg'       => __('Vui lòng nhập username')
            ]));
        }
        if (empty($_POST['password'])) {
            die(json_encode([
                'status'    => 'error',
                'msg'       => __('Vui lòng nhập mật khẩu')
            ]));
        }
        if($CMSNT->site('reCAPTCHA_status') == 1){
            if (empty($_POST['recaptcha'])) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
            $recaptcha = check_string($_POST['recaptcha']);
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$CMSNT->site('reCAPTCHA_secret_key')."&response=$recaptcha";
            $verify = file_get_contents($url);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
        }
        $getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `username` = '$username' ");
        if (!$getUser) {
            // Rate limit
            checkBlockIP('LOGIN', 5);

            die(json_encode([
                'status'    => 'error',
                'msg'       => __('Thông tin đăng nhập không chính xác')
            ]));
        }
        if (time() > $getUser['time_request']) {
            if (time() - $getUser['time_request'] < 3) {
                die(json_encode(['status' => 'error', 'msg' => __('Bạn đang thao tác quá nhanh, vui lòng đợi')]));
            }
        }
        if ($CMSNT->site('type_password') == 'bcrypt') {
            if (!password_verify($password, $getUser['password'])) {
                // Rate limit
                checkBlockIP('LOGIN', 5);
                if($getUser['login_attempts'] >= $CMSNT->site('limit_block_client_login')){
                    $User = new users();
                    $User->Banned($getUser['id'], __('Đăng nhập thất bại nhiều lần'));
                    die(json_encode(['status' => 'error', 'msg' => __('Tài khoản của bạn đã bị tạm khoá do đang nhập sai nhiều lần')]));
                }
                $CMSNT->cong('users', 'login_attempts', 1, " `id` = '".$getUser['id']."' ");
                die(json_encode([
                    'status'    => 'error',
                    'msg'       => __('Thông tin đăng nhập không chính xác')
                ]));
            }
        } else {
            if ($getUser['password'] != TypePassword($password)) {
                // Rate limit
                checkBlockIP('LOGIN', 5);
                if($getUser['login_attempts'] >= $CMSNT->site('limit_block_client_login')){
                    $User = new users();
                    $User->Banned($getUser['id'], __('Đăng nhập thất bại nhiều lần'));
                    die(json_encode(['status' => 'error', 'msg' => __('Tài khoản của bạn đã bị tạm khoá do đang nhập sai nhiều lần')]));
                }
                $CMSNT->cong('users', 'login_attempts', 1, " `id` = '".$getUser['id']."' ");
                die(json_encode([
                    'status'    => 'error',
                    'msg'       => __('Thông tin đăng nhập không chính xác')
                ]));
            }
        }
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
            if($CMSNT->site('email_temp_subject_otp_mail') != ''){
                $content = $CMSNT->site('email_temp_content_otp_mail');
                $content = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $content);
                $content = str_replace('{title}', $CMSNT->site('title'), $content);
                $content = str_replace('{username}', $getUser['username'], $content);
                $content = str_replace('{otp}', $otp_mail, $content);  
                $content = str_replace('{ip}', myip(), $content);  
                $content = str_replace('{device}', getUserAgent(), $content);    
                $content = str_replace('{time}', gettime(), $content);
                ////////////////////////////////////////////////////////////////////
                $subject = $CMSNT->site('email_temp_subject_otp_mail');
                $subject = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $subject);
                $subject = str_replace('{title}', $CMSNT->site('title'), $subject);
                $subject = str_replace('{username}', $getUser['username'], $subject);
                $subject = str_replace('{otp}', $otp_mail, $subject);  
                $subject = str_replace('{ip}', myip(), $subject);  
                $subject = str_replace('{device}', getUserAgent(), $subject);    
                $subject = str_replace('{time}', gettime(), $subject);
                $bcc = $CMSNT->site('title');
                // Rate limit
                checkBlockIP('SEND_OTP', 5);
                sendCSM($getUser['email'], $getUser['username'], $subject, $content, $bcc);
            }

            die(json_encode([
                'status'    => 'verify_otp_mail',
                'url'       => base_url('?action=verify_otp&token='.$token_otp_mail),
                'msg'       => __('Vui lòng xác minh OTP để hoàn tất quá trình đăng nhập')
            ]));
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
            die(json_encode([
                'status'    => 'verify_2fa',
                'url'       => base_url('?action=verify_2fa&token='.$token_2fa),
                'msg'       => __('Vui lòng xác minh 2FA để hoàn tất quá trình đăng nhập')
            ]));
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
            'update_date'       => gettime(),
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
        if($CMSNT->site('telegram_noti_login_user') != '' && $CMSNT->site('telegram_status') == 1 && $getUser['telegram_chat_id'] != ''){
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

        die(json_encode(['status' => 'success','msg' => __('Đăng nhập thành công!')]));
    }

    // XÁC MINH 2FA
    if($_POST['action'] == 'Verify2FA'){

        if (empty($_POST['token_2fa'])) {
            die(json_encode(['status' => 'error','msg' => __('Vui lòng thực hiện đăng nhập lại')]));
        }
        $token_2fa = check_string($_POST['token_2fa']);

        if (empty($_POST['code'])) {
            die(json_encode(['status' => 'error','msg' => __('Vui lòng nhập mã xác minh')]));
        }
        $code = check_string($_POST['code']);
        if($CMSNT->site('reCAPTCHA_status') == 1){
            if (empty($_POST['recaptcha'])) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
            $recaptcha = check_string($_POST['recaptcha']);
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$CMSNT->site('reCAPTCHA_secret_key')."&response=$recaptcha";
            $verify = file_get_contents($url);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
        }
        $getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token_2fa` = '$token_2fa' AND `token_2fa` IS NOT NULL ");
        if (!$getUser) {
            // Rate limit
            checkBlockIP('2FA', 5);
            die(json_encode(['status' => 'error','msg' => __('Thông tin đăng nhập không chính xác')]));
        }
        if (empty($getUser['token_2fa'])) {
            // Rate limit
            checkBlockIP('2FA', 5);
            die(json_encode(['status' => 'error','msg' => __('Dữ liệu không hợp lệ')]));
        }
        if($getUser['limit_2fa'] >= 5){
            $CMSNT->update("users", [
                'limit_2fa' => 0,
                'token_2fa' => NULL
            ], " `id` = '".$getUser['id']."' ");
            $CMSNT->insert('block_ip', [
                'ip'                => myip(),
                'attempts'          => $getUser['limit_2fa'],
                'create_gettime'    => gettime(),
                'banned'            => 1,
                'reason'            => __('Nhập sai 2FA quá 5 lần')
            ]);
            // Rate limit
            checkBlockIP('2FA', 5);
            die(json_encode(['status' => 'error', 'msg' => __('Bạn đã nhập sai quá nhiều lần, vui lòng xác minh lại từ đầu')]));
        }
        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($getUser['SecretKey_2fa'], $code, 2) != true) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => '[Warning] Phát hiện có người đang cố gắng nhập mã xác minh 2FA'
            ]);
            $CMSNT->cong('users', 'limit_2fa', 1, " `id` = '".$getUser['id']."' ");
            // Rate limit
            checkBlockIP('2FA', 5);
            die(json_encode(['status' => 'error', 'msg' => __('Mã xác minh không chính xác')]));
        }
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => '[Warning] '.__('Xác thực 2FA thành công - đã đăng nhập vào website')
        ]);
        $remember_token = generateRememberToken($getUser['remember_token'], $getUser['ip']);
        $CMSNT->update("users", [
            'remember_token'    => $remember_token,
            'token_2fa' => NULL,
            'limit_2fa' => 0,
            'ip'        => myip(),
            'time_request' => time(),
            'time_session' => time(),
            'update_date'       => gettime(),
            'device'    => getUserAgent()
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
        die(json_encode(['status' => 'success','msg' => __('Đăng nhập thành công!')]));
    }

    // XÁC MINH OTP
    if($_POST['action'] == 'VerifyOTP'){
        if (empty($_POST['token_otp_mail'])) {
            // Rate limit
            checkBlockIP('OTP', 15);
            die(json_encode(['status' => 'error','msg' => __('Vui lòng thực hiện đăng nhập lại')]));
        }
        $token_otp_mail = check_string($_POST['token_otp_mail']);
        if (empty($_POST['code'])) {
            die(json_encode(['status' => 'error','msg' => __('Vui lòng nhập OTP')]));
        }
        $code = check_string($_POST['code']);
        if($CMSNT->site('reCAPTCHA_status') == 1){
            if (empty($_POST['recaptcha'])) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
            $recaptcha = check_string($_POST['recaptcha']);
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$CMSNT->site('reCAPTCHA_secret_key')."&response=$recaptcha";
            $verify = file_get_contents($url);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token_otp_mail` = '$token_otp_mail' AND `token_otp_mail` IS NOT NULL ")) {
            // Rate limit
            checkBlockIP('OTP', 15);
            die(json_encode(['status' => 'error','msg' => __('Thông tin đăng nhập không chính xác')]));
        }
        if (empty($getUser['token_otp_mail'])) {
            // Rate limit
            checkBlockIP('OTP', 15);
            die(json_encode(['status' => 'error','msg' => __('Thông tin đăng nhập không chính xác')]));
        }
        if($getUser['limit_otp_mail'] >= 5){
            $CMSNT->update("users", [
                'limit_otp_mail' => 0,
                'token_otp_mail' => NULL,
                'otp_mail'       => NULL
            ], " `id` = '".$getUser['id']."' ");
            $CMSNT->insert('block_ip', [
                'ip'                => myip(),
                'attempts'          => $getUser['limit_otp_mail'],
                'create_gettime'    => gettime(),
                'banned'            => 1,
                'reason'            => __('Nhập sai OTP quá 5 lần')
            ]);
            // Rate limit
            checkBlockIP('OTP', 15);
            die(json_encode(['status' => 'error', 'msg' => __('Bạn đã nhập sai quá nhiều lần, vui lòng xác minh lại từ đầu')]));
        }
        if ($code != $getUser['otp_mail']) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => '[Warning] Phát hiện có người đang cố gắng nhập OTP'
            ]);
            $CMSNT->cong('users', 'limit_otp_mail', 1, " `id` = '".$getUser['id']."' ");
            // Rate limit
            checkBlockIP('OTP', 15);
            die(json_encode(['status' => 'error', 'msg' => __('OTP không chính xác')]));
        }
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => '[Warning] '.__('Xác thực OTP thành công - đã đăng nhập vào website')
        ]);
        $remember_token = generateRememberToken($getUser['remember_token'], $getUser['ip']);
        $CMSNT->update("users", [
            'remember_token'    => $remember_token,
            'token_otp_mail' => NULL,
            'limit_otp_mail' => 0,
            'otp_mail'  => NULL,
            'ip'        => myip(),
            'time_request' => time(),
            'time_session' => time(),
            'update_date'       => gettime(),
            'device'    => getUserAgent()
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
        if($CMSNT->site('telegram_noti_login_user') != '' && $CMSNT->site('telegram_status') == 1 && $getUser['telegram_chat_id'] != ''){
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
        die(json_encode(['status' => 'success','msg' => __('Đăng nhập thành công!')]));
    }

    // ĐĂNG KÝ TÀI KHOẢN
    if($_POST['action'] == 'Register'){
        // if (!empty($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        //     die(json_encode(['status' => 'error', 'msg' => __('Invalid CSRF Protection Token')]));
        // }
        if (empty($_POST['username'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập tên đăng nhập')]));
        }
        $username = check_string($_POST['username']);
        if(validateUsername($username) != true){
            die(json_encode(['status' => 'error', 'msg' => __('Tên đăng nhập không hợp lệ')]));
        }
        if (empty($_POST['email'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập địa chỉ Email')]));
        }
        $email = check_string($_POST['email']);
        if (validateEmail($email) != true) {
            die(json_encode(['status' => 'error', 'msg' => __('Định dạng Email không hợp lệ')]));
        }
        if (empty($_POST['password'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mật khẩu')]));
        }
        if (empty($_POST['repassword'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập lại mật khẩu')]));
        }
        $password = check_string($_POST['password']);
        $repassword = check_string($_POST['repassword']);
        if ($password != $repassword) {
            die(json_encode(['status' => 'error', 'msg' => __('Xác minh mật khẩu không chính xác')]));
        }
        if($CMSNT->site('reCAPTCHA_status') == 1){
            if (empty($_POST['recaptcha'])) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
            $recaptcha = check_string($_POST['recaptcha']);
            $url = "https://www.google.com/recaptcha/api/siteverify?secret=".$CMSNT->site('reCAPTCHA_secret_key')."&response=$recaptcha";
            $verify = file_get_contents($url);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng xác minh Captcha')]));
            }
        }
        if ($CMSNT->num_rows("SELECT * FROM `users` WHERE `username` = '$username' ") > 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Tên đăng nhập đã tồn tại trong hệ thống')]));
        }
        if ($CMSNT->num_rows("SELECT * FROM `users` WHERE `email` = '$email' ") > 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Địa chỉ email đã tồn tại trong hệ thống')]));
        }
        if ($CMSNT->num_rows("SELECT * FROM `users` WHERE `ip` = '".myip()."' ") >= $CMSNT->site('max_register_ip')) {
            die(json_encode(['status' => 'error', 'msg' => __('IP của bạn đã đạt đến giới hạn tạo tài khoản cho phép')]));
        }


        $role = 0; // Mặc định user thường
        $log = __('Create an account');
        // Thực hiện truy vấn
        $query = $CMSNT->query("SELECT COUNT(id) as total FROM `users`");
        // Kiểm tra nếu truy vấn thất bại
        if ($query === false) {
            $role = 0; // Đảm bảo role là 0 nếu MySQL bị lỗi
        } else {
            // Lấy dữ liệu và ép kiểu
            $row = $query->fetch_assoc();
            $countUsers = isset($row['total']) ? (int)$row['total'] : 1;

            // Nếu bảng users rỗng, đặt role = 99999 (admin)
            if ($countUsers === 0) {
                $role = 99999;
                $log = 'Tài khoản đầu tiên khi đăng ký được set Admin cao nhất';
            }
        }
        




        $google2fa = new Google2FA();
        $remember_token = generateUltraSecureToken(64);
        $token = generateUltraSecureToken(64);
        $isCreate = $CMSNT->insert("users", [
            'admin'          => $role,
            'remember_token'    => $remember_token,
            'ref_id'        => isset($_COOKIE['aff']) ? check_string($_COOKIE['aff']) : 0,
            'utm_source'    => isset($_COOKIE['utm_source']) ? check_string($_COOKIE['utm_source']) : 'web',
            'token'         => $token,
            'username'      => $username,
            'email'         => $email,
            'password'      => TypePassword($password),
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'create_date'   => gettime(),
            'update_date'   => gettime(),
            'time_session'  => time(),
            'api_key'       => md5($email.time().random('QWERTYUIOPASDFGHJKL', 6)),
            'SecretKey_2fa' => $google2fa->generateSecretKey()
        ]);
        if ($isCreate) {
            $CMSNT->insert("logs", [
                'user_id'       => $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '$token' ")['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => $log
            ]);

            // Tạo phiên đăng nhập
            createSession($CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '$token' ")['id'], $token);

            die(json_encode(['status' => 'success', 'msg' => __('Đăng ký thành công!')]));
        } else {
            die(json_encode(['status' => 'error', 'msg' => __('Tạo tài khoản không thành công, vui lòng thử lại')]));
        }
    }

    // THAY ĐỔI HỒ SƠ
    if($_POST['action'] == 'ChangeProfile'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
        }
        $isUpdate = $CMSNT->update("users", [
            'fullname' => isset($_POST['fullname']) ? check_string($_POST['fullname']) : null,
            'telegram_chat_id' => isset($_POST['telegram_chat_id']) ? check_string($_POST['telegram_chat_id']) : 'Male',
            'phone' => isset($_POST['phone']) ? check_string($_POST['phone']) : null
        ], " `token` = '".check_string($_POST['token'])."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Change profile information')
            ]);
            die(json_encode(['status' => 'success', 'msg' => __('Lưu thành công')]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Lưu thất bại')]));
    }

    // THAY ĐỔI MẬT KHẨU HỒ SƠ
    if($_POST['action'] == 'ChangePasswordProfile'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
        }
        if (empty($_POST['old_password'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mật khẩu hiện tại')]));
        }
        if (empty($_POST['new_password'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mật khẩu mới')]));
        }
        if(strlen($_POST['new_password']) < 5){
            die(json_encode(['status' => 'error', 'msg' => __('Mật khẩu mới quá ngắn')]));
        }
        if (empty($_POST['confirm_password'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Xác nhận mật khẩu không chính xác')]));
        }
        if ($_POST['confirm_password'] != $_POST['new_password']) {
            die(json_encode(['status' => 'error', 'msg' => __('Xác nhận mật khẩu không chính xác')]));
        }
        $password = check_string($_POST['old_password']);
        if ($CMSNT->site('type_password') == 'bcrypt') {
            if (!password_verify($password, $getUser['password'])) {
                die(json_encode(['status' => 'error', 'msg' => __('Mật khẩu hiện tại không đúng')]));
            }
        } else {
            if ($getUser['password'] != TypePassword($password)) {
                die(json_encode(['status' => 'error', 'msg' => __('Mật khẩu hiện tại không đúng')]));
            }
        }
        $token = generateUltraSecureToken(64);
        $isUpdate = $CMSNT->update("users", [
            'remember_token'    => bin2hex(random_bytes(64)),
            'password'          => TypePassword(check_string($_POST['new_password'])),
            'token'             => $token
        ], " `token` = '".check_string($_POST['token'])."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Change Password')
            ]);
            die(json_encode(['status' => 'success', 'msg' => __('Change password successfully!')]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Password change failed!')]));
    }

    // QUÊN MẬT KHẨU
    if($_POST['action'] == 'ForgotPassword'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if(empty($CMSNT->site('smtp_password'))){
            die(json_encode(['status' => 'error', 'msg' => __('Website chưa được cấu hình SMTP, vui lòng liên hệ Admin')]));
        }
        if (empty($_POST['email'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập địa chỉ Email')]));
        }
        if (!$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `email` = '".check_string($_POST['email'])."' ")) {
            checkBlockIP('RESET_PASSWORD', 15);
            die(json_encode(['status' => 'error', 'msg' => __('Địa chỉ Email này không tồn tại trong hệ thống')]));
        }
        if(time() - $getUser['time_forgot_password'] < 60){
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng thử lại trong ít phút')]));
        }
        $token = md5(random('QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 6).time()).md5(random('QWERTYUIOPASDFGHJKLZXCVBNMqwertyuiopasdfghjklzxcvbnm0123456789', 55));

        if($CMSNT->site('email_temp_subject_forgot_password') != ''){
            $content = $CMSNT->site('email_temp_content_forgot_password');
            $content = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $content);
            $content = str_replace('{title}', $CMSNT->site('title'), $content);
            $content = str_replace('{username}', $getUser['username'], $content);
            $content = str_replace('{link}', base_url('?action=reset-password&token='.$token), $content);  
            $content = str_replace('{ip}', myip(), $content);  
            $content = str_replace('{device}', getUserAgent(), $content);    
            $content = str_replace('{time}', gettime(), $content);
            ////////////////////////////////////////////////////////////////////
            $subject = $CMSNT->site('email_temp_subject_forgot_password');
            $subject = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $subject);
            $subject = str_replace('{title}', $CMSNT->site('title'), $subject);
            $subject = str_replace('{username}', $getUser['username'], $subject);
            $subject = str_replace('{link}', base_url('?action=reset-password&token='.$token), $subject);  
            $subject = str_replace('{ip}', myip(), $subject);  
            $subject = str_replace('{device}', getUserAgent(), $subject);    
            $subject = str_replace('{time}', gettime(), $subject);
            $bcc = $CMSNT->site('title');
            sendCSM($getUser['email'], $getUser['username'], $subject, $content, $bcc);
        }

        $isUpdate = $CMSNT->update('users', [
            'token_forgot_password' => $token,
            'time_forgot_password'  => time()
        ], " `id` = '".$getUser['id']."' ");
        if ($isUpdate) {
            die(json_encode([
                'status' => 'success', 
                'msg' => __('Vui lòng kiểm tra Email của bạn để hoàn tất quá trình đặt lại mật khẩu')
            ]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Có lỗi hệ thống, vui lòng liên hệ Developer')]));
    }

    // ĐỔI MẬT KHẨU KHÔI PHỤC
    if($_POST['action'] == 'ChangePassword'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Liên kết không hợp lệ')]));
        }
        $token = check_string($_POST['token']);
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token_forgot_password` = '$token' AND `token_forgot_password` IS NOT NULL ")) {
            checkBlockIP('RESET_PASSWORD', 15);
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
        }
        if(empty($getUser['token_forgot_password'])){
            die(json_encode(['status' => 'error', 'msg' => __('Liên kết không tồn tại')]));
        }
        if (empty($_POST['newpassword'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mật khẩu mới')]));
        }
        if(strlen($_POST['newpassword']) < 5){
            die(json_encode(['status' => 'error', 'msg' => __('Mật khẩu mới quá ngắn')]));
        }
        if (empty($_POST['renewpassword'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Xác nhận mật khẩu không chính xác')]));
        }
        if ($_POST['renewpassword'] != $_POST['newpassword']) {
            die(json_encode(['status' => 'error', 'msg' => __('Xác nhận mật khẩu không chính xác')]));
        }
        $password = check_string($_POST['newpassword']);
        $token = generateUltraSecureToken(64);
        $isUpdate = $CMSNT->update("users", [
            'remember_token'    => bin2hex(random_bytes(64)),
            'token_forgot_password' => NULL,
            'password'  => TypePassword($password),
            'token'     => $token
        ], " `id` = '".$getUser['id']."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Khôi phục lại mật khẩu')
            ]);
            /** NOTE ACTION */
            $my_text = $CMSNT->site('noti_action');
            $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
            $my_text = str_replace('{username}', $getUser['username'], $my_text);
            $my_text = str_replace('{action}', __('Khôi phục lại mật khẩu'), $my_text);
            $my_text = str_replace('{ip}', myip(), $my_text);    
            $my_text = str_replace('{time}', gettime(), $my_text);
            sendMessAdmin($my_text);
            die(json_encode(['status' => 'success', 'msg' => __('Thay đổi mật khẩu thành công')]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Thay đổi mật khẩu thất bại')]));
    }

    if($_POST['action'] == 'changeAPIKey'){
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        $api_key = md5($getUser['username'].time().random('QWERTYUIOPASDFGHJKL', 6));
        $isUpdate = $CMSNT->update('users', [
            'api_key'       => $api_key
        ], " `id` = '".$getUser['id']."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Thay đổi API KEY')
            ]);
            $data = json_encode([
                'api_key'   => $api_key,
                'status'    => 'success',
                'msg'       => __('Thay đổi API KEY thành công!')
            ]);
            die($data);
        }else{
            die(json_encode(['status' => 'error', 'msg' => __('Thay đổi API KEY thất bại')]));
        }
    }

    if($_POST['action'] == 'changeSecurity'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if($CMSNT->site('smtp_status') != 1){
            die(json_encode(['status' => 'error', 'msg' => __('SMTP chưa được cấu hình, vui lòng liên hệ Admin')]));
        }
        $isUpdate = $CMSNT->update('users', [
            'status_noti_login_to_mail'         => !empty($_POST['status_noti_login_to_mail']) ? check_string($_POST['status_noti_login_to_mail']) : 0,
            'status_otp_mail'                   => !empty($_POST['status_otp_mail']) ? check_string($_POST['status_otp_mail']) : 0,
            'telegram_notification'             => !empty($_POST['telegram_notification']) ? check_string($_POST['telegram_notification']) : 0
        ], " `id` = '".$getUser['id']."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Cấu hình bảo mật')
            ]);
            $data = json_encode([
                'status'    => 'success',
                'msg'       => __('Lưu thay đổi thành công!')
            ]);
            die($data);
        }
    }

    if($_POST['action'] == 'Save2FA'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (empty($_POST['secret'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mã xác minh 2FA')]));
        }
        $google2fa = new Google2FA();
        if ($google2fa->verifyKey($getUser['SecretKey_2fa'], check_string($_POST['secret'])) != true) {
            die(json_encode(['status' => 'error', 'msg' => __('Mã xác minh không chính xác')]));
        }
        $status_2fa = !empty($_POST['status_2fa']) ? check_string($_POST['status_2fa']) : 0;
        if($status_2fa == 1){
            $action = __('Bật xác thực Google Authenticator');
            $SecretKey_2fa = $getUser['SecretKey_2fa'];
        }else{
            $action = __('Tắt xác thực Google Authenticator');
            $SecretKey_2fa = $google2fa->generateSecretKey();
        }

        $isUpdate = $CMSNT->update('users', [
            'status_2fa'    => $status_2fa,
            'SecretKey_2fa' => $SecretKey_2fa
        ], " `id` = '".$getUser['id']."' ");
        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => $action
            ]);
            $data = json_encode([
                'status'    => 'success',
                'msg'       => $action.' '.__('thành công')
            ]);
            die($data);
        }
    }

    if($_POST['action'] == 'logoutSession') {
        if (empty($_POST['session_id'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        $session_id = check_string($_POST['session_id']); 

        // Kiểm tra session có tồn tại và thuộc về user hiện tại
        if (!$session = $CMSNT->get_row("SELECT * FROM `active_sessions` WHERE `id` = '$session_id' AND `user_id` = '".$getUser['id']."'")) {
            die(json_encode(['status' => 'error', 'msg' => __('Phiên đăng nhập không tồn tại')]));
        }

        // Không cho phép đăng xuất phiên hiện tại
        if ($session['device_token'] == getOrCreateDeviceToken()) {
            die(json_encode(['status' => 'error', 'msg' => __('Không thể đăng xuất phiên hiện tại')]));
        }

        // Xóa phiên
        if ($CMSNT->remove("active_sessions", " `id` = '$session_id' ")) {
            die(json_encode(['status' => 'success', 'msg' => __('Đăng xuất phiên thành công')]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Đã có lỗi xảy ra')]));
    }

    if($_POST['action'] == 'logoutAllSessions') {
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        // Xóa tất cả phiên đăng nhập của user này
        if ($CMSNT->remove("active_sessions", " `user_id` = '".$getUser['id']."' ")) {
            // Ghi log
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Đăng xuất tất cả phiên đăng nhập')
            ]);
            
            die(json_encode(['status' => 'success', 'msg' => __('Đã đăng xuất tất cả phiên đăng nhập thành công')]));
        }
        die(json_encode(['status' => 'error', 'msg' => __('Đã có lỗi xảy ra')]));
    }

    if($_POST['action'] == 'Get2FAQR'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        $google2fa = new Google2FA();
        $secretKey = $google2fa->generateSecretKey();
        
        // Tạo mã QR
        $qrCodeUrl = $google2fa->getQRCodeInline(
            $CMSNT->site('title'),
            $getUser['email'],
            $secretKey
        );

        // Lưu secret key tạm thời
        $CMSNT->update('users', [
            'SecretKey_2fa' => $secretKey
        ], " `id` = '".$getUser['id']."' ");

        die(json_encode(['status' => 'success', 'qr_code' => $qrCodeUrl, 'secret_key' => $secretKey]));
    }

    // LẤY THÔNG TIN BOT TELEGRAM
    if($_POST['action'] == 'getTelegramBotInfo'){
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        // Lấy thông tin bot từ cấu hình site
        $bot_username = $CMSNT->site('telegram_bot_username');
        if(empty($bot_username) || $bot_username == 'YourBotUsername') {
            die(json_encode(['status' => 'error', 'msg' => __('Bot Telegram chưa được cấu hình, vui lòng liên hệ Admin')]));
        }

        die(json_encode(['status' => 'success', 'bot_username' => $bot_username]));
    }

    // KIỂM TRA TRẠNG THÁI LIÊN KẾT TELEGRAM
    if($_POST['action'] == 'checkTelegramLink'){
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        // Kiểm tra xem user đã liên kết Telegram chưa
        $linked = !empty($getUser['telegram_chat_id']) ? true : false;
        
        die(json_encode(['status' => 'success', 'linked' => $linked]));
    }

    // HỦY LIÊN KẾT TELEGRAM
    if($_POST['action'] == 'unlinkTelegram'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['token'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
        }

        // Kiểm tra xem đã liên kết chưa
        if(empty($getUser['telegram_chat_id'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Tài khoản chưa liên kết với Telegram')]));
        }

        // Hủy liên kết
        $isUpdate = $CMSNT->update('users', [
            'telegram_chat_id'      => NULL,
            'telegram_username'     => NULL,
            'telegram_notification' => 0
        ], " `id` = '".$getUser['id']."' ");

        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Hủy liên kết tài khoản Telegram')
            ]);
            
            die(json_encode(['status' => 'success', 'msg' => __('Hủy liên kết Telegram thành công')]));
        }
        
        die(json_encode(['status' => 'error', 'msg' => __('Hủy liên kết thất bại')]));
    }

    // LIÊN KẾT TELEGRAM VIA BOT (Được gọi từ Telegram Bot)
    if($_POST['action'] == 'linkTelegramBot'){
        if ($CMSNT->site('status_demo') != 0) {
            die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
        }
        if (empty($_POST['api_key'])) {
            die(json_encode(['status' => 'error', 'msg' => __('API Key không hợp lệ')]));
        }
        if (empty($_POST['chat_id'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Chat ID không hợp lệ')]));
        }
        if (empty($_POST['telegram_username'])) {
            die(json_encode(['status' => 'error', 'msg' => __('Username Telegram không hợp lệ')]));
        }

        $api_key = check_string($_POST['api_key']);
        $chat_id = check_string($_POST['chat_id']);
        $telegram_username = check_string($_POST['telegram_username']);
        $full_name = isset($_POST['full_name']) ? check_string($_POST['full_name']) : '';

        // Tìm user theo token
        if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '$api_key' AND `banned` = 0 ")) {
            die(json_encode(['status' => 'error', 'msg' => __('API Key không hợp lệ hoặc tài khoản đã bị khóa')]));
        }

        // Kiểm tra xem chat_id đã được liên kết với tài khoản khác chưa
        if ($existUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `telegram_chat_id` = '$chat_id' AND `id` != '".$getUser['id']."'")) {
            die(json_encode(['status' => 'error', 'msg' => __('Tài khoản Telegram này đã được liên kết với tài khoản khác')]));
        }

        // Cập nhật thông tin Telegram cho user
        $isUpdate = $CMSNT->update('users', [
            'telegram_chat_id'      => $chat_id,
            'telegram_username'     => $telegram_username,
            'telegram_notification' => 1
        ], " `id` = '".$getUser['id']."' ");

        if ($isUpdate) {
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => 'Telegram Bot',
                'device'        => 'Telegram Bot',
                'createdate'    => gettime(),
                'action'        => __('Liên kết tài khoản với Telegram').' (@'.$telegram_username.')'
            ]);
            
            die(json_encode(['status' => 'success', 'msg' => __('Liên kết Telegram thành công'), 'username' => $getUser['username']]));
        }
        
        die(json_encode(['status' => 'error', 'msg' => __('Liên kết thất bại')]));
    }

    
}