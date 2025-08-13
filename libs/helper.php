<?php
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$CMSNT = new DB;
date_default_timezone_set($CMSNT->site('timezone'));
$session_login = $CMSNT->site('session_login');
ini_set('session.gc_maxlifetime', $session_login);
ini_set('session.cookie_lifetime', $session_login);
ini_set('session.cookie_secure', true); // Chỉ gửi cookie qua HTTPS
ini_set('session.cookie_httponly', true); // Chặn truy cập cookie từ JavaScript
session_start();

$_SERVER['SERVER_NAME'] = check_string($_SERVER['SERVER_NAME'] ?? '');
$_SERVER['HTTP_USER_AGENT'] = check_string($_SERVER['HTTP_USER_AGENT'] ?? '');
$_SERVER['REMOTE_ADDR'] = check_string($_SERVER['REMOTE_ADDR'] ?? '');
$_SERVER['REQUEST_URI'] = check_string($_SERVER['REQUEST_URI'] ?? '');
$_SERVER['REQUEST_METHOD'] = check_string($_SERVER['REQUEST_METHOD'] ?? '');
$_SERVER['HTTP_HOST'] = check_string($_SERVER['HTTP_HOST'] ?? '');



if($CMSNT->get_row(" SELECT * FROM `block_ip` WHERE `ip` = '".myip()."' AND `banned` = 1 ")){
    require_once(__DIR__.'/../views/common/block-ip.php');
    exit();
}
 

function getListServiceType(){
    global $CMSNT;
    return $CMSNT->get_list("SELECT * FROM `smm_service_types` ORDER BY `id` ASC");
}

function getServiceTypeByCode($code){
    global $CMSNT;
    return $CMSNT->get_row("SELECT * FROM `smm_service_types` WHERE `code` = '$code' ");
}

function getUserAgent(): string {
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';

    // Làm sạch User-Agent để tránh XSS hoặc injection
    return htmlspecialchars(strip_tags($userAgent), ENT_QUOTES, 'UTF-8');
}


function getRankStatusByTarget($target){
    global $CMSNT;
    return $CMSNT->get_row("SELECT `status` FROM `ranks` WHERE `target` = '$target' ")['status'];
}
function getRankTargetById($rank_id){
    global $CMSNT;
    return $CMSNT->get_row("SELECT `target` FROM `ranks` WHERE `id` = '$rank_id' ")['target'];
}
function getRankNameByTarget($target){
    global $CMSNT;
    return $CMSNT->get_row("SELECT `name` FROM `ranks` WHERE `target` = '$target' ")['name'];
}

function getRankNameById($rank_id){
    if($rank_id == 0){
        return __('Thành viên');
    }else if($rank_id == -1){
        return __('Vui lòng đăng nhập');
    }else{
        return getRowRealtime('ranks', $rank_id, 'name');
    }
}

/**
 * Lấy URL ảnh đại diện từ Gravatar dựa vào địa chỉ email.
 *
 * @param string|null $email Email của người dùng. Có thể là null nếu không tìm thấy email.
 * @param int $size Kích thước ảnh (pixel).
 * @param string $default Mã hoặc URL cho ảnh mặc định của Gravatar (vd: 'mp', 'identicon', 'monsterid', 'wavatar', 'retro', 'blank', hoặc URL ảnh).
 * @param string $rating Giới hạn đánh giá ảnh (g, pg, r, x).
 * @return string URL ảnh Gravatar.
 */
function getGravatarUrl(?string $email, int $size = 80, string $default = 'mp', string $rating = 'g'): string{
    global $CMSNT;
    if($CMSNT->site('type_avatar') == 'ui-avatars'){
        return 'https://ui-avatars.com/api/?name='.urlencode($email).'&color=ffffff&background=1c8ef9';
    }else if($CMSNT->site('type_avatar') == 'gravatar'){
        return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d=mp&s='.$size;
    }
    return 'https://www.gravatar.com/avatar/'.md5(strtolower(trim($email))).'?d=mp&s='.$size;
}
function deleteFolder($folderPath) {
    if (!is_dir($folderPath)) {
        return false; // Thư mục không tồn tại
    }

    $files = array_diff(scandir($folderPath), ['.', '..']);

    foreach ($files as $file) {
        $filePath = $folderPath . DIRECTORY_SEPARATOR . $file;
        is_dir($filePath) ? deleteFolder($filePath) : unlink($filePath);
    }

    return rmdir($folderPath);
}
function checkBlockIP($type, $time = 15){
    global $CMSNT;
    $ip_address = myip();
    if($type == 'API'){
        $reason = __('Request API sai API KEY quá nhiều lần');
        $max_attempts = $CMSNT->site('limit_block_ip_api');  // Số lần thử tối đa
    } elseif($type == 'LOGIN'){
        $reason = __('Đăng nhập thất bại quá nhiều lần');
        $max_attempts = $CMSNT->site('limit_block_ip_login');  // Số lần thử tối đa
    } elseif($type == 'ADMIN'){
        $reason = __('Đăng nhập Admin thất bại quá nhiều lần');
        $max_attempts = $CMSNT->site('limit_block_ip_admin_access');  // Số lần thử tối đa
    } elseif($type == 'RESET_PASSWORD'){
        $reason = __('Spam khôi phục mật khẩu');
        $max_attempts = $CMSNT->site('limit_block_ip_reset_password');  // Số lần thử tối đa
    } elseif($type == 'OTP'){
        $reason = __('Spam OTP');
        $max_attempts = $CMSNT->site('limit_block_ip_otp');  // Số lần thử tối đa
    } elseif($type == 'SEND_OTP'){
        $reason = __('Spam gửi OTP'); 
        $max_attempts = 10;  // Số lần thử tối đa
    } elseif($type == '2FA'){
        $reason = __('Spam 2FA');
        $max_attempts = $CMSNT->site('limit_block_ip_2fa');  // Số lần thử tối đa
    } elseif($type == 'PAYMENT'){
        $reason = __('Spam Tạo hóa đơn nạp tiền quá nhiều lần');
        $max_attempts = $CMSNT->site('limit_block_ip_payment');  // Số lần thử tối đa
    } else{
        $reason = __('Spam Request quá nhiều lần');
        $max_attempts = $CMSNT->site('limit_block_ip_spam');  // Số lần thử tối đa
    }
    if($max_attempts == 0){
        return false;
    }
    // Thêm log thất bại vào bảng failed_attempts
    $CMSNT->insert("failed_attempts", [
        'ip_address'        => $ip_address,
        'attempts'          => 1,
        'type'              => $type,
        'create_gettime'    => gettime()
    ]);
    // Đếm số lần thất bại trong 15 phút gần nhất
    $attempts = $CMSNT->get_row("SELECT COUNT(*) as total FROM `failed_attempts` 
        WHERE `ip_address` = '$ip_address' 
        AND `type` = '$type'
        AND `create_gettime` >= DATE_SUB(NOW(), INTERVAL $time MINUTE)");
        
    // Nếu số lần thất bại vượt quá giới hạn
    if ($attempts['total'] >= $max_attempts) {
        // Thêm vào danh sách block
        $CMSNT->insert('block_ip', [
            'ip' => $ip_address,
            'attempts' => $attempts['total'],
            'create_gettime' => gettime(),
            'banned' => 1,
            'reason' => __($reason)
        ]);
        // Xóa tất cả log thất bại của IP này
        $CMSNT->remove('failed_attempts', " `ip_address` = '$ip_address' AND `type` = '$type'");
        return json_encode(['status' => 'error', 'msg' => __('IP của bạn đã bị khóa. Vui lòng thử lại sau.')]);
    }
}

function checkDomainAPI($domain, $proxy = ''){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.cmsnt.co/checkdomain.php?domain={$domain}");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    
    
    $data = curl_exec($ch);
    curl_close($ch);
    $checkdomain = json_decode($data, true);
    if($checkdomain['status'] == false){
        return [
            'msg' => $checkdomain['msg'],
            'status' => false
        ];
    }
    return [
        'msg' => '',
        'status' => true
    ];
}
 
function log_admin_request() {
    global $CMSNT, $getUser;

    // Lấy thông tin cơ bản
    $request_url = check_string($_SERVER['REQUEST_URI']);
    $request_method = check_string($_SERVER['REQUEST_METHOD']);
    $ip = check_string($_SERVER['REMOTE_ADDR']);
    $user_agent = check_string($_SERVER['HTTP_USER_AGENT']);

    // Lấy tham số request (loại bỏ thông tin nhạy cảm)
    $params = [];
    if ($request_method === 'GET') {
        $params = $_GET;
    } elseif ($request_method === 'POST') {
        $params = $_POST;
    }
    
    // Xóa các trường nhạy cảm
    $filtered_params = array_filter($params, function($key) {
        return !in_array(strtolower($key), ['password', 'token', 'csrf', 'api_key']);
    }, ARRAY_FILTER_USE_KEY);

    // Kiểm tra và xóa log cũ nếu vượt quá 10,000 bản ghi
    $total_logs = $CMSNT->get_row("SELECT COUNT(*) as total FROM `admin_request_logs`")['total'];
    $max_logs = 10000;
    
    if ($total_logs >= $max_logs) {
        // Tính số bản ghi cần xóa = (tổng hiện tại - max cho phép) + 1
        $delete_count = ($total_logs - $max_logs) + 1;
        
        // Xóa các bản ghi cũ nhất dựa trên ID
        $CMSNT->query("
            DELETE FROM `admin_request_logs` 
            WHERE id IN (
                SELECT id 
                FROM (
                    SELECT id 
                    FROM `admin_request_logs` 
                    ORDER BY id ASC 
                    LIMIT $delete_count
                ) AS temp
            )
        ");
    }

    // Chèn log mới
    $CMSNT->insert('admin_request_logs', [
        'user_id'           => $getUser['id'],
        'request_url'       => $request_url,
        'request_method'    => $request_method,
        'request_params'    => json_encode($filtered_params, JSON_UNESCAPED_UNICODE),
        'ip'                => $ip,
        'user_agent'        => $user_agent,
        'timestamp'         => gettime()
    ]);
}


function display_method_xipay($method) {
    $method = htmlspecialchars($method);
    $output = '';
    
    switch(strtolower($method)) {
        case 'alipay':
            $output = '<span class="d-inline-flex align-items-center border rounded p-2">';
            $output .= '<i class="fab fa-alipay text-primary fa-2x me-2"></i>';
            $output .= '<span class="fs-7 text-primary">' . __('Alipay') . '</span>';
            $output .= '</span>';
            break;
            
        case 'wxpay':
            $output = '<span class="d-inline-flex align-items-center border rounded p-2">';
            $output .= '<i class="fab fa-weixin text-success fa-2x me-2"></i>';
            $output .= '<span class="fs-7 text-success">' . __('WeChat Pay') . '</span>';
            $output .= '</span>';
            break;
            
        default:
            break;
    }
    
    return $output;
}

function generateUltraSecureToken($length = 32) {
    $randomBytes = random_bytes($length);
    return bin2hex($randomBytes);
}
function generateApiKey($length = 32) {
    // Tạo chuỗi ngẫu nhiên với độ dài chỉ định (mặc định là 32)
    return bin2hex(random_bytes($length / 2)).uniqid();
}
function generateRememberToken($currentToken, $storedIp) {
    // Tạo token mới nếu token trống
    if (empty($currentToken)) {
        return bin2hex(random_bytes(64));
    }
    return $currentToken;
}

function isSecureCookie($name){
    if(isset($_COOKIE[$name])){
        return true;
    }else{
        false;
    }
}

function setSecureCookie($name, $value){
    global $CMSNT;
    return setcookie($name, $value, time() + $CMSNT->site('session_login'), "/", "", false, true);
}

function insert_options($name, $value){
    global $CMSNT;
    if (!$CMSNT->get_row("SELECT * FROM `settings` WHERE `name` = '$name' ")) {
        $CMSNT->insert("settings", [
            'name'  => $name,
            'value' => $value
        ]);
    }
}

//
$host = $_SERVER['HTTP_HOST'] ?? '';
$host = check_string($host);
$domains = $host . ',' . 'www.' . $host;
insert_options('domains', $domains);
//

function insert_ip_block($ip, $reason){
    global $CMSNT;
    if(!$CMSNT->get_row(" SELECT * FROM `block_ip` WHERE `ip` = '$ip' ")){
        $CMSNT->insert('block_ip', [
            'ip'        => check_string($ip),
            'attempts'  => 5,
            'banned'    => 1,
            'reason'    => check_string($reason),
            'create_gettime'    => gettime()
        ]);
    }
    return true;
}
function checkAccessAttempts($max_attempts = 5){
    global $CMSNT;
    $ip_address = myip();
    $attempt = $CMSNT->get_row("SELECT * FROM `failed_attempts` WHERE `ip_address` = '$ip_address' AND `type` = 'Spam Request' ");
    // Kiểm tra xem IP đã vượt quá số lần thử và trong khoảng thời gian lockout chưa
    if ($attempt && $attempt['attempts'] >= $max_attempts) {
        // Khóa IP vào bảng banned_ips
        $CMSNT->insert('block_ip', [
            'ip'                => $ip_address,
            'attempts'          => $attempt['attempts'],
            'create_gettime'    => gettime(),
            'banned'            => 1,
            'reason'            => __('Spam Request')
        ]);
        // Xóa IP ra khỏi bảng failed_attempts sau khi đã block
        $CMSNT->remove('failed_attempts', " `ip_address` = '$ip_address' ");
        return true;
    }
    // Nếu chưa đến mức lockout, tăng số lần thử
    if ($attempt) {
        // Cập nhật số lần thất bại
        $CMSNT->cong('failed_attempts', 'attempts', 1, " `ip_address` = '$ip_address' ");
    } else {
        // Thêm bản ghi mới cho IP này
        $CMSNT->insert("failed_attempts", [
            'ip_address'    => $ip_address,
            'attempts'      => 1,
            'type'          => 'Spam Request',
            'create_gettime'=> gettime()
        ]);
    }
    return true;
}

function removeSpaces($string) {
    return str_replace(' ', '', $string);
}
function curl_get_contents($url, $timeout = 10) {
    // Initialize a cURL session
    $ch = curl_init();
    // Set the URL to fetch
    curl_setopt($ch, CURLOPT_URL, $url);
    // Set the timeout for the request
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    // Return the transfer as a string instead of outputting it directly
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // Optional: Set a user-agent to mimic a browser request
    curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/58.0.3029.110 Safari/537.3');
    // Optional: Follow redirects (HTTP 3xx responses)
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    // Execute the request and store the result
    $result = curl_exec($ch);
    // Check for errors
    if (curl_errno($ch)) {
        // If there's an error, return false
        $result = false;
    }
    // Close the cURL session
    curl_close($ch);
    return $result;
}



function remove_html_tags($string) {
    // Loại bỏ các thẻ ul và li
    $string = preg_replace('/<ul[^>]*>/', '', $string);
    $string = preg_replace('/<\/ul>/', '', $string);
    $string = preg_replace('/<li[^>]*>/', '', $string);
    $string = preg_replace('/<\/li>/', '', $string);

    // Loại bỏ các thẻ b và i
    $string = preg_replace('/<b[^>]*>/', '', $string);
    $string = preg_replace('/<\/b>/', '', $string);
    $string = preg_replace('/<i[^>]*>/', '', $string);
    $string = preg_replace('/<\/i>/', '', $string);

    // Trả về chuỗi đã loại bỏ các thẻ HTML
    return $string;
}
function getDiscount($amount, $product_id){
    $CMSNT = new DB;
    foreach($CMSNT->get_list("SELECT * FROM `product_discount` WHERE `min` <= '$amount' AND `product_id` = '$product_id' ORDER BY `min` DESC ") as $discount){
        return $discount['discount'];
    } 
    return 0;
}
function checkPromotion($amount){
    global $CMSNT;
    foreach($CMSNT->get_list("SELECT * FROM `promotions` WHERE `min` <= '$amount' ORDER by `min` DESC ") as $promotion){
        $received = $amount + $amount * $promotion['discount'] / 100;
        return $received;
    }
    return $amount;
}
function admin_msg_success($text, $url, $time){
    return die('<script type="text/javascript">Swal.fire({
        title: "Thành công!",
        text: "'.$text.'",
        icon: "success"
    });
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function admin_msg_error($text, $url, $time){
    return die('<script type="text/javascript">Swal.fire("Thất Bại", "'.$text.'","error");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function admin_msg_warning($text, $url, $time){
    return die('<script type="text/javascript">Swal.fire("Thông Báo", "'.$text.'","warning");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function debit_processing($user_id){
    $CMSNT = new DB();
    $User = new users();

    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '$user_id' ");
    if($getUser['debit'] > 0){
        if($getUser['money'] >= $getUser['debit']){
            // ĐỦ TIỀN TRẢ NỢ
            $isTru = $CMSNT->tru('users', 'debit', $getUser['debit'], " `id` = '$user_id' ");
            if($isTru){
                $User->RemoveCredits($getUser['id'], $getUser['debit'], __('Thanh toán số tiền ghi nợ'));
                return true;
            }
        }else{
            // KHÔNG ĐỦ TIỀN
            $isTru = $CMSNT->tru('users', 'debit', $getUser['money'], " `id` = '$user_id' ");
            if($isTru){
                $User->RemoveCredits($getUser['id'], $getUser['money'], __('Thanh toán số tiền ghi nợ'));
                return true;
            }
        }
    }
    return false;
}
function checkPermission($admin_id, $role){
    global $CMSNT;
    // cấp độ cao nhất
    if($admin_id == 99999){
        return true;
    }
    // kiểm tra trong role
    if($row = $CMSNT->get_row(" SELECT * FROM `admin_role` WHERE `id` = '$admin_id' ")){
        if (in_array($role, json_decode($row['role'])) == true){
            return true;
        }
    }
    return false;
}
function getStock($code){
    $CMSNT = new DB;
    return $CMSNT->get_row(" SELECT COUNT(id) FROM `product_stock` WHERE  `product_code` = '$code' ")['COUNT(id)'];
}
function getCurrencyRate(){
    global $CMSNT;
    if (isset($_COOKIE['currency'])) {
        $currency = check_string($_COOKIE['currency']);
        $rowcurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$currency' AND `display` = 1 ");
        if ($rowcurrency) {
            return $rowcurrency['rate'];
        }
    }
    $rowcurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `default_currency` = 1 ");
    if ($rowcurrency) {
        return $rowcurrency['rate'];
    }
    return false;
}
function getCurrencyNameDefault(){
    return currencyDefault();
}
function currencyDefault(){
    $CMSNT = new DB;
    return $CMSNT->get_row(" SELECT `code` FROM `currencies` WHERE `display` = 1 AND `default_currency` = 1")['code'];
}
function dirImageProduct($image){
    $path = 'assets/storage/images/products/'.$image;
    return $path;
}
function generate_csrf_token() {
    if(!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = base64_encode(openssl_random_pseudo_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
function display_childpanel_status($status){
    if ($status == 'Pending') {
        return '<span class="badge bg-warning">'.__('Chờ duyệt').'</span>';
    } elseif ($status == 'Actived') {
        return '<span class="badge bg-success">'.__('Đã kích hoạt').'</span>';
    } elseif ($status == 'Expired') {
        return '<span class="badge bg-danger">'.__('Hết hạn').'</span>';
    } elseif ($status == 'Cancel') {
        return '<span class="badge bg-danger">'.__('Hủy').'</span>';
    }
    return '<span class="badge bg-secondary">'.__('Khác').'</span>';
}
function display_camp($status)
{
    if ($status == 0) {
        return '<span class="badge bg-info">Processing</span>';
    } elseif ($status == 1) {
        return '<span class="badge bg-success">Completed</span>';
    } elseif ($status == 2) {
        return '<span class="badge bg-danger">Cancel</span>';
    } else {
        return '<span class="badge bg-warning">Khác</span>';
    }
}
function display_withdraw($data){
    if ($data == 'pending') {
        $show = '<span class="badge bg-warning">Pending</span>';
    } elseif ($data == 'cancel') {
        $show = '<span class="badge bg-danger">Cancel</span>';
    }
    else if ($data == 'completed') {
        $show = '<span class="badge bg-success">Completed</span>';
    }
    return $show;
}
if (!function_exists('cal_days_in_month')) {
    function cal_days_in_month($calendar, $month, $year) {
        return date('t', mktime(0, 0, 0, $month, 1, $year));
    }
}
function setCurrency($id){
    global $CMSNT;
    if ($row = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$id' AND `display` = 1 ")) {
        $isSet = setcookie('currency', $row['id'], time() + (31536000 * 30), "/"); // 31536000 = 365 ngày
        if ($isSet) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}
function getCurrency(){
    global $CMSNT;
    if (isset($_COOKIE['currency'])) {
        $currency = check_string($_COOKIE['currency']);
        $rowcurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$currency' AND `display` = 1 ");
        if ($rowcurrency) {
            return $rowcurrency['id'];
        }
    }
    $rowcurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `default_currency` = 1 ");
    if ($rowcurrency) {
        return $rowcurrency['id'];
    }
    return false;
}
function display_status_rank($data){
    if ($data == 0) {
        $show = '<span class="badge bg-danger">'.__('Ẩn').'</span>';
    } 
    else if ($data == 1) {
        $show = '<span class="badge bg-success">'.__('Hoạt động').'</span>';
    }
    return $show;
}
function display_status_support_tickets($data){
    global $config_status_support_tickets;
    
    $badge_classes = [
        'open'      => 'badge bg-info-subtle text-info',
        'pending'   => 'badge bg-warning-subtle text-warning',
        'answered'  => 'badge bg-success-subtle text-success',
        'closed'    => 'badge bg-danger-subtle text-danger'
    ];
    
    if (isset($badge_classes[$data])) {
        $badge_class = $badge_classes[$data];
        $text = isset($config_status_support_tickets[$data]) ? $config_status_support_tickets[$data] : __($data);
        
        $show = '<span class="'.$badge_class.'">'.$text.'</span>';
    } else {
        $show = '<span class="badge bg-secondary-subtle text-secondary">'.__($data).'</span>';
    }
    
    return $show;
}
function display_status_scheduled_orders($data, $error_message = ''){
    global $config_status_scheduled_orders;
    
    $badge_classes = [
        'pending' => 'badge bg-warning',
        'executed' => 'badge bg-success',
        'cancelled' => 'badge bg-danger',
        'failed' => 'badge bg-danger'
    ];
    
    if (isset($badge_classes[$data])) {
        $badge_class = $badge_classes[$data];
        $text = isset($config_status_scheduled_orders[$data]) ? $config_status_scheduled_orders[$data] : __($data);
        
        // Thêm title với error_message nếu status = failed và có error_message
        $title_attr = '';
        if(($data === 'failed' || $data === 'cancelled') && !empty($error_message)) {
            $title_attr = 'data-toggle="tooltip" data-placement="bottom" title="' . htmlspecialchars($error_message, ENT_QUOTES, 'UTF-8') . '"';
        }
        
        $show = '<span class="'.$badge_class.'"'.$title_attr.'>'.$text.'</span>';
    } else {
        $show = '<span class="badge bg-secondary">'.__($data).'</span>';
    }
    
    return $show;
}
function display_service($data){
    global $config_status_order;
    
    $badge_classes = [
        'Pending' => 'badge bg-warning',
        'Canceled' => 'badge bg-danger',
        'Completed' => 'badge bg-success',
        'In progress' => 'badge bg-info',
        'Processing' => 'badge bg-secondary',
        'Partial' => 'badge bg-danger'
    ];
    
    if (isset($badge_classes[$data])) {
        $badge_class = $badge_classes[$data];
        $text = isset($config_status_order[$data]) ? $config_status_order[$data] : __($data);
        $show = '<span class="'.$badge_class.'">'.$text.'</span>';
    } else {
        $show = '<span class="badge bg-secondary">'.__($data).'</span>';
    }
    
    return $show;
}
function display_invoice($data){
    if ($data == 'waiting') {
        $show = '<span class="badge bg-warning">'.__('Chưa thanh toán').'</span>';
    } elseif ($data == 'expired') {
        $show = '<span class="badge bg-danger">'.__('Hết hạn').'</span>';
    }
    else if ($data == 'completed') {
        $show = '<span class="badge bg-success">'.__('Đã thanh toán').'</span>';
    } 
    else if ($data == 0) {
        $show = '<span class="badge bg-warning">Waiting</span>';
    } 
    else if ($data == 2) {
        $show = '<span class="badge bg-danger">Expired</span>';
    }
    else if ($data == 1) {
        $show = '<span class="badge bg-success">Completed</span>';
    }
    return $show;
}
function isValidTRC20Address($address){
    $curl = curl_init();
    curl_setopt_array($curl, array(
    CURLOPT_URL => 'https://walletvalidator.com/usdt-trc20-wallet-validator/',
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'POST',
    CURLOPT_POSTFIELDS => array('validate' => $address),
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    $response = json_decode($response, true);
    if($response['ok'] != false){
        return true;
    }
    return false;
}

function is_valid_domain_name($domain_name){
    return (preg_match("/^([a-z\d](-*[a-z\d])*)(\.([a-z\d](-*[a-z\d])*))*$/i", $domain_name) && preg_match("/^.{1,253}$/", $domain_name) && preg_match("/^[^\.]{1,63}(\.[^\.]{1,63})*$/", $domain_name));
}
function display_domains($data){
    if ($data == 1) {
        $show = '<span class="badge bg-success">'.__('Hoạt Động').'</span>';
    } elseif ($data == 0) {
        $show = '<span class="badge bg-warning">'.__('Đang Xây Dựng').'</span>';
    } elseif ($data == 2) {
        $show = '<span class="badge bg-danger">'.__('Huỷ').'</span>';
    }
    return $show;
}
  

function addRef($user_id, $price, $note = ''){
    $CMSNT = new DB;
    if($CMSNT->site('status_ref') != 1){
        return false;
    }
    $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '$user_id' ");
    if($getUser['ref_id'] != 0){
        $price = $price * $CMSNT->site('ck_ref') / 100;
        $CMSNT->cong('users', 'ref_money', $price, " `id` = '".$getUser['ref_id']."' ");
        $CMSNT->cong('users', 'ref_total_money', $price, " `id` = '".$getUser['ref_id']."' ");
        $CMSNT->cong('users', 'ref_amount', $price, " `id` = '".$getUser['id']."' ");
        $CMSNT->insert('log_ref', [
            'user_id'       => $getUser['ref_id'],
            'reason'        => $note,
            'sotientruoc'   => getRowRealtime('users', $getUser['ref_id'], 'ref_money') - $price,
            'sotienthaydoi' => $price,
            'sotienhientai' => getRowRealtime('users', $getUser['ref_id'], 'ref_money'),
            'create_gettime'    => gettime()
        ]);
        return true;
    }
    return false;
}
function sendMessAdmin($my_text){
    if($my_text != ''){
        return sendMessTelegram($my_text);
    }
    return false;
}
function sendMessTelegram($my_text, $token = '', $chat_id = ''){
    $CMSNT = new DB;
    if($chat_id == ''){
        $chat_id = $CMSNT->site('telegram_chat_id');
    }
    if($token == ''){
        $token = $CMSNT->site('telegram_token');
    }
    if($my_text == ''){
        return false;
    }
    if($CMSNT->site('telegram_status') == 1){
        if($token != '' && $chat_id != ''){
            // Sử dụng CDN Cloudflare để tránh bị chặn bởi nhà mạng Việt Nam
            $telegram_url = $CMSNT->site('telegram_url') . 'bot' . $token . '/sendMessage';
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $telegram_url);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('chat_id' => $chat_id, 'text' => $my_text, 'parse_mode' => 'Markdown')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = curl_exec($ch);
            curl_close($ch);

            // GHI LOG
            $CMSNT->insert('bot_telegram_logs', [
                'chat_id' => $chat_id,
                'message'   => $my_text,
                'token'     => $token,
                'response'  => $response,
                'created_at' => gettime()
            ]);
            return $response;
        }
    }
    return false;
}
function getFlag($flag){

    if(empty($flag)){
        return '';
    }
    return '<img width="30px;" src="https://flagicons.lipis.dev/flags/4x3/'.$flag.'.svg">';
}
function claimSpin($user_id, $trans_id, $total_money)
{
    $CMSNT = new DB();
    $USER = new users();
    if ($CMSNT->site('status_spin') == 1) {
        if ($total_money >= $CMSNT->site('condition_spin')) {
            $USER->AddSpin($user_id, 1, 'Nhập 1 SPIN từ đơn hàng #'.$trans_id);
        }
    }
}
function getRandomWeightedElement(array $weightedValues)
{
    $Rand = mt_Rand(1, (int) array_sum($weightedValues));
    foreach ($weightedValues as $key => $value) {
        $Rand -= $value;
        if ($Rand <= 0) {
            return $key;
        }
    }
}
function checkFormatCard($type, $seri, $pin)
{
    $seri = strlen($seri);
    $pin = strlen($pin);
    $data = [];
    if ($type == 'Viettel' || $type == "viettel" || $type == "VT" || $type == "VIETTEL") {
        if ($seri != 11 && $seri != 14) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 13 && $pin != 15) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'Mobifone' || $type == "mobifone" || $type == "Mobi" || $type == "MOBIFONE") {
        if ($seri != 15) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 12) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'VNMB' || $type == "Vnmb" || $type == "VNM" || $type == "VNMOBI") {
        if ($seri != 16) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 12) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'Vinaphone' || $type == "vinaphone" || $type == "Vina" || $type == "VINAPHONE") {
        if ($seri != 14) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 14) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'Garena' || $type == "garena") {
        if ($seri != 9) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 16) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'Zing' || $type == "zing" || $type == "ZING") {
        if ($seri != 12) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 9) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    if ($type == 'Vcoin' || $type == "VTC") {
        if ($seri != 12) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài seri không phù hợp'
            ];
            return $data;
        }
        if ($pin != 12) {
            $data = [
                'status'    => false,
                'msg'       => 'Độ dài mã thẻ không phù hợp'
            ];
            return $data;
        }
    }
    $data = [
        'status'    => true,
        'msg'       => 'Success'
    ];
    return $data;
}
function active_sidebar_client($action)
{
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) {
            return 'active';
        }
    }
    return '';
}
function show_sidebar_client($action)
{
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) {
            return 'show';
        }
    }
    return '';
}
function show_sidebar($action)
{
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) {
            return 'active open';
        }
    }
    return '';
}

function parse_order_id($des, $MEMO_PREFIX)
{
    $re = '/'.$MEMO_PREFIX.'\d+/im';
    preg_match_all($re, $des, $matches, PREG_SET_ORDER, 0);
    if (count($matches) == 0) {
        return null;
    }
    // Print the entire match result
    $orderCode = $matches[0][0];
    $prefixLength = strlen($MEMO_PREFIX);
    $orderId = intval(substr($orderCode, $prefixLength));
    return $orderId ;
}
function display_status_toyyibpay($status)
{
    if ($status == 0) {
        return '<b style="color:#db7e06;">'.__('Waiting').'</b>';
    } elseif ($status == 'confirming') {
        return '<b style="color:blue;">'.__('Confirming').'</b>';
    } elseif ($status == 'confirmed') {
        return '<b style="color:green;">'.__('Confirmed').'</b>';
    } elseif ($status == 'refunded') {
        return '<b style="color:pink;">'.__('Refunded').'</b>';
    } elseif ($status == 'expired') {
        return '<b style="color:red;">'.__('Expired').'</b>';
    } elseif ($status == 2) {
        return '<b style="color:red;">'.__('Failed').'</b>';
    } elseif ($status == 'partially_paid') {
        return '<b style="color:green;">'.__('Partially Paid').'</b>';
    } elseif ($status == 1) {
        return '<b style="color:green;">'.__('Finished').'</b>';
    }
}
// function display_status_crypto($status)
// {
//     if ($status == 'waiting') {
//         return '<b style="color:#db7e06;">'.__('Waiting').'</b>';
//     } elseif ($status == 'confirming') {
//         return '<b style="color:blue;">'.__('Confirming').'</b>';
//     } elseif ($status == 'confirmed') {
//         return '<b style="color:green;">'.__('Confirmed').'</b>';
//     } elseif ($status == 'refunded') {
//         return '<b style="color:pink;">'.__('Refunded').'</b>';
//     } elseif ($status == 'expired') {
//         return '<b style="color:red;">'.__('Expired').'</b>';
//     } elseif ($status == 'failed') {
//         return '<b style="color:red;">'.__('Failed').'</b>';
//     } elseif ($status == 'partially_paid') {
//         return '<b style="color:green;">'.__('Partially Paid').'</b>';
//     } elseif ($status == 'finished') {
//         return '<b style="color:green;">'.__('Finished').'</b>';
//     }
// }
function display_card($status){
    if ($status == 'pending') {
        return '<span class="badge bg-info">'.__('Đang chờ xử lý').'</span>';
    } elseif ($status == 'completed') {
        return '<span class="badge bg-success">'.__('Thành công').'</span>';
    } elseif ($status == 'error') {
        return '<span class="badge bg-danger">'.__('Thất bại').'</span>';
    } else {
        return '<span class="badge bg-warning">Khác</span>';
    }
}
function display_invoice_text($status)
{
    if ($status == 0) {
        return __('Đang chờ thanh toán');
    } elseif ($status == 1) {
        return __('Đã thanh toán');
    } elseif ($status == 2) {
        return __('Huỷ bỏ');
    } else {
        return __('Khác');
    }
}
// lấy dữ liệu theo thời gian thực
function getRowRealtime($table, $id, $row){
    global $CMSNT;
    if($data = $CMSNT->get_row("SELECT `".$row."` FROM `$table` WHERE `id` = '$id' ")){
        return $data[$row];
    }
    return false;
}

function get_url(){
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
        $url = "https://"; 
    }else {
        $url = "http://";
    }         
    $url.= $_SERVER['HTTP_HOST'];   
    $url.= $_SERVER['REQUEST_URI'];    
    return $url;  
}
function url() {
    global $CMSNT;

    // Lấy danh sách domains từ database
    $allowed_domains = array_map('trim', explode(',', $CMSNT->site('domains'))); // Làm sạch danh sách domains

    // Lấy giá trị SERVER_NAME hoặc HTTP_HOST
    $host = $_SERVER['SERVER_NAME'] ?? $_SERVER['HTTP_HOST'] ?? '';

    // Kiểm tra tính hợp lệ của host
    if (!preg_match('/^[a-zA-Z0-9\-\.]+$/', $host)) {
        $host = $allowed_domains[0]; // Sử dụng domain mặc định nếu không hợp lệ
    }

    // Nếu host không nằm trong danh sách domains, sử dụng domain đầu tiên
    if (!in_array($host, $allowed_domains)) {
        $host = $allowed_domains[0];
    }

    // Xác định giao thức (HTTPS hoặc HTTP)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') 
                ? 'https' : 'http';

    // Làm sạch REQUEST_URI để tránh lỗi XSS
    $uri = htmlspecialchars($_SERVER['REQUEST_URI'], ENT_QUOTES, 'UTF-8');

    // Trả về URL đầy đủ
    return sprintf("%s://%s%s", $protocol, $host, $uri);
}

function base_url($url = '') {
    global $CMSNT;

    // Lấy danh sách domains từ database
    $allowed_domains = array_map('trim', explode(',', $CMSNT->site('domains'))); // Làm sạch danh sách domains

    // Lấy giá trị HTTP_HOST
    $host = $_SERVER['HTTP_HOST'] ?? '';

    // Kiểm tra tính hợp lệ của HTTP_HOST
    if (!preg_match('/^[a-zA-Z0-9\-\.]+$/', $host)) {
        $host = $allowed_domains[0]; // Domain mặc định nếu HTTP_HOST không hợp lệ
    }

    // Nếu HTTP_HOST không nằm trong danh sách, sử dụng domain đầu tiên
    if (!in_array($host, $allowed_domains)) {
        $host = $allowed_domains[0]; // Domain mặc định
    }

    // Xác định giao thức (HTTPS hoặc HTTP)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

    // Xử lý localhost riêng (nếu cần)
    if ($host === 'localhost') {
        $base = 'http://localhost/CMSNT.CO/SMMPANEL2';
    } else {
        $base = $protocol . '://' . $host;
    }

    // Trả về URL đầy đủ
    return check_string($base) . '/' . ltrim($url, '/');
}

function base_url_admin($url = '') {
    global $CMSNT;

    // Lấy danh sách domains từ database
    $allowed_domains = array_map('trim', explode(',', $CMSNT->site('domains'))); // Làm sạch danh sách domains

    // Lấy giá trị HTTP_HOST
    $host = $_SERVER['HTTP_HOST'] ?? '';

    // Kiểm tra tính hợp lệ của HTTP_HOST
    if (!preg_match('/^[a-zA-Z0-9\-\.]+$/', $host)) {
        $host = $allowed_domains[0]; // Domain mặc định nếu HTTP_HOST không hợp lệ
    }

    // Nếu HTTP_HOST không nằm trong danh sách, sử dụng domain đầu tiên
    if (!in_array($host, $allowed_domains)) {
        $host = $allowed_domains[0]; // Domain mặc định
    }

    // Xác định giao thức (HTTPS hoặc HTTP)
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') ? 'https' : 'http';

    // Xử lý localhost riêng (nếu cần)
    if ($host === 'localhost') {
        $base = 'http://localhost/CMSNT.CO/SMMPANEL2';
    } else {
        $base = $protocol . '://' . $host;
    }

    // Kiểm tra và bảo toàn giá trị URL
    $final_url = rtrim(check_string($base), '/') . '/?module='.$CMSNT->site('path_admin').'&action=' . $url;

    // Trả về URL đầy đủ
    return $final_url;
}



// mã hoá password
function TypePassword($password)
{
    $CMSNT = new DB();
    if ($CMSNT->site('type_password') == 'md5') {
        return md5($password);
    }
    if ($CMSNT->site('type_password') == 'bcrypt') {
        return password_hash($password, PASSWORD_BCRYPT);
    }
    if ($CMSNT->site('type_password') == 'sha1') {
        return sha1($password);
    }
    return $password;
}
// lấy thông tin user theo id
function getUser($id, $row)
{
    $CMSNT = new DB();
    return $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '$id' ")[$row];
}
function validateUsername($username) {
    // Loại bỏ khoảng trắng đầu/cuối
    $username = trim($username);
    // Kiểm tra username chỉ chứa chữ cái, số, và có độ dài từ 3-20 ký tự
    if (preg_match('/^[a-zA-Z][a-zA-Z0-9]{2,19}$/', $username)) {
        return htmlspecialchars($username, ENT_QUOTES, 'UTF-8'); // Bảo vệ chống XSS
    }
    return false; // Không hợp lệ
}
function validateEmail($email) {
    // Loại bỏ khoảng trắng đầu/cuối
    $email = trim($email);

    // Kiểm tra email bằng filter_var
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); // Bảo vệ chống XSS
    }
    return false; // Không hợp lệ
}
// check định dạng số điện thoại
function validatePhone($data)
{
    if (preg_match('/^\+?(\d.*){3,}$/', $data, $matches)) {
        return true;
    } else {
        return false;
    }
}
// get datatime
function gettime()
{
    return date('Y/m/d H:i:s', time());
}

function format_currency2($amount)
{
    $CMSNT = new DB();
    $currency = $CMSNT->site('currency');
    if ($currency == 'USD') {
        return '$'.number_format($amount / $CMSNT->site('usd_rate'), 2, '.', '');
    } elseif ($currency == 'VND') {
        return format_cash($amount).'đ';
    } elseif ($currency == 'THB') {
        return format_cash($amount / 645.36).' THB';
    }
} 
function format_currency($amount){
    $CMSNT = new DB();
    if (isset($_COOKIE['currency'])) {
        $currency = check_string($_COOKIE['currency']);
        $rowCurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$currency' AND `display` = 1 ");
        if ($rowCurrency) {
            if($rowCurrency['seperator'] == 'comma'){
                $seperator = ',';
            }
            if($rowCurrency['seperator'] == 'space'){
                $seperator = '';
            }
            if($rowCurrency['seperator'] == 'dot'){
                $seperator = '.';
            } 
            return $rowCurrency['symbol_left'].number_format($amount / $rowCurrency['rate'], $rowCurrency['decimal_currency'], '.', $seperator).$rowCurrency['symbol_right'];
        }
    }
    $rowCurrency = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `default_currency` = 1 ");
    if ($rowCurrency) {
        if($rowCurrency['seperator'] == 'comma'){
            $seperator = ',';
        }
        if($rowCurrency['seperator'] == 'space'){
            $seperator = '';
        }
        if($rowCurrency['seperator'] == 'dot'){
            $seperator = '.';
        }
        return $rowCurrency['symbol_left'].number_format($amount / $rowCurrency['rate'], $rowCurrency['decimal_currency'], '.', $seperator).$rowCurrency['symbol_right'];
    }
    return format_cash($amount).'đ';
}
//show ip
// function myip(){
//     if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
//         $ip_address = $_SERVER['HTTP_CLIENT_IP'];
//     } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
//         $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
//     } else {
//         $ip_address = $_SERVER['REMOTE_ADDR'];
//     }
//     if(isset(explode(',', $ip_address)[1])){
//         return explode(',', $ip_address)[0];
//     }
//     return check_string($ip_address);
// }

function myip() {
    // Địa chỉ IP mặc định (REMOTE_ADDR)
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? '';

    // Kiểm tra các header khác (nếu có)
    if (!empty($_SERVER['HTTP_CLIENT_IP']) && filter_var($_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP)) {
        $ip_address = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Lấy danh sách IP từ X-Forwarded-For
        $ip_list = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        $ip_list = array_map('trim', $ip_list); // Loại bỏ khoảng trắng thừa

        // Lấy địa chỉ IP đầu tiên hợp lệ
        foreach ($ip_list as $ip) {
            if (filter_var($ip, FILTER_VALIDATE_IP)) {
                $ip_address = $ip;
                break;
            }
        }
    }
    // Kiểm tra và trả về địa chỉ IP đã xác thực
    return filter_var($ip_address, FILTER_VALIDATE_IP) ? $ip_address : '0.0.0.0';
}


// lọc input
function check_string($data)
{
    // Tránh double encoding bằng cách decode trước khi encode lại
    $data = html_entity_decode($data, ENT_QUOTES, 'UTF-8');
    return trim(htmlspecialchars(addslashes($data)));
    //return str_replace(array('<',"'",'>','?','/',"\\",'--','eval(','<php'),array('','','','','','','','',''),htmlspecialchars(addslashes(strip_tags($data))));
}
// định dạng tiền tệ
function format_cash($number, $suffix = '')
{
    return number_format($number, 0, ',', '.') . "{$suffix}";
}
function create_slug($str) {
    $unicode = array(
        'a'=>'á|à|ả|ã|ạ|ă|ắ|ằ|ẳ|ẵ|ặ|â|ấ|ầ|ẩ|ẫ|ậ',
        'A'=>'Á|À|Ả|Ã|Ạ|Ă|Ắ|Ằ|Ẳ|Ẵ|Ặ|Â|Ấ|Ầ|Ẩ|Ẫ|Ậ',
        'd'=>'đ',
        'D'=>'Đ',
        'e'=>'é|è|ẻ|ẽ|ẹ|ê|ế|ề|ể|ễ|ệ',
        'E'=>'É|È|Ẻ|Ẽ|Ẹ|Ê|Ế|Ề|Ể|Ễ|Ệ',
        'i'=>'í|ì|ỉ|ĩ|ị',
        'I'=>'Í|Ì|Ỉ|Ĩ|Ị',
        'o'=>'ó|ò|ỏ|õ|ọ|ô|ố|ồ|ổ|ỗ|ộ|ơ|ớ|ờ|ở|ỡ|ợ',
        'O'=>'Ó|Ò|Ỏ|Õ|Ọ|Ô|Ố|Ồ|Ổ|Ỗ|Ộ|Ơ|Ớ|Ờ|Ở|Ỡ|Ợ',
        'u'=>'ú|ù|ủ|ũ|ụ|ư|ứ|ừ|ử|ữ|ự',
        'U'=>'Ú|Ù|Ủ|Ũ|Ụ|Ư|Ứ|Ừ|Ử|Ữ|Ự',
        'y'=>'ý|ỳ|ỷ|ỹ|ỵ',
        'Y'=>'Ý|Ỳ|Ỷ|Ỹ|Ỵ'
    );

    foreach ($unicode as $nonUnicode => $uni) {
        $str = preg_replace("/($uni)/i", $nonUnicode, $str);
    }

    // Loại bỏ các ký tự không hợp lệ (chỉ giữ lại chữ cái, số và dấu gạch ngang)
    $str = preg_replace('/[^\w\s-]/', '', $str);

    // Thay khoảng trắng bằng dấu gạch ngang
    $str = preg_replace('/\s+/', '-', $str);

    return strtolower($str);
}

function checkAddon($id_addon){
    $CMSNT = new DB();
    $domain = str_replace('www.', '', $_SERVER['HTTP_HOST']);
    if($CMSNT->get_row("SELECT * FROM `addons` WHERE `id` = '$id_addon' ")['purchase_key'] == md5($domain.'|'.$id_addon)){
        return true;
    }
    return false;
}
function curl_get2($url){
    $arrContextOptions=array(
        "ssl"=>array(
            "verify_peer"=>false,
            "verify_peer_name"=>false,
        ),
    ); 
    return file_get_contents($url, false, stream_context_create($arrContextOptions));
}
// curl get
function curl_get($url){
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_VERBOSE, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return $data;
}
function curl_dataPost($url, $dataPost){
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS => $dataPost,
    ));
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function curl_post($url, $method, $postinfo, $cookie_file_path)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, true);
    curl_setopt($ch, CURLOPT_NOBODY, false);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_COOKIE, $cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_file_path);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookie_file_path);
    curl_setopt(
        $ch,
        CURLOPT_USERAGENT,
        "Mozilla/5.0 (Windows; U; Windows NT 5.0; en-US; rv:1.7.12) Gecko/20050915 Firefox/1.0.7"
    );
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $_SERVER['REQUEST_URI']);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);

    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    if ($method=='POST') {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postinfo);
    }
    $html = curl_exec($ch);
    curl_close($ch);
    return $html;
}

 
// hàm tạo string random
function random($string, $int)
{
    return substr(str_shuffle($string), 0, $int);
}
// Hàm redirect
function redirect($url){
    header("Location: {$url}");
    exit();
}

// show active sidebar AdminLTE3
function active_sidebar($action)
{
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) {
            return 'active';
        }
    }
    return '';
}
function menuopen_sidebar($action)
{
    foreach ($action as $row) {
        if (isset($_GET['action']) && $_GET['action'] == $row) {
            return 'menu-open';
        }
    }
    return '';
}

// Hàm lấy value từ $_POST
function input_post($key)
{
    return isset($_POST[$key]) ? trim($_POST[$key]) : false;
}

// Hàm lấy value từ $_GET
function input_get($key)
{
    return isset($_GET[$key]) ? trim($_GET[$key]) : false;
}

// Hàm kiểm tra submit
function is_submit($key)
{
    return (isset($_POST['request_name']) && $_POST['request_name'] == $key);
}

function display_mark($data)
{
    if ($data >= 1) {
        $show = '<span class="badge bg-success">Có</span>';
    } elseif ($data == 0) {
        $show = '<span class="badge bg-danger">Không</span>';
    }
    return $show;
}
// display banned
function display_banned($banned)
{
    if ($banned != 1) {
        return '<span class="badge bg-success">Active</span>';
    } else {
        return '<span class="badge bg-danger">Banned</span>';
    }
}
// display online
function display_online($time)
{
    if (time() - $time <= 300) {
        return '<span class="badge bg-success">Online</span>';
    } else {
        return '<span class="badge bg-danger">Offline</span>';
    }
}
// hiển thị cờ quốc gia
function display_flag($data)
{
    return '<img src="https://flagcdn.com/40x30/'.$data.'.png" >';
}
function display_live($data)
{
    if ($data == 'LIVE') {
        $show = '<span class="badge bg-success">LIVE</span>';
    } elseif ($data == 'DIE') {
        $show = '<span class="badge bg-danger">DIE</span>';
    }
    return $show;
}
function display_checklive($data)
{
    if ($data == 1) {
        $show = '<span class="badge bg-success">Có</span>';
    } elseif ($data == 0) {
        $show = '<span class="badge bg-danger">Không</span>';
    }
    return $show;
}
function card24h($telco, $amount, $serial, $pin, $trans_id){
    global $CMSNT;
    $partner_id = $CMSNT->site('card_partner_id');
    $partner_key = $CMSNT->site('card_partner_key');
    $url = base64_decode('aHR0cHM6Ly9jYXJkMjRoLmNvbS9jaGFyZ2luZ3dzL3YyP3NpZ249').md5($partner_key.$pin.$serial).'&telco='.$telco.'&code='.$pin.'&serial='.$serial.'&amount='.$amount.'&request_id='.$trans_id.'&partner_id='.$partner_id.'&command=charging';
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $data = curl_exec($ch);
    curl_close($ch);
    return json_decode($data, true);
}
// hiển thị trạng thái hiển thị
function display_status_product($data)
{
    if ($data == 1) {
        $show = '<span class="badge bg-success">Hiển thị</span>';
    } elseif ($data == 0) {
        $show = '<span class="badge bg-danger">Ẩn</span>';
    }
    return $show;
}
//display rank admin
function display_role($data)
{
    if ($data == 1) {
        $show = '<span class="badge badge-danger">Admin</span>';
    } elseif ($data == 0) {
        $show = '<span class="badge badge-info">Member</span>';
    }
    return $show;
}
// Hàm show msg
function msg_success($text, $url, $time)
{
    return die('<script type="text/javascript">swal("Thành Công", "'.$text.'","success");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_error($text, $url, $time)
{
    return die('<script type="text/javascript">swal("Thất Bại", "'.$text.'","error");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
function msg_warning($text, $url, $time)
{
    return die('<script type="text/javascript">swal("Thông Báo", "'.$text.'","warning");
    setTimeout(function(){ location.href = "'.$url.'" },'.$time.');</script>');
}
//paginationBoostrap
function paginationBoostrap($url, $start, $total, $kmess)
{
    $out[] = '<ul class="pagination">';
    $neighbors = 2;
    if ($start >= $total) {
        $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
    } else {
        $start = max(0, (int)$start - ((int)$start % (int)$kmess));
    }
    $base_link = '<li class="page-item"><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '">%s</a></li>';
    $out[] = $start == 0 ? '' : sprintf($base_link, $start / $kmess, '<i class="far fa-hand-point-left"></i>');
    if ($start > $kmess * $neighbors) {
        $out[] = sprintf($base_link, 1, '1');
    }
    if ($start > $kmess * ($neighbors + 1)) {
        $out[] = '<li class="page-item"><a class="page-link">...</a></li>';
    }
    for ($nCont = $neighbors;$nCont >= 1;$nCont--) {
        if ($start >= $kmess * $nCont) {
            $tmpStart = $start - $kmess * $nCont;
            $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
        }
    }
    $out[] = '<li class="page-item active"><a class="page-link">' . ($start / $kmess + 1) . '</a></li>';
    $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
    for ($nCont = 1;$nCont <= $neighbors;$nCont++) {
        if ($start + $kmess * $nCont <= $tmpMaxPages) {
            $tmpStart = $start + $kmess * $nCont;
            $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
        }
    }
    if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages) {
        $out[] = '<li class="page-item"><a class="page-link">...</a></li>';
    }
    if ($start + $kmess * $neighbors < $tmpMaxPages) {
        $out[] = sprintf($base_link, $tmpMaxPages / $kmess + 1, $tmpMaxPages / $kmess + 1);
    }
    if ($start + $kmess < $total) {
        $display_page = ($start + $kmess) > $total ? $total : ($start / $kmess + 2);
        $out[] = sprintf($base_link, $display_page, '<i class="far fa-hand-point-right"></i>
        ');
    }
    $out[] = '</ul>';
    return implode('', $out);
}
function check_img($img){
    $filename = $_FILES[$img]['name'];
    $ext = explode(".", $filename);
    $ext = end($ext);
    $valid_ext = array("png","jpeg","jpg","PNG","JPEG","JPG","gif","GIF","svg","SVG","webp","WEBP");
    if (in_array($ext, $valid_ext)) {
        return true;
    }
}
function timeAgo($time_ago)
{
    $time_ago = empty($time_ago) ? 0 : $time_ago;
    if ($time_ago == 0) {
        return '--';
    }
    $time_ago   = date("Y-m-d H:i:s", $time_ago);
    $time_ago   = strtotime($time_ago);
    $cur_time   = time();
    $time_elapsed   = $cur_time - $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60);
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400);
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640);
    $years      = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return "$seconds ".__('giây trước');
    }
    //Minutes
    elseif ($minutes <= 60) {
        return "$minutes ".__('phút trước');
    }
    //Hours
    elseif ($hours <= 24) {
        return "$hours ".__('tiếng trước');
    }
    //Days
    elseif ($days <= 7) {
        if ($days == 1) {
            return __('Hôm qua');
        } else {
            return "$days ".__('ngày trước');
        }
    }
    //Weeks
    elseif ($weeks <= 4.3) {
        return "$weeks ".__('tuần trước');
    }
    //Months
    elseif ($months <=12) {
        return "$months ".__('tháng trước');
    }
    //Years
    else {
        return "$years ".__('năm trước');
    }
}
function timeRemaining($timestamp)
{
    // Kiểm tra xem timestamp có hợp lệ không
    if (empty($timestamp)) {
        return '--';
    }

    // Chuyển đổi timestamp thành đối tượng DateTime
    $expirationDate = new DateTime();
    $expirationDate->setTimestamp($timestamp);
    
    $currentDate = new DateTime(); // Thời gian hiện tại

    // Tính toán khoảng thời gian còn lại
    $interval = $currentDate->diff($expirationDate);

    // Kiểm tra xem thời gian hết hạn đã qua chưa
    if ($currentDate >= $expirationDate) {
        return __('Thời gian đã hết hạn.');
    }

    // Xuất kết quả
    $remaining = '';
    if ($interval->y > 0) {
        $remaining .= $interval->y . ' ' . __('năm') . ' ';
    }
    if ($interval->m > 0) {
        $remaining .= $interval->m . ' ' . __('tháng') . ' ';
    }
    if ($interval->d > 0) {
        $remaining .= $interval->d . ' ' . __('ngày') . ' ';
    }
    if ($interval->h > 0) {
        $remaining .= $interval->h . ' ' . __('giờ') . ' ';
    }
    if ($interval->i > 0) {
        $remaining .= $interval->i . ' ' . __('phút') . ' ';
    }

    // Nếu không có khoảng thời gian lớn hơn 0, hiển thị "0 ngày"
    if (empty($remaining)) {
        return __('0 ngày');
    }

    return trim($remaining . __(' còn lại'));
}
function timeAgo2($time_ago)
{
    $time_ago   = date("Y-m-d H:i:s", $time_ago);
    $time_ago   = strtotime($time_ago);
    $time_elapsed   = $time_ago;
    $seconds    = $time_elapsed ;
    $minutes    = round($time_elapsed / 60);
    $hours      = round($time_elapsed / 3600);
    $days       = round($time_elapsed / 86400);
    $weeks      = round($time_elapsed / 604800);
    $months     = round($time_elapsed / 2600640);
    $years      = round($time_elapsed / 31207680);
    // Seconds
    if ($seconds <= 60) {
        return "$seconds ".__('giây');
    }
    //Minutes
    elseif ($minutes <= 60) {
        return "$minutes ".__('phút');
    }
    //Hours
    elseif ($hours <= 24) {
        return "$hours ".__('tiếng');
    }
    //Days
    elseif ($days <= 7) {
        if ($days == 1) {
            return "$days ".__('ngày');
        } else {
            return "$days ".__('ngày');
        }
    }
    //Weeks
    elseif ($weeks <= 4.3) {
        return "$weeks ".__('tuần');
    }
    //Months
    elseif ($months <=12) {
        return "$months ".__('tháng');
    }
    //Years
    else {
        return "$years ".__('năm');
    }
}
function CheckLiveClone($uid){
    //$json = json_decode(curl_get("https://graph.facebook.com/".$uid."/picture?redirect=false"), true);
    $json = json_decode(curl_get("https://graph2.facebook.com/v3.3/".$uid."/picture?redirect=0"), true);
    if ($json['data']) {
        if (empty($json['data']['height']) && empty($json['data']['width'])) {
            return 'DIE';
        } else {
            return 'LIVE';
        }
    }
    // else if($json['error']){
    //     return 'DIE';
    // }
    else{
        return 'LIVE';
    }
}
function dirToArray($dir)
{
    $result = array();

    $cdir = scandir($dir);
    foreach ($cdir as $key => $value) {
        if (!in_array($value, array(".",".."))) {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) {
                $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value);
            } else {
                $result[] = $value;
            }
        }
    }

    return $result;
}

 function realFileSize($path)
 {
     if (!file_exists($path)) {
         return false;
     }

     $size = filesize($path);

     if (!($file = fopen($path, 'rb'))) {
         return false;
     }

     if ($size >= 0) {//Check if it really is a small file (< 2 GB)
        if (fseek($file, 0, SEEK_END) === 0) {//It really is a small file
            fclose($file);
            return $size;
        }
     }

     //Quickly jump the first 2 GB with fseek. After that fseek is not working on 32 bit php (it uses int internally)
     $size = PHP_INT_MAX - 1;
     if (fseek($file, PHP_INT_MAX - 1) !== 0) {
         fclose($file);
         return false;
     }

     $length = 1024 * 1024;
     while (!feof($file)) {//Read the file until end
         $read = fread($file, $length);
         $size = bcadd($size, $length);
     }
     $size = bcsub($size, $length);
     $size = bcadd($size, strlen($read));

     fclose($file);
     return $size;
 }
function FileSizeConvert($bytes)
{
    $result = NULL;
    $bytes = floatval($bytes);
    $arBytes = array(
            0 => array(
                "UNIT" => "TB",
                "VALUE" => pow(1024, 4)
            ),
            1 => array(
                "UNIT" => "GB",
                "VALUE" => pow(1024, 3)
            ),
            2 => array(
                "UNIT" => "MB",
                "VALUE" => pow(1024, 2)
            ),
            3 => array(
                "UNIT" => "KB",
                "VALUE" => 1024
            ),
            4 => array(
                "UNIT" => "B",
                "VALUE" => 1
            ),
        );

    foreach ($arBytes as $arItem) {
        if ($bytes >= $arItem["VALUE"]) {
            $result = $bytes / $arItem["VALUE"];
            $result = str_replace(".", ",", strval(round($result, 2)))." ".$arItem["UNIT"];
            break;
        }
    }
    return $result;
}
function GetCorrectMTime($filePath)
{
    $time = filemtime($filePath);

    $isDST = (date('I', $time) == 1);
    $systemDST = (date('I') == 1);

    $adjustment = 0;

    if ($isDST == false && $systemDST == true) {
        $adjustment = 3600;
    } elseif ($isDST == true && $systemDST == false) {
        $adjustment = -3600;
    } else {
        $adjustment = 0;
    }

    return ($time + $adjustment);
}
function DownloadFile($file)
{ // $file = include path
    if (file_exists($file)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit;
    }
}
function getFileType(string $url): string
{
    $filename=explode('.', $url);
    $extension=end($filename);

    switch ($extension) {
        case 'pdf':
            $type=$extension;
            break;
        case 'docx':
        case 'doc':
            $type='word';
            break;
        case 'xls':
        case 'xlsx':
            $type='excel';
            break;
        case 'mp3':
        case 'ogg':
        case 'wav':
            $type='audio';
            break;
        case 'mp4':
        case 'mov':
            $type='video';
            break;
        case 'zip':
        case '7z':
        case 'rar':
            $type='archive';
            break;
        case 'jpg':
        case 'jpeg':
        case 'png':
            $type='image';
            break;
        default:
            $type='alt';
    }

    return $type;
}

function getLocation($ip)
{
    if($ip = '::1'){
        $data = [
            'country' => 'VN'
        ];
        return $data;
    }
    $url = "http://ipinfo.io/" . $ip;
    $location = json_decode(file_get_contents($url), true);
    return $location;
}
function pagination($url, $start, $total, $kmess){
    $out[] = ' <div class="pagination-style-1"><ul class="pagination mb-0">';
    $neighbors = 2;
    if ($start >= $total) $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
    else $start = max(0, (int)$start - ((int)$start % (int)$kmess));
    $base_link = '<li class="page-item  "><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=%d' . '">%s</a></li>';
    $out[] = $start == 0 ? '' : sprintf($base_link, $start / $kmess, '<i class="ri-arrow-left-s-line align-middle"></i>');
    if ($start > $kmess * $neighbors) $out[] = sprintf($base_link, 1, '1');
    if ($start > $kmess * ($neighbors + 1)) $out[] = '<li class="page-item disabled"><a class="page-link">...</a></li>';
    for ($nCont = $neighbors;$nCont >= 1;$nCont--) if ($start >= $kmess * $nCont) {
        $tmpStart = $start - $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    $out[] = '<li class="page-item active"><a class="page-link">' . ($start / $kmess + 1) . '</a></li>';
    $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
    for ($nCont = 1;$nCont <= $neighbors;$nCont++) if ($start + $kmess * $nCont <= $tmpMaxPages) {
        $tmpStart = $start + $kmess * $nCont;
        $out[] = sprintf($base_link, $tmpStart / $kmess + 1, $tmpStart / $kmess + 1);
    }
    if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages) $out[] = '<li class="page-item disabled"><a class="page-link">...</a></li>';
    if ($start + $kmess * $neighbors < $tmpMaxPages) $out[] = sprintf($base_link, $tmpMaxPages / $kmess + 1, $tmpMaxPages / $kmess + 1);
    if ($start + $kmess < $total)
    {
        $display_page = ($start + $kmess) > $total ? $total : ($start / $kmess + 2);
        $out[] = sprintf($base_link, $display_page, '<i class="ri-arrow-right-s-line align-middle"></i>');
    }
    $out[] = '</ul></div>';
    return implode('', $out);
}

function pagination_client($url, $start, $total, $kmess){
    $out[] = '<div style="margin-top: 20px;">';
    $out[] = '<ul class="pagination pagination-separated justify-content-center mb-0">';
    
    // Nút Previous
    $prev_disabled = ($start == 0) ? ' disabled' : '';
    $prev_url = ($start > 0) ? strtr($url, array('%' => '%%')) . 'page=' . ($start / $kmess) : 'javascript:void(0);';
    $out[] = '<li class="page-item' . $prev_disabled . '">';
    $out[] = '<a href="' . $prev_url . '" class="page-link"><i class="mdi mdi-chevron-left"></i></a>';
    $out[] = '</li>';
    
    $neighbors = 2;
    if ($start >= $total) {
        $start = max(0, $total - (($total % $kmess) == 0 ? $kmess : ($total % $kmess)));
    } else {
        $start = max(0, (int)$start - ((int)$start % (int)$kmess));
    }
    
    // Trang đầu tiên
    if ($start > $kmess * $neighbors) {
        $out[] = '<li class="page-item"><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=1">1</a></li>';
    }
    
    // Dấu ... nếu cần
    if ($start > $kmess * ($neighbors + 1)) {
        $out[] = '<li class="page-item disabled"><a class="page-link">...</a></li>';
    }
    
    // Các trang trước trang hiện tại
    for ($nCont = $neighbors; $nCont >= 1; $nCont--) {
        if ($start >= $kmess * $nCont) {
            $tmpStart = $start - $kmess * $nCont;
            $out[] = '<li class="page-item"><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=' . ($tmpStart / $kmess + 1) . '">' . ($tmpStart / $kmess + 1) . '</a></li>';
        }
    }
    
    // Trang hiện tại
    $out[] = '<li class="page-item active"><a class="page-link">' . ($start / $kmess + 1) . '</a></li>';
    
    // Các trang sau trang hiện tại
    $tmpMaxPages = (int)(($total - 1) / $kmess) * $kmess;
    for ($nCont = 1; $nCont <= $neighbors; $nCont++) {
        if ($start + $kmess * $nCont <= $tmpMaxPages) {
            $tmpStart = $start + $kmess * $nCont;
            $out[] = '<li class="page-item"><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=' . ($tmpStart / $kmess + 1) . '">' . ($tmpStart / $kmess + 1) . '</a></li>';
        }
    }
    
    // Dấu ... nếu cần
    if ($start + $kmess * ($neighbors + 1) < $tmpMaxPages) {
        $out[] = '<li class="page-item disabled"><a class="page-link">...</a></li>';
    }
    
    // Trang cuối cùng
    if ($start + $kmess * $neighbors < $tmpMaxPages) {
        $out[] = '<li class="page-item"><a class="page-link" href="' . strtr($url, array('%' => '%%')) . 'page=' . ($tmpMaxPages / $kmess + 1) . '">' . ($tmpMaxPages / $kmess + 1) . '</a></li>';
    }
    
    // Nút Next
    $next_disabled = ($start + $kmess >= $total) ? ' disabled' : '';
    $next_url = ($start + $kmess < $total) ? strtr($url, array('%' => '%%')) . 'page=' . ($start / $kmess + 2) : 'javascript:void(0);';
    $out[] = '<li class="page-item' . $next_disabled . '">';
    $out[] = '<a href="' . $next_url . '" class="page-link"><i class="mdi mdi-chevron-right"></i></a>';
    $out[] = '</li>';
    
    $out[] = '</ul>';
    $out[] = '</div>';
    
    return implode('', $out);
}
function roundMoney($amount) {
    // Làm tròn số lên đến hàng chục gần nhất
    $roundedAmount = round($amount, -2);
    // Lấy phần dư của số sau khi làm tròn đến hàng chục gần nhất
    $remainder = $amount - $roundedAmount;
    // Nếu phần dư lớn hơn hoặc bằng 50, làm tròn lên, ngược lại làm tròn xuống
    // Nếu phần dư lớn hơn hoặc bằng 25 và nhỏ hơn 50, làm tròn xuống đến 250
    // Nếu phần dư lớn hơn hoặc bằng 5 và nhỏ hơn 25, làm tròn xuống đến 600
    if ($remainder >= 50) {
        $roundedAmount += 100;
    } elseif ($remainder >= 25) {
        $roundedAmount += 0; // không làm gì cả
    } elseif ($remainder >= 5) {
        $roundedAmount += 0; // không làm gì cả
    }
    return $roundedAmount;
}
function check_path($path){
    return preg_replace("/[^A-Za-z0-9_-]/", '', check_string(basename($path)));
}

function checkAddonLicense($licensekey, $project) {
    // 1. Kiểm tra domain whitelist
    $domain_white = [
        // Thêm các domain được whitelist tại đây
        // "localhost",
        // "127.0.0.1",
        // "shopcuaban.com"
    ];
    $domain = $_SERVER['HTTP_HOST'] ?? 'unknown';

    if (in_array($domain, $domain_white)) {
        // Nếu domain nằm trong whitelist => trả về "kích hoạt" luôn
        return [
            'msg'    => '',
            'status' => true
        ];
    }

    // -----------------------------------------------------------
    // 2. Thực hiện logic kiểm tra giấy phép (tích hợp từ check_license_addon)
    // -----------------------------------------------------------
    $whmcsurl            = 'https://client.cmsnt.co/';
    $licensing_secret_key= $project; // Project
    $localkeydays        = 15;
    $allowcheckfaildays  = 5;
    $check_token         = time() . md5(mt_rand(100000000, mt_getrandmax()) . $licensekey);
    $checkdate           = date("Ymd");
    $domain              = $_SERVER['SERVER_NAME'] ?? 'unknown-domain';
    $usersip             = $_SERVER['SERVER_ADDR'] ?? ($_SERVER['LOCAL_ADDR'] ?? '127.0.0.1');
    $dirpath             = dirname(__FILE__);
    $verifyfilepath      = 'modules/servers/licensing/verify.php';

    $localkey    = ''; // Trong ví dụ này, ta bỏ localkey = '' vì chưa thấy lưu localkey cũ

    // ===========================================
    // Hàm con: parseLocalKey (nếu cần parse local key cũ)
    // Ở đây, ta tạm bỏ qua parse localkey do code gốc tạm cài localkey=''
    // ===========================================

    // -------------------------------------------
    // Gửi request đến server license
    // -------------------------------------------
    $responseCode = 0;
    $postfields = [
        'licensekey' => $licensekey,
        'domain'     => $domain,
        'ip'         => $usersip,
        'dir'        => $dirpath
    ];
    if ($check_token) {
        $postfields['check_token'] = $check_token;
    }
    $query_string = http_build_query($postfields);

    // Thử gửi cURL
    if (function_exists('curl_exec')) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $data = curl_exec($ch);
        $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    } else {
        // Nếu không có cURL, fallback fsockopen (ít dùng)
        $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
        $fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
        if ($fp) {
            $newlinefeed = "\r\n";
            $header = "POST ".$whmcsurl . $verifyfilepath." HTTP/1.0".$newlinefeed;
            $header .= "Host: ".$whmcsurl.$newlinefeed;
            $header .= "Content-type: application/x-www-form-urlencoded".$newlinefeed;
            $header .= "Content-length: ".@strlen($query_string).$newlinefeed;
            $header .= "Connection: close".$newlinefeed.$newlinefeed;
            $header .= $query_string;
            $data = '';
            @stream_set_timeout($fp, 20);
            @fputs($fp, $header);
            $status = @socket_get_status($fp);
            while (!@feof($fp) && $status) {
                $line = @fgets($fp, 1024);
                if (!$responseCode &&
                    preg_match($responseCodePattern, trim($line), $m)
                ) {
                    $responseCode = empty($m[1]) ? 0 : $m[1];
                }
                $data .= $line;
                $status = @socket_get_status($fp);
            }
            @fclose($fp);
        }
    }

    // -------------------------------------------
    // Xử lý kết quả
    // -------------------------------------------
    // Nếu server license không trả về 200 => check local key cũ => tạm bỏ qua, coi như invalid
    if ($responseCode != 200) {
        return [
            'status' => false,
            'msg'    => 'Remote Check Failed (HTTP '.$responseCode.')'
        ];
    }

    // Trích xuất kết quả
    preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
    $results = [];
    foreach ($matches[1] as $k=>$v) {
        $results[$v] = $matches[2][$k];
    }

    // MD5 check
    if (isset($results['md5hash'])) {
        if ($results['md5hash'] != md5($licensing_secret_key.$check_token)) {
            return [
                'status' => false,
                'msg'    => 'MD5 Checksum Verification Failed'
            ];
        }
    }

    // -----------------------------------------------------------
    // 3. So khớp status => trả về msg & status (true/false)
    // -----------------------------------------------------------
    $status = $results['status'] ?? 'Invalid'; // Mặc định invalid nếu ko có

    // Mảng thông báo
    $status_messages = [
        'Active'    => ['Kích hoạt giấy phép thành công!',  true],
        'Invalid'   => ['Giấy phép kích hoạt không hợp lệ',  false],
        'Expired'   => ['Giấy phép mã nguồn đã hết hạn, vui lòng gia hạn ngay', false],
        'Suspended' => ['Giấy phép của bạn đã bị tạm ngưng', false],
        'timeout'   => ['Yêu cầu kiểm tra giấy phép đã hết thời gian chờ',  true]
    ];

    if (isset($status_messages[$status])) {
        list($msg, $stt) = $status_messages[$status];
        return [
            'msg'    => $msg,
            'status' => $stt
        ];
    } else {
        // Không match => default
        return [
            'msg'    => '',
            'status' => true
        ];
    }
}



function CMSNT_check_license($licensekey, $localkey='') {
    global $config;
    $whmcsurl = 'https://client.cmsnt.co/';
    $licensing_secret_key = $config['project'];
    $localkeydays = 15;
    $allowcheckfaildays = 5;
    $check_token = time() . md5(mt_rand(100000000, mt_getrandmax()) . $licensekey);
    $checkdate = date("Ymd");
    $domain = $_SERVER['SERVER_NAME'];
    $usersip = isset($_SERVER['SERVER_ADDR']) ? $_SERVER['SERVER_ADDR'] : $_SERVER['LOCAL_ADDR'];
    $dirpath = dirname(__FILE__);
    $verifyfilepath = 'modules/servers/licensing/verify.php';
    $localkeyvalid = false;
    if ($localkey) {
        $localkey = str_replace("\n", '', $localkey); # Remove the line breaks
        $localdata = substr($localkey, 0, strlen($localkey) - 32); # Extract License Data
        $md5hash = substr($localkey, strlen($localkey) - 32); # Extract MD5 Hash
        if ($md5hash == md5($localdata . $licensing_secret_key)) {
            $localdata = strrev($localdata); # Reverse the string
            $md5hash = substr($localdata, 0, 32); # Extract MD5 Hash
            $localdata = substr($localdata, 32); # Extract License Data
            $localdata = base64_decode($localdata);
            $localkeyresults = json_decode($localdata, true);
            $originalcheckdate = $localkeyresults['checkdate'];
            if ($md5hash == md5($originalcheckdate . $licensing_secret_key)) {
                $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - $localkeydays, date("Y")));
                if ($originalcheckdate > $localexpiry) {
                    $localkeyvalid = true;
                    $results = $localkeyresults;
                    $validdomains = explode(',', $results['validdomain']);
                    if (!in_array($_SERVER['SERVER_NAME'], $validdomains)) {
                        $localkeyvalid = false;
                        $localkeyresults['status'] = "Invalid";
                        $results = array();
                    }
                    $validips = explode(',', $results['validip']);
                    if (!in_array($usersip, $validips)) {
                        $localkeyvalid = false;
                        $localkeyresults['status'] = "Invalid";
                        $results = array();
                    }
                    $validdirs = explode(',', $results['validdirectory']);
                    if (!in_array($dirpath, $validdirs)) {
                        $localkeyvalid = false;
                        $localkeyresults['status'] = "Invalid";
                        $results = array();
                    }
                }
            }
        }
    }
    if (!$localkeyvalid) {
        $responseCode = 0;
        $postfields = array(
            'licensekey' => $licensekey,
            'domain' => $domain,
            'ip' => $usersip,
            'dir' => $dirpath,
        );
        if ($check_token) $postfields['check_token'] = $check_token;
        $query_string = '';
        foreach ($postfields AS $k=>$v) {
            $query_string .= $k.'='.urlencode($v).'&';
        }
        if (function_exists('curl_exec')) {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $whmcsurl . $verifyfilepath);
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $query_string);
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            // $curl_errno = curl_errno($ch);
            $responseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            // if ($curl_errno == CURLE_OPERATION_TIMEDOUT) {
            //     $results = array();
            //     $results['status'] = 'timeout';
            //     return $results;
            // }
        } else {
            $responseCodePattern = '/^HTTP\/\d+\.\d+\s+(\d+)/';
            $fp = @fsockopen($whmcsurl, 80, $errno, $errstr, 5);
            if ($fp) {
                $newlinefeed = "\r\n";
                $header = "POST ".$whmcsurl . $verifyfilepath . " HTTP/1.0" . $newlinefeed;
                $header .= "Host: ".$whmcsurl . $newlinefeed;
                $header .= "Content-type: application/x-www-form-urlencoded" . $newlinefeed;
                $header .= "Content-length: ".@strlen($query_string) . $newlinefeed;
                $header .= "Connection: close" . $newlinefeed . $newlinefeed;
                $header .= $query_string;
                $data = $line = '';
                @stream_set_timeout($fp, 20);
                @fputs($fp, $header);
                $status = @socket_get_status($fp);
                while (!@feof($fp)&&$status) {
                    $line = @fgets($fp, 1024);
                    $patternMatches = array();
                    if (!$responseCode
                        && preg_match($responseCodePattern, trim($line), $patternMatches)
                    ) {
                        $responseCode = (empty($patternMatches[1])) ? 0 : $patternMatches[1];
                    }
                    $data .= $line;
                    $status = @socket_get_status($fp);
                }
                @fclose ($fp);
            }
        }
        if ($responseCode != 200) {
            $localexpiry = date("Ymd", mktime(0, 0, 0, date("m"), date("d") - ($localkeydays + $allowcheckfaildays), date("Y")));
            if ($originalcheckdate > $localexpiry) {
                $results = $localkeyresults;
            } else {
                $results = array();
                $results['status'] = "Invalid";
                $results['description'] = "Remote Check Failed";
                return $results;
            }
        } else {
            preg_match_all('/<(.*?)>([^<]+)<\/\\1>/i', $data, $matches);
            $results = array();
            foreach ($matches[1] AS $k=>$v) {
                $results[$v] = $matches[2][$k];
            }
        }
        if (!is_array($results)) {
            die("Invalid License Server Response");
        }
        if (isset($results['md5hash'])) {
            if ($results['md5hash'] != md5($licensing_secret_key . $check_token)) {
                $results['status'] = "Invalid";
                $results['description'] = "MD5 Checksum Verification Failed";
                return $results;
            }
        }
        if ($results['status'] == "Active") {
            $results['checkdate'] = $checkdate;
            $data_encoded = json_encode($results);
            $data_encoded = base64_encode($data_encoded);
            $data_encoded = md5($checkdate . $licensing_secret_key) . $data_encoded;
            $data_encoded = strrev($data_encoded);
            $data_encoded = $data_encoded . md5($data_encoded . $licensing_secret_key);
            $data_encoded = wordwrap($data_encoded, 80, "\n", true);
            $results['localkey'] = $data_encoded;
        }
        $results['remotecheck'] = true;
    }
    unset($postfields,$data,$matches,$whmcsurl,$licensing_secret_key,$checkdate,$usersip,$localkeydays,$allowcheckfaildays,$md5hash);
    return $results;
}

function checkLicenseKey($licensekey)
{
    return ['msg' => '', 'status' => true];
}


/**
 * Tạo nội dung bằng AI
 * @param string $prompt Nội dung prompt
 * @return string JSON response
 */
function generateAIContent($prompt){
    global $CMSNT;
    $api_key     = $CMSNT->site('chatgpt_api_key'); // API key
    $model       = $CMSNT->site('chatgpt_model'); // Hoặc "gpt-4" nếu bạn có quyền truy cập
    if(empty($api_key)){
        return json_encode([
            'success' => false,
            'message' => __('Vui lòng cấu hình API Key trong cài đặt -> kết nối')
        ]);
    }
    $max_tokens  = 1000; // Số lượng token tối đa
    $temperature = 0.7; // Giá trị 0.7 này giúp AI tạo ra nội dung vừa sáng tạo vừa đảm bảo chất lượng, rất phù hợp cho các ứng dụng tạo nội dung tự động như mô tả sản phẩm, bài viết, v.v...
    $url     = 'https://api.openai.com/v1/chat/completions'; // URL API
    $headers = [
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
    ];
    $data = [
        'model' => $model,
        'messages' => [
            ['role' => 'user', 'content' => $prompt]
        ],
        'max_tokens'   => $max_tokens,
        'temperature'  => $temperature
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_TIMEOUT, 30); // Timeout 30 giây
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10); // Timeout kết nối 10 giây
    // Tắt xác minh SSL nếu môi trường của bạn cần
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $response = curl_exec($ch);
    $curl_errno = curl_errno($ch);
    if ($curl_errno) {
        $error_message = curl_error($ch);
        curl_close($ch);
        
        // Kiểm tra nếu là lỗi timeout
        if ($curl_errno == CURLE_OPERATION_TIMEDOUT) {
            return json_encode([
                'success' => false,
                'message' => __('Yêu cầu tạo nội dung AI đã hết thời gian chờ (30 giây). Vui lòng thử lại sau.')
            ]);
        }
        
        return json_encode([
            'success' => false,
            'message' => __('Curl Error: ') . $error_message
        ]);
    }

    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code != 200) {
        return json_encode([
            'success' => false,
            'message' => __('HTTP Error: ') . $http_code . ' => ' . $response
        ]);
    }
    curl_close($ch);

    $response_data = json_decode($response, true);
    if (!$response_data) {
        return json_encode([
            'success' => false,
            'message' => __('AI đang gián đoạn, đang cố gắng thử lại sau: ') . $response
        ]);
    }
    // Lấy kết quả từ API nếu có
    if (isset($response_data['choices'][0]['message']['content'])) {
        $generatedContent = $response_data['choices'][0]['message']['content'];
        return json_encode([
            'success'     => true,
            'description' => $generatedContent
        ]);
    } else {
        return json_encode([
            'success' => false,
            'message' => __('No response generated')
        ]);
    } 
}

 

/**
 * Tự động cập nhật cấp bậc cho user dựa trên total_money
 * @param int $user_id ID của user
 * @param float $total_money Tổng tiền nạp của user
 */
function updateUserRank($user_id, $total_money) {
    global $CMSNT;
    if($total_money <= 0){
        return false;
    }
    // Lấy danh sách ranks đang hoạt động, sắp xếp theo min DESC để lấy rank cao nhất trước
    $ranks = $CMSNT->get_list("SELECT * FROM `ranks` WHERE `status` = 1 ORDER BY `min` DESC");
    
    if(!$ranks) {
        return false;
    }
    
    $suitable_rank = null;
    
    // Tìm rank phù hợp nhất (rank cao nhất mà user đủ điều kiện)
    foreach($ranks as $rank) {
        if($total_money >= $rank['min']) {
            $suitable_rank = $rank;
            break; // Lấy rank đầu tiên thỏa mãn (cao nhất)
        }
    }
    
    if($suitable_rank) {
        // Lấy rank hiện tại của user
        $current_user = $CMSNT->get_row("SELECT `rank_id` FROM `users` WHERE `id` = '".$user_id."'");
        
        if($current_user && $current_user['rank_id']) {
            // Lấy thông tin rank hiện tại
            $current_rank = $CMSNT->get_row("SELECT * FROM `ranks` WHERE `id` = '".$current_user['rank_id']."'");
            
            // CHỈ UPGRADE RANK - Không downgrade rank đã được set thủ công
            // Chỉ update nếu rank mới cao hơn rank hiện tại (so sánh theo min)
            if($current_rank && $suitable_rank['min'] > $current_rank['min']) {
                $update_result = $CMSNT->update("users", [
                    'rank_id' => $suitable_rank['id']
                ], " `id` = '".$user_id."' ");
                
                if($update_result) {
                    // Log hoạt động
                    $CMSNT->insert("logs", [
                        'user_id' => $user_id,
                        'ip' => myip(),
                        'device' => getUserAgent(),
                        'createdate' => gettime(),
                        'action' => sprintf(__('Tự động nâng cấp: %s → %s (Tổng nạp: %s)'), $current_rank['name'], $suitable_rank['name'], format_currency($total_money))
                    ]);
                    
                    return true;
                }
            }
        } else {
            // User chưa có rank, set rank đầu tiên
            $update_result = $CMSNT->update("users", [
                'rank_id' => $suitable_rank['id']
            ], " `id` = '".$user_id."' ");
            
            if($update_result) {
                // Log hoạt động
                $CMSNT->insert("logs", [
                    'user_id'       => $user_id,
                    'ip'            => myip(),
                    'device'        => getUserAgent(),
                    'createdate'    => gettime(),
                    'action'        => sprintf(__('Tự động thiết lập cấp bậc: %s (Tổng nạp: %s)'), $suitable_rank['name'], format_currency($total_money))
                ]);
                return true;
            }
        }
    }
    return false;
}


function whereInvoiceWaiting($payment_method, $amount){
    global $CMSNT;
    return $CMSNT->get_list(
        "SELECT * FROM `payment_bank_invoice` WHERE 
        `status` = 'waiting' AND 
        `short_name` = '$payment_method' AND 
        `amount` <= '$amount' AND 
        `api_tid` IS NULL AND
        ".time()." - `create_time` < ".$CMSNT->site('bank_expired_invoice')."
        ORDER BY id DESC "
    );
}

function get_device_by_user_agent($ua_string) {
    if (empty($ua_string)) {
        return __('Thiết bị không xác định');
    }

    $ua_string_lower = strtolower($ua_string);

    // Phones
    if (strpos($ua_string_lower, 'iphone') !== false) {
        return __('Điện thoại iPhone');
    }
    if (strpos($ua_string_lower, 'android') !== false && strpos($ua_string_lower, 'mobile') !== false) {
        return __('Điện thoại Android');
    }
    if (strpos($ua_string_lower, 'windows phone') !== false) {
        return __('Điện thoại Windows');
    }
    
    // Tablets
    if (strpos($ua_string_lower, 'ipad') !== false) {
        return __('Máy tính bảng iPad');
    }
    if (strpos($ua_string_lower, 'android') !== false) {
        return __('Máy tính bảng Android');
    }

    // Desktops
    if (strpos($ua_string_lower, 'windows') !== false) {
        return __('Máy tính Windows');
    }
    if (strpos($ua_string_lower, 'macintosh') !== false || strpos($ua_string_lower, 'mac os x') !== false) {
        return __('Máy tính Mac');
    }
    if (strpos($ua_string_lower, 'linux') !== false) {
        return __('Máy tính Linux');
    }
    
    return __('Thiết bị không xác định');
}

/**
 * Tạo mã đơn hàng duy nhất dựa vào cấu hình hệ thống
 * 
 * @return string Mã đơn hàng duy nhất
 */
function generateOrderTransactionId() {
    global $CMSNT;
    
    do {
        if($CMSNT->site('random_transid_order_type') == 'string'){
            $trans_id = $CMSNT->site('prefix_transid_order').random('QWERTYUOPASDFGHJKLZXCVBNM', intval($CMSNT->site('random_transid_order_length')));
        }
        elseif($CMSNT->site('random_transid_order_type') == 'string_number'){
            $trans_id = $CMSNT->site('prefix_transid_order').random('123456789QWERTYUOPASDFGHJKLZXCVBNM', intval($CMSNT->site('random_transid_order_length')));
        }
        else{
            $trans_id = $CMSNT->site('prefix_transid_order').random('123456789', intval($CMSNT->site('random_transid_order_length')));
        }
    } while($CMSNT->num_rows("SELECT * FROM `orders` WHERE `trans_id` = '$trans_id'") > 0);
    
    return $trans_id;
}