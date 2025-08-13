<?php

    define("IN_SITE", true);
    require_once(__DIR__.'/../../libs/db.php');
    require_once(__DIR__.'/../../libs/lang.php');
    require_once(__DIR__.'/../../libs/helper.php');
    require_once(__DIR__.'/../../config.php');
    require_once(__DIR__.'/../../libs/suppliers/smmpanel2.php');
    $CMSNT = new DB();

    if(!isset($_GET['key'])){
        die(__('Vui lòng nhập Key Cron Job vào đường dẫn'));
    }
    if(isset($_GET['key']) && $_GET['key'] != $CMSNT->site('key_cron_job')){
        die(__('Key không hợp lệ'));
    }

    if (time() > $CMSNT->site('time_cron_suppliers_SMMPANEL2')) {
        if (time() - $CMSNT->site('time_cron_suppliers_SMMPANEL2') < 5) {
            die('[ÉT O ÉT ]Thao tác quá nhanh, vui lòng đợi');
        }
    }
    $CMSNT->update("settings", ['value' => time()], " `name` = 'time_cron_suppliers_SMMPANEL2' ");

    // Lấy tất cả nhà cung cấp SMMPANEL2
    $suppliers = $CMSNT->get_list(" SELECT * FROM `suppliers` WHERE `status` = 1 AND `type` = 'SMMPANEL2' ");
    if (DEBUG) echo "Tìm thấy " . count($suppliers) . " nhà cung cấp SMMPANEL2 cần đồng bộ<br>";
    
    foreach($suppliers as $supplier){
        if (DEBUG) echo "<br>🔄 Đang xử lý nhà cung cấp:  (ID: " . $supplier['id'] . ")<br>";
        
        // CẬP NHẬT SỐ DƯ API
        if (DEBUG) echo "💰 Đang cập nhật số dư API...<br>";
        $result = get_balance_smmpanel2($supplier['domain'], $supplier['api_key'], $supplier['proxy']);
        $result = json_decode($result, true);
        if(!isset($result['error']) && isset($result['balance'])){
            $price = check_string($result['balance']).' '.check_string($result['currency']);
            $CMSNT->update('suppliers', [
                'price' => $price
            ], " `id` = '".$supplier['id']."' ");
            if (DEBUG) echo "✅ Cập nhật số dư: " . $price . "<br>";
        } else {
            if (DEBUG) echo "❌ Lỗi cập nhật số dư: " . ($result['error'] ?? 'Không xác định') . "<br>";
        }

        // CURL LẤY SẢN PHẨM
        if (DEBUG) echo "📦 Đang lấy danh sách sản phẩm từ API...<br>";
        $result = get_services_smmpanel2($supplier['domain'], $supplier['api_key'], $supplier['proxy']);
        $result = json_decode($result, true);
        if(!isset($result['error'])){
            if (DEBUG) echo "✅ Lấy được " . count($result) . " sản phẩm từ API<br>";
            
            $create_count = 0;
            $update_count = 0;
            $category_count = 0;
            
            foreach($result as $service){
                $service_id = check_string($service['service']);
                $service_name = check_string($service['name']);
                $service_desc = isset($service['desc']) ? $service['desc'] : NULL;
                $service_type = check_string($service['type']);
                $service_rate = check_string($service['rate']);
                $service_min = isset($service['min']) ? check_string($service['min']) : 1;
                $service_max = isset($service['max']) ? check_string($service['max']) : 1000000;
                $service_dripfeed = isset($service['dripfeed']) ? check_string($service['dripfeed']) : false;
                $service_refill = isset($service['refill']) ? check_string($service['refill']) : false;
                $service_cancel = isset($service['cancel']) ? check_string($service['cancel']) : false;
                $service_platform = isset($service['platform']) ? check_string($service['platform']) : NULL;
                $service_category = isset($service['category']) ? check_string($service['category']) : NULL;
                $category_id = 0; // Mặc định ID chuyên mục sẽ không có

                // TẠO CHUYÊN MỤC
                if($supplier['sync_category'] == 'ON'){
                    if(!$category_api = $CMSNT->get_row(" SELECT * FROM `categories` WHERE `name` = '$service_category'  ")){
                        $url_image = NULL;   // Ảnh mặc định nếu không có chuyên mục cha
                        $parent_id = -1;    // Mặc định khi chưa có chuyên mục cha hoặc không tìm thấy
                        if($service_platform != NULL){
                            // Tìm platform phù hợp từ $config_platform trong service_platform
                            $found_platform = null;
                            foreach($config_platform as $platform_key => $platform_value) {
                                if(stripos($service_platform, $platform_key) !== false) {
                                    $found_platform = $platform_value;
                                    break;
                                }
                            }
                            $get_platform = $found_platform ? $found_platform : $service_platform;
                            $parent_id = $CMSNT->get_row(" SELECT * FROM `categories` WHERE `name` LIKE '%$get_platform%' AND `parent_id` = 0 "); // Nếu API có chuyên mục cha
                            $parent_id = !empty($parent_id) ? $parent_id['id'] : -1;
                        } else if($service_category != NULL) {
                            // Nếu service_platform NULL, tìm platform từ service_category
                            $found_platform = null;
                            foreach($config_platform as $platform_key => $platform_value) {
                                if(stripos($service_category, $platform_key) !== false) {
                                    $found_platform = $platform_value;
                                    break;
                                }
                            }
                            if($found_platform) {
                                $get_platform = $found_platform;
                                $parent_id = $CMSNT->get_row(" SELECT * FROM `categories` WHERE `name` LIKE '%$get_platform%' AND `parent_id` = 0 "); // Tìm chuyên mục cha theo platform từ category
                                $parent_id = !empty($parent_id) ? $parent_id['id'] : -1;
                            }
                        }
                        $isInsert = $CMSNT->insert('categories', [
                            'parent_id'         => $parent_id,
                            'supplier_id'       => $supplier['id'],
                            'status'            => 'show',
                            'name'              => $service_category,
                            'slug'              => create_slug($service_category.$service_id),
                            'icon'              => $url_image,
                            'created_at'        => gettime()
                        ]);
                        if($isInsert){
                            $category_id = $CMSNT->get_row(" SELECT * FROM `categories` WHERE `name` = '$service_category' AND `supplier_id` = '".$supplier['id']."' ")['id'];
                            // if(DEBUG){
                            //     if (DEBUG) echo '<b style="color:red;">CREATE</b> - Tạo category '.$service_category.' thành công !<br>';
                            // }
                            $category_count++;
                        }
                    }else{
                        $category_id = $category_api['id']; // Lấy ID chuyên mục nếu đã tạo sẵn chuyên mục
                    }
                }

                // TÍNH GIÁ BÁN
                if($supplier['rate'] != 1){
                    $rate = $supplier['rate'];
                    $price = $service_rate * $rate;
                }else{
                    $price = $service_rate;
                }
                // NẾU DỊCH VỤ CHỈ MUA 1 LẦN 1 GÓI THÌ ĐỊNH DẠNG GIÁ BÁN
                if($service_type != 'Package' && $service_type != 'Subscriptions' && $service_type != 'Custom Comments Package'){
                    $price = $price / $supplier['format_price']; // Định dạng giá bán của API
                }
                $cost = $price;
                if($supplier['update_price'] == 'ON'){
                    // TÍNH % TĂNG GIÁ BÁN
                    $price    = $cost * $supplier['discount'] / 100; // Giá bán lẻ
                    $price_1  = $cost * $supplier['discount_price_1'] / 100; // Giá bán 1
                    $price_2  = $cost * $supplier['discount_price_2'] / 100; // Giá bán 2
                    $price_3  = $cost * $supplier['discount_price_3'] / 100; // Giá bán 3

                    // CẬP NHẬT GIÁ BÁN
                    $price   = $cost + $price; // Giá bán lẻ
                    $price_1 = $cost + $price_1; // Giá bán 1
                    $price_2 = $cost + $price_2; // Giá bán 2
                    $price_3 = $cost + $price_3; // Giá bán 3
                } else{
                    // GIỮ NGUYÊN GIÁ VỐN
                    $price   = $service_rate;
                    $price_1 = $service_rate;
                    $price_2 = $service_rate;
                    $price_3 = $service_rate;
                }

                // TẠO SERVICES
                if(!$product = $CMSNT->get_row(" SELECT * FROM `services` WHERE `api_id` = '$service_id' AND `supplier_id` = '".$supplier['id']."' ")){
                    if($supplier['sync_service'] == 'OFF'){
                        if (DEBUG) echo "❌ Đồng bộ dịch vụ đã bị tắt, vui lòng kiểm tra lại<br>";
                        continue;
                    }
                    // TÙY CHỈNH LỌC HTML TRONG TÊN MÔ TẢ
                    if($supplier['check_string_api'] == 'ON'){
                        $service_desc = check_string($service_desc);
                    }
                    // THÊM SẢN PHẨM
                    $CMSNT->insert('services', [
                        'user_id'           => $supplier['user_id'],
                        'category_id'       => $category_id,
                        'supplier_id'       => $supplier['id'],
                        'type'              => $service_type,
                        'name'              => $service_name,
                        'description'       => $service_desc,
                        'price'             => $price,
                        'price_1'           => $price_1,
                        'price_2'           => $price_2,
                        'price_3'           => $price_3,
                        'cost'              => $cost,
                        'min'               => $service_min,
                        'max'               => $service_max,
                        'created_at'        => gettime(),
                        'updated_at'        => gettime(),
                        'dripfeed'          => $service_dripfeed,
                        'refill'            => $service_refill,
                        'cancel'            => $service_cancel,
                        'api_id'            => $service_id,
                        'api_name'          => $service_name,
                        'api_time_update'   => time()
                    ]);
                    // if(DEBUG){
                    //     if (DEBUG) echo '<b style="color:red;">CREATE</b> - Tạo dịch vụ '.$service_name.' thành công !<br>';
                    // }
                    $create_count++;
                }
                else{
                    // CẬP NHẬT SẢN PHẨM

                    // TÍNH GIÁ BÁN
                    if($supplier['rate'] != 1){
                        $rate = $supplier['rate'];
                        $price = $service_rate * $rate;
                    }else{
                        $price = $service_rate;
                    }
                    // NẾU DỊCH VỤ CHỈ MUA 1 LẦN 1 GÓI THÌ ĐỊNH DẠNG GIÁ BÁN
                    if($service_type != 'Package' && $service_type != 'Subscriptions' && $service_type != 'Custom Comments Package'){
                        $price = $price / $supplier['format_price']; // Định dạng giá bán của API
                    }
                    $cost = $price;
                    if($supplier['update_price'] == 'ON'){
                        // TÍNH % TĂNG GIÁ BÁN
                        $price    = $cost * $supplier['discount'] / 100; // Giá bán lẻ
                        $price_1  = $cost * $supplier['discount_price_1'] / 100; // Giá bán 1
                        $price_2  = $cost * $supplier['discount_price_2'] / 100; // Giá bán 2
                        $price_3  = $cost * $supplier['discount_price_3'] / 100; // Giá bán 3

                        // CẬP NHẬT GIÁ BÁN
                        $price   = $cost + $price; // Giá bán lẻ
                        $price_1 = $cost + $price_1; // Giá bán 1
                        $price_2 = $cost + $price_2; // Giá bán 2
                        $price_3 = $cost + $price_3; // Giá bán 3
                    } else{
                        // GIỮ NGUYÊN GIÁ BÁN CŨ
                        $price   = $product['price'];
                        $price_1 = $product['price_1'];
                        $price_2 = $product['price_2'];
                        $price_3 = $product['price_3'];
                    }
                    $name = $service_name; // Tên dịch vụ gốc
                    // TÙY CHỈNH LỌC HTML TRONG TÊN MÔ TẢ
                    if($supplier['check_string_api'] == 'ON'){
                        $service_desc = check_string($service_desc);
                    }
                    $description = !empty($service_desc) ? $service_desc : $product['description']; // Nếu mô tả API không có thì lấy mô tả từ sản phẩm cũ
                    // NẾU TẮT ĐỒNG BỘ TÊN MÔ TẢ THÌ SẼ LẤY DỮ LIỆU TỪ DỊCH VỤ CŨ
                    if($supplier['update_name'] == 'OFF'){
                        $name = $product['name']; // Giữ nguyên tên dịch vụ
                        $description = $product['description']; // Giữ nguyên mô tả dịch vụ
                    }
                    // NẾU TẮT ĐỒNG BỘ MIN MAX THÌ SẼ LẤY DỮ LIỆU TỪ DỊCH VỤ CŨ
                    if($product['auto_sync_min_max'] == 0){
                        $service_min = $product['min'];
                        $service_max = $product['max'];
                    }
                    $CMSNT->update('services', [
                        'price'             => $price,
                        'price_1'           => $price_1,
                        'price_2'           => $price_2,
                        'price_3'           => $price_3,
                        'cost'              => $cost,
                        'min'               => $service_min,
                        'max'               => $service_max,
                        'name'              => $name,
                        'description'       => $description,
                        'api_name'          => $service_name,
                        'api_time_update'   => time()
                    ], " `id` = '".$product['id']."' ");
                    // if(DEBUG){
                    //     if (DEBUG) echo '<b style="color:green;">UPDATE</b> - dịch vụ '.$service_name.' thành công !<br>';
                    // }
                    $update_count++;
                }
            }
            
            if (DEBUG) {
                echo "📊 Thống kê cho nhà cung cấp ID " . $supplier['id'] . ":<br>";
                echo "✅ Tạo mới: $create_count services | 🔄 Cập nhật: $update_count services | 📁 Tạo category: $category_count<br>";
            }
            
            // NẾU TẮT ĐỒNG BỘ DỊCH VỤ THÌ SẼ BỎ QUA XÓA DỊCH VỤ
            if($supplier['sync_service'] == 'OFF') continue;
            // Xóa các services cũ không còn trong API (sau 15 phút)
            $old_services = $CMSNT->get_list(" SELECT * FROM `services` WHERE `supplier_id` = '".$supplier['id']."' AND ".time()." - `api_time_update` >= 900 ");
            if (!empty($old_services)) {
                if (DEBUG) echo "🗑️ Xóa " . count($old_services) . " services cũ không còn trong API<br>";
                $CMSNT->remove('services', " `supplier_id` = '".$supplier['id']."' AND ".time()." - `api_time_update` >= 900 ");
            }
        } else {
            if (DEBUG) echo "❌ Lỗi lấy danh sách sản phẩm: " . ($result['error'] ?? 'Không xác định') . "<br>";
        }
    }

    if (DEBUG) {
        echo "<br>=== TỔNG KẾT ĐỒNG BỘ ===<br>";
        echo "Tổng số nhà cung cấp đã xử lý: " . count($suppliers) . "<br>";
        echo "Thời gian hoàn thành: " . date('Y-m-d H:i:s') . "<br>";
        echo "========================<br>";
    }