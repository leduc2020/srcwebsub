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


// Thay đổi trạng thái ticket
if($_POST['action'] == 'changeStatusTicket') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_ticket') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));
    $status = check_string($_POST['status']);
    
    if(!in_array($status, array_keys($config_status_support_tickets))){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }
    
    // Kiểm tra ticket có tồn tại không
    if(!$ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$id'")){
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    
    // Cập nhật trạng thái
    $isUpdate = $CMSNT->update('support_tickets', [
        'status' => $status
    ], "`id` = '$id'");
    
    if($isUpdate){
        // Ghi log hành động

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Thay đổi trạng thái ticket #%d thành %s'), $id, $config_status_support_tickets[$status])
        ]);
        
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật trạng thái thành công')]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Có lỗi xảy ra, vui lòng thử lại')]));
    }
}

// Xóa ticket
if($_POST['action'] == 'deleteTicket') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_ticket') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $id = intval(check_string($_POST['id']));
    
    // Kiểm tra ticket có tồn tại không
    if(!$ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$id'")){
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    
    // Xóa tất cả tin nhắn của ticket này trước
    $CMSNT->remove('support_messages', "`ticket_id` = '$id'");
    
    // Xóa ticket
    $isDelete = $CMSNT->remove('support_tickets', "`id` = '$id'");
    
    if($isDelete){
        // Ghi log hành động
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa ticket')." #$id ({$ticket['subject']})"
        ]);

        die(json_encode(['status' => 'success', 'msg' => __('Xóa ticket thành công')]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Có lỗi xảy ra, vui lòng thử lại')]));
    }
}

// Trả lời ticket (cho admin)
if($_POST['action'] == 'replyTicket') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_support_tickets') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    $ticket_id = intval(check_string($_POST['ticket_id']));
    $message = trim(check_string($_POST['message']));
    
    // Validate
    if(empty($message)){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập nội dung tin nhắn')]));
    }
    
    if(strlen($message) > 5000){
        die(json_encode(['status' => 'error', 'msg' => __('Nội dung tin nhắn quá dài (tối đa 5000 ký tự)')]));
    }
    
    // Kiểm tra ticket có tồn tại không
    if(!$ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$ticket_id'")){
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    
    // Kiểm tra ticket có bị đóng không
    if($ticket['status'] == 'closed'){
        die(json_encode(['status' => 'error', 'msg' => __('Không thể trả lời ticket đã đóng')]));
    }
    
    // Thêm tin nhắn mới
    $isInsert = $CMSNT->insert('support_messages', [
        'ticket_id' => $ticket_id,
        'sender_id' => $getUser['id'],
        'sender_type' => 'admin',
        'message' => $message
    ]);
    
    if($isInsert){
        // Cập nhật trạng thái ticket thành "answered"
        $CMSNT->update('support_tickets', [
            'status' => 'answered'
        ], "`id` = '$ticket_id'");
        
        // Ghi log hành động

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Trả lời ticket')." #$ticket_id"
        ]);
        
        // GỬI THÔNG BÁO CHO USER KHI ADMIN REPLY TICKET
        $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '{$ticket['user_id']}'");
        if($user['telegram_chat_id'] != '' && $CMSNT->site('noti_user_admin_reply_ticket') != '' && $user['telegram_notification'] == 1){
            $content = $CMSNT->site('noti_user_admin_reply_ticket');
            $content = str_replace('{username}', $user['username'], $content);
            $content = str_replace('{subject}', $ticket['subject'], $content);
            $content = str_replace('{message}', $message, $content);
            $content = str_replace('{time}', gettime(), $content);
            sendMessTelegram($content, '', $user['telegram_chat_id']);
        }

        die(json_encode([
            'status' => 'success', 
            'msg' => __('Gửi tin nhắn thành công'),
            'admin_email' => $getUser['email'],
            'admin_username' => $getUser['username']
        ]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Có lỗi xảy ra, vui lòng thử lại')]));
    }
}

// Load tin nhắn mới cho admin
if($_POST['action'] == 'get_new_messages_admin') {
    $ticket_id = intval(check_string($_POST['ticket_id']));
    $last_message_id = intval(check_string($_POST['last_message_id']));
    
    // Kiểm tra ticket có tồn tại không
    if(!$ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$ticket_id'")){
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    
    // Lấy tin nhắn mới
    $messages = $CMSNT->get_list("
        SELECT sm.*, u.username 
        FROM `support_messages` sm 
        LEFT JOIN `users` u ON (sm.sender_id = u.id AND sm.sender_type = 'user') 
        WHERE sm.ticket_id = '$ticket_id' AND sm.id > '$last_message_id' 
        ORDER BY sm.created_at ASC
    ");
    
    if(!empty($messages)){
        $formatted_messages = [];
        foreach($messages as $msg){
            $formatted_messages[] = [
                'id' => $msg['id'],
                'sender_type' => $msg['sender_type'],
                'message' => nl2br(htmlspecialchars($msg['message'])),
                'username' => $msg['username'] ?: __('User'),
                'created_at' => $msg['created_at'],
                'formatted_time' => date('H:i d/m/Y', strtotime($msg['created_at'])),
                'time_ago' => timeAgo(strtotime($msg['created_at']))
            ];
        }
        
        die(json_encode([
            'status' => 'success', 
            'messages' => $formatted_messages
        ]));
    } else {
        die(json_encode(['status' => 'success', 'messages' => []]));
    }
}

// Xóa nhiều ticket cùng lúc
if($_POST['action'] == 'bulkDeleteTickets') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_ticket') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    if(empty($_POST['ids']) || !is_array($_POST['ids'])){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một ticket để xóa')]));
    }

    $ids = array_map('intval', $_POST['ids']); // Chuyển đổi sang kiểu int để bảo mật
    $validIds = [];
    $deletedSubjects = [];

    // Kiểm tra từng ticket có tồn tại không
    foreach($ids as $id){
        $id = check_string($id);
        if($ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$id'")){
            $validIds[] = $id;
            $deletedSubjects[] = "#{$id} ({$ticket['subject']})";
        }
    }

    if(empty($validIds)){
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy ticket nào để xóa')]));
    }

    $deletedCount = 0;
    foreach($validIds as $id){
        // Xóa tất cả tin nhắn của ticket này trước
        $CMSNT->remove('support_messages', "`ticket_id` = '$id'");
        
        // Xóa ticket
        if($CMSNT->remove('support_tickets', "`id` = '$id'")){
            $deletedCount++;
        }
    }

    if($deletedCount > 0){
        // Ghi log hành động

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Xóa %d ticket hàng loạt: %s'), $deletedCount, implode(', ', array_slice($deletedSubjects, 0, 5)) . (count($deletedSubjects) > 5 ? '...' : ''))
        ]);

        die(json_encode(['status' => 'success', 'msg' => sprintf(__('Đã xóa thành công %d ticket'), $deletedCount)]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Không thể xóa ticket nào')]));
    }
}

// Thay đổi trạng thái nhiều ticket cùng lúc
if($_POST['action'] == 'bulkChangeStatus') {
    // Kiểm tra quyền
    if(checkPermission($getUser['admin'], 'edit_ticket') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    if(empty($_POST['ids']) || !is_array($_POST['ids'])){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một ticket')]));
    }

    $ids = array_map('intval', $_POST['ids']); // Chuyển đổi sang kiểu int để bảo mật
    $status = check_string($_POST['status']);
    
    if(!in_array($status, array_keys($config_status_support_tickets))){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }

    $validIds = [];
    $updatedSubjects = [];

    // Kiểm tra từng ticket có tồn tại không
    foreach($ids as $id){
        if($ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$id'")){
            $validIds[] = $id;
            $updatedSubjects[] = "#{$id} ({$ticket['subject']})";
        }
    }

    if(empty($validIds)){
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy ticket nào để cập nhật')]));
    }

    $updatedCount = 0;
    foreach($validIds as $id){
        // Cập nhật trạng thái
        if($CMSNT->update('support_tickets', [
            'status' => $status
        ], "`id` = '$id'")){
            $updatedCount++;
        }
    }

    if($updatedCount > 0){
        // Ghi log hành động

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Thay đổi trạng thái %d ticket hàng loạt thành %s: %s'), $updatedCount, $config_status_support_tickets[$status], implode(', ', array_slice($updatedSubjects, 0, 5)) . (count($updatedSubjects) > 5 ? '...' : ''))
        ]);

        die(json_encode(['status' => 'success', 'msg' => sprintf(__('Đã cập nhật trạng thái thành công cho %d ticket'), $updatedCount)]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Không thể cập nhật trạng thái cho ticket nào')]));
    }
}

die(json_encode(['status' => 'error', 'msg' => __('Hành động không hợp lệ')]));
 