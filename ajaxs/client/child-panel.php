<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../libs/database/users.php');
require_once(__DIR__."/../../libs/sendEmail.php");




if ($CMSNT->site('status_demo') != 0) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('This function cannot be used because this is a demo site')
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



if($_POST['action'] == 'RenewChildPanel'){
    if ($CMSNT->site('status_demo') != 0) {
        die(json_encode(['status' => 'error', 'message' => __('Chức năng này không thể sử dụng trên website demo')]));
    }
    if($CMSNT->site('child_panel_status') != 1){
        die(json_encode(['status' => 'error', 'message' => __('Chức năng Child Panel đang được bảo trì')]));
    }
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'message' => __('Vui lòng đăng nhập')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'message' => __('Vui lòng đăng nhập')]));
    }

    // Validation inputs
    if (empty($_POST['child_panel_id'])) {
        die(json_encode(['status' => 'error', 'message' => __('Vui lòng chọn Child Panel cần gia hạn')]));
    }
    if (empty($_POST['renewal_period'])) {
        die(json_encode(['status' => 'error', 'message' => __('Vui lòng chọn thời gian gia hạn')]));
    }

    $child_panel_id = check_string($_POST['child_panel_id']);
    $renewal_period = intval(check_string($_POST['renewal_period']));

    // Validate renewal period
    if (!in_array($renewal_period, [1, 3, 6, 12])) {
        die(json_encode(['status' => 'error', 'message' => __('Thời gian gia hạn không hợp lệ')]));
    }

    // Check if child panel exists and belongs to user
    $child_panel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$child_panel_id' AND `user_id` = '".$getUser['id']."'");
    if (!$child_panel) {
        die(json_encode(['status' => 'error', 'message' => __('Child Panel không tồn tại hoặc không thuộc về bạn')]));
    }

    // Check if child panel is active
    if (!in_array($child_panel['status'], ['Active', 'Pending', 'Expired'])) {
        die(json_encode(['status' => 'error', 'message' => __('Child Panel đang trong trạng thái không thể gia hạn')]));
    }

    // Calculate renewal cost
    $child_panel_price = $CMSNT->site('child_panel_price');
    $renewal_cost = $child_panel_price * $renewal_period;

    // Check user balance
    if ($getUser['money'] < $renewal_cost) {
        die(json_encode(['status' => 'error', 'message' => __('Số dư tài khoản không đủ để gia hạn Child Panel. Cần: ').format_currency($renewal_cost)]));
    }

    // Calculate new expiry date
    $current_expired = strtotime($child_panel['expired_at']);
    $current_time = time();
    
    // If already expired, extend from current time, otherwise extend from current expiry
    $base_time = ($current_expired > $current_time) ? $current_expired : $current_time;
    $new_expired_at = date('Y-m-d H:i:s', strtotime("+{$renewal_period} months", $base_time));

    // Start transaction
    $CMSNT->query("START TRANSACTION");

    try {
        // TRỪ TIỀN TỪ TÀI KHOẢN
        $User = new users();
        $deduct = $User->RemoveCredits($getUser['id'], $renewal_cost, __('Gia hạn Child Panel').' - '.$child_panel['domain'].' ('.$renewal_period.' '.__('tháng').')');
        if (!$deduct) {
            throw new Exception(__('Không thể trừ tiền từ tài khoản'));
        }

        // KIỂM TRA GIAN LẬN
        if (getRowRealtime('users', $getUser['id'], 'money') < -100000) {
            $User->Banned($getUser['id'], __('Gian lận khi gia hạn Child Panel'));
            throw new Exception(__('Tài khoản đã bị khóa vì gian lận'));
        }

        // CẬP NHẬT THỜI GIAN GIA HẠN
        $update_result = $CMSNT->update('child_panels', [
            'expired_at' => $new_expired_at,
            'updated_at' => gettime()
        ], " `id` = '$child_panel_id' ");

        if (!$update_result) {
            throw new Exception(__('Không thể cập nhật thời gian gia hạn'));
        }

        // COMMIT TRANSACTION
        $CMSNT->query("COMMIT");

        // GỬI THÔNG BÁO CHO ADMIN
        $notification_text = $CMSNT->site('noti_child_panel_renewal');
        $notification_text = str_replace('{domain}', $child_panel['domain'], $notification_text);
        $notification_text = str_replace('{username}', $getUser['username'], $notification_text);
        $notification_text = str_replace('{email}', $child_panel['note_email'], $notification_text);
        $notification_text = str_replace('{phone}', $child_panel['note_phone'], $notification_text);
        $notification_text = str_replace('{ip}', myip(), $notification_text);
        $notification_text = str_replace('{time}', gettime(), $notification_text);
        $notification_text = str_replace('{renewal_period}', $renewal_period.' '.__('tháng'), $notification_text);
        $notification_text = str_replace('{renewal_cost}', format_currency($renewal_cost), $notification_text);
        $notification_text = str_replace('{new_expired_at}', date('d/m/Y H:i:s', strtotime($new_expired_at)), $notification_text);
        sendMessAdmin($notification_text);

        // NHẬT KÝ HOẠT ĐỘNG USER
        $CMSNT->insert('logs', [
            'user_id'   => $getUser['id'],
            'action'    => __('Gia hạn Child Panel').' '.$child_panel['domain'].' ('.$renewal_period.' '.__('tháng').')',
            'ip'        => myip(),
            'device'    => getUserAgent(),
            'createdate' => gettime()
        ]);

        die(json_encode([
            'status' => 'success', 
            'message' => __('Child Panel đã được gia hạn thành công!').' '.__('Thời gian hết hạn mới: ').date('d/m/Y H:i:s', strtotime($new_expired_at))
        ]));

    } catch (Exception $e) {
        // Rollback transaction
        $CMSNT->query("ROLLBACK");
        die(json_encode(['status' => 'error', 'message' => $e->getMessage()]));
    }
}

if($_POST['action'] == 'CreateChildPanel'){
    if ($CMSNT->site('status_demo') != 0) {
        die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
    }
    if($CMSNT->site('child_panel_status') != 1){
        die(json_encode(['status' => 'error', 'msg' => __('Chức năng Child Panel đang được bảo trì')]));
    }
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }

    // Validation inputs
    if (empty($_POST['domain'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập tên miền')]));
    }
    if (empty($_POST['admin_username'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập username admin')]));
    }
    if (empty($_POST['admin_password'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập password admin')]));
    }
    if (empty($_POST['admin_email'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập email admin')]));
    }

    $domain = strtolower(trim(check_string($_POST['domain'])));
    $admin_username = check_string($_POST['admin_username']);
    $admin_password = check_string($_POST['admin_password']);
    $admin_email = check_string($_POST['admin_email']);
    $admin_phone = check_string($_POST['admin_phone']) ?? '';

    // Validate domain format
    if (!filter_var('http://'.$domain, FILTER_VALIDATE_URL) || !preg_match('/^[a-zA-Z0-9][a-zA-Z0-9-_.]*[a-zA-Z0-9]*\.[a-zA-Z]{2,}$/', $domain)) {
        die(json_encode(['status' => 'error', 'msg' => __('Định dạng domain không hợp lệ')]));
    }

    // Check if domain already exists
    if ($CMSNT->get_row("SELECT * FROM `child_panels` WHERE `domain` = '$domain'")) {
        die(json_encode(['status' => 'error', 'msg' => __('Domain này đã được sử dụng')]));
    }

    // Validate username format
    if (!preg_match('/^[a-zA-Z0-9_.-]+$/', $admin_username)) {
        die(json_encode(['status' => 'error', 'msg' => __('Username chỉ được chứa chữ, số, dấu gạch dưới và dấu chấm')]));
    }

    // Validate password length
    if (strlen($admin_password) < 6) {
        die(json_encode(['status' => 'error', 'msg' => __('Mật khẩu phải có ít nhất 6 ký tự')]));
    }

    // Validate email format
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        die(json_encode(['status' => 'error', 'msg' => __('Định dạng email không hợp lệ')]));
    }

    // Check user balance if required
    $child_panel_price = $CMSNT->site('child_panel_price');
    if ($child_panel_price > 0 && $getUser['money'] < $child_panel_price) {
        die(json_encode(['status' => 'error', 'msg' => __('Số dư tài khoản không đủ để tạo Child Panel')]));
    }

    // Calculate expired date (1 month from now)
    $expired_at = date('Y-m-d H:i:s', strtotime('+1 month'));

    // Prepare admin info
    $note_data = [
        'username' => $admin_username,
        'password' => $admin_password,
        'email' => $admin_email,
        'phone' => $admin_phone
    ];

    // Start transaction
    $CMSNT->query("START TRANSACTION");

    try {
        // Deduct money if required
        if ($child_panel_price > 0) {
            $User = new users();
            $deduct = $User->RemoveCredits($getUser['id'], $child_panel_price, __('Tạo Child Panel').' - '.$domain);
            if (!$deduct) {
                throw new Exception(__('Không thể trừ tiền từ tài khoản'));
            }

            // Check for fraud
            if (getRowRealtime('users', $getUser['id'], 'money') < -100000) {
                $User->Banned($getUser['id'], __('Gian lận khi tạo Child Panel'));
                throw new Exception(__('Tài khoản đã bị khóa vì gian lận'));
            }
        }

        // Insert child panel
        $insert_data = [
            'user_id' => $getUser['id'],
            'domain' => $domain,
            'status' => 'Pending',
            'note' => __('Tạo Child Panel cho domain: ').$domain,
            'expired_at' => $expired_at,
            'created_at' => gettime(),
            'updated_at' => gettime(),
            'note_username' => $admin_username,
            'note_password' => $admin_password,
            'note_email' => $admin_email,
            'note_phone' => $admin_phone
        ];
        $insert_id = $CMSNT->insert('child_panels', $insert_data);

        if (!$insert_id) {
            throw new Exception(__('Không thể tạo Child Panel'));
        }

        // Commit transaction
        $CMSNT->query("COMMIT");

        // Send notification to admin
        $notification_text = $CMSNT->site('noti_child_panel_create');
        $notification_text = str_replace('{domain}', $domain, $notification_text);
        $notification_text = str_replace('{username}', $getUser['username'], $notification_text);
        $notification_text = str_replace('{api_key}', $getUser['api_key'], $notification_text);
        $notification_text = str_replace('{email}', $admin_email, $notification_text);
        $notification_text = str_replace('{phone}', $admin_phone, $notification_text);
        $notification_text = str_replace('{ip}', myip(), $notification_text);
        $notification_text = str_replace('{time}', gettime(), $notification_text);
        
        if ($CMSNT->site('telegram_chat_id')) {
            sendMessTelegram($notification_text, '', $CMSNT->site('telegram_chat_id'));
        }

        // Log activity
        $CMSNT->insert('logs', [
            'user_id'   => $getUser['id'],
            'action'    => __('Tạo Child Panel cho domain: ').$domain,
            'ip'        => myip(),
            'device'    => getUserAgent(),
            'createdate' => gettime()
        ]);

        die(json_encode([
            'status' => 'success', 
            'msg' => __('Child Panel đã được tạo thành công! Hệ thống sẽ thiết lập và kích hoạt trong vài phút.'),
        ]));

    } catch (Exception $e) {
        // Rollback transaction
        $CMSNT->query("ROLLBACK");
        die(json_encode(['status' => 'error', 'msg' => $e->getMessage()]));
    }
}