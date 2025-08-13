<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Thêm gói dịch vụ mới'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<!-- ckeditor -->
<script src="'.BASE_URL('public/ckeditor/ckeditor.js').'"></script>

<!-- Choices Css -->
<link rel="stylesheet" href="'.base_url('public/theme/').'assets/libs/choices.js/public/assets/styles/choices.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">



<!-- Include jQuery UI for autocomplete -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<style>
    /* ========== STYLE CHO AUTOCOMPLETE (GỢI Ý TỰ ĐỘNG) ========== */
    
    /* Container chứa danh sách gợi ý */
    .ui-autocomplete {
        max-height: 200px;              /* Chiều cao tối đa 200px */
        overflow-y: auto;               /* Cuộn dọc khi quá dài */
        overflow-x: hidden;             /* Ẩn cuộn ngang */
        z-index: 9999;                  /* Hiển thị trên cùng */
        border: 1px solid #ddd;         /* Viền xám nhạt */
        border-radius: 4px;             /* Bo góc 4px */
        background-color: white;        /* Nền trắng */
        box-shadow: 0 2px 8px rgba(0,0,0,0.15);  /* Đổ bóng nhẹ */
    }
    
    /* Mỗi item trong danh sách gợi ý */
    .ui-menu-item {
        font-size: 14px;                /* Cỡ chữ 14px */
        border-bottom: 1px solid #f0f0f0;  /* Đường kẻ ngăn cách */
    }
    
    /* Item cuối cùng không có đường kẻ */
    .ui-menu-item:last-child {
        border-bottom: none;
    }
    
    /* Wrapper bọc nội dung item */
    .ui-menu-item-wrapper {
        padding: 8px 12px;              /* Khoảng cách trong 8px-12px */
        cursor: pointer;                /* Con trỏ chuột dạng tay */
    }
    
    /* Style khi hover hoặc chọn item */
    .ui-state-active, .ui-widget-content .ui-state-active {
        background: #007bff !important; /* Nền xanh dương */
        color: white !important;        /* Chữ màu trắng */
        border: none !important;        /* Không có viền */
    }
    
    /* Icon loading bên cạnh ô input */
    #loading-supplier {
        position: absolute;             /* Vị trí tuyệt đối */
        right: 10px;                   /* Cách bên phải 10px */
        top: 50%;                      /* Giữa theo chiều dọc */
        transform: translateY(-50%);    /* Canh giữa chính xác */
        color: #007bff;                /* Màu xanh dương */
        z-index: 10;                   /* Hiển thị trên input */
    }
</style>
';
$body['footer'] = '
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Internal Select-2.js -->
<script src="'.base_url('public/theme/').'assets/js/select2.js"></script>

';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
if(checkPermission($getUser['admin'], 'edit_product') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>
<?php
if (isset($_POST['SaveService'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }
    $isInsert = $CMSNT->insert("services", [
        'category_id'   => check_string($_POST['category_id']),
        'supplier_id'   => check_string($_POST['supplier_id']),
        'name'          => check_string($_POST['name']),
        'description'   => $_POST['description'],
        'type'          => check_string($_POST['type']),
        'cost'          => check_string($_POST['cost']),
        'price'         => check_string($_POST['price']),
        'price_1'       => check_string($_POST['price_1']),
        'price_2'       => check_string($_POST['price_2']),
        'price_3'       => check_string($_POST['price_3']),
        'min'           => check_string($_POST['min']),
        'max'           => check_string($_POST['max']),
        'api_id'        => check_string($_POST['api_id']),
        'dripfeed'      => isset($_POST['dripfeed']) ? true : false,
        'refill'        => isset($_POST['refill']) ? true : false,
        'cancel'        => isset($_POST['cancel']) ? true : false,
        'display'       => check_string($_POST['display']),
        'average_time'  => check_string($_POST['average_time']),
        'auto_get_average_time' => isset($_POST['auto_get_average_time']) ? 1 : 0,
        'auto_sync_min_max' => isset($_POST['auto_sync_min_max']) ? 1 : 0,
        'created_at'    => gettime(),
        'updated_at'    => gettime()
    ]);
    if ($isInsert) {
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Thêm gói dịch vụ mới')." (".check_string($_POST['name']).")."
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Thêm gói dịch vụ mới')." (".check_string($_POST['name']).").", $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);    
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);

        die('<script type="text/javascript">
            Swal.fire({
                title: "'.__('Thêm dịch vụ thành công!').'",
                text: "'.__('Dịch vụ đã được thêm vào hệ thống thành công!').'",
                icon: "success",
                showCancelButton: false,
                showDenyButton: true,
                showConfirmButton: true,
                confirmButtonText: "<i class=\"fa-solid fa-plus me-1\"></i>'.__('Thêm gói mới').'",
                denyButtonText: "<i class=\"fa-solid fa-list me-1\"></i>'.__('Xem danh sách').'",
                confirmButtonColor: "#28a745",
                denyButtonColor: "#007bff",
                allowOutsideClick: false,
                allowEscapeKey: false,
                reverseButtons: false,
                focusConfirm: true
            }).then((result) => {
                if (result.isConfirmed) {
                    // Thêm gói mới - chuyển hướng đến trang thêm dịch vụ mới (tránh POST lại)
                    window.location.href = "'.base_url_admin('service-add').'";
                } else if (result.isDenied) {
                    // Xem danh sách dịch vụ
                    window.location.href = "'.base_url_admin('services').'";
                }
            });
        </script>');
    } else {
        die('<script type="text/javascript">showMessage("'.__('Thêm thất bại!').'", "error");</script>');
    }
}
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2">
                    <i class="fa-solid fa-plus-circle me-2 text-success"></i>
                    <?=__('Thêm gói dịch vụ mới');?>
                </h1>
                <p class="text-muted mb-0"><?=__('Tạo và cấu hình gói dịch vụ mới cho hệ thống');?></p>
            </div>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?=base_url_admin('services');?>" class="text-decoration-none">
                                <i class="fa fa-home me-1"></i><?=__('Gói dịch vụ');?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">
                            <?=__('Thêm mới');?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Thông tin cơ bản -->
                <div class="col-xl-8">
                    <div class="card custom-card shadow-sm">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-info-circle me-2 text-primary"></i>
                                <?=__('Thông tin cơ bản');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-tag me-1"></i><?=__('Tên dịch vụ');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <input name="name" type="text" value="" 
                                           class="form-control form-control-lg" 
                                           placeholder="<?=__('Nhập tên dịch vụ');?>" required>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-list me-1"></i><?=__('Chuyên mục');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="category_id" class="form-control js-example-basic-single" required>
                                        <option value=""><?=__('-- Chọn chuyên mục --');?></option>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0") as $category):?>
                                        <option disabled value="<?=$category['id'];?>" class="fw-bold">
                                            <?=$category['name'];?>
                                        </option>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$category['id']."' ") as $sub_category):?>
                                        <option value="<?=$sub_category['id'];?>">
                                            &nbsp;&nbsp;↳ <?=$sub_category['name'];?>
                                        </option>
                                        <?php endforeach?>
                                        <?php endforeach?>
                                    </select>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-cog me-1"></i><?=__('Loại dịch vụ');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <select name="type" class="form-control js-example-basic-single" required id="service_type_select">
                                        <option value=""><?=__('-- Chọn loại dịch vụ --');?></option>
                                        <?php foreach(getListServiceType() as $service_type):?>
                                        <option value="<?=$service_type['code'];?>" data-description="<?=$service_type['description'];?>">
                                            <?=$service_type['name'];?>
                                        </option>
                                        <?php endforeach?>
                                    </select>
                                    
                                    <!-- Hiển thị mô tả loại dịch vụ -->
                                    <div id="service_type_description" class="mt-2" style="display: none;">
                                        <div class="alert alert-info-transparent border-info border-opacity-25">
                                            <div class="d-flex align-items-start">
                                                <i class="ri-information-line fs-16 me-2 mt-1 text-info"></i>
                                                <div>
                                                    <strong class="text-info"><?=__('Mô tả loại dịch vụ:');?></strong><br>
                                                    <span id="description_text" class="text-muted"></span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-align-left me-1"></i><?=__('Mô tả');?>
                                    </label>
                                    <textarea name="description" id="description" rows="6" 
                                              class="form-control" 
                                              placeholder="<?=__('Nhập mô tả chi tiết về dịch vụ');?>"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin giá cả -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-dollar-sign me-2 text-success"></i>
                                <?=__('Thông tin giá cả');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-warning">
                                        <i class="fa-solid fa-coins me-1"></i><?=__('Giá vốn');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning-transparent">
                                            <i class="fa-solid fa-money-bill"></i>
                                        </span>
                                        <input name="cost" type="number" step="any" min="0" value=""
                                               class="form-control" placeholder="0.0000" required>
                                        <span class="input-group-text"><?=currencyDefault();?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-success">
                                        <i class="fa-solid fa-tag me-1"></i><?=__('Giá bán lẻ');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success-transparent">
                                            <i class="fa-solid fa-hand-holding-dollar"></i>
                                        </span>
                                        <input name="price" type="number" step="any" min="0" value=""
                                               class="form-control" placeholder="0.0000" required>
                                        <span class="input-group-text"><?=currencyDefault();?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold text-primary">
                                        <i class="fa-solid fa-star me-1"></i><?=__('Giá');?> <?=__(getRankNameByTarget('price_1'));?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary-transparent">VIP1</span>
                                        <input name="price_1" type="number" step="any" min="0" value=""
                                               class="form-control" placeholder="0.0000" required>
                                        <span class="input-group-text"><?=currencyDefault();?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold text-info">
                                        <i class="fa-solid fa-star me-1"></i><?=__('Giá');?> <?=__(getRankNameByTarget('price_2'));?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-info-transparent">VIP2</span>
                                        <input name="price_2" type="number" step="any" min="0" value=""
                                               class="form-control" placeholder="0.0000" required>
                                        <span class="input-group-text"><?=currencyDefault();?></span>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold text-purple">
                                        <i class="fa-solid fa-crown me-1"></i><?=__('Giá');?> <?=__(getRankNameByTarget('price_3'));?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-purple-transparent">VIP3</span>
                                        <input name="price_3" type="number" step="any" min="0" value=""
                                               class="form-control" placeholder="0.0000" required>
                                        <span class="input-group-text"><?=currencyDefault();?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cài đặt số lượng -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light d-flex justify-content-between align-items-center">
                            <div class="card-title text-uppercase mb-0">
                                <i class="fa-solid fa-calculator me-2 text-info"></i>
                                <?=__('Cài đặt số lượng');?>
                            </div>
                            <div class="form-check form-switch" id="quantity-setting-checkbox">
                                <input class="form-check-input" type="checkbox" value="1" checked
                                       id="auto_sync_min_max" name="auto_sync_min_max">
                                <label class="form-check-label fw-semibold" for="auto_sync_min_max" data-toggle="tooltip" data-placement="bottom" title="<?=__('Tự động đồng bộ min max theo API nhà cung cấp');?>">
                                    <?=__('Đồng bộ theo API');?>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-arrow-down me-1 text-success"></i><?=__('Số lượng tối thiểu');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success-transparent">MIN</span>
                                        <input name="min" type="number" value="1"
                                               class="form-control" placeholder="1" required>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-arrow-up me-1 text-danger"></i><?=__('Số lượng tối đa');?>
                                        <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-danger-transparent">MAX</span>
                                        <input name="max" type="number" value="999999"
                                               class="form-control" placeholder="999999" required>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar Settings -->
                <div class="col-xl-4">
                    <!-- Trạng thái -->
                    <div class="card custom-card shadow-sm">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-toggle-on me-2 text-success"></i>
                                <?=__('Trạng thái');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-eye me-1"></i><?=__('Hiển thị');?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" name="display" required>
                                    <option value="show">
                                        <i class="fa fa-eye"></i> <?=__('Hiển thị');?>
                                    </option>
                                    <option value="hide">
                                        <i class="fa fa-eye-slash"></i> <?=__('Ẩn');?>
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Cấu hình API -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-plug me-2 text-warning"></i>
                                <?=__('Cấu hình API');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-server me-1"></i><?=__('Nhà cung cấp API');?>
                                </label>
                                <select name="supplier_id" class="form-control js-example-basic-single">
                                    <option value="0">
                                        <i class="fa fa-home"></i> <?=__('Hệ thống');?>
                                    </option>
                                    <?php foreach($CMSNT->get_list("SELECT * FROM `suppliers` ") as $supplier):?>
                                    <option value="<?=$supplier['id'];?>">
                                        <?=$supplier['domain'];?>
                                    </option>
                                    <?php endforeach?>
                                </select>
                            </div>
                            
                            <div class="mb-4" id="api-id-container">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-key me-1"></i><?=__('ID API');?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fa-solid fa-hashtag"></i>
                                    </span>
                                    <input name="api_id" type="text" value=""
                                           class="form-control" placeholder="<?=__('Nhập ID API nếu có');?>">
                                </div>
                            </div>
                            
                            <div class="mb-4" id="system-message" style="display: none;">
                                <div class="alert alert-info d-flex align-items-center">
                                    <i class="fa-solid fa-info-circle me-2"></i>
                                    <div>
                                        <strong><?=__('Các đơn hàng của dịch vụ này sẽ cần được bạn xử lý thủ công.');?></strong><br>
                                        <?=__('Nếu bạn cần tài liệu API để kết nối vào Tool thì có thể xem tại đây.');?>
                                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" data-bs-toggle="modal" data-bs-target="#apiDocsModal">
                                            <i class="fa-solid fa-book me-1"></i><?=__('Xem tài liệu API');?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tùy chọn nâng cao -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-sliders me-2 text-secondary"></i>
                                <?=__('Tùy chọn nâng cao');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="dripfeed" name="dripfeed" value="true">
                                    <label class="form-check-label fw-semibold" for="dripfeed">
                                        <i class="fa-solid fa-clock me-1 text-warning"></i>
                                        <?=__('Chạy chậm (Dripfeed)');?>
                                    </label>
                                    <small class="d-block text-muted"><?=__('Chia nhỏ đơn hàng theo thời gian');?></small>
                                </div>
                                
                                <div class="form-check form-switch mb-3">
                                    <input class="form-check-input" type="checkbox" 
                                           id="refill" name="refill" value="true">
                                    <label class="form-check-label fw-semibold" for="refill">
                                        <i class="fa-solid fa-shield-alt me-1 text-success"></i>
                                        <?=__('Bảo hành (Refill)');?>
                                    </label>
                                    <small class="d-block text-muted"><?=__('Hỗ trợ bảo hành khi có sự cố');?></small>
                                </div>
                                
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input" type="checkbox" 
                                           id="cancel" name="cancel" value="true">
                                    <label class="form-check-label fw-semibold" for="cancel">
                                        <i class="fa-solid fa-times-circle me-1 text-danger"></i>
                                        <?=__('Cho phép hủy');?>
                                    </label>
                                    <small class="d-block text-muted"><?=__('Khách hàng có thể hủy đơn hàng');?></small>
                                </div>
                            </div>
                            <div class="mb-4">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <label class="form-label fw-semibold mb-0">
                                        <i class="fa-solid fa-clock me-1"></i><?=__('Thời gian hoàn thành đơn hàng trung bình (nếu có)');?>
                                    </label>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" value="1"
                                               id="auto_get_average_time" name="auto_get_average_time" checked>
                                        <label class="form-check-label fw-semibold" for="auto_get_average_time" data-toggle="tooltip" data-placement="bottom" title="<?=__('Hệ thống tự tính trung bình thời gian hoàn thành của 10 đơn hàng có số lượng 1000 gần nhất nếu bạn tích vào đây.');?>">
                                            <?=__('Auto');?>
                                        </label>
                                    </div>
                                </div>
                                <div class="input-group">
                                    <input name="average_time" type="text" value="0" required
                                           class="form-control" placeholder="<?=__('Nhập thời gian hoàn thành trung bình của số lượng 1.000 (nếu có)');?>">
                                    <span class="input-group-text"><?=__('Giây');?></span>
                                </div>
                                <small class="d-block text-muted"><?=__('Thời gian hoàn thành trung bình của số lượng 1.000');?></small>
                            </div>
                        </div>
                    </div>

                    <!-- Hướng dẫn -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-question-circle me-2 text-info"></i>
                                <?=__('Hướng dẫn');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                <h6 class="alert-heading">
                                    <i class="fa-solid fa-lightbulb me-1"></i>
                                    <?=__('Lưu ý quan trọng');?>
                                </h6>
                                <hr>
                                <small class="mb-0">
                                    • <?=__('Điền đầy đủ thông tin bắt buộc có dấu (*)')?>
                                    <br>• <?=__('Giá bán phải lớn hơn giá vốn')?>
                                    <br>• <?=__('Số lượng tối đa phải lớn hơn số lượng tối thiểu')?>
                                    <br>• <?=__('Cấu hình API chỉ cần thiết khi dùng API ngoài')?>
                                </small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card custom-card shadow-sm">
                        <div class="card-body text-center">
                            <a href="<?=base_url_admin('services');?>" 
                               class="btn btn-light btn-wave me-2">
                                <i class="fa fa-arrow-left me-1"></i>
                                <?=__('Quay lại');?>
                            </a>
                            <button type="submit" name="SaveService" 
                                    class="btn btn-success btn-wave">
                                <i class="fa fa-plus me-1"></i>
                                <?=__('Thêm gói dịch vụ');?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal API Documentation -->
<div class="modal fade" id="apiDocsModal" tabindex="-1" aria-labelledby="apiDocsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="apiDocsModalLabel">
                    <i class="fa-solid fa-code me-2"></i><?=__('Tài liệu API - Xử lý đơn hàng thủ công');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Nav tabs -->
                <ul class="nav nav-tabs mb-4" id="apiTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="get-orders-tab" data-bs-toggle="tab" data-bs-target="#get-orders" 
                                type="button" role="tab" aria-controls="get-orders" aria-selected="true">
                            <i class="fa-solid fa-list me-1"></i><?=__('Lấy danh sách đơn hàng');?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="update-order-tab" data-bs-toggle="tab" data-bs-target="#update-order" 
                                type="button" role="tab" aria-controls="update-order" aria-selected="false">
                            <i class="fa-solid fa-edit me-1"></i><?=__('Cập nhật đơn hàng');?>
                        </button>
                    </li>
                </ul>

                <!-- Tab content -->
                <div class="tab-content" id="apiTabsContent">
                    <!-- API Lấy danh sách đơn hàng -->
                    <div class="tab-pane fade show active" id="get-orders" role="tabpanel" aria-labelledby="get-orders-tab">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-primary"><i class="fa-solid fa-info-circle me-1"></i><?=__('Mô tả');?></h6>
                                <p class="mb-0"><?=__('API này dùng để lấy danh sách các đơn hàng có trạng thái (Pending, In progress, Processing) và Nhà cung cấp = hệ thống.');?></p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="text-success"><i class="fa-solid fa-globe me-1"></i><?=__('Endpoint');?></h6>
                                <div class="bg-dark text-light p-3 rounded">
                                    <code>GET <?=base_url('api/getOrders.php');?></code>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-info"><i class="fa-solid fa-cogs me-1"></i><?=__('Tham số bắt buộc');?></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th><?=__('Tham số');?></th>
                                            <th><?=__('Loại');?></th>
                                            <th><?=__('Mô tả');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td><?=__('API Key của admin có quyền request_api');?></td>
                                        </tr>
                                        <tr>
                                            <td><code>path_admin</code></td>
                                            <td>string</td>
                                            <td><?=$CMSNT->site('path_admin');?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-info"><i class="fa-solid fa-code me-1"></i><?=__('Ví dụ Request (cURL)');?></h6>
                            <div class="bg-dark text-light p-3 rounded">
                                <pre><code>curl -X GET "<?=base_url('api/getOrders.php');?>?key=YOUR_API_KEY&path_admin=<?=$CMSNT->site('path_admin');?>" \
-H "Content-Type: application/json"</code></pre>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-success"><i class="fa-solid fa-check me-1"></i><?=__('Response thành công');?></h6>
                            <div class="bg-dark text-light p-3 rounded">
                                <pre><code>[
  {
    "id": 12345,
    "trans_id": "1234567890",
    "service_id": 1,
    "link": "https://instagram.com/username",
    "comment": "Nice profile! Great content",
    "quantity": 1000,
    "start_count": 1500,
    "remains": 0,
    "status": "pending",
    "created_at": "2024-01-15 10:30:00",
    "updated_at": "2024-01-15 10:30:00"
  },
  {
    "id": 12346,
    "trans_id": "1234567890",
    "service_id": 2,
    "link": "https://facebook.com/fanpage",
    "comment": null,
    "quantity": 500,
    "start_count": 0,
    "remains": 500,
    "status": "In progress",
    "created_at": "2024-01-15 11:00:00",
    "updated_at": "2024-01-15 11:30:00"
  }
]</code></pre>
                            </div>
                        </div>
                    </div>

                    <!-- API Cập nhật đơn hàng -->
                    <div class="tab-pane fade" id="update-order" role="tabpanel" aria-labelledby="update-order-tab">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <h6 class="text-primary"><i class="fa-solid fa-info-circle me-1"></i><?=__('Mô tả');?></h6>
                                <p class="mb-0"><?=__('API này dùng để cập nhật trạng thái, ghi chú, số lượng còn lại và số lượng bắt đầu của đơn hàng.');?></p>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-md-12">
                                <h6 class="text-success"><i class="fa-solid fa-globe me-1"></i><?=__('Endpoint');?></h6>
                                <div class="bg-dark text-light p-3 rounded">
                                    <code>POST <?=base_url('api/updateOrders.php');?></code>
                                </div>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-info"><i class="fa-solid fa-cogs me-1"></i><?=__('Tham số bắt buộc');?></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-primary text-white">
                                        <tr>
                                            <th><?=__('Tham số');?></th>
                                            <th><?=__('Loại');?></th>
                                            <th><?=__('Mô tả');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>key</code></td>
                                            <td>string</td>
                                            <td><?=__('API Key của admin');?></td>
                                        </tr>
                                        <tr>
                                            <td><code>path_admin</code></td>
                                            <td>string</td>
                                            <td><?=__('Đường dẫn admin hiện tại');?></td>
                                        </tr>
                                        <tr>
                                            <td><code>order_id</code></td>
                                            <td>integer</td>
                                            <td><?=__('ID đơn hàng cần cập nhật (ID hoặc trans_id)');?></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-info"><i class="fa-solid fa-cogs me-1"></i><?=__('Tham số tùy chọn');?></h6>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead class="bg-secondary text-white">
                                        <tr>
                                            <th><?=__('Tham số');?></th>
                                            <th><?=__('Loại');?></th>
                                            <th><?=__('Mô tả');?></th>
                                            <th><?=__('Giá trị');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><code>status</code></td>
                                            <td>string</td>
                                            <td><?=__('Trạng thái đơn hàng');?></td>
                                            <td>Completed, In progress, Processing, Canceled, Partial</td>
                                        </tr>
                                        <tr>
                                            <td><code>reason</code></td>
                                            <td>string</td>
                                            <td><?=__('Lý do/ghi chú về đơn hàng');?></td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><code>remains</code></td>
                                            <td>integer</td>
                                            <td><?=__('Số lượng còn lại (dùng cho Partial)');?></td>
                                            <td>-</td>
                                        </tr>
                                        <tr>
                                            <td><code>start_count</code></td>
                                            <td>integer</td>
                                            <td><?=__('Số lượng ban đầu');?></td>
                                            <td>-</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-info"><i class="fa-solid fa-code me-1"></i><?=__('Ví dụ Request (cURL)');?></h6>
                            <div class="bg-dark text-light p-3 rounded">
                                <h6 class="text-warning mb-2"><?=__('1. Hoàn thành đơn hàng:');?></h6>
                                <pre><code>curl -X POST "<?=base_url('api/updateOrders.php');?>" \
-H "Content-Type: application/x-www-form-urlencoded" \
-d "key=YOUR_API_KEY&path_admin=<?=$CMSNT->site('path_admin');?>&order_id=12345&status=Completed&start_count=1500"</code></pre>
                                
                                <h6 class="text-warning mb-2 mt-3"><?=__('2. Hủy đơn hàng (tự động hoàn tiền):');?></h6>
                                <pre><code>curl -X POST "<?=base_url('api/updateOrders.php');?>" \
-H "Content-Type: application/x-www-form-urlencoded" \
-d "key=YOUR_API_KEY&path_admin=<?=$CMSNT->site('path_admin');?>&order_id=12345&status=Canceled&reason=Service%20unavailable"</code></pre>
                                
                                <h6 class="text-warning mb-2 mt-3"><?=__('3. Hoàn thành một phần (tự động hoàn tiền phần còn lại):');?></h6>
                                <pre><code>curl -X POST "<?=base_url('api/updateOrders.php');?>" \
-H "Content-Type: application/x-www-form-urlencoded" \
-d "key=YOUR_API_KEY&path_admin=<?=$CMSNT->site('path_admin');?>&order_id=12345&status=Partial&remains=200&start_count=800"</code></pre>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-success"><i class="fa-solid fa-check me-1"></i><?=__('Response thành công');?></h6>
                            <div class="bg-dark text-light p-3 rounded">
                                <h6 class="text-info mb-2"><?=__('Cập nhật thông thường:');?></h6>
                                <pre><code>{
  "status": "success",
  "msg": "Cập nhật đơn hàng thành công"
}</code></pre>
                                
                                <h6 class="text-info mb-2 mt-3"><?=__('Hủy đơn/Hoàn thành một phần (có hoàn tiền):');?></h6>
                                <pre><code>{
  "status": "success",
  "msg": "Hoàn tiền đơn hàng cho user thành công!"
}</code></pre>
                            </div>
                        </div>

                        <div class="mt-3">
                            <h6 class="text-danger"><i class="fa-solid fa-exclamation-triangle me-1"></i><?=__('Response lỗi');?></h6>
                            <div class="bg-dark text-light p-3 rounded">
                                <pre><code>{
  "status": "error",
  "msg": "Đơn hàng đã hoàn thành, không thể cập nhật"
}</code></pre>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="alert alert-warning mt-4">
                    <h6 class="alert-heading"><i class="fa-solid fa-exclamation-triangle me-1"></i><?=__('Lưu ý quan trọng');?></h6>
                    <ul class="mb-0">
                        <li><?=__('API Key có thể lấy từ trang khách hàng -> Tài liệu API');?></li>
                        <li><?=__('Khi set status = "Canceled": Hệ thống tự động hoàn tiền toàn bộ cho user');?></li>
                        <li><?=__('Khi set status = "Partial": Hệ thống tự động hoàn tiền phần còn lại dựa trên remains');?></li>
                        <li><?=__('Đơn hàng đã "Completed", "In progress", "Processing" hoặc "Canceled" không thể cập nhật');?></li>
                    </ul>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fa-solid fa-times me-1"></i><?=__('Đóng');?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>

<script>
    CKEDITOR.replace("description", {
        toolbar: [
            { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
            { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
            { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
            { name: 'links', items: ['Link', 'Unlink'] },
            { name: 'colors', items: ['TextColor', 'BGColor'] },
            { name: 'tools', items: ['Maximize'] }
        ],
        removeButtons: '',
        language: 'vi',
        height: 200,
        resize_enabled: true
    });

    // Custom styling for form
    document.addEventListener('DOMContentLoaded', function() {
        // Add hover effects to cards
        const cards = document.querySelectorAll('.card');
        cards.forEach(card => {
            card.style.transition = 'transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out';
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 4px 15px rgba(0,0,0,0.1)';
            });
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '';
            });
        });

        // Auto-calculate prices based on cost
        const costInput = document.querySelector('input[name="cost"]');
        const priceInput = document.querySelector('input[name="price"]');
        const price1Input = document.querySelector('input[name="price_1"]');
        const price2Input = document.querySelector('input[name="price_2"]');
        const price3Input = document.querySelector('input[name="price_3"]');

        if (costInput) {
            costInput.addEventListener('input', function() {
                const cost = parseFloat(this.value) || 0;
                if (cost > 0) {
                    // Tự động tính giá bán với tỷ lệ lãi suất
                    priceInput.value = (cost * 1.2).toFixed(4); // Lãi 20%
                    price1Input.value = (cost * 1.15).toFixed(4); // Lãi 15% cho VIP1
                    price2Input.value = (cost * 1.12).toFixed(4); // Lãi 12% cho VIP2  
                    price3Input.value = (cost * 1.1).toFixed(4); // Lãi 10% cho VIP3
                }
            });
        }
    });

    // ==================================================================================
    // TÍNH NĂNG TỰ ĐỘNG ĐIỀN THÔNG TIN DỊCH VỤ TỪ NHÀ CUNG CẤP API
    // ==================================================================================
    // Mục đích: Khi admin chọn nhà cung cấp API và nhập ID dịch vụ, hệ thống sẽ:
    // 1. Tải danh sách dịch vụ từ nhà cung cấp qua AJAX
    // 2. Hiển thị gợi ý tự động (autocomplete) khi nhập ID API
    // 3. Tự động điền toàn bộ thông tin dịch vụ vào form khi chọn
    // 4. Tiết kiệm thời gian và đảm bảo tính chính xác của dữ liệu
    // ==================================================================================
    
    // Biến lưu trữ danh sách dịch vụ từ nhà cung cấp
    let supplierServices = [];
    
    /**
     * Hàm tải danh sách dịch vụ từ nhà cung cấp được chọn
     * @param {number} supplierId - ID của nhà cung cấp
     */
    function loadSupplierServices(supplierId) {
        // Nếu chọn "Hệ thống" (ID = 0) thì ẩn API ID và hiển thị thông báo
        if (supplierId == 0) {
            supplierServices = [];
            setupApiIdAutocomplete([]);
            $('#api-id-container').hide();
            $('#system-message').show();
            return;
        }
        
        // Hiển thị lại API ID container và ẩn thông báo hệ thống
        $('#api-id-container').show();
        $('#system-message').hide();
        
        // Gửi yêu cầu AJAX để lấy danh sách dịch vụ từ nhà cung cấp
        $.ajax({
            url: '<?=base_url('ajaxs/admin/view.php');?>',
            type: 'POST',
            dataType: 'json',
            data: {
                action: 'getAllServiceBySupplier',  // Action để lấy dịch vụ
                supplier_id: supplierId            // ID nhà cung cấp
            },
            beforeSend: function() {
                // Hiển thị loading và disable input khi đang tải
                $('#loading-supplier').show();
                $('input[name="api_id"]').prop('readonly', true).attr('placeholder', '<?=__("Đang tải dịch vụ...")?>');
            },
            success: function(response) {
                // Xử lý khi tải thành công
                if (response.status === 'success') {
                    supplierServices = response.data;           // Lưu danh sách dịch vụ
                    setupApiIdAutocomplete(response.data);      // Thiết lập autocomplete
                    $('input[name="api_id"]').prop('readonly', false).attr('placeholder', '<?=__("Nhập ID API hoặc tên dịch vụ...")?>');
                } else {
                    // Hiển thị lỗi nếu không tải được dịch vụ
                    Swal.fire('<?=__("Lỗi")?>', response.msg, 'error');
                    supplierServices = [];
                    setupApiIdAutocomplete([]);
                }
            },
            error: function() {
                // Xử lý lỗi kết nối
                Swal.fire('<?=__("Lỗi")?>', '<?=__("Không thể kết nối đến server")?>', 'error');
                supplierServices = [];
                setupApiIdAutocomplete([]);
            },
            complete: function() {
                // Ẩn loading khi hoàn thành (dù thành công hay thất bại)
                $('#loading-supplier').hide();
            }
        });
    }
    
    /**
     * Hàm thiết lập tính năng autocomplete (gợi ý tự động) cho ô nhập ID API
     * @param {Array} services - Danh sách dịch vụ từ nhà cung cấp
     */
    function setupApiIdAutocomplete(services) {
        const apiIdInput = $('input[name="api_id"]');
        
        // Xóa autocomplete cũ nếu có (tránh trùng lặp)
        if (apiIdInput.hasClass('ui-autocomplete-input')) {
            apiIdInput.autocomplete('destroy');
        }
        
        // Nếu không có dịch vụ nào thì không thiết lập autocomplete
        if (services.length === 0) {
            return;
        }
        
        // Thiết lập autocomplete mới
        apiIdInput.autocomplete({
            source: function(request, response) {
                // Lấy từ khóa tìm kiếm và chuyển thành chữ thường
                const term = request.term.toLowerCase();
                
                // Lọc các dịch vụ phù hợp (theo ID hoặc tên dịch vụ)
                const matches = services.filter(service => 
                    service.service.toString().includes(term) ||     // Tìm theo ID dịch vụ
                    service.name.toLowerCase().includes(term)        // Tìm theo tên dịch vụ
                );
                
                // Tạo danh sách gợi ý hiển thị
                const suggestions = matches.map(service => ({
                    label: `[${service.service}] ${service.name}`,  // Hiển thị: [ID] Tên dịch vụ
                    value: service.service,                          // Giá trị chọn: ID dịch vụ
                    data: service                                    // Dữ liệu đầy đủ của dịch vụ
                }));
                
                response(suggestions);
            },
            minLength: 0,                                            // Hiển thị gợi ý ngay khi focus (không cần nhập gì)
            select: function(event, ui) {
                // Khi chọn một dịch vụ từ danh sách gợi ý
                event.preventDefault();
                fillFormWithServiceData(ui.item.data);               // Tự động điền form
                $(this).val(ui.item.value);                          // Đặt giá trị ID vào ô input
                return false;
            },
            focus: function(event, ui) {
                // Khi hover qua một gợi ý
                event.preventDefault();
                $(this).val(ui.item.value);                          // Hiển thị ID trong ô input
                return false;
            }
        }).focus(function() {
            // Khi click vào ô input, hiển thị tất cả dịch vụ
            $(this).autocomplete('search', '');
        });
    }
    
    /**
     * Hàm tự động điền thông tin dịch vụ vào form khi admin chọn từ danh sách gợi ý
     * @param {Object} service - Đối tượng chứa thông tin dịch vụ từ nhà cung cấp
     */
    function fillFormWithServiceData(service) {
        // ==================== ĐIỀN THÔNG TIN CỞ BẢN ====================
        
        // Điền tên dịch vụ
        $('input[name="name"]').val(service.name);
        
        // Điền mô tả dịch vụ (hỗ trợ cả CKEditor và textarea thường)
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.description) {
            // Nếu có CKEditor thì dùng CKEditor
            CKEDITOR.instances.description.setData(service.desc || service.description || '');
        } else {
            // Nếu không có CKEditor thì dùng textarea thường
            $('textarea[name="description"]').val(service.desc || service.description || '');
        }
        
        // ==================== ĐIỀN THÔNG TIN GIÁ CẢ ====================
        
        // Lấy giá từ nhà cung cấp (đã tính toán theo tỷ giá)
        const rate = parseFloat(service.rate) || 0;
        
        // Điền giá vốn
        $('input[name="cost"]').val(rate.toFixed(4));
        
        // Điền tất cả các loại giá bằng giá vốn (không có markup)
        $('input[name="price"]').val(rate.toFixed(4));      // Giá bán lẻ
        $('input[name="price_1"]').val(rate.toFixed(4));    // Giá VIP1
        $('input[name="price_2"]').val(rate.toFixed(4));    // Giá VIP2
        $('input[name="price_3"]').val(rate.toFixed(4));    // Giá VIP3
        
        // ==================== ĐIỀN GIỚI HẠN SỐ LƯỢNG ====================
        
        // Điền số lượng tối thiểu (mặc định 1 nếu không có)
        $('input[name="min"]').val(service.min || 1);
        
        // Điền số lượng tối đa (mặc định 999999 nếu không có)
        $('input[name="max"]').val(service.max || 999999);
        
        // ==================== ĐIỀN LOẠI DỊCH VỤ ====================
        
        // Chọn loại dịch vụ nếu có (Default, Custom Comments, ...)
        if (service.type) {
            $('select[name="type"]').val(service.type).trigger('change');
        }
        
        // ==================== ĐIỀN TÙY CHỌN NÂNG CAO ====================
        
        // Tích checkbox "Chạy chậm (Dripfeed)" nếu dịch vụ hỗ trợ
        if (service.dripfeed == 1 || service.dripfeed == true) {
            $('input[name="dripfeed"]').prop('checked', true);
        }
        
        // Tích checkbox "Bảo hành (Refill)" nếu dịch vụ hỗ trợ
        if (service.refill == 1 || service.refill == true) {
            $('input[name="refill"]').prop('checked', true);
        }
        
        // Tích checkbox "Cho phép hủy" nếu dịch vụ hỗ trợ
        if (service.cancel == 1 || service.cancel == true) {
            $('input[name="cancel"]').prop('checked', true);
        }
        
        // ==================== HIỂN THỊ THÔNG BÁO THÀNH CÔNG ====================
        
        // Hiển thị popup thông báo đã tự động điền thành công
        Swal.fire({
            title: '<?=__("Thành công")?>',
            text: '<?=__("Đã tự động điền thông tin dịch vụ")?>',
            icon: 'success',
            timer: 2000,                    // Tự động đóng sau 2 giây
            showConfirmButton: false        // Không hiển thị nút OK
        });
    }
    
    // ==================== KHỞI TẠO VÀ THIẾT LẬP SỰ KIỆN ====================
    
    /**
     * Hàm xử lý hiển thị/ẩn checkbox auto_sync_min_max
     * @param {string} supplierId - ID nhà cung cấp
     */
    function handleAutoSyncCheckboxVisibility(supplierId) {
        const checkbox = $('#quantity-setting-checkbox');
        const minInput = $('input[name="min"]');
        const maxInput = $('input[name="max"]');
        
        if (supplierId == 0) {
            // Hệ thống: Ẩn checkbox và bật lại input min/max
            checkbox.hide();
            minInput.prop('readonly', false);
            maxInput.prop('readonly', false);
            minInput.parent().removeClass('opacity-50');
            maxInput.parent().removeClass('opacity-50');
            minInput.attr('placeholder', '1');
            maxInput.attr('placeholder', '999999');
        } else {
            // Có nhà cung cấp API: Hiển thị checkbox
            checkbox.show();
            handleAutoSyncMinMax();
        }
    }
    
    /**
     * Hàm xử lý logic bật/tắt input min/max theo checkbox
     */
    function handleAutoSyncMinMax() {
        const supplierId = $('select[name="supplier_id"]').val();
        
        // Chỉ xử lý khi có nhà cung cấp API (không phải hệ thống)
        if (supplierId == 0) {
            return;
        }
        
        const isChecked = $('#auto_sync_min_max').is(':checked');
        const minInput = $('input[name="min"]');
        const maxInput = $('input[name="max"]');
        
        if (isChecked) {
            // Auto sync mode: Chỉ readonly các input min và max (vẫn gửi trong form)
            minInput.prop('readonly', true);
            maxInput.prop('readonly', true);
            minInput.parent().addClass('opacity-50');
            maxInput.parent().addClass('opacity-50');
            minInput.attr('placeholder', '<?=__('Sẽ tự động đồng bộ theo API');?>');
            maxInput.attr('placeholder', '<?=__('Sẽ tự động đồng bộ theo API');?>');
        } else {
            // Manual mode: Bật input để nhập thủ công
            minInput.prop('readonly', false);
            maxInput.prop('readonly', false);
            minInput.parent().removeClass('opacity-50');
            maxInput.parent().removeClass('opacity-50');
            minInput.attr('placeholder', '1');
            maxInput.attr('placeholder', '999999');
        }
    }
    
    /**
     * Thiết lập các sự kiện khi trang web đã tải xong
     */
    $(document).ready(function() {
        
        // Lắng nghe sự kiện thay đổi nhà cung cấp
        $('select[name="supplier_id"]').on('change', function() {
            const supplierId = $(this).val();          // Lấy ID nhà cung cấp được chọn
            loadSupplierServices(supplierId);          // Tải danh sách dịch vụ từ nhà cung cấp
            
            // Xóa ID API cũ khi thay đổi nhà cung cấp (tránh nhầm lẫn)
            $('input[name="api_id"]').val('');
            
            // Xử lý hiển thị checkbox auto_sync_min_max
            handleAutoSyncCheckboxVisibility(supplierId);
        });
        
        // Thêm biểu tượng loading vào bên cạnh ô nhập ID API
        $('input[name="api_id"]').parent().append('<div id="loading-supplier" style="display:none;"><i class="fa fa-spinner fa-spin"></i> Đang tải...</div>');
        
        // Khởi tạo trạng thái ban đầu (mặc định chọn "Hệ thống")
        const currentSupplierId = $('select[name="supplier_id"]').val();
        if (currentSupplierId == 0) {
            $('#api-id-container').hide();
            $('#system-message').show();
        }
        
        // Xử lý checkbox Auto get average time
        $('#auto_get_average_time').on('change', function() {
            const isChecked = $(this).is(':checked');
            const averageTimeInput = $('input[name="average_time"]');
            
            if (isChecked) {
                // Auto mode: Chỉ readonly input và set về 0 (hệ thống sẽ tự động lấy)
                averageTimeInput.prop('readonly', true);
                averageTimeInput.val('0');
                averageTimeInput.parent().addClass('opacity-50');
                averageTimeInput.attr('placeholder', '<?=__('Hệ thống sẽ tự động lấy thời gian');?>');
            } else {
                // Manual mode: Bật input để nhập thủ công
                averageTimeInput.prop('readonly', false);
                averageTimeInput.parent().removeClass('opacity-50');
                averageTimeInput.attr('placeholder', '<?=__('Nhập thời gian hoàn thành trung bình của số lượng 1.000 (nếu có)');?>');
            }
        });
        
        // Khởi tạo trạng thái ban đầu cho checkbox auto_get_average_time
        $('#auto_get_average_time').trigger('change');
        
        // Xử lý checkbox Auto sync min max
        $('#auto_sync_min_max').on('change', function() {
            handleAutoSyncMinMax();
        });
        
        // Khởi tạo trạng thái ban đầu
        const initialSupplierId = $('select[name="supplier_id"]').val();
        handleAutoSyncCheckboxVisibility(initialSupplierId);
        
        // ==================== XỬ LÝ HIỂN THỊ MÔ TẢ LOẠI DỊCH VỤ ====================
        
        /**
         * Hàm hiển thị mô tả loại dịch vụ khi chọn type
         */
        function showServiceTypeDescription() {
            const selectedOption = $('#service_type_select option:selected');
            const description = selectedOption.data('description');
            const descriptionContainer = $('#service_type_description');
            const descriptionText = $('#description_text');
            
            if (description && description.trim() !== '') {
                // Có mô tả: hiển thị container và cập nhật text
                descriptionText.text(description);
                descriptionContainer.slideDown(300);
            } else {
                // Không có mô tả: ẩn container
                descriptionContainer.slideUp(300);
            }
        }
        
        // Lắng nghe sự kiện change của dropdown loại dịch vụ
        $('#service_type_select').on('change', function() {
            showServiceTypeDescription();
        });
        
        // Kiểm tra trạng thái ban đầu (nếu đã có giá trị được chọn)
        if ($('#service_type_select').val()) {
            showServiceTypeDescription();
        }
        
    });

     
     
</script>

