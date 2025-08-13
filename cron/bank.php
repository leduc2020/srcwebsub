<?php

    define("IN_SITE", true);
    require_once(__DIR__.'/../libs/db.php');
    require_once(__DIR__.'/../libs/lang.php');
    require_once(__DIR__.'/../libs/helper.php');
    require_once(__DIR__.'/../config.php');
    require_once(__DIR__.'/../libs/database/users.php');
    $CMSNT = new DB();
    $user = new users();


    if(!isset($_GET['key'])){
        die(__('Vui lòng nhập Key Cron Job vào đường dẫn'));
    }
    if(isset($_GET['key']) && $_GET['key'] != $CMSNT->site('key_cron_job')){
        die(__('Key không hợp lệ'));
    }

    if (time() > $CMSNT->site('check_time_cron_bank')) {
        if (time() - $CMSNT->site('check_time_cron_bank') < 5) {
            die('[ÉT O ÉT ]Thao tác quá nhanh, vui lòng đợi');
        }
    }
    $CMSNT->update("settings", ['value' => time()], " `name` = 'check_time_cron_bank' ");

    $config_list_api_web2m = [
        'Vietcombank' => [
            'api' => 'https://api.web2m.com/historyapivcbv3/Password/AccountNumber/Token'
        ],
        'VCB' => [
            'api' => 'https://api.web2m.com/historyapivcbv3/Password/AccountNumber/Token'
        ],
        'MB' => [
            'api' => 'https://api.web2m.com/historyapimbv3/Password/AccountNumber/Token'
        ],
        'MBBank' => [
            'api' => 'https://api.web2m.com/historyapimbv3/Password/AccountNumber/Token'
        ],
        'TPBank' => [
            'api' => 'https://api.web2m.com/historyapitpbv3/Token'
        ],
        'Techcombank' => [
            'api' => 'https://api.web2m.com/historyapitcbv3/Password/AccountNumber/Token'
        ],
        'TCB' => [
            'api' => 'https://api.web2m.com/historyapitcbv3/Password/AccountNumber/Token'
        ],
        'ACB' => [
            'api' => 'https://api.web2m.com/historyapiacbv3/Password/AccountNumber/Token'
        ],
        'BIDV' => [
            'api' => 'https://api.web2m.com/historyapibidvv3/Password/AccountNumber/Token'
        ],
    ];

    // Lấy danh sách tất cả các ngân hàng đang hoạt động
    foreach($CMSNT->get_list(" SELECT * FROM `banks` WHERE `status` = 1 AND `token` != '' ") as $bank){

        // Nếu short_name không có trong danh sách API thì bỏ qua (không phân biệt hoa thường)
        $config_keys_lower = array_change_key_case($config_list_api_web2m, CASE_LOWER);
        if (!isset($config_keys_lower[strtolower($bank['short_name'])])) {
            continue;
        }
        $api_url = $config_keys_lower[strtolower($bank['short_name'])]['api']; // Lấy URL API từ config
        // Thay thế các placeholder trong URL API
        if(strpos($api_url, 'Password') !== false){
            $api_url = str_replace('Password', $bank['password'], $api_url);
        }
        if(strpos($api_url, 'AccountNumber') !== false){
            $api_url = str_replace('AccountNumber', $bank['accountNumber'], $api_url);
        }
        if(strpos($api_url, 'Token') !== false){
            $api_url = str_replace('Token', $bank['token'], $api_url);
        }
        // Thay thế các placeholder trong URL API

        // Gọi API
        $result = curl_get($api_url);

        // Nếu debug bật thì hiển thị kết quả từ API
        if($CMSNT->site('debug_auto_bank') == 1){
            echo $result;
        }
        $result = json_decode($result, true);
        if($result['status'] != true){
            continue;
        }
        foreach ($result['transactions'] as $data) {
            $tid            = check_string($data['transactionID']);
            $description    = check_string($data['description']);
            $amount         = check_string($data['amount']);
            $type           = check_string($data['type']);

            // Nếu là giao dịch rút tiền thì bỏ qua
            if($type == 'OUT'){
                continue;
            }

            // Tìm hoá đơn đã thanh toán trước đó hay chưa, nếu có thì bỏ qua
            if($CMSNT->num_rows(" SELECT * FROM `payment_bank_invoice` WHERE `api_tid` = '$tid' AND `api_desc` = '$description' AND `short_name` = '".$bank['short_name']."' ") > 0){
                continue;
            }
            // Xử lý những bill đủ điều kiện
            foreach (whereInvoiceWaiting($bank['short_name'], $amount) as $invoice) {
                // Tìm kiếm nội dung chuyển tiền trong hóa đơn xem có trans_id không
                if (isset(explode($invoice['trans_id'], strtoupper($description))[1])) {
                    // Cập nhật trạng thái và thông tin hóa đơn
                    $isUpdate = $CMSNT->update("payment_bank_invoice", [
                        'status'        => 'completed',
                        'api_tid'       => $tid,
                        'api_desc'      => $description,
                        'api_type'      => 'WEB2M',
                        'updated_at'    => gettime()
                    ], " `id` = '".$invoice['id']."' AND `status` = 'waiting' ");
                    if($isUpdate){
                        // Xử lý cộng tiền cho tài khoản User đủ điều kiện
                        $isCong = $user->AddCredits($invoice['user_id'], $invoice['received'], __('Thanh toán hoá đơn nạp tiền').' #'.$invoice['trans_id'], 'INVOICE_'.$invoice['trans_id']);
                        if($isCong){
                            // LẤY THÔNG TIN USER
                            $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".$invoice['user_id']."' ");
                            // CỘNG HOA HỒNG
                            if($CMSNT->site('affiliate_status') == 1 && $getUser['ref_id'] != 0){
                                $ck = $CMSNT->site('affiliate_ck');
                                if(getRowRealtime('users', $getUser['ref_id'], 'ref_ck') != 0){
                                    $ck = getRowRealtime('users', $getUser['ref_id'], 'ref_ck');
                                }
                                $price = $invoice['received'] * $ck / 100;
                                $user->AddCommission($getUser['ref_id'], $getUser['id'], $price, __('Hoa hồng thành viên'.' '.$getUser['username']));
                            }
                            // XỬ LÝ TIỀN NỢ NẾU CÓ
                            debit_processing($getUser['id']);
                            // TẠO LOG GIAO DỊCH GẦN ĐÂY
                            $CMSNT->insert('deposit_log',[
                                'user_id'       => $invoice['user_id'],
                                'method'        => $bank['short_name'],
                                'amount'        => $invoice['amount'],
                                'received'      => $invoice['received'],
                                'create_time'   => time(),
                                'is_virtual'    => 0
                            ]);
                            // GỬI THÔNG BÁO CHO ADMIN
                            $my_text = $CMSNT->site('noti_recharge');
                            $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
                            $my_text = str_replace('{title}', $CMSNT->site('title'), $my_text);
                            $my_text = str_replace('{trans_id}', $invoice['trans_id'], $my_text);
                            $my_text = str_replace('{username}', getRowRealtime('users', $invoice['user_id'], 'username'), $my_text);
                            $my_text = str_replace('{method}', $bank['short_name'], $my_text);
                            $my_text = str_replace('{amount}', format_currency($invoice['amount']), $my_text);
                            $my_text = str_replace('{price}', format_currency($invoice['received']), $my_text);
                            $my_text = str_replace('{time}', gettime(), $my_text);
                            sendMessAdmin($my_text);
                            echo '[<b style="color:green">-</b>] Xử lý thành công 1 hoá đơn.'.PHP_EOL;
                        }
                    }
                }
            }
        }
    }
    
    // CẬP NHẬT TRẠNG THÁI HÓA ĐƠN HẾT THỜI GIAN
    $CMSNT->update('payment_bank_invoice', [
        'status'    => 'expired'
    ], " `status` = 'waiting' AND ".time()." - `create_time` > ".$CMSNT->site('bank_expired_invoice')." ");


    curl_get2(base_url('cron/cron.php'));
 
    