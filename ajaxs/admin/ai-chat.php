<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../libs/database_schema.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../models/is_admin.php');

header('Content-Type: application/json; charset=utf-8');

if (!isset($_POST['action'])) {
    die(json_encode(['success' => false, 'message' => 'Action not specified']));
}
if ($CMSNT->site('status_demo') == 1) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Chức năng này không khả dụng trong chế độ demo')
    ]);
    die($data);
}

$action = check_string($_POST['action']);

switch ($action) {
    case 'send_message':
        sendMessage();
        break;
    case 'save_chat':
        saveChatHistory();
        break;
    case 'load_history':
        loadChatHistory();
        break;
    case 'clear_history':
        clearChatHistory();
        break;
    case 'get_memory_status':
        getMemoryStatus();
        break;
    case 'toggle_memory':
        toggleMemoryStatus();
        break;
    case 'change_model':
        changeChatModel();
        break;
    default:
        die(json_encode(['success' => false, 'message' => 'Invalid action']));
}

function sendMessage() {
    global $CMSNT, $getUser;
    
    if (!isset($_POST['message']) || empty(trim($_POST['message']))) {
        die(json_encode(['success' => false, 'message' => 'Message is required']));
    }
    
    $message = trim(check_string($_POST['message']));
    
    // Sử dụng model từ cấu hình hệ thống
    $model = $CMSNT->site('chatgpt_model');
    if (empty($model)) {
        $model = 'gpt-3.5-turbo'; // fallback default
    }
    
    // Kiểm tra cấu hình API
    $api_key = $CMSNT->site('chatgpt_api_key');
    if (empty($api_key)) {
        die(json_encode([
            'success' => false, 
            'message' => 'Vui lòng cấu hình API Key OpenAI trong phần cài đặt'
        ]));
    }
    
    
    // Kiểm tra xem có phải là yêu cầu database query không
    $isDbQuery = isDatabaseQuery($message);
    
    if (DEBUG) {
        error_log("AI Chat Debug - Message: " . $message);
        error_log("AI Chat Debug - Is DB Query: " . ($isDbQuery ? 'YES' : 'NO'));
    }
    
    if ($isDbQuery) {
        $response = handleDatabaseQuery($message, $model);
        // Thêm debug info vào response nếu debug mode
        if (DEBUG) {
            $response['debug_info'] = array_merge($response['debug_info'] ?? [], [
                'detection' => 'Database Query Detected',
                'original_message' => $message,
                'is_db_query' => true
            ]);
        }
    } else {
        // Gọi API OpenAI thông thường
        $response = callOpenAIAPI($message, $model);
        // Thêm debug info vào response nếu debug mode
        if (DEBUG) {
            $response['debug_info'] = [
                'detection' => 'Normal Chat Query',
                'original_message' => $message,
                'is_db_query' => false,
                'tokens_used' => $response['tokens'] ?? 0
            ];
        }
    }
    
    if ($response['success']) {
        // Lưu vào database
        $CMSNT->insert("ai_chat_history", [
            'user_id' => $getUser['id'],
            'user_type' => 'admin',
            'message' => $message,
            'response' => $response['response'],
            'model' => $model,
            'tokens_used' => $response['tokens'] ?? 0,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        
        // Ghi log hoạt động
        $logAction = "Chat với AI: " . substr($message, 0, 50) . "...";
        // Đảm bảo UTF-8 encoding và loại bỏ ký tự không hợp lệ
        $logAction = mb_convert_encoding($logAction, 'UTF-8', 'UTF-8');
        $logAction = preg_replace('/[^\x00-\x7F\x80-\xFF]/', '', $logAction); // Loại bỏ ký tự 4-byte UTF-8
        
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => $logAction
        ]);
        
        die(json_encode($response));
    } else {
        die(json_encode($response));
    }
}

/**
 * Kiểm tra xem tin nhắn có phải là yêu cầu database query không
 */
function isDatabaseQuery($message) {
    $queryKeywords = [
        // Từ khóa tiếng Việt
        'thống kê', 'báo cáo', 'dữ liệu', 'xem dữ liệu', 'tìm kiếm', 'kiểm tra',
        'bao nhiêu', 'có bao nhiêu', 'tổng', 'đếm', 'liệt kê', 'danh sách',
        'người dùng', 'đơn hàng', 'dịch vụ', 'giao dịch', 'lịch sử',
        'top', 'cao nhất', 'thấp nhất', 'nhiều nhất', 'ít nhất',
        'hôm nay', 'tuần này', 'tháng này', 'năm nay',
        'phân tích', 'xu hướng', 'biểu đồ', 'doanh thu', 'lợi nhuận',
        // Thêm các từ khóa nhật ký số dư
        'nhật ký', 'lịch sử', 'số dư', 'giao dịch', 'biến động',
        'nạp tiền', 'rút tiền', 'cộng tiền', 'trừ tiền', 'balance',
        'cho tôi', 'xem', 'hiển thị', 'show', 'get', 'tìm',
        // Thêm số điện thoại pattern
        'của', 'user', 'tài khoản', 'account',
        
        // Từ khóa tiếng Anh
        'show me', 'list', 'count', 'total', 'sum', 'average', 'max', 'min',
        'users', 'orders', 'services', 'transactions', 'statistics',
        'report', 'data', 'analyze', 'find', 'search', 'query',
        'how many', 'top', 'highest', 'lowest', 'most', 'least',
        'balance', 'history', 'log', 'activity'
    ];
    
    $message_lower = strtolower($message);
    
    // Kiểm tra pattern số điện thoại (10-11 chữ số)
    if (preg_match('/\d{10,11}/', $message)) {
        return true;
    }
    
    foreach ($queryKeywords as $keyword) {
        if (strpos($message_lower, strtolower($keyword)) !== false) {
            return true;
        }
    }
    
    return false;
}

/**
 * Xử lý yêu cầu database query
 */
function handleDatabaseQuery($message, $model) {
    global $CMSNT;
    
    try {
        // Bước 1: Phân tích yêu cầu và tạo SQL query thông qua AI
        $sqlQuery = generateSQLQuery($message, $model);
        
        if (!$sqlQuery['success']) {
            return $sqlQuery;
        }
        
        $sql = $sqlQuery['sql'];
        
        // Bước 2: Kiểm tra tính an toàn của SQL query
        if (!isSafeSQLQuery($sql)) {
            return [
                'success' => false,
                'message' => '❌ **Từ chối thực thi:** Query chứa các lệnh không an toàn hoặc tên bảng sai.

📋 **SQL được tạo:**
```sql
' . $sql . '
```

💡 **Lý do có thể:**
- AI tạo câu lệnh không phải SELECT
- Chứa từ khóa bị cấm (INSERT, UPDATE, DELETE, DROP...)
- Sử dụng tên bảng sai (activity_logs, balance_logs, transaction_logs...)
- Có ký tự nguy hiểm

🔧 **Tên bảng đúng:**
- Log hoạt động: **logs** (cột createdate)
- Nhật ký số dư: **dongtien** (cột thoigian)
- Thông tin user: **users**
- Đơn hàng: **orders**

**Thử lại với tên bảng chính xác!**'
            ];
        }
        
        // Bước 3: Thực thi SQL query
        $queryResult = executeSQLQuery($sql);
        
        if (!$queryResult['success']) {
            return $queryResult;
        }
        
        // Bước 4: Format kết quả đẹp mắt thông qua AI
        $formattedResult = formatQueryResult($queryResult['data'], $message, $sql, $model);
        
        // Thêm debug info nếu cần
        if (DEBUG) {
            $formattedResult['debug_info'] = [
                'sql_generated' => $sql,
                'rows_returned' => $queryResult['count'],
                'tokens_used' => $sqlQuery['tokens'] ?? 0
            ];
        }
        
        return $formattedResult;
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => '❌ Lỗi xử lý: ' . $e->getMessage()
        ];
    }
}

/**
 * Tạo SQL query thông qua AI
 */
function generateSQLQuery($userMessage, $model) {
    global $CMSNT;
    
    // Lấy schema database
    $schema = DatabaseSchema::getSchema();
    $schemaText = "CẤU TRÚC DATABASE:\n";
    
    foreach ($schema as $tableName => $tableInfo) {
        $schemaText .= "\n📋 Bảng: $tableName - " . $tableInfo['description'] . "\n";
        foreach ($tableInfo['fields'] as $columnName => $columnDesc) {
            $schemaText .= "   • $columnName: $columnDesc\n";
        }
    }
    
    // Thêm thông tin thời gian hiện tại
    $currentTime = date('Y-m-d H:i:s');
    $currentDate = date('Y-m-d');
    $currentYear = date('Y');
    $currentMonth = date('m');
    $currentWeek = date('W');
    
    $systemPrompt = "⏰ **THỜI GIAN HIỆN TẠI:**
- Thời gian: $currentTime
- Ngày hôm nay: $currentDate  
- Năm hiện tại: $currentYear
- Tháng hiện tại: $currentMonth
- Tuần hiện tại: $currentWeek

🚨 QUY TẮC QUAN TRỌNG NHẤT - ĐỌC TRƯỚC KHI TẠO QUERY:
📋 TÊN BẢNG VÀ CỘT CHÍNH XÁC:
- Log hoạt động: 'logs' (cột IP: 'ip', cột thời gian: 'createdate')
- Nhật ký số dư: 'dongtien' (cột thời gian: 'thoigian')  
- Log bảo mật: 'failed_attempts' (cột IP: 'ip_address', cột thời gian: 'create_gettime')
- Thông tin user: 'users'
- Đơn hàng: 'orders'
- Dịch vụ: 'services'
- Danh mục: 'categories'
- Thẻ cào: 'cards'
- Cấu hình: 'settings'

⚠️ LƯU Ý CỘT IP:
- Bảng 'logs': cột IP là 'ip' (KHÔNG PHẢI 'ip_address')
- Bảng 'failed_attempts': cột IP là 'ip_address'

❌ TUYỆT ĐỐI KHÔNG DÙNG: activity_logs, balance_logs, transaction_logs, user_logs, log_activity, money_logs

Bạn là một Database Analyst chuyên nghiệp cho hệ thống SMM Panel.

🎯 NHIỆM VỤ: Tạo câu lệnh SQL SELECT an toàn dựa trên yêu cầu của user.

📚 THÔNG TIN DATABASE:
$schemaText

🔒 QUY TẮC AN TOÀN:
- CHỈ được tạo câu lệnh SELECT
- TUYỆT ĐỐI KHÔNG được dùng: INSERT, UPDATE, DELETE, DROP, TRUNCATE
- KHÔNG dùng comment (-- hoặc /* */)
- KHÔNG dùng dấu chấm phẩy (;) ở cuối
- Sử dụng LIMIT để giới hạn kết quả (tối đa 100 records) CHỈ KHI cần thiết
- Luôn thêm ORDER BY nếu có thể để sắp xếp logic

⚠️ QUY TẮC TÊN BẢNG VÀ CỘT (QUAN TRỌNG):
- Bảng logs: TÊN CHÍNH XÁC là 'logs' (KHÔNG PHẢI activity_logs, log_activity, user_logs)
- Cột thời gian trong logs: TÊN CHÍNH XÁC là 'createdate' (KHÔNG PHẢI timestamp, created_at, time)
- Các bảng có sẵn: users, orders, services, categories, dongtien, cards, aff_log, suppliers, logs, settings
- KHÔNG được bịa tên bảng không tồn tại!

📊 QUY TẮC THỐNG KÊ DOANH THU:
- Khi thống kê DOANH THU/LỢI NHUẬN: bao gồm các trạng thái: 'Completed', 'Pending', 'In progress', 'Processing'
- Chỉ loại trừ trạng thái: 'Canceled', 'Partial' (những đơn bị hủy hoặc thất bại)
- Sử dụng WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing')
- Đối với thống kê khác (không phải doanh thu): có thể lấy tất cả hoặc theo yêu cầu cụ thể

💰 QUY TẮC THỐNG KÊ NẠP TIỀN:
- Khi thống kê TỔNG NẠP/DEPOSIT của user: sử dụng cột 'total_money' trong bảng 'users'
- KHÔNG dùng bảng 'dongtien' để tính tổng nạp vì total_money đã tổng hợp sẵn
- Ví dụ: SELECT username, total_money FROM users ORDER BY total_money DESC LIMIT 10

📋 QUY TẮC THỐNG KÊ HOẠT ĐỘNG:
- Khi thống kê LOG/HOẠT ĐỘNG của user: CHỈ sử dụng bảng 'logs' 
- TUYỆT ĐỐI KHÔNG dùng: activity_logs, log_activity, user_logs, user_activities
- Cột thời gian: CHỈ dùng 'createdate' (KHÔNG dùng timestamp, created_at, time)
- Ví dụ ĐÚNG: SELECT * FROM logs WHERE user_id = 123 ORDER BY createdate DESC LIMIT 10
- Ví dụ SAI: SELECT * FROM activity_logs WHERE user_id = 123 ORDER BY timestamp DESC

💳 QUY TẮC THỐNG KÊ NHẬT KÝ SỐ DƯ:
- Khi thống kê NHẬT KÝ SỐ DƯ/GIAO DỊCH TIỀN: CHỈ sử dụng bảng 'dongtien'
- TUYỆT ĐỐI KHÔNG dùng: balance_logs, transaction_logs, money_logs, user_balance
- Cột thời gian: CHỈ dùng 'thoigian' (KHÔNG dùng timestamp, created_at)
- Ví dụ ĐÚNG: SELECT * FROM dongtien WHERE user_id = 123 ORDER BY thoigian DESC LIMIT 10
- Ví dụ SAI: SELECT * FROM balance_logs WHERE user_id = 123 ORDER BY timestamp DESC

💡 CÁC VÍ DỤ QUERY THÔNG DỤNG:
- Thống kê doanh thu: SELECT SUM(price) as total FROM orders WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing')
- Doanh thu hôm nay: SELECT SUM(price) as total FROM orders WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing') AND DATE(created_at) = '$currentDate'
- Doanh thu theo ngày: SELECT DATE(created_at) as date, SUM(price) as revenue FROM orders WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing') GROUP BY DATE(created_at) ORDER BY date DESC
- Doanh thu tháng này: SELECT SUM(price) as revenue FROM orders WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing') AND YEAR(created_at)=$currentYear AND MONTH(created_at)=$currentMonth
- Doanh thu theo tháng: SELECT MONTH(created_at) as month, SUM(price) as revenue FROM orders WHERE status IN ('Completed', 'Pending', 'In progress', 'Processing') AND YEAR(created_at)=$currentYear GROUP BY MONTH(created_at) ORDER BY month DESC
- Top user nạp nhiều nhất: SELECT username, total_money FROM users ORDER BY total_money DESC LIMIT 10
- Tổng tiền đã nạp hệ thống: SELECT SUM(total_money) as total_deposits FROM users
- Log hoạt động user: SELECT * FROM logs WHERE user_id = (SELECT id FROM users WHERE username = 'abc') ORDER BY createdate DESC LIMIT 10
- Nhật ký số dư user: SELECT * FROM dongtien WHERE user_id = (SELECT id FROM users WHERE username = 'abc') ORDER BY thoigian DESC LIMIT 10
- Top dịch vụ bán chạy: SELECT service_name, COUNT(*) as orders FROM orders GROUP BY service_name ORDER BY orders DESC LIMIT 10
- User hoạt động nhiều: SELECT u.username, COUNT(o.id) as total_orders FROM users u JOIN orders o ON u.id=o.user_id GROUP BY u.id ORDER BY total_orders DESC LIMIT 10

🔍 CÁC VÍ DỤ TÌM KIẾM THEO SỐ ĐIỆN THOẠI:
- Nhật ký số dư theo SĐT: SELECT d.*, u.username FROM dongtien d JOIN users u ON d.user_id = u.id WHERE u.username = '0947838128' ORDER BY d.thoigian DESC LIMIT 50
- Thông tin user theo SĐT: SELECT * FROM users WHERE username = '0947838128'
- Đơn hàng theo SĐT: SELECT * FROM orders WHERE user_id = (SELECT id FROM users WHERE username = '0947838128') ORDER BY created_at DESC LIMIT 50
- Log hoạt động theo SĐT: SELECT * FROM logs WHERE user_id = (SELECT id FROM users WHERE username = '0947838128') ORDER BY createdate DESC LIMIT 50

🌐 CÁC VÍ DỤ TÌM KIẾM THEO IP:
- Log hoạt động theo IP: SELECT * FROM logs WHERE ip = '103.200.23.68' ORDER BY createdate DESC LIMIT 50
- Log bảo mật theo IP: SELECT * FROM failed_attempts WHERE ip_address = '103.200.23.68' ORDER BY create_gettime DESC LIMIT 50

📱 LƯU Ý VỀ SỐ ĐIỆN THOẠI:
- Số điện thoại thường được lưu trong cột 'username' của bảng 'users'
- Khi user nhập số điện thoại, hãy tìm theo username
- Luôn JOIN với bảng users để lấy thông tin user_id cho các bảng khác

📝 ĐỊNH DẠNG TRẢ LỜI:
- Chỉ trả về câu lệnh SQL thuần túy
- KHÔNG giải thích, KHÔNG comment
- KHÔNG dùng markdown, KHÔNG dùng ```sql
- Một dòng SQL duy nhất

YÊU CẦU USER: $userMessage

🔴 LƯU Ý CUỐI CÙNG: 
- Nếu cần log hoạt động: CHỈ dùng bảng 'logs' với cột 'createdate'
- Nếu cần nhật ký số dư: CHỈ dùng bảng 'dongtien' với cột 'thoigian'
- Nếu cần thông tin user: CHỉ dùng bảng 'users' 
- Nếu cần đơn hàng: CHỉ dùng bảng 'orders'
- TUYỆT ĐỐI KHÔNG bịa tên bảng không có trong danh sách!

SQL Query:";

    $response = callOpenAIAPI($systemPrompt, $model, false);
    
    if (!$response['success']) {
        return [
            'success' => false,
            'message' => '❌ Không thể tạo SQL query: ' . $response['message']
        ];
    }
    
    $sql = trim($response['response']);
    
    // Loại bỏ các ký tự không cần thiết
    $sql = str_replace(['```sql', '```', '`'], '', $sql);
    $sql = trim($sql);
    
    // Loại bỏ comment và dấu chấm phẩy
    $sql = preg_replace('/--.*$/', '', $sql); // loại bỏ comment --
    $sql = preg_replace('/\/\*.*?\*\//', '', $sql); // loại bỏ comment /* */
    $sql = rtrim($sql, ';'); // loại bỏ dấu chấm phẩy cuối
    $sql = trim($sql);
    
    // Kiểm tra tên bảng sai ngay lập tức
    $sql_upper = strtoupper($sql);
    if (strpos($sql_upper, 'BALANCE_LOGS') !== false || 
        strpos($sql_upper, 'ACTIVITY_LOGS') !== false ||
        strpos($sql_upper, 'TRANSACTION_LOGS') !== false) {
        return [
            'success' => false,
            'message' => '❌ **AI đã tạo tên bảng sai!**

📋 **SQL được tạo:**
```sql
' . $sql . '
```

🔥 **Tên bảng chính xác:**
- Nhật ký số dư: **dongtien** (KHÔNG phải balance_logs)
- Log hoạt động: **logs** (KHÔNG phải activity_logs)
- Giao dịch: **dongtien** (KHÔNG phải transaction_logs)

**AI cần học lại tên bảng!**'
        ];
    }
    
    return [
        'success' => true,
        'sql' => $sql,
        'tokens' => $response['tokens'] ?? 0
    ];
}

/**
 * Kiểm tra tính an toàn của SQL query
 */
function isSafeSQLQuery($sql) {
    $sql_upper = strtoupper(trim($sql));
    
    // Chỉ cho phép SELECT statements
    if (!preg_match('/^SELECT\s+/', $sql_upper)) {
        return false;
    }
    
    // Danh sách các từ khóa nguy hiểm (chỉ những lệnh thực sự có thể làm hại dữ liệu)
    $dangerousKeywords = [
        'DELETE', 'UPDATE', 'INSERT', 'DROP', 'TRUNCATE'
    ];
    
    foreach ($dangerousKeywords as $keyword) {
        if (strpos($sql_upper, $keyword) !== false) {
            return false;
        }
    }
    
    // Kiểm tra tên bảng sai thường gặp
    $wrongTableNames = [
        'ACTIVITY_LOGS', 'LOG_ACTIVITY', 'USER_LOGS', 'USER_ACTIVITIES',
        'TRANSACTIONS', 'PAYMENTS', 'USER_TRANSACTIONS',
        'BALANCE_LOGS', 'TRANSACTION_LOGS', 'MONEY_LOGS', 'USER_BALANCE'
    ];
    
    foreach ($wrongTableNames as $wrongTable) {
        if (strpos($sql_upper, $wrongTable) !== false) {
            return false;
        }
    }
    
    return true;
}

/**
 * Thực thi SQL query an toàn
 */
function executeSQLQuery($sql) {
    global $CMSNT;
    
    try {
        // Kiểm tra xem có phải là query thống kê không
        $sql_upper = strtoupper($sql);
        $isStatisticalQuery = (
            strpos($sql_upper, 'SUM(') !== false ||
            strpos($sql_upper, 'COUNT(') !== false ||
            strpos($sql_upper, 'AVG(') !== false ||
            strpos($sql_upper, 'MAX(') !== false ||
            strpos($sql_upper, 'MIN(') !== false ||
            strpos($sql_upper, 'GROUP BY') !== false
        );
        
        // Chỉ thêm LIMIT nếu không phải query thống kê và chưa có LIMIT
        if (!$isStatisticalQuery && !preg_match('/LIMIT\s+\d+/i', $sql)) {
            $sql .= ' LIMIT 100';
        }
        
        $result = $CMSNT->get_list($sql);
        
        if ($result === false) {
            return [
                'success' => false,
                'message' => '❌ Lỗi thực thi SQL query'
            ];
        }
        
        return [
            'success' => true,
            'data' => $result,
            'count' => count($result),
            'sql' => $sql
        ];
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => '❌ Lỗi database: ' . $e->getMessage()
        ];
    }
}

/**
 * Format kết quả query đẹp mắt thông qua AI
 */
function formatQueryResult($data, $originalMessage, $sql, $model) {
    global $CMSNT;
    
    if (empty($data)) {
        $response = "📊 **KẾT QUẢ TRUY VẤN**\n\n❌ Không tìm thấy dữ liệu phù hợp với yêu cầu của bạn.";
        
        if (DEBUG) {
            $response .= "\n\n🔍 **SQL đã thực thi:**\n```sql\n$sql\n```";
        }
        
        return [
            'success' => true,
            'response' => $response,
            'tokens' => 0
        ];
    }
    
    // Chuyển dữ liệu thành JSON để gửi cho AI
    $dataJson = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    
    $systemPrompt = "Bạn là một Data Analyst chuyên nghiệp. Nhiệm vụ của bạn là phân tích và trình bày dữ liệu một cách đẹp mắt, dễ hiểu.

🎯 YÊU CẦU NGƯỜI DÙNG: $originalMessage

📊 DỮ LIỆU TRUY VẤN:
$dataJson

📝 HƯỚNG DẪN TRÌNH BÀY:
1. Bắt đầu với tiêu đề đẹp mắt sử dụng emoji phù hợp
2. Tóm tắt kết quả ngắn gọn
3. Trình bày dữ liệu dưới dạng bảng hoặc danh sách có format đẹp
4. Đưa ra phân tích, insights hay xu hướng nếu có
5. Không cần hiển thị SQL query

💡 SỬ DỤNG:
- Emoji phù hợp để trang trí
- **Bold** cho tiêu đề và số liệu quan trọng
- Bảng markdown nếu phù hợp
- Bullet points cho danh sách

🎨 STYLE: Chuyên nghiệp, thân thiện, dễ đọc, có cấu trúc rõ ràng.";

    $response = callOpenAIAPI($systemPrompt, $model, false);
    
    if (!$response['success']) {
        // Fallback: hiển thị dữ liệu dạng đơn giản
        $simpleFormat = "📊 **KẾT QUẢ TRUY VẤN**\n\n";
        $simpleFormat .= "✅ Tìm thấy **" . count($data) . "** kết quả\n\n";
        
        if (count($data) <= 10) {
            $simpleFormat .= "📋 **Chi tiết:**\n";
            foreach ($data as $index => $row) {
                $simpleFormat .= "\n**#" . ($index + 1) . ":**\n";
                foreach ($row as $key => $value) {
                    $simpleFormat .= "• $key: $value\n";
                }
            }
        } else {
            $simpleFormat .= "📋 **Mẫu dữ liệu (10 dòng đầu):**\n";
            for ($i = 0; $i < 10; $i++) {
                if (isset($data[$i])) {
                    $simpleFormat .= "\n**#" . ($i + 1) . ":**\n";
                    foreach ($data[$i] as $key => $value) {
                        $simpleFormat .= "• $key: $value\n";
                    }
                }
            }
        }
        
        // Chỉ hiển thị SQL khi debug mode
        if (DEBUG) {
            $simpleFormat .= "\n🔍 **SQL đã thực thi:**\n```sql\n$sql\n```";
        }
        
        return [
            'success' => true,
            'response' => $simpleFormat,
            'tokens' => 0
        ];
    }
    
    return [
        'success' => true,
        'response' => $response['response'],
        'tokens' => $response['tokens'] ?? 0
    ];
}

function getRecentChatHistory($limit = 5) {
    global $CMSNT, $getUser;
    
    $chatHistory = $CMSNT->get_list("
        SELECT * FROM `ai_chat_history` 
        WHERE `user_id` = '".$getUser['id']."' AND `user_type` = 'admin'
        ORDER BY `created_at` DESC 
        LIMIT $limit
    ");
    
    // Đảo ngược thứ tự để có thứ tự từ cũ đến mới
    return $chatHistory ? array_reverse($chatHistory) : [];
}

function buildMessagesWithMemory($currentMessage) {
    global $CMSNT;
    
    // Thêm thông tin thời gian hiện tại
    $currentTime = date('Y-m-d H:i:s');
    $currentDate = date('Y-m-d');
    $currentYear = date('Y');
    $currentMonth = date('m');
    $dayOfWeek = date('N'); // 1 = Monday, 7 = Sunday
    $weekDays = ['', 'Thứ Hai', 'Thứ Ba', 'Thứ Tư', 'Thứ Năm', 'Thứ Sáu', 'Thứ Bảy', 'Chủ Nhật'];
    $currentDayName = $weekDays[$dayOfWeek];
    
    // System prompt nâng cao với hỗ trợ database
    $systemPrompt = <<<EOT
⏰ **THỜI GIAN HIỆN TẠI:**
- Thời gian: $currentTime
- Ngày hôm nay: $currentDate ($currentDayName)
- Năm hiện tại: $currentYear
- Tháng hiện tại: $currentMonth

Bạn là một AI Assistant thông minh và hữu ích cho hệ thống SMM Panel. Hãy trả lời bằng tiếng Việt một cách chính xác, chi tiết và dễ hiểu.

🤖 **VAI TRÒ CỦA BẠN:**
- Hỗ trợ admin quản lý hệ thống SMM Panel
- Giải đáp thắc mắc về chức năng, cấu hình
- Hỗ trợ viết code, debug, tối ưu hệ thống
- Tư vấn về kinh doanh, marketing
- Giải thích các tính năng phức tạp một cách đơn giản

🚀 **TÍNH NĂNG DATABASE QUERY THÔNG MINH:**
- Tự động nhận diện khi user muốn xem/tìm/thống kê dữ liệu
- Phân tích cấu trúc database (users, orders, services, categories, cards, settings...)  
- Tạo SQL query an toàn (chỉ SELECT, không xóa/sửa dữ liệu)
- Thực thi query và format kết quả đẹp mắt
- Tương tác trực tiếp với database thực tế

🎯 **NGUYÊN TẮC TRẢ LỜI:**
- Trả lời chính xác, chi tiết
- Sử dụng ví dụ cụ thể khi cần
- Đưa ra giải pháp thực tế
- Ưu tiên bảo mật và hiệu suất

Nếu câu hỏi liên quan đến lập trình, hãy cung cấp code examples. Nếu câu hỏi về kinh doanh, hãy đưa ra lời khuyên thực tế.
EOT;
    
    $messages = [
        [
            'role' => 'system',
            'content' => $systemPrompt
        ]
    ];
    
    // Kiểm tra cấu hình memory
    $memoryEnabled = $CMSNT->site('ai_memory_enabled') ?? '1'; // mặc định bật
    
    if ($memoryEnabled == '1') {
        // Thêm lịch sử chat gần nhất (5 cuộc trò chuyện)
        $recentChats = getRecentChatHistory(5);
        
        foreach ($recentChats as $chat) {
            $messages[] = [
                'role' => 'user',
                'content' => $chat['message']
            ];
            $messages[] = [
                'role' => 'assistant',
                'content' => $chat['response']
            ];
        }
    }
    
    // Thêm tin nhắn hiện tại
    $messages[] = [
        'role' => 'user',
        'content' => $currentMessage
    ];
    
    return $messages;
}

function callOpenAIAPI($message, $model = 'gpt-3.5-turbo', $useMemory = true) {
    global $CMSNT;
    
    $api_key = $CMSNT->site('chatgpt_api_key');
    $url = 'https://api.openai.com/v1/chat/completions';
    
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ];
    
    // Xây dựng messages array
    if ($useMemory) {
        $messages = buildMessagesWithMemory($message);
    } else {
        $messages = [
            [
                'role' => 'user',
                'content' => $message
            ]
        ];
    }
    
    $data = [
        'model' => $model,
        'messages' => $messages,
        'max_tokens' => 2000,
        'temperature' => 0.7,
        'top_p' => 1.0,
        'frequency_penalty' => 0.0,
        'presence_penalty' => 0.0
    ];
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    
    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);
    
    if ($curlError) {
        return [
            'success' => false,
            'message' => 'Lỗi kết nối: ' . $curlError
        ];
    }
    
    if ($httpCode !== 200) {
        return [
            'success' => false,
            'message' => 'Lỗi API OpenAI (HTTP ' . $httpCode . ')'
        ];
    }
    
    $responseData = json_decode($response, true);
    
    if (isset($responseData['error'])) {
        return [
            'success' => false,
            'message' => 'Lỗi OpenAI: ' . $responseData['error']['message']
        ];
    }
    
    if (!isset($responseData['choices'][0]['message']['content'])) {
        return [
            'success' => false,
            'message' => __('Không nhận được phản hồi từ AI')
        ];
    }
    
    return [
        'success' => true,
        'response' => trim($responseData['choices'][0]['message']['content']),
        'tokens' => $responseData['usage']['total_tokens'] ?? 0,
        'model' => $model
    ];
}

function saveChatHistory() {
    global $CMSNT, $getUser;
    
    if (!isset($_POST['message']) || !isset($_POST['response'])) {
        die(json_encode(['success' => false, 'message' => 'Missing required fields']));
    }
    
    $message = check_string($_POST['message']);
    $response = check_string($_POST['response']);
    
    // Sử dụng model từ cấu hình hệ thống
    $model = $CMSNT->site('chatgpt_model');
    if (empty($model)) {
        $model = 'gpt-3.5-turbo'; // fallback default
    }
    
    $isInsert = $CMSNT->insert("ai_chat_history", [
        'user_id' => $getUser['id'],
        'user_type' => 'admin',
        'message' => $message,
        'response' => $response,
        'model' => $model,
        'tokens_used' => 0,
        'created_at' => date('Y-m-d H:i:s')
    ]);
    
    if ($isInsert) {
        die(json_encode(['success' => true, 'message' => __('Chat saved successfully')]));
    } else {
        die(json_encode(['success' => false, 'message' => __('Failed to save chat')]));
    }
}

function loadChatHistory() {
    global $CMSNT, $getUser;
    
    // Lấy tham số pagination
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 1;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 10;
    $offset = ($page - 1) * $limit;
    
    // Đếm tổng số tin nhắn
    $countQuery = $CMSNT->get_row("
        SELECT COUNT(*) as total FROM `ai_chat_history` 
        WHERE `user_id` = '".$getUser['id']."' AND `user_type` = 'admin'
    ");
    $totalCount = $countQuery['total'] ?? 0;
    
    // Lấy tin nhắn theo trang
    $chatHistory = $CMSNT->get_list("
        SELECT * FROM `ai_chat_history` 
        WHERE `user_id` = '".$getUser['id']."' AND `user_type` = 'admin'
        ORDER BY `created_at` DESC 
        LIMIT $limit OFFSET $offset
    ");
    
    // Kiểm tra xem có còn tin nhắn khác không
    $hasMore = ($offset + $limit) < $totalCount;
    
    if ($chatHistory) {
        // Đảo ngược thứ tự để hiển thị từ cũ đến mới
        $chatHistory = array_reverse($chatHistory);
        
        die(json_encode([
            'success' => true,
            'history' => $chatHistory,
            'total' => count($chatHistory),
            'total_count' => $totalCount,
            'current_page' => $page,
            'has_more' => $hasMore
        ]));
    } else {
        die(json_encode([
            'success' => true,
            'history' => [],
            'total' => 0,
            'total_count' => $totalCount,
            'current_page' => $page,
            'has_more' => $hasMore
        ]));
    }
}

function clearChatHistory() {
    global $CMSNT, $getUser;
    
    $isDelete = $CMSNT->remove('ai_chat_history', " `user_id` = '".$getUser['id']."' AND `user_type` = 'admin'");
    
    if ($isDelete) {
        // Ghi log hoạt động
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => "Xóa lịch sử chat AI"
        ]);
        
        die(json_encode(['success' => true, 'message' => __('Chat history cleared successfully')]));
    } else {
        die(json_encode(['success' => false, 'message' => __('Failed to clear chat history')]));
    }
}

function getMemoryStatus() {
    global $CMSNT;
    
    $memoryEnabled = $CMSNT->site('ai_memory_enabled') ?? '1';
    $memoryStatus = $memoryEnabled == '1' ? 'Memory ON' : 'Memory OFF';
    
    die(json_encode([
        'success' => true,
        'memory_enabled' => $memoryEnabled,
        'memory_status' => $memoryStatus
    ]));
}

function toggleMemoryStatus() {
    global $CMSNT, $getUser;
    
    // Lấy trạng thái memory hiện tại
    $currentStatus = $CMSNT->site('ai_memory_enabled') ?? '1';
    
    // Toggle trạng thái
    $newStatus = $currentStatus == '1' ? '0' : '1';
    $newStatusText = $newStatus == '1' ? 'Memory ON' : 'Memory OFF';
    
    // Cập nhật vào database
    $isUpdated = $CMSNT->update('settings', [
        'value' => $newStatus
    ], "`name` = 'ai_memory_enabled'");
    
    if ($isUpdated) {
        // Ghi log hoạt động
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => "Thay đổi AI Memory: " . $newStatusText
        ]);
        
        die(json_encode([
            'success' => true,
            'memory_enabled' => $newStatus,
            'memory_status' => $newStatusText,
            'message' => __('Đã ') . ($newStatus == '1' ? __('bật') : __('tắt')) . __(' Memory AI')
        ]));
    } else {
        die(json_encode([
            'success' => false,
            'message' => __('Không thể thay đổi trạng thái Memory')
        ]));
    }
}

function changeChatModel() {
    global $CMSNT, $getUser;

    if (!isset($_POST['model']) || empty($_POST['model'])) {
        die(json_encode(['success' => false, 'message' => __('Vui lòng chọn model hợp lệ.')]));
    }

    $newModel = check_string($_POST['model']);

    // Cập nhật vào database
    $isUpdated = $CMSNT->update('settings', [
        'value' => $newModel
    ], "`name` = 'chatgpt_model'");
    
    if ($isUpdated) {
        // Ghi log hoạt động
        $CMSNT->insert("logs", [
            'user_id' => $getUser['id'],
            'ip' => myip(),
            'device' => getUserAgent(),
            'createdate' => gettime(),
            'action' => __('Thay đổi AI Chat Model thành:')." " . $newModel
        ]);
        
        die(json_encode([
            'success' => true,
            'message' => __('Đã thay đổi model thành công!'),
            'new_model' => $newModel
        ]));
    } else {
        die(json_encode([
            'success' => false,
            'message' => __('Không thể thay đổi model')
        ]));
    }
}

?>