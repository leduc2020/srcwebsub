<!-- Developer By CMSNT.CO | FB.COM/CMSNT.CO | ZALO.ME/0947838128 | MMO Solution -->
<?php
define("IN_SITE", true);
require_once(__DIR__.'/libs/db.php');
require_once(__DIR__.'/libs/lang.php');
require_once(__DIR__.'/libs/helper.php');
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/libs/database/users.php');
$CMSNT = new DB();

if ($CMSNT->site('status') != 1 && isSecureCookie('admin_login') != true) {
    require_once(__DIR__.'/views/common/maintenance.php');
    exit();
}

// Định nghĩa hằng số cho thư mục views
define('VIEWS_PATH', __DIR__ . '/views');

// Kiểm tra module và action hợp lệ
$module = !empty($_GET['module']) ? check_path($_GET['module']) : 'client';
$module = $module == $CMSNT->site('path_admin') ? 'admin' : 'client'; 
$home   = $module == 'client' ? $CMSNT->site('home_page') : 'order';
$action = !empty($_GET['action']) ? check_path($_GET['action']) : $home;

// Chặn các action nhạy cảm
$blocked_actions = ['footer', 'header', 'sidebar', 'nav', 'widget-tools', 'block-promotion', 'block-info-user'];
if(in_array($action, $blocked_actions)) {
    require_once(VIEWS_PATH.'/common/404.php');
    exit();
}

if($module == 'admin'){
    require_once __DIR__.'/models/is_admin.php';
}

if(isset($_GET['utm_source'])) {
    $utm_source = check_string($_GET['utm_source']);
    setcookie('utm_source', $utm_source, time() + (86400 * 30), "/"); // Cookie sẽ tồn tại trong 30 ngày
}
if(isset($_GET['aff'])) {
    $aff = check_string(intval($_GET['aff']));
    setcookie('aff', $aff, time() + (86400 * 30), "/"); // Cookie sẽ tồn tại trong 30 ngày
    if($user_ref = $CMSNT->get_row("SELECT id FROM `users` WHERE `id` = $aff ")){
        // CỘNG LƯỢT CLICK
        $CMSNT->cong('users', 'ref_click', 1, " `id` = '".$user_ref['id']."' ");
    }
}

// Xây dựng đường dẫn an toàn
$path = VIEWS_PATH . '/' . $module . '/' . $action . '.php';

// Kiểm tra file tồn tại và nằm trong thư mục views
if (file_exists($path) && strpos(realpath($path), realpath(VIEWS_PATH)) === 0) {
    require_once($path);
    exit();
} else {
    require_once(VIEWS_PATH.'/common/404.php');
    exit();
}
?>
