<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Thêm API cần kết nối'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '

';
$body['footer'] = '
<!-- bs-custom-file-input -->
<script src="'.BASE_URL('public/AdminLTE3/').'plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- Page specific script -->
<script>
$(function () {
  bsCustomFileInput.init();
});
</script> 
';
require_once(__DIR__.'/../../libs/suppliers/smmpanel2.php');
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
require_once(__DIR__.'/../../models/is_license.php');
 
if(checkPermission($getUser['admin'], 'manager_suppliers') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>
<?php
if (isset($_POST['submit'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('Không được dùng chức năng này vì đây là trang web demo.').'")){window.history.back().location.reload();}</script>');
    }
    if (empty($_POST['type'])) {
        die('<script type="text/javascript">if(!alert("'.__('Vui lòng chọn loại API cần kết nối').'")){window.history.back().location.reload();}</script>');
    }
    $type = check_string($_POST['type']);
    if (empty($_POST['domain'])) {
        die('<script type="text/javascript">if(!alert("'.__('Vui lòng nhập domain cần kết nối').'")){window.history.back().location.reload();}</script>');
    }
    $domain = check_string($_POST['domain']);
    $price = '';
    $token = !empty($_POST['token']) ? check_string($_POST['token']) : NULL;

    if(in_array($domain, $domain_blacklist)){
        die('<script type="text/javascript">if(!alert("'.$domain.' '.__('nằm trong danh sách đen, không thể kết nối').'")){window.history.back().location.reload();}</script>');
    }
    $checkKey = checkLicenseKey($CMSNT->site('license_key'));
    if($checkKey['status'] != true){
        die('<script type="text/javascript">if(!alert("'.$checkKey['msg'].'")){window.history.back().location.reload();}</script>');
    }
    if($type == 'SMMPANEL2'){
        if (empty($_POST['api_key'])) {
            die('<script type="text/javascript">if(!alert("'.__('Vui lòng nhập api_key').'")){window.history.back().location.reload();}</script>');
        }
        $response = get_balance_smmpanel2(check_string($_POST['domain']), check_string($_POST['api_key']), check_string($_POST['proxy']));
        $result = json_decode($response, true);
        if(isset($result['error'])){
            die('<script type="text/javascript">if(!alert("'.$result['error'].'")){window.history.back().location.reload();}</script>');
        }
        $price = check_string($result['balance']).' '.check_string($result['currency']);
    }
    $isInsert = $CMSNT->insert('suppliers', [
        'user_id'           => $getUser['id'],
        'type'              => $type,
        'domain'            => $domain,
        'username'          => !empty($_POST['username']) ? check_string($_POST['username']) : NULL,
        'password'          => !empty($_POST['password']) ? check_string($_POST['password']) : NULL,
        'api_key'           => !empty($_POST['api_key']) ? check_string($_POST['api_key']) : NULL,
        'token'             => $token,
        'coupon'            => !empty($_POST['coupon']) ? check_string($_POST['coupon']) : NULL,
        'price'             => check_string($price),
        'discount'          => !empty($_POST['discount']) ? check_string($_POST['discount']) : 0,
        'discount_price_1'  => !empty($_POST['discount_price_1']) ? check_string($_POST['discount_price_1']) : 0,
        'discount_price_2'  => !empty($_POST['discount_price_2']) ? check_string($_POST['discount_price_2']) : 0,
        'discount_price_3'  => !empty($_POST['discount_price_3']) ? check_string($_POST['discount_price_3']) : 0,
        'rate'              => !empty($_POST['rate']) ? check_string($_POST['rate']) : 1,
        'format_price'      => !empty($_POST['format_price']) ? check_string($_POST['format_price']) : 1,
        'update_name'       => check_string($_POST['update_name']),
        'sync_category'     => !empty($_POST['sync_category']) ? check_string($_POST['sync_category']) : 'OFF',
        'sync_service'      => !empty($_POST['sync_service']) ? check_string($_POST['sync_service']) : 'ON',
        'update_price'      => check_string($_POST['update_price']),
        'status'            => 1,
        'check_string_api'  => check_string($_POST['check_string_api']),
        'create_gettime'    => gettime(),
        'update_gettime'    => gettime()
    ]);
    if ($isInsert) {
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __("Thêm API cần kết nối")." (".check_string($_POST['domain']).")."
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __("Thêm API cần kết nối")." (".check_string($_POST['domain']).").", $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);    
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        die('<script type="text/javascript">if(!alert("'.__("Thêm thành công!").'")){location.href = "'.base_url_admin('suppliers').'";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("'.__("Thêm thất bại!").'")){window.history.back().location.reload();}</script>');
    }
}

$domain = '';
if(!empty($_GET['domain'])){
    $domain = check_string($_GET['domain']);
}
$type = '';
if(!empty($_GET['type'])){
    $type = check_string($_GET['type']);
}
?>




<div class="main-content app-content">

    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button"
                    class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1"
                    href="<?=base_url_admin('suppliers');?>"><i class="fa-solid fa-arrow-left"></i></a> <?=__('Thêm API cần kết nối');?>
            </h1>
        </div>


        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <div class="col-xl-7">
                    <div class="card custom-card">
                        <div class="card-body">
                            <div class="row mb-5 gy-3">
                                <div class="col-12 mb-2">
                                    <div class="api-section p-3 rounded bg-light mb-3">
                                        <h5 class="border-bottom pb-2 mb-3"><i
                                                class="fa-solid fa-plug-circle-plus text-primary"></i> <?=__('Thông tin kết nối API');?></h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="api-select">
                                                    <i class="fa-solid fa-server text-info"></i> <?=__('Loại API:');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select form-select-lg shadow-sm" id="api-select"
                                                    name="type" required>
                                                    <option value="">-- <?=__('Chọn loại API');?> --</option>
                                                    <option <?=$type == 'SMMPANEL2' ? 'selected' : '';?>
                                                        value="SMMPANEL2" class="bg-success-subtle">
                                                        SMMPANEL V2 (<?=__('Miễn phí');?>)</option>
                                                </select>
                                                <div class="form-text"><i class="fas fa-info-circle"></i> <?=__('API SMMPANEL2 được hỗ trợ miễn phí, API khác tính phí 200.000đ/lần');?></div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="domain">
                                                    <i class="fa-solid fa-globe text-primary"></i> <?=__('Domain');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group input-group-lg">
                                                    <span class="input-group-text bg-light"><i
                                                            class="fas fa-link"></i></span>
                                                    <input type="text" class="form-control shadow-sm" id="domain"
                                                        value="<?=$domain;?>" placeholder="VD: https://domain.com/"
                                                        name="domain" autocomplete="off" data-lpignore="true" required>
                                                </div>
                                                <div class="form-text"><i class="fas fa-info-circle"></i> <?=__('Nhập đầy đủ URL kèm https:// hoặc http://');?></div>
                                            </div>
                                        </div>

                                        <!-- Thông tin đăng nhập -->
                                        <div class="credentials-container mt-3">
                                            <div class="row">
                                                <div class="col-md-6 mb-3" id="username" style="display: none;">
                                                    <label class="form-label fw-bold" for="username-input">
                                                        <i class="fa-solid fa-user text-warning"></i>
                                                        <?=__('Username:');?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i
                                                                class="fas fa-user"></i></span>
                                                        <input type="text" class="form-control shadow-sm"
                                                            id="username-input" name="username"
                                                            placeholder="<?=__('Nhập tên đăng nhập website API');?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3" id="password" style="display: none;">
                                                    <label class="form-label fw-bold" for="password-input">
                                                        <i class="fa-solid fa-key text-warning"></i>
                                                        <?=__('Password:');?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i
                                                                class="fas fa-lock"></i></span>
                                                        <input type="password" class="form-control shadow-sm"
                                                            id="password-input" name="password"
                                                            placeholder="<?=__('Nhập mật khẩu đăng nhập website API');?>">
                                                        <button class="btn btn-outline-secondary" type="button"
                                                            id="toggle-password">
                                                            <i class="fas fa-eye"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3" id="api_key" style="display: none;">
                                                    <label class="form-label fw-bold" for="api-key-input">
                                                        <i class="fa-solid fa-key text-danger"></i> <?=__('API Key:');?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i
                                                                class="fas fa-key"></i></span>
                                                        <input type="text" class="form-control shadow-sm"
                                                            id="api-key-input" name="api_key"
                                                            placeholder="<?=__('Nhập Api Key trong website API');?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3" id="token" style="display: none;">
                                                    <label class="form-label fw-bold" for="token-input">
                                                        <i class="fa-solid fa-shield-halved text-success"></i>
                                                        <?=__('Token:');?>
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i
                                                                class="fas fa-shield-alt"></i></span>
                                                        <input type="text" class="form-control shadow-sm"
                                                            id="token-input" name="token"
                                                            placeholder="<?=__('Nhập Token trong website API');?>">
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-3" id="coupon" style="display: none;">
                                                    <label class="form-label fw-bold" for="coupon-input">
                                                        <i class="fa-solid fa-tag text-info"></i> <?=__('Coupon:');?>
                                                    </label>
                                                    <div class="input-group">
                                                        <span class="input-group-text bg-light"><i
                                                                class="fas fa-percentage"></i></span>
                                                        <input type="text" class="form-control shadow-sm"
                                                            id="coupon-input" name="coupon"
                                                            placeholder="<?=__('Nhập mã giảm giá nếu có');?>">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Cài đặt đồng bộ -->
                                <div class="col-12 mb-2">
                                    <div class="api-section p-3 rounded bg-light mb-3">
                                        <h5 class="border-bottom pb-2 mb-3"><i
                                                class="fa-solid fa-sliders text-success"></i> <?=__('Cài đặt đồng bộ dữ liệu');?>
                                        </h5>
                                        <div class="row">
                                            <div class="col-md-6 mb-3" id="sync_category" style="display: none;">
                                                <label class="form-label fw-bold" for="sync-category-select">
                                                    <i class="fa-solid fa-folder-tree text-primary"></i> <?=__('Đồng bộ chuyên mục từ API');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="sync-category-select"
                                                    name="sync_category" required>
                                                    <option value="ON"><?=__('ON - Đồng bộ tự động');?></option>
                                                    <option value="OFF"><?=__('OFF - Không đồng bộ');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Hệ thống sẽ tự động đồng bộ tất cả chuyên mục từ API vào website của bạn, phù hợp cho website chỉ kết nối duy nhất 1 API');?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3" id="sync_service">
                                                <label class="form-label fw-bold" for="sync-service-select">
                                                    <i class="fa-solid fa-folder-tree text-primary"></i> <?=__('Đồng bộ dịch vụ từ API');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="sync-service-select"
                                                    name="sync_service" required>
                                                    <option value="ON"><?=__('ON - Đồng bộ tự động');?></option>
                                                    <option value="OFF"><?=__('OFF - Không đồng bộ');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Hệ thống sẽ tự động đồng bộ tất cả dịch vụ từ API vào website của bạn, nếu OFF bạn cần phải tạo dịch vụ thủ công.');?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="update-price-select">
                                                    <i class="fa-solid fa-sack-dollar text-success"></i> <?=__('Cập nhật giá bán tự động');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="update-price-select" name="update_price"
                                                    required>
                                                    <option value="ON"><?=__('ON - Cập nhật tự động');?></option>
                                                    <option value="OFF"><?=__('OFF - Giữ nguyên giá');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Khi giá sản phẩm thay đổi ở API, hệ thống sẽ tự động cập nhật');?>
                                                </div>
                                            </div>
                                            <div id="discount-fields" class="col-12">
                                                <div class="row">
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold" for="discount-input">
                                                            <i class="fa-solid fa-percent text-danger"></i> <?=__('Tăng giá bán lẻ');?>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control shadow-sm"
                                                                id="discount-input" value="0" min="0"
                                                                placeholder="Nhập % tăng giá" name="discount" required>
                                                            <span class="input-group-text bg-light">%</span>
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle"></i> <?=__('Nhập 10 để tăng giá bán lẻ thêm 10% so với giá vốn, nhập 0 để giữ nguyên');?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold" for="discount-price-1-input">
                                                            <i class="fa-solid fa-percent text-danger"></i> <?=__('Tăng giá bán').' '.getRankNameByTarget('price_1');?>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control shadow-sm"
                                                                id="discount-price-1-input" value="0" min="0"
                                                                placeholder="Nhập % tăng giá" name="discount_price_1" required>
                                                            <span class="input-group-text bg-light">%</span>
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle"></i> <?=sprintf(__('Nhập 10 để tăng giá bán %s thêm 10%% so với giá vốn, nhập 0 để giữ nguyên'), getRankNameByTarget('price_1'));?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold" for="discount-price-2-input">
                                                            <i class="fa-solid fa-percent text-danger"></i> <?=__('Tăng giá bán').' '.getRankNameByTarget('price_2');?>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control shadow-sm"
                                                                id="discount-price-2-input" value="0" min="0"
                                                                placeholder="Nhập % tăng giá" name="discount_price_2" required>
                                                            <span class="input-group-text bg-light">%</span>
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle"></i> <?=sprintf(__('Nhập 10 để tăng giá bán %s thêm 10%% so với giá vốn, nhập 0 để giữ nguyên'), getRankNameByTarget('price_2'));?>
                                                        </div>
                                                    </div>
                                                    <div class="col-md-3 mb-3">
                                                        <label class="form-label fw-bold" for="discount-price-3-input">
                                                            <i class="fa-solid fa-percent text-danger"></i> <?=__('Tăng giá bán').' '.getRankNameByTarget('price_3');?>
                                                            <span class="text-danger">*</span>
                                                        </label>
                                                        <div class="input-group">
                                                            <input type="text" class="form-control shadow-sm"
                                                                id="discount-price-3-input" value="0" min="0"
                                                                placeholder="Nhập % tăng giá" name="discount_price_3" required>
                                                            <span class="input-group-text bg-light">%</span>
                                                        </div>
                                                        <div class="form-text">
                                                            <i class="fas fa-info-circle"></i> <?=sprintf(__('Nhập 10 để tăng giá bán %s thêm 10%% so với giá vốn, nhập 0 để giữ nguyên'), getRankNameByTarget('price_3'));?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="discount-input">
                                                    <i class="fa-solid fa-percent text-danger"></i> <?=__('Tỷ giá tiền tệ quốc tế (nếu có)');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control shadow-sm"
                                                        id="discount-input" value="1" min="0"
                                                        placeholder="Nhập tỷ giá" name="rate" required>
                                                        <span class="input-group-text bg-light"><?=currencyDefault();?></span>
                                                </div>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Nếu giá dịch vụ của API giống giá tiền tệ của bạn, hãy nhập 1. Nếu website bạn sử dụng VND nhưng giá dịch vụ API là USD, hãy nhập tỷ giá của 1 USD.');?>
                                                </div>
                                            </div>
                                            
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="format-price-select">
                                                    <i class="fa-solid fa-sack-dollar text-success"></i> <?=__('Định dạng giá bán của API');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="format-price-select" name="format_price"
                                                    required>
                                                    <option value="1000"><?=__('Giá của 1000 lượt');?></option>
                                                    <option value="1"><?=__('Giá của 1 lượt');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Vui lòng kiểm tra API xem giá của API là giá của 1 lượt hay 1000 lượt.');?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="update-name-select">
                                                    <i class="fa-solid fa-font text-info"></i> <?=__('Cập nhật tên & mô tả tự động');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="update-name-select" name="update_name"
                                                    required>
                                                    <option value="ON"><?=__('ON - Cập nhật tự động');?></option>
                                                    <option value="OFF"><?=__('OFF - Giữ nguyên nội dung');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Tự động cập nhật tên và mô tả sản phẩm từ API');?>
                                                </div>
                                            </div>
                                            <div class="col-md-6 mb-3">
                                                <label class="form-label fw-bold" for="check-string-api-select">
                                                    <i class="fa-solid fa-code text-warning"></i> <?=__('Lọc HTML trong nội dung API');?>
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <select class="form-select" id="check-string-api-select"
                                                    name="check_string_api" required>
                                                    <option value="ON"><?=__('ON - Kích hoạt bảo vệ');?></option>
                                                    <option value="OFF"><?=__('OFF - Tắt bảo vệ');?></option>
                                                </select>
                                                <div class="form-text">
                                                    <i class="fas fa-shield-alt text-danger"></i> <?=__('Bảo vệ website bằng cách lọc mã HTML độc hại từ API');?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 mb-2">
                                    <div class="api-section p-3 rounded bg-light mb-3">
                                        <h5 class="border-bottom pb-2 mb-3"><i
                                                class="fa-solid fa-sliders text-success"></i> <?=__('Cài đặt khác');?></h5>
                                        <div class="row">

                                            <div class="col-md-6 mb-3" id="proxy" style="display: none;">
                                                <label class="form-label fw-bold" for="proxy-input">
                                                    <i class="fa-solid fa-globe text-danger"></i> <?=__('Proxy v4 hoặc v6 (nếu có)');?>
                                                </label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control shadow-sm" id="proxy-input"
                                                        placeholder="ip:port:username:password" name="proxy"
                                                        autocomplete="off">
                                                </div>
                                                <div class="form-text">
                                                    <i class="fas fa-info-circle"></i> <?=__('Chỉ dùng Proxy nếu quý khách đã nhờ phía API whitelist IP nhưng vẫn không hiện số dư sau khi kết nối.');?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="d-grid gap-2 mb-3">
                                <button type="submit" name="submit" class="btn btn-primary btn-lg shadow-lg btn-wave">
                                    <i class="fa-solid fa-plug-circle-bolt me-1"></i> <?=__('Kết nối API ngay');?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="card custom-card position-sticky" style="top: 85px;">
                        <div class="card-header bg-primary">
                            <div class="card-title">
                                <i class="fa-solid fa-circle-info me-1"></i> <?=__('LƯU Ý');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-primary" role="alert">
                                <i class="fa-solid fa-lightbulb me-1"></i> <strong><?=__('Mục đích:');?></strong> <?=__('Chức năng này cho phép quý khách bán lại sản phẩm của website khác trên chính website của quý khách.');?>
                            </div>

                            <div class="alert alert-warning mb-3" role="alert">
                                <h6 class="alert-heading"><i class="fa-solid fa-triangle-exclamation me-1"></i> <?=__('Lưu ý quan trọng!');?></h6>
                                <p><?=__('Trường hợp quý khách cấu hình đúng nhưng không hiện số dư API có thể do máy chủ không thể kết nối với API đích.');?></p>
                                <a href="https://help.cmsnt.co/huong-dan/ket-noi-api-nhap-dung-thong-tin-nhung-khong-ra-so-du-thi-lam-sao/"
                                    class="btn btn-sm btn-warning mt-2" target="_blank">
                                    <i class="fas fa-external-link-alt me-1"></i> <?=__('Xem hướng dẫn xử lý');?>
                                </a>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded bg-light mb-3">
                                <div class="me-3 text-primary fs-3"><i class="fa-solid fa-handshake"></i></div>
                                <div>
                                    <h6 class="mb-1"><?=__('API SMMPANEL2');?></h6>
                                    <p class="mb-0 text-success fw-bold"><?=__('Miễn phí');?></p>
                                </div>
                            </div>

                            <div class="d-flex align-items-center p-3 rounded bg-light mb-3">
                                <div class="me-3 text-warning fs-3"><i class="fa-solid fa-circle-dollar-to-slot"></i>
                                </div>
                                <div>
                                    <h6 class="mb-1"><?=__('API ngoài hệ sinh thái');?></h6>
                                    <p class="mb-0"><?=__('Phí tích hợp:');?> <span class="text-danger fw-bold"><?=__('200.000đ / 1 lần');?></span></p>
                                </div>
                            </div>

                            <div class="d-grid gap-2">
                                <a href="https://www.cmsnt.co/p/contact.html" class="btn btn-outline-primary"
                                    target="_blank">
                                    <i class="fa-solid fa-headset me-1"></i> <?=__('Liên hệ hỗ trợ kết nối API');?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>


         
         
         


    </div>
</div>



<?php
require_once(__DIR__.'/footer.php');
?>

<script>
document.addEventListener("DOMContentLoaded", function() {
    // Ngăn chặn autofill bằng cách thêm một trường ẩn và đảm bảo không tự động điền
    const form = document.querySelector('form');
    const hiddenInput = document.createElement('input');
    hiddenInput.type = 'text';
    hiddenInput.style.display = 'none';
    hiddenInput.name = 'prevent_autofill';
    hiddenInput.setAttribute('autocomplete', 'off');
    form.prepend(hiddenInput);

    // Thêm thuộc tính autocomplete="new-password" vào tất cả các trường input
    const allInputs = document.querySelectorAll('input[type="text"], input[type="password"]');
    allInputs.forEach(input => {
        input.setAttribute('autocomplete', 'new-password');
    });

    // Đoạn code xử lý toggle fields
    const typeSelect = document.querySelector("select[name='type']");
    const usernameField = document.getElementById("username");
    const passwordField = document.getElementById("password");
    const apiKeyField = document.getElementById("api_key");
    const tokenField = document.getElementById("token");
    const couponField = document.getElementById("coupon");
    const sync_category = document.getElementById("sync_category");
    const proxyField = document.getElementById("proxy");
    const updatePriceSelect = document.getElementById("update-price-select");
    const discountFields = document.getElementById("discount-fields");

    // Thêm xử lý hiển thị/ẩn mật khẩu
    document.getElementById('toggle-password').addEventListener('click', function() {
        const passwordInput = document.getElementById('password-input');
        const icon = this.querySelector('i');

        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    function toggleFields() {
        const selectedType = typeSelect.value;
        usernameField.style.display = "none";
        passwordField.style.display = "none";
        apiKeyField.style.display = "none";
        tokenField.style.display = "none";
        couponField.style.display = "none";
        sync_category.style.display = "none";
        proxyField.style.display = "none";

        if (selectedType === "SHOPCLONE6") {
            sync_category.style.display = "block";
            usernameField.style.display = "block";
            passwordField.style.display = "block";
            proxyField.style.display = "block";
        } else if (selectedType === "SMMPANEL2") {
            sync_category.style.display = "block";
            apiKeyField.style.display = "block";
            couponField.style.display = "block";
            proxyField.style.display = "block";
        } else if (selectedType === "API_4" || selectedType === "API_17") {
            usernameField.style.display = "block";
            passwordField.style.display = "block";
        } else if (selectedType === "API_1" || selectedType === "API_6" || selectedType === "API_18" ||
            selectedType === "API_19" || selectedType === "API_9" || selectedType === "API_23" ||
            selectedType === "API_24" || selectedType === "API_25") {
            apiKeyField.style.display = "block";
        } else if (selectedType === "API_14" || selectedType === "API_21" || selectedType === "API_22") {
            tokenField.style.display = "block";
        } else if (selectedType === "API_20" || selectedType === "API_26") {
            apiKeyField.style.display = "block";
            tokenField.style.display = "block";
        }
    }

    // Hàm điều khiển hiển thị/ẩn các trường tăng giá bán
    function toggleDiscountFields() {
        const isUpdatePriceOn = updatePriceSelect.value === "ON";
        if (isUpdatePriceOn) {
            discountFields.style.display = "block";
        } else {
            discountFields.style.display = "none";
        }
    }

    toggleFields();
    toggleDiscountFields(); // Gọi khi trang được tải
    typeSelect.addEventListener("change", toggleFields);
    updatePriceSelect.addEventListener("change", toggleDiscountFields);

    // Cải thiện UX với hiệu ứng làm nổi bật section
    const apiSelect = document.getElementById('api-select');
    apiSelect.addEventListener('change', function() {
        if (this.value) {
            document.querySelector('.credentials-container').classList.add('animate__animated',
                'animate__fadeIn');
            setTimeout(() => {
                document.querySelector('.credentials-container').classList.remove(
                    'animate__animated', 'animate__fadeIn');
            }, 1000);
        }
    });
});
</script>