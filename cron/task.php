<?php

    define("IN_SITE", true);
    require_once(__DIR__.'/../libs/db.php');
    require_once(__DIR__.'/../libs/lang.php');
    require_once(__DIR__.'/../libs/helper.php');
    require_once(__DIR__.'/../config.php');
    $CMSNT = new DB();
    $Mobile_Detect = new Mobile_Detect();
    
    if(!isset($_GET['key'])){
        die(__('Vui lòng nhập Key Cron Job vào đường dẫn'));
    }
    if(isset($_GET['key']) && $_GET['key'] != $CMSNT->site('key_cron_job')){
        die(__('Key không hợp lệ'));
    }


    /* START CHỐNG SPAM */
    if (time() > $CMSNT->site('check_time_cron_task')) {
        if (time() - $CMSNT->site('check_time_cron_task') < 3) {
            die('Thao tác quá nhanh, vui lòng thử lại sau!');
        }
    }
    $CMSNT->update("settings", [
        'value' => time()
    ], " `name` = 'check_time_cron_task' ");


    foreach($CMSNT->get_list(" SELECT * FROM `automations` ") as $task){

        // XÓA ĐƠN HÀNG ĐÃ BÁN
        if($task['type'] == 'delete_order'){
            // XÓA ĐƠN HÀNG ĐỦ THỜI GIAN
            $isRemove = $CMSNT->remove('orders', " ".time()." - UNIX_TIMESTAMP(created_at) >= ".$task['schedule']." ");
        }

        // XÓA LỊCH SỬ NẠP TIỀN
        if($task['type'] == 'delete_recharge_history'){
            $CMSNT->remove('payment_momo', " ".time()." - UNIX_TIMESTAMP(create_gettime) >= ".$task['schedule']." ");
            $CMSNT->remove('payment_bank_invoice', " ".time()." - UNIX_TIMESTAMP(created_at) >= ".$task['schedule']." ");
            $CMSNT->remove('payment_crypto', " ".time()." - UNIX_TIMESTAMP(create_gettime) >= ".$task['schedule']." ");
            $CMSNT->remove('payment_thesieure', " ".time()." - UNIX_TIMESTAMP(create_gettime) >= ".$task['schedule']." ");
        }

        // XÓA USER KHÔNG PHÁT SINH GIAO DỊCH
        if($task['type'] == 'delete_users_no_recharge'){
            // TÌM CÁC USER KHÔNG CÓ PHÁT SINH GIAO DỊCH VÀ ĐỦ THỜI GIAN
            foreach($CMSNT->get_list(" SELECT * FROM `users` WHERE ".time()." - UNIX_TIMESTAMP(create_date) >= ".$task['schedule']." AND `admin` = 0 AND `money` = 0 AND `total_money` = 0 ") as $user){
                // KIỂM TRA USER CÓ PHÁT SINH GIAO DỊCH KHÔNG
                $checkRecharge = $CMSNT->num_rows(" SELECT * FROM `dongtien` WHERE `user_id` = '".$user['id']."' ");
                if(isset($checkRecharge) && $checkRecharge == 0){
                    // XÓA USER KHÔNG CÓ CÓ PHÁT SINH GIAO DỊCH
                    $isRemove = $CMSNT->remove('users', " `id` = '".$user['id']."' ");
                }
            }
        }

        // XÓA NHẬT KÝ BOT TELEGRAM
        if($task['type'] == 'delete_telegram_log'){
            $CMSNT->remove('bot_telegram_logs', " ".time()." - UNIX_TIMESTAMP(created_at) >= ".$task['schedule']." ");
        }


    }

 