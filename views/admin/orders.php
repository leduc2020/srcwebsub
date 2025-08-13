<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Đơn hàng').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    /* CSS cho hàng được chọn */
    .table tr.selected {
        background-color: rgba(0, 94, 234, 0.08) !important;
        position: relative;
        outline: 1px solid #0d6efd !important;
        outline-offset: -1px;
    }

    .table tr.selected td {
        border-color: rgba(13, 110, 253, 0.2) !important;
        color: rgba(0, 0, 0, 0.7);
    }

    .table tr.selected:hover {
        background-color: rgba(13, 110, 253, 0.12) !important;
    }

    .table tr.selected td:first-child {
        border-left: 2px solid #0d6efd !important;
    }

    [data-theme-mode="dark"] .table tr.selected {
        background-color: rgba(13, 110, 253, 0.15) !important;
        outline: 1px solid #3384ff !important;
        color: rgba(255, 255, 255, 0.7);
    }

    [data-theme-mode="dark"] .table tr.selected td {
        border-color: rgba(13, 110, 253, 0.3) !important;
        color: rgba(255, 255, 255, 0.7);
    }
    
    /* Loading overlay */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.active {
        display: flex;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Hỗ trợ cho các nút bulk action */
    #bulk-action-buttons {
        transition: all 0.3s ease;
    }
    
    #selected-counter {
        font-size: 13px;
        padding: 3px 8px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 4px;
    }
    
    /* CSS cho dropdown menu có submenu */
    .dropdown-menu {
        animation: fadeInDown 0.3s ease-in-out;
        border: none !important;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.15) !important;
        min-width: 200px;
    }
    
    .dropdown-item {
        padding: 8px 16px;
        transition: all 0.2s ease;
        border-radius: 4px;
        margin: 2px 4px;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        text-align: left;
    }
    
    .dropdown-item span {
        flex: 1;
        text-align: left;
        margin-left: 4px;
    }
    
    .dropdown-item:hover {
        background-color: rgba(13, 110, 253, 0.1) !important;
        transform: translateX(2px);
    }
    
    .dropdown-item.has-submenu .fa-chevron-right {
        font-size: 10px;
        color: #666;
        transition: transform 0.2s ease;
        margin-left: auto;
        margin-right: 0;
    }
    
    .dropdown-submenu:hover .fa-chevron-right {
        transform: rotate(90deg);
    }
    
    /* Submenu styles */
    .dropdown-submenu {
        position: relative;
    }
    
    .dropdown-submenu .dropdown-menu {
        position: absolute !important;
        top: 0;
        left: 100%;
        margin-top: 0;
        margin-left: 2px;
        border-radius: 6px;
        display: none;
        animation: fadeInRight 0.2s ease-in-out;
    }
    
    .dropdown-submenu:hover > .dropdown-menu {
        display: block;
    }
    
    .dropdown-submenu .dropdown-item {
        padding: 6px 12px;
        font-size: 13px;
        text-align: left;
        justify-content: flex-start;
    }
    
    .dropdown-submenu .dropdown-item i {
        width: 16px;
        text-align: center;
        margin-right: 8px;
    }
    
    /* Icon spacing cho tất cả dropdown items */
    .dropdown-item i {
        width: 18px;
        text-align: center;
        margin-right: 8px;
    }
    
    @keyframes fadeInDown {
        0% {
            opacity: 0;
            transform: translateY(-10px);
        }
        100% {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    @keyframes fadeInRight {
        0% {
            opacity: 0;
            transform: translateX(-10px);
        }
        100% {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    /* Dark mode support */
    [data-theme-mode="dark"] .dropdown-menu {
        background-color: #1a1d29 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item {
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item:focus {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item.has-submenu .fa-chevron-right {
        color: #ccc !important;
    }
    
    [data-theme-mode="dark"] .dropdown-submenu .dropdown-menu {
        background-color: #1a1d29 !important;
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }
    
    [data-theme-mode="dark"] .dropdown-submenu .dropdown-item {
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-submenu .dropdown-item:hover {
        background-color: rgba(255, 255, 255, 0.1) !important;
        color: #fff !important;
    }
    
    [data-theme-mode="dark"] .dropdown-divider {
        border-color: rgba(255, 255, 255, 0.1) !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item i {
        color: inherit !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item.text-danger {
        color: #ff6b6b !important;
    }
    
    [data-theme-mode="dark"] .dropdown-item.text-danger:hover {
        color: #ff5252 !important;
        background-color: rgba(255, 107, 107, 0.1) !important;
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
if(checkPermission($getUser['admin'], 'view_orders_product') != true){
    die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
}
if(isset($_GET['limit'])){
    $limit = intval(check_string($_GET['limit']));
}else{
    $limit = 10;
}
if(isset($_GET['page'])){
    $page = check_string(intval($_GET['page']));
}
else{
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `id` > 0 ";
$user_id = '';
$username = '';
$created_at = '';
$trans_id = '';
$shortByDate  = '';
$supplier_id = '';
$api_transid = '';
$service_id = '';
$link = '';
$status = '';
if (!empty($_GET['link'])) {
    $link = check_string($_GET['link']);
    $product_sold_rows = $CMSNT->get_list('SELECT * FROM `orders` WHERE `link` = "'.$link.'" ');
    if (!empty($product_sold_rows)) {
        $trans_ids = array_map(function($row) {
            return $row['trans_id'];
        }, $product_sold_rows);
        $trans_ids_str = implode('","', $trans_ids);
        $where .= ' AND `trans_id` IN ("'.$trans_ids_str.'") ';
    }
}
if(!empty($_GET['service_id'])){
    $service_id = check_string($_GET['service_id']);
    $where .= ' AND `service_id` = "'.$service_id.'" ';
}
if(!empty($_GET['api_transid'])){
    $api_transid = check_string($_GET['api_transid']);
    $where .= ' AND `order_id` LIKE "%'.$api_transid.'%" ';
}
if(!empty($_GET['supplier_id'])){
    $supplier_id = check_string($_GET['supplier_id']);
    $supplier_id_value = $supplier_id == 'none' ? 0 : $supplier_id;
    $where .= ' AND `supplier_id` = "'.$supplier_id_value.'" ';
}
if (!empty($_GET['username'])) {
    $username = check_string($_GET['username']);
    if($idUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `username` = '$username' ")){
        $where .= ' AND `user_id` =  "'.$idUser['id'].'" ';
    }else{
        $where .= ' AND `user_id` =  "" ';
    }
}
if(!empty($_GET['user_id'])){
    $user_id = check_string($_GET['user_id']);
    $where .= ' AND `user_id` = "'.$user_id.'" ';
}
if(!empty($_GET['trans_id'])){
    $trans_id = check_string($_GET['trans_id']);
    // Kiểm tra xem có phải nhiều mã đơn hàng không (phân tách bằng dấu phẩy)
    if(strpos($trans_id, ',') !== false) {
        // Tách các mã đơn hàng bằng dấu phẩy và loại bỏ khoảng trắng
        $trans_ids = array_map('trim', explode(',', $trans_id));
        $trans_ids = array_filter($trans_ids); // Loại bỏ phần tử rỗng
        if(!empty($trans_ids)) {
            $trans_ids_str = implode('","', $trans_ids);
            $where .= ' AND `trans_id` IN ("'.$trans_ids_str.'") ';
        }
    } else {
        // Tìm kiếm một mã đơn hàng như trước
        $where .= ' AND `trans_id` LIKE "%'.$trans_id.'%" ';
    }
}
if(!empty($_GET['created_at'])){
    $created_at = check_string($_GET['created_at']);
    $createdate = $created_at;
    $created_at_1 = str_replace('-', '/', $created_at);
    $created_at_1 = explode(' to ', $created_at_1);

    if($created_at_1[0] != $created_at_1[1]){
        $created_at_1 = [$created_at_1[0].' 00:00:00', $created_at_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$created_at_1[0]."' AND `created_at` <= '".$created_at_1[1]."' ";
    }
}
if(isset($_GET['shortByDate'])){
    $shortByDate = check_string($_GET['shortByDate']);
    $yesterday = date('Y-m-d', strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date('m');
    $currentYear = date('Y');
    $currentDate = date("Y-m-d");
    if($shortByDate == 1){
        $where .= " AND `created_at` LIKE '%".$currentDate."%' ";
    }
    if($shortByDate == 2){
        $where .= " AND YEAR(created_at) = $currentYear AND WEEK(created_at, 1) = $currentWeek ";
    }
    if($shortByDate == 3){
        $where .= " AND MONTH(created_at) = '$currentMonth' AND YEAR(created_at) = '$currentYear' ";
    }
    if($shortByDate == 4){
        $where .= " AND DATE(created_at) = '$yesterday' ";
    }
}
if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "'.$status.'" ';
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `orders` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `orders` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination(base_url_admin("orders&limit=$limit&shortByDate=$shortByDate&user_id=$user_id&trans_id=$trans_id&created_at=$created_at&username=$username&supplier_id=$supplier_id&api_transid=$api_transid&service_id=$service_id&link=$link&status=$status&"), $from, $totalDatatable, $limit);

?>



<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-cart-shopping"></i> <?=__('Đơn hàng');?>
            </h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">



                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="<?= base_url(); ?>" class="align-items-center mb-3" name="formSearch"
                            method="GET">
                            <div class="row g-2 mb-3">
                                <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                <input type="hidden" name="action" value="orders">
                                <input type="hidden" value="<?=$getUser['token'];?>" id="token">
                                <div class="col-md-3 col-6">
                                    <input class="form-control" value="<?=$user_id;?>" name="user_id"
                                        placeholder="ID User">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control" value="<?=$username;?>" name="username"
                                        placeholder="Username">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control" value="<?=$trans_id;?>" name="trans_id"
                                        placeholder="Mã đơn hàng (cách nhau bởi dấu ,)">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control" value="<?=$link;?>" name="link"
                                        placeholder="<?=__('Liên kết');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control" value="<?=$api_transid;?>" name="api_transid"
                                        placeholder="<?=__('Mã đơn hàng API');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select class="form-control js-example-basic-single" name="supplier_id">
                                        <option value=""><?=__('-- API Supplier --');?></option>
                                        <option value="none" <?=$supplier_id == 'none' ? 'selected' : '';?>>
                                            <?=__('Dịch vụ hệ thống');?></option>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `suppliers` ") as $supplier):?>
                                        <option <?=$supplier_id == $supplier['id'] ? 'selected' : '';?>
                                            value="<?=$supplier['id'];?>"><?=$supplier['domain'];?></option>
                                        <?php endforeach?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <select class="form-control js-example-basic-single" name="service_id">
                                        <option value=""><?=__('-- Dịch Vụ -- ');?></option>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `services` ") as $product):?>
                                        <option <?=$service_id == $product['id'] ? 'selected' : '';?>
                                            value="<?=$product['id'];?>">
                                            <?=$product['id'];?> - <?=$product['name'];?>
                                        </option>
                                        <?php endforeach?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <select class="form-control js-example-basic-single" name="status">
                                        <option value=""><?=__('-- Trạng thái -- ');?></option>
                                        <?php foreach($config_status_order as $key => $value):?>
                                        <option <?=$status == $key ? 'selected' : '';?>
                                            value="<?=$key;?>">
                                            <?=$value;?>
                                        </option>
                                        <?php endforeach?>
                                    </select>
                                </div>
                                <div class="col-md-3 col-6">
                                    <input type="text" name="created_at" class="form-control" id="daterange"
                                        value="<?=$created_at;?>" placeholder="<?=__('Chọn thời gian');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <button class="btn btn-hero btn-primary"><i class="fa fa-search"></i>
                                        <?=__('Tìm kiếm');?>
                                    </button>
                                    <a class="btn btn-hero btn-danger" href="<?=base_url_admin('orders');?>"><i
                                            class="fa fa-trash"></i>
                                        <?=__('Xóa bộ lọc');?>
                                    </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                        <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                        <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                        <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                        <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                        <option <?=$limit == 500 ? 'selected' : '';?> value="500">500</option>
                                        <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label"><?=__('Short by Date:');?></label>
                                    <select name="shortByDate" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option value=""><?=__('Tất cả');?></option>
                                        <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1"><?=__('Hôm nay');?>
                                        </option>
                                        <option <?=$shortByDate == 4 ? 'selected' : '';?> value="4"><?=__('Hôm qua');?>
                                        </option>
                                        <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2"><?=__('Tuần này');?>
                                        </option>
                                        <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3">
                                            <?=__('Tháng này');?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>

                        <!-- Nút hành động hàng loạt -->
                        <div class="d-flex mb-3">
                            <div class="btn-list" id="bulk-action-buttons" style="display: none;">
                                <div class="dropdown">
                                    <button type="button"
                                        class="btn btn-outline-primary shadow-primary btn-wave btn-sm dropdown-toggle"
                                        data-bs-toggle="dropdown" aria-expanded="false" id="btn_thao_tac_nhanh">
                                        <i class="fa-solid fa-cog"></i> <?=__('Thao tác nhanh');?>
                                    </button>
                                    <ul class="dropdown-menu shadow-lg border-0">
                                        <!-- Chuyển trạng thái -->
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item has-submenu" href="javascript:void(0);">
                                                <i class="fa-solid fa-exchange-alt text-primary me-2"></i>
                                                <span><?=__('Chuyển trạng thái');?></span>
                                                <i class="fa-solid fa-chevron-right ms-auto"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStatus('Processing')">
                                                        <i class="fa-solid fa-spinner text-warning me-2"></i>
                                                        <?=__('Đang xử lý');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStatus('In progress')">
                                                        <i class="fa-solid fa-clock text-info me-2"></i>
                                                        <?=__('Đang tiến hành');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStatus('Completed')">
                                                        <i class="fa-solid fa-check-circle text-success me-2"></i>
                                                        <?=__('Hoàn thành');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStatus('Partial')">
                                                        <i
                                                            class="fa-solid fa-exclamation-triangle text-warning me-2"></i>
                                                        <?=__('Hoàn thành một phần');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStatus('Canceled')">
                                                        <i class="fa-solid fa-times-circle text-danger me-2"></i>
                                                        <?=__('Hủy đơn hàng');?>
                                                    </a></li>
                                            </ul>
                                        </li>

                                        <!-- Cập nhật thông tin -->
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item has-submenu" href="javascript:void(0);">
                                                <i class="fa-solid fa-edit text-success me-2"></i>
                                                <span><?=__('Cập nhật thông tin');?></span>
                                                <i class="fa-solid fa-chevron-right ms-auto"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateNote()">
                                                        <i class="fa-solid fa-comment-dots text-info me-2"></i>
                                                        <?=__('Ghi chú');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateStartCount()">
                                                        <i class="fa-solid fa-play-circle text-primary me-2"></i>
                                                        <?=__('Số lượng ban đầu');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="quickUpdateRemains()">
                                                        <i class="fa-solid fa-calculator text-warning me-2"></i>
                                                        <?=__('Số lượng còn lại');?>
                                                    </a></li>
                                            </ul>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <!-- Sao chép -->
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item has-submenu" href="javascript:void(0);">
                                                <i class="fa-solid fa-copy text-info me-2"></i>
                                                <span><?=__('Sao chép');?></span>
                                                <i class="fa-solid fa-chevron-right ms-auto"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="copyOrderData('trans_id')">
                                                        <i class="fa-solid fa-hashtag text-primary me-2"></i>
                                                        <?=__('Mã đơn hàng');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="copyOrderData('order_id')">
                                                        <i class="fa-solid fa-code text-success me-2"></i>
                                                        <?=__('Mã đơn hàng API');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="copyOrderData('link')">
                                                        <i class="fa-solid fa-link text-warning me-2"></i>
                                                        <?=__('Liên kết');?>
                                                    </a></li>
                                            </ul>
                                        </li>

                                        <!-- Export -->
                                        <li class="dropdown-submenu">
                                            <a class="dropdown-item has-submenu" href="javascript:void(0);">
                                                <i class="fa-solid fa-download text-success me-2"></i>
                                                <span><?=__('Export');?></span>
                                                <i class="fa-solid fa-chevron-right ms-auto"></i>
                                            </a>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="exportOrderData('csv')">
                                                        <i class="fa-solid fa-file-csv text-success me-2"></i>
                                                        <?=__('Xuất CSV');?>
                                                    </a></li>
                                                <li><a class="dropdown-item" href="javascript:void(0);"
                                                        onclick="exportOrderData('txt')">
                                                        <i class="fa-solid fa-file-alt text-primary me-2"></i>
                                                        <?=__('Xuất TXT');?>
                                                    </a></li>
                                            </ul>
                                        </li>

                                        <li>
                                            <hr class="dropdown-divider">
                                        </li>

                                        <!-- Xóa đơn hàng -->
                                        <li><a class="dropdown-item text-danger" href="javascript:void(0);"
                                                onclick="deleteSelectedOrders()">
                                                <i class="fa-solid fa-trash text-danger me-2"></i>
                                                <?=__('Xóa đơn hàng');?>
                                            </a></li>

                                    </ul>
                                </div>
                                <span id="selected-counter" class="ms-2 align-self-center text-primary"></span>
                            </div>
                            <div class="ms-auto">
                                <button id="select-all-btn" type="button" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-check-double"></i> <?=__('Chọn tất cả');?>
                                </button>
                                <button id="deselect-all-btn" type="button" class="btn btn-sm btn-outline-secondary">
                                    <i class="fa-solid fa-xmark"></i> <?=__('Bỏ chọn tất cả');?>
                                </button>
                            </div>
                        </div>

                        <div class="table-responsive table-wrapper mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input" name="check_all"
                                                    id="check_all_checkbox_product" value="option1">
                                            </div>
                                        </th>
                                        <th class="text-center"><?=__('Thao tác');?></th>
                                        <th class="text-center"><?=__('Username');?></th>
                                        <th class="text-center"><?=__('Đơn hàng');?></th>
                                        <th class="text-center"><?=__('Thanh toán');?></th>
                                        <th class="text-center"><?=__('Trạng thái');?></th>
                                        <th class="text-center"><?=__('Dịch vụ');?></th>
                                        <th class="text-center"><?=__('Liên kết');?></th>
                                        <th class="text-center"><?=__('Bình luận');?></th>
                                        <th class="text-center"><?=__('Ban đầu');?></th>
                                        <th class="text-center"><?=__('Còn lại');?></th>
                                        <th class="text-center"><?=__('Thời gian');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $order): ?>
                                    <tr>
                                        <td class="text-center">
                                            <div class="form-check form-check-md d-flex align-items-center">
                                                <input type="checkbox" class="form-check-input checkbox_product"
                                                    data-id="<?=$order['id'];?>"
                                                    data-trans-id="<?=$order['trans_id'];?>"
                                                    data-order-id="<?=$order['order_id'] ? $order['order_id'] : '';?>"
                                                    data-link="<?=isset($order['link']) && !empty($order['link']) ? htmlspecialchars($order['link']) : '';?>"
                                                    name="checkbox_product" value="<?=$order['id'];?>" />
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <a class="btn btn-info btn-sm shadow-info btn-wave" id="btnViewOrder"
                                                href="<?=base_url_admin('order-edit&id='.$order['id']);?>"
                                                data-toggle="tooltip" type="button"><i class="fa-solid fa-edit"></i></a>
                                            <button type="button" onclick="deleteOrder(`<?=$order['id'];?>`)"
                                                id="btnDeleteOrder<?=$order['id'];?>"
                                                class="btn btn-danger btn-sm shadow-danger btn-wave">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            <?php if($order['user_id'] > 0): ?>
                                            <a class="text-primary"
                                                href="<?=base_url_admin('user-edit&id='.$order['user_id']);?>"><?=getRowRealtime('users', $order['user_id'], 'username');?>
                                                [ID <?=$order['user_id'];?>]</a>
                                            <?php else: ?>
                                            <span class="text-muted"><?=__('Hệ thống');?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?=__('Mã đơn hàng:');?> <strong>#<?=$order['trans_id'];?></strong><br>
                                            <?=__('Mã đơn hàng API (nếu có):');?>
                                            <strong>#<?=$order['order_id'] ? $order['order_id'] : 'N/A';?></strong><br>
                                            <?=__('Server API (nếu có):');?> <?php if($order['supplier_id'] > 0):?><a
                                                class="font-weight-bold"
                                                href="<?=base_url_admin('supplier-edit&id='.$order['supplier_id']);?>"><?=getRowRealtime('suppliers', $order['supplier_id'], 'domain');?></a>
                                            <?php endif?>
                                        </td>
                                        <td>
                                            <?=__('Số lượng:');?>
                                            <strong><?=format_cash($order['quantity']);?></strong><br>
                                            <?=__('Thanh toán:');?> <strong
                                                style="color:red;"><?=format_currency($order['pay']);?></strong><br>
                                            <?=__('Giá vốn:');?> <strong
                                                style="color:blue;"><?=format_currency($order['cost']);?></strong>
                                            - <?=__('Lãi:');?> <strong
                                                style="color:green;"><?=format_currency($order['pay']-$order['cost']);?></strong>
                                        </td>
                                        <td class="text-center">
                                            <?=display_service($order['status']);?>
                                        </td>
                                        <td class="text-left">
                                            <span class="badge bg-outline-info">ID: <?=$order['service_id'];?></span>
                                            <a class="text-primary"
                                                href="<?=base_url_admin('service-edit&id='.$order['service_id']);?>">
                                                <?=$order['service_name'];?>
                                            </a>
                                        </td>
                                        <td class="text-center">
                                            <?php if(isset($order['link'])): ?>
                                            <input type="text" class="form-control copy-link" data-toggle="tooltip"
                                                data-placement="bottom" title="<?=$order['link'];?>"
                                                value="<?=$order['link'];?>" readonly>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <?php if(isset($order['comment']) && !empty($order['comment'])): ?>
                                            <textarea class="form-control copy-comment" rows="1"
                                                readonly><?=$order['comment'];?></textarea>
                                            <?php else: ?>
                                            <span class="text-muted">N/A</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end text-info">
                                            <?=isset($order['start_count']) ? format_cash($order['start_count']) : 'N/A';?>
                                        </td>
                                        <td class="text-end text-danger">
                                            <?=isset($order['remains']) ? format_cash($order['remains']) : 'N/A';?>
                                        </td>
                                        <td>
                                            <?=__('Thời gian tạo:');?> <span class="badge bg-light text-body"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="<?=timeAgo(strtotime($order['created_at']));?>">
                                                <?=$order['created_at'];?>
                                            </span>
                                            <br>
                                            <?=__('Cập nhật:');?> <span class="badge bg-light text-body"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="<?=timeAgo(strtotime($order['updated_at']));?>">
                                                <?=$order['updated_at'];?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="11">
                                            <?=__('Tổng số đơn hàng:');?>
                                            <strong><?=format_cash($totalDatatable);?></strong> | <?=__('Doanh thu:');?>
                                            <strong style="color:red;"><?=format_currency($CMSNT->get_row(" SELECT SUM(pay) FROM orders WHERE $where ")['SUM(pay)']);?></strong>
                                            | <?=__('Lợi nhuận:');?>
                                            <strong style="color:green;"><?=format_currency($CMSNT->get_row(" SELECT SUM(pay-cost) FROM orders WHERE $where ")['SUM(pay-cost)']);?></strong>

                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info">Showing <?=$limit;?> of <?=format_cash($totalDatatable);?>
                                    Results</p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?=$totalDatatable > $limit ? $urlDatatable : '';?>
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



<!-- Loading overlay -->
<div class="loading-overlay" id="loading-overlay">
    <div class="loading-spinner"></div>
</div>

<script type="text/javascript">
new ClipboardJS(".copy");

function copy() {
    showMessage("<?=__('Đã sao chép vào bộ nhớ tạm');?>", 'success');
}
</script>

<script>
// Script để copy nội dung khi click vào input hoặc textarea
document.addEventListener('DOMContentLoaded', function() {
    // Xử lý sự kiện click cho các input có class copy-link
    document.querySelectorAll('.copy-link, .copy-comment').forEach(function(element) {
        element.addEventListener('click', function() {
            this.select(); // Chọn toàn bộ nội dung
            document.execCommand('copy'); // Copy vào clipboard
            showMessage("<?=__('Đã sao chép vào bộ nhớ tạm');?>", 'success');
        });
    });
});

// Hàm hiển thị nội dung đầy đủ
function showMore(id) {
    document.getElementById('short-' + id).style.display = 'none';
    document.getElementById('full-' + id).style.display = 'inline';
}

// Hàm ẩn nội dung đầy đủ
function showLess(id) {
    document.getElementById('short-' + id).style.display = 'inline';
    document.getElementById('full-' + id).style.display = 'none';
}
</script>

<script>
// JavaScript xử lý chức năng cập nhật nhanh
$(function() {
    // Checkbox "check all"
    $('#check_all_checkbox_product').on('click', function() {
        $('.checkbox_product').prop('checked', this.checked);
        updateSelectedRows();
    });

    // Chọn/bỏ chọn hàng khi click vào checkbox
    $(document).on('change', '.checkbox_product', function() {
        updateSelectedRows();
    });

    // Nút chọn tất cả
    $('#select-all-btn').on('click', function() {
        $('.checkbox_product').prop('checked', true);
        updateSelectedRows();
    });

    // Nút bỏ chọn tất cả
    $('#deselect-all-btn').on('click', function() {
        $('.checkbox_product').prop('checked', false);
        updateSelectedRows();
    });

    function updateSelectedRows() {
        // Highlight các hàng được chọn
        $('.checkbox_product').each(function() {
            if ($(this).prop('checked')) {
                $(this).closest('tr').addClass('selected');
            } else {
                $(this).closest('tr').removeClass('selected');
            }
        });

        // Cập nhật số lượng đã chọn
        var count = $('.checkbox_product:checked').length;

        // Hiển thị/ẩn các nút hành động hàng loạt
        if (count > 0) {
            $('#bulk-action-buttons').fadeIn(200);
            $('#selected-counter').text(count + ' ' + (count == 1 ? '<?=__('đơn hàng');?>' :
                '<?=__('đơn hàng');?>') + ' <?=__('đã chọn');?>');
        } else {
            $('#bulk-action-buttons').fadeOut(200);
            $('#selected-counter').text('');
        }
    }



    // Xử lý checkbox chọn tất cả trong bảng
    $('#check_all_checkbox_product').on('change', function() {
        $('.checkbox_product').prop('checked', this.checked);
        updateSelectedRows();
    });
});

// Hàm cập nhật nhanh trạng thái
function quickUpdateStatus(status) {
    var selectedOrders = $('.checkbox_product:checked');
    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng để cập nhật');?>', 'error');
        return;
    }

    var statusText = '';
    var alertType = 'info';
    var alertMessage = '';

    switch (status) {
        case 'Processing':
            statusText = '<?=__('Đang xử lý');?>';
            break;
        case 'In progress':
            statusText = '<?=__('Đang tiến hành');?>';
            break;
        case 'Completed':
            statusText = '<?=__('Hoàn thành');?>';
            break;
        case 'Partial':
            statusText = '<?=__('Hoàn thành một phần');?>';
            alertType = 'warning';
            alertMessage = '<?=__('Lưu ý: Trạng thái Partial sẽ yêu cầu nhập số lượng còn lại (Remains)');?>';
            break;
        case 'Canceled':
            statusText = '<?=__('Hủy đơn hàng');?>';
            alertType = 'warning';
            alertMessage = '<?=__('Cảnh báo: Hệ thống sẽ tự động hoàn tiền toàn bộ cho đơn hàng khi hủy');?>';
            break;
    }

    // Nếu là trạng thái Partial, hiển thị modal với input Remains ngay
    if (status === 'Partial') {
        Swal.fire({
            title: '<?=__('Cập nhật trạng thái Partial');?>',
            html: `
                <div class="text-start">
                    <div class="alert alert-warning mb-3">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong><?=__('Cập nhật');?> ${selectedOrders.length} <?=__('đơn hàng thành trạng thái');?> "${statusText}"</strong>
                        <br><small>${alertMessage}</small>
                    </div>
                    <label for="remainsValue" class="form-label"><?=__('Số lượng còn lại (Remains)');?> <span class="text-danger">*</span></label>
                    <input type="number" id="remainsValue" class="form-control" min="0" placeholder="<?=__('Nhập số lượng còn lại');?>">
                    <small class="text-muted mt-1 d-block"><?=__('Hệ thống sẽ tự động hoàn tiền cho số lượng chưa hoàn thành');?></small>
                </div>
            `,
            icon: 'warning',
            focusConfirm: false,
            showCancelButton: true,
            confirmButtonText: '<?=__('Cập nhật');?>',
            cancelButtonText: '<?=__('Hủy');?>',
            preConfirm: () => {
                const remains = document.getElementById('remainsValue').value;
                if (!remains || isNaN(remains) || parseInt(remains) < 0) {
                    Swal.showValidationMessage('<?=__('Vui lòng nhập số lượng còn lại hợp lệ');?>');
                    return false;
                }
                return parseInt(remains);
            }
        }).then((result) => {
            if (result.isConfirmed) {
                executeQuickUpdate(selectedOrders, status, result.value);
            }
        });
        return;
    }

    // Cho các trạng thái khác, hiển thị xác nhận thông thường
    var confirmMessage = '<?=__('Bạn có muốn cập nhật');?> ' + selectedOrders.length +
        ' <?=__('đơn hàng thành trạng thái');?> "' + statusText + '"?';

    if (alertMessage) {
        confirmMessage += '\n\n' + alertMessage;
    }

    Swal.fire({
        title: '<?=__('Xác nhận cập nhật');?>',
        text: confirmMessage,
        icon: alertType,
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "<?=__('Đồng ý');?>",
        cancelButtonText: "<?=__('Hủy');?>"
    }).then((result) => {
        if (result.isConfirmed) {
            executeQuickUpdate(selectedOrders, status);
        }
    });
}

// Thực hiện cập nhật nhanh trạng thái
function executeQuickUpdate(selectedOrders, status, remains = null) {
    // Hiển thị loading
    $('#loading-overlay').addClass('active');

    var updateData = {
        field: 'status',
        value: status,
        orderIds: []
    };

    // Thu thập ID của các đơn hàng được chọn
    selectedOrders.each(function() {
        updateData.orderIds.push($(this).data('id'));
    });

    // Thêm remains nếu có
    if (remains !== null) {
        updateData.remains = remains;
    }

    // Gửi dữ liệu cập nhật lên server
    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            token: $("#token").val(),
            action: 'bulkUpdateOrders',
            data: JSON.stringify(updateData)
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, result.status);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showMessage(result.msg || '<?=__('Lỗi không xác định');?>', result.status);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            showMessage('<?=__('Đã xảy ra lỗi khi cập nhật:');?> ' + errorThrown, 'error');
        },
        complete: function() {
            // Ẩn loading
            $('#loading-overlay').removeClass('active');
        }
    });
}

// Hàm xử lý xóa đơn hàng
function deleteOrder(id) {
    Swal.fire({
        title: "<?=__('Bạn có chắc chắn không?');?>",
        text: "<?=__('Bạn sẽ không thể khôi phục lại đơn hàng này!');?>",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "<?=__('Đồng ý, xóa!');?>",
        cancelButtonText: "<?=__('Hủy');?>"
    }).then((result) => {
        if (result.isConfirmed) {
            // Hiển thị loading
            $('#loading-overlay').addClass('active');

            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'removeOrder',
                    id: id,
                    token: $("#token").val()
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        Swal.fire({
                            title: "<?=__('Thành công!');?>",
                            text: respone.msg,
                            icon: "success"
                        }).then((result) => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "<?=__('Thất bại!');?>",
                            text: respone.msg,
                            icon: "error"
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: "<?=__('Thất bại!');?>",
                        text: "<?=__('Đã xảy ra lỗi khi kết nối đến máy chủ');?>",
                        icon: "error"
                    });
                },
                complete: function() {
                    // Ẩn loading
                    $('#loading-overlay').removeClass('active');
                }
            });
        }
    });
}

// Hàm xử lý xóa nhiều đơn hàng một lúc
function delete_records() {
    // Hiển thị loading
    $('#loading-overlay').addClass('active');

    // Thu thập ID của các đơn hàng được chọn
    var ids = [];
    $('.checkbox_product:checked').each(function() {
        ids.push($(this).val());
    });

    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'bulkRemoveOrders',
            ids: JSON.stringify(ids),
            token: $("#token").val()
        },
        success: function(respone) {
            if (respone.status == 'success') {
                Swal.fire({
                    title: "<?=__('Thành công!');?>",
                    text: respone.msg,
                    icon: "success"
                }).then((result) => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    title: "<?=__('Thất bại!');?>",
                    text: respone.msg,
                    icon: "error"
                });
            }
        },
        error: function() {
            Swal.fire({
                title: "<?=__('Thất bại!');?>",
                text: "<?=__('Đã xảy ra lỗi khi kết nối đến máy chủ');?>",
                icon: "error"
            });
        },
        complete: function() {
            // Ẩn loading
            $('#loading-overlay').removeClass('active');
        }
    });
}


// Hàm sao chép ID đơn hàng
function copySelectedOrderIds() {
    var selectedOrders = $('.checkbox_product:checked');
    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    var orderIds = [];
    selectedOrders.each(function() {
        orderIds.push($(this).data('id'));
    });

    // Sao chép vào clipboard
    var textToCopy = orderIds.join(', ');
    if (navigator.clipboard) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showMessage('<?=__('Đã sao chép');?> ' + orderIds.length + ' <?=__('ID đơn hàng');?>', 'success');
        });
    } else {
        // Fallback cho các trình duyệt cũ
        var textArea = document.createElement("textarea");
        textArea.value = textToCopy;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showMessage('<?=__('Đã sao chép');?> ' + orderIds.length + ' <?=__('ID đơn hàng');?>', 'success');
    }
}

// Hàm sao chép dữ liệu đơn hàng theo loại
function copyOrderData(dataType) {
    var selectedOrders = $('.checkbox_product:checked');

    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    var dataList = [];
    var labelText = '';

    selectedOrders.each(function() {
        var element = $(this);
        var data = '';

        switch (dataType) {
            case 'trans_id':
                data = element.attr('data-trans-id');
                labelText = '<?=__('mã đơn hàng');?>';
                break;
            case 'order_id':
                data = element.attr('data-order-id');
                labelText = '<?=__('mã đơn hàng API');?>';
                break;
            case 'link':
                data = element.attr('data-link');
                labelText = '<?=__('liên kết');?>';
                break;
            default:
                data = element.attr('data-id');
                labelText = '<?=__('ID đơn hàng');?>';
        }

        // Chỉ thêm dữ liệu không rỗng
        if (data && data.trim() !== '') {
            dataList.push(data.trim());
        }
    });

    if (dataList.length === 0) {
        showMessage('<?=__('Không có dữ liệu');?> ' + labelText + ' <?=__('để sao chép');?>', 'warning');
        return;
    }

    // Tạo text để sao chép - mỗi dữ liệu một dòng
    var textToCopy = dataList.join('\n');

    // Sao chép vào clipboard
    if (navigator.clipboard) {
        navigator.clipboard.writeText(textToCopy).then(function() {
            showMessage('<?=__('Đã sao chép');?> ' + dataList.length + ' ' + labelText, 'success');
        }).catch(function(err) {
            fallbackCopyTextToClipboard(textToCopy, dataList.length, labelText);
        });
    } else {
        // Fallback cho các trình duyệt cũ
        fallbackCopyTextToClipboard(textToCopy, dataList.length, labelText);
    }
}

// Hàm fallback để sao chép text
function fallbackCopyTextToClipboard(text, count, label) {
    var textArea = document.createElement("textarea");
    textArea.value = text;
    textArea.style.position = "fixed";
    textArea.style.top = "-1000px";
    textArea.style.left = "-1000px";
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();

    try {
        var successful = document.execCommand('copy');
        if (successful) {
            showMessage('<?=__('Đã sao chép');?> ' + count + ' ' + label, 'success');
        } else {
            showMessage('<?=__('Không thể sao chép');?>', 'error');
        }
    } catch (err) {
        console.error('<?=__('Lỗi fallback sao chép');?>: ', err);
        showMessage('<?=__('Không thể sao chép');?>', 'error');
    }

    document.body.removeChild(textArea);
}


// Hàm cập nhật ghi chú hàng loạt
function quickUpdateNote() {
    var selectedOrders = $('.checkbox_product:checked');
    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    Swal.fire({
        title: '<?=__('Cập nhật ghi chú');?>',
        html: `
            <div class="text-start">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong><?=__('Cập nhật ghi chú cho');?> ${selectedOrders.length} <?=__('đơn hàng');?></strong>
                </div>
                <label for="noteValue" class="form-label"><?=__('Ghi chú mới');?></label>
                <textarea id="noteValue" class="form-control" rows="3" placeholder="<?=__('Nhập ghi chú cho đơn hàng');?>"></textarea>
            </div>
        `,
        icon: 'question',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<?=__('Cập nhật');?>',
        cancelButtonText: '<?=__('Hủy');?>',
        preConfirm: () => {
            const note = document.getElementById('noteValue').value.trim();
            if (!note) {
                Swal.showValidationMessage('<?=__('Vui lòng nhập ghi chú');?>');
                return false;
            }
            return note;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            executeQuickUpdateField(selectedOrders, 'note', result.value);
        }
    });
}

// Hàm cập nhật Start Count hàng loạt
function quickUpdateStartCount() {
    var selectedOrders = $('.checkbox_product:checked');
    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    Swal.fire({
        title: '<?=__('Cập nhật Start Count');?>',
        html: `
            <div class="text-start">
                <div class="alert alert-info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong><?=__('Cập nhật Start Count cho');?> ${selectedOrders.length} <?=__('đơn hàng');?></strong>
                </div>
                <label for="startCountValue" class="form-label"><?=__('Start Count mới');?> <span class="text-danger">*</span></label>
                <input type="number" id="startCountValue" class="form-control" min="0" placeholder="<?=__('Nhập Start Count');?>">
            </div>
        `,
        icon: 'question',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<?=__('Cập nhật');?>',
        cancelButtonText: '<?=__('Hủy');?>',
        preConfirm: () => {
            const startCount = document.getElementById('startCountValue').value;
            if (!startCount || isNaN(startCount) || parseInt(startCount) < 0) {
                Swal.showValidationMessage('<?=__('Vui lòng nhập Start Count hợp lệ');?>');
                return false;
            }
            return parseInt(startCount);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            executeQuickUpdateField(selectedOrders, 'start_count', result.value);
        }
    });
}

// Hàm cập nhật Remains hàng loạt
function quickUpdateRemains() {
    var selectedOrders = $('.checkbox_product:checked');
    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    Swal.fire({
        title: '<?=__('Cập nhật Remains');?>',
        html: `
            <div class="text-start">
                <div class="alert alert-warning mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong><?=__('Cập nhật Remains cho');?> ${selectedOrders.length} <?=__('đơn hàng');?></strong>
                    <br><small><?=__('Lưu ý: Việc cập nhật Remains có thể ảnh hưởng đến tính toán hoàn tiền');?></small>
                </div>
                <label for="remainsValue" class="form-label"><?=__('Remains mới');?> <span class="text-danger">*</span></label>
                <input type="number" id="remainsValue" class="form-control" min="0" placeholder="<?=__('Nhập số lượng còn lại');?>">
            </div>
        `,
        icon: 'warning',
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonText: '<?=__('Cập nhật');?>',
        cancelButtonText: '<?=__('Hủy');?>',
        preConfirm: () => {
            const remains = document.getElementById('remainsValue').value;
            if (!remains || isNaN(remains) || parseInt(remains) < 0) {
                Swal.showValidationMessage('<?=__('Vui lòng nhập Remains hợp lệ');?>');
                return false;
            }
            return parseInt(remains);
        }
    }).then((result) => {
        if (result.isConfirmed) {
            executeQuickUpdateField(selectedOrders, 'remains', result.value);
        }
    });
}

// Hàm thực hiện cập nhật field hàng loạt
function executeQuickUpdateField(selectedOrders, field, value) {
    // Hiển thị loading
    $('#loading-overlay').addClass('active');

    var updateData = {
        field: field,
        value: value,
        orderIds: []
    };

    // Thu thập ID của các đơn hàng được chọn
    selectedOrders.each(function() {
        updateData.orderIds.push($(this).data('id'));
    });

    // Gửi dữ liệu cập nhật lên server
    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            token: $("#token").val(),
            action: 'bulkUpdateOrderField',
            data: JSON.stringify(updateData)
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, result.status);
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                showMessage(result.msg || '<?=__('Lỗi không xác định');?>', result.status);
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            showMessage('<?=__('Đã xảy ra lỗi khi cập nhật:');?> ' + errorThrown, 'error');
        },
        complete: function() {
            // Ẩn loading
            $('#loading-overlay').removeClass('active');
        }
    });
}

// Hàm export dữ liệu đơn hàng
function exportOrderData(format) {
    var selectedOrders = $('.checkbox_product:checked');

    if (selectedOrders.length == 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng để xuất dữ liệu');?>', 'error');
        return;
    }

    // Hiển thị loading
    $('#loading-overlay').addClass('active');

    var orderIds = [];
    selectedOrders.each(function() {
        orderIds.push($(this).data('id'));
    });

    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/view.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            token: $("#token").val(),
            action: 'exportOrderData',
            format: format,
            orderIds: JSON.stringify(orderIds)
        },
        success: function(result) {
            if (result.status == 'success') {
                // Tạo và download file
                var blob = new Blob([result.data], {
                    type: result.mimeType
                });
                var url = window.URL.createObjectURL(blob);
                var a = document.createElement('a');
                a.href = url;
                a.download = result.filename;
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);

                showMessage('<?=__('Xuất dữ liệu thành công');?> ' + selectedOrders.length +
                    ' <?=__('đơn hàng');?>', 'success');
            } else {
                showMessage(result.msg || '<?=__('Lỗi không xác định');?>', 'error');
            }
        },
        error: function(xhr, textStatus, errorThrown) {
            showMessage('<?=__('Đã xảy ra lỗi khi xuất dữ liệu:');?> ' + errorThrown, 'error');
        },
        complete: function() {
            // Ẩn loading
            $('#loading-overlay').removeClass('active');
        }
    });
}

// Hàm xóa đơn hàng đã chọn
function deleteSelectedOrders() {
    var checkboxes = document.querySelectorAll('input[name="checkbox_product"]:checked');
    if (checkboxes.length === 0) {
        showMessage('<?=__('Vui lòng chọn ít nhất một đơn hàng');?>', 'error');
        return;
    }

    Swal.fire({
        title: "<?=__('Xác nhận xóa đơn hàng');?>",
        html: `
            <div class="text-start">
                <div class="alert alert-danger mb-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong><?=__('Cảnh báo!');?></strong><br>
                    <?=__('Bạn sắp xóa');?> <strong>${checkboxes.length}</strong> <?=__('đơn hàng');?><br>
                    <small><?=__('Hành động này không thể hoàn tác!');?></small>
                </div>
                <label for="confirmText" class="form-label">
                    <?=__('Để xác nhận, vui lòng nhập');?> <strong class="text-danger">DELETE</strong>
                </label>
                <input type="text" id="confirmText" class="form-control" placeholder="<?=__('Nhập: DELETE');?>" autocomplete="off">
                <small class="text-muted mt-1 d-block"><?=__('Nhập chính xác từ "DELETE" để tiếp tục');?></small>
            </div>
        `,
        icon: "warning",
        focusConfirm: false,
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "<?=__('Xóa đơn hàng');?>",
        cancelButtonText: "<?=__('Hủy');?>",
        preConfirm: () => {
            const confirmText = document.getElementById('confirmText').value.trim();
            if (confirmText !== 'DELETE') {
                Swal.showValidationMessage('<?=__('Vui lòng nhập chính xác "DELETE" để xác nhận');?>');
                return false;
            }
            return true;
        }
    }).then((result) => {
        if (result.isConfirmed) {
            delete_records();
        }
    });
}
</script>