<?php 
define("IN_SITE", true);
require_once(__DIR__ . "/../../libs/db.php");
require_once(__DIR__ . "/../../libs/lang.php");
require_once(__DIR__ . "/../../libs/helper.php");
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__."/../../libs/sendEmail.php");

if (!isset($_POST['action'])) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => 'The Request Not Found'
    ]);
    die($data);
}
if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
}

if($_POST['action'] == 'changeStatusChildPanel') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));
    $status = check_string($_POST['status']);

    if(!in_array($status, ['Actived', 'Pending', 'Cancel', 'Expired'])){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }

    if(!$CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'")){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    $isUpdate = $CMSNT->update('child_panels', ['status' => $status], " `id` = '$id'");
    if($isUpdate){
        // GỬI THÔNG BÁO CHO USER KHI CHILD PANEL HẾT HẠN
        $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '{$childPanel['user_id']}'");
        if($status == 'Expired' && $user['telegram_chat_id'] != '' && $CMSNT->site('noti_user_child_panel_expired') != '' && $user['telegram_notification'] == 1){
            $content = $CMSNT->site('noti_user_child_panel_expired');
            $content = str_replace('{domain}', $childPanel['domain'], $content);
            $content = str_replace('{username}', $user['username'], $content);
            $content = str_replace('{email_user}', $user['email'], $content);
            $content = str_replace('{phone_user}', $user['phone'], $content);
            $content = str_replace('{time}', gettime(), $content);
            sendMessTelegram($content, '', $user['telegram_chat_id']);
        }
        // Log action
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => __('Thay đổi trạng thái Child Panel') . ' #' . $id . ' -> ' . $status . ' (' . $childPanel['domain'] . ')'
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Thay đổi trạng thái thành công')]));
    }
    else{
        die(json_encode(['status' => 'error', 'msg' => __('Thay đổi trạng thái thất bại')]));
    }
}

if($_POST['action'] == 'removeChildPanel') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    // Xóa Child Panel
    $CMSNT->remove('child_panels', ['id' => $id]);

    // Log action
    $CMSNT->insert("logs", [
        'user_id' => $getUser['id'],
        'ip' => myip(),
        'device' => getUserAgent(),
        'createdate' => gettime(),
        'action' => __('Xóa Child Panel') . ' #' . $id . ' (' . $childPanel['domain'] . ')'
    ]);

    die(json_encode(['status' => 'success', 'msg' => __('Xóa Child Panel thành công')]));
}

if($_POST['action'] == 'extendChildPanel') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));
    $days = intval(check_string($_POST['days']));

    if($days < 1){
        die(json_encode(['status' => 'error', 'msg' => __('Số ngày gia hạn không hợp lệ')]));
    }

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    // Tính toán ngày hết hạn mới
    $currentExpired = $childPanel['expired_at'] ? strtotime($childPanel['expired_at']) : time();
    if($currentExpired < time()) {
        // Nếu đã hết hạn, tính từ thời điểm hiện tại
        $currentExpired = time();
    }
    $newExpired = date('Y-m-d H:i:s', $currentExpired + ($days * 24 * 60 * 60));

    // Cập nhật ngày hết hạn
    $CMSNT->update('child_panels', [
        'expired_at' => $newExpired,
        'updated_at' => gettime()
    ], " `id` = '$id'");

    // Log action
    $CMSNT->insert("logs", [
        'user_id' => $getUser['id'],
        'ip' => myip(),
        'device' => getUserAgent(),
        'createdate' => gettime(),
        'action' => __('Gia hạn Child Panel') . ' #' . $id . ' (' . $childPanel['domain'] . ') thêm ' . $days . ' ngày'
    ]);

    die(json_encode(['status' => 'success', 'msg' => __('Gia hạn Child Panel thành công') . ' ' . $days . ' ' . __('ngày')]));
}

if($_POST['action'] == 'editChildPanel') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));
    $note = check_string($_POST['note']);
    $status = check_string($_POST['status']);
    $expired_at = check_string($_POST['expired_at']);

    if(!in_array($status, ['Actived', 'Pending', 'Cancel', 'Expired'])){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    $updateData = [
        'note' => $note,
        'status' => $status,
        'updated_at' => gettime()
    ];

    if(!empty($expired_at)){
        $updateData['expired_at'] = $expired_at . ' 23:59:59';
    }

    // Cập nhật Child Panel
    $isUpdate = $CMSNT->update('child_panels', $updateData, " `id` = '$id'");

    // Log action
    $CMSNT->insert("logs", [
        'user_id' => $getUser['id'],
        'ip' => myip(),
        'device' => getUserAgent(),
        'createdate' => gettime(),
        'action' => __('Cập nhật Child Panel') . ' #' . $id . ' (' . $childPanel['domain'] . ')'
    ]);

    if($isUpdate){
        // GỬI THÔNG BÁO CHO USER KHI CHILD PANEL HẾT HẠN
        $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '{$childPanel['user_id']}'");
        if($status == 'Expired' && $user['telegram_chat_id'] != '' && $CMSNT->site('noti_user_child_panel_expired') != '' && $user['telegram_notification'] == 1){
            $content = $CMSNT->site('noti_user_child_panel_expired');
            $content = str_replace('{domain}', $childPanel['domain'], $content);
            $content = str_replace('{username}', $user['username'], $content);
            $content = str_replace('{email_user}', $user['email'], $content);
            $content = str_replace('{phone_user}', $user['phone'], $content);
            $content = str_replace('{time}', gettime(), $content);
            sendMessTelegram($content, '', $user['telegram_chat_id']);
        }
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật Child Panel thành công')]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Cập nhật Child Panel thất bại')]));
    }
}

if($_POST['action'] == 'getChildPanel') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'view_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }

    die(json_encode(['status' => 'success', 'data' => $childPanel]));
}

if($_POST['action'] == 'bulkChangeStatus') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $ids = $_POST['ids'];
    $status = check_string($_POST['status']);

    if(!is_array($ids) || empty($ids)){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một Child Panel')]));
    }

    if(!in_array($status, ['Actived', 'Pending', 'Cancel', 'Expired'])){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }

    $updatedCount = 0;
    $childPanelDomains = [];
    
    foreach($ids as $id){
        $id = intval($id);
        $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
        if($childPanel){
            $isUpdate = $CMSNT->update('child_panels', [
                'status' => $status,
                'updated_at' => gettime()
            ], " `id` = '$id'");
            if($isUpdate){
                $updatedCount++;
                $childPanelDomains[] = $childPanel['domain'];

                $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '{$childPanel['user_id']}'");

                // GỬI THÔNG BÁO CHO USER KHI CHILD PANEL HẾT HẠN
                if($status == 'Expired' && $user['telegram_chat_id'] != '' && $CMSNT->site('noti_user_child_panel_expired') != '' && $user['telegram_notification'] == 1){
                    $content = $CMSNT->site('noti_user_child_panel_expired');
                    $content = str_replace('{domain}', $childPanel['domain'], $content);
                    $content = str_replace('{username}', $user['username'], $content);
                    $content = str_replace('{email_user}', $user['email'], $content);
                    $content = str_replace('{phone_user}', $user['phone'], $content);
                    $content = str_replace('{time}', gettime(), $content);
                    sendMessTelegram($content, '', $user['telegram_chat_id']);
                }
            }
        }
    }

    if($updatedCount > 0){
        
        // Log action
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => __('Thay đổi trạng thái hàng loạt') . ' ' . $updatedCount . ' Child Panel -> ' . $status . ' (' . implode(', ', array_slice($childPanelDomains, 0, 5)) . ($updatedCount > 5 ? '...' : '') . ')'
        ]);

        die(json_encode(['status' => 'success', 'msg' => __('Đã cập nhật trạng thái cho') . ' ' . $updatedCount . ' Child Panel']));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Không có Child Panel nào được cập nhật')]));
    }
}

if($_POST['action'] == 'bulkDelete') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $ids = $_POST['ids'];
    if(!is_array($ids) || empty($ids)){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một Child Panel')]));
    }

    $deletedCount = 0;
    $childPanelDomains = [];
    
    foreach($ids as $id){
        $id = intval($id);
        $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id'");
        if($childPanel){
            $CMSNT->remove('child_panels', ['id' => $id]);
            $deletedCount++;
            $childPanelDomains[] = $childPanel['domain'];
        }
    }

    if($deletedCount > 0){
        // Log action
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => __('Xóa hàng loạt') . ' ' . $deletedCount . ' Child Panel (' . implode(', ', array_slice($childPanelDomains, 0, 5)) . ($deletedCount > 5 ? '...' : '') . ')'
        ]);

        die(json_encode(['status' => 'success', 'msg' => __('Đã xóa') . ' ' . $deletedCount . ' Child Panel']));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Không có Child Panel nào được xóa')]));
    }
}

if($_POST['action'] == 'copyChildPanelInfo') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'view_child_panel') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));

    $childPanel = $CMSNT->get_row("SELECT * FROM `child_panels` WHERE `id` = '$id' ");
    if(!$childPanel){
        die(json_encode(['status' => 'error', 'msg' => __('Child Panel không tồn tại')]));
    }
    if(!$user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '{$childPanel['user_id']}'")){
        die(json_encode(['status' => 'error', 'msg' => __('User không tồn tại')]));
    }

    // Tạo thông tin để copy
    $copyInfo = "=== THÔNG TIN CHILD PANEL ===\n";
    $copyInfo .= "ID: #" . $childPanel['id'] . "\n";
    $copyInfo .= "Domain: " . $childPanel['domain'] . "\n";
    $copyInfo .= "API Key: " . $user['api_key'] . "\n";
    
    
    if($childPanel['note']) {
        $copyInfo .= "Ghi chú: " . $childPanel['note'] . "\n";
    }
    
    // Thêm thông tin đăng nhập nếu có
    if($childPanel['note_username'] || $childPanel['note_password'] || $childPanel['note_email'] || $childPanel['note_phone']) {
        $copyInfo .= "\n=== THÔNG TIN ĐĂNG NHẬP ===\n";
        if($childPanel['note_username']) {
            $copyInfo .= "Username: " . $childPanel['note_username'] . "\n";
        }
        if($childPanel['note_password']) {
            $copyInfo .= "Password: " . $childPanel['note_password'] . "\n";
        }
        if($childPanel['note_email']) {
            $copyInfo .= "Email: " . $childPanel['note_email'] . "\n";
        }
        if($childPanel['note_phone']) {
            $copyInfo .= "Phone: " . $childPanel['note_phone'] . "\n";
        }
    }
    
    $copyInfo .= "\n=== THÔNG TIN API ===\n";
    $copyInfo .= "API Endpoint: " . BASE_URL() . "/api/v2\n";
    
    // Log action
    $CMSNT->insert("logs", [
        'user_id' => $getUser['id'],
        'ip' => myip(),
        'device' => getUserAgent(),
        'createdate' => gettime(),
        'action' => __('Sao chép thông tin Child Panel') . ' #' . $id . ' (' . $childPanel['domain'] . ')'
    ]);

    die(json_encode([
        'status' => 'success', 
        'msg' => __('Thông tin Child Panel đã được sao chép vào clipboard'),
        'data' => $copyInfo
    ]));
}