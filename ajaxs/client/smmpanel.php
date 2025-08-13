<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__."/../../libs/sendEmail.php");
require_once(__DIR__.'/../../libs/database/users.php');
$Mobile_Detect = new Mobile_Detect();

if ($CMSNT->site('status') != 1) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Hệ thống đang bảo trì!')
    ]);
    die($data);
}
if(!isset($_REQUEST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}

// API tạo đơn hàng
if($_REQUEST['action'] == 'add'){
    if ($CMSNT->site('status_demo') == 1) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('Không thể sử dụng chức năng này vì đây là trang web demo')
        ]);
        die($data);
    }
    if (empty($_REQUEST['key'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    //
    if(empty($_REQUEST['service'])){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn dịch vụ'), 'error' => __('error.incorrect_service_id')]));
    }
    $service_id = intval(check_string($_REQUEST['service']));
    if(!$service = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$service_id' AND `display` = 'show' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Dịch vụ không tồn tại'), 'error' => __('error.incorrect_service_id')]));
    }
    //
    if(empty($_REQUEST['link'])){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập liên kết'), 'error' => __('neworder.error.link')]));
    }
    $link = check_string($_REQUEST['link']);
    //
    // Nếu dịch vụ là Custom Comments Package thì số lượng luôn là 1
    if($service['type'] == 'Custom Comments Package'){
        $quantity = 1;
    }else{
        if(empty($_REQUEST['quantity'])){
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập số lượng'), 'error' => __('neworder.error.min_quantity')]));
        }
        $quantity = intval(check_string($_REQUEST['quantity']));
    }
    if($quantity < $service['min']){
        die(json_encode(['status' => 'error', 'msg' => __('Số lượng tối thiểu là').' '.$service['min'], 'error' => __('neworder.error.min_quantity')]));
    }
    if($quantity > $service['max']){
        die(json_encode(['status' => 'error', 'msg' => __('Số lượng tối đa là').' '.$service['max'], 'error' => __('neworder.error.max_quantity')]));
    }
    //
    if(empty($_REQUEST['comments']) && $service['type'] == 'Custom Comments'){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập comment'), 'error' => __('neworder.error.comment')]));
    }
    $comment = !empty($_REQUEST['comments']) ? check_string($_REQUEST['comments']) : NULL;
    //
    if($service['type'] == 'Custom Comments'){
        // Lọc các dòng trống trước khi đếm
        $lines = explode("\n", $comment);
        $nonEmptyLines = array_filter($lines, function($line) {
            return trim($line) !== '';
        });
        $quantity = count($nonEmptyLines);
    }
    
    // Trước mắt tính giá chưa discount
    $pay = $service['price'] * $quantity;
    $cost = $service['cost'] * $quantity;
    if($getUser['rank_id'] > 0){
        $pay = $quantity * $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
    }
    if($getUser['discount'] > 0){
        $pay = $pay * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
    }
    // Tính thuế VAT nếu có
    if(floatval($CMSNT->site('tax_vat')) > 0){
        $price_vat      = floatval($CMSNT->site('tax_vat')) > 0 ? $pay * floatval($CMSNT->site('tax_vat')) / 100 : 0; // Số tiền thuế VAT cần trả thêm
        $pay            = $pay + $price_vat; // Số tiền thanh toán sau khi tính thuế VAT
    }
    //
    if(getRowRealtime('users', $getUser['id'], 'money') < floatval($pay)){
        die(json_encode(['status' => 'error', 'msg' => __('Số dư không đủ, vui lòng nạp thêm'), 'error' => __('neworder.error.insufficient_balance')]));
    }
    // Xác định đơn hàng mua từ đâu
    if(isset($_REQUEST['order_source'])){
        $order_source = check_string($_REQUEST['order_source']);
    }else{
        $order_source = 'web';
    }
    //
    // Xử lý đơn hàng Đặt lịch
    if(isset($_REQUEST['schedule']) && $_REQUEST['schedule'] == 1){
        if($CMSNT->site('status_scheduled_orders') != 1){
            die(json_encode(['status' => 'error', 'msg' => __('Hệ thống đang tạm ngưng đặt lịch đơn hàng'), 'error' => __('Hệ thống đang tạm ngưng đặt lịch đơn hàng')]));
        }
        if(empty($_REQUEST['schedule_time'])){
            die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn thời gian đặt lịch đơn hàng'), 'error' => __('neworder.error.schedule_time')]));
        }
        // Kiểm tra thời gian đặt lịch có hợp lệ hay không
        $schedule_time_input = check_string($_REQUEST['schedule_time']);
        
        // Sử dụng DateTime::createFromFormat để parse đúng format Y-m-d H:i từ flatpickr
        $schedule_datetime = DateTime::createFromFormat('Y-m-d H:i', $schedule_time_input, new DateTimeZone($CMSNT->site('timezone')));
        
        if (!$schedule_datetime) {
            die(json_encode(['status' => 'error', 'msg' => __('Định dạng thời gian không hợp lệ'), 'error' => __('neworder.error.schedule_time')]));
        }
        
        // Kiểm tra thời gian phải sau hiện tại ít nhất 5 phút
        $now = new DateTime('now', new DateTimeZone($CMSNT->site('timezone')));
        $now->add(new DateInterval('PT5M')); // Thêm 5 phút
        
        if($schedule_datetime <= $now){
            die(json_encode(['status' => 'error', 'msg' => __('Thời gian đặt lịch chạy đơn hàng phải sau thời gian hiện tại ít nhất 5 phút'), 'error' => __('neworder.error.schedule_time')]));
        }
        
        // Convert sang mặc định của hệ thống để lưu database
        $schedule_datetime->setTimezone(new DateTimeZone($CMSNT->site('timezone')));
        $schedule_time = $schedule_datetime->format('Y-m-d H:i:s');

        // Nếu có đặt lịch thì lưu vào bảng scheduled_orders
        if($schedule_time !== NULL) {
            // Lưu thông tin đơn hàng đặt lịch
            $isInsert = $CMSNT->insert("scheduled_orders", [
                'user_id'       => $getUser['id'],
                'service_id'    => $service_id,
                'link'          => $link,
                'quantity'      => $quantity,
                'comment'       => $comment,
                'schedule_time' => $schedule_time,
                'status'        => 'pending',
                'order_source'  => $order_source
            ]);
            if($isInsert){
                die(json_encode(['status' => 'success', 'msg' => __('Đơn hàng đã được lên lịch thành công, vui lòng đảm bảo số dư luôn đủ để chạy đơn hàng')]));
            }else{
                die(json_encode(['status' => 'error', 'msg' => __('Đơn hàng đặt lịch thất bại'), 'error' => __('Đơn hàng đặt lịch thất bại')]));
            }
        }
        die(json_encode(['status' => 'error', 'msg' => __('Thời gian đặt lịch không hợp lệ'), 'error' => __('Thời gian đặt lịch không hợp lệ')]));
    }

    // Xử lý đơn hàng thông thường (không có đặt lịch)
    $trans_id = generateOrderTransactionId();
    //
    $User = new users();
    $isTru = $User->RemoveCredits($getUser['id'], $pay, __('Thanh toán đơn hàng').' <b>'.$service['name'].'</b> - #'.$trans_id, 'ORDER_'.$trans_id);
    if($isTru){
        if (getRowRealtime("users", $getUser['id'], "money") < -500) {
            $User->Banned($getUser['id'], __('Gian lận khi mua hàng'));
            die(json_encode(['status' => 'error', 'msg' => __('Bạn đã bị khoá tài khoản vì gian lận'), 'error' => __('neworder.error.account_banned')]));
        }
        // Đơn thủ công
        if($service['supplier_id'] == 0){
            $isInsert = $CMSNT->insert("orders", [
                'user_id'       => $getUser['id'],
                'supplier_id'   => 0,
                'service_id'    => $service_id,
                'service_name'  => $service['name'],
                'link'          => $link,
                'quantity'      => $quantity,
                'created_at'    => gettime(),
                'updated_at'    => gettime(),
                'status'        => 'Pending',
                'price'         => $pay,
                'cost'          => $cost,
                'pay'           => $pay,
                'trans_id'      => $trans_id,
                'comment'       => $comment,
                'order_source'  => $order_source,
                'refill'        => isset($service['refill']) ? (int)$service['refill'] : 0,
                'cancel'        => isset($service['cancel']) ? (int)$service['cancel'] : 0
            ]);
            if($isInsert){
                if($CMSNT->site('noti_buy_service_manual') != ''){
                    /** SEND NOTI CHO ADMIN */
                    $my_text = $CMSNT->site('noti_buy_service_manual');
                    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                    $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                    $my_text = str_replace('{username}', $getUser['username'], $my_text);
                    $my_text = str_replace('{service}', $service['name'], $my_text);
                    $my_text = str_replace('{link}', $link, $my_text);
                    $my_text = str_replace('{quantity}', $quantity, $my_text);
                    $my_text = str_replace('{comment}', $comment, $my_text);
                    $my_text = str_replace('{trans_id}', $trans_id, $my_text);
                    $my_text = str_replace('{pay}', format_currency($pay), $my_text);
                    $my_text = str_replace('{ip}', myip(), $my_text);
                    $my_text = str_replace('{time}', gettime(), $my_text);
                    sendMessAdmin($my_text);
                }
                // GỬI THÔNG BÁO VỀ TELEGRAM CHO USER
                if($CMSNT->site('noti_buy_service_to_user') != '' && $getUser['telegram_id'] != '' && $getUser['telegram_notification'] == 1){
                    $my_text = $CMSNT->site('noti_buy_service_to_user');
                    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                    $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                    $my_text = str_replace('{username}', $getUser['username'], $my_text);
                    $my_text = str_replace('{service}', $service['name'], $my_text);
                    $my_text = str_replace('{link}', $link, $my_text);
                    $my_text = str_replace('{quantity}', $quantity, $my_text);
                    $my_text = str_replace('{comment}', $comment, $my_text);
                    $my_text = str_replace('{trans_id}', $trans_id, $my_text);
                    $my_text = str_replace('{pay}', format_currency($pay), $my_text);
                    $my_text = str_replace('{ip}', myip(), $my_text);
                    $my_text = str_replace('{time}', gettime(), $my_text);
                    sendMessTelegram($my_text, '', $getUser['telegram_id']);
                }
                die(json_encode(['status' => 'success', 'msg' => __('Đơn hàng đã được tạo thành công'), 'order' => $trans_id]));
            }else{
                $User->RefundCredits($getUser['id'], $pay, __('[Error 1] Hoàn tiền đơn hàng').' #'.$trans_id, 'REFUND_'.$trans_id);
                die(json_encode(['status' => 'error', 'msg' => __('Đơn hàng không được tạo thành công'), 'error' => __('Đơn hàng không được tạo thành công')]));
            }
        }else{
            // Đơn có nhà cung cấp
            if(!$supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '".$service['supplier_id']."' ")){
                die(json_encode(['status' => 'error', 'msg' => __('Nhà cung cấp không tồn tại'), 'error' => __('Nhà cung cấp không tồn tại')]));
            }
            if($supplier['status'] != 1){
                die(json_encode(['status' => 'error', 'msg' => __('Dịch vụ tạm bảo trì'), 'error' => __('Dịch vụ tạm bảo trì')]));
            }
            // API SMMPANEL2
            if($supplier['type'] == 'SMMPANEL2'){
                require_once(__DIR__.'/../../libs/suppliers/smmpanel2.php');
                $result = buy_service_smmpanel2($supplier['domain'], $supplier['api_key'], $service['api_id'], $quantity, $link, $comment, $supplier['proxy']);
                $result = json_decode($result, true);
                if(!isset($result['order'])){
                    $User->RefundCredits($getUser['id'], $pay, __('[Error 2] Hoàn tiền đơn hàng').' #'.$trans_id, 'REFUND_'.$trans_id);
                
                    die(json_encode(['status' => 'error', 'msg' => check_string($result['error']), 'error' => check_string($result['error'])]));
                }
                $order_id = $result['order'];
            }
            // API khác


            $isInsert = $CMSNT->insert("orders", [
                'user_id'       => $getUser['id'],
                'supplier_id'   => $supplier['id'],
                'service_id'    => $service_id,
                'service_name'  => $service['name'],
                'link'          => $link,
                'quantity'      => $quantity,
                'created_at'    => gettime(),
                'updated_at'    => gettime(),
                'status'        => 'Pending',
                'price'         => $pay,
                'cost'          => $cost,
                'pay'           => $pay,
                'trans_id'      => $trans_id,
                'comment'       => $comment,
                'order_id'      => $order_id,
                'order_source'  => $order_source,
                'refill'        => isset($service['refill']) ? (int)$service['refill'] : 0,
                'cancel'        => isset($service['cancel']) ? (int)$service['cancel'] : 0
            ]);
            if($isInsert){
                if($CMSNT->site('noti_buy_service_api') != ''){
                    /** SEND NOTI CHO ADMIN */
                    $my_text = $CMSNT->site('noti_buy_service_api');
                    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                    $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                    $my_text = str_replace('{username}', $getUser['username'], $my_text);
                    $my_text = str_replace('{service}', $service['name'], $my_text);
                    $my_text = str_replace('{supplier}', $supplier['domain'], $my_text);
                    $my_text = str_replace('{link}', $link, $my_text);
                    $my_text = str_replace('{quantity}', $quantity, $my_text);
                    $my_text = str_replace('{comment}', $comment, $my_text);
                    $my_text = str_replace('{trans_id}', $trans_id, $my_text);
                    $my_text = str_replace('{pay}', format_currency($pay), $my_text);
                    $my_text = str_replace('{ip}', myip(), $my_text);
                    $my_text = str_replace('{time}', gettime(), $my_text);
                    sendMessAdmin($my_text);
                }
                // GỬI THÔNG BÁO VỀ TELEGRAM CHO USER
                if($CMSNT->site('noti_buy_service_to_user') != '' && $getUser['telegram_id'] != '' && $getUser['telegram_notification'] == 1){
                    $my_text = $CMSNT->site('noti_buy_service_to_user');
                    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                    $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                    $my_text = str_replace('{username}', $getUser['username'], $my_text);
                    $my_text = str_replace('{service}', $service['name'], $my_text);
                    $my_text = str_replace('{link}', $link, $my_text);
                    $my_text = str_replace('{quantity}', $quantity, $my_text);
                    $my_text = str_replace('{comment}', $comment, $my_text);
                    $my_text = str_replace('{trans_id}', $trans_id, $my_text);
                    $my_text = str_replace('{pay}', format_currency($pay), $my_text);
                    $my_text = str_replace('{ip}', myip(), $my_text);
                    $my_text = str_replace('{time}', gettime(), $my_text);
                    sendMessTelegram($my_text, '', $getUser['telegram_id']);
                }
                die(json_encode(['status' => 'success', 'msg' => __('Đơn hàng đã được tạo thành công'), 'order' => $trans_id]));
            }else{
                die(json_encode([
                    'status' => 'error', 
                    'msg' => __('Đơn hàng không được tạo thành công vui lòng liên hệ Admin'), 
                    'error' => __('Đơn hàng không được tạo thành công vui lòng liên hệ Admin')])
                );
            }
        }
    }
} 





// API Xử lý hủy đơn hàng
if($_REQUEST['action'] == 'cancel'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    //
    if(empty($_REQUEST['orders'])){
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mã đơn hàng'), 'error' => __('error.incorrect_order_id')]));
    }
    $orders = explode(',', check_string($_REQUEST['orders']));
    // Giới hạn tối đa 100 ID
    $orders = array_slice($orders, 0, 100);
    $response = [];
    foreach ($orders as $order_id) {
        $order_id = trim($order_id);
        if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Incorrect order ID')
                ]
            ];
            continue;
        }
        if($order['status'] == 'Completed' || $order['status'] == 'Canceled' || $order['status'] == 'Partial'){
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Đơn hàng này đã hoàn thành hoặc đã bị hủy')
                ]
            ];
            continue;
        }
        if($order['cancel'] != 1){
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Dịch vụ này không hỗ trợ hủy đơn')
                ]
            ];
            continue;
        }
        if($order['cancel_status'] == 2 || $order['cancel_status'] == 3){
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Đơn hàng này đang trong quá trình hủy')
                ]
            ];
            continue;
        }
        if($order['cancel_status'] == 1){
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Đơn hàng này đã được hủy trước đó rồi')
                ]
            ];
            continue;
        }
        // Xử lý đơn thủ công
        $isUpdate = $CMSNT->update("orders", [
            'cancel_status' => 2, // 2 = Đang chờ hủy
            'updated_at' => gettime()
        ], " `id` = '".$order['id']."' ");
        
        if($isUpdate){
            // GHI LOG NHẬT KÝ HOẠT ĐỘNG 
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => __('Hủy đơn hàng').' #'.$order['trans_id']
            ]);
            $response[] = [
                'order' => $order_id,
                'cancel' => 1
            ];
        } else {
            $response[] = [
                'order' => $order_id,
                'cancel' => [
                    'error' => __('Yêu cầu hủy đơn thất bại')
                ]
            ];
        }
    }
    // Trả về kết quả theo format mới
    die(json_encode($response));
}





// API Xử lý bảo hành đơn hàng
if($_REQUEST['action'] == 'refill'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập'), 'error' => __('Invalid API key')]));
    }
    
    // Xử lý nhiều đơn hàng
    if(!empty($_REQUEST['orders'])){
        $orders = explode(',', check_string($_REQUEST['orders']));
        // Giới hạn tối đa 100 ID
        $orders = array_slice($orders, 0, 100);
        $response = [];
        
        foreach ($orders as $order_id) {
            $order_id = trim($order_id);
            if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Incorrect order ID')
                    ]
                ];
                continue;
            }
            if($order['status'] != 'Completed'){
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Đơn hàng phải ở trạng thái hoàn thành mới có thể bảo hành')
                    ]
                ];
                continue;
            }
            // Kiểm tra thời gian hoàn thành đơn hàng phải đủ 24 giờ
            $order_completed_time = strtotime($order['updated_at']);
            $current_time = time();
            $time_diff = $current_time - $order_completed_time;
            $hours_diff = $time_diff / 3600; // Chuyển đổi giây sang giờ
            
            if($hours_diff < 24){
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Đơn hàng phải hoàn thành đủ 24 giờ mới có thể bảo hành')
                    ]
                ];
                continue;
            }
            if($order['refill'] != 1){
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Dịch vụ này không hỗ trợ bảo hành')
                    ]
                ];
                continue;
            }
            if($order['refill_status'] != 0){
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Đơn hàng này đã được yêu cầu bảo hành trước đó')
                    ]
                ];
                continue;
            }
            
            // Xử lý bảo hành
            $isUpdate = $CMSNT->update("orders", [
                'refill_status' => 2, // 2 = Đang chờ bảo hành
                'updated_at' => gettime()
            ], " `id` = '".$order['id']."' ");
            
            if($isUpdate){
                $response[] = [
                    'order' => $order_id,
                    'refill' => $order_id
                ];
            } else {
                $response[] = [
                    'order' => $order_id,
                    'refill' => [
                        'error' => __('Yêu cầu bảo hành thất bại')
                    ]
                ];
            }
        }
        // Trả về kết quả theo format mới
        die(json_encode($response));
    }
    // Xử lý một đơn hàng
    else if(!empty($_REQUEST['order'])){
        $order_id = trim(check_string($_REQUEST['order']));
        
        if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
            die(json_encode(['refill' => ['error' => __('Incorrect order ID')]]));
        }
        if($order['status'] != 'Completed'){
            die(json_encode(['refill' => ['error' => __('Đơn hàng phải ở trạng thái hoàn thành mới có thể bảo hành')]]));
        }
        // Kiểm tra thời gian hoàn thành đơn hàng phải đủ 24 giờ
        $order_completed_time = strtotime($order['updated_at']);
        $current_time = time();
        $time_diff = $current_time - $order_completed_time;
        $hours_diff = $time_diff / 3600; // Chuyển đổi giây sang giờ
        
        if($hours_diff < 24){
            die(json_encode(['refill' => ['error' => __('Đơn hàng phải hoàn thành đủ 24 giờ mới có thể bảo hành')]]));
        }
        if($order['refill'] != 1){
            die(json_encode(['refill' => ['error' => __('Dịch vụ này không hỗ trợ bảo hành')]]));
        }
        if($order['refill_status'] != 0){
            die(json_encode(['refill' => ['error' => __('Đơn hàng này đã được yêu cầu bảo hành trước đó')]]));
        }
        
        // Xử lý bảo hành
        $isUpdate = $CMSNT->update("orders", [
            'refill_status' => 2, // 2 = Đang chờ bảo hành
            'updated_at' => gettime()
        ], " `id` = '".$order['id']."' ");
        
        if($isUpdate){
            die(json_encode(['refill' => $order['trans_id']]));
        } else {
            die(json_encode(['refill' => ['error' => __('Yêu cầu bảo hành thất bại')]]));
        }
    }
    else {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập mã đơn hàng'), 'error' => __('error.incorrect_order_id')]));
    }
}


// API Xử lý bảo hành đơn hàng
if($_REQUEST['action'] == 'refill_status'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['error' => __('Invalid API key')]));
    }
    
    // Xử lý nhiều đơn hàng
    if(!empty($_REQUEST['refills'])){
        $orders = explode(',', check_string($_REQUEST['refills']));
        // Giới hạn tối đa 100 ID
        $orders = array_slice($orders, 0, 100);
        $response = [];
        
        foreach ($orders as $order_id) {
            $order_id = trim($order_id);
            if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
                $response[] = [
                    'refill' => $order_id,
                    'status' => [
                        'error' => __('Incorrect order ID')
                    ]
                ];
                continue;
            }
            if($order['refill_status'] == 1){
                $response[] = [
                    'refill' => $order_id,
                    'status' => 'Completed'
                ];
                continue;
            }
            if($order['refill_status'] == 2){
                $response[] = [
                    'refill' => $order_id,
                    'status' => 'Pending'
                ];
                continue;
            }
            if($order['refill_status'] == 3){
                $response[] = [
                    'refill' => $order_id,
                    'status' => 'Rejected'
                ];
                continue;
            }
        }
        // Trả về kết quả theo format mới
        die(json_encode($response));
    }
    // Xử lý một đơn hàng
    else if(!empty($_REQUEST['refill'])){
        $order_id = trim(check_string($_REQUEST['refill']));
        
        if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
            $response[] = [
                'refill' => $order_id,
                'status' => [
                    'error' => __('Incorrect refill ID')
                ]
            ];
        }
        if($order['refill_status'] == 1){
            $response[] = [
                'refill' => $order_id,
                'status' => 'Completed'
            ];
        }
        if($order['refill_status'] == 2){
            $response[] = [
                'refill' => $order_id,
                'status' => 'Pending'
            ];
        }
        if($order['refill_status'] == 3){
            $response[] = [
                'refill' => $order_id,
                'status' => 'Rejected'
            ];
        }
        die(json_encode($response));
    }
    else {
        die(json_encode(['error' => __('error.incorrect_refill_id')]));
    }
}


// API lấy danh sách dịch vụ
if($_REQUEST['action'] == 'services'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['error' => __('Invalid API key')]));
    }
    
    // Lấy danh sách dịch vụ từ database với JOIN category và parent category
    $services_raw = $CMSNT->get_list("
        SELECT s.*, c.name as category_name, pc.name as parent_category_name
        FROM `services` s 
        LEFT JOIN `categories` c ON s.category_id = c.id 
        LEFT JOIN `categories` pc ON c.parent_id = pc.id
        WHERE s.display = 'show' 
        AND c.status = 'show'
        AND pc.status = 'show'
        ORDER BY s.id ASC
    ");
    
    // Format lại dữ liệu theo yêu cầu API
    $services = [];
    foreach ($services_raw as $service) {
        // Tính giá dựa trên type của dịch vụ
        if ($service['type'] == 'Package' || $service['type'] == 'Subscriptions' || $service['type'] == 'Custom Comments Package') {
            // Dịch vụ Package hiển thị giá của số lượng 1
            $price = $service['price'];
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
        } else {
            // Các dịch vụ khác hiển thị giá của số lượng 1.000
            $price = $service['price'] * 1000;
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])] * 1000; // Lấy giá theo Rank của User
            }
        }
        
        if($getUser['discount'] > 0){
            $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
        }
        $services[] = [
            'service' => (int)$service['id'],
            'name' => $service['name'],
            'desc' => $service['description'],
            'type' => $service['type'] ?: 'Default',
            'category' => $service['category_name'] ?: NULL,
            'platform' => $service['parent_category_name'] ?: NULL,
            'rate'  => (string)$price,
            'min' => (string)$service['min'],
            'max' => (string)$service['max'],
            'refill'    => $service['refill'] === '1' || $service['refill'] === 'true',
            'cancel'    => $service['cancel'] === '1' || $service['cancel'] === 'true',
            'dripfeed'  => $service['dripfeed'] === '1' || $service['dripfeed'] === 'true'
        ];
    }
    die(json_encode($services));
}

// API Trạng thái đơn
if($_REQUEST['action'] == 'status'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['error' => __('Invalid API key')]));
    }
    
    // Xử lý nhiều đơn hàng
    if(!empty($_REQUEST['orders'])){
        $orders = explode(',', check_string($_REQUEST['orders']));
        // Giới hạn tối đa 100 ID
        $orders = array_slice($orders, 0, 100);
        $response = [];
        
        foreach ($orders as $order_id) {
            $order_id = trim($order_id);
            if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
                $response[$order_id] = [
                    'error' => __('Incorrect order ID')
                ];
                continue;
            }
            
            $response[$order_id] = [
                'charge' => (string)$order['pay'],
                'start_count' => (string)($order['start_count'] ?: '0'),
                'status' => $order['status'],
                'remains' => (string)($order['remains'] ?: '0'),
                'currency' => currencyDefault()
            ];
        }
        
        die(json_encode($response));
    }
    // Xử lý một đơn hàng
    else if(!empty($_REQUEST['order'])){
        $order_id = trim(check_string($_REQUEST['order']));
        
        if(!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `trans_id` = '$order_id' AND `user_id` = '".$getUser['id']."' ")){
            die(json_encode(['error' => __('Incorrect order ID')]));
        }
        
        $response = [
            'charge'        => (string)$order['pay'],
            'start_count'   => (string)($order['start_count'] ?: '0'),
            'status'        => $order['status'],
            'remains'       => (string)($order['remains'] ?: '0'),
            'currency'      => currencyDefault()
        ];
        
        die(json_encode($response));
    }
    else {
        die(json_encode(['error' => __('Missing order parameter')]));
    }
}

// API Số dư tài khoản
if($_REQUEST['action'] == 'balance'){
    if (empty($_REQUEST['key'])) {
        die(json_encode(['error' => __('Invalid API key')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `api_key` = '".check_string($_REQUEST['key'])."' AND `banned` = 0 ")) {
        // Rate limit
        checkBlockIP('API', 5);
        die(json_encode(['error' => __('Invalid API key')]));
    }
    
    $response = [
        'balance' => (string)$getUser['money'],
        'currency' => currencyDefault()
    ];
    
    die(json_encode($response));
}

$data = json_encode([
    'status'    => 'error',
    'msg'       => __('The Request Not Found')
]);
die($data);  