<?php

    define("IN_SITE", true);
    require_once(__DIR__.'/../../libs/db.php');
    require_once(__DIR__.'/../../libs/lang.php');
    require_once(__DIR__.'/../../libs/helper.php');
    require_once(__DIR__.'/../../config.php');
    require_once(__DIR__.'/../../libs/suppliers/smmpanel2.php');
    require_once(__DIR__.'/../../libs/database/users.php');
    $CMSNT = new DB();
   
    if(!isset($_GET['key'])){
        die(__('Vui lòng nhập Key Cron Job vào đường dẫn'));
    }
    if(isset($_GET['key']) && $_GET['key'] != $CMSNT->site('key_cron_job')){
        die(__('Key không hợp lệ'));
    }


    // Khởi tạo biến thống kê tổng
    $total_refill_requests = 0;
    $total_refill_success = 0;
    $total_refill_failed = 0;
    $total_status_checks = 0;
    $total_status_success = 0;
    $total_status_failed = 0;
    $start_time = microtime(true);

    // Hàm debug với timestamp
    function debug_log($message, $type = 'INFO') {
        if (DEBUG) {
            $timestamp = date('[Y-m-d H:i:s]');
            $icons = [
                'INFO' => 'ℹ️',
                'SUCCESS' => '✅',
                'ERROR' => '❌',
                'WARNING' => '⚠️',
                'PROCESS' => '🔄',
                'API' => '🌐',
                'DATA' => '📊'
            ];
            echo $timestamp . ' ' . ($icons[$type] ?? '•') . ' ' . $message . "<br>";
        }
    }

    debug_log("=== BẮT ĐẦU XỬ LÝ BẢO HÀNH SMMPANEL2 ===", 'PROCESS');

    if (time() > $CMSNT->site('time_cron_suppliers_SMMPANEL2_refil')) {
        if (time() - $CMSNT->site('time_cron_suppliers_SMMPANEL2_refil') < 5) {
            die('[ÉT O ÉT] Thao tác quá nhanh, vui lòng đợi');
        }
    }
    $CMSNT->update("settings", ['value' => time()], " `name` = 'time_cron_suppliers_SMMPANEL2_refil' ");
    debug_log("Đã cập nhật thời gian cron", 'SUCCESS');

    // Lấy tất cả nhà cung cấp SMMPANEL2
    $suppliers = $CMSNT->get_list(" SELECT * FROM `suppliers` WHERE `status` = 1 AND `type` = 'SMMPANEL2' ");
    debug_log("Tìm thấy " . count($suppliers) . " nhà cung cấp SMMPANEL2 hoạt động", 'DATA');
    
    foreach($suppliers as $supplier){
        debug_log("", 'INFO'); // Dòng trống để phân cách
        debug_log("🏢 ĐANG XỬ LÝ NHÀ CUNG CẤP", 'PROCESS');
        debug_log("├─ ID: " . $supplier['id'], 'INFO');
        debug_log("├─ Domain: " . $supplier['domain'], 'INFO');
        debug_log("└─ Proxy: " . ($supplier['proxy'] ? 'Có' : 'Không'), 'INFO');
        
        // Khởi tạo mảng lưu trữ order_id và biến đếm cho nhà cung cấp này
        $order_id_refil = [];
        $id_refil = [];
        $supplier_refill_new = 0;
        $supplier_refill_pending = 0;

        // Lấy đơn hàng cần tạo bảo hành mới (refill_status = 0)
        $orders_new = $CMSNT->get_list(" SELECT `order_id`, `refill_id`, `quantity`, `link` FROM `orders` WHERE `supplier_id` = '".$supplier['id']."' AND `status` = 'Completed' AND `refill` = 1 AND `refill_status` = 2 AND `refill_id` IS NULL ORDER BY RAND() LIMIT 50 ");
        
        // Lấy đơn hàng đang chờ kiểm tra trạng thái bảo hành (refill_status = 2)
        $orders_pending = $CMSNT->get_list(" SELECT `order_id`, `refill_id`, `quantity`, `link` FROM `orders` WHERE `supplier_id` = '".$supplier['id']."' AND `status` = 'Completed' AND `refill` = 1 AND `refill_status` = 2 AND `refill_id` IS NOT NULL ORDER BY RAND() LIMIT 50 ");
        
        debug_log("📊 THỐNG KÊ ĐơN HÀNG:", 'DATA');
        debug_log("├─ Cần tạo bảo hành mới: " . count($orders_new), 'INFO');
        debug_log("└─ Đang chờ kiểm tra trạng thái: " . count($orders_pending), 'INFO');
        
        // Phân loại đơn hàng cần tạo bảo hành mới
        foreach($orders_new as $order){
            $order_id_refil[] = $order['order_id'];
            $supplier_refill_new++;
            debug_log("├─ Đơn hàng #" . $order['order_id'] . " (SL: " . number_format($order['quantity']) . ") - Cần tạo bảo hành", 'INFO');
        }

        // Phân loại đơn hàng cần kiểm tra trạng thái
        foreach($orders_pending as $order){
            if(!empty($order['refill_id'])){
                $id_refil[] = $order['refill_id'];
                $supplier_refill_pending++;
                debug_log("├─ Đơn hàng #" . $order['order_id'] . " (Refill ID: " . $order['refill_id'] . ") - Kiểm tra trạng thái", 'INFO');
            }
        }

        // PHẦN 1: TẠO BẢO HÀNH MỚI
        if(!empty($order_id_refil)){
            debug_log("🔄 BƯỚC 1: TẠO BẢO HÀNH MỚI", 'PROCESS');
            debug_log("├─ Số lượng đơn hàng: " . count($order_id_refil), 'INFO');
            debug_log("├─ Order IDs: " . implode(', ', $order_id_refil), 'INFO');
            debug_log("└─ Gửi request đến API...", 'API');
            
            $api_start_time = microtime(true);
            $order_id_refil_string = implode(',', $order_id_refil);
            $result = refill_order_smmpanel2($supplier['domain'], $supplier['api_key'], $order_id_refil_string, $supplier['proxy']);
            $api_time = round((microtime(true) - $api_start_time) * 1000, 2);
            
            debug_log("API Response Time: " . $api_time . "ms", 'API');
            debug_log("Raw API Response: " . substr($result, 0, 200) . (strlen($result) > 200 ? '...' : ''), 'API');
            
            $result = json_decode($result, true);
            
            if(is_array($result)){
                debug_log("✅ API trả về " . count($result) . " kết quả", 'SUCCESS');
                $refil_success = 0;
                $refil_failed = 0;
                
                foreach($result as $item){
                    if(isset($item['order']) && isset($item['refill'])){
                        $order_id = $item['order'];
                        
                        if(isset($item['refill']['error'])){
                            debug_log("❌ Đơn #" . $order_id . " - Lỗi: " . $item['refill']['error'], 'ERROR');
                            $CMSNT->update("orders", [
                                'refill_status' => 3,
                                'refill_error'  => check_string($item['refill']['error']),
                                'updated_at'    => gettime()
                            ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $refil_failed++;
                            $total_refill_failed++;
                        }else{
                            $refill_id = check_string($item['refill']) ?? '';
                            debug_log("✅ Đơn #" . $order_id . " - Refill ID: " . $refill_id, 'SUCCESS');
                            $CMSNT->update("orders", [
                                'refill_status' => 2,
                                'refill_id'     => $refill_id,
                                'refill_error'  => '',
                                'updated_at'    => gettime()
                            ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $refil_success++;
                            $total_refill_success++;
                        }
                    }
                }
                
                debug_log("📊 KẾT QUẢ TẠO BẢO HÀNH:", 'DATA');
                debug_log("├─ Thành công: " . $refil_success, 'SUCCESS');
                debug_log("└─ Thất bại: " . $refil_failed, 'ERROR');
                
            } else {
                debug_log("❌ API trả về dữ liệu không hợp lệ", 'ERROR');
                debug_log("Response: " . $result, 'ERROR');
            }
            
            $total_refill_requests += count($order_id_refil);
        } else {
            debug_log("ℹ️ Không có đơn hàng nào cần tạo bảo hành mới", 'INFO');
        }

        // PHẦN 2: KIỂM TRA TRẠNG THÁI BẢO HÀNH
        if(!empty($id_refil)){
            debug_log("🔄 BƯỚC 2: KIỂM TRA TRẠNG THÁI BẢO HÀNH", 'PROCESS');
            debug_log("├─ Số lượng Refill ID: " . count($id_refil), 'INFO');
            debug_log("├─ Refill IDs: " . implode(', ', $id_refil), 'INFO');
            debug_log("└─ Gửi request đến API...", 'API');
            
            $api_start_time = microtime(true);
            $id_refil_string = implode(',', $id_refil);
            $result = get_refill_status_smmpanel2($supplier['domain'], $supplier['api_key'], $id_refil_string, $supplier['proxy']);
            $api_time = round((microtime(true) - $api_start_time) * 1000, 2);
            
            debug_log("API Response Time: " . $api_time . "ms", 'API');
            debug_log("Raw API Response: " . substr($result, 0, 200) . (strlen($result) > 200 ? '...' : ''), 'API');
            
            $result = json_decode($result, true);
            
            if(is_array($result)){
                debug_log("✅ API trả về " . count($result) . " kết quả", 'SUCCESS');
                $refil_success2 = 0;
                $refil_failed2 = 0;
                $refil_pending2 = 0;
                
                foreach($result as $item){
                    if(isset($item['refill'])){
                        $refill_id = check_string($item['refill']);
                        
                        if(isset($item['status']['error'])){
                            debug_log("❌ Refill #" . $refill_id . " - Lỗi: " . $item['status']['error'], 'ERROR');
                            $CMSNT->update("orders", [
                                'refill_status' => 3,
                                'refill_error'  => check_string($item['status']['error']),
                                'updated_at'    => gettime()
                            ], " `refill_id` = '".$refill_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $refil_failed2++;
                            $total_status_failed++;
                        }
                        else if(isset($item['status']) && $item['status'] == 'Completed'){
                            debug_log("✅ Refill #" . $refill_id . " - Hoàn thành", 'SUCCESS');
                            $CMSNT->update("orders", [
                                'refill_status' => 1,
                                'refill_error'  => '',
                                'updated_at'    => gettime()
                            ], " `refill_id` = '".$refill_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $refil_success2++;
                            $total_status_success++;
                        }
                        else if(isset($item['status']) && $item['status'] == 'Rejected'){
                            debug_log("❌ Refill #" . $refill_id . " - Bị từ chối", 'ERROR');
                            $CMSNT->update("orders", [
                                'refill_status' => 3,
                                'refill_error'  => 'Rejected',
                                'updated_at'    => gettime()
                            ], " `refill_id` = '".$refill_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $refil_failed2++;
                            $total_status_failed++;
                        }
                        else if(isset($item['status']) && in_array($item['status'], ['Pending', 'Processing', 'In progress', 'tasks.status.inprogress'])){
                            debug_log("⏳ Refill #" . $refill_id . " - Đang xử lý (" . $item['status'] . ")", 'WARNING');
                            $refil_pending2++;
                        }
                        else{
                            debug_log("ℹ️ Refill #" . $refill_id . " - Trạng thái không xác định: " . ($item['status'] ?? 'N/A'), 'WARNING');
                            $refil_pending2++;
                        }
                    }
                }
                
                debug_log("📊 KẾT QUẢ KIỂM TRA TRẠNG THÁI:", 'DATA');
                debug_log("├─ Hoàn thành: " . $refil_success2, 'SUCCESS');
                debug_log("├─ Thất bại: " . $refil_failed2, 'ERROR');
                debug_log("└─ Đang chờ: " . $refil_pending2, 'WARNING');
                
            } else {
                debug_log("❌ API trả về dữ liệu không hợp lệ", 'ERROR');
                debug_log("Response: " . $result, 'ERROR');
            }
            
            $total_status_checks += count($id_refil);
        } else {
            debug_log("ℹ️ Không có đơn hàng nào cần kiểm tra trạng thái", 'INFO');
        }
        
        debug_log("🏁 KẾT THÚC XỬ LÝ NHÀ CUNG CẤP #" . $supplier['id'], 'PROCESS');

    }

    // TỔNG KẾT CUỐI CÙNG
    $total_time = round(microtime(true) - $start_time, 2);
    debug_log("", 'INFO'); // Dòng trống
    debug_log("=== TỔNG KẾT XỬ LÝ BẢO HÀNH SMMPANEL2 ===", 'PROCESS');
    debug_log("📊 THỐNG KÊ TỔNG QUAN:", 'DATA');
    debug_log("├─ Số nhà cung cấp xử lý: " . count($suppliers), 'INFO');
    debug_log("├─ Tổng thời gian xử lý: " . $total_time . "s", 'INFO');
    debug_log("├─ Thời gian hoàn thành: " . date('Y-m-d H:i:s'), 'INFO');
    debug_log("└─ Tốc độ xử lý: " . round(($total_refill_requests + $total_status_checks) / max($total_time, 0.1), 2) . " đơn/giây", 'INFO');
    
    debug_log("📊 TẠO BẢO HÀNH MỚI:", 'DATA');
    debug_log("├─ Tổng số request: " . $total_refill_requests, 'INFO');
    debug_log("├─ Thành công: " . $total_refill_success, 'SUCCESS');
    debug_log("├─ Thất bại: " . $total_refill_failed, 'ERROR');
    debug_log("└─ Tỷ lệ thành công: " . ($total_refill_requests > 0 ? round(($total_refill_success / $total_refill_requests) * 100, 1) : 0) . "%", 'INFO');
    
    debug_log("📊 KIỂM TRA TRẠNG THÁI:", 'DATA');
    debug_log("├─ Tổng số kiểm tra: " . $total_status_checks, 'INFO');
    debug_log("├─ Hoàn thành: " . $total_status_success, 'SUCCESS');
    debug_log("├─ Thất bại: " . $total_status_failed, 'ERROR');
    debug_log("└─ Tỷ lệ thành công: " . ($total_status_checks > 0 ? round(($total_status_success / $total_status_checks) * 100, 1) : 0) . "%", 'INFO');
    
    debug_log("=== KẾT THÚC XỬ LÝ ===", 'SUCCESS');
