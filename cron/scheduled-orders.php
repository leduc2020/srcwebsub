<?php

    define("IN_SITE", true);
    require_once(__DIR__.'/../libs/db.php');
    require_once(__DIR__.'/../libs/lang.php');
    require_once(__DIR__.'/../libs/helper.php');
    require_once(__DIR__.'/../config.php');
    $CMSNT = new DB();

    if(!isset($_GET['key'])){
        die(__('Vui lòng nhập Key Cron Job vào đường dẫn'));
    }
    if(isset($_GET['key']) && $_GET['key'] != $CMSNT->site('key_cron_job')){
        die(__('Key không hợp lệ'));
    }

    if($CMSNT->site('status_scheduled_orders') != 1){
        die(__('Hệ thống đang tạm ngưng đặt lịch đơn hàng'));
    }
    /* START CHỐNG SPAM */
    if (time() > $CMSNT->site('check_time_cron_scheduled_orders')) {
        if (time() - $CMSNT->site('check_time_cron_scheduled_orders') < 3) {
            die('Thao tác quá nhanh, vui lòng thử lại sau!');
        }
    }
    $CMSNT->update("settings", [
        'value' => time()
    ], " `name` = 'check_time_cron_scheduled_orders' ");

    // Lấy danh sách đơn hàng đã đặt lịch
    $current_time = date('Y-m-d H:i:s');
    if (DEBUG) echo "Thời gian hiện tại: " . $current_time . "<br>";
    
    $scheduled_orders = $CMSNT->get_list("SELECT * FROM `scheduled_orders` WHERE `status` = 'pending' AND `schedule_time` <= '$current_time' ORDER BY `schedule_time` ASC");
    
    if (DEBUG) echo "Tìm thấy " . count($scheduled_orders) . " đơn hàng cần xử lý<br>";
    
    foreach($scheduled_orders as $order){
        if (DEBUG) echo "Đang xử lý đơn hàng ID: " . $order['id'] . " - Thời gian đặt lịch: " . $order['schedule_time'] . "<br>";

        if(!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '".$order['user_id']."' ")){
            if (DEBUG) echo "User không tồn tại, bỏ qua đơn hàng ID: " . $order['id'] . "<br>";
            continue;
        }

        // Gửi request đến API để chạy đơn hàng
        $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => base_url('api/v2'),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array(
            'key'       => $getUser['api_key'],
            'action'    => 'add',
            'service'   => $order['service_id'],
            'link'      => $order['link'],
            'comment'   => $order['comment'],
            'quantity'  => $order['quantity']),
        ));
        $response = curl_exec($curl);
        curl_close($curl);
        $result = json_decode($response, true);
        // 
        
        if (DEBUG) echo "Kết quả API cho đơn hàng ID " . $order['id'] . ": " . $response . "<br>";

        if($result && $result['status'] == 'success'){
            // Nếu thành công thì cập nhật trạng thái đơn hàng thành executed
            $isUpdate = $CMSNT->update("scheduled_orders", [
                'status'        => 'executed',
                'executed_at'   => gettime(),
            ], " `id` = '".$order['id']."' ");
            if($isUpdate){
                if (DEBUG) echo "✅ Đơn hàng ID " . $order['id'] . " đã được thực thi thành công<br>";
                // Ghi log hoạt động
                $CMSNT->insert("logs", [
                    'user_id'       => $order['user_id'],
                    'ip'            => myip(),
                    'device'        => getUserAgent(),
                    'createdate'    => gettime(),
                    'action'        => __('Chạy đơn hàng đã đặt lịch').' #'.$order['id'].' - '.($result['order_id'] ?? 'N/A')
                ]);
            }
        }else{
            // Nếu thất bại thì cập nhật trạng thái đơn hàng thành failed
            $error_msg = $result['msg'] ?? ($result['error'] ?? 'Lỗi không xác định');
            $CMSNT->update("scheduled_orders", [
                'status'        => 'failed',
                'reason'        => $error_msg,
                'executed_at'   => gettime()
            ], " `id` = '".$order['id']."' ");
            if (DEBUG) echo "❌ Đơn hàng ID " . $order['id'] . " thất bại: " . $error_msg . "<br>";
        }

    }

    if (DEBUG) {
        echo "<br>=== TỔNG KẾT ===<br>";
        echo "Tổng số đơn hàng đã xử lý: " . count($scheduled_orders) . "<br>";
        echo "Thời gian hoàn thành: " . date('Y-m-d H:i:s') . "<br>";
        echo "================<br>";
    }

 
  
