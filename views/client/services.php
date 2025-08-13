<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Bảng giá dịch vụ').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
.select2-container--default .select2-selection--single {
    height: 38px !important;
    padding: 4px 12px;
    font-size: 14px;
    line-height: 1.5;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 24px !important;
    font-size: 14px;
    padding-left: 0;
    display: flex;
    align-items: center;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 38px !important;
    right: 6px;
}

/* Hiệu ứng highlight chuyên nghiệp */
.price-highlight {
    font-weight: 600 !important;
    color: #0d6efd !important;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.1) 0%, rgba(13, 110, 253, 0.05) 100%);
    padding: 4px 8px;
    border-radius: 6px;
    border: 1px solid rgba(13, 110, 253, 0.2);
    position: relative;
    display: inline-block;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(13, 110, 253, 0.1);
}

.price-highlight:hover {
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.15) 0%, rgba(13, 110, 253, 0.08) 100%);
    border-color: rgba(13, 110, 253, 0.3);
    box-shadow: 0 4px 8px rgba(13, 110, 253, 0.15);
    transform: translateY(-1px);
}

/* Subtle glow effect */
.price-highlight::before {
    content: "";
    position: absolute;
    top: -1px;
    left: -1px;
    right: -1px;
    bottom: -1px;
    background: linear-gradient(135deg, rgba(13, 110, 253, 0.3), rgba(13, 110, 253, 0.1));
    border-radius: 7px;
    z-index: -1;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.price-highlight:hover::before {
    opacity: 1;
}

/* Icon indicator cho cột được highlight */
.price-highlight::after {
    content: "👑";
    position: absolute;
    top: -8px;
    right: -8px;
    font-size: 12px;
    opacity: 0.7;
}

/* Styles cho rank cards */
.rank-card {
    transition: all 0.3s ease;
    cursor: pointer;
}

.rank-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
}

.rank-icon {
    position: relative;
}

.rank-icon::before {
    content: "";
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 60px;
    height: 60px;
    background: linear-gradient(45deg, rgba(255, 255, 255, 0.1), rgba(255, 255, 255, 0.05));
    border-radius: 50%;
    z-index: -1;
}

.rank-benefits .benefit-item {
    display: flex;
    align-items: center;
    justify-content: center;
}

.rank-card.border-primary {
    animation: subtle-pulse 2s infinite;
}

.rank-card.border-warning {
    animation: subtle-pulse 2s infinite 0.5s;
}

.rank-card.border-info {
    animation: subtle-pulse 2s infinite 1s;
}

.rank-card.border-success {
    animation: subtle-pulse 2s infinite 1.5s;
}

@keyframes subtle-pulse {
    0%, 100% {
        box-shadow: 0 0 0 0 rgba(var(--bs-primary-rgb), 0.3);
    }
    50% {
        box-shadow: 0 0 0 8px rgba(var(--bs-primary-rgb), 0);
    }
}
</style>
';
$body['footer'] = '
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
';
if($CMSNT->site('isLoginRequiredToViewProduct') == 1) {
    require_once(__DIR__ . '/../../models/is_user.php');
}else{
    if (isSecureCookie('user_login') == true) {
        require_once(__DIR__ . '/../../models/is_user.php');
    }
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

// ✅ OPTIMIZATION: Load tất cả service types 1 lần duy nhất để tránh query nhiều lần
$serviceTypesMapping = [];
$serviceTypesList = $CMSNT->get_list("SELECT * FROM `smm_service_types`");
foreach($serviceTypesList as $serviceType) {
    $serviceTypesMapping[$serviceType['code']] = $serviceType;
}

// Function helper để lấy service type từ mapping thay vì query database
function getServiceTypeFromMapping($code) {
    global $serviceTypesMapping;
    return isset($serviceTypesMapping[$code]) ? $serviceTypesMapping[$code] : ['quantity_unit' => 1000, 'name' => 'Default', 'description' => ''];
}

 
if(isset($_GET['limit'])){
    $limit = intval(check_string($_GET['limit']));
}else{
    $limit = 20;
}
if(isset($_GET['page'])){
    $page = check_string(intval($_GET['page']));
}else{
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `display` = 'show' AND `category_id` != 0 ";

$platform = '';
$category = '';
$name = '';

if(!empty($_GET['platform'])){
    $platform = check_string($_GET['platform']);
    $where .= ' AND `category_id` IN (SELECT id FROM categories WHERE parent_id = "'.$platform.'") ';
}

if(!empty($_GET['category'])){
    $category = check_string($_GET['category']);
    $where .= ' AND `category_id` = "'.$category.'" ';
}

if(!empty($_GET['name'])){
    $name = check_string($_GET['name']);
    $where .= ' AND `name` LIKE "%'.$name.'%" ';
}


$listDatatable = $CMSNT->get_list(" SELECT * FROM `services` WHERE $where ORDER BY `category_id` ASC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `services` WHERE $where ORDER BY `category_id` ASC ");
$urlDatatable = pagination_client(base_url("?action=services&limit=$limit&platform=$platform&category=$category&name=$name&"), $from, $totalDatatable, $limit);

?>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Bảng giá dịch vụ');?></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a
                                        href="<?=base_url('client/home');?>"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Bảng giá dịch vụ');?></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-12 mb-5">
                    <div class="row g-4">
                        <!-- Cấp bậc mặc định (Khách lẻ) -->
                        <div class="col-md-6 col-xl-3">
                            <div class="rank-card-wrapper position-relative">
                                <div class="card rank-card border-0 h-100 <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'active-rank' : '';?>"
                                    style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease; <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'box-shadow: 0 8px 40px rgba(13, 110, 253, 0.15);' : 'box-shadow: 0 4px 20px rgba(0,0,0,0.08);';?>">

                                    <!-- Background Pattern -->
                                    <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 1;">
                                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                                            <circle cx="60" cy="60" r="40"
                                                stroke="<?=(!isset($getUser) || $getUser['rank_id'] == 0) ? '#0d6efd' : '#6c757d';?>"
                                                stroke-width="2" />
                                            <circle cx="60" cy="60" r="20"
                                                stroke="<?=(!isset($getUser) || $getUser['rank_id'] == 0) ? '#0d6efd' : '#6c757d';?>"
                                                stroke-width="1" />
                                        </svg>
                                    </div>

                                    <!-- Status Badge -->
                                    <?php if(!isset($getUser) || $getUser['rank_id'] == 0): ?>
                                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 3;">
                                        <span class="badge bg-primary rounded-pill px-3 py-2"
                                            style="font-size: 11px; font-weight: 600; letter-spacing: 0.5px;">
                                            <i class="ri-check-line me-1"></i><?=__('HIỆN TẠI');?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="card-body text-center p-4" style="position: relative; z-index: 2;">
                                        <!-- Icon Circle -->
                                        <div class="rank-icon-circle mx-auto mb-4 position-relative"
                                            style="width: 80px; height: 80px; background: <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)' : 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
                                            <i class="ri-user-line"
                                                style="font-size: 32px; color: <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'white' : '#6c757d';?>;"></i>
                                        </div>

                                        <!-- Title -->
                                        <h5 class="card-title mb-2 <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'text-info' : '';?>"
                                            style="font-weight: 600; font-size: 18px;">
                                            <?=__('Khách lẻ');?>
                                        </h5>

                                        <p class="text-muted mb-4" style="font-size: 13px; letter-spacing: 0.3px;">
                                            <?=__('Cấp bậc mặc định cho tất cả khách hàng');?>
                                        </p>

                                        <!-- Benefits List -->
                                        <div class="benefits-list text-start">
                                            <div class="benefit-item d-flex align-items-center mb-3 p-2 rounded-3"
                                                style="background: <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'rgba(13, 110, 253, 0.08)' : 'rgba(108, 117, 125, 0.08)';?>;">
                                                <div class="benefit-icon me-3"
                                                    style="width: 32px; height: 32px; background: linear-gradient(135deg, #20c997, #0dcaf0); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="ri-price-tag-3-line text-white"
                                                        style="font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="font-size: 13px;">
                                                        <?=__('Giá bán lẻ');?></div>
                                                    <small class="text-muted"><?=__('Áp dụng giá tiêu chuẩn');?></small>
                                                </div>
                                            </div>

                                            <div class="benefit-item d-flex align-items-center p-2 rounded-3"
                                                style="background: <?=(!isset($getUser) || $getUser['rank_id'] == 0) ? 'rgba(13, 110, 253, 0.08)' : 'rgba(108, 117, 125, 0.08)';?>;">
                                                <div class="benefit-icon me-3"
                                                    style="width: 32px; height: 32px; background: linear-gradient(135deg, #fd7e14, #ffc107); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="ri-wallet-line text-white" style="font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="font-size: 13px;">
                                                        <?=__('Không yêu cầu nạp tối thiểu');?></div>
                                                    <small class="text-muted"><?=__('Dễ dàng bắt đầu');?></small>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Các cấp bậc từ database -->
                        <?php 
                        $ranks = $CMSNT->get_list("SELECT * FROM `ranks` WHERE `status` = 1 ORDER BY `min` ASC LIMIT 10");
                        $rankIcons = ['ri-medal-line', 'ri-trophy-line', 'ri-star-line', 'ri-gift-line', 'ri-crown-line', 'ri-vip-crown-line'];
                        $rankGradients = [
                            'linear-gradient(135deg, #ffc107 0%, #ff8c00 100%)', // Gold
                            'linear-gradient(135deg, #20c997 0%, #0dcaf0 100%)', // Teal to Cyan
                            'linear-gradient(135deg, #dc3545 0%, #fd7e14 100%)', // Red to Orange
                            'linear-gradient(135deg, #6f42c1 0%, #d63384 100%)', // Purple to Pink
                            'linear-gradient(135deg, #198754 0%, #20c997 100%)', // Green to Teal
                            'linear-gradient(135deg, #0d6efd 0%, #6610f2 100%)'  // Blue to Indigo
                        ];
                        $rankBorderColors = ['#ffc107', '#20c997', '#dc3545', '#6f42c1', '#198754', '#0d6efd'];
                        ?>
                        <?php foreach($ranks as $index => $rank): ?>
                        <div class="col-md-6 col-xl-3">
                            <div class="rank-card-wrapper position-relative">
                                <div class="card rank-card border-0 h-100 <?=(isset($getUser) && $getUser['rank_id'] == $rank['id']) ? 'active-rank' : '';?>"
                                    style="border-radius: 16px; overflow: hidden; transition: all 0.3s ease; <?=(isset($getUser) && $getUser['rank_id'] == $rank['id']) ? 'box-shadow: 0 8px 40px rgba('.hexdec(substr($rankBorderColors[$index % 6], 1, 2)).', '.hexdec(substr($rankBorderColors[$index % 6], 3, 2)).', '.hexdec(substr($rankBorderColors[$index % 6], 5, 2)).', 0.2);' : 'box-shadow: 0 4px 20px rgba(0,0,0,0.08);';?>">

                                    <!-- Background Pattern -->
                                    <div class="position-absolute top-0 end-0 opacity-10" style="z-index: 1;">
                                        <svg width="120" height="120" viewBox="0 0 120 120" fill="none">
                                            <circle cx="60" cy="60" r="40" stroke="<?=$rankBorderColors[$index % 6];?>"
                                                stroke-width="2" />
                                            <circle cx="60" cy="60" r="20" stroke="<?=$rankBorderColors[$index % 6];?>"
                                                stroke-width="1" />
                                        </svg>
                                    </div>

                                    <!-- Status Badge -->
                                    <?php if(isset($getUser) && $getUser['rank_id'] == $rank['id']): ?>
                                    <div class="position-absolute top-0 end-0 m-3" style="z-index: 3;">
                                        <span class="badge rounded-pill px-3 py-2"
                                            style="background: <?=$rankGradients[$index % 6];?>; font-size: 11px; font-weight: 600; letter-spacing: 0.5px; color: white;">
                                            <i class="ri-check-line me-1"></i><?=__('HIỆN TẠI');?>
                                        </span>
                                    </div>
                                    <?php endif; ?>

                                    <div class="card-body text-center p-4" style="position: relative; z-index: 2;">
                                        <!-- Icon Circle -->
                                        <div class="rank-icon-circle mx-auto mb-4 position-relative"
                                            style="width: 80px; height: 80px; background: <?=(isset($getUser) && $getUser['rank_id'] == $rank['id']) ? $rankGradients[$index % 6] : 'linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%)';?>; border-radius: 50%; display: flex; align-items: center; justify-content: center; box-shadow: 0 8px 32px rgba(0,0,0,0.12);">
                                            <i class="<?=$rankIcons[$index % 6];?>"
                                                style="font-size: 32px; color: <?=(isset($getUser) && $getUser['rank_id'] == $rank['id']) ? 'white' : '#6c757d';?>;"></i>
                                        </div>

                                        <!-- Title -->
                                        <h5 class="card-title mb-2 <?=(isset($getUser) && $getUser['rank_id'] == $rank['id']) ? 'text-info' : '';?>"
                                            style="font-weight: 600; font-size: 18px;">
                                            <?=$rank['name'];?>
                                        </h5>

                                        <p class="text-muted mb-4" style="font-size: 13px; letter-spacing: 0.3px;">
                                            <?=__('Cấp bậc đặc biệt với ưu đãi hấp dẫn');?>
                                        </p>

                                        <!-- Benefits List -->
                                        <div class="benefits-list text-start">
                                            <div class="benefit-item d-flex align-items-center mb-3 p-2 rounded-3"
                                                style="background: rgba(108, 117, 125, 0.08);">
                                                <div class="benefit-icon me-3"
                                                    style="width: 32px; height: 32px; background: <?=$rankGradients[$index % 6];?>; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="ri-price-tag-3-line text-white"
                                                        style="font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="font-size: 13px;"><?=__('Giá');?>
                                                        <?=$rank['name'];?></div>
                                                    <small class="text-muted"><?=__('Ưu đãi đặc biệt');?></small>
                                                </div>
                                            </div>

                                            <!-- Hiển thị detail nếu có -->
                                            <?php if(!empty($rank['detail'])): ?>
                                            <?php echo $rank['detail'];?>
                                            <?php endif; ?>

                                            <div class="benefit-item d-flex align-items-center mb-3 p-2 rounded-3"
                                                style="background: rgba(108, 117, 125, 0.08);">
                                                <div class="benefit-icon me-3"
                                                    style="width: 32px; height: 32px; background: linear-gradient(135deg, #fd7e14, #ffc107); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                                                    <i class="ri-wallet-line text-white" style="font-size: 14px;"></i>
                                                </div>
                                                <div>
                                                    <div class="fw-medium" style="font-size: 13px;">
                                                        <?=__('Nạp tối thiểu');?></div>
                                                    <small
                                                        class="fw-semibold"><?=format_currency($rank['min']);?></small>
                                                </div>
                                            </div>

                                            <!-- Status Progress -->
                                            <?php if(isset($getUser)): ?>
                                            <div class="progress-container">
                                                <?php if($getUser['total_money'] >= $rank['min']): ?>
                                                <div class="alert alert-success p-2 mb-0 rounded-3"
                                                    style="border: 0; background: linear-gradient(135deg, rgba(25, 135, 84, 0.1), rgba(32, 201, 151, 0.1));">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-checkbox-circle-fill text-success me-2"></i>
                                                        <small
                                                            class="text-success fw-semibold mb-0"><?=__('Đã đạt điều kiện');?></small>
                                                    </div>
                                                </div>
                                                <?php else: ?>
                                                <div class="alert alert-warning p-2 mb-0 rounded-3"
                                                    style="border: 0; background: linear-gradient(135deg, rgba(255, 193, 7, 0.1), rgba(255, 140, 0, 0.1));">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="d-flex align-items-center">
                                                            <i class="ri-information-line text-warning me-2"></i>
                                                            <small
                                                                class="text-warning fw-medium mb-0"><?=__('Còn thiếu');?></small>
                                                        </div>
                                                        <small
                                                            class="fw-bold text-warning"><?=format_currency($rank['min'] - $getUser['total_money']);?></small>
                                                    </div>
                                                    <div class="progress mt-2" style="height: 4px; border-radius: 2px;">
                                                        <div class="progress-bar bg-warning"
                                                            style="width: <?=($getUser['total_money'] / $rank['min']) * 100;?>%; border-radius: 2px;">
                                                        </div>
                                                    </div>
                                                </div>
                                                <?php endif; ?>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="col-lg-12" <?=$aos['fade-up'];?>>
                    <div class="card">
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="services">
                                <div class="row g-3">
                                    <div class="col-xxl-2 col-sm-6">
                                        <select class="form-select select2-platform" name="platform" id="platform">
                                            <option value=""><?=__('Tất cả nền tảng');?></option>
                                            <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC") as $cat):?>
                                            <option value="<?=$cat['id'];?>" data-image="<?=base_url($cat['icon']);?>"
                                                <?=$platform == $cat['id'] ? 'selected' : '';?>><?=$cat['name'];?>
                                            </option>
                                            <?php endforeach;?>
                                        </select>
                                    </div>
                                    <div class="col-xxl-2 col-sm-6">
                                        <select class="form-select select2-category" name="category" id="category">
                                            <option value=""><?=__('Tất cả phân loại');?></option>
                                        </select>

                                        <!-- Kho categories ẩn để JavaScript sử dụng -->
                                        <div id="categories-storage" style="display: none;">
                                            <?php 
                                            $allCategories = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` != 0 ORDER BY `parent_id`, `stt` DESC ");
                                            foreach($allCategories as $cat): 
                                            ?>
                                            <option value="<?=$cat['id'];?>" data-platform="<?=$cat['parent_id'];?>"
                                                <?=$category == $cat['id'] ? 'data-selected="true"' : '';?>>
                                                <?=$cat['name'];?></option>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="name" value="<?=$name;?>" placeholder="<?=__('Tên dịch vụ');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-5 col-sm-6">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('?action=services');?>"
                                                class="btn btn-light waves-effect waves-light">
                                                <i class="ri-delete-bin-line align-bottom"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div>
                                <div class="table-responsive table-card">
                                    <table class="table align-middle table-nowrap table-striped table-hover">
                                        <thead class="table-dark">
                                            <tr>
                                                <th><?=__('ID');?> - <?=__('Dịch vụ');?></th>
                                                <th class="text-end"><?=__('Giá bán lẻ');?></th>
                                                <?php if(getRankStatusByTarget('price_1') == 1): ?>
                                                <th class="text-end"><?=__('Giá');?>
                                                    <?=__(getRankNameByTarget('price_1'));?></th>
                                                <?php endif; ?>
                                                <?php if(getRankStatusByTarget('price_2') == 1): ?>
                                                <th class="text-end"><?=__('Giá');?>
                                                    <?=__(getRankNameByTarget('price_2'));?></th>
                                                <?php endif; ?>
                                                <?php if(getRankStatusByTarget('price_3') == 1): ?>
                                                <th class="text-end"><?=__('Giá');?>
                                                    <?=__(getRankNameByTarget('price_3'));?></th>
                                                <?php endif; ?>
                                                <th class="text-center"><?=__('Thời gian trung bình');?> <i
                                                        class="ri-question-line" data-bs-toggle="tooltip"
                                                        title="<?=__('Thời gian trung bình để hoàn thành đơn hàng');?>"></i>
                                                </th>
                                                <th class="text-center"><?=__('Thao tác');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="text-center p-3">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                            trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                            style="width:75px;height:75px">
                                                        </lord-icon>
                                                        <h5 class="mt-2"><?=__('Không tìm thấy kết quả');?></h5>
                                                        <p class="text-muted mb-0">
                                                            <?=__('Không có dịch vụ nào được tìm thấy');?>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach($listDatatable as $row): ?>
                                            <?php 
                                            // Lấy thông tin category
                                            $category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '".$row['category_id']."' ");
                                            $platform = null;
                                            if($category && isset($category['parent_id'])) {
                                                $platform = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '".$category['parent_id']."' ");
                                            }
                                            
                                            // Xác định class cho từng cột giá dựa trên rank_id của user
                                            $priceClass  = '';
                                            $price1Class = '';
                                            $price2Class = '';
                                            $price3Class = '';
                                            
                                            if(isset($getUser)){
                                                switch($getUser['rank_id']) {
                                                    case 0:
                                                        $priceClass = 'price-highlight';
                                                        break;
                                                    case 1:
                                                        $price1Class = 'price-highlight';
                                                        break;
                                                    case 2:
                                                        $price2Class = 'price-highlight';
                                                        break;
                                                    case 3:
                                                        $price3Class = 'price-highlight';
                                                        break;
                                                }
                                            }
                                            ?>
                                            <tr>
                                                <td>
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-3">
                                                            <?php if($platform && $platform['icon']): ?>
                                                            <img src="<?=base_url($platform['icon']);?>"
                                                                alt="<?=$platform['name'];?>" class="rounded"
                                                                style="width: 24px; height: 24px; object-fit: cover;">
                                                            <?php else: ?>
                                                            <div class="avatar-xs">
                                                                <div class="avatar-title bg-primary rounded fs-16">
                                                                    <i class="ri-service-line"></i>
                                                                </div>
                                                            </div>
                                                            <?php endif; ?>
                                                        </div>
                                                        <div class="flex-grow-1">
                                                            <h6 class="mb-1 fw-semibold">
                                                                <span class="badge bg-primary me-1">ID:
                                                                    <?=$row['id'];?></span>
                                                                <?=$row['name'];?>
                                                                <?php if($row['refill'] == 1): ?>
                                                                <span
                                                                    class="badge border border-success text-success ms-2"><?=__('Bảo hành');?></span>
                                                                <?php endif; ?>
                                                                <?php if($row['cancel'] == 1): ?>
                                                                <span
                                                                    class="badge border border-danger text-danger ms-1"><?=__('Hỗ trợ hủy');?></span>
                                                                <?php endif; ?>
                                                                <?php if($row['dripfeed'] == 1): ?>
                                                                <span
                                                                    class="badge border border-info text-info ms-1"><?=__('Lên chậm');?>
                                                                    <i class="ri-question-line" data-bs-toggle="tooltip"
                                                                        title="<?=__('Tăng từ từ phù hợp cho dịch vụ có tính chất lớn');?>"></i></span>
                                                                <?php endif; ?>
                                                            </h6>
                                                            <small class="text-muted"><?=$platform['name'] ?? '';?> •
                                                                <?=$category['name'] ?? '';?></small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-end">
                                                    <span data-bs-toggle="tooltip"
                                                        title="<?=__('Giá của số lượng').' '.getServiceTypeFromMapping($row['type'])['quantity_unit'];?>"
                                                        class="<?=$priceClass;?>"><?=format_currency($row['price'] * getServiceTypeFromMapping($row['type'])['quantity_unit']);?></span>
                                                </td>
                                                <?php if(getRankStatusByTarget('price_1') == 1): ?>
                                                <td class="text-end">
                                                    <span data-bs-toggle="tooltip"
                                                        title="<?=__('Giá của số lượng').' '.getServiceTypeFromMapping($row['type'])['quantity_unit'];?>"
                                                        class="<?=$price1Class;?>"><?=format_currency($row['price_1'] * getServiceTypeFromMapping($row['type'])['quantity_unit']);?></span>
                                                </td>
                                                <?php endif; ?>
                                                <?php if(getRankStatusByTarget('price_2') == 1): ?>
                                                <td class="text-end">
                                                    <span data-bs-toggle="tooltip"
                                                        title="<?=__('Giá của số lượng').' '.getServiceTypeFromMapping($row['type'])['quantity_unit'];?>"
                                                        class="<?=$price2Class;?>"><?=format_currency($row['price_2'] * getServiceTypeFromMapping($row['type'])['quantity_unit']);?></span>
                                                </td>
                                                <?php endif; ?>
                                                <?php if(getRankStatusByTarget('price_3') == 1): ?>
                                                <td class="text-end">
                                                    <span data-bs-toggle="tooltip"
                                                        title="<?=__('Giá của số lượng').' '.getServiceTypeFromMapping($row['type'])['quantity_unit'];?>"
                                                        class="<?=$price3Class;?>"><?=format_currency($row['price_3'] * getServiceTypeFromMapping($row['type'])['quantity_unit']);?></span>
                                                </td>
                                                <?php endif; ?>
                                                <td class="text-center">
                                                    <span class="text-muted">-</span>
                                                </td>
                                                <td class="text-center">
                                                    <a href="<?=base_url('service/'.$platform['slug'].'/'.$category['slug'].'/'.$row['id']);?>"
                                                        class="btn btn-sm btn-primary">
                                                        <i class="ri-shopping-cart-line me-1"></i><?=__('Mua ngay');?>
                                                    </a>

                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?=$urlDatatable;?>
                            </div>
                        </div>
                    </div>

                </div>
                <!--end col-->
            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->





    <!-- Modal Mô tả dịch vụ -->
    <div class="modal fade" id="serviceDescriptionModal" tabindex="-1" aria-labelledby="serviceDescriptionModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="serviceDescriptionModalLabel"><?=__('Mô tả dịch vụ');?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <h6 class="fw-semibold mb-3" id="serviceName"></h6>
                    <div id="serviceDescription" class="text-muted"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                </div>
            </div>
        </div>
    </div>

    <?php
require_once(__DIR__.'/footer.php');
?>


    <script>
    // Format option với hình ảnh cho select2
    function formatOptionWithImage(option) {
        if (!option.id) {
            return option.text;
        }
        var img = $(option.element).data('image');
        if (img) {
            return $('<span><img src="' + img +
                '" style="width:20px;height:20px;object-fit:cover;border-radius:3px;margin-right:8px;">' + option
                .text + '</span>');
        }
        return option.text;
    }

    // Hiển thị modal mô tả dịch vụ
    function showServiceDescription(serviceName, description) {
        document.getElementById('serviceName').textContent = serviceName;
        document.getElementById('serviceDescription').innerHTML = description;

        var modal = new bootstrap.Modal(document.getElementById('serviceDescriptionModal'));
        modal.show();
    }

    $(document).ready(function() {
        // Khởi tạo tooltip
        $('[data-bs-toggle="tooltip"]').tooltip();

        // Khởi tạo Select2 cho platform
        $('#platform').select2({
            templateResult: formatOptionWithImage,
            templateSelection: formatOptionWithImage,
            escapeMarkup: function(m) {
                return m;
            }
        });

        // Khởi tạo Select2 cho category
        $('#category').select2();

        // Xử lý sự kiện thay đổi platform
        $('#platform').on('change', function() {
            var platformId = $(this).val();


            // Reset category dropdown
            $('#category').empty();
            $('#category').append('<option value=""><?=__('Tất cả phân loại');?></option>');

            if (platformId && platformId !== '') {
                // Thêm categories cho platform này
                var added = 0;
                $('#categories-storage option').each(function() {
                    if ($(this).data('platform') == platformId) {
                        var value = $(this).attr('value');
                        var text = $(this).text();
                        $('#category').append('<option value="' + value + '">' + text +
                            '</option>');
                        added++;
                    }
                });
            }

            // Reset giá trị
            $('#category').val('').trigger('change');
        });

        // Load categories ban đầu nếu có platform được chọn
        var initialPlatform = $('#platform').val();
        if (initialPlatform) {
            $('#platform').trigger('change');
        }



        // Helper function to format currency
        function formatCurrency(amount) {
            return new Intl.NumberFormat('vi-VN', {
                style: 'currency',
                currency: 'VND',
                minimumFractionDigits: 0
            }).format(amount).replace('₫', 'đ');
        }
    });
    </script>