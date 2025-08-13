<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => 'Settings',
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<!-- ckeditor -->
<script src="'.BASE_URL('public/ckeditor/ckeditor.js').'"></script>
<!-- Thêm CSS của CodeMirror -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.css">

<!-- Thêm JavaScript của CodeMirror -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/codemirror.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/theme/monokai.min.css">
<!-- Mode HTML mixed (hỗ trợ HTML, CSS và JS) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/htmlmixed/htmlmixed.min.js"></script>
<!-- Mode cho CSS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/css/css.min.js"></script>
<!-- Mode cho JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/javascript/javascript.min.js"></script>
<!-- Mode cho XML (cần cho HTML) -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.5/mode/xml/xml.min.js"></script>

';
$body['footer'] = '
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Internal Select-2.js -->
<script src="' . base_url('public/theme/') . 'assets/js/select2.js"></script>
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
require_once(__DIR__.'/../../models/is_license.php');
if(checkPermission($getUser['admin'], 'edit_setting') != true){
    die('<script type="text/javascript">if(!alert("Bạn không có quyền sử dụng tính năng này")){window.history.back();}</script>');
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
        'action'        => __('Thay đổi thông tin trong trang cài đặt')
    ]);
    // Xử lý cập nhật quantity_unit cho service types trước
    foreach ($_POST as $key => $value) {
        // Kiểm tra nếu là field service types
        if (strpos($key, 'smm_service_types_') === 0) {
            // Lấy ID service type từ key
            $service_type_id = str_replace('smm_service_types_', '', $key);
            // Cập nhật quantity_unit vào table smm_service_types
            $CMSNT->update("smm_service_types", array(
                'quantity_unit' => $value
            ), " `id` = '$service_type_id' ");
        }
    }
    
    // Xử lý các settings thông thường (bỏ qua fields service types)
    foreach ($_POST as $key => $value) {
        // Bỏ qua fields service types vì đã xử lý ở trên
        if (strpos($key, 'smm_service_types_') !== 0) {
            $CMSNT->update("settings", array(
                'value' => $value
            ), " `name` = '$key' ");
        }
    }
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __('Thay đổi thông tin trong trang cài đặt'), $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);    
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);

    admin_msg_success("Lưu thành công!", "", 1000);
} 
?>

<style>
/* Ẩn pseudo-element :before của card title */
.card.custom-card .card-header .card-title:before {
    display: none !important;
}

/* Hoặc có thể dùng cách này */
.card.custom-card .card-header .card-title:before {
    content: none !important;
}
</style>
<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-gear"></i> Cài đặt</h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-xl-2">
                                <nav class="nav nav-tabs flex-column nav-style-5 mb-3" role="tablist">
                                    <a class="nav-link active" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#cai-dat-chung" aria-selected="false"><i
                                            class="bx bx-cog me-2 align-middle d-inline-block"></i><?=__('Cài đặt chung');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#cai-dat-smmpanel" aria-selected="false"><i
                                            class="bx bx-cog me-2 align-middle d-inline-block"></i><?=__('Cài đặt SMM Panel');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#ket-noi" aria-selected="false"><i
                                            class="bx bx-plug me-2 align-middle d-inline-block"></i><?=__('Kết nối');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#notification" aria-selected="false"><i
                                            class="bx bx-bell me-2 align-middle d-inline-block"></i><?=__('Thông báo');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#telegram-template" aria-selected="true"><i
                                            class="fa-brands fa-telegram me-2 align-middle d-inline-block"></i><?=__('Telegram Template');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#mail-template" aria-selected="true"><i
                                            class="fa-solid fa-envelope me-2 align-middle d-inline-block"></i><?=__('Mail Template');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#security" aria-selected="true"><i
                                            class="fa-solid fa-shield-halved me-2 align-middle d-inline-block"></i><?=__('Bảo mật');?></a>
                                    <a class="nav-link" data-bs-toggle="tab" role="tab" aria-current="page"
                                        href="#widget" aria-selected="false"><i
                                            class="fa-brands fa-themeco me-2 align-middle d-inline-block"></i><?=__('Widget');?></a>
                                </nav>
                            </div>
                            <div class="col-xl-10">
                                <div class="tab-content">
                                    <div class="tab-pane text-muted show active" id="cai-dat-chung" role="tabpanel">
                                        <h4><?=__('Cài đặt chung');?></h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-6">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td><?=__('Allowed Domains');?></td>
                                                                <td>
                                                                    <input type="text" name="domains"
                                                                        value="<?=$CMSNT->site('domains');?>"
                                                                        class="form-control">
                                                                    <small>Không thay đổi nếu không hiểu rõ, phí khôi
                                                                        phục 100.000đ 1 lần</small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Title');?></td>
                                                                <td>
                                                                    <input type="text" name="title"
                                                                        value="<?=$CMSNT->site('title');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Description');?></td>
                                                                <td>
                                                                    <textarea name="description"
                                                                        class="form-control"><?=$CMSNT->site('description');?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Keywords');?></td>
                                                                <td>
                                                                    <textarea name="keywords"
                                                                        class="form-control"><?=$CMSNT->site('keywords');?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Copyright Footer Left');?></td>
                                                                <td>
                                                                    <textarea name="copyright_footer_left"
                                                                        class="form-control"><?=$CMSNT->site('copyright_footer_left');?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Copyright Footer Right');?></td>
                                                                <td>
                                                                    <textarea name="copyright_footer"
                                                                        class="form-control"><?=$CMSNT->site('copyright_footer');?></textarea>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Author');?></td>
                                                                <td>
                                                                    <input type="text" name="author"
                                                                        value="<?=$CMSNT->site('author');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Timezone');?></td>
                                                                <td>
                                                                    <input type="text" name="timezone"
                                                                        value="<?=$CMSNT->site('timezone');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Email');?></td>
                                                                <td>
                                                                    <div class="input-group mb-1">
                                                                        <span
                                                                            class="input-group-text"><?=$CMSNT->site('icon_email');?></span>
                                                                        <input type="text" name="icon_email"
                                                                            value='<?=$CMSNT->site('icon_email');?>'
                                                                            class="form-control">
                                                                    </div>
                                                                    <input type="text" name="email"
                                                                        value="<?=$CMSNT->site('email');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Hotline');?></td>
                                                                <td>
                                                                    <div class="input-group mb-1">
                                                                        <span
                                                                            class="input-group-text"><?=$CMSNT->site('icon_hotline');?></span>
                                                                        <input type="text" name="icon_hotline"
                                                                            value='<?=$CMSNT->site('icon_hotline');?>'
                                                                            class="form-control">
                                                                    </div>
                                                                    <input type="text" name="hotline"
                                                                        value="<?=$CMSNT->site('hotline');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>

                                                            <tr>
                                                                <td><?=__('Địa chỉ');?></td>
                                                                <td>
                                                                    <div class="input-group mb-1">
                                                                        <span
                                                                            class="input-group-text"><?=$CMSNT->site('icon_address');?></span>
                                                                        <input type="text" name="icon_address"
                                                                            value='<?=$CMSNT->site('icon_address');?>'
                                                                            class="form-control">
                                                                    </div>
                                                                    <input type="text" name="address"
                                                                        value="<?=$CMSNT->site('address');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Fanpage');?></td>
                                                                <td>
                                                                    <input type="text" name="fanpage"
                                                                        value="<?=$CMSNT->site('fanpage');?>"
                                                                        class="form-control">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('ON/OFF Debug Auto Bank');?></td>
                                                                <td>
                                                                    <select class="form-control" name="debug_auto_bank">
                                                                        <option
                                                                            <?=$CMSNT->site('debug_auto_bank') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('debug_auto_bank') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <small><?=__('Không bật ON khi chưa được CMSNT yêu cầu');?>.</small>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td>Font Family</td>
                                                                <td>
                                                                    <input type="text" name="font_family"
                                                                        value="<?=$CMSNT->site('font_family');?>"
                                                                        class="form-control">
                                                                    <small><a class="text-primary"
                                                                            href="https://help.cmsnt.co/huong-dan/smmpanel2-huong-dan-thay-doi-font-chu-cho-website/"
                                                                            target="_blank"><?=__('Hướng dẫn sử dụng');?></a></small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Trạng thái website');?></td>
                                                                <td>
                                                                    <select class="form-control" name="status">
                                                                        <option
                                                                            <?=$CMSNT->site('status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <small><?=__('Chọn OFF nếu bạn muốn bật chế độ bảo trì.');?></small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('Cập nhật phiên bản tự động');?></td>
                                                                <td>
                                                                    <select class="form-control" name="status_update">
                                                                        <option
                                                                            <?=$CMSNT->site('status_update') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('status_update') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <small><?=__('Hệ thống sẽ tự động cập nhật khi có phiên bản mới nếu bạn chọn ON.');?></small>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td><?=__('ON/OFF Tài liệu API');?></td>
                                                                <td>
                                                                    <select class="form-control" name="api_status">
                                                                        <option
                                                                            <?=$CMSNT->site('api_status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('api_status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <small><?=__('Hệ thống sẽ ẩn menu tài liệu API nếu bạn chọn OFF');?></small>
                                                                </td>
                                                            </tr>
                                                            <!-- <tr>
                                                                <td>ON/OFF menu Blogs</td>
                                                                <td>
                                                                    <select class="form-control" name="blog_status">
                                                                        <option
                                                                            <?=$CMSNT->site('blog_status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('blog_status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <small>Hệ thống sẽ ẩn menu Blogs nếu bạn chọn
                                                                        OFF, các bài viết vẫn hiển thị trên google tìm
                                                                        kiếm.</small>
                                                                </td>
                                                            </tr> -->



                                                            <tr>
                                                                <td><?=__('Hiển thị hình đại diện');?>
                                                                </td>
                                                                <td>
                                                                    <select class="form-control" name="type_avatar">
                                                                        <option
                                                                            <?=$CMSNT->site('type_avatar') == 'gravatar' ? 'selected' : '';?>
                                                                            value="gravatar"><?=__('Gravatar');?>
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('type_avatar') == 'ui-avatars' ? 'selected' : '';?>
                                                                            value="ui-avatars">
                                                                            <?=__('Theo chữ cái đầu (UI Avatars)');?>
                                                                        </option>
                                                                    </select>
                                                                </td>
                                                            </tr>

                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-12">
                                                    <div class="card border border-success-transparent">
                                                        <div class="card-header bg-success-transparent">
                                                            <div class="d-flex align-items-center">
                                                                <div>
                                                                    <h6
                                                                        class="card-title mb-0 text-uppercase text-dark fw-semibold">
                                                                        <?=__('Tùy chỉnh Script/HTML');?>
                                                                    </h6>
                                                                    <small
                                                                        class="text-muted"><?=__('Cấu hình các script và HTML tùy chỉnh cho website');?></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <!-- Header Script cho trang khách -->
                                                                <div class="col-lg-12 mb-4">
                                                                    <div
                                                                        class="card border border-primary-transparent h-100">
                                                                        <div class="card-header bg-primary-transparent">
                                                                            <div class="d-flex align-items-center">
                                                                                <div>
                                                                                    <h6
                                                                                        class="card-title mb-0 fw-semibold text-dark">
                                                                                        <?=__('Script/HTML Header trang khách');?>
                                                                                    </h6>
                                                                                    <small class="text-muted">
                                                                                        <?=__('Hiển thị trong thẻ &lt;head&gt; của trang khách');?>
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <textarea rows="8" name="javascript_header"
                                                                                id="javascript_header"
                                                                                class="form-control"
                                                                                placeholder="<?=__('Nhập script/HTML header trang khách...');?>"><?=$CMSNT->site('javascript_header');?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Footer Script cho trang khách -->
                                                                <div class="col-lg-12 mb-4">
                                                                    <div
                                                                        class="card border border-warning-transparent h-100">
                                                                        <div
                                                                            class="card-header bg-warning-transparent d-flex justify-content-between align-items-center">
                                                                            <div>
                                                                                <h6
                                                                                    class="card-title mb-0 fw-semibold text-dark">
                                                                                    <?=__('Script/HTML Footer trang khách');?>
                                                                                </h6>
                                                                                <small class="text-muted">
                                                                                    <?=__('Hiển thị cuối trang khách trước thẻ &lt;/body&gt;');?>
                                                                                </small>
                                                                            </div>
                                                                            <button type="button"
                                                                                class="btn btn-sm btn-dark btn-wave text-white"
                                                                                onclick="openFooterScriptAIModal()"
                                                                                title="Tạo script bằng AI">
                                                                                <i
                                                                                    class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                            </button>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <textarea rows="8" name="javascript_footer"
                                                                                id="javascript_footer"
                                                                                class="form-control"
                                                                                placeholder="<?=__('Nhập script/HTML footer trang khách...');?>"><?=$CMSNT->site('javascript_footer');?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>



                                                                <!-- Footer Script cho trang admin -->
                                                                <div class="col-lg-12 mb-4">
                                                                    <div
                                                                        class="card border border-danger-transparent h-100">
                                                                        <div class="card-header bg-danger-transparent">
                                                                            <div class="d-flex align-items-center">
                                                                                <div>
                                                                                    <h6
                                                                                        class="card-title mb-0 fw-semibold text-dark">
                                                                                        <?=__('Script/HTML Footer trang quản trị');?>
                                                                                    </h6>
                                                                                    <small class="text-muted">
                                                                                        <?=__('Hiển thị cuối trang admin trước thẻ &lt;/body&gt;');?>
                                                                                    </small>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <textarea rows="8"
                                                                                name="script_footer_admin"
                                                                                id="script_footer_admin"
                                                                                class="form-control"
                                                                                placeholder="<?=__('Nhập script/HTML footer trang admin...');?>"><?=$CMSNT->site('script_footer_admin');?></textarea>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Thông tin hỗ trợ -->
                                                            <div class="row">
                                                                <div class="col-12">
                                                                    <div
                                                                        class="alert alert-light border border-light-subtle">
                                                                        <div class="d-flex align-items-start">
                                                                            <i
                                                                                class="ri-lightbulb-line text-warning me-2 fs-16 mt-1"></i>
                                                                            <div>
                                                                                <h6 class="alert-heading mb-2">
                                                                                    <i
                                                                                        class="ri-information-line me-1"></i>
                                                                                    <?=__('Lưu ý quan trọng');?>
                                                                                </h6>
                                                                                <ul class="mb-0 small">
                                                                                    <li><?=__('Header Script: Dành cho Google Analytics, Facebook Pixel, Meta tags...');?>
                                                                                    </li>
                                                                                    <li><?=__('Footer Script: Dành cho chat plugin, tracking, popup...');?>
                                                                                    </li>
                                                                                    <li><?=__('Footer Card: Script hiển thị trong phần footer card của trang khách');?>
                                                                                    </li>
                                                                                    <li><?=__('Admin Footer: Script chỉ hiển thị trong trang quản trị');?>
                                                                                    </li>
                                                                                    <li class="text-danger">
                                                                                        <?=__('Cẩn thận với script có thể ảnh hưởng đến bảo mật website');?>
                                                                                    </li>
                                                                                </ul>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings"
                                                class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> <?=__('Save');?>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="cai-dat-smmpanel" role="tabpanel">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                                                    <i class="ri-settings-3-line fs-18 text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-1"><?=__('Cài đặt SMM Panel');?></h4>
                                                <p class="text-muted mb-0">
                                                    <?=__('Cấu hình các tính năng và tham số cho hệ thống SMM Panel');?>
                                                </p>
                                            </div>
                                        </div>

                                        <form action="" method="POST">
                                            <div class="row g-4">
                                                <!-- Card: Cấu hình thuế và quyền truy cập -->
                                                <div class="col-xl-6">
                                                    <div class="card border border-primary-subtle h-100">
                                                        <div class="card-body">
                                                            <!-- Thuế VAT -->
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-percent-line me-2 text-primary"></i>
                                                                    <?=__('Thuế VAT nếu có');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input name="tax_vat" type="text"
                                                                        class="form-control"
                                                                        value="<?=$CMSNT->site('tax_vat');?>"
                                                                        placeholder="<?=__('Nhập % thuế VAT');?>"
                                                                        required>
                                                                    <span
                                                                        class="input-group-text bg-primary-transparent text-primary fw-semibold">
                                                                        <i class="ri-percent-line me-1"></i>
                                                                    </span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Nhập 0 nếu không muốn tính thuế VAT cho đơn hàng');?>
                                                                </div>
                                                            </div>

                                                            <!-- Quyền xem dịch vụ -->
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-lock-line me-2 text-warning"></i>
                                                                    <?=__('Yêu cầu đăng nhập để xem dịch vụ');?>
                                                                </label>
                                                                <select class="form-select"
                                                                    name="isLoginRequiredToViewProduct">
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('isLoginRequiredToViewProduct') == 1 ? 'selected' : '';?>>
                                                                        <i class="ri-lock-line"></i>
                                                                        <?=__('BẬT - Phải đăng nhập mới xem được dịch vụ');?>
                                                                    </option>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('isLoginRequiredToViewProduct') == 0 ? 'selected' : '';?>>
                                                                        <i class="ri-lock-unlock-line"></i>
                                                                        <?=__('TẮT - Không cần đăng nhập');?>
                                                                    </option>
                                                                </select>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Bật tính năng này để yêu cầu khách hàng phải đăng nhập mới có thể xem danh sách dịch vụ');?>
                                                                </div>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-repeat-line me-2 text-success"></i>
                                                                    <?=__('ON/OFF Multiple Order');?>
                                                                </label>
                                                                <select class="form-select" name="isMultipleOrder">
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('isMultipleOrder') == 1 ? 'selected' : '';?>>
                                                                        <i class="ri-repeat-line"></i>
                                                                        <?=__('BẬT');?>
                                                                    </option>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('isMultipleOrder') == 0 ? 'selected' : '';?>>
                                                                        <i class="ri-repeat-line"></i>
                                                                        <?=__('TẮT');?>
                                                                    </option>
                                                                </select>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Bật tính năng này để cho phép khách hàng đặt hàng nhiều lần trong cùng 1 đơn hàng');?>
                                                                </div>
                                                            </div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card: Hệ thống tải dữ liệu -->
                                                <div class="col-xl-6">
                                                    <div class="card border border-success-subtle h-100">
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-database-2-line me-2 text-info"></i>
                                                                    <?=__('Chế độ tải dữ liệu');?>
                                                                </label>
                                                                <select class="form-select" name="load_data_type"
                                                                    id="load_data_type"
                                                                    onchange="updateLoadDataDescription()">
                                                                    <option value="all"
                                                                        <?=$CMSNT->site('load_data_type') == 'all' ? 'selected' : '';?>>
                                                                        <i class="ri-database-line"></i>
                                                                        <?=__('All Data - Tải toàn bộ');?>
                                                                    </option>
                                                                    <option value="lazy"
                                                                        <?=$CMSNT->site('load_data_type') == 'lazy' ? 'selected' : '';?>>
                                                                        <i class="ri-speed-line"></i>
                                                                        <?=__('Lazy Load - Tải từng phần');?>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Mô tả chi tiết về chế độ -->
                                                            <div id="load_data_description" class="mt-3">
                                                                <div class="alert alert-info-transparent border-0 p-3">
                                                                    <div class="d-flex align-items-start">
                                                                        <i
                                                                            class="ri-information-line fs-16 me-2 mt-1"></i>
                                                                        <div>
                                                                            <small class="text-muted fw-medium"></small>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card border border-primary-subtle h-100">
                                                        <div class="card-body">
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i
                                                                        class="ri-price-tag-3-line me-2 text-primary"></i>
                                                                    <?=__('Cấu hình hiển thị giá bán dịch vụ cho từng loại dịch vụ');?>
                                                                </label>
                                                                <?php foreach(getListServiceType() as $service_type):?>
                                                                <div class="input-group mb-1">
                                                                    <span class="input-group-text fw-semibold">
                                                                        <?=$service_type['name'];?>
                                                                    </span>
                                                                    <select class="form-select"
                                                                        name="smm_service_types_<?=$service_type['id'];?>">
                                                                        <option value="1"
                                                                            <?=$service_type['quantity_unit'] == 1 ? 'selected' : '';?>>
                                                                            <i class="ri-lock-line"></i>
                                                                            <?=__('Hiển thị giá bán của số lượng 1');?>
                                                                        </option>
                                                                        <option value="1000"
                                                                            <?=$service_type['quantity_unit'] == 1000 ? 'selected' : '';?>>
                                                                            <i class="ri-lock-unlock-line"></i>
                                                                            <?=__('Hiển thị giá bán của số lượng 1000');?>
                                                                        </option>
                                                                    </select>
                                                                </div>
                                                                <?php endforeach;?>
                                                            </div>
                                                            <div class="form-text">
                                                                <i class="ri-information-line me-1"></i>
                                                                <?=__('Chỉ áp dụng trên giao diện đặt hàng và Bảng giá dịch vụ, không áp dụng cho API.');?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6">
                                                    <div class="card border border-success-subtle h-100">
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"><i
                                                                        class="ri-repeat-line me-2 text-success"></i>
                                                                    <?=__('Loại random mã đơn hàng');?></label>
                                                                <select class="form-select"
                                                                    name="random_transid_order_type">
                                                                    <option value="string"
                                                                        <?=$CMSNT->site('random_transid_order_type') == 'string' ? 'selected' : '';?>>
                                                                        <?=__('Chuỗi ký tự (ABC...)');?>
                                                                    </option>
                                                                    <option value="string_number"
                                                                        <?=$CMSNT->site('random_transid_order_type') == 'string_number' ? 'selected' : '';?>>
                                                                        <?=__('Chuỗi ký tự + số (ABC123...)');?>
                                                                    </option>
                                                                    <option value="number"
                                                                        <?=$CMSNT->site('random_transid_order_type') == 'number' ? 'selected' : '';?>>
                                                                        <?=__('Chỉ số (123456...)');?>
                                                                    </option>
                                                                </select>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"><i
                                                                        class="ri-text me-2 text-danger"></i>
                                                                    <?=__('Số ký tự Random');?></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="ri-text"></i></span>
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('random_transid_order_length');?>"
                                                                        name="random_transid_order_length"
                                                                        placeholder="6" min="6" max="20">
                                                                </div>
                                                                <div class="form-text text-muted">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Tối thiểu 6 ký tự, tối đa 20 ký tự');?></div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold"><i
                                                                        class="ri-hashtag me-2 text-info"></i>
                                                                    <?=__('Prefix');?></label>
                                                                <div class="input-group">
                                                                    <span class="input-group-text"><i
                                                                            class="ri-hashtag"></i></span>
                                                                    <input type="text" class="form-control"
                                                                        value="<?=$CMSNT->site('prefix_transid_order');?>"
                                                                        name="prefix_transid_order"
                                                                        placeholder="<?=__('Nhập prefix (không bắt buộc)');?>">
                                                                </div>

                                                                <div class="form-text text-muted">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Prefix sẽ được thêm vào đầu mã đơn hàng');?>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Save Button -->
                                            <div class="row mt-4">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" name="SaveSettings"
                                                            class="btn btn-primary btn-lg px-5">
                                                            <i class="ri-save-line me-2"></i>
                                                            <?=__('Lưu cài đặt SMM Panel');?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="notification" role="tabpanel">
                                        <h4><?=__('Thay đổi nội dung thông báo');?></h4>
                                        <form action="" method="POST">
                                            <div class="row">
                                                <!-- Thông báo Website -->
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card border border-primary-transparent">
                                                        <div class="card-header bg-primary-transparent">
                                                            <h6 class="card-title mb-0 text-uppercase text-dark">
                                                                <i class="ri-notification-line me-2"></i>
                                                                <?=__('Thông báo Website');?>
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    <i
                                                                        class="ri-home-line me-1"></i><?=__('Thông báo ngoài trang chủ');?>
                                                                </label>
                                                                <textarea class="form-control" id="notice_home" rows="4"
                                                                    name="notice_home"
                                                                    placeholder="<?=__('Nhập thông báo hiển thị trên trang chủ...');?>"><?=$CMSNT->site('notice_home');?></textarea>
                                                                <small
                                                                    class="text-muted"><?=__('Hiển thị ở banner hoặc khu vực thông báo trang chủ');?></small>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label fw-semibold">
                                                                    <i
                                                                        class="ri-history-line me-1"></i><?=__('Thông báo trang lịch sử đơn hàng');?>
                                                                </label>
                                                                <textarea class="form-control" id="notice_orders"
                                                                    rows="4" name="notice_orders"
                                                                    placeholder="<?=__('Nhập thông báo hiển thị trên trang lịch sử đơn hàng...');?>"><?=$CMSNT->site('notice_orders');?></textarea>
                                                                <small
                                                                    class="text-muted"><?=__('Hiển thị ở đầu trang lịch sử đơn hàng của khách hàng');?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Thông báo Popup -->
                                                <div class="col-lg-6 mb-4">
                                                    <div class="card border border-warning-transparent">
                                                        <div class="card-header bg-warning-transparent">
                                                            <h6 class="card-title mb-0 text-uppercase text-dark">
                                                                <i class="ri-window-line me-2"></i>
                                                                <?=__('Thông báo Popup');?>
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="mb-3">
                                                                <label class="form-label fw-semibold">
                                                                    <i
                                                                        class="ri-toggle-line me-1"></i><?=__('Trạng thái Popup');?>
                                                                </label>
                                                                <select class="form-select" name="popup_status">
                                                                    <option
                                                                        <?=$CMSNT->site('popup_status') == 1 ? 'selected' : '';?>
                                                                        value="1">
                                                                        <i class="ri-checkbox-circle-line"></i>
                                                                        <?=__('Bật');?> (ON)
                                                                    </option>
                                                                    <option
                                                                        <?=$CMSNT->site('popup_status') == 0 ? 'selected' : '';?>
                                                                        value="0">
                                                                        <i class="ri-close-circle-line"></i>
                                                                        <?=__('Tắt');?> (OFF)
                                                                    </option>
                                                                </select>
                                                                <small
                                                                    class="text-muted"><?=__('Bật/tắt hiển thị popup thông báo trên website');?></small>
                                                            </div>
                                                            <div class="mb-0">
                                                                <label class="form-label fw-semibold">
                                                                    <i
                                                                        class="ri-window-2-line me-1"></i><?=__('Nội dung Popup');?>
                                                                </label>
                                                                <textarea class="form-control" id="popup_noti" rows="5"
                                                                    name="popup_noti"
                                                                    placeholder="<?=__('Nhập nội dung hiển thị trong popup...');?>"><?=$CMSNT->site('popup_noti');?></textarea>
                                                                <small
                                                                    class="text-muted"><?=__('Nội dung sẽ hiển thị trong popup khi khách truy cập website');?></small>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Nội dung các trang -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card border border-info-transparent">
                                                        <div class="card-header bg-info-transparent">
                                                            <h6 class="card-title mb-0 text-uppercase text-dark">
                                                                <i class="ri-pages-line me-2"></i>
                                                                <?=__('Nội dung các trang hệ thống');?>
                                                            </h6>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <!-- Trang liên hệ -->
                                                                <div class="col-lg-6 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-label fw-semibold mb-0">
                                                                            <i
                                                                                class="ri-phone-line me-1"></i><?=__('Nội dung trang liên hệ');?>
                                                                        </label>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-dark btn-wave text-white btn-ai-page"
                                                                            data-type="page_contact"
                                                                            data-target="#page_contact"
                                                                            title="Tạo nội dung bằng AI">
                                                                            <i
                                                                                class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                        </button>
                                                                    </div>
                                                                    <textarea class="form-control" id="page_contact"
                                                                        rows="6" name="page_contact"
                                                                        placeholder="<?=__('Nhập nội dung trang liên hệ...');?>"><?=$CMSNT->site('page_contact');?></textarea>
                                                                    <small
                                                                        class="text-muted"><?=__('Hiển thị trên trang');?>
                                                                        <a href="<?=BASE_URL('client/contact');?>"
                                                                            target="_blank"><?=__('Liên hệ');?></a></small>
                                                                </div>

                                                                <!-- Trang chính sách -->
                                                                <div class="col-lg-6 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-label fw-semibold mb-0">
                                                                            <i
                                                                                class="ri-file-shield-line me-1"></i><?=__('Nội dung trang chính sách');?>
                                                                        </label>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-dark btn-wave text-white btn-ai-page"
                                                                            data-type="page_policy"
                                                                            data-target="#page_policy"
                                                                            title="Tạo nội dung bằng AI">
                                                                            <i
                                                                                class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                        </button>
                                                                    </div>
                                                                    <textarea class="form-control" id="page_policy"
                                                                        rows="6" name="page_policy"
                                                                        placeholder="<?=__('Nhập nội dung trang chính sách...');?>"><?=$CMSNT->site('page_policy');?></textarea>
                                                                    <small
                                                                        class="text-muted"><?=__('Hiển thị trên trang');?>
                                                                        <a href="<?=BASE_URL('client/policy');?>"
                                                                            target="_blank"><?=__('Chính sách');?></a></small>
                                                                </div>

                                                                <!-- Trang quyền riêng tư -->
                                                                <div class="col-lg-6 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-label fw-semibold mb-0">
                                                                            <i
                                                                                class="ri-shield-user-line me-1"></i><?=__('Nội dung trang quyền riêng tư');?>
                                                                        </label>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-dark btn-wave text-white btn-ai-page"
                                                                            data-type="page_privacy"
                                                                            data-target="#page_privacy"
                                                                            title="Tạo nội dung bằng AI">
                                                                            <i
                                                                                class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                        </button>
                                                                    </div>
                                                                    <textarea class="form-control" id="page_privacy"
                                                                        rows="6" name="page_privacy"
                                                                        placeholder="<?=__('Nhập nội dung trang quyền riêng tư...');?>"><?=$CMSNT->site('page_privacy');?></textarea>
                                                                    <small
                                                                        class="text-muted"><?=__('Hiển thị trên trang');?>
                                                                        <a href="<?=BASE_URL('client/privacy');?>"
                                                                            target="_blank"><?=__('Quyền riêng tư');?></a></small>
                                                                </div>

                                                                <!-- Trang FAQ -->
                                                                <div class="col-lg-6 mb-3">
                                                                    <div
                                                                        class="d-flex justify-content-between align-items-center mb-2">
                                                                        <label class="form-label fw-semibold mb-0">
                                                                            <i
                                                                                class="ri-question-answer-line me-1"></i><?=__('Nội dung trang FAQ');?>
                                                                        </label>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-dark btn-wave text-white btn-ai-page"
                                                                            data-type="page_faq" data-target="#page_faq"
                                                                            title="Tạo nội dung bằng AI">
                                                                            <i
                                                                                class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                        </button>
                                                                    </div>
                                                                    <textarea class="form-control" id="page_faq"
                                                                        rows="6" name="page_faq"
                                                                        placeholder="<?=__('Nhập nội dung trang FAQ...');?>"><?=$CMSNT->site('page_faq');?></textarea>
                                                                    <small
                                                                        class="text-muted"><?=__('Hiển thị trên trang');?>
                                                                        <a href="<?=BASE_URL('client/faq');?>"
                                                                            target="_blank"><?=__('Câu hỏi thường gặp');?></a></small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="card border border-success-transparent">
                                                        <div class="card-body text-center">
                                                            <button type="submit" name="SaveSettings"
                                                                class="btn btn-success btn-wave">
                                                                <i
                                                                    class="ri-save-line me-2"></i><?=__('Lưu tất cả thay đổi');?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="ket-noi" role="tabpanel">
                                        <h4><?=__('Kết nối');?></h4>
                                        <form action="" method="POST" class="settings-form">
                                            <div class="row g-4">
                                                <div class="col-lg-6">
                                                    <!-- SMTP Configuration -->
                                                    <div class="card custom-card border-0 shadow-sm">
                                                        <div class="card-header bg-primary-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('assets/img/icon-smtp.png');?>"
                                                                        alt="SMTP" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Cấu hình SMTP');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- SMTP Status -->
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-toggle-line me-2 text-success"></i>
                                                                    <?=__('Trạng thái SMTP');?>
                                                                </label>
                                                                <select class="form-select" name="smtp_status">
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('smtp_status') == 1 ? 'selected' : '';?>>
                                                                        <i class="ri-check-line"></i> <?=__('Bật');?>
                                                                    </option>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('smtp_status') == 0 ? 'selected' : '';?>>
                                                                        <i class="ri-close-line"></i> <?=__('Tắt');?>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- SMTP Host -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-server-line me-2 text-primary"></i>
                                                                    <?=__('SMTP Host');?>
                                                                </label>
                                                                <input type="text" name="smtp_host" class="form-control"
                                                                    placeholder="<?=__('VD: smtp.gmail.com');?>"
                                                                    value="<?=$CMSNT->site('smtp_host');?>">
                                                            </div>

                                                            <!-- SMTP Encryption & Port -->
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i
                                                                                class="ri-shield-line me-2 text-warning"></i>
                                                                            <?=__('Mã hóa');?>
                                                                        </label>
                                                                        <input type="text" name="smtp_encryption"
                                                                            class="form-control"
                                                                            placeholder="<?=__('ssl/tls');?>"
                                                                            value="<?=$CMSNT->site('smtp_encryption');?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i class="ri-plug-line me-2 text-info"></i>
                                                                            <?=__('Cổng');?>
                                                                        </label>
                                                                        <input type="text" name="smtp_port"
                                                                            class="form-control"
                                                                            placeholder="<?=__('465, 587');?>"
                                                                            value="<?=$CMSNT->site('smtp_port');?>">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- SMTP Email -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-mail-line me-2 text-danger"></i>
                                                                    <?=__('Email SMTP');?>
                                                                </label>
                                                                <input type="email" name="smtp_email"
                                                                    class="form-control"
                                                                    placeholder="<?=__('VD: yourmail@gmail.com');?>"
                                                                    value="<?=$CMSNT->site('smtp_email');?>">
                                                            </div>

                                                            <!-- SMTP Password -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-key-line me-2 text-secondary"></i>
                                                                    <?=__('Mật khẩu SMTP');?>
                                                                </label>
                                                                <input type="password" name="smtp_password"
                                                                    class="form-control"
                                                                    placeholder="<?=__('Nhập mật khẩu SMTP...');?>"
                                                                    value="<?=$CMSNT->site('smtp_password');?>">
                                                            </div>
                                                            <!-- Help Link -->
                                                            <div class="alert alert-primary-transparent border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="ri-information-line fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?=__('Cần trợ giúp?');?></strong><br>
                                                                        <a href="https://help.cmsnt.co/huong-dan/huong-dan-cau-hinh-smtp-vao-website/"
                                                                            target="_blank"
                                                                            class="text-primary fw-medium">
                                                                            <i
                                                                                class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn chi tiết tích hợp SMTP');?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- Telegram Bot Configuration -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-secondary-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('assets/img/icon-bot-telegram.avif');?>"
                                                                        alt="Telegram" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Bot thông báo Telegram');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Telegram Status -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-robot-line me-2 text-success"></i>
                                                                    <?=__('Trạng thái Bot');?>
                                                                </label>
                                                                <select class="form-select" name="telegram_status">
                                                                    <option
                                                                        <?=$CMSNT->site('telegram_status') == 1 ? 'selected' : '';?>
                                                                        value="1">
                                                                        <?=__('Bật');?>
                                                                    </option>
                                                                    <option
                                                                        <?=$CMSNT->site('telegram_status') == 0 ? 'selected' : '';?>
                                                                        value="0">
                                                                        <?=__('Tắt');?>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Bot Token -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-key-line me-2 text-warning"></i>
                                                                    <?=__('Bot Token');?>
                                                                </label>
                                                                <input type="password" name="telegram_token"
                                                                    value="<?=$CMSNT->site('telegram_token');?>"
                                                                    class="form-control"
                                                                    placeholder="123456789:XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX">
                                                                <div class="form-text">
                                                                    <a class="text-primary fw-medium"
                                                                        href="https://help.cmsnt.co/huong-dan/huong-dan-tich-hop-bot-telegram-vao-shopclone7/"
                                                                        target="_blank">
                                                                        <i
                                                                            class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn tạo bot');?>
                                                                    </a>
                                                                </div>
                                                            </div>

                                                            <!-- Chat ID & Bot Username -->
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i
                                                                                class="ri-chat-3-line me-2 text-info"></i>
                                                                            <?=__('Chat ID');?>
                                                                        </label>
                                                                        <input type="text" name="telegram_chat_id"
                                                                            value="<?=$CMSNT->site('telegram_chat_id');?>"
                                                                            class="form-control"
                                                                            placeholder="-100XXXXXXXXX">
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i
                                                                                class="ri-user-line me-2 text-primary"></i>
                                                                            <?=__('Bot Username');?>
                                                                        </label>
                                                                        <input type="text" name="telegram_bot_username"
                                                                            value="<?=$CMSNT->site('telegram_bot_username');?>"
                                                                            class="form-control"
                                                                            placeholder="@your_bot_name">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Webhook Secret -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i
                                                                        class="ri-shield-keyhole-line me-2 text-danger"></i>
                                                                    <?=__('Webhook Secret');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="password"
                                                                        name="telegram_webhook_secret"
                                                                        id="telegram_webhook_secret"
                                                                        value="<?=$CMSNT->site('telegram_webhook_secret');?>"
                                                                        class="form-control"
                                                                        placeholder="<?=__('Để trống sẽ tự động tạo khi set webhook');?>">
                                                                    <button type="button" class="btn btn-success-light"
                                                                        id="btn_set_webhook">
                                                                        <i class="ri-settings-line me-1"
                                                                            id="webhook_icon"></i>
                                                                        <i class="ri-loader-line spin"
                                                                            id="webhook_loading"
                                                                            style="display: none;"></i>
                                                                        <?=__('Set Webhook');?>
                                                                    </button>
                                                                </div>
                                                                <div class="form-text text-danger">
                                                                    <i class="ri-alert-line me-1"></i>
                                                                    <?=__('Bảo mật quan trọng - không chia sẻ cho ai');?>
                                                                </div>
                                                                <div id="webhook_result" class="mt-2"></div>
                                                            </div>

                                                            <!-- Telegram API URL -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-global-line me-2 text-secondary"></i>
                                                                    <?=__('API Server');?>
                                                                </label>
                                                                <select class="form-select" name="telegram_url">
                                                                    <option value="https://api.telegram.org/"
                                                                        <?=$CMSNT->site('telegram_url') == 'https://api.telegram.org/' ? 'selected' : '';?>>
                                                                        🌐 Official Telegram API
                                                                    </option>
                                                                    <option
                                                                        value="https://bypass-telegram.cmsnt.workers.dev/"
                                                                        <?=$CMSNT->site('telegram_url') == 'https://bypass-telegram.cmsnt.workers.dev/' ? 'selected' : '';?>>
                                                                        🚀 CMSNT Proxy Server
                                                                        (<?=__('Khuyến nghị cho VN');?>)
                                                                    </option>
                                                                </select>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Sử dụng proxy nếu Telegram bị chặn tại Việt Nam');?>
                                                                </div>
                                                            </div>
                                                            <!-- Help Link -->
                                                            <div class="alert alert-primary-transparent border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="ri-information-line fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?=__('Cần trợ giúp?');?></strong><br>
                                                                        <a href="https://help.cmsnt.co/huong-dan/smmpanel2-huong-dan-tich-hop-bot-telegram/"
                                                                            target="_blank"
                                                                            class="text-primary fw-medium">
                                                                            <i
                                                                                class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn chi tiết tích hợp Bot Telegram');?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Second Column -->
                                                <div class="col-lg-6">
                                                    <!-- Google Analytics -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-success-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('assets/img/icon-Google-Analytics.png');?>"
                                                                        alt="Analytics" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Google Analytics');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Trạng thái');?></label>
                                                                        <select class="form-select"
                                                                            name="google_analytics_status">
                                                                            <option
                                                                                <?=$CMSNT->site('google_analytics_status') == 1 ? 'selected' : '';?>
                                                                                value="1">
                                                                                <?=__('Bật');?>
                                                                            </option>
                                                                            <option
                                                                                <?=$CMSNT->site('google_analytics_status') == 0 ? 'selected' : '';?>
                                                                                value="0">
                                                                                <?=__('Tắt');?>
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Measurement ID');?></label>
                                                                        <input type="text" name="google_analytics_id"
                                                                            placeholder="VD: G-XXXXXXXX"
                                                                            value="<?=$CMSNT->site('google_analytics_id');?>"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Google Ads -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-warning-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('mod/img/icon-google-ads.webp');?>"
                                                                        alt="Ads" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Google Ads');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <div class="row">
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Trạng thái');?></label>
                                                                        <select class="form-select"
                                                                            name="google_ads_status">
                                                                            <option
                                                                                <?=$CMSNT->site('google_ads_status') == 1 ? 'selected' : '';?>
                                                                                value="1">
                                                                                <?=__('Bật');?>
                                                                            </option>
                                                                            <option
                                                                                <?=$CMSNT->site('google_ads_status') == 0 ? 'selected' : '';?>
                                                                                value="0">
                                                                                <?=__('Tắt');?>
                                                                            </option>
                                                                        </select>
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Google Ads ID');?></label>
                                                                        <input type="text" name="google_ads_id"
                                                                            placeholder="VD: AW-1234567890"
                                                                            value="<?=$CMSNT->site('google_ads_id');?>"
                                                                            class="form-control">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Google Login -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-danger-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('assets/img/icon-google-login.png');?>"
                                                                        alt="Google Login" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Đăng nhập Google');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Status -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium"><?=__('Trạng thái');?></label>
                                                                <select class="form-select" name="status_google_login">
                                                                    <option
                                                                        <?=$CMSNT->site('status_google_login') == 1 ? 'selected' : '';?>
                                                                        value="1">
                                                                        <?=__('Bật');?>
                                                                    </option>
                                                                    <option
                                                                        <?=$CMSNT->site('status_google_login') == 0 ? 'selected' : '';?>
                                                                        value="0">
                                                                        <?=__('Tắt');?>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Client ID & Secret -->
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Client ID');?></label>
                                                                        <input type="text" name="google_login_client_id"
                                                                            value="<?=$CMSNT->site('google_login_client_id');?>"
                                                                            class="form-control"
                                                                            placeholder="<?=__('Google OAuth Client ID');?>">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium"><?=__('Client Secret');?></label>
                                                                        <input type="password"
                                                                            name="google_login_client_secret"
                                                                            value="<?=$CMSNT->site('google_login_client_secret');?>"
                                                                            class="form-control"
                                                                            placeholder="<?=__('Google OAuth Client Secret');?>">
                                                                    </div>
                                                                </div>
                                                            </div>

                                                            <!-- Redirect URI -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium"><?=__('Authorized redirect URIs');?></label>
                                                                <div class="input-group">
                                                                    <input type="text" readonly
                                                                        value="<?=base_url('api/callback_google_login.php');?>"
                                                                        class="form-control bg-light"
                                                                        id="google-redirect-uri">
                                                                    <button class="btn btn-primary-light" type="button"
                                                                        onclick="copyToClipboard('google-redirect-uri')"
                                                                        title="<?=__('Sao chép URL');?>">
                                                                        <i class="ri-file-copy-line"></i>
                                                                    </button>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Nhập vào Authorized redirect URIs trong Google Cloud Console');?>
                                                                </div>
                                                            </div>
                                                            <!-- Help Link -->
                                                            <div class="alert alert-primary-transparent border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="ri-information-line fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?=__('Cần trợ giúp?');?></strong><br>
                                                                        <a href="https://help.cmsnt.co/huong-dan/smmpanel2-huong-dan-cau-hinh-tinh-nang-dang-nhap-bang-google/"
                                                                            target="_blank"
                                                                            class="text-primary fw-medium">
                                                                            <i
                                                                                class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn chi tiết tích hợp ChatGPT');?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- ChatGPT Configuration -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-info-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div
                                                                    class="avatar avatar-sm bg-white-transparent me-2 rounded">
                                                                    <img src="https://i.imgur.com/5iOyCNW.png"
                                                                        alt="ChatGPT" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Tích hợp ChatGPT');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- API Key -->
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-key-line me-2 text-warning"></i>
                                                                    <?=__('API Key');?>
                                                                </label>
                                                                <input type="password" name="chatgpt_api_key"
                                                                    placeholder="VD: sk-XXXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX"
                                                                    value="<?=$CMSNT->site('chatgpt_api_key');?>"
                                                                    class="form-control">
                                                                <div class="form-text">
                                                                    <i class="ri-shield-keyhole-line me-1"></i>
                                                                    <?=__('Lấy API Key từ');?> <a
                                                                        href="https://platform.openai.com/api-keys"
                                                                        target="_blank"
                                                                        class="text-primary fw-medium">OpenAI
                                                                        Platform</a>
                                                                </div>
                                                            </div>

                                                            <!-- Model Selection -->
                                                            <div class="mb-3">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i class="ri-cpu-line me-2 text-info"></i>
                                                                    <?=__('Chọn Model AI');?>
                                                                </label>
                                                                <select class="form-select js-example-basic-single"
                                                                    name="chatgpt_model">
                                                                    <optgroup
                                                                        label="🔥 <?=__('Khuyến nghị - Mới nhất 2025');?>">
                                                                        <option value="gpt-4o-2024-11-20"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-4o-2024-11-20' ? 'selected' : ''; ?>>
                                                                            🔥 GPT-4o (2024-11-20) - $2.50/$5.00 per 1M
                                                                            tokens
                                                                        </option>
                                                                    </optgroup>

                                                                    <optgroup
                                                                        label="💎 <?=__('GPT-4 Series - Cao cấp');?>">
                                                                        <option value="gpt-4o"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-4o' ? 'selected' : ''; ?>>
                                                                            GPT-4o - $2.50/$5.00 per 1M tokens
                                                                        </option>
                                                                        <option value="gpt-4o-2024-08-06"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-4o-2024-08-06' ? 'selected' : ''; ?>>
                                                                            GPT-4o (Aug 2024) - $2.50/$5.00 per 1M
                                                                            tokens
                                                                        </option>
                                                                        <option value="gpt-4o-mini"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-4o-mini' ? 'selected' : ''; ?>>
                                                                            GPT-4o Mini - $0.15/$0.60 per 1M tokens
                                                                        </option>
                                                                        <option value="gpt-4-turbo"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-4-turbo' ? 'selected' : ''; ?>>
                                                                            GPT-4 Turbo - $10.00/$30.00 per 1M tokens
                                                                            [Legacy]
                                                                        </option>
                                                                    </optgroup>

                                                                    <optgroup
                                                                        label="💡 <?=__('GPT-3.5 Series - Tiết kiệm');?>">
                                                                        <option value="gpt-3.5-turbo"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-3.5-turbo' ? 'selected' : ''; ?>>
                                                                            GPT-3.5 Turbo - $0.50/$1.50 per 1M tokens
                                                                        </option>
                                                                        <option value="gpt-3.5-turbo-0125"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'gpt-3.5-turbo-0125' ? 'selected' : ''; ?>>
                                                                            GPT-3.5 Turbo (0125) - $0.50/$1.50 per 1M
                                                                            tokens
                                                                        </option>
                                                                    </optgroup>

                                                                    <optgroup
                                                                        label="🧠 <?=__('o1 Series - Lý luận phức tạp');?>">
                                                                        <option value="o1"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'o1' ? 'selected' : ''; ?>>
                                                                            o1 - $15.00/$60.00 per 1M tokens
                                                                        </option>
                                                                        <option value="o1-preview"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'o1-preview' ? 'selected' : ''; ?>>
                                                                            o1 Preview - $15.00/$60.00 per 1M tokens
                                                                        </option>
                                                                        <option value="o1-mini"
                                                                            <?= $CMSNT->site('chatgpt_model') == 'o1-mini' ? 'selected' : ''; ?>>
                                                                            o1 Mini - $3.00/$12.00 per 1M tokens
                                                                        </option>
                                                                    </optgroup>
                                                                </select>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('o1 Series dành cho tác vụ phức tạp, GPT-4o cho đa năng, GPT-3.5 cho tiết kiệm chi phí');?>
                                                                </div>
                                                            </div>
                                                            <!-- Help Link -->
                                                            <div class="alert alert-primary-transparent border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="ri-information-line fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?=__('Cần trợ giúp?');?></strong><br>
                                                                        <a href="https://help.cmsnt.co/huong-dan/smmpanel2-huong-dan-cau-hinh-chatgpt-de-su-dung-tinh-nang-ai/"
                                                                            target="_blank"
                                                                            class="text-primary fw-medium">
                                                                            <i
                                                                                class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn chi tiết tích hợp ChatGPT');?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <!-- reCAPTCHA Configuration -->
                                                    <div class="card custom-card border-0 shadow-sm mb-4">
                                                        <div class="card-header bg-danger-gradient border-bottom-0">
                                                            <div
                                                                class="card-title text-white mb-0 d-flex align-items-center">
                                                                <div class="avatar avatar-sm bg-white-transparent me-2">
                                                                    <img src="<?=BASE_URL('assets/img/google_recaptcha.png');?>"
                                                                        alt="reCAPTCHA" class="w-100 h-100">
                                                                </div>
                                                                <span
                                                                    class="fw-semibold fs-15"><?=__('Google reCAPTCHA');?></span>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- reCAPTCHA Status -->
                                                            <div class="mb-4">
                                                                <label
                                                                    class="form-label fw-medium d-flex align-items-center">
                                                                    <i
                                                                        class="ri-shield-check-line me-2 text-success"></i>
                                                                    <?=__('Trạng thái reCAPTCHA');?>
                                                                </label>
                                                                <select class="form-select" name="reCAPTCHA_status">
                                                                    <option
                                                                        <?=$CMSNT->site('reCAPTCHA_status') == 1 ? 'selected' : '';?>
                                                                        value="1">
                                                                        <?=__('Bật - Bảo vệ khỏi bot spam');?>
                                                                    </option>
                                                                    <option
                                                                        <?=$CMSNT->site('reCAPTCHA_status') == 0 ? 'selected' : '';?>
                                                                        value="0">
                                                                        <?=__('Tắt');?>
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Site Key & Secret Key -->
                                                            <div class="row">
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i
                                                                                class="ri-global-line me-2 text-primary"></i>
                                                                            <?=__('Site Key (Public)');?>
                                                                        </label>
                                                                        <input type="text" name="reCAPTCHA_site_key"
                                                                            value="<?=$CMSNT->site('reCAPTCHA_site_key');?>"
                                                                            class="form-control"
                                                                            placeholder="6Lc6BAAAAAAAAChqRbQZcn_yyyyyyyyyyyyyyyyy">
                                                                    </div>
                                                                </div>
                                                                <div class="col-lg-6">
                                                                    <div class="mb-3">
                                                                        <label
                                                                            class="form-label fw-medium d-flex align-items-center">
                                                                            <i class="ri-key-line me-2 text-danger"></i>
                                                                            <?=__('Secret Key (Private)');?>
                                                                        </label>
                                                                        <input type="password"
                                                                            name="reCAPTCHA_secret_key"
                                                                            value="<?=$CMSNT->site('reCAPTCHA_secret_key');?>"
                                                                            class="form-control"
                                                                            placeholder="6Lc6BAAAAAAAARfWwGtnGbOOOOOOOOOOOOOOOOOOO">
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <!-- Help Link -->
                                                            <div class="alert alert-primary-transparent border-0">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="me-2">
                                                                        <i class="ri-information-line fs-16"></i>
                                                                    </div>
                                                                    <div>
                                                                        <strong><?=__('Cần trợ giúp?');?></strong><br>
                                                                        <a href="https://help.cmsnt.co/huong-dan/smmpanel2-huong-dan-cau-hinh-recaptcha/"
                                                                            target="_blank"
                                                                            class="text-primary fw-medium">
                                                                            <i
                                                                                class="ri-external-link-line me-1"></i><?=__('Xem hướng dẫn chi tiết tích hợp reCAPTCHA');?>
                                                                        </a>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <!-- Save Button -->
                                            <div class="row">
                                                <div class="col-12">
                                                    <div class="d-flex justify-content-end">
                                                        <button type="submit" name="SaveSettings"
                                                            class="btn btn-primary btn-lg px-5">
                                                            <i class="ri-save-line me-2"></i>
                                                            <?=__('Lưu cấu hình');?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="telegram-template" role="tabpanel">
                                        <h4>Nội dung thông báo Telegram</h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-12">
                                                    <div class="row">
                                                        <div class="col-md-12 mb-3">
                                                            <div class="alert alert-info">
                                                                <strong><?=__('Lưu ý:');?></strong>
                                                                <?=__('Để mặc định nếu bạn không có nhu cầu tùy chỉnh. Xóa toàn bộ nội dung trong ô nếu không muốn bật thông báo.');?>
                                                            </div>
                                                        </div>

                                                        <!-- Hàng 1: Thông báo đơn hàng -->
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-hand-paper me-2"></i>
                                                                        <?=__('Thông báo đơn hàng thủ công cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_buy_service_manual"
                                                                        data-target="#noti_buy_service_manual"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_buy_service_manual"
                                                                        id="noti_buy_service_manual"
                                                                        placeholder="<?=__('Nhập nội dung thông báo đơn hàng thủ công...');?>"><?=$CMSNT->site('noti_buy_service_manual');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {trans_id},
                                                                        {service}, {link}, {comment}, {quantity}, {pay},
                                                                        {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-robot me-2"></i>
                                                                        <?=__('Thông báo đơn hàng API cho');?> <span
                                                                            class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_buy_service_api"
                                                                        data-target="#noti_buy_service_api"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_buy_service_api"
                                                                        id="noti_buy_service_api"
                                                                        placeholder="<?=__('Nhập nội dung thông báo đơn hàng API...');?>"><?=$CMSNT->site('noti_buy_service_api');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {trans_id},
                                                                        {service}, {supplier}, {link}, {comment},
                                                                        {quantity}, {pay}, {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Hàng 2: Thông báo nạp tiền và hành động -->
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-credit-card me-2"></i>
                                                                        <?=__('Thông báo nạp tiền cho');?> <span
                                                                            class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_recharge"
                                                                        data-target="#noti_recharge"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_recharge" id="noti_recharge"
                                                                        placeholder="<?=__('Nhập nội dung thông báo nạp tiền...');?>"><?=$CMSNT->site('noti_recharge');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {trans_id}, {method},
                                                                        {amount}, {price}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-tasks me-2"></i>
                                                                        <?=__('Thông báo hành động cho');?> <span
                                                                            class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_action"
                                                                        data-target="#noti_action"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_action" id="noti_action"
                                                                        placeholder="<?=__('Nhập nội dung thông báo hành động...');?>"><?=$CMSNT->site('noti_action');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {action}, {ip},
                                                                        {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <!-- Hàng 3: Thông báo rút hoa hồng và đăng nhập -->
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-money-bill-wave me-2"></i>
                                                                        <?=__('Thông báo rút số dư hoa hồng cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_affiliate_withdraw"
                                                                        data-target="#noti_affiliate_withdraw"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_affiliate_withdraw"
                                                                        id="noti_affiliate_withdraw"
                                                                        placeholder="<?=__('Nhập nội dung thông báo rút hoa hồng...');?>"><?=$CMSNT->site('noti_affiliate_withdraw');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {bank},
                                                                        {account_number}, {account_name}, {amount},
                                                                        {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-ticket-alt me-2"></i>
                                                                        <?=__('Thông báo khi có ticket mới cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="support_tickets_telegram_message"
                                                                        data-target="#support_tickets_telegram_message"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="support_tickets_telegram_message"
                                                                        id="support_tickets_telegram_message"
                                                                        placeholder="<?=__('Nhập nội dung thông báo khi có ticket mới...');?>"><?=$CMSNT->site('support_tickets_telegram_message');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {subject},
                                                                        {content}, {status}, {category}, {quantity}
                                                                        {ip}, {time}, {device}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-reply me-2"></i>
                                                                        <?=__('Thông báo khi User trả lời ticket cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="support_tickets_telegram_message_reply"
                                                                        data-target="#support_tickets_telegram_message_reply"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="support_tickets_telegram_message_reply"
                                                                        id="support_tickets_telegram_message_reply"
                                                                        placeholder="<?=__('Nhập nội dung thông báo khi User trả lời ticket...');?>"><?=$CMSNT->site('support_tickets_telegram_message_reply');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {subject}, {message},
                                                                        {category}, {ip}, {time}, {device}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-child me-2"></i>
                                                                        <?=__('Thông báo tạo khi có yêu cầu tạo Child Panel cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_child_panel_create"
                                                                        data-target="#noti_child_panel_create"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_child_panel_create"
                                                                        id="noti_child_panel_create"
                                                                        placeholder="<?=__('Nhập nội dung thông báo tạo Child Panel...');?>"><?=$CMSNT->site('noti_child_panel_create');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {api_key}, {email},
                                                                        {phone}, {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-refresh me-2"></i>
                                                                        <?=__('Thông báo gia hạn khi có yêu cầu gia hạn Child Panel cho');?>
                                                                        <span class="text-danger">Admin</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_child_panel_renewal"
                                                                        data-target="#noti_child_panel_renewal"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_child_panel_renewal"
                                                                        id="noti_child_panel_renewal"
                                                                        placeholder="<?=__('Nhập nội dung thông báo gia hạn Child Panel...');?>"><?=$CMSNT->site('noti_child_panel_renewal');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {email}, {phone},
                                                                        {renewal_period}, {renewal_cost},
                                                                        {new_expired_at}, {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-child me-2"></i>
                                                                        <?=__('Thông báo khi Child Panel hết hạn cho');?>
                                                                        <span class="text-info">User</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_user_child_panel_expired"
                                                                        data-target="#noti_user_child_panel_expired"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_user_child_panel_expired"
                                                                        id="noti_user_child_panel_expired"
                                                                        placeholder="<?=__('Nhập nội dung thông báo khi Child Panel hết hạn...');?>"><?=$CMSNT->site('noti_user_child_panel_expired');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {email_user},
                                                                        {phone_user}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-sign-in-alt me-2"></i>
                                                                        <?=__('Thông báo đăng nhập cho');?> <span
                                                                            class="text-info">User</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="telegram_noti_login_user"
                                                                        data-target="#telegram_noti_login_user"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="telegram_noti_login_user"
                                                                        id="telegram_noti_login_user"
                                                                        placeholder="<?=__('Nhập nội dung thông báo đăng nhập...');?>"><?=$CMSNT->site('telegram_noti_login_user');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {ip}, {device},
                                                                        {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-cart-plus me-2"></i>
                                                                        <?=__('Thông báo đơn hàng cho');?> <span
                                                                            class="text-info">User</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_buy_service_to_user"
                                                                        data-target="#noti_buy_service_to_user"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_buy_service_to_user"
                                                                        id="noti_buy_service_to_user"
                                                                        placeholder="<?=__('Nhập nội dung thông báo đơn hàng...');?>"><?=$CMSNT->site('noti_buy_service_to_user');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {domain}, {username}, {trans_id},
                                                                        {service}, {link}, {comment}, {quantity},
                                                                        {pay}, {ip}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <div class="card mb-3">
                                                                <div
                                                                    class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                                                                    <h6 class="mb-0">
                                                                        <i class="fa fa-reply me-2"></i>
                                                                        <?=__('Thông báo khi Admin reply ticket cho');?>
                                                                        <span class="text-info">User</span>
                                                                    </h6>
                                                                    <button type="button"
                                                                        class="btn btn-sm btn-dark btn-wave text-white btn-ai-telegram"
                                                                        data-type="noti_user_admin_reply_ticket"
                                                                        data-target="#noti_user_admin_reply_ticket"
                                                                        title="Tạo nội dung bằng AI">
                                                                        <i
                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                    </button>
                                                                </div>
                                                                <div class="card-body">
                                                                    <textarea class="form-control mb-2" rows="4"
                                                                        name="noti_user_admin_reply_ticket"
                                                                        id="noti_user_admin_reply_ticket"
                                                                        placeholder="<?=__('Nhập nội dung thông báo khi Admin reply ticket...');?>"><?=$CMSNT->site('noti_user_admin_reply_ticket');?></textarea>
                                                                    <small class="text-muted">
                                                                        <strong><?=__('Biến sử dụng:');?></strong>
                                                                        {username}, {subject}, {message}, {time}
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>




                                                    </div>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings"
                                                class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> <?=__('Save');?>
                                            </button>
                                        </form>
                                    </div>
                                    <div class="tab-pane text-muted" id="mail-template" role="tabpanel">
                                        <h4><?=__('Nội dung thông báo Mail');?></h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-12">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <thead class="table-primary">
                                                            <tr>
                                                                <th colspan="2" class="text-center">
                                                                    <?=__('Để mặc định nếu bạn không có nhu cầu tùy chỉnh');?><br>
                                                                    <small><?=__('Xóa toàn bộ nội dung trong ô Subject nếu không muốn bật thông báo');?></small>
                                                                </th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <!-- <tr>
                                                                <td colspan="2">
                                                                    <div class="card border border-primary-transparent">
                                                                        <div class="card-header">
                                                                            <h6
                                                                                class="card-title mb-0 text-uppercase text-dark">
                                                                                <i class="ri-mail-line me-2"></i>
                                                                                <?=__('Thông báo Admin khi User tạo ticket');?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-semibold">
                                                                                    <i
                                                                                        class="ri-text me-1"></i><?=__('Subject');?>
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    name="email_temp_subject_warning_ticket"
                                                                                    placeholder="<?=__('Nhập tiêu đề email...');?>"
                                                                                    value="<?=$CMSNT->site('email_temp_subject_warning_ticket');?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                                    <label
                                                                                        class="form-label fw-semibold mb-0">
                                                                                        <i
                                                                                            class="ri-file-text-line me-1"></i><?=__('Nội dung Email');?>
                                                                                    </label>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-dark btn-ai-email"
                                                                                        data-type="email_temp_content_warning_ticket"
                                                                                        data-target="#email_temp_content_warning_ticket"
                                                                                        title="Tạo nội dung bằng AI">
                                                                                        <i
                                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                                    </button>
                                                                                </div>
                                                                                <textarea class="form-control"
                                                                                    id="email_temp_content_warning_ticket"
                                                                                    rows="8"
                                                                                    name="email_temp_content_warning_ticket"
                                                                                    placeholder="<?=__('Nhập nội dung email hoặc click Generated by AI...');?>"><?=$CMSNT->site('email_temp_content_warning_ticket');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="accordion accordion-customicon1 accordion-primary">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header">
                                                                                <button
                                                                                    class="accordion-button collapsed"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#email_temp_content_warning_ticket"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="email_temp_content_warning_ticket">
                                                                                    <?=__('Văn bản thay thế');?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="email_temp_content_warning_ticket"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <li><b>{domain}</b> =>
                                                                                            <?=__('Link Website');?>.
                                                                                        </li>
                                                                                        <li><b>{title}</b> =>
                                                                                            <?=__('Tên website');?>.
                                                                                        </li>
                                                                                        <li><b>{username}</b> =>
                                                                                            <?=__('Tên khách hàng');?>.
                                                                                        </li>
                                                                                        <li><b>{ip}</b> =>
                                                                                            <?=__('Địa chỉ IP');?>.</li>
                                                                                        <li><b>{device}</b> =>
                                                                                            <?=__('Thiết bị');?>.</li>
                                                                                        <li><b>{time}</b> =>
                                                                                            <?=__('Thời gian');?>.</li>
                                                                                        <li><b>{subject}</b> =>
                                                                                            <?=__('Tiêu đề ticket');?>.
                                                                                        </li>
                                                                                        <li><b>{category}</b> =>
                                                                                            <?=__('Chủ đề ticket');?>.
                                                                                        </li>
                                                                                        <li><b>{order_id}</b> =>
                                                                                            <?=__('Mã đơn hàng');?>.
                                                                                        </li>
                                                                                        <li><b>{content}</b> =>
                                                                                            <?=__('Nội dung ticket');?>.
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr> -->
                                                            <!-- <tr>
                                                                <td colspan="2">
                                                                    <div class="card border border-success-transparent">
                                                                        <div class="card-header bg-success-transparent">
                                                                            <h6
                                                                                class="card-title mb-0 text-uppercase text-dark">
                                                                                <i class="ri-reply-line me-2"></i>
                                                                                <?=__('Thông báo Admin khi User trả lời ticket');?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-semibold">
                                                                                    <i
                                                                                        class="ri-text me-1"></i><?=__('Subject');?>
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    name="email_temp_subject_reply_ticket"
                                                                                    placeholder="<?=__('Nhập tiêu đề email...');?>"
                                                                                    value="<?=$CMSNT->site('email_temp_subject_reply_ticket');?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                                    <label
                                                                                        class="form-label fw-semibold mb-0">
                                                                                        <i
                                                                                            class="ri-file-text-line me-1"></i><?=__('Nội dung Email');?>
                                                                                    </label>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-dark btn-ai-email"
                                                                                        data-type="email_temp_content_reply_ticket"
                                                                                        data-target="#email_temp_content_reply_ticket"
                                                                                        title="Tạo nội dung bằng AI">
                                                                                        <i
                                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                                    </button>
                                                                                </div>
                                                                                <textarea class="form-control"
                                                                                    id="email_temp_content_reply_ticket"
                                                                                    rows="8"
                                                                                    name="email_temp_content_reply_ticket"
                                                                                    placeholder="<?=__('Nhập nội dung email hoặc click Generated by AI...');?>"><?=$CMSNT->site('email_temp_content_reply_ticket');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="accordion accordion-customicon1 accordion-primary">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header">
                                                                                <button
                                                                                    class="accordion-button collapsed"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#email_temp_content_reply_ticket"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="email_temp_content_reply_ticket">
                                                                                    <?=__('Văn bản thay thế');?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="email_temp_content_reply_ticket"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <li><b>{domain}</b> =>
                                                                                            <?=__('Link Website');?>.
                                                                                        </li>
                                                                                        <li><b>{title}</b> =>
                                                                                            <?=__('Tên website');?>.
                                                                                        </li>
                                                                                        <li><b>{username}</b> =>
                                                                                            <?=__('Tên khách hàng');?>.
                                                                                        </li>
                                                                                        <li><b>{ip}</b> =>
                                                                                            <?=__('Địa chỉ IP');?>.</li>
                                                                                        <li><b>{device}</b> =>
                                                                                            <?=__('Thiết bị');?>.</li>
                                                                                        <li><b>{time}</b> =>
                                                                                            <?=__('Thời gian');?>.</li>
                                                                                        <li><b>{subject}</b> =>
                                                                                            <?=__('Tiêu đề ticket');?>.
                                                                                        </li>
                                                                                        <li><b>{category}</b> =>
                                                                                            <?=__('Chủ đề ticket');?>.
                                                                                        </li>
                                                                                        <li><b>{order_id}</b> =>
                                                                                            <?=__('Mã đơn hàng');?>.
                                                                                        </li>
                                                                                        <li><b>{content}</b> =>
                                                                                            <?=__('Nội dung ticket');?>.
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr> -->


                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="card border border-warning-transparent">
                                                                        <div class="card-header bg-warning-transparent">
                                                                            <h6
                                                                                class="card-title mb-0 text-uppercase text-dark">
                                                                                <i
                                                                                    class="ri-shield-check-line me-2"></i>
                                                                                <?=__('Thông báo đăng nhập');?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-semibold">
                                                                                    <i
                                                                                        class="ri-text me-1"></i><?=__('Subject');?>
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    name="email_temp_subject_warning_login"
                                                                                    placeholder="Nhập tiêu đề email..."
                                                                                    value="<?=$CMSNT->site('email_temp_subject_warning_login');?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                                    <label
                                                                                        class="form-label fw-semibold mb-0">
                                                                                        <i
                                                                                            class="ri-file-text-line me-1"></i><?=__('Nội dung Email');?>
                                                                                    </label>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-dark btn-ai-email"
                                                                                        data-type="email_temp_content_warning_login"
                                                                                        data-target="#email_temp_content_warning_login"
                                                                                        title="Tạo nội dung bằng AI">
                                                                                        <i
                                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                                    </button>
                                                                                </div>
                                                                                <textarea class="form-control"
                                                                                    id="email_temp_content_warning_login"
                                                                                    rows="8"
                                                                                    name="email_temp_content_warning_login"
                                                                                    placeholder="Nhập nội dung email hoặc click Generated by AI..."><?=$CMSNT->site('email_temp_content_warning_login');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="accordion accordion-customicon1 accordion-primary">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header">
                                                                                <button
                                                                                    class="accordion-button collapsed"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#email_temp_content_warning_login"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="email_temp_content_warning_login">
                                                                                    Văn bản thay thế
                                                                                </button>
                                                                            </h2>
                                                                            <div id="email_temp_content_warning_login"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <li><b>{domain}</b> => Link
                                                                                            Website.</li>
                                                                                        <li><b>{title}</b> => Tên
                                                                                            website.</li>
                                                                                        <li><b>{username}</b> => Tên
                                                                                            khách hàng.</li>
                                                                                        <li><b>{ip}</b> => Địa chỉ IP.
                                                                                        </li>
                                                                                        <li><b>{device}</b> => Thiết bị.
                                                                                        </li>
                                                                                        <li><b>{time}</b> => Thời gian.
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="card border border-info-transparent">
                                                                        <div class="card-header bg-info-transparent">
                                                                            <h6
                                                                                class="card-title mb-0 text-uppercase text-dark">
                                                                                <i class="ri-key-2-line me-2"></i>
                                                                                <?=__('Gửi OTP xác minh đăng nhập');?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-semibold">
                                                                                    <i class="ri-text me-1"></i>Subject
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    name="email_temp_subject_otp_mail"
                                                                                    placeholder="Nhập tiêu đề email..."
                                                                                    value="<?=$CMSNT->site('email_temp_subject_otp_mail');?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                                    <label
                                                                                        class="form-label fw-semibold mb-0">
                                                                                        <i
                                                                                            class="ri-file-text-line me-1"></i><?=__('Nội dung Email');?>
                                                                                    </label>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-dark btn-ai-email"
                                                                                        data-type="email_temp_content_otp_mail"
                                                                                        data-target="#email_temp_content_otp_mail"
                                                                                        title="Tạo nội dung bằng AI">
                                                                                        <i
                                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                                    </button>
                                                                                </div>
                                                                                <textarea class="form-control" rows="8"
                                                                                    id="email_temp_content_otp_mail"
                                                                                    name="email_temp_content_otp_mail"
                                                                                    placeholder="<?=__('Nhập nội dung email hoặc click Generated by AI...');?>"><?=$CMSNT->site('email_temp_content_otp_mail');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="accordion accordion-customicon1 accordion-primary">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header">
                                                                                <button
                                                                                    class="accordion-button collapsed"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#email_temp_content_otp_mail"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="email_temp_content_otp_mail">
                                                                                    <?=__('Văn bản thay thế');?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="email_temp_content_otp_mail"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <li><b>{domain}</b> => Link
                                                                                            Website.</li>
                                                                                        <li><b>{title}</b> => Tên
                                                                                            website.</li>
                                                                                        <li><b>{username}</b> => Tên
                                                                                            khách hàng.</li>
                                                                                        <li><b>{otp}</b> => Mã OTP.
                                                                                        </li>
                                                                                        <li><b>{ip}</b> => Địa chỉ IP.
                                                                                        </li>
                                                                                        <li><b>{device}</b> => Thiết bị.
                                                                                        </li>
                                                                                        <li><b>{time}</b> => Thời gian.
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <div class="card border border-danger-transparent">
                                                                        <div class="card-header bg-danger-transparent">
                                                                            <h6
                                                                                class="card-title mb-0 text-uppercase text-dark">
                                                                                <i class="ri-lock-unlock-line me-2"></i>
                                                                                <?=__('Khôi phục mật khẩu');?>
                                                                            </h6>
                                                                        </div>
                                                                        <div class="card-body">
                                                                            <div class="mb-3">
                                                                                <label class="form-label fw-semibold">
                                                                                    <i class="ri-text me-1"></i>Subject
                                                                                </label>
                                                                                <input class="form-control"
                                                                                    name="email_temp_subject_forgot_password"
                                                                                    placeholder="Nhập tiêu đề email..."
                                                                                    value="<?=$CMSNT->site('email_temp_subject_forgot_password');?>">
                                                                            </div>
                                                                            <div class="mb-3">
                                                                                <div
                                                                                    class="d-flex justify-content-between align-items-center mb-2">
                                                                                    <label
                                                                                        class="form-label fw-semibold mb-0">
                                                                                        <i
                                                                                            class="ri-file-text-line me-1"></i><?=__('Nội dung Email');?>
                                                                                    </label>
                                                                                    <button type="button"
                                                                                        class="btn btn-sm btn-dark btn-ai-email"
                                                                                        data-type="email_temp_content_forgot_password"
                                                                                        data-target="#email_temp_content_forgot_password"
                                                                                        title="Tạo nội dung bằng AI">
                                                                                        <i
                                                                                            class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                                                                    </button>
                                                                                </div>
                                                                                <textarea class="form-control" rows="8"
                                                                                    id="email_temp_content_forgot_password"
                                                                                    name="email_temp_content_forgot_password"
                                                                                    placeholder="<?=__('Nhập nội dung email hoặc click Generated by AI...');?>"><?=$CMSNT->site('email_temp_content_forgot_password');?></textarea>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div
                                                                        class="accordion accordion-customicon1 accordion-primary">
                                                                        <div class="accordion-item">
                                                                            <h2 class="accordion-header">
                                                                                <button
                                                                                    class="accordion-button collapsed"
                                                                                    type="button"
                                                                                    data-bs-toggle="collapse"
                                                                                    data-bs-target="#email_temp_content_forgot_password"
                                                                                    aria-expanded="false"
                                                                                    aria-controls="email_temp_content_forgot_password">
                                                                                    <?=__('Văn bản thay thế');?>
                                                                                </button>
                                                                            </h2>
                                                                            <div id="email_temp_content_forgot_password"
                                                                                class="accordion-collapse collapse">
                                                                                <div class="accordion-body">
                                                                                    <ul>
                                                                                        <li><b>{domain}</b> => Link
                                                                                            Website.</li>
                                                                                        <li><b>{title}</b> => Tên
                                                                                            website.</li>
                                                                                        <li><b>{username}</b> => Tên
                                                                                            khách hàng.</li>
                                                                                        <li><b>{link}</b> => Link xác
                                                                                            minh.
                                                                                        </li>
                                                                                        <li><b>{ip}</b> => Địa chỉ IP.
                                                                                        </li>
                                                                                        <li><b>{device}</b> => Thiết bị.
                                                                                        </li>
                                                                                        <li><b>{time}</b> => Thời gian.
                                                                                        </li>
                                                                                    </ul>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings"
                                                class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> <?=__('Save');?>
                                            </button>
                                        </form>
                                    </div>

                                    <div class="tab-pane text-muted" id="security" role="tabpanel">
                                        <div class="d-flex align-items-center mb-4">
                                            <div class="flex-shrink-0">
                                                <div class="avatar avatar-md bg-primary-transparent rounded-circle">
                                                    <i class="ri-shield-check-line fs-18 text-primary"></i>
                                                </div>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <h4 class="mb-1"><?=__('Cài đặt bảo mật hệ thống');?></h4>
                                                <p class="text-muted mb-0">
                                                    <?=__('Cấu hình các tính năng bảo mật để bảo vệ hệ thống khỏi các cuộc tấn công');?>
                                                </p>
                                            </div>
                                        </div>
                                        <form action="" method="POST">
                                            <div class="row">
                                                <!-- Card 1: Bảo vệ chống Brute Force -->
                                                <div class="col-xl-6">
                                                    <div class="card border border-danger-subtle h-100">
                                                        <div class="card-header bg-danger-subtle">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    <i
                                                                        class="ri-shield-cross-line fs-18 text-danger"></i>
                                                                </div>
                                                                <div class="flex-grow-1 ms-2">
                                                                    <h6 class="card-title mb-0 fw-semibold text-danger">
                                                                        <?=__('Bảo vệ chống Brute Force');?>
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        <?=__('Ngăn chặn tấn công scan tài khoản');?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Block IP Login -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-lock-line me-1 text-primary"></i>
                                                                    <?=__('Khóa IP nếu đăng nhập sai mật khẩu quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_login');?>"
                                                                        name="limit_block_ip_login" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Khuyến nghị: ≤ 5 lần để bảo mật tốt nhất');?>
                                                                </div>
                                                            </div>

                                                            <!-- Block Client Account -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i
                                                                        class="ri-user-forbid-line me-1 text-warning"></i>
                                                                    <?=__('Khóa tài khoản nếu đăng nhập sai mật khẩu quá nhiều lần');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_client_login');?>"
                                                                        name="limit_block_client_login" min="1"
                                                                        max="50">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Khuyến nghị: ≤ 10 lần để cân bằng bảo mật và trải nghiệm');?>
                                                                </div>
                                                            </div>

                                                            <!-- Block API -->
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-key-line me-1 text-info"></i>
                                                                    <?=__('Khóa IP nếu API KEY sai quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_api');?>"
                                                                        name="limit_block_ip_api" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Khuyến nghị: ≤ 20 lần để bảo vệ API');?>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i
                                                                        class=" ri-shield-keyhole-fill me-1 text-info"></i>
                                                                    <?=__('Khóa IP nếu nhập sai 2FA quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_2fa');?>"
                                                                        name="limit_block_ip_2fa" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Khuyến nghị: ≤ 10 lần');?>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-shield-flash-line me-1 text-info"></i>
                                                                    <?=__('Khóa IP nếu nhập sai OTP quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_otp');?>"
                                                                        name="limit_block_ip_otp" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Khuyến nghị: ≤ 10 lần');?>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-shield-user-line me-1 text-info"></i>
                                                                    <?=__('Khóa IP nếu tạo hóa đơn nạp tiền quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_payment');?>"
                                                                        name="limit_block_ip_payment" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-shield-user-line me-1 text-info"></i>
                                                                    <?=__('Khóa IP nếu yêu cầu khôi phục mật khẩu quá nhiều lần trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_reset_password');?>"
                                                                        name="limit_block_ip_reset_password" min="1">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Card 2: Kiểm soát truy cập -->
                                                <div class="col-xl-6">
                                                    <div class="card border border-primary-subtle h-100">
                                                        <div class="card-header bg-primary-subtle">
                                                            <div class="d-flex align-items-center">
                                                                <div class="flex-shrink-0">
                                                                    <i
                                                                        class="ri-shield-check-line fs-18 text-primary"></i>
                                                                </div>
                                                                <div class="flex-grow-1 ms-2">
                                                                    <h6
                                                                        class="card-title mb-0 fw-semibold text-primary">
                                                                        <?=__('Kiểm soát truy cập');?>
                                                                    </h6>
                                                                    <small class="text-muted">
                                                                        <?=__('Giới hạn số thiết bị và IP đăng nhập');?>
                                                                    </small>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        <div class="card-body">
                                                            <!-- Admin Access Attempts -->
                                                            <div class="mb-4">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-admin-line me-1 text-danger"></i>
                                                                    <?=__('Khóa IP truy cập trái phép Admin Panel trong 15 phút');?>
                                                                </label>
                                                                <div class="input-group">
                                                                    <input type="number" class="form-control"
                                                                        value="<?=$CMSNT->site('limit_block_ip_admin_access');?>"
                                                                        name="limit_block_ip_admin_access" min="1"
                                                                        max="20">
                                                                    <span
                                                                        class="input-group-text"><?=__('lần');?></span>
                                                                </div>
                                                                <div class="form-text">
                                                                    <i class="ri-information-line me-1"></i>
                                                                    <?=__('Số lần truy cập sai URL admin trước khi block IP');?>
                                                                </div>
                                                            </div>

                                                            <!-- Single IP Admin -->
                                                            <div class="mb-4">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-global-line me-1 text-success"></i>
                                                                    <?=__('Chỉ cho phép Admin đăng nhập từ 1 IP');?>
                                                                </label>
                                                                <select class="form-select"
                                                                    name="status_only_ip_login_admin" required>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('status_only_ip_login_admin') == 0 ? 'selected' : '';?>>
                                                                        <i class="ri-close-line"></i> <?=__('Tắt');?>
                                                                        (<?=__('Cho phép nhiều IP');?>)
                                                                    </option>
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('status_only_ip_login_admin') == 1 ? 'selected' : '';?>>
                                                                        <i class="ri-check-line"></i> <?=__('Bật');?>
                                                                        (<?=__('Chỉ 1 IP duy nhất');?>)
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Single Device Admin -->
                                                            <div class="mb-4">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-computer-line me-1 text-warning"></i>
                                                                    <?=__('Chỉ cho phép Admin đăng nhập từ 1 thiết bị');?>
                                                                </label>
                                                                <select class="form-select"
                                                                    name="status_only_device_admin" required>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('status_only_device_admin') == 0 ? 'selected' : '';?>>
                                                                        <?=__('Tắt');?>
                                                                        (<?=__('Cho phép nhiều thiết bị');?>)
                                                                    </option>
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('status_only_device_admin') == 1 ? 'selected' : '';?>>
                                                                        <?=__('Bật');?>
                                                                        (<?=__('Chỉ 1 thiết bị duy nhất');?>)
                                                                    </option>
                                                                </select>
                                                            </div>

                                                            <!-- Single Device Client -->
                                                            <div class="mb-0">
                                                                <label class="form-label fw-medium">
                                                                    <i class="ri-smartphone-line me-1 text-info"></i>
                                                                    <?=__('Chỉ cho phép Client đăng nhập từ 1 thiết bị');?>
                                                                </label>
                                                                <select class="form-select"
                                                                    name="status_only_device_client" required>
                                                                    <option value="0"
                                                                        <?=$CMSNT->site('status_only_device_client') == 0 ? 'selected' : '';?>>
                                                                        <?=__('Tắt');?>
                                                                        (<?=__('Cho phép nhiều thiết bị');?>)
                                                                    </option>
                                                                    <option value="1"
                                                                        <?=$CMSNT->site('status_only_device_client') == 1 ? 'selected' : '';?>>
                                                                        <?=__('Bật');?>
                                                                        (<?=__('Chỉ 1 thiết bị duy nhất');?>)
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card 3: Admin Panel URL Security -->
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="card border border-warning-subtle">
                                                            <div class="card-header bg-warning-subtle">
                                                                <div class="d-flex align-items-center">
                                                                    <div class="flex-shrink-0">
                                                                        <i
                                                                            class="ri-shield-check-line fs-18 text-warning"></i>
                                                                    </div>
                                                                    <div class="flex-grow-1 ms-2">
                                                                        <h6
                                                                            class="card-title mb-0 fw-semibold text-warning">
                                                                            <?=__('Bảo mật khác');?>
                                                                        </h6>
                                                                        <small class="text-muted">
                                                                            <?=__('Một số cấu hình bảo mật khác');?>
                                                                        </small>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="card-body">
                                                                <div class="row">
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-medium">
                                                                                <i
                                                                                    class="ri-admin-line me-1 text-primary"></i>
                                                                                <?=__('Đường dẫn Admin Panel');?>
                                                                            </label>
                                                                            <div class="input-group">
                                                                                <span
                                                                                    class="input-group-text"><?=base_url('?module=');?></span>
                                                                                <input type="text" class="form-control"
                                                                                    name="path_admin"
                                                                                    value="<?=$CMSNT->site('path_admin');?>"
                                                                                    placeholder="adcp" required>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-medium">
                                                                                <i
                                                                                    class="ri-admin-line me-1 text-primary"></i>
                                                                                <?=__('ON/OFF Hiển thị nút truy cập Admin Panel');?>
                                                                            </label>
                                                                            <div class="input-group">
                                                                                <select class="form-select"
                                                                                    name="status_show_button_admin_panel"
                                                                                    required>
                                                                                    <option value="0"
                                                                                        <?=$CMSNT->site('status_show_button_admin_panel') == 0 ? 'selected' : '';?>>
                                                                                        <?=__('Tắt');?>
                                                                                    </option>
                                                                                    <option value="1"
                                                                                        <?=$CMSNT->site('status_show_button_admin_panel') == 1 ? 'selected' : '';?>>
                                                                                        <?=__('Bật');?>
                                                                                    </option>
                                                                                </select>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-medium">
                                                                                <i
                                                                                    class="ri-admin-line me-1 text-primary"></i>
                                                                                <?=__('Số lượng tài khoản có thể đăng ký tối đa của 1 IP');?>
                                                                            </label>
                                                                            <input name="max_register_ip" type="text"
                                                                                class="form-control"
                                                                                value="<?=$CMSNT->site('max_register_ip');?>"
                                                                                required>
                                                                            <div class="form-text">
                                                                                <i class="ri-information-line me-1"></i>
                                                                                <?=__('1 IP chỉ được phép đăng ký tối đa');?>
                                                                                <strong><?=$CMSNT->site('max_register_ip');?></strong>
                                                                                <?=__('tài khoản');?>.
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-medium">
                                                                                <i
                                                                                    class="ri-time-line me-1 text-primary"></i>
                                                                                <?=__('Thời gian lưu đăng nhập');?>
                                                                            </label>
                                                                            <div class="input-group">
                                                                                <input name="session_login"
                                                                                    type="number" class="form-control"
                                                                                    value="<?=$CMSNT->site('session_login');?>"
                                                                                    placeholder="<?=__('Nhập thời gian...');?>"
                                                                                    required>
                                                                                <span class="input-group-text">
                                                                                    <i class="ri-time-line me-1"></i>
                                                                                    <?=__('giây');?>
                                                                                </span>
                                                                            </div>
                                                                            <div class="form-text">
                                                                                <i class="ri-information-line me-1"></i>
                                                                                <?=__('VD: 86400 = 24 giờ, 3600 = 1 giờ');?>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <div class="mb-3">
                                                                            <label class="form-label fw-medium">
                                                                                <i
                                                                                    class="ri-key-line me-1 text-primary"></i>
                                                                                <?=__('Mã bí mật Cron Job');?>
                                                                            </label>
                                                                            <div class="input-group">
                                                                                <input name="key_cron_job" type="text"
                                                                                    class="form-control"
                                                                                    value="<?=$CMSNT->site('key_cron_job');?>"
                                                                                    required readonly>
                                                                                <button type="button"
                                                                                    class="btn btn-primary"
                                                                                    onclick="generateKeyCronJob()">
                                                                                    <i
                                                                                        class="ri-refresh-line me-1"></i><?=__('Tạo mới');?>
                                                                                </button>
                                                                            </div>
                                                                            <div class="form-text">
                                                                                <i class="ri-information-line me-1"></i>
                                                                                <?=__('Mã bí mật Cron Job sẽ được sử dụng để xác thực yêu cầu từ Cron Job, tránh spam cron job từ bên ngoài.');?>
                                                                            </div>

                                                                        </div>
                                                                    </div>

                                                                    <script>
                                                                    /**
                                                                     * Hàm tạo random key 16 ký tự cho Cron Job
                                                                     * Sử dụng các ký tự alphanumeric (a-z, A-Z, 0-9) để đảm bảo tính bảo mật
                                                                     */
                                                                    function generateKeyCronJob() {
                                                                        // Xác nhận từ user trước khi thay đổi
                                                                        cuteAlert({
                                                                            type: "question",
                                                                            title: "<?=__('Xác nhận thay đổi');?>",
                                                                            message: "<?=__('Bạn có chắc chắn muốn tạo mã bí mật Cron Job mới?');?>",
                                                                            confirmText: "<?=__('Đồng ý');?>",
                                                                            cancelText: "<?=__('Hủy');?>"
                                                                        }).then((confirmed) => {
                                                                            if (confirmed) {
                                                                                // Tạo random key 16 ký tự
                                                                                const characters =
                                                                                    'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
                                                                                let randomKey = '';

                                                                                // Tạo 16 ký tự random
                                                                                for (let i = 0; i < 16; i++) {
                                                                                    randomKey += characters
                                                                                        .charAt(Math.floor(Math
                                                                                            .random() *
                                                                                            characters
                                                                                            .length));
                                                                                }

                                                                                // Cập nhật giá trị vào input field
                                                                                const keyInput = document
                                                                                    .querySelector(
                                                                                        'input[name="key_cron_job"]'
                                                                                    );
                                                                                if (keyInput) {
                                                                                    keyInput.value = randomKey;

                                                                                    // Hiệu ứng highlight để user biết đã thay đổi
                                                                                    keyInput.style
                                                                                        .backgroundColor =
                                                                                        '#fff3cd';
                                                                                    keyInput.style.borderColor =
                                                                                        '#ffc107';

                                                                                    // Reset highlight sau 2 giây
                                                                                    setTimeout(() => {
                                                                                        keyInput.style
                                                                                            .backgroundColor =
                                                                                            '';
                                                                                        keyInput.style
                                                                                            .borderColor =
                                                                                            '';
                                                                                    }, 2000);

                                                                                    // Hiển thị thông báo thành công
                                                                                    cuteAlert({
                                                                                        type: "success",
                                                                                        title: "<?=__('Thành công!');?>",
                                                                                        message: "<?=__('Đã tạo mã bí mật Cron Job mới. Vui lòng lưu cài đặt để áp dụng thay đổi.');?>",
                                                                                        confirmText: "<?=__('Đóng');?>"
                                                                                    });
                                                                                } else {
                                                                                    // Lỗi không tìm thấy input field
                                                                                    cuteAlert({
                                                                                        type: "error",
                                                                                        title: "<?=__('Lỗi!');?>",
                                                                                        message: "<?=__('Không tìm thấy trường nhập mã bí mật.');?>",
                                                                                        confirmText: "<?=__('Đóng');?>"
                                                                                    });
                                                                                }
                                                                            }
                                                                        });
                                                                    }
                                                                    </script>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Save Button -->
                                                <div class="row mt-4">
                                                    <div class="col-12">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <div class="text-muted">
                                                                <i class="ri-information-line me-1"></i>
                                                                <?=__('Lưu ý: Những thay đổi này sẽ ảnh hưởng đến bảo mật toàn hệ thống');?>
                                                            </div>
                                                            <button type="submit" name="SaveSettings"
                                                                class="btn btn-danger btn-label">
                                                                <i
                                                                    class="ri-save-line label-icon align-middle fs-16 me-2"></i>
                                                                <?=__('Lưu cài đặt bảo mật');?>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>


                                    <div class="tab-pane text-muted" id="widget" role="tabpanel">
                                        <h4><?=__('Tùy chỉnh Widget');?></h4>
                                        <form action="" method="POST">
                                            <div class="row push mb-3">
                                                <div class="col-md-6">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <select class="form-control mb-1"
                                                                        name="widget_zalo1_status">
                                                                        <option
                                                                            <?=$CMSNT->site('widget_zalo1_status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('widget_zalo1_status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <img src="<?=base_url('assets/img/demo-widget-zalo1.png');?>"
                                                                        width="500px">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Số điện thoại
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        value="<?=$CMSNT->site('widget_zalo1_sdt');?>"
                                                                        name="widget_zalo1_sdt">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <select class="form-control mb-1"
                                                                        name="widget_fbzalo2_status">
                                                                        <option
                                                                            <?=$CMSNT->site('widget_fbzalo2_status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('widget_fbzalo2_status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <img src="<?=base_url('assets/img/demo-widget-fbzalo2.png');?>"
                                                                        width="200px">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Link Zalo
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        value="<?=$CMSNT->site('widget_fbzalo2_zalo');?>"
                                                                        name="widget_fbzalo2_zalo">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Link Facebook
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        value="<?=$CMSNT->site('widget_fbzalo2_fb');?>"
                                                                        name="widget_fbzalo2_fb">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <div class="col-md-6">
                                                    <table class="mb-3 table table-bordered table-striped table-hover">
                                                        <tbody>
                                                            <tr>
                                                                <td colspan="2">
                                                                    <select class="form-control mb-1"
                                                                        name="widget_phone1_status">
                                                                        <option
                                                                            <?=$CMSNT->site('widget_phone1_status') == 1 ? 'selected' : '';?>
                                                                            value="1">ON
                                                                        </option>
                                                                        <option
                                                                            <?=$CMSNT->site('widget_phone1_status') == 0 ? 'selected' : '';?>
                                                                            value="0">
                                                                            OFF
                                                                        </option>
                                                                    </select>
                                                                    <img src="<?=base_url('assets/img/demo-widget-phone1.png');?>"
                                                                        width="500px">
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <td>Số điện thoại
                                                                </td>
                                                                <td>
                                                                    <input type="text" class="form-control"
                                                                        value="<?=$CMSNT->site('widget_phone1_sdt');?>"
                                                                        name="widget_phone1_sdt">
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <button type="submit" name="SaveSettings"
                                                class="btn btn-primary w-100 mb-3">
                                                <i class="fa fa-fw fa-save me-1"></i> <?=__('Save');?>
                                            </button>
                                        </form>
                                    </div>
                                </div>
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


<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get the active tab from Local Storage
    var activeTab = localStorage.getItem('activeTab');
    if (activeTab) {
        // Show the saved tab
        $('.nav-tabs a[href="#' + activeTab + '"]').tab('show');
    }

    // Save the selected tab to Local Storage
    $('.nav-tabs a').on('shown.bs.tab', function(e) {
        var selectedTab = $(e.target).attr('href').substr(1);
        localStorage.setItem('activeTab', selectedTab);
    });

    // Xử lý nút Set Webhook
    $('#btn_set_webhook').click(function() {
        var btn = $(this);
        var input = $('#telegram_webhook_secret');
        var loading = $('#webhook_loading');
        var icon = $('#webhook_icon');
        var result = $('#webhook_result');

        // Disable button và show loading
        btn.prop('disabled', true);
        loading.show();
        icon.hide();
        result.html('');

        // Tạo secret token mới (64 ký tự hex)
        var newSecret = '';
        var chars = '0123456789abcdef';
        for (var i = 0; i < 64; i++) {
            newSecret += chars.charAt(Math.floor(Math.random() * chars.length));
        }

        // Cập nhật input với token mới
        input.val(newSecret);

        // Gọi AJAX để set webhook
        $.ajax({
            url: '<?=base_url('ajaxs/admin/update.php');?>',
            type: 'POST',
            data: {
                action: 'set_webhook',
                telegram_webhook_secret: newSecret
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    result.html(
                        '<div class="alert alert-success mt-2"><i class="fa fa-check"></i> ' +
                        response.msg + '</div>');

                    // Tự động lưu setting mới
                    $('input[name="telegram_webhook_secret"]').val(newSecret);

                    // Hiển thị thông tin webhook
                    if (response.webhook_url) {
                        result.append(
                            '<div class="alert alert-info mt-1"><strong><?=__("Webhook URL");?>:</strong><br><code>' +
                            response.webhook_url + '</code></div>');
                    }
                } else {
                    result.html(
                        '<div class="alert alert-danger mt-2"><i class="fa fa-times"></i> ' +
                        response.msg + '</div>');
                }
            },
            error: function() {
                result.html(
                    '<div class="alert alert-danger mt-2"><i class="fa fa-times"></i> <?=__("Lỗi kết nối, vui lòng thử lại");?></div>'
                );
            },
            complete: function() {
                // Enable button và hide loading
                btn.prop('disabled', false);
                loading.hide();
                icon.show();
            }
        });
    });

    // Xử lý nút AI cho Telegram notifications
    $('.btn-ai-telegram').click(function() {
        var btn = $(this);
        var type = btn.data('type');
        var target = btn.data('target');
        var targetElement = $(target);

        // Hiển thị loading
        var originalContent = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?=base_url('ajaxs/admin/ai.php');?>',
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'generateTelegramNotification',
                type: type
            },
            success: function(response) {
                if (response.success) {
                    targetElement.val(response.content);

                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: '<?=__('Đã tạo nội dung thông báo Telegram bằng AI');?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Hiển thị lỗi
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Có lỗi xảy ra');?>',
                        text: response.message ||
                            '<?=__('Không thể tạo nội dung');?>'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi kết nối');?>',
                    text: '<?=__('Không thể kết nối đến server AI');?>'
                });
            },
            complete: function() {
                // Khôi phục nút
                btn.html(originalContent).prop('disabled', false);
            }
        });
    });

    // Xử lý nút AI cho Email notifications
    $('.btn-ai-email').click(function() {
        var btn = $(this);
        var type = btn.data('type');
        var target = btn.data('target');
        var targetElement = $(target);

        // Hiển thị loading
        var originalContent = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?=base_url('ajaxs/admin/ai.php');?>',
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'generateEmailNotification',
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // Nếu là CKEDITOR, cập nhật vào editor
                    if (CKEDITOR.instances[targetElement.attr('id')]) {
                        CKEDITOR.instances[targetElement.attr('id')].setData(response
                            .content);
                    } else {
                        // Nếu là textarea thường
                        targetElement.val(response.content);
                    }

                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: '<?=__('Đã tạo nội dung thông báo Email bằng AI');?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Hiển thị lỗi
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Có lỗi xảy ra');?>',
                        text: response.message ||
                            '<?=__('Không thể tạo nội dung');?>'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi kết nối');?>',
                    text: '<?=__('Không thể kết nối đến server AI');?>'
                });
            },
            complete: function() {
                // Khôi phục nút
                btn.html(originalContent).prop('disabled', false);
            }
        });
    });

    // Xử lý nút AI cho System Pages
    $('.btn-ai-page').click(function() {
        var btn = $(this);
        var type = btn.data('type');
        var target = btn.data('target');
        var targetElement = $(target);

        // Hiển thị loading
        var originalContent = btn.html();
        btn.html('<i class="fa fa-spinner fa-spin"></i>').prop('disabled', true);

        $.ajax({
            url: '<?=base_url('ajaxs/admin/ai.php');?>',
            method: 'POST',
            dataType: 'JSON',
            data: {
                action: 'generateSystemPageContent',
                type: type
            },
            success: function(response) {
                if (response.success) {
                    // Nếu là CKEDITOR, cập nhật vào editor
                    if (CKEDITOR.instances[targetElement.attr('id')]) {
                        CKEDITOR.instances[targetElement.attr('id')].setData(response
                            .content);
                    } else {
                        // Nếu là textarea thường
                        targetElement.val(response.content);
                    }

                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: '<?=__('Đã tạo nội dung trang hệ thống bằng AI');?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    // Hiển thị lỗi
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Có lỗi xảy ra');?>',
                        text: response.message ||
                            '<?=__('Không thể tạo nội dung');?>'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('Error:', error);

                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi kết nối');?>',
                    text: '<?=__('Không thể kết nối đến server AI');?>'
                });
            },
            complete: function() {
                // Khôi phục nút
                btn.html(originalContent).prop('disabled', false);
            }
        });
    });

    // Xử lý modal AI cho Footer Script
    window.openFooterScriptAIModal = function() {
        document.getElementById('footerScriptDescription').value = '';
        var modal = new bootstrap.Modal(document.getElementById('footerScriptAIModal'));
        modal.show();
    };

    // Xử lý tạo script footer bằng AI
    window.generateFooterScript = function() {
        const description = document.getElementById('footerScriptDescription').value.trim();
        const generateBtn = document.getElementById('generateFooterBtn');

        if (description === '') {
            Swal.fire({
                icon: 'warning',
                title: '<?=__('Thiếu thông tin');?>',
                text: '<?=__('Vui lòng nhập mô tả về script cần tạo');?>'
            });
            return;
        }

        // Disable button và show loading
        generateBtn.disabled = true;
        generateBtn.innerHTML =
            '<i class="me-1 spinner-border spinner-border-sm"></i><?=__('Đang tạo...');?>';

        $.ajax({
            url: '<?=base_url('ajaxs/admin/ai.php');?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'generateFooterScript',
                description: description
            },
            success: function(response) {
                console.log('AI Response:', response); // Debug log
                if (response.success) {
                    // Sử dụng CodeMirror instance từ biến global
                    if (window.codeMirrorInstances && window.codeMirrorInstances[
                            'javascript_footer']) {
                        const codeMirrorInstance = window.codeMirrorInstances[
                            'javascript_footer'];
                        const currentContent = codeMirrorInstance.getValue();
                        const newContent = currentContent + (currentContent ? '\n\n' : '') +
                            response.content;
                        codeMirrorInstance.setValue(newContent);

                        // Focus và đặt cursor ở cuối
                        codeMirrorInstance.focus();
                        codeMirrorInstance.setCursor(codeMirrorInstance.lineCount(), 0);

                        console.log('Updated CodeMirror content:', newContent); // Debug log
                    } else {
                        // Fallback - cập nhật trực tiếp vào textarea nếu không tìm thấy CodeMirror
                        const footerTextarea = document.getElementById('javascript_footer');
                        if (footerTextarea) {
                            const currentContent = footerTextarea.value;
                            const newContent = currentContent + (currentContent ? '\n\n' : '') +
                                response.content;
                            footerTextarea.value = newContent;
                            footerTextarea.dispatchEvent(new Event('change'));
                            console.log('Updated textarea directly:', newContent); // Debug log
                        } else {
                            console.error('Editor not found!');
                            Swal.fire({
                                icon: 'error',
                                title: '<?=__('Lỗi');?>',
                                text: 'Không tìm thấy editor javascript_footer'
                            });
                            return;
                        }
                    }

                    // Đóng modal
                    var modal = bootstrap.Modal.getInstance(document.getElementById(
                        'footerScriptAIModal'));
                    if (modal) {
                        modal.hide();
                    }

                    // Hiển thị thông báo thành công
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: '<?=__('Đã tạo script footer bằng AI');?>',
                        timer: 2000,
                        showConfirmButton: false
                    });
                } else {
                    console.error('AI Error:', response.message); // Debug log
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Có lỗi xảy ra');?>',
                        text: response.message || '<?=__('Không thể tạo script');?>'
                    });
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi kết nối');?>',
                    text: '<?=__('Không thể kết nối đến server AI');?>'
                });
            },
            complete: function() {
                // Enable button
                generateBtn.disabled = false;
                generateBtn.innerHTML =
                    '<i class="ri-magic-line me-1"></i><?=__('Tạo ngay');?>';
            }
        });
    };
});

// Function để copy URL vào clipboard
function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const button = element.parentElement.querySelector('button');

    // Copy text to clipboard
    navigator.clipboard.writeText(element.value).then(function() {
        // Thay đổi icon và màu button tạm thời
        const originalHTML = button.innerHTML;
        button.innerHTML = '<i class="ri-check-line"></i>';
        button.classList.remove('btn-outline-secondary');
        button.classList.add('btn-success');

        // Hiển thị thông báo
        showMessage('<?=__('Đã sao chép!');?>', 'success')

        // Khôi phục button sau 2 giây
        setTimeout(function() {
            button.innerHTML = originalHTML;
            button.classList.remove('btn-success');
            button.classList.add('btn-outline-secondary');
        }, 2000);
    }).catch(function(err) {
        // Fallback cho trình duyệt cũ
        element.select();
        element.setSelectionRange(0, 99999);
        document.execCommand('copy');

        // Hiển thị thông báo
        showMessage('<?=__('Đã sao chép!');?>', 'success')
    });
}
</script>

<script>
// Cấu hình CKEDITOR đơn giản với các chức năng cần thiết - 1 hàng duy nhất
var ckeditorConfig = {
    toolbar: [{
            name: 'styles',
            items: ['Format', 'FontSize']
        },
        {
            name: 'basicstyles',
            items: ['Bold', 'Italic', 'Underline']
        },
        {
            name: 'colors',
            items: ['TextColor']
        },
        {
            name: 'paragraph',
            items: ['NumberedList', 'BulletedList', 'JustifyLeft', 'JustifyCenter']
        },
        {
            name: 'links',
            items: ['Link']
        },
        {
            name: 'insert',
            items: ['Image', 'Table']
        },
        {
            name: 'tools',
            items: ['Source']
        }
    ],
    height: 200,
    removePlugins: 'elementspath',
    resize_enabled: true,
    language: 'vi'
};

// Khởi tạo CKEDITOR cho các textarea thông báo
CKEDITOR.replace("popup_noti", ckeditorConfig);
CKEDITOR.replace("page_faq", ckeditorConfig);
CKEDITOR.replace("page_policy", ckeditorConfig);
CKEDITOR.replace("page_privacy", ckeditorConfig);
CKEDITOR.replace("page_contact", ckeditorConfig);
CKEDITOR.replace("notice_home", ckeditorConfig);
CKEDITOR.replace("notice_orders", ckeditorConfig);
// Email notifications
CKEDITOR.replace("email_temp_content_warning_login");
CKEDITOR.replace("email_temp_content_forgot_password");
CKEDITOR.replace("email_temp_content_otp_mail");
CKEDITOR.replace("email_temp_content_warning_ticket");
CKEDITOR.replace("email_temp_content_reply_ticket");

// Khởi tạo CodeMirror cho các textarea script/HTML
setTimeout(function() {
    // CodeMirror configuration
    var codeMirrorConfig = {
        lineNumbers: true,
        mode: "htmlmixed",
        theme: "monokai",
        matchBrackets: true,
        autoCloseTags: true,
        lineWrapping: true,
        foldGutter: true,
        gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
        extraKeys: {
            "Ctrl-Space": "autocomplete",
            "F11": function(cm) {
                cm.setOption("fullScreen", !cm.getOption("fullScreen"));
            },
            "Esc": function(cm) {
                if (cm.getOption("fullScreen")) cm.setOption("fullScreen", false);
            }
        }
    };

    // Tạo biến global để lưu trữ CodeMirror instances
    window.codeMirrorInstances = {};

    // Khởi tạo cho tất cả các textarea script
    ['javascript_header', 'javascript_footer', 'script_footer_admin'].forEach(function(id) {
        if (document.getElementById(id)) {
            window.codeMirrorInstances[id] = CodeMirror.fromTextArea(document.getElementById(id),
                codeMirrorConfig);
        }
    });
}, 100);

// Function để cập nhật mô tả load data type
function updateLoadDataDescription() {
    const selectElement = document.getElementById('load_data_type');
    const descriptionElement = document.getElementById('load_data_description').querySelector('small');

    if (selectElement.value === 'all') {
        descriptionElement.innerHTML =
            '<strong class="text-info"><?=__('Chế độ All Data:');?></strong><br>' +
            '<?=__('Tải toàn bộ dịch vụ và chuyên mục ngay khi vào trang Order. Phù hợp cho website có ít hơn 500 dịch vụ. Tốc độ hiển thị nhanh nhưng tốn bandwidth ban đầu.');?>';

        // Cập nhật alert color
        const alertContainer = document.getElementById('load_data_description').querySelector('.alert');
        alertContainer.className = 'alert alert-primary-transparent border-0 p-3';
    } else if (selectElement.value === 'lazy') {
        descriptionElement.innerHTML =
            '<strong class="text-success"><?=__('Chế độ Lazy Load:');?></strong><br>' +
            '<?=__('Chỉ tải dịch vụ của chuyên mục được chọn. Tối ưu tốc độ tải trang, giảm 75-90% thời gian loading và 90-95% bandwidth. Khuyến nghị cho website có nhiều dịch vụ.');?>';

        // Cập nhật alert color
        const alertContainer = document.getElementById('load_data_description').querySelector('.alert');
        alertContainer.className = 'alert alert-success-transparent border-0 p-3';
    }
}

// Gọi function khi trang load để hiển thị mô tả ban đầu
document.addEventListener('DOMContentLoaded', function() {
    updateLoadDataDescription();
});
</script>


<!-- Modal AI Generate Footer Script -->
<div class="modal fade" id="footerScriptAIModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
            <!-- Header với gradient -->
            <div class="modal-header border-0 text-white pb-2">
                <div class="d-flex align-items-center">
                    <div class="me-3 p-2 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="ri-robot-line" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold text-white"><?=__('AI Script Generator');?></h5>
                        <small class="opacity-75"><?=__('Tạo script/HTML thông minh');?></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body với background trắng -->
            <div class="modal-body bg-white m-3 rounded-4 shadow-sm">
                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 60px; height: 60px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%;">
                        <i class="ri-magic-line text-white" style="font-size: 24px;"></i>
                    </div>
                    <p class="text-muted mb-0"><?=__('Mô tả script/hiệu ứng bạn muốn tạo');?></p>
                </div>

                <textarea class="form-control border-2" id="footerScriptDescription" rows="3"
                    placeholder="<?=__('VD: Tạo hiệu ứng tuyết rơi, chat popup, back to top button, loading animation...');?>"
                    style="border-color: #667eea; border-radius: 12px; resize: none;"></textarea>

                <div class="mt-2">
                    <small class="text-muted">
                        <i class="ri-lightbulb-line me-1"></i>
                        <?=__('Gợi ý: Hãy mô tả rõ ràng hiệu ứng, màu sắc, vị trí hiển thị để AI tạo script chính xác nhất');?>
                    </small>
                </div>
            </div>

            <!-- Footer với nút gradient -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" style="border-radius: 25px;">
                    <i class="ri-close-line me-1"></i><?=__('Hủy');?>
                </button>
                <button type="button" class="btn text-white fw-bold px-4" onclick="generateFooterScript()"
                    id="generateFooterBtn"
                    style="background: linear-gradient(45deg, #667eea, #764ba2); border: none; border-radius: 25px; min-width: 140px;">
                    <i class="ri-magic-line me-1"></i><?=__('Tạo ngay');?>
                </button>
            </div>
        </div>
    </div>
</div>