<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../models/is_admin.php');



if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}

 

if($_POST['action'] == 'getAllServiceBySupplier'){
    $supplier_id = check_string($_POST['supplier_id']);

    if(!$supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '".$supplier_id."'")){
        die(json_encode(['status' => 'error', 'msg' => __('Nhà cung cấp không tồn tại')]));
    }
    if($supplier['type'] == 'SMMPANEL2'){
        require_once(__DIR__.'/../../libs/suppliers/smmpanel2.php');
        $response = get_services_smmpanel2($supplier['domain'], $supplier['api_key'], $supplier['proxy']);
        $response = json_decode($response, true);
        if(isset($response['error'])){
            die(json_encode(['status' => 'error', 'msg' => $response['error']]));
        }
        $services = [];
        foreach($response as $data){

            $service_id = check_string($data['service']);
            $service_name = check_string($data['name']);
            $service_desc = isset($data['desc']) ? check_string($data['desc']) : NULL;
            $service_type = check_string($data['type']);
            $service_rate = check_string($data['rate']);
            $service_min = isset($data['min']) ? check_string($data['min']) : 1;
            $service_max = isset($data['max']) ? check_string($data['max']) : 1000000;
            $service_dripfeed = isset($data['dripfeed']) ? check_string($data['dripfeed']) : false;
            $service_refill = isset($data['refill']) ? check_string($data['refill']) : false;
            $service_cancel = isset($data['cancel']) ? check_string($data['cancel']) : false;
            $service_platform = isset($data['platform']) ? check_string($data['platform']) : NULL;
            $service_category = isset($data['category']) ? check_string($data['category']) : NULL;

            $rate = $service_rate * $supplier['rate']; // Rate của nhà cung cấp
            $rate = $rate / $supplier['format_price'];
            //
            $service['service'] = $service_id;
            $service['name'] = $service_name;
            $service['type'] = $service_type;
            $service['platform'] = $service_platform;
            $service['rate'] = $rate;
            $service['min'] = $service_min;
            $service['max'] = $service_max;
            $service['category'] = $service_category;
            $service['desc'] = $service_desc;
            $service['refill'] = $service_refill;
            $service['cancel'] = $service_cancel;
            $service['dripfeed'] = $service_dripfeed;
            $services[] = $service;
        }

        die(json_encode(['status' => 'success', 'data' => $services]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Nhà cung cấp không hỗ trợ')]));
}


if($_POST['action'] == 'show_thong_ke_dashboard'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $currentDate = date("Y-m-d");
    $currentYear = date('Y');
    $currentMonth = date('m');
    
    // Xác định ngày bắt đầu và kết thúc của tuần hiện tại (Thứ Hai đến Chủ Nhật)
    $startOfWeek = date("Y-m-d", strtotime("last Monday", strtotime($currentDate)));
    // Nếu hôm nay là Thứ Hai, không cần lùi lại
    if (date('N', strtotime($currentDate)) == 1) {
        $startOfWeek = $currentDate;
    }
    $endOfWeek = date("Y-m-d", strtotime("next Sunday", strtotime($currentDate)));
    // Nếu hôm nay là Chủ Nhật, không cần tiến lên
    if (date('N', strtotime($currentDate)) == 7) {
        $endOfWeek = $currentDate;
    }

    // Dữ liệu hôm nay
    $query1 = "SELECT 
                COUNT(id) AS total_orders_today, 
                SUM(pay) AS total_pay_today, 
                SUM(cost) AS total_cost_today 
              FROM `orders` 
              WHERE `status` IN ('Completed', 'In progress', 'Processing', 'Pending')
              AND DATE(created_at) = '$currentDate'";
    $result1 = $CMSNT->get_row($query1);
    
    $total_orders_today = $result1['total_orders_today'];
    $total_pay_today = $result1['total_pay_today'];
    $total_cost_today = $result1['total_cost_today'];
    $profit_today = $total_pay_today - $total_cost_today;
    
    $new_users_today = $CMSNT->get_row("SELECT COUNT(id) AS total_users_today FROM `users` WHERE DATE(create_date) = '$currentDate'")['total_users_today'];
    
    // Dữ liệu tuần này
    $query_week = "SELECT 
                    COUNT(id) AS total_orders_week, 
                    SUM(pay) AS total_pay_week, 
                    SUM(cost) AS total_cost_week 
                  FROM `orders` 
                  WHERE `status` IN ('Completed', 'In progress', 'Processing')
                  AND DATE(created_at) BETWEEN '$startOfWeek' AND '$endOfWeek'";
    $result_week = $CMSNT->get_row($query_week);
    
    $total_orders_week = $result_week['total_orders_week'];
    $total_pay_week = $result_week['total_pay_week'];
    $total_cost_week = $result_week['total_cost_week'];
    $profit_week = $total_pay_week - $total_cost_week;
    
    $new_users_week = $CMSNT->get_row("SELECT COUNT(id) AS total_users_week FROM `users` WHERE DATE(create_date) BETWEEN '$startOfWeek' AND '$endOfWeek'")['total_users_week'];
    
    // Dữ liệu tháng này
    $query2 = "SELECT 
                COUNT(id) AS total_orders_month, 
                SUM(pay) AS total_pay_month, 
                SUM(cost) AS total_cost_month 
              FROM `orders` 
              WHERE `status` IN ('Completed', 'In progress', 'Processing')
              AND YEAR(created_at) = $currentYear 
              AND MONTH(created_at) = $currentMonth";
    $result2 = $CMSNT->get_row($query2);
    
    $total_orders_month = $result2['total_orders_month'];
    $total_pay_month = $result2['total_pay_month'];
    $total_cost_month = $result2['total_cost_month'];
    $profit_month = $total_pay_month - $total_cost_month;
    
    $new_users_month = $CMSNT->get_row("SELECT COUNT(id) AS total_users_month FROM `users` WHERE YEAR(create_date) = $currentYear AND MONTH(create_date) = $currentMonth")['total_users_month'];
    
    // Dữ liệu toàn thời gian
    $query3 = "SELECT 
                COUNT(id) AS total_orders_all, 
                SUM(pay) AS total_pay_all, 
                SUM(cost) AS total_cost_all 
              FROM `orders` 
              WHERE `status` IN ('Completed', 'In progress', 'Processing')";
    $result3 = $CMSNT->get_row($query3);
    
    $total_orders_all = $result3['total_orders_all'];
    $total_pay_all = $result3['total_pay_all'];
    $total_cost_all = $result3['total_cost_all'];
    $profit_all = $total_pay_all - $total_cost_all;

    $total_users_all = $CMSNT->get_row("SELECT COUNT(id) AS total_users_all FROM `users`")['total_users_all'];
    
    $data = array(
        "total_orders_today" => format_cash($total_orders_today),
        "total_pay_today" => format_currency($total_pay_today),
        "total_cost_today" => format_currency($total_cost_today),
        "profit_today" => format_currency($profit_today),
        "new_users_today" => format_cash($new_users_today),
        
        // Thêm dữ liệu tuần này
        "total_orders_week" => format_cash($total_orders_week),
        "total_pay_week" => format_currency($total_pay_week),
        "total_cost_week" => format_currency($total_cost_week),
        "profit_week" => format_currency($profit_week),
        "new_users_week" => format_cash($new_users_week),
        
        "total_orders_month" => format_cash($total_orders_month),
        "total_pay_month" => format_currency($total_pay_month),
        "total_cost_month" => format_currency($total_cost_month),
        "profit_month" => format_currency($profit_month),
        "new_users_month" => format_cash($new_users_month),
        "total_orders_all" => format_cash($total_orders_all),
        "total_pay_all" => format_currency($total_pay_all),
        "total_cost_all" => format_currency($total_cost_all),
        "profit_all" => format_currency($profit_all),
        "total_users_all" => format_cash($total_users_all)
    );

    die(json_encode($data));
}

if($_POST['action'] == 'view_chart_thong_ke_don_hang'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $time_range = check_string($_POST['time_range']);
    $labels = [];
    $revenues = [];
    $profits = [];
    
    if ($time_range == 'week') {
        // Thống kê 7 ngày gần đây
        for ($i = 6; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $query = "SELECT SUM(pay) AS total_pay, SUM(cost) AS total_cost FROM `orders` WHERE `status` IN ('Completed', 'In progress', 'Processing') AND DATE(created_at) = '$date'";
            $result = $CMSNT->get_row($query);
            
            $labels[] = date("d/m", strtotime("-$i days"));
            $revenues[] = $result['total_pay'] ?? 0;
            $profits[] = ($result['total_pay'] ?? 0) - ($result['total_cost'] ?? 0);
        }
    } else if ($time_range == 'month') {
        // Thống kê theo tháng hiện tại
        $month = date('m');
        $year = date('Y');
        $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
        for ($day = 1; $day <= $numOfDays; $day++) {
            $date = "$year-$month-$day";
            $query = "SELECT SUM(pay) AS total_pay, SUM(cost) AS total_cost FROM `orders` WHERE `status` IN ('Completed', 'In progress', 'Processing') AND DATE(created_at) = '$date'";
            $result = $CMSNT->get_row($query);
            
            $labels[] = "$day/$month";
            $revenues[] = $result['total_pay'] ?? 0;
            $profits[] = ($result['total_pay'] ?? 0) - ($result['total_cost'] ?? 0);
        }
    } else if ($time_range == 'year') {
        // Thống kê theo năm hiện tại
        $year = date('Y');
        
        for ($month = 1; $month <= 12; $month++) {
            $month_name = date('m', mktime(0, 0, 0, $month, 1));
            $query = "SELECT SUM(pay) AS total_pay, SUM(cost) AS total_cost FROM `orders` 
                      WHERE `status` IN ('Completed', 'In progress', 'Processing') AND MONTH(created_at) = '$month' AND YEAR(created_at) = '$year'";
            $result = $CMSNT->get_row($query);
            
            $labels[] = "Tháng $month_name";
            $revenues[] = $result['total_pay'] ?? 0;
            $profits[] = ($result['total_pay'] ?? 0) - ($result['total_cost'] ?? 0);
        }
    }

    die(json_encode([
        'labels' => $labels,
        'revenues' => $revenues,
        'profits' => $profits
    ]));
}

if($_POST['action'] == 'view_don_hang_gan_day'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_recent_transactions') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $orders = $CMSNT->get_list("SELECT * FROM `orders` ORDER BY id DESC limit 100");
    $html = '';
    foreach($orders as $order){
        $username = $CMSNT->get_row("SELECT username FROM `users` WHERE `id` = '".$order['user_id']."'")['username'] ?? 'N/A';
        $html .= '<li>
            <div class="timeline-time text-end">
                <span class="date">'.timeAgo(strtotime($order['created_at'])).'</span>
            </div>
            <div class="timeline-icon">
                <a href="javascript:void(0);"></a>
            </div>
            <div class="timeline-body">
                <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                    <div class="flex-fill">
                        <div class="d-flex align-items-center">
                            <div class="mt-sm-0 mt-2">
                                <p class="mb-0 text-muted"><a class="fw-bold" href="'.base_url_admin('user-edit&id='.$order['user_id']).'" style="color: green;">'.$username.'</a>
                                    mua <b style="color: red;">'.format_cash($order['quantity']).'</b>
                                    <b>'.$order['service_name'].'</b> với giá <b style="color:blue;">'.format_currency($order['pay']).'</b>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </li>';
    }
    die($html);
}

if($_POST['action'] == 'view_nap_tien_gan_day'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_recent_transactions') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $deposits = $CMSNT->get_list("SELECT * FROM `deposit_log` WHERE `is_virtual` = 0 ORDER BY id DESC limit 100");
    $html = '';
    foreach($deposits as $deposit){
        $username = $CMSNT->get_row("SELECT username FROM `users` WHERE `id` = '".$deposit['user_id']."'")['username'] ?? 'N/A';
        $html .= '<li>
        <div class="timeline-time text-end">
            <span class="date">'.timeAgo($deposit['create_time']).'</span>
        </div>
        <div class="timeline-icon">
            <a href="javascript:void(0);"></a>
        </div>
        <div class="timeline-body">
            <div class="d-flex align-items-top timeline-main-content flex-wrap mt-0">
                <div class="flex-fill">
                    <div class="d-flex align-items-center">
                        <div class="mt-sm-0 mt-2">
                            <p class="mb-0 text-muted"><a class="fw-bold" href="'.base_url_admin('user-edit&id='.$deposit['user_id']).'" style="color: green;">'.$username.'</a>
                                thực hiện nạp <b style="color: blue;">'.format_currency($deposit['amount']).'</b>
                                bằng <b style="color:red">'.$deposit['method'].'</b> thực nhận <b style="color:blue;">'.format_currency($deposit['received']).'</b>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </li>';
    }
    die($html);
}

if($_POST['action'] == 'view_chart_thong_ke_nap_tien'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $time_range = check_string($_POST['time_range']);
    $labels = [];
    $amount = [];
    
    if ($time_range == 'week') {
        // Thống kê 7 ngày gần đây
        for ($i = 6; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
        
            $total_topup_bank = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
            $payment_bank_invoice = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank_invoice WHERE `status` = 'completed' AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_card = $CMSNT->get_row("SELECT SUM(amount) AS total FROM cards WHERE `status` = 'completed' AND DATE(create_date) = '$date'")['total'] ?? 0;
            $total_topup_crypto = $CMSNT->get_row("SELECT SUM(received) AS total FROM payment_crypto WHERE `status` = 'completed' AND DATE(create_gettime) = '$date'")['total'] ?? 0;
            $total_topup_momo = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_momo WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
            $total_topup_paypal = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_paypal WHERE DATE(create_date) = '$date'")['total'] ?? 0;
            $total_topup_xipay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_xipay WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_tmweasyapi = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_tmweasyapi WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup = $total_topup_bank + $total_topup_card + $total_topup_crypto + $total_topup_momo + $total_topup_paypal + $total_topup_xipay + $total_topup_tmweasyapi + $payment_bank_invoice;
        
            $labels[] = date("d/m", strtotime("-$i days"));
            $amount[] = $total_topup;
        }
    } else if ($time_range == 'month') {
        // Thống kê theo tháng hiện tại
        $month = date('m');
        $year = date('Y');
        $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
        
        for ($day = 1; $day <= $numOfDays; $day++) {
            $date = "$year-$month-$day";
        
            $total_topup_bank = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
            $payment_bank_invoice = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank_invoice WHERE `status` = 'completed' AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_card = $CMSNT->get_row("SELECT SUM(amount) AS total FROM cards WHERE `status` = 'completed' AND DATE(create_date) = '$date'")['total'] ?? 0;
            $total_topup_crypto = $CMSNT->get_row("SELECT SUM(received) AS total FROM payment_crypto WHERE `status` = 'completed' AND DATE(create_gettime) = '$date'")['total'] ?? 0;
            $total_topup_momo = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_momo WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
            $total_topup_paypal = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_paypal WHERE DATE(create_date) = '$date'")['total'] ?? 0;
           
            $total_topup_xipay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_xipay WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_korapay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_korapay WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_tmweasyapi = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_tmweasyapi WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup_openpix = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_openpix WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
            $total_topup = $total_topup_bank + $total_topup_card + $total_topup_crypto + $total_topup_momo + $total_topup_paypal + $total_topup_xipay + $total_topup_korapay + $total_topup_tmweasyapi + $total_topup_openpix + $payment_bank_invoice;
        
            $labels[] = "$day/$month";
            $amount[] = $total_topup;
        }
    } else if ($time_range == 'year') {
        // Thống kê theo năm hiện tại
        $year = date('Y');
        
        for ($month = 1; $month <= 12; $month++) {
            $month_name = date('m', mktime(0, 0, 0, $month, 1));
        
            $start_date = "$year-$month-01";
            $last_day = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $end_date = "$year-$month-$last_day";
        
            $total_topup_bank = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank WHERE DATE(create_gettime) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $payment_bank_invoice = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank_invoice WHERE `status` = 'completed' AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_card = $CMSNT->get_row("SELECT SUM(amount) AS total FROM cards WHERE `status` = 'completed' AND DATE(create_date) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_crypto = $CMSNT->get_row("SELECT SUM(received) AS total FROM payment_crypto WHERE `status` = 'completed' AND DATE(create_gettime) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_momo = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_momo WHERE DATE(create_gettime) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_paypal = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_paypal WHERE DATE(create_date) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_xipay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_xipay WHERE `status` = 1 AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_korapay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_korapay WHERE `status` = 1 AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_tmweasyapi = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_tmweasyapi WHERE `status` = 1 AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup_openpix = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_openpix WHERE `status` = 1 AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'")['total'] ?? 0;
            $total_topup = $total_topup_bank + $total_topup_card + $total_topup_crypto + $total_topup_momo + $total_topup_paypal + $total_topup_xipay + $total_topup_korapay + $total_topup_tmweasyapi + $total_topup_openpix + $payment_bank_invoice;
            
            $labels[] = "Tháng $month_name";
            $amount[] = $total_topup;
        }
    }

    die(json_encode([
        'labels' => $labels,
        'amount' => $amount
    ]));
}

if($_POST['action'] == 'view_chart_thong_ke_nap_tien_thang'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $month = date('m');
    $year = date('Y');
    $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);
    
    $labels = [];
    $data = [];
    
    for ($day = 1; $day <= $numOfDays; $day++) {
        $date = "$year-$month-$day";
    
        $total_topup_bank = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
        $payment_bank_invoice = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_bank_invoice WHERE `status` = 'completed' AND DATE(created_at) = '$date'")['total'] ?? 0;
        $total_topup_card = $CMSNT->get_row("SELECT SUM(amount) AS total FROM cards WHERE `status` = 'completed' AND DATE(create_date) = '$date'")['total'] ?? 0;
        $total_topup_crypto = $CMSNT->get_row("SELECT SUM(received) AS total FROM payment_crypto WHERE `status` = 'completed' AND DATE(create_gettime) = '$date'")['total'] ?? 0;
        $total_topup_momo = $CMSNT->get_row("SELECT SUM(amount) AS total FROM payment_momo WHERE DATE(create_gettime) = '$date'")['total'] ?? 0;
        $total_topup_paypal = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_paypal WHERE DATE(create_date) = '$date'")['total'] ?? 0;
        
        $total_topup_xipay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_xipay WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
        $total_topup_korapay = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_korapay WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
        $total_topup_tmweasyapi = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_tmweasyapi WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
        $total_topup_openpix = $CMSNT->get_row("SELECT SUM(price) AS total FROM payment_openpix WHERE `status` = 1 AND DATE(created_at) = '$date'")['total'] ?? 0;
        $total_topup = $total_topup_bank + $total_topup_card + $total_topup_crypto + $total_topup_momo + $total_topup_paypal + $total_topup_xipay + $total_topup_korapay + $total_topup_tmweasyapi + $total_topup_openpix + $payment_bank_invoice;
    
        $labels[] = "$day/$month/$year";
        $data[] = $total_topup;
    }

    die(json_encode([
        'labels' => $labels,
        'data' => $data
    ]));
}
 

 



if($_POST['action'] == 'phan_tich_utm_source_users'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_user') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    // Tạo HTML cho tab
$html = '<ul class="nav nav-tabs mb-5 nav-justified nav-style-1 d-sm-flex d-block" id="myTab" role="tablist">';
$html .= '<li class="nav-item">';
$html .= '<a class="nav-link active" id="table-tab" data-toggle="tab" href="#table-content" role="tab" aria-controls="table-content" aria-selected="true">Table</a>';
$html .= '</li>';
$html .= '<li class="nav-item">';
$html .= '<a class="nav-link" id="chart-tab" data-toggle="tab" href="#chart-content" role="tab" aria-controls="chart-content" aria-selected="false">Pie Chart</a>';
$html .= '</li>';
$html .= '</ul>';

// Tạo HTML cho nội dung của tab
$html .= '<div class="tab-content" id="myTabContent">';
$html .= '<div class="tab-pane fade show active" id="table-content" role="tabpanel" aria-labelledby="table-tab">';
$html .= '<div class="table-responsive table-wrapper" style="max-height: 500px;overflow-y: auto;">';
$html .= '<table class="table text-nowrap table-striped table-hover table-bordered">
            <thead>
                <tr>
                    <th class="text-center">Xếp hạng</th>
                    <th class="text-center">utm_source</th>
                    <th class="text-center">Số thành viên đăng ký</th>
                </tr>
            </thead>
            <tbody>';
$i = 1;
$data_labels = [];
$data_user_counts = [];
foreach($CMSNT->get_list("SELECT 
    utm_source, 
    COUNT(*) AS total_users
FROM users 
GROUP BY utm_source 
ORDER BY total_users DESC ") as $row){
    $data_labels[] = $row['utm_source'];
    $data_user_counts[] = $row['total_users'];
    $html .= "<tr>
    <td class='text-center' style='font-size:15px;'>" . $i++ . "</td>
    <td class='text-center'>" . $row['utm_source'] . "</td>
    <td class='text-center'><b>" . format_cash($row['total_users']) . "</b></td>
  </tr>";
}
$html .= "</tbody>
        </table>";
$html .= "</div>";
$html .= '</div>';

$html .= '<div class="tab-pane fade" id="chart-content" role="tabpanel" aria-labelledby="chart-tab">';
$html .= '<canvas id="myChart" width="500" height="300"></canvas>';
$html .= '</div>';

$html .= '</div>';

// Thêm kịch bản JavaScript để chuyển đổi tab
$html .= '<script>
            $(document).ready(function(){
                $("#table-tab").click(function(){
                    $("#chart-content").removeClass("show active");
                    $("#chart-tab").removeClass("active");
                    $("#table-content").addClass("show active");
                    $("#table-tab").addClass("active");
                });
                $("#chart-tab").click(function(){
                    $("#table-content").removeClass("show active");
                    $("#table-tab").removeClass("active");
                    $("#chart-content").addClass("show active");
                    $("#chart-tab").addClass("active");
                    // Thêm kịch bản JavaScript để vẽ biểu đồ Pie Chart
                    var ctx = document.getElementById("myChart").getContext("2d");
                    var myChart = new Chart(ctx, {
                        type: "pie",
                        data: {
                            labels: '.json_encode($data_labels).',
                            datasets: [{
                                label: "Số lượng người dùng",
                                data: '.json_encode($data_user_counts).',
                                backgroundColor: [
                                    "rgba(255, 99, 132, 0.6)",
                                    "rgba(54, 162, 235, 0.6)",
                                    "rgba(255, 206, 86, 0.6)",
                                    "rgba(75, 192, 192, 0.6)",
                                    "rgba(153, 102, 255, 0.6)",
                                    "rgba(255, 159, 64, 0.6)"
                                ],
                                borderColor: [
                                    "rgba(255, 99, 132, 1)",
                                    "rgba(54, 162, 235, 1)",
                                    "rgba(255, 206, 86, 1)",
                                    "rgba(75, 192, 192, 1)",
                                    "rgba(153, 102, 255, 1)",
                                    "rgba(255, 159, 64, 1)"
                                ],
                                borderWidth: 1
                            }]
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            legend: {
                                position: "right",
                                labels: {
                                    fontColor: "black",
                                    fontSize: 12
                                }
                            }
                        }
                    });
                });
            });
        </script>';




    


    die($html);
}

if($_POST['action'] == 'export_users_email'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_user') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    // Lấy tất cả email của users
    $users = $CMSNT->get_list("SELECT `id`, `username`, `email`, `fullname`, `create_date` FROM `users` ORDER BY id DESC");
    
    // Tạo header CSV
    $csv_data = "ID,Username,Email,Full Name,Create Date\n";
    
    // Thêm dữ liệu users
    foreach($users as $user) {
        $csv_data .= $user['id'] . ',';
        $csv_data .= '"' . str_replace('"', '""', $user['username']) . '",';
        $csv_data .= '"' . str_replace('"', '""', $user['email']) . '",';
        $csv_data .= '"' . str_replace('"', '""', $user['fullname']) . '",';
        $csv_data .= '"' . str_replace('"', '""', $user['create_date']) . '"';
        $csv_data .= "\n";
    }
    
    die(json_encode([
        'status' => 'success',
        'csv_data' => $csv_data,
        'total_users' => count($users)
    ]));
}

 
 
 
 
 

// Thống kê doanh thu nhà cung cấp theo khoảng thời gian
if($_POST['action'] == 'view_chart_supplier_revenue'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $supplier_id = check_string($_POST['supplier_id']);
    $time_range = check_string($_POST['time_range']);
    
    // Kiểm tra tồn tại supplier_id
    if (!$CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '$supplier_id'")) {
        die(json_encode(['status' => 'error', 'msg' => __('Nhà cung cấp không tồn tại')]));
    }
    
    $labels = [];
    $revenues = [];
    
    if ($time_range == '7_days') {
        // Thống kê 7 ngày gần đây
        for ($i = 6; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $query = "SELECT SUM(pay) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            $result = $CMSNT->get_row($query);
            
            $labels[] = date("d/m", strtotime("-$i days"));
            $revenues[] = $result['total'] ?? 0;
        }
    } else if ($time_range == '30_days') {
        // Thống kê 30 ngày gần đây
        for ($i = 29; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $query = "SELECT SUM(pay) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            $result = $CMSNT->get_row($query);
            
            $labels[] = date("d/m", strtotime("-$i days"));
            $revenues[] = $result['total'] ?? 0;
        }
    } else if ($time_range == '1_year') {
        // Thống kê 12 tháng gần đây
        for ($i = 11; $i >= 0; $i--) {
            $year = date("Y", strtotime("-$i months"));
            $month = date("m", strtotime("-$i months"));
            
            $query = "SELECT SUM(pay) AS total FROM `orders` 
                      WHERE `supplier_id` = '$supplier_id' 
                      AND MONTH(created_at) = '$month' AND YEAR(created_at) = '$year'
                      AND `status` IN ('Completed', 'In progress', 'Processing')";
            $result = $CMSNT->get_row($query);
            
            $labels[] = date("m/Y", strtotime("-$i months"));
            $revenues[] = $result['total'] ?? 0;
        }
    }

    die(json_encode([
        'status' => 'success',
        'labels' => $labels,
        'revenues' => $revenues
    ]));
}

// Thống kê doanh thu vs lợi nhuận nhà cung cấp theo khoảng thời gian
if($_POST['action'] == 'view_chart_supplier_revenue_profit'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $supplier_id = check_string($_POST['supplier_id']);
    $time_range = check_string($_POST['time_range']);
    
    // Kiểm tra tồn tại supplier_id
    if (!$CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '$supplier_id'")) {
        die(json_encode(['status' => 'error', 'msg' => __('Nhà cung cấp không tồn tại')]));
    }
    
    $labels = [];
    $revenues = [];
    $profits = [];
    
    if ($time_range == '7_days') {
        // Thống kê 7 ngày gần đây
        for ($i = 6; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $revenue_query = "SELECT SUM(pay) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            $cost_query = "SELECT SUM(cost) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            
            $revenue_result = $CMSNT->get_row($revenue_query);
            $cost_result = $CMSNT->get_row($cost_query);
            
            $daily_revenue = $revenue_result['total'] ?? 0;
            $daily_cost = $cost_result['total'] ?? 0;
            
            $labels[] = date("d/m", strtotime("-$i days"));
            $revenues[] = $daily_revenue;
            $profits[] = $daily_revenue - $daily_cost;
        }
    } else if ($time_range == '30_days') {
        // Thống kê 30 ngày gần đây
        for ($i = 29; $i >= 0; $i--) {
            $date = date("Y-m-d", strtotime("-$i days"));
            $revenue_query = "SELECT SUM(pay) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            $cost_query = "SELECT SUM(cost) AS total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND DATE(created_at) = '$date' AND `status` IN ('Completed', 'In progress', 'Processing')";
            
            $revenue_result = $CMSNT->get_row($revenue_query);
            $cost_result = $CMSNT->get_row($cost_query);
            
            $daily_revenue = $revenue_result['total'] ?? 0;
            $daily_cost = $cost_result['total'] ?? 0;
            
            $labels[] = date("d/m", strtotime("-$i days"));
            $revenues[] = $daily_revenue;
            $profits[] = $daily_revenue - $daily_cost;
        }
    } else if ($time_range == '1_year') {
        // Thống kê 12 tháng gần đây
        for ($i = 11; $i >= 0; $i--) {
            $year = date("Y", strtotime("-$i months"));
            $month = date("m", strtotime("-$i months"));
            
            $revenue_query = "SELECT SUM(pay) AS total FROM `orders` 
                            WHERE `supplier_id` = '$supplier_id' 
                            AND MONTH(created_at) = '$month' AND YEAR(created_at) = '$year'
                            AND `status` IN ('Completed', 'In progress', 'Processing')";
            $cost_query = "SELECT SUM(cost) AS total FROM `orders` 
                         WHERE `supplier_id` = '$supplier_id' 
                         AND MONTH(created_at) = '$month' AND YEAR(created_at) = '$year'
                         AND `status` IN ('Completed', 'In progress', 'Processing')";
            
            $revenue_result = $CMSNT->get_row($revenue_query);
            $cost_result = $CMSNT->get_row($cost_query);
            
            $monthly_revenue = $revenue_result['total'] ?? 0;
            $monthly_cost = $cost_result['total'] ?? 0;
            
            $labels[] = date("m/Y", strtotime("-$i months"));
            $revenues[] = $monthly_revenue;
            $profits[] = $monthly_revenue - $monthly_cost;
        }
    }

    die(json_encode([
        'status' => 'success',
        'labels' => $labels,
        'revenues' => $revenues,
        'profits' => $profits
    ]));
}

// Load translate data with pagination and search
if($_POST['action'] == 'load_translate_data'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $lang_id = check_string($_POST['lang_id']);
    $draw = intval(check_string($_POST['draw']));
    $start = intval(check_string($_POST['start']));
    $length = intval(check_string($_POST['length']));
    $search = check_string(check_string($_POST['search']['value']));
    $order_column = intval(check_string($_POST['order'][0]['column']));
    $order_dir = check_string(check_string($_POST['order'][0]['dir']));
    $filter = isset($_POST['filter']) ? check_string($_POST['filter']) : 'all';
    
    // Kiểm tra ngôn ngữ tồn tại
    if (!$lang_row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$lang_id' ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Ngôn ngữ không tồn tại')]));
    }
    
    // Cột để sắp xếp
    $columns = array('id', 'name', 'value', 'id');
    $order_column_name = isset($columns[$order_column]) ? $columns[$order_column] : 'id';
    
    // Xây dựng câu truy vấn
    $where = "WHERE `lang_id` = '$lang_id'";
    
    // Thêm filter cho nội dung chưa dịch
    if ($filter === 'untranslated') {
        $where .= " AND (`name` = `value` OR `value` = '' OR `value` IS NULL)";
    }
    
    if (!empty($search)) {
        $where .= " AND (`name` LIKE '%$search%' OR `value` LIKE '%$search%')";
    }
    
    // Tổng số bản ghi
    $total_records = $CMSNT->num_rows("SELECT * FROM `translate` WHERE `lang_id` = '$lang_id'");
    
    // Tổng số bản ghi sau khi lọc
    $total_filtered = $CMSNT->num_rows("SELECT * FROM `translate` $where");
    
    // Lấy dữ liệu với phân trang và sắp xếp
    $sql = "SELECT * FROM `translate` $where ORDER BY $order_column_name $order_dir LIMIT $start, $length";
    $translates = $CMSNT->get_list($sql);
    
    $data = array();
    
    foreach($translates as $trans) {
        $row = array();
        $row[] = '<input type="checkbox" class="form-check-input row-checkbox" value="'.$trans['id'].'" data-name="'.htmlspecialchars($trans['name']).'" data-code="'.$lang_row['code'].'">';
        $row[] = '<textarea class="form-control" disabled>'.htmlspecialchars($trans['name']).'</textarea>';
        $row[] = '<textarea class="form-control" id="value'.$trans['id'].'" onchange="updateForm(\''.$trans['id'].'\')">' . htmlspecialchars($trans['value']) . '</textarea>';
        $row[] = '<div class="btn-list">
                    <button type="button" class="btn btn-primary-gradient btn-wave btn-sm" onclick="autoTranslate(\''.$trans['id'].'\', \''.addslashes($trans['name']).'\', \''.$lang_row['code'].'\', this)">
                        <i class="ri-translate"></i> '.__('Dịch tự động').'
                    </button>
                    <button type="button" class="btn btn-danger-gradient btn-wave btn-sm" onclick="RemoveRow(\''.$trans['id'].'\', \''.addslashes($trans['name']).'\')">
                        <i class="ri-delete-bin-line"></i> '.__('Delete').'
                    </button>
                  </div>';
        $data[] = $row;
    }
    
    $response = array(
        "draw" => $draw,
        "recordsTotal" => $total_records,
        "recordsFiltered" => $total_filtered,
        "data" => $data
    );
    
    die(json_encode($response));
}

// Lấy bảng xếp hạng user theo giá trị đơn hàng trong ngày
if($_POST['action'] == 'get_daily_leaderboard'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $currentDate = date("Y-m-d");
    
    // Lấy top 50 user có tổng giá trị đơn hàng cao nhất trong ngày
    $query = "SELECT 
                u.id,
                u.username,
                u.fullname,
                u.email,
                SUM(o.pay) as total_spent,
                COUNT(o.id) as total_orders
              FROM `users` u
              INNER JOIN `orders` o ON u.id = o.user_id
              WHERE o.status IN ('Completed', 'In progress', 'Processing', 'Pending')
              AND DATE(o.created_at) = '$currentDate'
              GROUP BY u.id, u.username, u.fullname, u.email
              ORDER BY total_spent DESC
              LIMIT 50";
    
    $leaderboard = $CMSNT->get_list($query);
    
    $data = [];
    $rank = 1;
    
    foreach($leaderboard as $user) {
        $data[] = [
            'rank'  => $rank,
            'id'    => $user['id'],
            'username' => $user['username'],
            'fullname' => $user['fullname'] ? $user['fullname'] : $user['username'],
            'email' => $user['email'],
            'total_spent' => format_currency($user['total_spent']),
            'total_orders' => format_cash($user['total_orders'])
        ];
        $rank++;
    }
    
    die(json_encode([
        'status' => 'success',
        'data' => $data,
        'date' => date('d/m/Y')
    ]));
}

// Lấy top 50 services bán chạy nhất trong ngày
if($_POST['action'] == 'get_daily_top_services'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $currentDate = date("Y-m-d");
    
    // Lấy top 50 services có tổng doanh thu cao nhất trong ngày
    $query = "SELECT 
                o.service_id,
                o.service_name,
                SUM(o.pay) as total_revenue,
                SUM(o.cost) as total_cost,
                COUNT(o.id) as total_orders,
                AVG(o.pay) as avg_price
              FROM `orders` o
              WHERE o.status IN ('Completed', 'In progress', 'Processing', 'Pending')
              AND DATE(o.created_at) = '$currentDate'
              AND o.service_name IS NOT NULL
              AND o.service_name != ''
              GROUP BY o.service_id, o.service_name
              ORDER BY total_revenue DESC
              LIMIT 50";
    
    $services = $CMSNT->get_list($query);
    
    $data = [];
    $rank = 1;
    
    foreach($services as $service) {
        $profit = $service['total_revenue'] - $service['total_cost'];
        $data[] = [
            'rank' => $rank,
            'service_id' => $service['service_id'],
            'service_name' => $service['service_name'],
            'total_revenue' => format_currency($service['total_revenue']),
            'total_cost' => format_currency($service['total_cost']),
            'profit' => format_currency($profit),
            'total_orders' => format_cash($service['total_orders']),
            'avg_price' => format_currency($service['avg_price'])
        ];
        $rank++;
    }
    
    die(json_encode([
        'status' => 'success',
        'data' => $data,
        'date' => date('d/m/Y')
    ]));
}

// Lấy thống kê nhà cung cấp trong ngày
if($_POST['action'] == 'get_daily_suppliers_stats'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_statistical') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $currentDate = date("Y-m-d");
    
    // Lấy thống kê các nhà cung cấp có đơn hàng trong ngày
    $query = "SELECT 
                s.id as supplier_id,
                s.type,
                s.domain as supplier_name,
                s.price,
                SUM(o.pay) as total_revenue,
                SUM(o.cost) as total_cost,
                COUNT(o.id) as total_orders
              FROM `suppliers` s
              INNER JOIN `orders` o ON s.id = o.supplier_id
              WHERE o.status IN ('Completed', 'In progress', 'Processing', 'Pending')
              AND DATE(o.created_at) = '$currentDate'
              GROUP BY s.id, s.type, s.domain, s.price
              ORDER BY total_revenue DESC";
    
    $suppliers = $CMSNT->get_list($query);
    
    $data = [];
    $rank = 1;
    
    foreach($suppliers as $supplier) {
        $profit = $supplier['total_revenue'] - $supplier['total_cost'];
        $profit_margin = $supplier['total_revenue'] > 0 ? round(($profit / $supplier['total_revenue']) * 100, 2) : 0;
        
        // Làm sạch tên nhà cung cấp (bỏ https://, http://, www.)
        $supplier_name = $supplier['supplier_name'];
        $supplier_name = preg_replace('/^https?:\/\//', '', $supplier_name);
        $supplier_name = preg_replace('/^www\./', '', $supplier_name);
        $supplier_name = rtrim($supplier_name, '/');
        
        $data[] = [
            'rank' => $rank,
            'supplier_id' => $supplier['supplier_id'],
            'supplier_name' => $supplier_name,
            'type' => $supplier['type'],
            'price' => $supplier['price'] ?? 0,
            'total_revenue' => format_currency($supplier['total_revenue']),
            'total_cost' => format_currency($supplier['total_cost']),
            'profit' => format_currency($profit),
            'total_orders' => format_cash($supplier['total_orders']),
            'profit_margin' => $profit_margin
        ];
        $rank++;
    }
    
    die(json_encode([
        'status' => 'success',
        'data' => $data,
        'date' => date('d/m/Y')
    ]));
}

// Lấy danh sách chuyên mục cho modal transfer
if($_POST['action'] == 'loadCategoriesForTransfer'){
    if(checkPermission($getUser['admin'], 'view_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $categories = [];
    $parent_categories = $CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ORDER BY `stt` DESC");
    
    foreach ($parent_categories as $parent) {
        $child_categories = $CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$parent['id']."' ORDER BY `stt` DESC");
        
        foreach ($child_categories as $child) {
            $service_count = $CMSNT->num_rows("SELECT * FROM `services` WHERE `category_id` = '".$child['id']."' ORDER BY `stt` DESC ");
            $supplier_name = $child['supplier_id'] != 0 ? 'API: ' . getRowRealtime('suppliers', $child['supplier_id'], 'domain') : __('Hệ thống');
            
            $categories[] = [
                'id' => $child['id'],
                'name' => $child['name'],
                'parent_name' => $parent['name'],
                'supplier_name' => $supplier_name,
                'service_count' => intval($service_count)
            ];
        }
    }
    
    die(json_encode([
        'status' => 'success',
        'categories' => $categories
    ]));
}

// Lấy số lượng dịch vụ trong chuyên mục
if($_POST['action'] == 'getCategoryServiceCount'){
    if(checkPermission($getUser['admin'], 'view_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $category_id = intval(check_string($_POST['category_id']));
    if(!$category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$category_id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục không tồn tại trong hệ thống')]));
    }
    
    $count = $CMSNT->num_rows("SELECT * FROM `services` WHERE `category_id` = '$category_id' ");
    
    die(json_encode([
        'status' => 'success',
        'count' => intval($count),
        'category_name' => $category['name']
    ]));
}

// Export dữ liệu đơn hàng
if($_POST['action'] == 'exportOrderData'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'view_orders_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $format = check_string($_POST['format']);
    $orderIds = json_decode($_POST['orderIds'], true);
    
    if (empty($orderIds) || !is_array($orderIds)) {
        die(json_encode(['status' => 'error', 'msg' => __('Không có đơn hàng nào được chọn')]));
    }
    
    // Validate format
    if (!in_array($format, ['csv', 'txt'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Định dạng không hợp lệ')]));
    }
    
    // Lấy dữ liệu đơn hàng
    $orderIds = array_map('intval', $orderIds);
    $orderIdsStr = implode(',', $orderIds);
    
    $orders = $CMSNT->get_list("SELECT 
        `trans_id`, 
        `order_id`, 
        `service_name`, 
        `quantity`, 
        `link`, 
        `comment`, 
        `pay`,
        `created_at`
    FROM `orders` 
    WHERE `id` IN ($orderIdsStr) 
    ORDER BY `id` DESC");
    
    if (empty($orders)) {
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy đơn hàng nào')]));
    }
    
    $data = '';
    $filename = 'orders_export_' . date('Y-m-d_H-i-s');
    $mimeType = '';
    
    if ($format == 'csv') {
        // Tạo CSV
        $filename .= '.csv';
        $mimeType = 'text/csv; charset=utf-8';
        
        // Header CSV
        $data = "\xEF\xBB\xBF"; // UTF-8 BOM để Excel hiển thị đúng tiếng Việt
        $data .= '"' . __('Tên dịch vụ') . '","' . __('Mã đơn hàng') . '","' . __('Mã đơn hàng API') . '","' . __('Số lượng') . '","' . __('Liên kết') . '","' . __('Bình luận') . '","' . __('Số tiền thanh toán') . '","' . __('Thời gian tạo') . '"' . "\n";
        
        // Dữ liệu CSV
        foreach ($orders as $order) {
            $data .= '"' . str_replace('"', '""', $order['service_name']) . '",';
            $data .= '"' . str_replace('"', '""', $order['trans_id']) . '",';
            $data .= '"' . str_replace('"', '""', $order['order_id'] ? $order['order_id'] : 'N/A') . '",';
            $data .= '"' . str_replace('"', '""', format_cash($order['quantity'])) . '",';
            $data .= '"' . str_replace('"', '""', $order['link'] ? $order['link'] : 'N/A') . '",';
            $data .= '"' . str_replace('"', '""', $order['comment'] ? $order['comment'] : 'N/A') . '",';
            $data .= '"' . str_replace('"', '""', format_currency($order['pay'])) . '",';
            $data .= '"' . str_replace('"', '""', $order['created_at']) . '"';
            $data .= "\n";
        }
        
    } else if ($format == 'txt') {
        // Tạo TXT
        $filename .= '.txt';
        $mimeType = 'text/plain; charset=utf-8';
        
        // Header TXT
        $data = "=== " . __('DANH SÁCH ĐƠN HÀNG XUẤT') . " ===\n";
        $data .= __('Thời gian xuất') . ": " . date('d/m/Y H:i:s') . "\n";
        $data .= __('Tổng số đơn hàng') . ": " . count($orders) . "\n";
        $data .= str_repeat("=", 80) . "\n\n";
        
        // Dữ liệu TXT
        $index = 1;
        foreach ($orders as $order) {
            $data .= "[$index] " . __('ĐƠN HÀNG') . " #" . $order['trans_id'] . "\n";
            $data .= "- " . __('Tên dịch vụ') . ": " . $order['service_name'] . "\n";
            $data .= "- " . __('Mã đơn hàng API') . ": " . ($order['order_id'] ? $order['order_id'] : 'N/A') . "\n";
            $data .= "- " . __('Số lượng') . ": " . format_cash($order['quantity']) . "\n";
            $data .= "- " . __('Liên kết') . ": " . ($order['link'] ? $order['link'] : 'N/A') . "\n";
            $data .= "- " . __('Bình luận') . ": " . ($order['comment'] ? $order['comment'] : 'N/A') . "\n";
            $data .= "- " . __('Số tiền thanh toán') . ": " . format_currency($order['pay']) . "\n";
            $data .= "- " . __('Thời gian tạo') . ": " . $order['created_at'] . "\n";
            $data .= str_repeat("-", 60) . "\n\n";
            $index++;
        }
        
        $data .= "=== " . __('KẾT THÚC DANH SÁCH') . " ===\n";
    }
    
    die(json_encode([
        'status' => 'success',
        'data' => $data,
        'filename' => $filename,
        'mimeType' => $mimeType,
        'total_orders' => count($orders)
    ]));
}