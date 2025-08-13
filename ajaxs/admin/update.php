<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../libs/database/users.php');
require_once(__DIR__.'/../../models/is_admin.php');


if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}
if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
}


if($_POST['action'] == 'set_webhook'){
    if(checkPermission($getUser['admin'], 'edit_setting') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $webhook_secret = check_string($_POST['telegram_webhook_secret']);
    if (empty($webhook_secret)) {
        die(json_encode(['status' => 'error', 'msg' => __('Secret token không được để trống')]));
    }
    // Kiểm tra độ dài secret token (phải là 64 ký tự hex)
    if (strlen($webhook_secret) !== 64 || !ctype_xdigit($webhook_secret)) {
        die(json_encode(['status' => 'error', 'msg' => __('Secret token không hợp lệ (phải là 64 ký tự hex)')]));
    }
    // Lấy thông tin bot
    $bot_token = $CMSNT->site('telegram_token');
    $telegram_url = $CMSNT->site('telegram_url');
    
    if (empty($bot_token)) {
        die(json_encode(['status' => 'error', 'msg' => __('Chưa cấu hình Telegram Bot Token')]));
    }
    
    // Cập nhật secret token vào database trước
    $isUpdate = $CMSNT->update("settings", [
        'value' => $webhook_secret
    ], " `name` = 'telegram_webhook_secret' ");
    
    if ($isUpdate) {
        // Tạo webhook URL
        $webhook_url = base_url('api/webhook_telegram.php');
        // Gọi API Telegram để set webhook
        $url = $telegram_url . "bot{$bot_token}/setWebhook";
        $post_data = [
            'url' => $webhook_url,
            'secret_token' => $webhook_secret,
            'max_connections' => 10,
            'drop_pending_updates' => true
        ];
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        
        $response = curl_exec($ch);
        $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        $result = json_decode($response, true);

        if ($http_code !== 200) {
            die(json_encode([
                'status' => 'error', 
                'msg' => check_string($result['description']) . ": HTTP $http_code"
            ]));
        }
        
        
        if (!$result) {
            die(json_encode([
                'status' => 'error', 
                'msg' => __('Telegram API trả về dữ liệu không hợp lệ')
            ]));
        }
        
        if ($result['ok']) {
            // Log hoạt động
            $CMSNT->insert("logs", [
                'user_id' => $getUser['id'],
                'ip' => myip(),
                'device' => getUserAgent(),
                'createdate' => gettime(),
                'action' => __('Set Telegram Webhook với Secret Token mới')
            ]);
            
            die(json_encode([
                'status' => 'success',
                'msg' => __('Webhook đã được thiết lập thành công với bảo mật!'),
                'webhook_url' => $webhook_url,
                'secret_preview' => substr($webhook_secret, 0, 8) . '...'
            ]));
        } else {
            die(json_encode([
                'status' => 'error',
                'msg' => __('Lỗi từ Telegram') . ': ' . ($result['description'] ?? 'Unknown error')
            ]));
        }
    }
    die(json_encode(['status' => 'error', 'msg' => __('Set webhook thất bại')]));
}

if($_POST['action'] == 'reset_total_money_users'){
    if(checkPermission($getUser['admin'], 'edit_user') != true){
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $isUpdate = $CMSNT->update('users', [
        'total_money'  => 0
    ], " `total_money` > 0 ");
    if(isset($isUpdate)){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Reset tổng nạp toàn bộ thành viên')
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Reset tổng nạp toàn bộ user thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Reset thất bại')]));
}
if($_POST['action'] == 'update_status_user'){
    if(checkPermission($getUser['admin'], 'edit_user') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    if(!$user = $CMSNT->get_row(" SELECT * FROM `users` WHERE `id` = '".check_string($_POST['id'])."' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Thành viên không tồn tại trong hệ thống')]));
    }
    $isUpdate = $CMSNT->update("users", [
        'banned'    => !empty($_POST['status']) ? check_string($_POST['status']) : 0
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái thành viên (Tên: %s - ID: %s)'), $user['username'], $user['id'])
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}


 

if($_POST['action'] == 'update_stt_table_product'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $id = intval(check_string($_POST['id']));
    if(!$service = $CMSNT->get_row(" SELECT * FROM `services` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Dịch vụ không tồn tại trong hệ thống')]));
    }
    $stt = intval(check_string($_POST['stt']));
    $isUpdate = $CMSNT->update("services", [
        'stt' => $stt
    ], " `id` = '$id' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật STT dịch vụ (ID %s)'), $id)
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật STT thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật STT thất bại')]));
}

if($_POST['action'] == 'update_category_category'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("categories", [
        'parent_id'    => !empty($_POST['category_id']) ? check_string($_POST['category_id']) : 0
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật chuyên mục cha cho chuyên mục (ID %s)'), check_string($_POST['id']))
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật chuyên mục cha thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}

if($_POST['action'] == 'update_category_product'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("products", [
        'category_id'    => !empty($_POST['category_id']) ? check_string($_POST['category_id']) : 0
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật chuyên mục cho sản phẩm (ID %s)'), check_string($_POST['id']))
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật chuyên mục thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}
if($_POST['action'] == 'updateTableProductAPI'){
    if(checkPermission($getUser['admin'], 'manager_suppliers') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("suppliers", [
        'status'    => !empty($_POST['status']) ? check_string($_POST['status']) : 0
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Update Supplier (ID '.check_string($_POST['id']).')'
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}

if($_POST['action'] == 'updateTableCategory'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $isUpdate = $CMSNT->update("categories", [
        'stt'       => !empty($_POST['stt']) ? check_string($_POST['stt']) : 0,
        'status'    => !empty($_POST['status']) ? check_string($_POST['status']) : 'hide'
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Update Table Category (ID '.check_string($_POST['id']).')'
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}



if($_POST['action'] == 'update_status_category'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("categories", [
        'status'    => !empty($_POST['status']) ? check_string($_POST['status']) : 0
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái chuyên mục (ID %s)'), check_string($_POST['id']))
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}

if($_POST['action'] == 'update_status_table_service'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $id = intval(check_string($_POST['id']));
    if(!$service = $CMSNT->get_row(" SELECT * FROM `services` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Dịch vụ không tồn tại trong hệ thống')]));
    }
    $status = !empty($_POST['status']) ? 'show' : 'hide';
    $isUpdate = $CMSNT->update("services", [
        'display' => $status
    ], " `id` = '$id' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái dịch vụ (ID %s)'), $id)
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật trạng thái thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật trạng thái thất bại')]));
}


if($_POST['action'] == 'update_status_table_category'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $id = intval(check_string($_POST['id']));
    if(!$category = $CMSNT->get_row(" SELECT * FROM `categories` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục không tồn tại trong hệ thống')]));
    }
    $status = !empty($_POST['status']) ? 'show' : 'hide';
    $isUpdate = $CMSNT->update("categories", [
        'status' => $status
    ], " `id` = '$id' ");
    if($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái chuyên mục (ID %s)'), $id)
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}



if($_POST['action'] == 'cancel_email_campaigns'){
    if(checkPermission($getUser['admin'], 'edit_email_campaigns') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("email_campaigns", [
        'status'  => 2
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}

if($_POST['action'] == 'setDefaultLanguage'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    if (empty($_POST['id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không tồn tại')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('Dữ liệu không tồn tại')
        ]);
        die($data);
    }
    $CMSNT->update("languages", [
        'lang_default' => 0
    ], " `id` > 0 ");
    $isUpdate = $CMSNT->update("languages", [
        'lang_default' => 1
    ], " `id` = '$id' ");
    if ($isUpdate) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Thiết lập ngôn ngữ mặc định (%s ID %s)'), $row['lang'], $row['id'])
        ]);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Thay đổi ngôn ngữ mặc định thành công')
        ]);
        die($data);
    }
}

if($_POST['action'] == 'changeTranslate'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isUpdate = $CMSNT->update("translate", [
        'value'  => check_string($_POST['value'])
    ], " `id` = '".check_string($_POST['id'])."' ");
    if($isUpdate){
        die(json_encode(['status' => 'success', 'msg' => __('Update successful!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Update failed!')]));
}

if($_POST['action'] == 'setDefaultCurrency'){
    if(checkPermission($getUser['admin'], 'edit_currency') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('ID tiền tệ không tồn tại trong hệ thống')
        ]);
        die($data);
    }
    $CMSNT->update("currencies", [
        'default_currency' => 0
    ], " `id` > 0 ");
    $isUpdate = $CMSNT->update("currencies", [
        'default_currency' => 1
    ], " `id` = '$id' ");
    if ($isUpdate){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Set mặc định tiền tệ (%s ID %s)'), $row['name'], $row['id'])
        ]);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Thay đổi trạng thái tiền tệ thành công')
        ]);
        die($data);
    }else{
        die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
    }
}

if($_POST['action'] == 'logoutALL'){
    if(checkPermission($getUser['admin'], 'edit_user') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    // Xóa tất cả phiên đăng nhập
    $CMSNT->remove("active_sessions", " `id` > 0 ");
    
    foreach($CMSNT->get_list(" SELECT * FROM `users` WHERE `id` > 0 ") as $row){
        $CMSNT->update('users', [
            'token'     => generateUltraSecureToken(32)
        ], " `id` = '".$row['id']."' ");
    }
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Log out all members on the system')
    ]);
    $data = json_encode([
        'status'    => 'success',
        'msg'       => __('Đăng xuất tất cả tài khoản thành công!')
    ]);
    die($data);
}

if($_POST['action'] == 'changeAPIKey'){
    if(checkPermission($getUser['admin'], 'edit_user') != true){
        die(json_encode(['status' => 'error', 'msg' => 'Bạn không có quyền sử dụng tính năng này']));
    }
    foreach($CMSNT->get_list(" SELECT * FROM `users`  ") as $row){
        $CMSNT->update('users', [
            'api_key'     => generateUltraSecureToken(16)
        ], " `id` = '".$row['id']."' ");
    }
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Change API Key for all members')
    ]);

    $data = json_encode([
        'status'    => 'success',
        'msg'       => __('Thay đổi API KEY thành công!')
    ]);
    die($data);
}


// Thêm hàm xử lý cập nhật thứ tự chuyên mục khi kéo thả
if ($_POST['action'] == 'updateCategorySTT') {
    if (isset($_POST['order']) && is_array($_POST['order'])) {
        $order = $_POST['order'];
        
        foreach ($order as $item) {
            $id = isset($item['id']) ? intval($item['id']) : 0;
            $position = isset($item['position']) ? intval($item['position']) : 0;
            
            if ($id > 0) {
                $CMSNT->update("categories", [
                    'stt' => $position
                ], " `id` = $id ");
            }
        }
        
        die(json_encode([
            'status' => 'success',
            'msg' => __('Cập nhật thứ tự thành công!')
        ]));
    }
}

if ($_POST['action'] == 'updateChildCategorySTT') {
    if (isset($_POST['order']) && is_array($_POST['order'])) {
        $order = $_POST['order'];
        
        foreach ($order as $item) {
            $id = isset($item['id']) ? intval($item['id']) : 0;
            $position = isset($item['position']) ? intval($item['position']) : 0;
            $parent_id = isset($item['parent_id']) ? intval($item['parent_id']) : 0;
            
            if ($id > 0 && $parent_id > 0) {
                // Cập nhật thứ tự cho danh mục con, đảm bảo nó thuộc danh mục cha đúng
                $check = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = $id AND `parent_id` = $parent_id");
                if ($check) {
                    $CMSNT->update("categories", [
                        'stt' => $position
                    ], " `id` = $id ");
                }
            }
        }
        
        die(json_encode([
            'status' => 'success',
            'msg' => __('Cập nhật thứ tự chuyên mục con thành công!')
        ]));
    }
}
if($_POST['action'] == 'updateCategorySubSTT'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    if(empty($_POST['order'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    $order = json_decode($_POST['order'], true);
    if(!is_array($order)){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    
    foreach($order as $item){
        $id = intval($item['id']);
        $position = intval($item['position']);
        if($id > 0){
            $CMSNT->update("categories", [
                'stt' => $position
            ], " `id` = '$id' ");
        }
    }
    
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Cập nhật thứ tự sắp xếp chuyên mục con')
    ]);
    
    die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thứ tự thành công')]));
}

// Cập nhật thứ tự dịch vụ sau khi kéo thả
if($_POST['action'] == 'updateServiceSTT'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    if(empty($_POST['order'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    $order = json_decode($_POST['order'], true);
    if(!is_array($order)){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    
    foreach($order as $item){
        $id = intval($item['id']);
        $position = intval($item['position']);
        if($id > 0){
            $CMSNT->update("services", [
                'stt' => $position
            ], " `id` = '$id' ");
        }
    }
    
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Cập nhật thứ tự sắp xếp dịch vụ')
    ]);
    
    die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thứ tự thành công')]));
}

// Cập nhật hàng loạt dịch vụ
if($_POST['action'] == 'bulkUpdateServices'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['data'])){
        die(json_encode(['status' => 'error', 'msg' => __('Không có dữ liệu để cập nhật')]));
    }
    
    $data = json_decode($_POST['data'], true);
    if(!is_array($data) || !isset($data['field']) || !isset($data['productIds']) || empty($data['productIds'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }
    
    $field = $data['field'];
    $productIds = $data['productIds'];
    $updateCount = 0;
    
    // Xử lý cập nhật tùy theo trường
    switch($field){
        case 'status':
            if(!isset($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Thiếu giá trị cập nhật')]));
            }
            $value = $data['value'] == 'show' ? 'show' : 'hide';
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("services", ['display' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
            
        case 'category_id':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn chuyên mục')]));
            }
            $value = intval($data['value']);
            // Kiểm tra chuyên mục tồn tại
            if(!$CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$value'")){
                die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục không tồn tại')]));
            }
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("services", ['category_id' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
            
        case 'price':
        case 'price_1':
        case 'price_2':
        case 'price_3':
            // Xác định trường giá cần cập nhật
            $priceField = check_string($field); // Trường sẽ là 'price', 'price_1', 'price_2', hoặc 'price_3'
            
            if(isset($data['value']) && !empty($data['value'])){
                // Cập nhật giá cố định
                $value = floatval($data['value']);
                if($value < 0){
                    die(json_encode(['status' => 'error', 'msg' => __('Giá bán không được âm')]));
                }
                foreach($productIds as $id){
                    $id = intval($id);
                    if($CMSNT->update("services", [$priceField => $value], " `id` = '$id' ")){
                        $updateCount++;
                    }
                }
            } elseif(isset($data['percentValue']) && !empty($data['percentValue']) && isset($data['percentAction'])) {
                // Cập nhật theo % tăng/giảm dựa trên giá vốn (cost)
                $percentValue = floatval($data['percentValue']);
                $percentAction = $data['percentAction'] == 'increase' ? 'increase' : 'decrease';
                
                foreach($productIds as $id){
                    $id = intval($id);
                    $service = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$id'");
                    if($service){
                        $costPrice = floatval($service['cost']); // Lấy giá vốn làm cơ sở tính toán
                        $adjustment = $costPrice * ($percentValue / 100);
                        $newPrice = $percentAction == 'increase' ? $costPrice + $adjustment : $costPrice - $adjustment;
                        $newPrice = round($newPrice, 2); // Làm tròn giá đến 2 chữ số thập phân
                        if($newPrice < 0) $newPrice = 0; // Đảm bảo giá không âm
                        
                        if($CMSNT->update("services", [$priceField => $newPrice], " `id` = '$id' ")){
                            $updateCount++;
                        }
                    }
                }
            } else {
                die(json_encode(['status' => 'error', 'msg' => __('Không có giá trị để cập nhật')]));
            }
            break;
        case 'cost':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập giá vốn')]));
            }
            $value = floatval($data['value']);
            if($value < 0){
                die(json_encode(['status' => 'error', 'msg' => __('Giá vốn không được âm')]));
            }
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("services", ['cost' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
            
        case 'min':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập số lượng tối thiểu')]));
            }
            $value = intval($data['value']);
            if($value <= 0){
                die(json_encode(['status' => 'error', 'msg' => __('Số lượng tối thiểu phải lớn hơn 0')]));
            }
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("services", ['min' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
            
        case 'max':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập số lượng tối đa')]));
            }
            $value = intval($data['value']);
            if($value <= 0){
                die(json_encode(['status' => 'error', 'msg' => __('Số lượng tối đa phải lớn hơn 0')]));
            }
            foreach($productIds as $id){
                $id = intval($id);
                // Kiểm tra min <= max
                $service = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$id'");
                if($service && $service['min'] > $value){
                    continue; // bỏ qua nếu min > max
                }
                if($CMSNT->update("services", ['max' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;

        case 'description':
            $value = $data['value'];
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("services", ['description' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
        default:
            die(json_encode(['status' => 'error', 'msg' => __('Trường cập nhật không hợp lệ')]));
    }
    
    if($updateCount > 0){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Cập nhật hàng loạt').' '.$updateCount.' '.check_string($_POST['action']).' (trường: '.$field.')'
        ]);
        
        die(json_encode([
            'status' => 'success', 
            'msg' => __('Đã cập nhật thành công').' '.$updateCount.' '.check_string($_POST['action'])
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Không có dịch vụ nào được cập nhật')]));
}

// Cập nhật hàng loạt chuyên mục con
if($_POST['action'] == 'bulkUpdateCategorySub'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['data'])){
        die(json_encode(['status' => 'error', 'msg' => __('Không có dữ liệu để cập nhật')]));
    }
    
    $data = json_decode($_POST['data'], true);
    if(!is_array($data) || !isset($data['field']) || !isset($data['productIds']) || empty($data['productIds'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }
    
    $field = $data['field'];
    $productIds = $data['productIds'];
    $updateCount = 0;

    switch($field){
        // CẬP NHẬT TRẠNG THÁI CHUYÊN MỤC
        case 'status':
            if(!isset($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Thiếu giá trị cập nhật')]));
            }
            $value = $data['value'] == 'show' ? 'show' : 'hide';
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("categories", ['status' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
        // CHUYỂN CHUYÊN MỤC CON SANG CHUYÊN MỤC CHA MỚI
        case 'category_id':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn chuyên mục cha')]));
            }
            $value = intval($data['value']);
            // Kiểm tra chuyên mục tồn tại
            if(!$CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$value'")){
                die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục cha không tồn tại')]));
            }
            foreach($productIds as $id){
                $id = intval($id);
                if($CMSNT->update("categories", ['parent_id' => $value], " `id` = '$id' ")){
                    $updateCount++;
                }
            }
            break;
        // CHUYỂN DỊCH VỤ SANG CHUYÊN MỤC MỚI
        case 'convert_category_id':
            if(!isset($data['value']) || empty($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn chuyên mục mới')]));
            }
            $value = intval($data['value']);
            foreach($productIds as $category_id){
                $category_id = intval($category_id);
                // LẤY DANH SÁCH DỊCH VỤ THEO ID CHUYÊN MỤC
                foreach($CMSNT->get_list("SELECT * FROM `services` WHERE `category_id` = '$category_id' ") as $row){
                    // CẬP NHẬT DỊCH VỤ
                    if($CMSNT->update("services", ['category_id' => $value], " `id` = '".$row['id']."' ")){
                        $updateCount++;
                    }
                }
            }
            break;
        default:
            die(json_encode(['status' => 'error', 'msg' => __('Trường cập nhật không hợp lệ')]));
    }
    
    if($updateCount > 0){

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Cập nhật hàng loạt').' '.$updateCount.' '.check_string($_POST['action']).' (trường: '.$field.')'
        ]);
        
        die(json_encode([
            'status' => 'success', 
            'msg' => __('Đã cập nhật thành công').' '.$updateCount.' '.check_string($_POST['action'])
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Không có dịch vụ nào được cập nhật')]));
}

if($_POST['action'] == 'bulkUpdateOrders'){
    if(checkPermission($getUser['admin'], 'edit_orders_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['data'])){
        die(json_encode(['status' => 'error', 'msg' => __('Không có dữ liệu để cập nhật')]));
    }
    
    $data = json_decode($_POST['data'], true);
    if(!is_array($data) || !isset($data['field']) || !isset($data['orderIds']) || empty($data['orderIds'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }
    
    $field = $data['field'];
    $orderIds = $data['orderIds'];
    $updateCount = 0;
    $refundCount = 0;
    
    // Xử lý cập nhật tùy theo trường
    switch($field){
        case 'status':
            if(!isset($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Thiếu giá trị cập nhật')]));
            }
            $value = check_string($data['value']);
            foreach($orderIds as $id){
                $id = intval($id);
                if(!$row = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '$id' ")){
                    continue;
                }
                
                // Kiểm tra nếu đơn hàng đã ở trạng thái Canceled hoặc Partial rồi thì không cho phép thay đổi
                if(($row['status'] == 'Canceled' || $row['status'] == 'Partial')) {
                    continue; // Bỏ qua đơn hàng này, không cho phép thay đổi
                }
                
                // Nếu cập nhật thành trạng thái Canceled và chưa được hoàn tiền
                if($value == 'Canceled' && $row['status'] != 'Canceled' && $row['pay'] > 0) {
                    // Thực hiện hoàn tiền
                    $User = new users();
                    $isRefund = $User->RefundCredits($row['user_id'], $row['pay'], 
                        '[Admin] '.sprintf(__("Hoàn tiền đơn hàng #%s"), $row['trans_id']), 
                        'Canceled_'.$row['trans_id']);
                    
                    if($isRefund) {
                        
                        // Cập nhật trạng thái đơn hàng
                        $CMSNT->update("orders", [
                            'status' => $value,
                            'pay' => 0,
                            'cost' => 0,
                            'updated_at' => gettime()
                        ], " `id` = '$id' ");
                        
                
                        $CMSNT->insert("logs", [
                            'user_id'       => $getUser['id'],
                            'ip'            => myip(),
                            'device'        => getUserAgent(),
                            'createdate'    => gettime(),
                            'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s và hoàn tiền tự động'), $row['trans_id'], $value)
                        ]);
                        
                        $refundCount++;
                        $updateCount++;
                    } else {
                        continue; // Bỏ qua nếu không hoàn tiền được
                    }
                }
                // Xử lý trường hợp Partial - hoàn tiền một phần
                else if($value == 'Partial' && isset($data['remains'])) {
                    $remains = intval($data['remains']);
                    $originalQty = $row['quantity'];
                    
                    // Kiểm tra giá trị remains hợp lệ
                    if($remains < 0 || $remains >= $originalQty) {
                        continue; // Bỏ qua nếu remains không hợp lệ
                    }
                    
                    // Tính số lượng cần hoàn
                    $refundQty = $originalQty - $remains;
                    
                    // Tính số tiền cần hoàn lại
                    $refundAmount = 0;
                    if($originalQty > 0) {
                        $pricePerUnit = $row['pay'] / $originalQty; // Giá trên mỗi đơn vị
                        $refundAmount = $pricePerUnit * $refundQty;
                    }
                    
                    if($refundAmount > 0) {
                        // Thực hiện hoàn tiền
                        $User = new users();
                        $isRefund = $User->RefundCredits($row['user_id'], $refundAmount, 
                            '[Admin] '.sprintf(__("Hoàn tiền một phần cho đơn hàng #%s (Số lượng còn lại: %d)"), $row['trans_id'], $remains), 
                            'Partial_'.$row['trans_id']);
                        
                        if($isRefund) {
                            
                            // Tính toán giá vốn mới
                            $newCost = 0;
                            if($originalQty > 0) {
                                $costPerUnit = $row['cost'] / $originalQty;
                                $newCost = $costPerUnit * $remains;
                            }
                            
                            // Cập nhật trạng thái đơn hàng
                            $CMSNT->update("orders", [
                                'status' => $value,
                                'remains' => 0,
                                'quantity' => $row['quantity'] - $remains,
                                'pay' => $row['pay'] - $refundAmount,
                                'cost' => $newCost,
                                'updated_at' => gettime()
                            ], " `id` = '$id' ");
                            
                    
                            $CMSNT->insert("logs", [
                                'user_id'       => $getUser['id'],
                                'ip'            => myip(),
                                'device'        => getUserAgent(),
                                'createdate'    => gettime(),
                                'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s và hoàn tiền một phần (Số lượng còn lại: %d)'), $row['trans_id'], $value, $remains)
                            ]);
                            
                            $refundCount++;
                            $updateCount++;
                        } else {
                            continue; // Bỏ qua nếu không hoàn tiền được
                        }
                    } else {
                        // Chỉ cập nhật trạng thái nếu không cần hoàn tiền
                        if($CMSNT->update("orders", [
                            'status' => $value,
                            'remains' => $remains,
                            'updated_at' => gettime()
                        ], " `id` = '$id' ")){
                    
                            $CMSNT->insert("logs", [
                                'user_id'       => $getUser['id'],
                                'ip'            => myip(),
                                'device'        => getUserAgent(),
                                'createdate'    => gettime(),
                                'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s (Số lượng còn lại: %d)'), $row['trans_id'], $value, $remains)
                            ]);
                            $updateCount++;
                        }
                    }
                }
                else {
                    // Cập nhật bình thường nếu không phải trạng thái Canceled hoặc Partial
                    if($CMSNT->update("orders", [
                        'status' => $value,
                        'updated_at' => gettime()
                    ], " `id` = '$id' ")){
                
                        $CMSNT->insert("logs", [
                            'user_id'       => $getUser['id'],
                            'ip'            => myip(),
                            'device'        => getUserAgent(),
                            'createdate'    => gettime(),
                            'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s'), $row['trans_id'], $value)
                        ]);
                        $updateCount++;
                    }
                }
            }
            break;
        case 'note':
            if(!isset($data['value'])){
                die(json_encode(['status' => 'error', 'msg' => __('Thiếu giá trị cập nhật')]));
            }
            $value = check_string($data['value']);
            foreach($orderIds as $id){
                $id = intval($id);
                if(!$row = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '$id' ")){
                    continue;
                }
                if($CMSNT->update("orders", ['note' => $value], " `id` = '$id' ")){
            
                    $CMSNT->insert("logs", [
                        'user_id'       => $getUser['id'],
                        'ip'            => myip(),
                        'device'        => getUserAgent(),
                        'createdate'    => gettime(),
                        'action'        => sprintf(__('Cập nhật ghi chú đơn hàng #%s'), $row['trans_id'])
                    ]);
                    $updateCount++;
                }
            }
            break;
        default:
            die(json_encode(['status' => 'error', 'msg' => __('Trường cập nhật không được hỗ trợ')]));
    }
    
    if($updateCount > 0){
        $msg = __('Cập nhật thành công').' '.$updateCount.' '.__('đơn hàng');
        if($refundCount > 0) {
            $msg .= ', '.__('đã hoàn tiền').' '.$refundCount.' '.__('đơn hàng');
        }
        die(json_encode([
            'status' => 'success', 
            'msg' => $msg
        ]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Không có đơn hàng nào được cập nhật')]));
}

if($_POST['action'] == 'syncTranslate'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['lang_id'])){
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy thông tin ngôn ngữ')]));
    }
    
    $lang_id = check_string($_POST['lang_id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$lang_id' ");
    if (!$row) {
        die(json_encode(['status' => 'error', 'msg' => __('Ngôn ngữ không tồn tại trong hệ thống')]));
    }
    
    // Đọc file lang.php để lấy dữ liệu mặc định
    $langDefault = [];
    if (file_exists(__DIR__ . '/../../lang.php')) {
        include(__DIR__ . '/../../lang.php');
    }
    
    if (!empty($langDefault)) {
        $insertCount = 0;
        foreach ($langDefault as $key => $value) {
            $isExist = $CMSNT->get_row("SELECT * FROM `translate` WHERE `lang_id` = '$lang_id' AND `name` = '$key' ");
            if($isExist) {
                continue;
            }
            $isInsert = $CMSNT->insert("translate", [
                'lang_id'   => $lang_id,
                'value'     => $value,
                'name'      => $key
            ]);
            if($isInsert) {
                $insertCount++;
            }
        }
        
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Đồng bộ bản dịch từ lang.php cho ngôn ngữ %s (%d items)'), $row['lang'], $insertCount)
        ]);
        
        die(json_encode([
            'status' => 'success',
            'msg' => sprintf(__('Đồng bộ bản dịch thành công! %d nội dung đã được đồng bộ.'), $insertCount),
            'count' => $insertCount
        ]));
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy dữ liệu trong file lang.php')]));
    }
    
}

if($_POST['action'] == 'updateTranslate'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['lang_id'])){
        die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy thông tin ngôn ngữ')]));
    }
    
    $lang_id = check_string($_POST['lang_id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$lang_id' ");
    if (!$row) {
        die(json_encode(['status' => 'error', 'msg' => __('Ngôn ngữ không tồn tại trong hệ thống')]));
    }
    
    if ($row['lang_default'] == 1) {
        die(json_encode(['status' => 'error', 'msg' => __('Không thể thực hiện vì đây là ngôn ngữ mặc định của hệ thống')]));
    }
    
    // Xóa tất cả bản dịch của ngôn ngữ hiện tại
    $isDelete = $CMSNT->remove("translate", " `lang_id` = '$lang_id' ");
    
    if ($isDelete) {
        // Lấy ngôn ngữ mặc định
        $defaultLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1 ");
        if (!$defaultLang) {
            die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy ngôn ngữ mặc định')]));
        }
        
        // Sao chép bản dịch từ ngôn ngữ mặc định
        $defaultTranslations = $CMSNT->get_list("SELECT * FROM `translate` WHERE `lang_id` = '".$defaultLang['id']."' ");
        if (!empty($defaultTranslations)) {
            $insertCount = 0;
            foreach ($defaultTranslations as $tran) {
                $isInsert = $CMSNT->insert("translate", [
                    'lang_id'   => $lang_id,
                    'value'     => $tran['value'],
                    'name'      => $tran['name']
                ]);
                if($isInsert) {
                    $insertCount++;
                }
            }
            
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => sprintf(__('Tạo lại bản dịch cho ngôn ngữ %s từ ngôn ngữ mặc định %s (%d items)'), $row['lang'], $defaultLang['lang'], $insertCount)
            ]);
            
            die(json_encode([
                'status' => 'success',
                'msg' => sprintf(__('Tạo lại bản dịch thành công! %d nội dung đã được sao chép từ ngôn ngữ mặc định.'), $insertCount),
                'count' => $insertCount
            ]));
        } else {
            die(json_encode(['status' => 'error', 'msg' => __('Không tìm thấy bản dịch trong ngôn ngữ mặc định')]));
        }
    } else {
        die(json_encode(['status' => 'error', 'msg' => __('Có lỗi xảy ra khi xóa dữ liệu cũ')]));
    }
}

// Bulk auto translate
if($_POST['action'] == 'bulk_auto_translate'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $translate_data = $_POST['translate_data'];
    $target_lang = check_string($_POST['target_lang']);
    
    if (empty($translate_data) || !is_array($translate_data)) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một mục để dịch')]));
    }
    
    if (empty($target_lang)) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng cập nhật ISO CODE ngôn ngữ trước khi thực hiện dịch tự động!')]));
    }
    
    // Kiểm tra giới hạn max_input_vars
    $maxInputVars = ini_get('max_input_vars');
    $maxAllowedItems = floor($maxInputVars / 6); // Mỗi item có khoảng 6 variables
    
    if (count($translate_data) > $maxAllowedItems) {
        die(json_encode([
            'status' => 'error', 
            'msg' => sprintf(__('Vượt quá giới hạn max_input_vars (%d). Chỉ có thể dịch tối đa %d items cùng lúc. Vui lòng tăng max_input_vars trong php.ini hoặc dịch theo từng lô nhỏ hơn.'), $maxInputVars, $maxAllowedItems)
        ]));
    }
    
    $translated_count = 0;
    $failed_count = 0;
    
    foreach($translate_data as $item) {
        $id = check_string($item['id']);
        $defaultText = check_string($item['name']);
        
        // Gọi API dịch
        $apiUrl = 'https://api.cmsnt.co/translation-api.php';
        $url = $apiUrl . '?license_key='.$CMSNT->site('license_key').'&q='.urlencode($defaultText).'&target='.urlencode($target_lang);
        
        $response = @file_get_contents($url);
        if ($response) {
            $data = json_decode($response, true);
            if (isset($data['data']['translations'][0]['translatedText'])) {
                $translatedText = $data['data']['translations'][0]['translatedText'];
                
                // Cập nhật vào database
                $CMSNT->update("translate", [
                    'value' => $translatedText
                ], " `id` = '$id' ");
                
                $translated_count++;
            } else {
                $failed_count++;
            }
        } else {
            $failed_count++;
        }
        
        // Thêm delay để tránh spam API
        // usleep(500000); // 0.5 giây
    }
    
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => "Bulk Auto Translate $translated_count items."
    ]);
    
    $msg = __('Đã dịch thành công') . ' ' . $translated_count . ' ' . __('bản dịch');
    if ($failed_count > 0) {
        $msg .= ', ' . $failed_count . ' ' . __('bản dịch thất bại');
    }
    
    die(json_encode([
        'status' => 'success',
        'msg' => $msg
    ]));
}

// Cập nhật thông tin ngôn ngữ
if($_POST['action'] == 'updateLanguage'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $id = intval(check_string($_POST['id']));
    if(!$language = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Ngôn ngữ không tồn tại trong hệ thống')]));
    }
    
    $stt = isset($_POST['stt']) ? intval(check_string($_POST['stt'])) : $language['stt'];
    $status = isset($_POST['status']) ? check_string($_POST['status']) : 0;
    
    $isUpdate = $CMSNT->update("languages", [
        'stt' => $stt,
        'status' => $status
    ], " `id` = '$id' ");
    
    if($isUpdate){
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật ngôn ngữ %s (ID %s)'), $language['lang'], $id)
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thất bại')]));
}

// Cập nhật thứ tự ngôn ngữ
if($_POST['action'] == 'updateLanguageOrder'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    if(empty($_POST['order'])){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    
    $order = json_decode($_POST['order'], true);
    if(!is_array($order)){
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu sắp xếp không hợp lệ')]));
    }
    
    $updateCount = 0;
    foreach($order as $item){
        $id = intval($item['id']);
        $position = intval($item['position']);
        if($id > 0){
            $isUpdate = $CMSNT->update("languages", [
                'stt' => $position
            ], " `id` = '$id' ");
            if($isUpdate) {
                $updateCount++;
            }
        }
    }
    
    if($updateCount > 0){
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật thứ tự sắp xếp %d ngôn ngữ'), $updateCount)
        ]);
        die(json_encode(['status' => 'success', 'msg' => __('Cập nhật thứ tự thành công!')]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật thứ tự thất bại')]));
}

// Đặt ngôn ngữ mặc định
if($_POST['action'] == 'setDefaultLanguage'){
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $id = intval(check_string($_POST['id']));
    if(!$language = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Ngôn ngữ không tồn tại trong hệ thống')]));
    }
    
    // Xóa mặc định của tất cả ngôn ngữ khác
    $CMSNT->update("languages", [
        'lang_default' => 'hide'
    ], " `id` != '$id' ");
    
    // Đặt ngôn ngữ hiện tại làm mặc định
    $isUpdate = $CMSNT->update("languages", [
        'lang_default' => 'show',
        'status' => 'show' // Đảm bảo ngôn ngữ mặc định luôn hiển thị
    ], " `id` = '$id' ");
    
    if($isUpdate){
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Đặt ngôn ngữ %s làm mặc định'), $language['lang'])
        ]);
        die(json_encode(['status' => 'success', 'msg' => sprintf(__('Đã đặt %s làm ngôn ngữ mặc định!'), $language['lang'])]));
    }
    die(json_encode(['status' => 'error', 'msg' => __('Đặt ngôn ngữ mặc định thất bại')]));
}


// Chuyển đổi tất cả dịch vụ từ chuyên mục này sang chuyên mục khác
if($_POST['action'] == 'transferCategoryServices'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $source_category_id = intval(check_string($_POST['source_category_id']));
    $target_category_id = intval(check_string($_POST['target_category_id']));
    
    // Kiểm tra chuyên mục nguồn
    if(!$source_category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$source_category_id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục nguồn không tồn tại')]));
    }
    
    // Kiểm tra chuyên mục đích
    if(!$target_category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$target_category_id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục đích không tồn tại')]));
    }
    
    // Kiểm tra không được chuyển sang chính nó
    if($source_category_id == $target_category_id){
        die(json_encode(['status' => 'error', 'msg' => __('Không thể chuyển sang chính chuyên mục đó')]));
    }
    
    // Đếm số dịch vụ trong chuyên mục nguồn
    $service_count = $CMSNT->num_rows("SELECT * FROM `services` WHERE `category_id` = '$source_category_id' ");
    
    if($service_count == 0){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục nguồn không có dịch vụ nào để chuyển')]));
    }
    
    // Thực hiện chuyển đổi
    $isUpdate = $CMSNT->update("services", [
        'category_id' => $target_category_id
    ], " `category_id` = '$source_category_id' ");
    
    if($isUpdate){
        // Ghi log
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Chuyển %d dịch vụ từ chuyên mục "%s" sang chuyên mục "%s"'), $service_count, $source_category['name'], $target_category['name'])
        ]);
        
        die(json_encode([
            'status' => 'success', 
            'msg' => sprintf(__('Đã chuyển thành công %d dịch vụ từ chuyên mục "%s" sang chuyên mục "%s"'), $service_count, $source_category['name'], $target_category['name'])
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Chuyển đổi chuyên mục thất bại')]));
}

// Cập nhật trạng thái dịch vụ
if($_POST['action'] == 'updateServiceStatus'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $id = intval(check_string($_POST['id']));
    $status = check_string($_POST['status']);
    
    if(!$service = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Dịch vụ không tồn tại trong hệ thống')]));
    }
    
    if(!in_array($status, ['show', 'hide'])){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }
    
    $isUpdate = $CMSNT->update("services", [
        'display' => $status
    ], " `id` = '$id' ");
    
    if($isUpdate){
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái dịch vụ (ID %s)'), $id)
        ]);
        
        $statusText = $status == 'show' ? __('hiển thị') : __('ẩn');
        die(json_encode([
            'status' => 'success', 
            'msg' => sprintf(__('Đã cập nhật trạng thái dịch vụ thành %s'), $statusText)
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật trạng thái thất bại')]));
}

// Cập nhật trạng thái chuyên mục
if($_POST['action'] == 'updateCategoryStatus'){
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $id = intval(check_string($_POST['id']));
    $status = check_string($_POST['status']);
    
    if(!$category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$id' ")){
        die(json_encode(['status' => 'error', 'msg' => __('Chuyên mục không tồn tại trong hệ thống')]));
    }
    
    if(!in_array($status, ['show', 'hide'])){
        die(json_encode(['status' => 'error', 'msg' => __('Trạng thái không hợp lệ')]));
    }
    
    $isUpdate = $CMSNT->update("categories", [
        'status' => $status
    ], " `id` = '$id' ");
    
    if($isUpdate){
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => sprintf(__('Cập nhật trạng thái chuyên mục (ID %s)'), $id)
        ]);
        
        $statusText = $status == 'show' ? __('hiển thị') : __('ẩn');
        die(json_encode([
            'status' => 'success', 
            'msg' => sprintf(__('Đã cập nhật trạng thái chuyên mục thành %s'), $statusText)
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Cập nhật trạng thái thất bại')]));
}


die(json_encode([
    'status'    => 'error',
    'msg'       => __('Invalid data')
]));