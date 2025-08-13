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
    

    if (time() > $CMSNT->site('time_cron_suppliers_SMMPANEL2_history')) {
        if (time() - $CMSNT->site('time_cron_suppliers_SMMPANEL2_history') < 5) {
            die('[ÉT O ÉT ]Thao tác quá nhanh, vui lòng đợi');
        }
    }
    $CMSNT->update("settings", ['value' => time()], " `name` = 'time_cron_suppliers_SMMPANEL2_history' ");

    // Lấy tất cả nhà cung cấp SMMPANEL2
    $suppliers = $CMSNT->get_list(" SELECT * FROM `suppliers` WHERE `status` = 1 AND `type` = 'SMMPANEL2' ");
    if (DEBUG) echo "Tìm thấy " . count($suppliers) . " nhà cung cấp SMMPANEL2 cần xử lý<br>";
    
    foreach($suppliers as $supplier){
        if (DEBUG) echo "<br>🔄 Đang xử lý nhà cung cấp: (ID: " . $supplier['id'] . ")<br>";
        
        // Khởi tạo mảng lưu trữ order_id cần lấy lịch sử và order_id cần hủy
        $order_id_history = [];
        $order_id_cancel = [];
        // Lấy tất cả đơn hàng cần lấy lịch sử
        $orders = $CMSNT->get_list(" SELECT `order_id`, `cancel_status` FROM `orders` WHERE `supplier_id` = '".$supplier['id']."' AND (`status` = 'Pending' OR `status` = 'In progress' OR `status` = 'Processing') ORDER BY RAND() LIMIT 100 ");
        
        if (DEBUG) echo "📝 Tìm thấy " . count($orders) . " đơn hàng cần kiểm tra lịch sử<br>";
        
        foreach($orders as $order){
            // Lấy lịch sử đơn hàng
            $order_id_history[] = $order['order_id'];

            // Lấy tất cả order_id của dịch vụ cần hủy bỏ vào mảng
            if($order['cancel_status'] == 2){
                $order_id_cancel[] = $order['order_id'];
            }
        }
        
        // Lấy lịch sử đơn hàng
        if (!empty($order_id_history)) {
            if (DEBUG) echo "📊 Đang lấy lịch sử cho " . count($order_id_history) . " đơn hàng<br>";
            
            $order_id_history = implode(',', $order_id_history); // Thêm , ngăn cách mỗi order_id
            $result = get_history_smmpanel2($supplier['domain'], $supplier['api_key'], $order_id_history, $supplier['proxy']);
            $result = json_decode($result, true);
            
            if (DEBUG) echo "🔍 Kết quả API lịch sử: " . (is_array($result) ? count($result) . " đơn hàng" : "Lỗi") . "<br>";
            
            if(!isset($result['error'])){
                $completed_count = 0;
                $progress_count = 0;
                $canceled_count = 0;
                $partial_count = 0;
                
                foreach($result as $order_id => $item){
                    $status = isset($item['status']) ? check_string($item['status']) : 'Completed';
                    $start_count = isset($item['start_count']) ? intval(check_string($item['start_count'])) : 0;
                    $remains = isset($item['remains']) ? intval(check_string($item['remains'])) : 0;

                    // Xử lý đơn hành hoàn tất
                    if($status == 'Completed'){
                        $CMSNT->update("orders", [
                            'status'        => $status,
                            'start_count'   => $start_count,
                            'remains'       => $remains,
                            'updated_at'    => gettime()
                        ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        $completed_count++;
                    }
                    // Xử lý đơn hàng đang thực hiện
                    else if($status == 'In progress' || $status == 'Processing'){
                        $CMSNT->update("orders", [
                            'status'        => $status,
                            'start_count'   => $start_count,
                            'remains'       => $remains,
                            'updated_at'    => gettime()
                        ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        $progress_count++;
                    }
                    // Xử lý đơn hàng hoàn tiền toàn bộ
                    else if($status == 'Canceled'){
                        // Lấy chi tiết đơn hàng
                        $order = $CMSNT->get_row(" SELECT * FROM `orders` WHERE `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        // Cập nhật trạng thái đơn hàng
                        $isUpdate = $CMSNT->update("orders", [
                            'status'        => $status,
                            'remains'       => $remains,
                            'pay'           => 0,
                            'cost'          => 0,
                            'updated_at'    => gettime()
                        ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        if($isUpdate){
                            $User = new users();
                            // Hoàn tiền đơn hàng
                            $User->AddCredits($order['user_id'], $order['pay'], __('Hoàn tiền đơn hàng') . " #".$order['trans_id'], 'Canceled_'.$order['trans_id']);
                        }
                        $canceled_count++;
                    }
                    // Xử lý đơn hàng hoàn tiền một phần
                    else if($status == 'Partial'){
                        // Lấy chi tiết đơn hàng
                        $order = $CMSNT->get_row(" SELECT * FROM `orders` WHERE `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        // Lấy giá bán của số lượng còn lại
                        $price_remains = $order['price'] / $order['quantity']; // Lấy giá bán của 1 số lượng
                        $price_remains *= $remains; // Tính giá bán của số lượng còn lại
                        // Lấy giá vốn của số lượng còn lại
                        $cost_remains = $order['cost'] / $order['quantity']; // Lấy giá vốn của 1 số lượng
                        $cost_remains *= $remains; // Tính giá vốn của số lượng còn lại
                        // Cập nhật trạng thái đơn hàng
                        $isUpdate = $CMSNT->update("orders", [
                            'status'        => $status,
                            'remains'       => $remains,
                            'pay'           => $order['pay'] - $price_remains,
                            'cost'          => $order['cost'] - $cost_remains,
                            'updated_at'    => gettime()
                        ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                        if($isUpdate){
                            $User = new users();
                            // Hoàn tiền đơn hàng
                            $User->AddCredits($order['user_id'], $price_remains, __('Hoàn tiền một phần đơn hàng') . " #".$order['trans_id'], 'Partial_'.$order['trans_id']);
                        }
                        $partial_count++;
                    }
                }
                
                if (DEBUG) {
                    echo "✅ Completed: $completed_count | 🔄 In Progress: $progress_count | ❌ Canceled: $canceled_count | ⚠️ Partial: $partial_count<br>";
                }
            }else{
                // Nếu có lỗi thì hiển thị lỗi
                if (DEBUG) {
                    echo "❌ Lỗi API lịch sử: " . check_string($result['error']);
                    echo "<br>Order IDs: " . $order_id_history . "<br>";
                }
            }
        } else {
            if (DEBUG) echo "ℹ️ Không có đơn hàng nào cần lấy lịch sử<br>";
        }

        // Hủy đơn hàng
        if(!empty($order_id_cancel)){
            if (DEBUG) echo "🗑️ Đang hủy " . count($order_id_cancel) . " đơn hàng<br>";
            
            $order_id_cancel = implode(',', $order_id_cancel); // Thêm , ngăn cách mỗi order_id
            $result = cancel_order_smmpanel2($supplier['domain'], $supplier['api_key'], $order_id_cancel, $supplier['proxy']);
            $result = json_decode($result, true);
            
            if (DEBUG) echo "🔍 Kết quả API hủy đơn: " . (is_array($result) ? count($result) . " kết quả" : "Lỗi") . "<br>";
            
            // Xử lý kết quả hủy đơn
            if(is_array($result)){
                $cancel_success = 0;
                $cancel_failed = 0;
                
                foreach($result as $item){
                    if(isset($item['order']) && isset($item['cancel'])){
                        $order_id = $item['order'];
                        // Trường hợp hủy đơn thất bại
                        if(isset($item['cancel']['error'])){
                            // Cập nhật trạng thái hủy đơn thất bại
                            $CMSNT->update("orders", [
                                'cancel_status' => 3, // Hủy thất bại
                                'updated_at'    => gettime()
                            ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $cancel_failed++;
                        }else{
                            // Cập nhật trạng thái hủy đơn thành công
                            $CMSNT->update("orders", [
                                'cancel_status' => 1, // Đã hủy
                                'updated_at'    => gettime()
                            ], " `order_id` = '".$order_id."' AND `supplier_id` = '".$supplier['id']."' ");
                            $cancel_success++;
                        }
                    }
                }
                
                if (DEBUG) {
                    echo "✅ Hủy thành công: $cancel_success | ❌ Hủy thất bại: $cancel_failed<br>";
                }
            } else {
                if (DEBUG) echo "❌ Lỗi khi gọi API hủy đơn hàng<br>";
            }
        } else {
            if (DEBUG) echo "ℹ️ Không có đơn hàng nào cần hủy<br>";
        }

        
        
    }

    if (DEBUG) {
        echo "<br>=== TỔNG KẾT ===<br>";
        echo "Tổng số nhà cung cấp đã xử lý: " . count($suppliers) . "<br>";
        echo "Thời gian hoàn thành: " . date('Y-m-d H:i:s') . "<br>";
        echo "================<br>";
    }
