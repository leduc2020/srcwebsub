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

    /* START CHỐNG SPAM */
    if (time() > $CMSNT->site('check_time_cron_cron')) {
        if (time() - $CMSNT->site('check_time_cron_cron') < 3) {
            die('Thao tác quá nhanh, vui lòng thử lại sau!');
        }
    }
    $CMSNT->update("settings", [
        'value' => time()
    ], " `name` = 'check_time_cron_cron' ");


    // Thay đổi trạng thái hóa đơn Bank hết hạn
    $CMSNT->update("payment_bank_invoice", [
        'status' => 'expired'
    ], " `status` = 'pending' AND `created_at` <= NOW() - INTERVAL ".intval($CMSNT->site('bank_expired_invoice'))." SECOND ");

    // Thay đổi trạng thái hóa đơn Crypto hết hạn
    $CMSNT->update("payment_crypto", [
        'status' => 'expired'
    ], " `status` = 'waiting' AND `create_gettime` <= NOW() - INTERVAL 86400 SECOND ");


    // Tính toán Thời gian trung bình hoàn thành của 10 đơn hàng gần nhất
    foreach($CMSNT->get_list("SELECT * FROM `services` WHERE `display` = 'show' AND `auto_get_average_time` = 1 ") as $service){
        $recent_orders = $CMSNT->get_list("SELECT `created_at`, `updated_at`, `status` FROM `orders` WHERE `status` = 'completed' AND `service_id` = '".$service['id']."' AND `quantity` = 1000 ORDER BY `id` DESC LIMIT 10");
        if(count($recent_orders) > 0) {
            $total_completion_time = 0;
            $valid_orders = 0;
            foreach($recent_orders as $order) {
                $created_time = strtotime($order['created_at']);
                $updated_time = strtotime($order['updated_at']);
                // Chỉ tính những đơn hàng có thời gian hoàn thành hợp lệ
                if($updated_time > $created_time) {
                    $completion_time = $updated_time - $created_time; // Thời gian hoàn thành tính bằng giây
                    $total_completion_time += $completion_time;
                    $valid_orders++;
                }
            }
            if($valid_orders > 0) {
                $average_time = $total_completion_time / $valid_orders; // Thời gian trung bình tính bằng giây
                // Lưu kết quả
                $CMSNT->update('services', [
                    'average_time' => $average_time
                ], " `id` = '".$service['id']."' ");
            }
        }
    }

    


    // Task chỉ xử lý mỗi 24 giờ
    if (time() > $CMSNT->site('task_24h')) {
        if (time() - $CMSNT->site('task_24h') > 86400) {
            $CMSNT->update("settings", [
                'value' => time()
            ], " `name` = 'task_24h' ");

            // Dọn dẹp failed_attempts
            $isRemove = $CMSNT->remove('failed_attempts', " `create_gettime` <= NOW() - INTERVAL 1 DAY ");
            if($isRemove){
                $CMSNT->insert("logs", [
                    'user_id'     => 0, // 0 = log hệ thống
                    'action'      => __('Hệ thống thực hiện dọn dẹp failed_attempts sau mỗi 24 giờ'),
                    'createdate'  => gettime(),
                    'ip'          => myip(),
                    'device'      => getUserAgent()
                ]);
            }

            // Xóa file CMSNT.CO thừa
            if(is_dir(__DIR__.'/../CMSNT.CO')){
                deleteFolder(__DIR__.'/../CMSNT.CO');
                
                $CMSNT->insert("logs", [
                    'user_id'     => 0, // 0 = log hệ thống
                    'action'      => __('Hệ thống thực hiện xóa file rác'),
                    'createdate'  => gettime(),
                    'ip'          => myip(),
                    'device'      => getUserAgent()
                ]);
            }
        }
    }
    

    // XỬ LÝ CHILD PANEL HẾT HẠN
    foreach($CMSNT->get_list("SELECT * FROM `child_panels` WHERE `status` = 'Actived' AND `expired_at` < NOW() AND `user_id` != 0") as $childPanel){
        // LẤY THÔNG TIN USER
        $user = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '".$childPanel['user_id']."'");
        // GỬI THÔNG BÁO CHO USER KHI CHILD PANEL HẾT HẠN
        if($user['telegram_chat_id'] != '' && $CMSNT->site('telegram_noti_child_panel_expired') != ''){
            $content = $CMSNT->site('noti_user_child_panel_expired');
            $content = str_replace('{domain}', $childPanel['domain'], $content);
            $content = str_replace('{username}', $user['username'], $content);
            $content = str_replace('{email_user}', $user['email'], $content);
            $content = str_replace('{phone_user}', $user['phone'], $content);
            $content = str_replace('{ip}', myip(), $content);
            $content = str_replace('{time}', gettime(), $content);
            sendMessTelegram($content, '', $user['telegram_chat_id']);
        }
        // SET TRẠNG THÁI HẾT HẠN
        $CMSNT->update('child_panels', ['status' => 'Expired'], " `id` = '".$childPanel['id']."' ");
    }

    

    $CMSNT->remove('deposit_log', " ".time()." - `create_time` >= 604800 ");
    // $CMSNT->remove('order_log', " ".time()." - `create_time` >= 604800 ");


    // Tạo sitemap chuẩn SEO với đầy đủ thông tin
    $urls = array();

    // Thêm trang chủ với độ ưu tiên cao nhất
    $urls[] = array(
        'loc' => base_url(),
        'lastmod' => date('Y-m-d\TH:i:s+07:00'),
        'changefreq' => 'daily',
        'priority' => '1.0'
    );

    // Thêm các trang chính
    $urls[] = array(
        'loc' => base_url('services'),
        'lastmod' => date('Y-m-d\TH:i:s+07:00'),
        'changefreq' => 'daily',
        'priority' => '0.9'
    );

    // Thêm URL cho các nền tảng (platforms - categories có parent_id = 0)
    foreach($CMSNT->get_list(" SELECT *, created_at FROM categories WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC ") as $platform){
        $urls[] = array(
            'loc' => base_url('service/'.$platform['slug']),
            'lastmod' => date('Y-m-d\TH:i:s+07:00', strtotime($platform['created_at'])),
            'changefreq' => 'weekly',
            'priority' => '0.8'
        );
    }
    
    // Thêm URL cho các phân loại (categories - categories có parent_id > 0)
    foreach($CMSNT->get_list(" SELECT c.*, p.slug as platform_slug, c.created_at FROM categories c JOIN categories p ON c.parent_id = p.id WHERE c.status = 'show' AND c.parent_id > 0 ORDER BY c.stt DESC ") as $category){
        $urls[] = array(
            'loc' => base_url('service/'.$category['platform_slug'].'/'.$category['slug']),
            'lastmod' => date('Y-m-d\TH:i:s+07:00', strtotime($category['created_at'])),
            'changefreq' => 'weekly',
            'priority' => '0.7'
        );
    }
    
    // Thêm URL cho các dịch vụ (services)
    foreach($CMSNT->get_list(" SELECT s.*, c.slug as category_slug, p.slug as platform_slug, s.created_at FROM services s JOIN categories c ON s.category_id = c.id JOIN categories p ON c.parent_id = p.id WHERE s.display = 'show' ORDER BY s.stt DESC ") as $service){
        $urls[] = array(
            'loc' => base_url('service/'.$service['platform_slug'].'/'.$service['category_slug'].'/'.$service['id']),
            'lastmod' => date('Y-m-d\TH:i:s+07:00', strtotime($service['created_at'])),
            'changefreq' => 'monthly',
            'priority' => '0.6'
        );
    }

    // Giới hạn số lượng URL tối đa 50,000 (chuẩn sitemap)
    if(count($urls) > 50000) {
        $urls = array_slice($urls, 0, 50000);
    }

    // Tạo tệp XML chuẩn SEO
    $xml = new DOMDocument('1.0', 'UTF-8');
    $xml->formatOutput = true;
    
    // Tạo phần tử gốc <urlset> với đầy đủ namespace
    $urlset = $xml->createElement('urlset');
    $urlset->setAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $urlset->setAttribute('xmlns:xsi', 'http://www.w3.org/2001/XMLSchema-instance');
    $urlset->setAttribute('xsi:schemaLocation', 'http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd');
    
    // Thêm các URL với đầy đủ thông tin SEO
    foreach ($urls as $urlData) {
        $urlElement = $xml->createElement('url');
        
        // Thêm loc (URL)
        $locElement = $xml->createElement('loc', htmlspecialchars($urlData['loc']));
        $urlElement->appendChild($locElement);
        
        // Thêm lastmod (thời gian cập nhật cuối)
        $lastmodElement = $xml->createElement('lastmod', $urlData['lastmod']);
        $urlElement->appendChild($lastmodElement);
        
        // Thêm changefreq (tần suất thay đổi)
        $changefreqElement = $xml->createElement('changefreq', $urlData['changefreq']);
        $urlElement->appendChild($changefreqElement);
        
        // Thêm priority (độ ưu tiên)
        $priorityElement = $xml->createElement('priority', $urlData['priority']);
        $urlElement->appendChild($priorityElement);
        
        $urlset->appendChild($urlElement);
    }
    
    $xml->appendChild($urlset);
    
    // Lưu sitemap vào tệp
    $xml->save('../sitemap.xml');
    