<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
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


if($_POST['action'] == 'replyTicket'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (empty($_POST['ticket_id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    if (empty($_POST['message'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập tin nhắn')]));
    }
    if($CMSNT->site('support_tickets_status') == 0){
        die(json_encode(['status' => 'error', 'msg' => __('Chức năng này đã bị tắt bởi quản trị viên')]));
    }
    $ticket_id = check_string($_POST['ticket_id']);
    $message = check_string($_POST['message']);
    $isTicket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '".$ticket_id."' AND `user_id` = '".$getUser['id']."' ");
    if(!$isTicket){
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    $isInsert = $CMSNT->insert('support_messages', [
        'ticket_id'     => $ticket_id,
        'sender_type'   => 'user',
        'sender_id'     => $getUser['id'],
        'message'       => $message
    ]);
    if($isInsert){
        // Cập nhật trạng thái ticket thành "open"
        $CMSNT->update('support_tickets', [
            'status' => 'open'
        ], "`id` = '$ticket_id'");

        // Nếu trạng thái cũ là đã trả lời hoặc đóng thì báo Admin
        if($isTicket['status'] != 'open'){

            // THÔNG BÁO VỀ TELEGRAM CHO ADMIN
            if($CMSNT->site('support_tickets_telegram_message_reply') != '' && $CMSNT->site('support_tickets_telegram_chat_id') != ''){
                $my_text = $CMSNT->site('support_tickets_telegram_message_reply');
                $my_text = str_replace('{username}', $getUser['username'], $my_text);
                $my_text = str_replace('{ip}', myip(), $my_text);
                $my_text = str_replace('{device}', getUserAgent(), $my_text);
                $my_text = str_replace('{time}', gettime(), $my_text);
                $my_text = str_replace('{subject}', $isTicket['subject'], $my_text);
                $my_text = str_replace('{message}', $message, $my_text);
                $my_text = str_replace('{category}', $config_category_support_tickets[$isTicket['category']] ?? $isTicket['category'], $my_text);
                sendMessTelegram($my_text, '', $CMSNT->site('support_tickets_telegram_chat_id'));
            }

            // THÔNG BÁO VỀ MAIL ADMIN KHI USER TRẢ LỜI TICKET
            // if($CMSNT->site('email_temp_subject_reply_ticket') != ''){
            //     // Chuẩn bị dữ liệu thay thế
            //     $replace_data = [
            //         '{domain}'      => check_string($_SERVER['SERVER_NAME']),
            //         '{title}'       => $CMSNT->site('title'),
            //         '{username}'    => $getUser['username'],
            //         '{ip}'          => myip(),
            //         '{device}'      => getUserAgent(),
            //         '{time}'        => gettime(),
            //         '{subject}'     => $isTicket['subject'],
            //         '{category}'    => $config_category_support_tickets[$isTicket['category']] ?? $isTicket['category'],
            //         '{order_id}'    => $isTicket['order_id'] ?: __('Không có'),
            //         '{content}'     => $message
            //     ];
            //     // Template email subject
            //     $email_subject = $CMSNT->site('email_temp_subject_reply_ticket');
            //     $email_subject = str_replace(array_keys($replace_data), array_values($replace_data), $email_subject);
            //     // Template email content
            //     $email_content = $CMSNT->site('email_temp_content_reply_ticket');
            //     $email_content = str_replace(array_keys($replace_data), array_values($replace_data), $email_content);
            //     $bcc = $CMSNT->site('title');
            //     sendCSM($CMSNT->site('email'), $CMSNT->site('email'), $email_subject, $email_content, $bcc);
            // }
        }

        die(json_encode(['status' => 'success', 'msg' => __('Tin nhắn đã được gửi thành công')]));
    }else{
        die(json_encode(['status' => 'error', 'msg' => __('Lỗi khi gửi tin nhắn')]));
    }
}

if($_POST['action'] == 'createTicket'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    if (empty($_POST['subject'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập tiêu đề ticket')]));
    }
    if (empty($_POST['category'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn chủ đề hỗ trợ')]));
    }
    if (empty($_POST['content'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập nội dung ticket')]));
    } 
    if($CMSNT->site('support_tickets_status') == 0){ 
        die(json_encode(['status' => 'error', 'msg' => __('Chức năng này đã bị tắt bởi quản trị viên')]));
    }
    $subject = check_string($_POST['subject']);
    $category = check_string($_POST['category']);
    $order_id = isset($_POST['order_id']) ? check_string($_POST['order_id']) : NULL;
    $content = check_string($_POST['content']);

    if(!empty($order_id) && $category == 'order'){
        $isOrder = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '".$order_id."' AND `user_id` = '".$getUser['id']."' ");
        if(!$isOrder){
            die(json_encode(['status' => 'error', 'msg' => __('Mã đơn hàng không tồn tại')]));
        }
        // Lấy mã đơn hàng nếu có
        $order_id = $isOrder['id'];
        // Nếu mã đơn hàng này đã tạo ticket trước đó rồi thì không cho tạo nữa.
        if($CMSNT->get_row("SELECT COUNT(id) FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `order_id` = '".$order_id."' ")['COUNT(id)'] > 0){
            die(json_encode(['status' => 'error', 'msg' => __('Mã đơn hàng này đã tạo ticket trước đó rồi, không thể tạo thêm.')]));
        }
    }

    if($CMSNT->get_row("SELECT COUNT(id) FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `status` = 'open' ")['COUNT(id)'] >= 5){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn đã có 5 yêu cầu hỗ trợ đang mở, vui lòng đợi xử lý')]));
    }

    $isInsert = $CMSNT->insert('support_tickets', [
        'user_id'           => $getUser['id'],
        'order_id'          => $order_id,
        'category'          => $category,
        'subject'           => $subject,
        'content'           => $content,
        'status'            => 'open',
        'created_at'        => gettime()
    ]);
    if($isInsert){
        // // THÔNG BÁO VỀ MAIL ADMIN KHI USER TẠO TICKET
        // if($CMSNT->site('email_temp_subject_warning_ticket') != ''){
        
        //     $replace_data = [
        //         '{domain}'      => check_string($_SERVER['SERVER_NAME']),
        //         '{title}'       => $CMSNT->site('title'),
        //         '{username}'    => $getUser['username'],
        //         '{ip}'          => myip(),
        //         '{device}'      => getUserAgent(),
        //         '{time}'        => gettime(),
        //         '{subject}'     => $subject,
        //         '{category}'    => $config_category_support_tickets[$category] ?? $category,
        //         '{order_id}'    => $order_id,
        //         '{content}'     => $content
        //     ];
        //     // Template email subject
        //     $email_subject = $CMSNT->site('email_temp_subject_warning_ticket');
        //     $email_subject = str_replace(array_keys($replace_data), array_values($replace_data), $email_subject);
        //     // Template email content
        //     $email_content = $CMSNT->site('email_temp_content_warning_ticket');
        //     $email_content = str_replace(array_keys($replace_data), array_values($replace_data), $email_content);
        //     $bcc = $CMSNT->site('title');
        //     sendCSM($CMSNT->site('email'), $CMSNT->site('email'), $email_subject, $email_content, $bcc);
        // }

        // THÔNG BÁO VỀ TELEGRAM CHO ADMIN
        if($CMSNT->site('support_tickets_telegram_message') != '' && $CMSNT->site('support_tickets_telegram_chat_id') != ''){
        
            $my_text = $CMSNT->site('support_tickets_telegram_message');
            $my_text = str_replace('{username}', $getUser['username'], $my_text);
            $my_text = str_replace('{ip}', myip(), $my_text);
            $my_text = str_replace('{device}', getUserAgent(), $my_text);
            $my_text = str_replace('{time}', gettime(), $my_text);
            $my_text = str_replace('{subject}', $subject, $my_text);
            $my_text = str_replace('{content}', $content, $my_text);
            $my_text = str_replace('{status}', $config_status_support_tickets[$isTicket['status']] ?? $isTicket['status'], $my_text); 
            $my_text = str_replace('{category}', $config_category_support_tickets[$category] ?? $category, $my_text);
            sendMessTelegram($my_text, '', $CMSNT->site('support_tickets_telegram_chat_id'));
        }


        // Ghi log hành động
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Tạo yêu cầu hỗ trợ')
        ]);
    
        die(json_encode(['status' => 'success', 'msg' => __('Yêu cầu hỗ trợ đã được tạo thành công')]));
    }else{
        die(json_encode(['status' => 'error', 'msg' => __('Lỗi khi tạo yêu cầu hỗ trợ')]));
    }
}


if($_POST['action'] == 'getNewMessages'){
    $ticket_id = intval(check_string($_POST['ticket_id']));
    $last_message_id = intval(check_string($_POST['last_message_id']));
    $token = check_string($_POST['token']);
    if (empty($token)) {
        die(json_encode(['status' => 'error', 'msg' => __('Token không hợp lệ')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập')]));
    }
    
    // Validate ticket belongs to user
    $ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$ticket_id' AND `user_id` = '".$getUser['id']."'");
    if (!$ticket) {
        die(json_encode(['status' => 'error', 'msg' => __('Ticket không tồn tại')]));
    }
    
    // Get new messages after last_message_id
    $where_condition = "sm.ticket_id = '$ticket_id'";
    if ($last_message_id > 0) {
        $where_condition .= " AND sm.id > '$last_message_id'";
    }
    
    // Lấy danh sách tin nhắn mới từ database
    $messages = $CMSNT->get_list("
        SELECT sm.*, u.username 
        FROM `support_messages` sm 
        LEFT JOIN `users` u ON (sm.sender_id = u.id AND sm.sender_type = 'user') 
        WHERE $where_condition 
        ORDER BY sm.created_at ASC
    ");
    
    /* 
     * Giải thích query:
     * - SELECT sm.*, u.username: Lấy tất cả trường từ bảng support_messages và thêm username từ bảng users
     * - FROM support_messages sm: Bảng chính chứa tin nhắn support
     * - LEFT JOIN users u: Kết nối với bảng users để lấy tên user (chỉ khi sender_type = 'user')
     * - ON (sm.sender_id = u.id AND sm.sender_type = 'user'): Điều kiện join - chỉ join khi tin nhắn từ user
     * - WHERE $where_condition: Điều kiện lọc (ticket_id và message_id > last_message_id)
     * - ORDER BY sm.created_at ASC: Sắp xếp theo thời gian tạo từ cũ đến mới
     */
    
    if (empty($messages)) {
        die(json_encode(['status' => 'success', 'messages' => []]));
    }
    
    // Format messages
    $formatted_messages = [];
    foreach ($messages as $msg) {
        $formatted_msg = [
            'id' => $msg['id'],
            'sender_type' => $msg['sender_type'],
            'message' => nl2br(htmlspecialchars($msg['message'])),
            'formatted_time' => date('H:i d/m/Y', strtotime($msg['created_at'])),
            'time_ago' => timeAgo(strtotime($msg['created_at'])),
            'created_at' => $msg['created_at']
        ];
        
        if ($msg['sender_type'] == 'user') {
            $formatted_msg['username'] = $msg['username'] ?: __('User');
        }
        
        $formatted_messages[] = $formatted_msg;
    }
    
    die(json_encode([
        'status' => 'success', 
        'messages' => $formatted_messages,
        'count' => count($formatted_messages)
    ]));
}


die(json_encode([
    'status'    => 'error',
    'msg'       => __('Invalid data')
]));