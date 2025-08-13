<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

/**
 * Database Schema cho SMMPANEL2
 * Sử dụng cho AI Chat để hiểu cấu trúc database và tạo SQL queries
 */

class DatabaseSchema {
    
    public static function getSchema() {
        return [
            // Bảng người dùng
            'users' => [
                'description' => 'Bảng quản lý thông tin người dùng, admin, ctv',
                'fields' => [
                    'id' => 'ID người dùng (Primary Key)',
                    'username' => 'Tên đăng nhập',
                    'password' => 'Mật khẩu (đã mã hóa)',
                    'email' => 'Email người dùng',
                    'fullname' => 'Họ tên đầy đủ',
                    'phone' => 'Số điện thoại',
                    'admin' => 'Quyền admin (0: user, >0: admin, 99999: super admin)',
                    'ctv' => 'Quyền cộng tác viên (0: không, 1: có)',
                    'banned' => 'Trạng thái bị cấm (0: bình thường, 1: bị cấm)',
                    'reason_banned' => 'Lý do bị cấm',
                    'create_date' => 'Ngày tạo tài khoản',
                    'update_date' => 'Ngày cập nhật cuối',
                    'money' => 'Số dư hiện tại (VND)',
                    'total_money' => 'Tổng số tiền đã nạp',
                    'debit' => 'Số tiền user còn nợ hệ thống',
                    'discount' => 'Chiết khấu giảm giá',
                    'ref_id' => 'ID người giới thiệu',
                    'ref_ck' => 'Phần trăm chiết khấu affiliate',
                    'ref_amount' => 'Tổng tiền hoa hồng affiliate',
                    'api_key' => 'API key cá nhân',
                    'rank_id' => 'ID rank/cấp bậc'
                ]
            ],
            
            // Bảng đơn hàng
            'orders' => [
                'description' => 'Bảng quản lý đơn hàng dịch vụ SMM',
                'fields' => [
                    'id' => 'ID đơn hàng (Primary Key)',
                    'trans_id' => 'Mã giao dịch duy nhất',
                    'user_id' => 'ID người dùng đặt hàng',
                    'supplier_id' => 'ID nhà cung cấp trong table suppliers',
                    'service_id' => 'ID dịch vụ trong table services',
                    'service_name' => 'Tên dịch vụ',
                    'link' => 'Link/URL cần tăng tương tác',
                    'quantity' => 'Số lượng đặt hàng',
                    'price' => 'Giá đơn hàng',
                    'pay' => 'Số tiền đã thanh toán',
                    'cost' => 'Giá vốn',
                    'status' => 'Trạng thái đơn hàng (Pending, In progress, Processing, Completed, Canceled, Partial)',
                    'reason' => 'Lý do (nếu bị hủy)',
                    'created_at' => 'Ngày tạo đơn',
                    'updated_at' => 'Ngày cập nhật cuối',
                    'order_id' => 'ID đơn hàng từ supplier',
                    'start_count' => 'Số lượng ban đầu',
                    'remains' => 'Số lượng còn lại',
                    'order_source' => 'Nguồn đặt hàng (web, api)',
                    'refill' => 'Hỗ trợ refill (true/false)',
                    'cancel' => 'Có thể hủy (0/1)'
                ]
            ],
            
            // Bảng dịch vụ
            'services' => [
                'description' => 'Bảng quản lý các dịch vụ SMM (Like, Follow, View, Comment...)',
                'fields' => [
                    'id' => 'ID dịch vụ (Primary Key)',
                    'category_id' => 'ID danh mục',
                    'supplier_id' => 'ID nhà cung cấp',
                    'name' => 'Tên dịch vụ',
                    'description' => 'Mô tả dịch vụ',
                    'type' => 'Loại dịch vụ (Default, Custom Comments, Package, Custom Comments Package, Subscriptions, etc.) trong table smm_service_types',
                    'price' => 'Giá bán cho user thường',
                    'price_1' => 'Giá bán cho rank 1',
                    'price_2' => 'Giá bán cho rank 2', 
                    'price_3' => 'Giá bán cho rank 3',
                    'cost' => 'Giá vốn',
                    'min' => 'Số lượng mua tối thiểu',
                    'max' => 'Số lượng mua tối đa',
                    'display' => 'Hiển thị (show/hide)',
                    'created_at' => 'Ngày tạo',
                    'updated_at' => 'Ngày cập nhật',
                    'refill' => 'Hỗ trợ refill',
                    'cancel' => 'Có thể hủy',
                    'api_id' => 'ID dịch vụ từ API supplier'
                ]
            ],
            
            // Bảng danh mục
            'categories' => [
                'description' => 'Bảng quản lý danh mục dịch vụ (Facebook, TikTok, Instagram...)',
                'fields' => [
                    'id' => 'ID danh mục (Primary Key)',
                    'parent_id' => 'ID danh mục cha (0 nếu là danh mục gốc)',
                    'supplier_id' => 'ID nhà cung cấp',
                    'name' => 'Tên danh mục',
                    'title' => 'Tiêu đề SEO',
                    'description' => 'Mô tả danh mục',
                    'slug' => 'Đường dẫn thân thiện',
                    'icon' => 'Icon danh mục',
                    'status' => 'Trạng thái (show/hide)',
                    'created_at' => 'Ngày tạo',
                    'updated_at' => 'Ngày cập nhật'
                ]
            ],
            
            // Bảng giao dịch tiền
            'dongtien' => [
                'description' => 'Bảng lịch sử giao dịch tiền (nạp, trừ, hoàn tiền)',
                'fields' => [
                    'id' => 'ID giao dịch (Primary Key)',
                    'user_id' => 'ID người dùng',
                    'sotientruoc' => 'Số dư trước giao dịch',
                    'sotienthaydoi' => 'Số tiền thay đổi (+/-)',
                    'sotiensau' => 'Số dư sau giao dịch',
                    'thoigian' => 'Thời gian giao dịch',
                    'noidung' => 'Nội dung giao dịch',
                    'transid' => 'Mã giao dịch liên quan'
                ]
            ],
            
            // Bảng thẻ cào
            'cards' => [
                'description' => 'Bảng quản lý giao dịch thẻ cào',
                'fields' => [
                    'id' => 'ID giao dịch thẻ (Primary Key)',
                    'user_id' => 'ID người dùng',
                    'trans_id' => 'Mã giao dịch',
                    'telco' => 'Nhà mạng (Viettel, Mobifone, Vinaphone)',
                    'amount' => 'Mệnh giá thẻ',
                    'price' => 'Tiền thực nhận',
                    'serial' => 'Số serial thẻ',
                    'pin' => 'Mã PIN thẻ',
                    'status' => 'Trạng thái (pending, success, failed)',
                    'create_date' => 'Ngày tạo',
                    'update_date' => 'Ngày cập nhật',
                    'reason' => 'Lý do (nếu thất bại)'
                ]
            ],
            
            // Bảng affiliate
            'aff_log' => [
                'description' => 'Bảng lịch sử hoa hồng affiliate',
                'fields' => [
                    'id' => 'ID log (Primary Key)',
                    'user_id' => 'ID người nhận hoa hồng',
                    'reason' => 'Lý do nhận hoa hồng',
                    'sotientruoc' => 'Số dư affiliate trước',
                    'sotienthaydoi' => 'Số tiền hoa hồng',
                    'sotienhientai' => 'Số dư affiliate sau',
                    'create_gettime' => 'Thời gian tạo'
                ]
            ],
            
            // Bảng rút tiền affiliate
            'aff_withdraw' => [
                'description' => 'Bảng yêu cầu rút tiền affiliate',
                'fields' => [
                    'id' => 'ID yêu cầu rút (Primary Key)',
                    'user_id' => 'ID người rút',
                    'trans_id' => 'Mã giao dịch',
                    'bank' => 'Tên ngân hàng',
                    'stk' => 'Số tài khoản',
                    'name' => 'Tên chủ tài khoản',
                    'amount' => 'Số tiền rút',
                    'status' => 'Trạng thái (pending, approved, rejected)',
                    'create_gettime' => 'Ngày yêu cầu',
                    'update_gettime' => 'Ngày xử lý',
                    'reason' => 'Lý do (nếu từ chối)'
                ]
            ],
            
            // Bảng nhà cung cấp
            'suppliers' => [
                'description' => 'Bảng quản lý nhà cung cấp dịch vụ',
                'fields' => [
                    'id' => 'ID supplier (Primary Key)',
                    'name' => 'Tên nhà cung cấp',
                    'domain' => 'Tên miền API',
                    'api_key' => 'API key',
                    'status' => 'Trạng thái (active/inactive)',
                    'balance' => 'Số dư tài khoản',
                    'create_gettime' => 'Ngày tạo',
                    'update_gettime' => 'Ngày cập nhật'
                ]
            ],
            
            // Bảng logs
            'logs' => [
                'description' => 'Bảng lưu hoạt động của users - TÊN CỘT IP LÀ "ip" KHÔNG PHẢI "ip_address"',
                'fields' => [
                    'id' => 'ID log (Primary Key)',
                    'user_id' => 'ID người dùng',
                    'ip' => 'Địa chỉ IP (TÊN CỘT: ip)',
                    'device' => 'Thông tin thiết bị',
                    'createdate' => 'Thời gian tạo (TÊN CỘT: createdate)',
                    'action' => 'Hành động thực hiện'
                ]
            ],
            
            // Bảng cấu hình
            'settings' => [
                'description' => 'Bảng cấu hình hệ thống',
                'fields' => [
                    'id' => 'ID setting (Primary Key)',
                    'name' => 'Tên cấu hình',
                    'value' => 'Giá trị cấu hình',
                    'note' => 'Ghi chú'
                ]
            ],
            
            // Bảng thanh toán ngân hàng
            'payment_bank_invoice' => [
                'description' => 'Bảng hóa đơn thanh toán qua ngân hàng (chuyển khoản)',
                'fields' => [
                    'id' => 'ID hóa đơn (Primary Key)',
                    'user_id' => 'ID người dùng',
                    'trans_id' => 'Mã giao dịch duy nhất',
                    'amount' => 'Số tiền nạp',
                    'received' => 'Số tiền thực tế nhận được',
                    'bank_name' => 'Tên ngân hàng',
                    'bank_number' => 'Số tài khoản ngân hàng',
                    'bank_holder' => 'Tên chủ tài khoản',
                    'content' => 'Nội dung chuyển khoản',
                    'status' => 'Trạng thái (pending, success, failed)',
                    'created_at' => 'Thời gian tạo',
                    'updated_at' => 'Thời gian cập nhật',
                    'note' => 'Ghi chú admin',
                    'image' => 'Ảnh biên lai chuyển khoản'
                ]
            ],
            
            // Bảng thanh toán crypto
            'payment_crypto' => [
                'description' => 'Thanh toán bằng tiền điện tử (Bitcoin, USDT, Ethereum)',
                'fields' => [
                    'id' => 'ID tự động tăng',
                    'trans_id' => 'Mã giao dịch',
                    'user_id' => 'ID người dùng',
                    'request_id' => 'ID yêu cầu từ API',
                    'amount' => 'Số tiền giao dịch (crypto)',
                    'received' => 'Số tiền thực nhận (VND)',
                    'create_gettime' => 'Thời gian tạo',
                    'update_gettime' => 'Thời gian cập nhật',
                    'status' => 'Trạng thái (waiting, completed, failed)',
                    'msg' => 'Thông báo từ API',
                    'url_payment' => 'Link thanh toán'
                ]
            ],
            
            // Bảng log affiliate/referral
            'log_ref' => [
                'description' => 'Nhật ký chi tiết hoa hồng affiliate/referral',
                'fields' => [
                    'id' => 'ID tự động tăng',
                    'user_id' => 'ID người dùng nhận hoa hồng',
                    'reason' => 'Lý do nhận hoa hồng',
                    'sotientruoc' => 'Số tiền trước khi thay đổi',
                    'sotienthaydoi' => 'Số tiền thay đổi',
                    'sotienhientai' => 'Số tiền hiện tại',
                    'create_gettime' => 'Thời gian tạo'
                ]
            ],
            
            // Bảng theo dõi log truy cập bất thường
            'failed_attempts' => [
                'description' => 'Theo dõi các log truy cập bất thường để bảo mật - TÊN CỘT IP LÀ "ip_address"',
                'fields' => [
                    'id' => 'ID tự động tăng',
                    'ip_address' => 'Địa chỉ IP (TÊN CỘT: ip_address)',
                    'attempts' => 'Số lần thử không thành công',
                    'create_gettime' => 'Thời gian tạo (TÊN CỘT: create_gettime)',
                    'type' => 'Loại đăng nhập (ADMIN, USER, API)'
                ]
            ],
            
            // Bảng định nghĩa loại dịch vụ SMM (chưa tồn tại - cần thêm)
            'smm_service_types' => [
                'description' => 'Định nghĩa các loại dịch vụ SMM và validation rules',
                'fields' => [
                    'id' => 'ID tự động tăng',
                    'name' => 'Tên loại dịch vụ',
                    'description' => 'Mô tả loại dịch vụ',
                    'created_at' => 'Thời gian tạo',
                    'updated_at' => 'Thời gian cập nhật'
                ]
            ]
        ];
    }
    
    /**
     * Lấy thông tin về bảng cụ thể
     */
    public static function getTableInfo($tableName) {
        $schema = self::getSchema();
        return isset($schema[$tableName]) ? $schema[$tableName] : null;
    }
    
    /**
     * Lấy danh sách tất cả các bảng
     */
    public static function getAllTables() {
        return array_keys(self::getSchema());
    }
    
    /**
     * Tìm kiếm bảng và cột liên quan đến từ khóa
     */
    public static function searchRelatedTables($keyword) {
        $schema = self::getSchema();
        $results = [];
        $keyword = strtolower($keyword);
        
        foreach ($schema as $tableName => $tableInfo) {
            // Tìm trong tên bảng
            if (strpos(strtolower($tableName), $keyword) !== false) {
                $results[] = $tableName;
                continue;
            }
            
            // Tìm trong mô tả bảng
            if (strpos(strtolower($tableInfo['description']), $keyword) !== false) {
                $results[] = $tableName;
                continue;
            }
            
            // Tìm trong tên cột và mô tả cột
            $fields = isset($tableInfo['fields']) ? $tableInfo['fields'] : (isset($tableInfo['columns']) ? $tableInfo['columns'] : []);
            foreach ($fields as $columnName => $columnDesc) {
                if (strpos(strtolower($columnName), $keyword) !== false || 
                    strpos(strtolower($columnDesc), $keyword) !== false) {
                    if (!in_array($tableName, $results)) {
                        $results[] = $tableName;
                    }
                    break;
                }
            }
        }
        
        return $results;
    }
}

?>
