<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Quản Lý Child Panel').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<link rel="stylesheet" href="'.base_url('assets/libs/flatpickr/flatpickr.min.css').'">
';
$body['footer'] = '
<script src="'.base_url('assets/libs/flatpickr/flatpickr.min.js').'"></script>
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

// Kiểm tra quyền xem child panel
if(checkPermission($getUser['admin'], 'view_child_panel') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}



if (isset($_POST['SaveSettings'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }
    if(checkPermission($getUser['admin'], 'edit_child_panel') != true){
        die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
    }
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Cấu hình Child Panel')
    ]);
    foreach ($_POST as $key => $value) {
        $CMSNT->update("settings", array(
            'value' => $value
        ), " `name` = '$key' ");
    }
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __('Cấu hình Child Panel'), $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);    
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);
    die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.history.back().location.reload();}</script>');
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
$where = " cp.id > 0 ";
$created_at = '';
$domain = '';
$shortByDate = '';
$username = '';
$status = '';

// Filters
if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND cp.status = "'.$status.'" ';
}
if(!empty($_GET['domain'])){
    $domain = check_string($_GET['domain']);
    $where .= ' AND cp.domain LIKE "%'.$domain.'%" ';
}
if(!empty($_GET['username'])){
    $username = check_string($_GET['username']);
    $where .= ' AND u.username LIKE "%'.$username.'%" ';
}
if(!empty($_GET['created_at'])){
    $created_at = check_string($_GET['created_at']);
    $date_range = explode(' to ', str_replace('-', '/', $created_at));
    if(count($date_range) == 2 && $date_range[0] != $date_range[1]){
        $date_range = [$date_range[0].' 00:00:00', $date_range[1].' 23:59:59'];
        $where .= " AND cp.created_at >= '".$date_range[0]."' AND cp.created_at <= '".$date_range[1]."' ";
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
        $where .= " AND DATE(cp.created_at) = '".$currentDate."' ";
    }
    if($shortByDate == 2){
        $where .= " AND YEAR(cp.created_at) = $currentYear AND WEEK(cp.created_at, 1) = $currentWeek ";
    }
    if($shortByDate == 3){
        $where .= " AND MONTH(cp.created_at) = '$currentMonth' AND YEAR(cp.created_at) = '$currentYear' ";
    }
}

$listDatatable = $CMSNT->get_list("
    SELECT cp.*, u.username, u.email, u.money
    FROM child_panels cp 
    LEFT JOIN users u ON cp.user_id = u.id 
    WHERE $where 
    ORDER BY cp.id DESC 
    LIMIT $from,$limit
");

$totalDatatable = $CMSNT->num_rows("
    SELECT cp.id 
    FROM child_panels cp 
    LEFT JOIN users u ON cp.user_id = u.id 
    WHERE $where
");

$urlDatatable = pagination(base_url_admin("child-panel&limit=$limit&shortByDate=$shortByDate&domain=$domain&created_at=$created_at&username=$username&status=$status&"), $from, $totalDatatable, $limit);

?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">
                <i class="ri-window-line me-2"></i><?=__('Quản Lý Child Panel');?>
            </h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?=base_url_admin('');?>"><?=__('Dashboard');?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?=__('Child Panel');?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <!-- Statistics Cards -->


        <!-- Tickets Table -->
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-config-tickets" class="btn btn-primary label-btn mb-3">
                        <i class="ri-settings-4-line label-btn-icon me-2"></i> <?=__('CẤU HÌNH');?>
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-config-tickets" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label"
                                            for="example-hf-email"><?=__('Trạng thái');?></label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="child_panel_status">
                                                <option <?=$CMSNT->site('child_panel_status') == 1 ? 'selected' : '';?>
                                                    value="1"><?=__('ON');?></option>
                                                <option <?=$CMSNT->site('child_panel_status') == 0 ? 'selected' : '';?>
                                                    value="0"><?=__('OFF');?></option>
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label"
                                            for="example-hf-email"><?=__('Giá thuê');?></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="child_panel_price"
                                                    value="<?=$CMSNT->site('child_panel_price');?>">
                                                <span class="input-group-text"><?=__('1 Tháng');?></span>
                                            </div>
                                            <div class="text-muted mt-2">
                                                <?=__('Chi phí này do người dùng trả cho bạn mỗi tháng.');?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label"
                                            for="example-hf-email"><?=__('Bạn muốn tự xây dựng Child Panel hay thuê tại CMSNT?');?></label>
                                        <div class="col-sm-8">
                                            <select class="form-control" name="child_panel_type" id="child_panel_type"
                                                onchange="updateChildPanelTypeNote()">
                                                <option
                                                    <?=$CMSNT->site('child_panel_type') == 'build' ? 'selected' : '';?>
                                                    value="build"><?=__('Tự xây dựng');?></option>
                                                <option
                                                    <?=$CMSNT->site('child_panel_type') == 'rent' ? 'selected' : '';?>
                                                    value="rent"><?=__('Thuê tại CMSNT');?></option>
                                            </select>
                                            <div id="child_panel_type_note" class="mt-2">
                                                <!-- Thông báo sẽ được cập nhật bằng JavaScript -->
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label"
                                            for="example-hf-email"><?=__('Nameserver 1');?></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="child_panel_ns1"
                                                    value="<?=$CMSNT->site('child_panel_ns1');?>">
                                                <span class="input-group-text"><?=__('NS1');?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label"
                                            for="example-hf-email"><?=__('Nameserver 2');?></label>
                                        <div class="col-sm-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="child_panel_ns2"
                                                    value="<?=$CMSNT->site('child_panel_ns2');?>">
                                                <span class="input-group-text"><?=__('NS2');?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" name="SaveSettings" class="btn btn-primary btn-block"><i
                                        class="fa fa-fw fa-save me-1"></i>
                                    <?=__('Lưu');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title text-uppercase">
                            <?=__('Danh sách Child Panel');?>
                        </div>
                        <div class="d-flex">
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="collapse"
                                data-bs-target="#filtersCollapse">
                                <i class="ri-filter-line me-1"></i><?=__('Bộ lọc');?>
                            </button>
                        </div>
                    </div>

                    <!-- Filters -->
                    <div class="collapse <?=(!empty($status) || !empty($domain) || !empty($username) || !empty($created_at) || !empty($shortByDate)) ? 'show' : '';?>"
                        id="filtersCollapse">
                        <div class="card-body border-bottom">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                <input type="hidden" name="action" value="child-panel">

                                <div class="row g-3">
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label"><?=__('Người dùng');?></label>
                                        <input class="form-control" value="<?=$username;?>" name="username"
                                            placeholder="<?=__('Tên người dùng');?>">
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label"><?=__('Domain');?></label>
                                        <input class="form-control" value="<?=$domain;?>" name="domain"
                                            placeholder="<?=__('Tên miền');?>">
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label"><?=__('Trạng thái');?></label>
                                        <select class="form-select" name="status">
                                            <option value=""><?=__('Tất cả');?></option>
                                            <option <?=$status == 'Actived' ? 'selected' : '';?> value="Actived">
                                                <?=__('Hoạt động');?></option>
                                            <option <?=$status == 'Pending' ? 'selected' : '';?> value="Pending">
                                                <?=__('Chờ xác nhận');?></option>
                                            <option <?=$status == 'Cancel' ? 'selected' : '';?> value="Cancel">
                                                <?=__('Hủy');?></option>
                                            <option <?=$status == 'Expired' ? 'selected' : '';?> value="Expired">
                                                <?=__('Hết hạn');?></option>
                                        </select>
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label"><?=__('Thời gian tạo');?></label>
                                        <input type="text" name="created_at" class="form-control" id="daterange"
                                            value="<?=$created_at;?>" placeholder="<?=__('Chọn thời gian');?>">
                                    </div>
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6">
                                        <label class="form-label"><?=__('Lọc nhanh');?></label>
                                        <select name="shortByDate" class="form-select">
                                            <option value=""><?=__('Tất cả');?></option>
                                            <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1">
                                                <?=__('Hôm nay');?></option>
                                            <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2">
                                                <?=__('Tuần này');?></option>
                                            <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3">
                                                <?=__('Tháng này');?></option>
                                        </select>
                                    </div>
                                    <div class="col-12">
                                        <button class="btn btn-primary me-2">
                                            <i class="ri-search-line me-1"></i><?=__('Tìm kiếm');?>
                                        </button>
                                        <a class="btn btn-outline-secondary" href="<?=base_url_admin('child-panel');?>">
                                            <i class="ri-refresh-line me-1"></i><?=__('Đặt lại');?>
                                        </a>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Table Controls -->
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <label class="form-label me-2 mb-0"><?=__('Hiển thị');?>:</label>
                                <form method="GET" class="d-inline">
                                    <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                    <input type="hidden" name="action" value="child-panel">
                                    <input type="hidden" name="shortByDate" value="<?=$shortByDate;?>">
                                    <input type="hidden" name="domain" value="<?=$domain;?>">
                                    <input type="hidden" name="created_at" value="<?=$created_at;?>">
                                    <input type="hidden" name="username" value="<?=$username;?>">
                                    <input type="hidden" name="status" value="<?=$status;?>">
                                    <select name="limit" onchange="this.form.submit()"
                                        class="form-select form-select-sm" style="width: auto;">
                                        <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                        <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                        <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                        <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                        <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                    </select>
                                </form>
                            </div>
                            <div class="d-flex align-items-center gap-3">
                                <!-- Auto Load Control -->
                                <div class="d-flex align-items-center">
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" id="autoLoadTickets">
                                        <label class="form-check-label" for="autoLoadTickets">
                                            <i class="ri-refresh-line me-1"></i><?=__('Auto Load');?>
                                        </label>
                                    </div>
                                    <div class="ms-2">
                                        <select id="autoLoadInterval" class="form-select form-select-sm"
                                            style="width: auto;">
                                            <option value="15"><?=__('15 giây');?></option>
                                            <option value="30"><?=__('30 giây');?></option>
                                            <option value="60" selected><?=__('1 phút');?></option>
                                            <option value="120"><?=__('2 phút');?></option>
                                            <option value="300"><?=__('5 phút');?></option>
                                        </select>
                                    </div>
                                    <div class="ms-2">
                                        <span id="autoLoadStatus" class="badge bg-secondary-transparent"
                                            style="display: none;">
                                            <i class="ri-time-line me-1"></i><span id="countdown"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="text-muted">
                                    <?=__('Hiển thị');?> <?=count($listDatatable);?> <?=__('trong tổng số');?>
                                    <?=number_format($totalDatatable);?> <?=__('kết quả');?>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div class="d-flex align-items-center">
                                <button type="button" id="btnBulkDelete" class="btn btn-outline-danger me-2"
                                    style="display: none;">
                                    <i class="ri-delete-bin-line me-1"></i><?=__('Xóa đã chọn');?>
                                    (<span id="selectedCount">0</span>)
                                </button>
                                <button type="button" id="btnBulkStatus"
                                    class="btn btn-outline-primary dropdown-toggle me-2" data-bs-toggle="dropdown"
                                    style="display: none;">
                                    <i class="ri-settings-3-line me-1"></i><?=__('Thay đổi trạng thái');?>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" type="button" onclick="bulkChangeStatus('Actived')">
                                            <i class="ri-play-circle-line me-2 text-success"></i><?=__('Kích hoạt');?>
                                        </a></li>
                                    <li><a class="dropdown-item" type="button" onclick="bulkChangeStatus('Pending')">
                                            <i class="ri-time-line me-2 text-warning"></i><?=__('Chờ xác nhận');?>
                                        </a></li>
                                    <li><a class="dropdown-item" type="button" onclick="bulkChangeStatus('Cancel')">
                                            <i class="ri-close-circle-line me-2 text-danger"></i><?=__('Hủy');?>
                                        </a></li>
                                    <li><a class="dropdown-item" type="button" onclick="bulkChangeStatus('Expired')">
                                            <i
                                                class="ri-calendar-close-line me-2 text-secondary"></i><?=__('Hết hạn');?>
                                        </a></li>
                                </ul>
                            </div>
                            <div class="text-muted">
                                <?=__('Tổng cộng');?>: <?=number_format($totalDatatable);?> <?=__('Child Panel');?>
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th style="width: 50px;">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="selectAll">
                                                <label class="form-check-label" for="selectAll"></label>
                                            </div>
                                        </th>
                                        <th><?=__('Domain');?></th>
                                        <th><?=__('Người dùng');?></th>
                                        <th class="text-center" style="width: 120px;"><?=__('Trạng thái');?></th>
                                        <th class="text-center" style="width: 130px;"><?=__('Hết hạn');?></th>
                                        <th class="text-center" style="width: 150px;"><?=__('Ngày tạo');?></th>
                                        <th class="text-center" style="width: 120px;"><?=__('Thao tác');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if(empty($listDatatable)): ?>
                                    <tr>
                                        <td colspan="8" class="text-center py-4">
                                            <div class="empty-state">
                                                <i class="ri-window-line fs-48 text-muted"></i>
                                                <h5 class="mt-3 text-muted"><?=__('Không có dữ liệu');?></h5>
                                                <p class="text-muted mb-0">
                                                    <?=__('Không tìm thấy Child Panel nào phù hợp với điều kiện tìm kiếm');?>
                                                </p>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php else: ?>
                                    <?php foreach ($listDatatable as $row): ?>
                                    <tr>
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input child-panel-checkbox" type="checkbox"
                                                    value="<?=$row['id'];?>" id="childpanel_<?=$row['id'];?>">
                                                <label class="form-check-label"
                                                    for="childpanel_<?=$row['id'];?>"></label>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="ri-global-line fs-18 text-primary me-2"></i>
                                                <div>
                                                    <div class="fw-semibold">
                                                        <a href="https://<?=$row['domain'];?>" target="_blank"
                                                            class="text-dark text-decoration-none">
                                                            <?=htmlspecialchars($row['domain']);?>
                                                        </a>
                                                    </div>
                                                    <?php if(!empty($row['note'])): ?>
                                                    <small
                                                        class="text-muted"><?=htmlspecialchars(substr($row['note'], 0, 30));?><?=strlen($row['note']) > 30 ? '...' : '';?></small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <img src="<?=getGravatarUrl($row['email']);?>"
                                                        alt="<?=$row['username'];?>" class="avatar-img rounded-circle">
                                                </div>
                                                <div>
                                                    <div class="fw-semibold">
                                                        <a href="<?=base_url_admin('user-edit&id='.$row['user_id']);?>"
                                                            class="text-primary text-decoration-none"
                                                            data-bs-toggle="tooltip"
                                                            title="<?=__('Chỉnh sửa người dùng');?>">
                                                            <?=$row['username'];?>
                                                        </a>
                                                    </div>
                                                    <small class="text-muted"><?=$row['email'];?></small>

                                                    <?php $user = $CMSNT->get_row("SELECT `money`, `total_money` FROM `users` WHERE `id` = '{$row['user_id']}'");?>
                                                    <div class="mt-1">
                                                        <small class="badge bg-success-transparent me-1"
                                                            data-bs-toggle="tooltip" title="<?=__('Số dư hiện tại');?>">
                                                            <i
                                                                class="ri-wallet-line me-1"></i><?=format_currency($user['money']);?>
                                                        </small>
                                                        <small class="badge bg-info-transparent"
                                                            data-bs-toggle="tooltip" title="<?=__('Tổng đã nạp');?>">
                                                            <i
                                                                class="ri-money-dollar-circle-line me-1"></i><?=number_format($user['total_money']);?>
                                                        </small>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <?=display_childpanel_status($row['status']);?>
                                        </td>
                                        <td class="text-center">
                                            <?php if($row['expired_at']): ?>
                                            <?php
                                            $expiredDate = strtotime($row['expired_at']);
                                            $now = time();
                                            $isExpired = $expiredDate < $now;
                                            $daysLeft = ceil(($expiredDate - $now) / (24 * 60 * 60));
                                            ?>
                                            <div
                                                class="<?=$isExpired ? 'text-danger' : ($daysLeft <= 7 ? 'text-warning' : 'text-muted');?>">
                                                <small><?=date('d/m/Y', $expiredDate);?></small>
                                                <br>
                                                <?php if($isExpired): ?>
                                                <small
                                                    class="badge bg-danger-transparent"><?=__('Đã hết hạn');?></small>
                                                <?php elseif($daysLeft <= 7): ?>
                                                <small class="badge bg-warning-transparent"><?=$daysLeft;?>
                                                    <?=__('ngày');?></small>
                                                <?php else: ?>
                                                <small><?=$daysLeft;?> <?=__('ngày');?></small>
                                                <?php endif; ?>
                                            </div>
                                            <?php else: ?>
                                            <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <div class="text-muted">
                                                <small><?=date('d/m/Y', strtotime($row['created_at']));?></small>
                                                <br><small><?=date('H:i', strtotime($row['created_at']));?></small>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-primary-light"
                                                    onclick="editChildPanel(<?=$row['id'];?>)" data-bs-toggle="tooltip"
                                                    title="<?=__('Chỉnh sửa');?>">
                                                    <i class="ri-edit-line"></i>
                                                </button>
                                                <div class="btn-group">
                                                    <button type="button"
                                                        class="btn btn-sm btn-outline-primary dropdown-toggle dropdown-toggle-split"
                                                        data-bs-toggle="dropdown">
                                                        <span class="visually-hidden">Toggle Dropdown</span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <?php if($row['status'] == 'Actived'): ?>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="changeChildPanelStatus(<?=$row['id'];?>, 'Cancel')">
                                                                <i
                                                                    class="ri-close-circle-line me-2 text-danger"></i><?=__('Hủy');?>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="changeChildPanelStatus(<?=$row['id'];?>, 'Expired')">
                                                                <i
                                                                    class="ri-calendar-close-line me-2 text-warning"></i><?=__('Hết hạn');?>
                                                            </button>
                                                        </li>
                                                        <?php else: ?>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="changeChildPanelStatus(<?=$row['id'];?>, 'Actived')">
                                                                <i
                                                                    class="ri-play-circle-line me-2 text-success"></i><?=__('Kích hoạt');?>
                                                            </button>
                                                        </li>
                                                        <?php endif; ?>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="extendChildPanel(<?=$row['id'];?>)">
                                                                <i
                                                                    class="ri-time-line me-2 text-warning"></i><?=__('Gia hạn');?>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item"
                                                                onclick="copyChildPanelInfo(<?=$row['id'];?>)">
                                                                <i
                                                                    class="ri-file-copy-line me-2 text-info"></i><?=__('Sao chép Thông tin');?>
                                                            </button>
                                                        </li>
                                                        <li>
                                                            <hr class="dropdown-divider">
                                                        </li>
                                                        <li>
                                                            <button class="dropdown-item text-danger"
                                                                onclick="RemoveChildPanel(<?=$row['id'];?>)">
                                                                <i
                                                                    class="ri-delete-bin-line me-2 text-danger"></i><?=__('Xóa');?>
                                                            </button>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if($totalDatatable > $limit): ?>
                        <div class="d-flex justify-content-center mt-4">
                            <?=$urlDatatable;?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>

<script type="text/javascript">
$(document).ready(function() {
    // Initialize flatpickr for date range
    $("#daterange").flatpickr({
        mode: "range",
        dateFormat: "d/m/Y",
        locale: "vn"
    });

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Auto Load functionality
    let autoLoadTimer = null;
    let countdownTimer = null;
    let timeLeft = 0;

    // Load saved auto load settings
    const savedAutoLoad = localStorage.getItem('autoLoadChildPanels');
    const savedInterval = localStorage.getItem('autoLoadInterval');

    // Set saved interval first before starting auto load
    if (savedInterval) {
        $('#autoLoadInterval').val(savedInterval);
    }

    // Then start auto load if it was enabled
    if (savedAutoLoad === 'true') {
        $('#autoLoadTickets').prop('checked', true);
        startAutoLoad();
    }

    // Auto load checkbox change event
    $('#autoLoadTickets').change(function() {
        if ($(this).is(':checked')) {
            localStorage.setItem('autoLoadChildPanels', 'true');
            startAutoLoad();
        } else {
            localStorage.setItem('autoLoadChildPanels', 'false');
            stopAutoLoad();
        }
    });

    // Auto load interval change event
    $('#autoLoadInterval').change(function() {
        const interval = $(this).val();
        localStorage.setItem('autoLoadInterval', interval);

        if ($('#autoLoadTickets').is(':checked')) {
            stopAutoLoad();
            startAutoLoad();
        }
    });

    function startAutoLoad() {
        const interval = parseInt($('#autoLoadInterval').val()) * 1000;
        timeLeft = parseInt($('#autoLoadInterval').val());

        $('#autoLoadStatus').show();
        updateCountdown();

        // Start countdown
        countdownTimer = setInterval(function() {
            timeLeft--;
            updateCountdown();

            if (timeLeft <= 0) {
                timeLeft = parseInt($('#autoLoadInterval').val());
            }
        }, 1000);

        // Start auto reload
        autoLoadTimer = setInterval(function() {
            // Show loading indicator
            showLoadingIndicator();

            // Reload page to get fresh data
            setTimeout(function() {
                location.reload();
            }, 500);
        }, interval);
    }

    function stopAutoLoad() {
        if (autoLoadTimer) {
            clearInterval(autoLoadTimer);
            autoLoadTimer = null;
        }

        if (countdownTimer) {
            clearInterval(countdownTimer);
            countdownTimer = null;
        }

        $('#autoLoadStatus').hide();
    }

    function updateCountdown() {
        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;
        const display = minutes > 0 ? `${minutes}:${seconds.toString().padStart(2, '0')}` : `${seconds}s`;
        $('#countdown').text(display);

        // Change badge color based on time left
        if (timeLeft <= 10) {
            $('#autoLoadStatus').removeClass('bg-secondary-transparent bg-warning-transparent').addClass(
                'bg-danger-transparent');
        } else if (timeLeft <= 30) {
            $('#autoLoadStatus').removeClass('bg-secondary-transparent bg-danger-transparent').addClass(
                'bg-warning-transparent');
        } else {
            $('#autoLoadStatus').removeClass('bg-warning-transparent bg-danger-transparent').addClass(
                'bg-secondary-transparent');
        }
    }

    function showLoadingIndicator() {
        // Create loading overlay
        if (!$('#loadingOverlay').length) {
            $('body').append(`
                <div id="loadingOverlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.1); z-index: 9999; display: none;">
                    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 20px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1);">
                        <div class="d-flex align-items-center">
                            <div class="spinner-border spinner-border-sm text-primary me-3" role="status"></div>
                            <span><?=__('Đang tải dữ liệu mới...');?></span>
                        </div>
                    </div>
                </div>
            `);
        }

        $('#loadingOverlay').fadeIn(200);
    }

    // Stop auto load when page is about to unload
    $(window).on('beforeunload', function() {
        stopAutoLoad();
    });

    // Keep auto load running even when tab is not visible
    // Only show/hide the visual countdown, but keep the timer running
    document.addEventListener('visibilitychange', function() {
        if (document.hidden) {
            // Hide the countdown badge when tab is not visible (optional)
            // $('#autoLoadStatus').hide();
        } else {
            // Show the countdown badge when tab becomes visible again (optional)  
            if ($('#autoLoadTickets').is(':checked')) {
                $('#autoLoadStatus').show();
            }
        }
    });
});

function changeChildPanelStatus(id, status) {
    let alertTitle = '<?=__('Xác nhận thay đổi');?>';
    Swal.fire({
        icon: 'question',
        title: alertTitle,
        text: "<?=__('Bạn có chắc chắn muốn set');?> " + status + " <?=__('Child Panel này không?');?>",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "<?=__('Xác nhận');?>",
        cancelButtonText: "<?=__('Hủy');?>"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'changeStatusChildPanel',
                    id: id,
                    status: status
                },
                beforeSend: function() {
                    $('button').prop('disabled', true);
                },
                success: function(result) {
                    $('button').prop('disabled', false);
                    if (result.status == 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "<?=__('Thành công');?>",
                            text: result.msg,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: result.msg
                        });
                    }
                },
                error: function() {
                    $('button').prop('disabled', false);
                    Swal.fire({
                        icon: "error",
                        title: "<?=__('Lỗi');?>",
                        text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
                    });
                }
            });
        }
    })
}

function RemoveChildPanel(id) {
    Swal.fire({
        icon: "question",
        title: "<?=__('Xác nhận xóa');?>",
        text: "<?=__('Bạn có chắc chắn muốn xóa Child Panel này không? Thao tác này không thể hoàn tác.');?>",
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: "<?=__('Xóa');?>",
        cancelButtonText: "<?=__('Hủy');?>"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'removeChildPanel',
                    id: id
                },
                beforeSend: function() {
                    $('button').prop('disabled', true);
                },
                success: function(result) {
                    $('button').prop('disabled', false);
                    if (result.status == 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "<?=__('Thành công');?>",
                            text: result.msg,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: result.msg
                        });
                    }
                },
                error: function() {
                    $('button').prop('disabled', false);
                    Swal.fire({
                        icon: "error",
                        title: "<?=__('Lỗi');?>",
                        text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
                    });
                }
            });
        }
    })
}



function copyChildPanelInfo(id) {
    $.ajax({
        url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
        type: 'POST',
        dataType: "JSON",
        data: {
            action: 'copyChildPanelInfo',
            id: id
        },
        beforeSend: function() {
            Swal.fire({
                title: '<?=__('Đang xử lý...');?>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(result) {
            if (result.status == 'success') {
                // Sao chép vào clipboard
                navigator.clipboard.writeText(result.data).then(function() {
                    Swal.fire({
                        icon: "success",
                        title: "<?=__('Thành công');?>",
                        html: `
                            <div class="text-start">
                                <p class="mb-3"><?=__('Thông tin Child Panel đã được sao chép vào clipboard!');?></p>
                                <div class="bg-light p-3 rounded">
                                    <small class="text-muted"><?=__('Nội dung đã sao chép:');?></small>
                                    <pre class="mt-2 text-start" style="font-size: 12px; max-height: 200px; overflow-y: auto;">${result.data}</pre>
                                </div>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: "<?=__('Đóng');?>",
                        width: '600px'
                    });
                }).catch(function(err) {
                    console.error('Could not copy text: ', err);
                    // Fallback: Hiển thị thông tin để user copy thủ công
                    Swal.fire({
                        icon: "info",
                        title: "<?=__('Thông tin Child Panel');?>",
                        html: `
                            <div class="text-start">
                                <p class="mb-3"><?=__('Vui lòng sao chép thông tin bên dưới:');?></p>
                                <div class="bg-light p-3 rounded">
                                    <textarea class="form-control" rows="10" readonly onclick="this.select()" style="font-size: 12px;">${result.data}</textarea>
                                </div>
                                <small class="text-muted mt-2 d-block"><?=__('Click vào textarea để chọn tất cả và nhấn Ctrl+C để sao chép');?></small>
                            </div>
                        `,
                        showConfirmButton: true,
                        confirmButtonText: "<?=__('Đóng');?>",
                        width: '600px'
                    });
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "<?=__('Lỗi');?>",
                    text: result.msg
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: "error",
                title: "<?=__('Lỗi');?>",
                text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
            });
        }
    });
}

function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        Swal.fire({
            icon: "success",
            title: "<?=__('Đã sao chép');?>",
            text: "<?=__('API Key đã được sao chép vào clipboard');?>",
            timer: 1500,
            showConfirmButton: false
        });
    });
}

function editChildPanel(id) {
    // Ajax get child panel data
    $.ajax({
        url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
        type: 'POST',
        dataType: "JSON",
        data: {
            action: 'getChildPanel',
            id: id
        },
        success: function(result) {
            if (result.status == 'success') {
                const data = result.data;
                Swal.fire({
                    title: "<?=__('Chỉnh sửa Child Panel');?>",
                    html: `
                        <form id="editChildPanelForm">
                            <div class="row g-3 text-start">
                                <div class="col-12">
                                    <label class="form-label"><?=__('Domain');?></label>
                                    <input type="text" class="form-control" name="domain" value="${data.domain}" readonly>
                                </div>
                                <div class="col-12">
                                    <label class="form-label"><?=__('Ghi chú');?></label>
                                    <textarea class="form-control" name="note" rows="3">${data.note || ''}</textarea>
                                </div>
                                <div class="col-6">
                                    <label class="form-label"><?=__('Trạng thái');?></label>
                                    <select class="form-select" name="status">
                                        <option value="Actived" ${data.status == 'Actived' ? 'selected' : ''}><?=__('Kích hoạt');?></option>
                                        <option value="Pending" ${data.status == 'Pending' ? 'selected' : ''}><?=__('Chờ xác nhận');?></option>
                                        <option value="Cancel" ${data.status == 'Cancel' ? 'selected' : ''}><?=__('Hủy');?></option>
                                        <option value="Expired" ${data.status == 'Expired' ? 'selected' : ''}><?=__('Hết hạn');?></option>
                                    </select>
                                </div>
                                <div class="col-6">
                                    <label class="form-label"><?=__('Ngày hết hạn');?></label>
                                    <input type="date" class="form-control" name="expired_at" value="${data.expired_at ? data.expired_at.split(' ')[0] : ''}">
                                </div>
                            </div>
                        </form>
                    `,
                    showCancelButton: true,
                    confirmButtonText: "<?=__('Cập nhật');?>",
                    cancelButtonText: "<?=__('Hủy');?>",
                    preConfirm: () => {
                        const formData = new FormData(document.getElementById(
                            'editChildPanelForm'));
                        return {
                            note: formData.get('note'),
                            status: formData.get('status'),
                            expired_at: formData.get('expired_at')
                        };
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        updateChildPanel(id, result.value);
                    }
                });
            }
        }
    });
}

function updateChildPanel(id, data) {
    $.ajax({
        url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
        type: 'POST',
        dataType: "JSON",
        data: {
            action: 'editChildPanel',
            id: id,
            ...data
        },
        beforeSend: function() {
            Swal.fire({
                title: '<?=__('Đang xử lý...');?>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
        },
        success: function(result) {
            if (result.status == 'success') {
                Swal.fire({
                    icon: "success",
                    title: "<?=__('Thành công');?>",
                    text: result.msg,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "<?=__('Lỗi');?>",
                    text: result.msg
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: "error",
                title: "<?=__('Lỗi');?>",
                text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
            });
        }
    });
}

function extendChildPanel(id) {
    Swal.fire({
        title: "<?=__('Gia hạn Child Panel');?>",
        html: `
            <div class="row g-3 text-start">
                <div class="col-12">
                    <label class="form-label"><?=__('Số ngày gia hạn');?></label>
                    <input type="number" class="form-control" id="extendDays" min="1" value="30" placeholder="<?=__('Nhập số ngày');?>">
                    <small class="text-muted"><?=__('Nếu Child Panel đã hết hạn, thời gian sẽ được tính từ hiện tại');?></small>
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: "<?=__('Gia hạn');?>",
        cancelButtonText: "<?=__('Hủy');?>",
        preConfirm: () => {
            const days = document.getElementById('extendDays').value;
            if (!days || days < 1) {
                Swal.showValidationMessage('<?=__('Vui lòng nhập số ngày hợp lệ');?>');
                return false;
            }
            return {
                days: days
            };
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'extendChildPanel',
                    id: id,
                    days: result.value.days
                },
                beforeSend: function() {
                    Swal.fire({
                        title: '<?=__('Đang xử lý...');?>',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                },
                success: function(result) {
                    if (result.status == 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "<?=__('Thành công');?>",
                            text: result.msg,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: result.msg
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "<?=__('Lỗi');?>",
                        text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
                    });
                }
            });
        }
    });
}

// Xử lý checkbox chọn tất cả và chọn từng item
$(document).ready(function() {
    // Xử lý checkbox chọn tất cả
    $('#selectAll').change(function() {
        var isChecked = $(this).is(':checked');
        $('.child-panel-checkbox').prop('checked', isChecked);
        updateBulkButtons();
    });

    // Xử lý checkbox từng item
    $(document).on('change', '.child-panel-checkbox', function() {
        var totalCheckboxes = $('.child-panel-checkbox').length;
        var checkedCheckboxes = $('.child-panel-checkbox:checked').length;

        // Cập nhật trạng thái checkbox "chọn tất cả"
        $('#selectAll').prop('checked', totalCheckboxes === checkedCheckboxes);

        updateBulkButtons();
    });

    // Xử lý nút xóa hàng loạt
    $('#btnBulkDelete').click(function() {
        var selectedIds = getSelectedChildPanelIds();
        if (selectedIds.length === 0) {
            Swal.fire({
                icon: "warning",
                title: "<?=__('Cảnh báo');?>",
                text: "<?=__('Vui lòng chọn ít nhất một Child Panel để xóa');?>"
            });
            return;
        }

        Swal.fire({
            icon: "question",
            title: "<?=__('Xác nhận xóa');?>",
            text: "<?=__('Bạn có chắc chắn muốn xóa');?> " + selectedIds.length +
                " <?=__('Child Panel đã chọn? Thao tác này không thể hoàn tác.');?>",
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: "<?=__('Xóa');?>",
            cancelButtonText: "<?=__('Hủy');?>"
        }).then((result) => {
            if (result.isConfirmed) {
                bulkDeleteChildPanels(selectedIds);
            }
        });
    });
});

// Cập nhật hiển thị nút bulk action
function updateBulkButtons() {
    var selectedCount = $('.child-panel-checkbox:checked').length;
    $('#selectedCount').text(selectedCount);

    if (selectedCount > 0) {
        $('#btnBulkDelete, #btnBulkStatus').show();
    } else {
        $('#btnBulkDelete, #btnBulkStatus').hide();
    }
}

// Lấy danh sách ID đã chọn
function getSelectedChildPanelIds() {
    var selectedIds = [];
    $('.child-panel-checkbox:checked').each(function() {
        selectedIds.push($(this).val());
    });
    return selectedIds;
}

// Xóa nhiều Child Panel cùng lúc
function bulkDeleteChildPanels(ids) {
    $.ajax({
        url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
        type: 'POST',
        dataType: "JSON",
        data: {
            action: 'bulkDelete',
            ids: ids
        },
        beforeSend: function() {
            $('#btnBulkDelete').prop('disabled', true);
            $('#btnBulkDelete').html(
                '<i class="ri-loader-2-line spinner-border spinner-border-sm me-1"></i><?=__('Đang xóa...');?>'
            );
        },
        success: function(result) {
            $('#btnBulkDelete').prop('disabled', false);
            $('#btnBulkDelete').html(
                '<i class="ri-delete-bin-line me-1"></i><?=__('Xóa đã chọn');?> (<span id="selectedCount">0</span>)'
            );

            if (result.status == 'success') {
                Swal.fire({
                    icon: "success",
                    title: "<?=__('Thành công');?>",
                    text: result.msg,
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: "error",
                    title: "<?=__('Lỗi');?>",
                    text: result.msg
                });
            }
        },
        error: function() {
            $('#btnBulkDelete').prop('disabled', false);
            $('#btnBulkDelete').html(
                '<i class="ri-delete-bin-line me-1"></i><?=__('Xóa đã chọn');?> (<span id="selectedCount">0</span>)'
            );
            Swal.fire({
                icon: "error",
                title: "<?=__('Lỗi');?>",
                text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
            });
        }
    });
}

// Thay đổi trạng thái hàng loạt
function bulkChangeStatus(status) {
    var selectedIds = getSelectedChildPanelIds();
    if (selectedIds.length === 0) {
        Swal.fire({
            icon: "warning",
            title: "<?=__('Cảnh báo');?>",
            text: "<?=__('Vui lòng chọn ít nhất một Child Panel');?>"
        });
        return;
    }

    let statusText = '';
    switch (status) {
        case 'Actived':
            statusText = '<?=__('Kích hoạt');?>';
            break;
        case 'Pending':
            statusText = '<?=__('Chờ xác nhận');?>';
            break;
        case 'Expired':
            statusText = '<?=__('Hết hạn');?>';
            break;
        case 'Cancel':
            statusText = '<?=__('Hủy');?>';
            break;
    }

    Swal.fire({
        icon: "question",
        title: "<?=__('Xác nhận thay đổi');?>",
        text: "<?=__('Bạn có chắc chắn muốn');?> " + statusText + " " + selectedIds.length +
            " <?=__('Child Panel đã chọn?');?>",
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: "<?=__('Xác nhận');?>",
        cancelButtonText: "<?=__('Hủy');?>"
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: "<?=BASE_URL('ajaxs/admin/child-panel.php');?>",
                type: 'POST',
                dataType: "JSON",
                data: {
                    action: 'bulkChangeStatus',
                    ids: selectedIds,
                    status: status
                },
                beforeSend: function() {
                    $('#btnBulkStatus').prop('disabled', true);
                },
                success: function(result) {
                    $('#btnBulkStatus').prop('disabled', false);
                    if (result.status == 'success') {
                        Swal.fire({
                            icon: "success",
                            title: "<?=__('Thành công');?>",
                            text: result.msg,
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: result.msg
                        });
                    }
                },
                error: function() {
                    $('#btnBulkStatus').prop('disabled', false);
                    Swal.fire({
                        icon: "error",
                        title: "<?=__('Lỗi');?>",
                        text: "<?=__('Có lỗi xảy ra, vui lòng thử lại');?>"
                    });
                }
            });
        }
    });
}
</script>

<script>
// Function cập nhật thông báo cho Child Panel Type
function updateChildPanelTypeNote() {
    const selectElement = document.getElementById('child_panel_type');
    const noteElement = document.getElementById('child_panel_type_note');
    const selectedValue = selectElement.value;

    let noteContent = '';

    if (selectedValue === 'build') {
        noteContent = `
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                <i class="ri-information-line me-2"></i>
                <strong><?=__('Tự xây dựng Child Panel:');?></strong>
                <ul class="mb-0 mt-2">
                    <li><?=__('Bạn cần phải mua mã nguồn tạo website con hoặc thuê code riêng mã nguồn theo yêu cầu của bạn.');?></li>
                    <li><?=__('Bạn sẽ tự thiết lập server và cài đặt hệ thống.');?></li>
                    <li><?=__('Chi phí thấp hơn nhưng cần kiến thức kỹ thuật.');?></li>
                    <li><?=__('Bạn có toàn quyền kiểm soát hệ thống.');?></li>
                    <li><?=__('Cần tự bảo trì và cập nhật.');?></li>
                </ul>
            </div>
        `;
        // Xóa nameserver khi chọn build
        clearNameservers();
    } else if (selectedValue === 'rent') {
        noteContent = `
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="ri-shield-check-line me-2"></i>
                <strong><?=__('Thuê Child Panel tại CMSNT:');?></strong>
                <ul class="mb-0 mt-2">
                    <li><?=__('Hệ thống được CMSNT cài đặt và cấu hình sẵn.');?></li>
                    <li><?=__('Hỗ trợ kỹ thuật từ đội ngũ CMSNT.');?></li>
                    <li><?=__('Tự động cập nhật và bảo trì hệ thống.');?></li>
                    <li><?=__('Chi phí thuê hàng tháng ổn định.');?></li>
                    <li><?=__('Liên hệ: Telegram @ntthanhz để được tư vấn');?></li>
                </ul>
            </div>
        `;
        // Tự động lấy nameserver từ API CMSNT
        fetchCMSNTNameservers();
    }

    noteElement.innerHTML = noteContent;
}

// Function lấy nameserver từ API CMSNT
function fetchCMSNTNameservers() {
    const ns1Input = document.querySelector('input[name="child_panel_ns1"]');
    const ns2Input = document.querySelector('input[name="child_panel_ns2"]');

    // Hiển thị loading
    if (ns1Input) ns1Input.value = '<?=__('Đang tải...');?>';
    if (ns2Input) ns2Input.value = '<?=__('Đang tải...');?>';

    // Gọi API để lấy nameserver
    fetch('https://api.cmsnt.co/api/get_ns_child_panel.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            // Cập nhật nameserver vào input
            if (ns1Input && data.ns1) {
                ns1Input.value = data.ns1;
            }
            if (ns2Input && data.ns2) {
                ns2Input.value = data.ns2;
            }
        });
}

// Function xóa nameserver
function clearNameservers() {
    const ns1Input = document.querySelector('input[name="child_panel_ns1"]');
    const ns2Input = document.querySelector('input[name="child_panel_ns2"]');

    if (ns1Input) ns1Input.value = '';
    if (ns2Input) ns2Input.value = '';
}


document.addEventListener('DOMContentLoaded', function() {
    var button = document.getElementById('open-card-config-tickets');
    var card = document.getElementById('card-config-tickets');

    // Thêm sự kiện click cho nút button
    button.addEventListener('click', function() {
        // Kiểm tra nếu card đang hiển thị thì ẩn đi, ngược lại hiển thị
        if (card.style.display === 'none' || card.style.display === '') {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });

    // Cập nhật thông báo ban đầu
    updateChildPanelTypeNote();
});
</script>