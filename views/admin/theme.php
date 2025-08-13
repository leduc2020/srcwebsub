<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => 'Theme',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<style>
/* Landing Page Theme Styling - Chiều cao ảnh đồng đều */
.theme-choices .theme-img {
    height: 220px !important;
    width: 100%;
    object-fit: cover;
    object-position: top;
}

.theme-choices .theme-item {
    margin-bottom: 20px;
}

.theme-choices .theme-label {
    display: block;
    cursor: pointer;
    text-align: center;
    padding: 10px;
    border-radius: 8px;
    border: 2px solid #e9ecef;
    background: #fff;
    transition: all 0.3s ease;
    height: 100%;
}

.theme-choices .theme-label:hover {
    border-color: var(--bs-primary);
    box-shadow: 0 4px 15px rgba(0,123,255,0.15);
}

.theme-radio:checked + .theme-label {
    border-color: var(--bs-primary);
    background: rgba(0,123,255,0.05);
    box-shadow: 0 4px 20px rgba(0,123,255,0.2);
}

.theme-choices .theme-name {
    display: block;
    margin-top: 10px;
    font-weight: 600;
    color: #495057;
    font-size: 0.9rem;
}

.theme-radio:checked + .theme-label .theme-name {
    color: var(--bs-primary);
}

.theme-radio {
    position: absolute;
    opacity: 0;
    cursor: pointer;
}

/* Responsive */
@media (max-width: 768px) {
    .theme-choices .theme-img {
        height: 160px !important;
    }
}
</style>


';
$body['footer'] = '
<script>
    $(document).ready(function() {
        // Lấy tab đã lưu trong localStorage (nếu có)
        var activeTab = localStorage.getItem("activeThemeTab");
        
        if (activeTab) {
            // Kích hoạt tab đã lưu
            $(".nav-tabs a[href=\'" + activeTab + "\']").tab("show");
        }
        
        // Lưu tab hiện tại vào localStorage khi chuyển tab
        $("a[data-bs-toggle=\'tab\']").on("shown.bs.tab", function(e) {
            var id = $(e.target).attr("href");
            localStorage.setItem("activeThemeTab", id);
        });
        
        // Hiệu ứng khi chọn theme
        $("input[name=\'data-theme\']").on("change", function() {
            // Thêm hiệu ứng active cho theme được chọn
            $("input[name=\'data-theme\']").each(function() {
                $(this).next(".theme-label").removeClass("active");
            });
            $(this).next(".theme-label").addClass("active");
        });
        
        // Thiết lập active cho các item đã được chọn khi tải trang
        $("input[name=\'data-theme\']:checked").next(".theme-label").addClass("active");
    });
</script>
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
 
if(checkPermission($getUser['admin'], 'edit_theme') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}

if (isset($_POST['SaveImages'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }
    if (check_img('logo_light') == true) {
        unlink($CMSNT->site('logo_light'));
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 3);
        $uploads_dir = 'assets/storage/images/logo_light_'.$rand.'.png';
        $tmp_name = $_FILES['logo_light']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $CMSNT->update('settings', [
                'value'  => $uploads_dir
            ], " `name` = 'logo_light' ");
        }
    }
    if (check_img('logo_dark') == true) {
        unlink($CMSNT->site('logo_dark'));
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 3);
        $uploads_dir = 'assets/storage/images/logo_dark_'.$rand.'.png';
        $tmp_name = $_FILES['logo_dark']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $CMSNT->update('settings', [
                'value'  => $uploads_dir
            ], " `name` = 'logo_dark' ");
        }
    }
    if (check_img('favicon') == true) {
        unlink($CMSNT->site('favicon'));
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 3);
        $uploads_dir = 'assets/storage/images/favicon_'.$rand.'.png';
        $tmp_name = $_FILES['favicon']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $CMSNT->update('settings', [
                'value'  => $uploads_dir
            ], " `name` = 'favicon' ");
        }
    }
    if (check_img('image') == true) {
        unlink($CMSNT->site('image'));
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 3);
        $uploads_dir = 'assets/storage/images/image_'.$rand.'.png';
        $tmp_name = $_FILES['image']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $CMSNT->update('settings', [
                'value'  => $uploads_dir
            ], " `name` = 'image' ");
        }
    }
    if (check_img('avatar') == true) {
        unlink($CMSNT->site('avatar'));
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 3);
        $uploads_dir = 'assets/storage/images/avatar'.$rand.'.png';
        $tmp_name = $_FILES['avatar']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $CMSNT->update('settings', [
                'value'  => $uploads_dir
            ], " `name` = 'avatar' ");
        }
    }

    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Thay đổi ảnh giao diện website')
    ]);
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __("Thay đổi ảnh giao diện website"), $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);    
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);
    die('<script type="text/javascript">if(!alert("Save successfully!")){window.history.back().location.reload();}</script>');
}

if (isset($_POST['SaveSettings'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Thay đổi giao diện website')
    ]);
    foreach ($_POST as $key => $value) {
        $CMSNT->update("settings", array(
            'value' => check_string($value)
        ), " `name` = '$key' ");
    }
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __('Thay đổi giao diện website'), $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);    
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);

    admin_msg_success("Lưu thành công!", "", 1000);
} 

?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-4 mb-0"><i class="fa-solid fa-image me-2"></i><?=__('Theme');?></h1>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card shadow-sm">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('Thay đổi giao diện website');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Nav tabs -->
                        <ul class="nav nav-tabs nav-tabs-custom nav-justified mb-3" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" data-bs-toggle="tab" href="#images_tab" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-image"></i></span>
                                    <span class="d-none d-sm-block"><?=__('Tùy chỉnh hình ảnh');?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#other_settings_tab" role="tab">
                                    <span class="d-block d-sm-none"><i class="fas fa-cogs"></i></span>
                                    <span class="d-none d-sm-block"><?=__('Tùy chỉnh khác');?></span>
                                </a>
                            </li>
                        </ul>

                        <!-- Tab panes -->
                        <div class="tab-content p-3">
                            <!-- Tab Tùy chỉnh hình ảnh -->
                            <div class="tab-pane active" id="images_tab" role="tabpanel">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="row">
                                        <!-- Logo Light -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="logo_light" class="form-label">Logo Light</label>
                                                <input type="file" class="form-control" name="logo_light"
                                                    id="logo_light">
                                            </div>
                                            <div class="mt-2">
                                                <img width="250px" class="bg-light rounded p-3"
                                                    src="<?=BASE_URL($CMSNT->site('logo_light'));?>" alt="Logo Light">
                                            </div>
                                        </div>

                                        <!-- Logo Dark -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="logo_dark" class="form-label">Logo Dark</label>
                                                <input type="file" class="form-control" name="logo_dark" id="logo_dark">
                                            </div>
                                            <div class="mt-2">
                                                <img width="250px" class="bg-light rounded p-3"
                                                    src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="Logo Dark">
                                            </div>
                                        </div>

                                        <!-- Favicon -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="favicon" class="form-label">Favicon</label>
                                                <input type="file" class="form-control" name="favicon" id="favicon">
                                            </div>
                                            <div class="mt-2">
                                                <img width="50px" class="rounded-circle"
                                                    src="<?=BASE_URL($CMSNT->site('favicon'));?>" alt="Favicon">
                                            </div>
                                        </div>

                                        <!-- Image -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="image" class="form-label">Image</label>
                                                <input type="file" class="form-control" name="image" id="image">
                                            </div>
                                            <div class="mt-2">
                                                <img width="250px" class="rounded"
                                                    src="<?=BASE_URL($CMSNT->site('image'));?>" alt="Image">
                                            </div>
                                        </div>


                                        <!-- Avatar -->
                                        <div class="col-lg-6 mb-4">
                                            <div class="form-group">
                                                <label for="avatar" class="form-label">Avatar</label>
                                                <input type="file" class="form-control" name="avatar" id="avatar">
                                            </div>
                                            <div class="mt-2">
                                                <img width="250px" class="rounded-circle"
                                                    src="<?=BASE_URL($CMSNT->site('avatar'));?>" alt="Avatar">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button name="SaveImages" class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i> <?=__('Lưu Hình Ảnh');?>
                                        </button>
                                    </div>
                                </form>
                            </div>

                            <!-- Tab Tùy chỉnh khác -->
                            <div class="tab-pane" id="other_settings_tab" role="tabpanel">
                                <form action="" method="POST">
                                    <div class="row">
                                        <div class="col-lg-12 mb-4">
                                            <div class="row">
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Chọn kiểu layout');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-layout">
                                                            <option value="vertical"
                                                                <?=($CMSNT->site('data-layout') == 'vertical') ? 'selected' : '';?>>
                                                                <?=__('Vertical');?></option>
                                                            <option value="horizontal"
                                                                <?=($CMSNT->site('data-layout') == 'horizontal') ? 'selected' : '';?>>
                                                                <?=__('Horizontal');?></option>
                                                            <option value="twocolumn"
                                                                <?=($CMSNT->site('data-layout') == 'twocolumn') ? 'selected' : '';?>>
                                                                <?=__('Two Column');?></option>
                                                            <option value="semibox"
                                                                <?=($CMSNT->site('data-layout') == 'semibox') ? 'selected' : '';?>>
                                                                <?=__('Semi Box');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Chế độ tối mặc định');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-bs-theme">
                                                            <option value="light"
                                                                <?=($CMSNT->site('data-bs-theme') == 'light') ? 'selected' : '';?>>
                                                                <?=__('Light');?></option>
                                                            <option value="dark"
                                                                <?=($CMSNT->site('data-bs-theme') == 'dark') ? 'selected' : '';?>>
                                                                <?=__('Dark');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Cố định menu khi cuộn trang');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-layout-position">
                                                            <option value="fixed"
                                                                <?=($CMSNT->site('data-layout-position') == 'fixed') ? 'selected' : '';?>>
                                                                <?=__('Cố định');?></option>
                                                            <option value="scrollable"
                                                                <?=($CMSNT->site('data-layout-position') == 'scrollable') ? 'selected' : '';?>>
                                                                <?=__('Cuộn');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label class="form-label fw-medium"><?=__('Topbar Color');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-topbar">
                                                            <option value="dark"
                                                                <?=($CMSNT->site('data-topbar') == 'dark') ? 'selected' : '';?>>
                                                                <?=__('Dark');?></option>
                                                            <option value="light"
                                                                <?=($CMSNT->site('data-topbar') == 'light') ? 'selected' : '';?>>
                                                                <?=__('Light');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label class="form-label fw-medium"><?=__('Sidebar Size');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-sidebar-size">
                                                            <option value="lg"
                                                                <?=($CMSNT->site('data-sidebar-size') == 'lg') ? 'selected' : '';?>>
                                                                <?=__('Default');?></option>
                                                            <option value="md"
                                                                <?=($CMSNT->site('data-sidebar-size') == 'md') ? 'selected' : '';?>>
                                                                <?=__('Compact');?></option>
                                                            <option value="sm"
                                                                <?=($CMSNT->site('data-sidebar-size') == 'sm') ? 'selected' : '';?>>
                                                                <?=__('Small (Icon View)');?></option>
                                                            <option value="sm-hover"
                                                                <?=($CMSNT->site('data-sidebar-size') == 'sm-hover') ? 'selected' : '';?>>
                                                                <?=__('Small Hover View');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label class="form-label fw-medium"><?=__('Sidebar View');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-layout-style">
                                                            <option value="default"
                                                                <?=($CMSNT->site('data-layout-style') == 'default') ? 'selected' : '';?>>
                                                                <?=__('Default');?></option>
                                                            <option value="detached"
                                                                <?=($CMSNT->site('data-layout-style') == 'detached') ? 'selected' : '';?>>
                                                                <?=__('Detached');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Hiệu ứng Loader');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-preloader">
                                                            <option value="disable"
                                                                <?=($CMSNT->site('data-preloader') == 'disable') ? 'selected' : '';?>>
                                                                <?=__('Không');?></option>
                                                            <option value="enable"
                                                                <?=($CMSNT->site('data-preloader') == 'enable') ? 'selected' : '';?>>
                                                                <?=__('Có');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Hiển thị menu trên mobile');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select mb-1" name="data-menu-mobile">
                                                            <option value="0"
                                                                <?=($CMSNT->site('data-menu-mobile') == '0') ? 'selected' : '';?>>
                                                                <?=__('Không');?></option>
                                                            <option value="1"
                                                                <?=($CMSNT->site('data-menu-mobile') == '1') ? 'selected' : '';?>>
                                                                <?=__('Có');?></option>
                                                        </select>
                                                        <img src="<?=BASE_URL('assets/img/demo-menu-mobile.webp');?>"
                                                            alt="Menu Mobile"
                                                            class="img-fluid border rounded shadow-sm">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Hiển thị block thông tin User tại trang đặt hàng');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select mb-1" name="data-order-block-user-info">
                                                            <option value="0"
                                                                <?=($CMSNT->site('data-order-block-user-info') == '0') ? 'selected' : '';?>>
                                                                <?=__('Không');?></option>
                                                            <option value="1"
                                                                <?=($CMSNT->site('data-order-block-user-info') == '1') ? 'selected' : '';?>>
                                                                <?=__('Có');?></option>
                                                        </select>
                                                        <img src="<?=BASE_URL('assets/img/demo-order-block-user-info.webp');?>"
                                                            alt="Order Block User Info"
                                                            class="img-fluid border rounded shadow-sm">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Hiển thị menu dịch vụ trong Sidebar');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-sidebar-service-show">
                                                            <option value="0"
                                                                <?=($CMSNT->site('data-sidebar-service-show') == '0') ? 'selected' : '';?>>
                                                                <?=__('Không');?></option>
                                                            <option value="1"
                                                                <?=($CMSNT->site('data-sidebar-service-show') == '1') ? 'selected' : '';?>>
                                                                <?=__('Có');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Animate On Scroll');?></label>
                                                    <div class="form-group">
                                                        <select class="form-select" name="data-block-animate">
                                                            <option value="0"
                                                                <?=($CMSNT->site('data-block-animate') == 0) ? 'selected' : '';?>>
                                                                <?=__('Không');?></option>
                                                            <option value="1"
                                                                <?=($CMSNT->site('data-block-animate') == 1) ? 'selected' : '';?>>
                                                                <?=__('Có');?></option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <!-- <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                        <label
                                                            class="form-label fw-medium"><?=__('Hiển thị thông tin người dùng trong Sidebar');?></label>
                                                        <div class="form-group">
                                                            <select class="form-select" name="data-sidebar-user-show">
                                                                <option value="0"
                                                                    <?=($CMSNT->site('data-sidebar-user-show') == '0') ? 'selected' : '';?>>
                                                                    <?=__('Không');?></option>
                                                                <option value="1"
                                                                    <?=($CMSNT->site('data-sidebar-user-show') == '1') ? 'selected' : '';?>>
                                                                    <?=__('Có');?></option>
                                                            </select>
                                                        </div>
                                                    </div> -->


                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Button Primary Color');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-btn-primary"
                                                            value="<?=$CMSNT->site('data-btn-primary');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Button Primary RGB');?></label>
                                                    <div class="form-group">
                                                        <?php
                                                            // Chuyển đổi giá trị RGB thành mã HEX
                                                            $rgb_value = $CMSNT->site('data-btn-primary-rgb');
                                                            $hex_value = '#3b71ca'; // Giá trị mặc định
                                                            if ($rgb_value) {
                                                                $rgb_array = explode(',', $rgb_value);
                                                                if (count($rgb_array) == 3) {
                                                                    $hex_value = sprintf("#%02x%02x%02x", $rgb_array[0], $rgb_array[1], $rgb_array[2]);
                                                                }
                                                            }
                                                            ?>
                                                        <input type="color" id="btn-primary-rgb-color"
                                                            class="form-control form-control-color border-0"
                                                            value="<?=$hex_value;?>" title="Chọn màu">
                                                        <input type="hidden" name="data-btn-primary-rgb"
                                                            id="btn-primary-rgb-val"
                                                            value="<?=$CMSNT->site('data-btn-primary-rgb');?>">
                                                        <small class="text-muted mt-1 d-block"
                                                            id="btn-primary-rgb-val-preview"></small>
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Button Primary Text Emphasis');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-btn-primary-emphasis"
                                                            value="<?=$CMSNT->site('data-btn-primary-emphasis');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label class="form-label fw-medium"><?=__('Link Color');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-link-color"
                                                            value="<?=$CMSNT->site('data-link-color');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Link Hover Color');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-link-hover-color"
                                                            value="<?=$CMSNT->site('data-link-hover-color');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Sidebar Color');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-sidebar-color"
                                                            value="<?=$CMSNT->site('data-sidebar-color');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                                <div class="col-lg-3 col-md-6 col-sm-12 mb-3">
                                                    <label
                                                        class="form-label fw-medium"><?=__('Menu Item Color');?></label>
                                                    <div class="form-group">
                                                        <input type="color"
                                                            class="form-control form-control-color border-0"
                                                            name="data-menu-item-color"
                                                            value="<?=$CMSNT->site('data-menu-item-color');?>"
                                                            title="Choose your color">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 mb-4">
                                            <hr>
                                            <label class="form-label fw-medium"><?=__('Chọn Home Page');?></label>
                                            <div class="theme-choices">
                                                <div class="row g-3">
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage-none" value="order"
                                                                <?=$CMSNT->site('home_page') == 'none' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="homepage-none">
                                                                <div class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center"
                                                                    style="height: 180px;">

                                                                </div>
                                                                <span
                                                                    class="theme-name"><?=__('Không sử dụng homepage');?></span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage1" value="home1"
                                                                <?=($CMSNT->site('home_page') == 'home1') ? 'checked' : '';?>>
                                                            <label for="homepage1" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage1.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 1">
                                                                <span class="theme-name">Modern Professional</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage2" value="home2"
                                                                <?=($CMSNT->site('home_page') == 'home2') ? 'checked' : '';?>>
                                                            <label for="homepage2" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage2.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 2">
                                                                <span class="theme-name">Dark Cyber</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage3" value="home3"
                                                                <?=($CMSNT->site('home_page') == 'home3') ? 'checked' : '';?>>
                                                            <label for="homepage3" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage3.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 3">
                                                                <span class="theme-name">Luxury Premium</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage4" value="home4"
                                                                <?=($CMSNT->site('home_page') == 'home4') ? 'checked' : '';?>>
                                                            <label for="homepage4" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage4.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 4">
                                                                <span class="theme-name">Corporate Clean</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage5" value="home5"
                                                                <?=($CMSNT->site('home_page') == 'home5') ? 'checked' : '';?>>
                                                            <label for="homepage5" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage5.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 5">
                                                                <span class="theme-name">Ultra Modern</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage6" value="home6"
                                                                <?=($CMSNT->site('home_page') == 'home6') ? 'checked' : '';?>>
                                                            <label for="homepage6" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage6.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 6">
                                                                <span class="theme-name">Cyber Neon</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage7" value="home7"
                                                                <?=($CMSNT->site('home_page') == 'home7') ? 'checked' : '';?>>
                                                            <label for="homepage7" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage7.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 7">
                                                                <span class="theme-name">Glassmorphism</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="home_page"
                                                                id="homepage8" value="home8"
                                                                <?=($CMSNT->site('home_page') == 'home8') ? 'checked' : '';?>>
                                                            <label for="homepage8" class="theme-label">
                                                                <img src="<?=base_url('assets/img/homepage8.webp');?>"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Homepage 8">
                                                                <span class="theme-name">Minimalism</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Theme Selection -->
                                        <div class="col-lg-12 mb-4">
                                            <hr>
                                            <label class="form-label fw-medium"><?=__('Chọn giao diện');?></label>
                                            <div class="theme-choices">
                                                <div class="row g-3">
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-default" value="default"
                                                                <?=($CMSNT->site('data-theme') == 'default') ? 'checked' : '';?>>
                                                            <label for="theme-default" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/default.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Default Theme">
                                                                <span class="theme-name">Default</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-saas" value="saas"
                                                                <?=($CMSNT->site('data-theme') == 'saas') ? 'checked' : '';?>>
                                                            <label for="theme-saas" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/saas.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Saas Theme">
                                                                <span class="theme-name">Saas</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-corporate" value="corporate"
                                                                <?=($CMSNT->site('data-theme') == 'corporate') ? 'checked' : '';?>>
                                                            <label for="theme-corporate" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/corporate.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Corporate Theme">
                                                                <span class="theme-name">Corporate</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-galaxy" value="galaxy"
                                                                <?=($CMSNT->site('data-theme') == 'galaxy') ? 'checked' : '';?>>
                                                            <label for="theme-galaxy" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/galaxy.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Galaxy Theme">
                                                                <span class="theme-name">Galaxy</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-material" value="material"
                                                                <?=($CMSNT->site('data-theme') == 'material') ? 'checked' : '';?>>
                                                            <label for="theme-material" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/material.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Material Theme">
                                                                <span class="theme-name">Material</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-creative" value="creative"
                                                                <?=($CMSNT->site('data-theme') == 'creative') ? 'checked' : '';?>>
                                                            <label for="theme-creative" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/creative.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Creative Theme">
                                                                <span class="theme-name">Creative</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-minimal" value="minimal"
                                                                <?=($CMSNT->site('data-theme') == 'minimal') ? 'checked' : '';?>>
                                                            <label for="theme-minimal" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/minimal.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Minimal Theme">
                                                                <span class="theme-name">Minimal</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-modern" value="modern"
                                                                <?=($CMSNT->site('data-theme') == 'modern') ? 'checked' : '';?>>
                                                            <label for="theme-modern" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/modern.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Modern Theme">
                                                                <span class="theme-name">Modern</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio" name="data-theme"
                                                                id="theme-interactive" value="interactive"
                                                                <?=($CMSNT->site('data-theme') == 'interactive') ? 'checked' : '';?>>
                                                            <label for="theme-interactive" class="theme-label">
                                                                <img src="https://themesbrand.com/velzon/assets/images/demo/interactive.png"
                                                                    class="img-fluid rounded theme-img"
                                                                    alt="Interactive Theme">
                                                                <span class="theme-name">Interactive</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <style>
                                            /* .theme-choices {
                                                    max-height: 400px;
                                                    overflow-y: auto;
                                                } */

                                            .theme-item {
                                                position: relative;
                                                margin-bottom: 15px;
                                            }

                                            .theme-radio {
                                                position: absolute;
                                                opacity: 0;
                                                width: 0;
                                                height: 0;
                                            }

                                            .theme-label {
                                                display: flex;
                                                flex-direction: column;
                                                align-items: center;
                                                cursor: pointer;
                                                padding: 6px;
                                                border-radius: 6px;
                                                border: 2px solid #eee;
                                                transition: all 0.3s;
                                            }

                                            .theme-img {
                                                width: 100%;
                                                transition: all 0.3s;
                                            }

                                            .theme-name {
                                                margin-top: 5px;
                                                font-size: 12px;
                                                font-weight: 600;
                                            }

                                            .theme-radio:checked+.theme-label {
                                                border-color: #3b71ca;
                                                background-color: rgba(59, 113, 202, 0.05);
                                                box-shadow: 0 0 0 2px rgba(59, 113, 202, 0.25);
                                            }

                                            .theme-radio:checked+.theme-label .theme-name {
                                                color: #3b71ca;
                                            }

                                            .theme-label:hover {
                                                border-color: #3b71ca;
                                            }
                                            </style>
                                        </div>

                                        <div class="col-lg-12 col-md-12 col-sm-12 mb-3">
                                            <label class="form-label fw-medium"><?=__('Sidebar Image');?></label>
                                            <div class="form-group">
                                                <div class="row g-3">
                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio"
                                                                name="data-sidebar-image" id="sidebarimg-none"
                                                                value="none"
                                                                <?=$CMSNT->site('data-sidebar-image') == 'none' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="sidebarimg-none">
                                                                <div class="avatar-md w-auto bg-light d-flex align-items-center justify-content-center"
                                                                    style="height: 180px;">

                                                                </div>
                                                                <span
                                                                    class="theme-name"><?=__('Không sử dụng ảnh');?></span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio"
                                                                name="data-sidebar-image" id="sidebarimg-01"
                                                                value="img-1"
                                                                <?=$CMSNT->site('data-sidebar-image') == 'img-1' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="sidebarimg-01">
                                                                <img src="<?=BASE_URL('public/client/assets/');?>images/sidebar/img-1.jpg"
                                                                    class="img-fluid rounded theme-img sidebar-preview"
                                                                    alt="Sidebar Image 1">
                                                                <span class="theme-name">Hình 1</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio"
                                                                name="data-sidebar-image" id="sidebarimg-02"
                                                                value="img-2"
                                                                <?=$CMSNT->site('data-sidebar-image') == 'img-2' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="sidebarimg-02">
                                                                <img src="<?=BASE_URL('public/client/assets/');?>images/sidebar/img-2.jpg"
                                                                    class="img-fluid rounded theme-img sidebar-preview"
                                                                    alt="Sidebar Image 2">
                                                                <span class="theme-name">Hình 2</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio"
                                                                name="data-sidebar-image" id="sidebarimg-03"
                                                                value="img-3"
                                                                <?=$CMSNT->site('data-sidebar-image') == 'img-3' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="sidebarimg-03">
                                                                <img src="<?=BASE_URL('public/client/assets/');?>images/sidebar/img-3.jpg"
                                                                    class="img-fluid rounded theme-img sidebar-preview"
                                                                    alt="Sidebar Image 3">
                                                                <span class="theme-name">Hình 3</span>
                                                            </label>
                                                        </div>
                                                    </div>

                                                    <div class="col-lg-3 col-sm-6">
                                                        <div class="theme-item">
                                                            <input type="radio" class="theme-radio"
                                                                name="data-sidebar-image" id="sidebarimg-04"
                                                                value="img-4"
                                                                <?=$CMSNT->site('data-sidebar-image') == 'img-4' ? 'checked' : '';?>>
                                                            <label class="theme-label" for="sidebarimg-04">
                                                                <img src="<?=BASE_URL('public/client/assets/');?>images/sidebar/img-4.jpg"
                                                                    class="img-fluid rounded theme-img sidebar-preview"
                                                                    alt="Sidebar Image 4">
                                                                <span class="theme-name">Hình 4</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <style>
                                        .sidebar-preview {
                                            height: 280px;
                                            object-fit: cover;
                                            width: 150px;
                                            margin: 0 auto;
                                        }
                                        </style>

                                        <div class="col-12">
                                            <script>
                                            document.addEventListener('DOMContentLoaded', function() {
                                                // Hàm chuyển HEX sang RGB
                                                function hexToRgb(hex) {
                                                    // Loại bỏ dấu # nếu có
                                                    hex = hex.replace(/^#/, '');

                                                    // Phân tích các giá trị RGB
                                                    var r = parseInt(hex.substring(0, 2), 16);
                                                    var g = parseInt(hex.substring(2, 4), 16);
                                                    var b = parseInt(hex.substring(4, 6), 16);

                                                    // Trả về chuỗi RGB
                                                    return r + "," + g + "," + b;
                                                }

                                                // Xử lý Button Primary RGB
                                                var btnPrimaryRgbColor = document.getElementById(
                                                    'btn-primary-rgb-color');
                                                var btnPrimaryRgbVal = document.getElementById(
                                                    'btn-primary-rgb-val');
                                                var btnPrimaryRgbValPreview = document.getElementById(
                                                    'btn-primary-rgb-val-preview');

                                                // Thiết lập giá trị ban đầu nếu chưa có
                                                if (!btnPrimaryRgbVal.value) {
                                                    btnPrimaryRgbVal.value = hexToRgb(btnPrimaryRgbColor
                                                        .value);
                                                }

                                                // Hiển thị giá trị RGB hiện tại
                                                btnPrimaryRgbValPreview.textContent = 'RGB: ' +
                                                    btnPrimaryRgbVal.value;

                                                // Cập nhật khi người dùng chọn màu
                                                btnPrimaryRgbColor.addEventListener('input', function(e) {
                                                    var rgbValue = hexToRgb(e.target.value);
                                                    btnPrimaryRgbVal.value = rgbValue;
                                                    btnPrimaryRgbValPreview.textContent = 'RGB: ' +
                                                        rgbValue;
                                                });
                                            });
                                            </script>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <button name="SaveSettings" class="btn btn-primary" type="submit">
                                            <i class="fas fa-save"></i> <?=__('Lưu Cài Đặt');?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






<?php
require_once(__DIR__.'/footer.php');
?>