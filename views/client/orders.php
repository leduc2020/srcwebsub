<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => __('Đơn hàng đã mua').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.service-name-short:hover {
    text-decoration: underline;
    color: #0d6efd;
}
.service-name-full {
    display: block;
    padding: 4px 8px;
    background-color: #f8f9fa;
    border-radius: 4px;
    margin-top: 4px;
    position: relative;
}
.close-full-name {
    position: absolute;
    right: 4px;
    top: 4px;
}

/* Style cho copy trans id */
.copy-trans-id {
    cursor: pointer;
    transition: all 0.3s ease;
}

.copy-trans-id:hover {
    background-color: #e3f2fd !important;
    transform: scale(1.02);
}

/* Success animation for trans id */
.success-flash {
    animation: success-flash 0.6s ease-in-out;
}

@keyframes success-flash {
    0% { background-color: transparent; }
    50% { background-color: #d4edda; }
    100% { background-color: transparent; }
}
</style>

';
$body['footer'] = '

 

';
require_once(__DIR__.'/../../models/is_user.php');

if(isset($_GET['limit'])){
    $limit = intval(check_string($_GET['limit']));
}else{
    $limit = 10;
}
if(isset($_GET['page'])){
    $page = check_string(intval($_GET['page']));
}else{
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `user_id` = '".$getUser['id']."' ";
$shortByDate = '';
$transid = '';
$time = '';
$status = '';
$service_name = '';
$service_id = '';
if(!empty($_GET['time'])){
    $time = check_string($_GET['time']);
    $create_date_1 = str_replace('-', '/', $time);
    $create_date_1 = explode(' to ', $create_date_1);
    if($create_date_1[0] != $create_date_1[1]){
        $create_date_1 = [$create_date_1[0].' 00:00:00', $create_date_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$create_date_1[0]."' AND `created_at` <= '".$create_date_1[1]."' ";
    }
}
if(!empty($_GET['transid'])){
    $transid = check_string($_GET['transid']);
    $where .= ' AND `trans_id` LIKE "%'.$transid.'%" ';
}
if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "'.$status.'" ';
}
if(!empty($_GET['service_name'])){
    $service_name = check_string($_GET['service_name']);
    $where .= ' AND `service_name` LIKE "%'.$service_name.'%" ';
}
if(!empty($_GET['service_id'])){
    $service_id = check_string($_GET['service_id']);
    $where .= ' AND `service_id` = "'.$service_id.'" ';
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

}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `orders` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `orders` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=orders&limit=$limit&shortByDate=$shortByDate&time=$time&transid=$transid&status=$status&service_name=$service_name&service_id=$service_id&"), $from, $totalDatatable, $limit);




require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>


<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Đơn hàng đã mua');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Đơn hàng đã mua');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <?php
            // Lấy thống kê số lượng đơn hàng theo trạng thái
            $stats = [];
            $stats['Completed'] = 0;
            $stats['Pending'] = 0;
            $stats['Processing'] = 0;
            $stats['Canceled'] = 0;
            $stats['Partial'] = 0;
            $stats['In progress'] = 0;
            
            $order_stats = $CMSNT->get_list("SELECT `status`, COUNT(*) as count FROM `orders` WHERE `user_id` = '".$getUser['id']."' GROUP BY `status`");
            foreach ($order_stats as $stat) {
                $stats[$stat['status']] = $stat['count'];
            }
            ?>

            <div class="row">


                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div
                                    class="avatar-title border bg-success-subtle border-success border-opacity-25 rounded-2 fs-17">
                                    <i data-feather="check-circle" class="icon-dual-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['Completed']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đơn hàng hoàn tất');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div
                                    class="avatar-title border bg-warning-subtle border-warning border-opacity-25 rounded-2 fs-17">
                                    <i data-feather="clock" class="icon-dual-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['Pending'] + $stats['Processing']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đơn hàng đang chờ');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div
                                    class="avatar-title border bg-danger-subtle border-danger border-opacity-25 rounded-2 fs-17">
                                    <i data-feather="x-circle" class="icon-dual-danger"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['Canceled'] + $stats['Partial']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đơn hàng đã hủy');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div
                                    class="avatar-title border bg-primary-subtle border-primary border-opacity-25 rounded-2 fs-17">
                                    <i data-feather="play-circle" class="icon-dual-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['In progress']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đơn hàng đang chạy');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
                <?php if($CMSNT->site('notice_orders') != ''): ?>
                <div class="col-xl-12">
                    <div class="card overflow-hidden">
                        <div class="card-body bg-info-subtle d-flex">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start">
                                    <?=$CMSNT->site('notice_orders');?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="col-xl-12" <?=$aos['fade-up'];?>>
                <div class="card">
                    <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                        <form action="<?=base_url();?>" method="GET">
                            <input type="hidden" name="action" value="orders">
                            <div class="row g-3">
                                <div class="col-xxl-3 col-sm-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search bg-light border-light"
                                            name="transid" value="<?=$transid;?>" placeholder="<?=__('Mã đơn hàng');?>">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search bg-light border-light"
                                            name="service_id" value="<?=$service_id;?>"
                                            placeholder="<?=__('ID dịch vụ');?>">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search bg-light border-light"
                                            name="service_name" value="<?=$service_name;?>"
                                            placeholder="<?=__('Tên dịch vụ');?>">

                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-6">
                                    <div class="search-box">
                                        <select name="status" class="form-control bg-light border-light">
                                            <option value=""><?=__('-- Trạng thái --');?></option>
                                            <?php foreach($config_status_order as $key => $value): ?>
                                            <option value="<?=$key;?>" <?=$status == $key ? 'selected' : '';?>>
                                                <?=$value;?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <i class="ri-computer-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6 col-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control bg-light border-light"
                                            id="flatpickr-range" name="time"
                                            placeholder="<?=__('Chọn thời gian cần tìm');?>" value="<?=$time;?>"
                                            readonly>
                                        <i class="ri-calendar-line search-icon"></i>
                                    </div>
                                </div>
                                <div class="col-xxl-3 col-sm-6">
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary waves-effect waves-light">
                                            <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                        </button>
                                        <a href="<?=base_url('?action=orders');?>"
                                            class="btn btn-light waves-effect waves-light">
                                            <i class="ri-delete-bin-line me-1 align-bottom"></i> <?=__('Bỏ lọc');?>
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-lg-6 col-6 mb-2 mb-lg-0">
                                    <div class="d-flex flex-column flex-sm-row align-items-sm-center gap-2">
                                        <label class="form-label mb-0 me-2"><?=__('Hiển thị:');?></label>
                                        <select name="limit" onchange="this.form.submit()" class="form-select"
                                            style="width: auto">
                                            <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                            <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                            <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                            <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                            <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                            <option <?=$limit == 500 ? 'selected' : '';?> value="500">500</option>
                                            <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-6">
                                    <div
                                        class="d-flex flex-column flex-sm-row align-items-sm-center gap-2 justify-content-lg-end">
                                        <label class="form-label mb-0 me-2"><?=__('Sắp xếp theo ngày:');?></label>
                                        <select name="shortByDate" onchange="this.form.submit()" class="form-select"
                                            style="width: auto">
                                            <option value=""><?=__('Tất cả');?></option>
                                            <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1">
                                                <?=__('Hôm nay');?></option>
                                            <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2">
                                                <?=__('Tuần này');?></option>
                                            <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3">
                                                <?=__('Tháng này');?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div>
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap table-striped table-hover table-bordered">
                                    <thead class="table-dark">
                                        <tr>
                                            <th class="text-center">
                                                <i class="ri-hashtag me-1"></i><?=__('Mã đơn hàng');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-calendar-line me-1"></i><?=__('Thời gian đặt hàng');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-service-line me-1"></i><?=__('Dịch vụ');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-link me-1"></i><?=__('Liên kết');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-chat-3-line me-1"></i><?=__('Bình luận');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-flag-line me-1"></i><?=__('Trạng thái');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-money-dollar-circle-line me-1"></i><?=__('Thanh toán');?>
                                            </th>
                                            <th class="text-end">
                                                <i class="ri-add-circle-line me-1"></i><?=__('Số lượng cần tăng');?>
                                            </th>
                                            <th class="text-end">
                                                <i class="ri-play-circle-line me-1"></i><?=__('Ban đầu');?>
                                            </th>
                                            <th class="text-end">
                                                <i class="ri-pause-circle-line me-1"></i><?=__('Còn lại');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-refresh-line me-1"></i><?=__('Cập nhật');?>
                                            </th>
                                            <th class="text-center">
                                                <i class="ri-settings-3-line me-1"></i><?=__('Thao tác');?>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all" id="invoice-list-data">
                                        <?php if(empty($listDatatable)): ?>
                                        <tr>
                                            <td colspan="12" class="text-center">
                                                <div class="text-center p-3">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2"><?=__('Không tìm thấy kết quả');?></h5>
                                                    <p class="text-muted mb-0">
                                                        <?=__('Không có đơn hàng nào được tìm thấy');?>
                                                    </p>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach($listDatatable as $order): ?>
                                        <tr>
                                            <td class="text-center">
                                                <input type="text"
                                                    class="form-control copy-trans-id bg-light border-0 text-center fw-bold"
                                                    value="<?=$order['trans_id'];?>" readonly data-bs-toggle="tooltip"
                                                    data-bs-placement="top"
                                                    title="<?=__('Click để sao chép mã đơn hàng');?>"
                                                    style="max-width: 120px; margin: 0 auto; cursor: pointer; font-size: 10px;">
                                            </td>
                                            <td class="text-center"><span class="badge bg-light text-body"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    title="<?=timeAgo(strtotime($order['created_at']));?>"><?=$order['created_at'];?></span>
                                            </td>
                                            <td class="text-left">
                                                <span class="badge border border-secondary text-secondary me-1">ID:
                                                    <?=$order['service_id'];?></span>
                                                <span class="service-name-short cursor-pointer" data-bs-toggle="tooltip"
                                                    title="<?=__('Nhấn để xem đầy đủ');?>"><?=mb_strimwidth($order['service_name'], 0, 40, "...");?></span>
                                                <span
                                                    class="service-name-full d-none"><?=$order['service_name'];?></span>
                                            </td>
                                            <td class="text-center"><input type="text" class="form-control copy-link" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="<?=$order['link'];?>"
                                                    value="<?=$order['link'];?>" readonly></td>
                                            <td class="text-center">
                                                <?php if(!empty($order['comment'])): ?>
                                                <div class="comment-container">
                                                    <button type="button" class="btn btn-light btn-sm view-comment-btn"
                                                        data-comment="<?=htmlspecialchars($order['comment'], ENT_QUOTES);?>"
                                                        data-bs-toggle="tooltip" data-bs-placement="top"
                                                        title="<?=__('Xem bình luận');?>">
                                                        <i class="ri-chat-3-line me-1"></i>
                                                        <?=__('Xem');?>
                                                    </button>
                                                </div>
                                                <?php else: ?>
                                                <span class="text-muted"><i class="ri-chat-off-line"></i>
                                                    <?=__('Không có');?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?=display_service($order['status']);?><br><small><?=$order['reason'];?></small>
                                            </td>
                                            <td class="text-end text-secondary">
                                                <?=format_currency($order['pay']);?></td>
                                            <td class="text-end text-success"><?=format_cash($order['quantity']);?>
                                            </td>
                                            <td class="text-end text-info"><?=format_cash($order['start_count']);?>
                                            </td>
                                            <td class="text-end text-danger"><?=format_cash($order['remains']);?>
                                            </td>
                                            <td class="text-center"><span class="badge bg-light text-body"
                                                    data-toggle="tooltip" data-placement="bottom"
                                                    title="<?=timeAgo(strtotime($order['updated_at']));?>"><?=$order['updated_at'];?></span>
                                            </td>
                                            <td class="text-center">
                                                <?php if($order['status'] == 'Completed' && $CMSNT->site('support_tickets_status') == 1 && $CMSNT->site('support_tickets_order_history') == 1): ?>
                                                <button
                                                    class="btn btn-sm btn-danger waves-effect waves-light report-order"
                                                    data-bs-toggle="tooltip" data-bs-placement="left"
                                                    title="<?=__('Tạo ticket khiếu nại đơn hàng này');?>"
                                                    data-transid="<?=$order['trans_id'];?>"><i
                                                        class="ri-flag-line"></i></button>
                                                <?php endif; ?>
                                                <?php if($order['refill'] == 1 && $order['status'] == 'Completed'): ?>
                                                <?php 
                                                    // Kiểm tra đơn hàng hoàn thành sau 24 giờ mới hiển thị nút bảo hành
                                                    $current_time = time();
                                                    $order_completed_time = strtotime($order['updated_at']);
                                                    $time_diff = $current_time - $order_completed_time;
                                                    $hours_diff = $time_diff / 3600; // Chuyển đổi giây sang giờ
                                                    
                                                    if($hours_diff >= 24 && $order['refill_status'] == 0): 
                                                    ?>
                                                <button
                                                    class="btn btn-sm btn-secondary waves-effect waves-light refill-order"
                                                    data-transid="<?=$order['trans_id'];?>"><i class="ri-swap-fill"></i>
                                                    <?=__('Bảo hành');?></button>
                                                <?php elseif($order['refill_status'] == 0 && $hours_diff < 24): ?>
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="<?=__('Bạn có thể bảo hành sau 24 giờ kể từ khi đơn hàng hoàn thành');?>"
                                                    style="cursor: not-allowed;">
                                                    <button class="btn btn-sm btn-secondary waves-effect waves-light"
                                                        disabled><i class="ri-swap-fill"></i>
                                                        <?=__('Bảo hành');?></button>
                                                </span>
                                                <?php elseif($order['refill_status'] == 2 || $order['refill_status'] == 1): ?>
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="<?=__('Đơn hàng đang trong quá trình bảo hành');?>"
                                                    style="cursor: not-allowed;">
                                                    <button class="btn btn-sm btn-secondary waves-effect waves-light"
                                                        disabled><i class="ri-swap-fill"></i>
                                                        <?=__('Bảo hành');?></button>
                                                </span>
                                                <?php elseif($order['refill_status'] == 3): ?>
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="<?=__('Không thể bảo hành đơn hàng này');?>"
                                                    style="cursor: not-allowed;">
                                                    <button class="btn btn-sm btn-secondary waves-effect waves-light"
                                                        disabled><i class="ri-swap-fill"></i>
                                                        <?=__('Bảo hành');?></button>
                                                </span>
                                                <?php endif; ?>
                                                <?php endif; ?>

                                                <?php if($order['cancel'] == 1 && $order['status'] != 'Completed' && $order['status'] != 'Canceled' && $order['status'] != 'Partial'):?>
                                                <?php if(($order['cancel_status'] == 0)): ?>
                                                <button
                                                    class="btn btn-sm btn-danger waves-effect waves-light cancel-order"
                                                    data-transid="<?=$order['trans_id'];?>"><i
                                                        class="ri-close-circle-fill"></i>
                                                    <?=__('Hủy đơn');?></button>
                                                <?php elseif($order['cancel_status'] == 2 || $order['cancel_status'] == 1): ?>
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="<?=__('Đơn hàng đang trong quá trình hủy');?>"
                                                    style="cursor: not-allowed;">
                                                    <button class="btn btn-sm btn-danger waves-effect waves-light"
                                                        disabled><i class="ri-close-circle-fill"></i>
                                                        <?=__('Hủy đơn');?></button>
                                                </span>
                                                <?php elseif($order['cancel_status'] == 3): ?>
                                                <span data-bs-toggle="tooltip" data-bs-placement="bottom"
                                                    title="<?=__('Không thể hủy đơn hàng này');?>"
                                                    style="cursor: not-allowed;">
                                                    <button class="btn btn-sm btn-danger waves-effect waves-light"
                                                        disabled><i class="ri-close-circle-fill"></i>
                                                        <?=__('Hủy đơn');?></button>
                                                </span>
                                                <?php endif?>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?=$totalDatatable > $limit ? $urlDatatable : '';?>
                            <?php if(!empty($listDatatable)): ?>
                            <div class="mt-1">
                                <div class="p-3 bg-soft-info rounded-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <span class="text-muted me-3"><?=__('Tổng số tiền đã thanh toán');?>:</span>
                                            <span
                                                class="fw-bold text-danger fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `orders` WHERE $where AND `status` = 'Completed' ")['SUM(`price`)']);?></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
</div>


<?php
require_once(__DIR__.'/footer.php');
?>

<!-- Modal Xác nhận hủy đơn -->
<div class="modal fade zoomIn" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body p-4">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/tdrtiskw.json" trigger="loop"
                        colors="primary:#f7b84b,secondary:#f06548" style="width:120px;height:120px">
                    </lord-icon>
                    <div class="mt-4 pt-2 fs-15">
                        <h4 class="fs-18"><?=__('Bạn có chắc chắn không?');?></h4>
                        <p class="text-muted mx-4 mb-0">
                            <?=__('Hệ thống sẽ thực hiện hủy đơn này và hoàn lại tiền còn thừa nếu có khi bạn xác nhận!');?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light w-sm" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                <button type="button" class="btn btn-danger w-sm"
                    id="confirmCancelOrder"><?=__('Xác nhận hủy');?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận bảo hành -->
<div class="modal fade zoomIn" id="refillOrderModal" tabindex="-1" aria-labelledby="refillOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0">
            <div class="modal-body p-4">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/nocovwne.json" trigger="loop"
                        colors="primary:#0ab39c,secondary:#405189" style="width:120px;height:120px">
                    </lord-icon>
                    <div class="mt-4 pt-2 fs-15">
                        <h4 class="fs-18"><?=__('Bạn có chắc chắn không?');?></h4>
                        <p class="text-muted mx-4 mb-0">
                            <?=__('Hệ thống sẽ thực hiện bảo hành đơn hàng này khi bạn xác nhận!');?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light w-sm" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                <button type="button" class="btn btn-secondary w-sm"
                    id="confirmRefillOrder"><?=__('Xác nhận bảo hành');?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xem bình luận -->
<div class="modal fade" id="commentModal" tabindex="-1" aria-labelledby="commentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label"><i class="ri-chat-3-line me-2"></i><?=__('Nội dung bình luận:');?></label>
                    <textarea class="form-control" id="commentContent" rows="5" readonly></textarea>
                </div>
                <div class="mt-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-danger btn-sm" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i><?=__('Đóng');?>
                    </button>
                    <button type="button" class="btn btn-primary btn-sm" id="copyCommentBtn">
                        <i class="ri-file-copy-line me-1"></i><?=__('Sao chép');?>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal tạo ticket khiếu nại -->
<div class="modal fade" id="complaintModal" tabindex="-1" aria-labelledby="complaintModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="complaintModalLabel">
                    <i class="ri-flag-line me-2"></i><?=__('Tạo ticket khiếu nại đơn hàng');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="complaintForm">
                    <!-- Thông tin đơn hàng -->
                    <div class="alert alert-info border-0 mb-4">
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <i class="ri-information-line fs-20"></i>
                            </div>
                            <div class="flex-grow-1 ms-3">
                                <h6 class="alert-heading mb-1"><?=__('Thông tin đơn hàng');?></h6>
                                <div class="row text-sm">
                                    <div class="col-md-6">
                                        <strong><?=__('Mã đơn hàng');?>:</strong> <span id="orderTransId">#</span>
                                    </div>
                                    <div class="col-md-6">
                                        <strong><?=__('Dịch vụ');?>:</strong> <span id="orderServiceName">-</span>
                                    </div>
                                    <div class="col-md-6 mt-1">
                                        <strong><?=__('Số tiền');?>:</strong> <span id="orderPrice">-</span>
                                    </div>
                                    <div class="col-md-6 mt-1">
                                        <strong><?=__('Trạng thái');?>:</strong> <span id="orderStatus">-</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Form nhập khiếu nại -->
                    <div class="mb-3">
                        <label for="complaintReason" class="form-label">
                            <?=__('Tiêu đề khiếu nại');?> <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="complaintReason" name="reason" required>
                            <option value=""><?=__('Chọn lý do khiếu nại');?></option>
                            <option value="<?=__('Dịch vụ không được thực hiện đúng');?>">
                                <?=__('Dịch vụ không được thực hiện đúng');?></option>
                            <option value="<?=__('Chất lượng dịch vụ không đạt yêu cầu');?>">
                                <?=__('Chất lượng dịch vụ không đạt yêu cầu');?></option>
                            <option value="<?=__('Thực hiện sai dịch vụ');?>"><?=__('Thực hiện sai dịch vụ');?></option>
                            <option value="<?=__('Dịch vụ bị chậm trễ quá mức');?>">
                                <?=__('Dịch vụ bị chậm trễ quá mức');?></option>
                            <option value="<?=__('Lỗi kỹ thuật');?>"><?=__('Lỗi kỹ thuật');?></option>
                            <option value="<?=__('Lý do khác');?>"><?=__('Lý do khác');?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="complaintContent" class="form-label">
                            <?=__('Nội dung khiếu nại');?> <span class="text-danger">*</span>
                        </label>
                        <textarea class="form-control" id="complaintContent" name="content" rows="5"
                            placeholder="<?=__('Mô tả chi tiết vấn đề bạn gặp phải với đơn hàng này...');?>"
                            required></textarea>
                        <div class="form-text">
                            <i class="ri-information-line me-1"></i>
                            <?=__('Vui lòng mô tả chi tiết để chúng tôi có thể hỗ trợ bạn tốt nhất');?>
                        </div>
                    </div>

                    <input type="hidden" id="complaintOrderId" name="order_id">
                    <input type="hidden" name="token" value="<?=$getUser['token'];?>">
                    <input type="hidden" name="category" value="order">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i><?=__('Hủy');?>
                </button>
                <button type="button" class="btn btn-danger" id="submitComplaintBtn">
                    <span class="btn-spinner d-none">
                        <i class="me-1 spinner-border spinner-border-sm"></i><?=__('Đang gửi...');?>
                    </span>
                    <span class="btn-text">
                        <i class="ri-send-plane-line me-1"></i><?=__('Gửi khiếu nại');?>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
document.addEventListener("DOMContentLoaded", function() {

    // Xử lý hiển thị modal bình luận
    var viewCommentBtns = document.querySelectorAll(".view-comment-btn");
    viewCommentBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            var comment = this.getAttribute('data-comment');
            document.getElementById('commentContent').value = comment;

            var commentModal = new bootstrap.Modal(document.getElementById('commentModal'));
            commentModal.show();
        });
    });

    // Xử lý sao chép bình luận từ modal
    document.getElementById('copyCommentBtn').addEventListener('click', function() {
        var textarea = document.getElementById('commentContent');

        navigator.clipboard.writeText(textarea.value).then(function() {
            // Success animation
            var btn = document.getElementById('copyCommentBtn');
            btn.innerHTML = '<i class="ri-check-line me-1"></i><?=__('Đã sao chép');?>';
            showMessage("<?=__('Đã sao chép bình luận');?>", "success");

            setTimeout(function() {
                btn.innerHTML =
                    '<i class="ri-file-copy-line me-1"></i><?=__('Sao chép');?>';
            }, 1000);
        }).catch(function() {
            // Fallback for older browsers
            textarea.select();
            document.execCommand("copy");
            showMessage("<?=__('Đã sao chép bình luận');?>", "success");
        });
    });


    // Xử lý copy mã đơn hàng khi click vào input
    var copyTransIds = document.querySelectorAll(".copy-trans-id");
    copyTransIds.forEach(function(input) {
        input.addEventListener("click", function() {
            var transId = this.value;
            navigator.clipboard.writeText(transId).then(function() {
                input.classList.add('success-flash');
                showMessage("<?=__('Đã sao chép mã đơn hàng');?>", "success");
                setTimeout(function() {
                    input.classList.remove('success-flash');
                }, 600);
            }).catch(function() {
                // Fallback for older browsers
                input.select();
                document.execCommand("copy");
                showMessage("<?=__('Đã sao chép mã đơn hàng');?>", "success");
            });
        });
    });

    var copyLinks = document.querySelectorAll(".copy-link");
    copyLinks.forEach(function(input) {
        input.addEventListener("click", function() {
            this.select();
            document.execCommand("copy");

            // Hiển thị thông báo
            var originalBackground = this.style.background;
            this.style.background = "#d4edda";
            showMessage("<?=__('Đã sao chép liên kết');?>", "success");
            setTimeout(function() {
                input.style.background = originalBackground;
            }, 300);
        });
    });
    var copyComments = document.querySelectorAll(".copy-comment");
    copyComments.forEach(function(textarea) {
        textarea.addEventListener("click", function() {
            this.select();
            document.execCommand("copy");

            // Hiển thị thông báo
            var originalBackground = this.style.background;
            this.style.background = "#d4edda";
            showMessage("<?=__('Đã sao chép bình luận');?>", "success");
            setTimeout(function() {
                textarea.style.background = originalBackground;
            }, 300);
        });
    });

    // Xử lý hiển thị tên dịch vụ
    var serviceNameShorts = document.querySelectorAll(".service-name-short");
    serviceNameShorts.forEach(function(nameShort) {
        nameShort.addEventListener("click", function() {
            // Lấy phần tử hiển thị tên đầy đủ
            var nameFull = this.nextElementSibling;

            // Nếu đang ẩn thì hiển thị đầy đủ
            if (nameFull.classList.contains('d-none')) {
                nameFull.classList.remove('d-none');
                this.classList.add('d-none');
                // Thêm nút đóng
                nameFull.innerHTML +=
                    ' <a href="javascript:void(0)" class="text-danger close-full-name"><i class="ri-close-line"></i></a>';

                // Xử lý sự kiện khi nhấn nút đóng
                nameFull.querySelector('.close-full-name').addEventListener('click', function(
                    e) {
                    e.stopPropagation();
                    nameFull.classList.add('d-none');
                    nameShort.classList.remove('d-none');
                    // Xóa nút đóng
                    var fullText = nameFull.innerHTML;
                    nameFull.innerHTML = fullText.substring(0, fullText.indexOf(
                        '<a href='));
                });
            }
        });
    });

    // Xử lý nút hủy đơn
    var cancelButtons = document.querySelectorAll(".cancel-order");
    var cancelOrderTransId = null;

    cancelButtons.forEach(function(button) {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            cancelOrderTransId = this.getAttribute("data-transid");
            var cancelModal = new bootstrap.Modal(document.getElementById('cancelOrderModal'));
            cancelModal.show();
        });
    });

    // Xử lý nút xác nhận hủy trong modal
    document.getElementById("confirmCancelOrder").addEventListener("click", function() {
        if (cancelOrderTransId) {
            // Hiển thị nút loading
            var btnConfirm = this;
            btnConfirm.innerHTML =
                '<i class="mdi mdi-loading mdi-spin me-1"></i> <?=__('Đang xử lý...');?>';
            btnConfirm.disabled = true;

            // Sử dụng Ajax để gửi yêu cầu POST đến API
            $.ajax({
                url: '<?=base_url('ajaxs/client/smmpanel.php');?>',
                type: 'POST',
                data: {
                    key: '<?=$getUser['api_key'];?>',
                    action: 'cancel',
                    orders: cancelOrderTransId
                },
                dataType: 'json',
                success: function(response) {
                    // Đóng modal
                    var cancelModal = bootstrap.Modal.getInstance(document.getElementById(
                        'cancelOrderModal'));
                    cancelModal.hide();

                    // Xử lý phản hồi theo định dạng mới
                    if (Array.isArray(response)) {
                        // Tìm thông tin đơn hàng hiện tại
                        var currentOrder = response.find(function(item) {
                            return item.order == cancelOrderTransId;
                        });
                        if (currentOrder) {
                            if (currentOrder.cancel === 1) {
                                // Hủy đơn thành công
                                showMessage('<?=__('Đơn hàng đã được hủy thành công');?>',
                                    'success');
                                // Tải lại trang sau 1 giây
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else if (currentOrder.cancel && currentOrder.cancel.error) {
                                // Có lỗi khi hủy đơn
                                showMessage(currentOrder.cancel.error, 'error');
                                btnConfirm.innerHTML = '<?=__('Xác nhận hủy');?>';
                                btnConfirm.disabled = false;
                            } else {
                                // Trường hợp khác
                                showMessage('<?=__('Có lỗi xảy ra khi hủy đơn hàng');?>',
                                    'error');
                                btnConfirm.innerHTML = '<?=__('Xác nhận hủy');?>';
                                btnConfirm.disabled = false;
                            }
                        } else {
                            showMessage('<?=__('Không tìm thấy thông tin đơn hàng');?>',
                                'error');
                            btnConfirm.innerHTML = '<?=__('Xác nhận hủy');?>';
                            btnConfirm.disabled = false;
                        }
                    } else if (response.status == 'success') {
                        // Xử lý theo định dạng cũ (để tương thích ngược)
                        showMessage(response.msg, 'success');
                        // Tải lại trang sau 1 giây
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showMessage(response.msg ||
                            '<?=__('Có lỗi xảy ra khi hủy đơn hàng');?>', 'error');
                        btnConfirm.innerHTML = '<?=__('Xác nhận hủy');?>';
                        btnConfirm.disabled = false;
                    }
                },
                error: function(xhr, status, error) {
                    // Đóng modal
                    var cancelModal = bootstrap.Modal.getInstance(document.getElementById(
                        'cancelOrderModal'));
                    cancelModal.hide();

                    showMessage('<?=__('Có lỗi xảy ra trong quá trình xử lý');?>', 'error');
                    btnConfirm.innerHTML = '<?=__('Xác nhận hủy');?>';
                    btnConfirm.disabled = false;
                }
            });
        }
    });

    // Xử lý nút bảo hành
    var refillButtons = document.querySelectorAll(".refill-order");
    var refillOrderTransId = null;

    refillButtons.forEach(function(button) {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            refillOrderTransId = this.getAttribute("data-transid");
            var refillModal = new bootstrap.Modal(document.getElementById('refillOrderModal'));
            refillModal.show();
        });
    });

    // Xử lý nút xác nhận bảo hành trong modal
    document.getElementById("confirmRefillOrder").addEventListener("click", function() {
        if (refillOrderTransId) {
            // Hiển thị nút loading
            var btnConfirm = this;
            btnConfirm.innerHTML =
                '<i class="mdi mdi-loading mdi-spin me-1"></i> <?=__('Đang xử lý...');?>';
            btnConfirm.disabled = true;

            // Sử dụng Ajax để gửi yêu cầu POST đến API
            $.ajax({
                url: '<?=base_url('ajaxs/client/smmpanel.php');?>',
                type: 'POST',
                data: {
                    key: '<?=$getUser['api_key'];?>',
                    action: 'refill',
                    orders: refillOrderTransId
                },
                dataType: 'json',
                success: function(response) {
                    // Đóng modal
                    var refillModal = bootstrap.Modal.getInstance(document.getElementById(
                        'refillOrderModal'));
                    refillModal.hide();

                    // Xử lý phản hồi theo định dạng mới
                    if (Array.isArray(response)) {
                        // Tìm thông tin đơn hàng hiện tại
                        var currentOrder = response.find(function(item) {
                            return item.order == refillOrderTransId;
                        });
                        if (currentOrder) {
                            if (currentOrder.refill === 1) {
                                // Bảo hành thành công
                                showMessage(
                                    '<?=__('Yêu cầu bảo hành đã được gửi thành công');?>',
                                    'success');
                                // Tải lại trang sau 1 giây
                                setTimeout(function() {
                                    location.reload();
                                }, 1000);
                            } else if (currentOrder.refill && currentOrder.refill.error) {
                                // Có lỗi khi bảo hành
                                showMessage(currentOrder.refill.error, 'error');
                                btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                                btnConfirm.disabled = false;
                            } else {
                                // Trường hợp khác
                                showMessage(
                                    '<?=__('Có lỗi xảy ra khi bảo hành đơn hàng');?>',
                                    'error');
                                btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                                btnConfirm.disabled = false;
                            }
                        } else {
                            showMessage('<?=__('Không tìm thấy thông tin đơn hàng');?>',
                                'error');
                            btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                            btnConfirm.disabled = false;
                        }
                    } else if (response.refill === "1") {
                        // Xử lý theo định dạng một đơn lẻ
                        showMessage('<?=__('Yêu cầu bảo hành đã được gửi thành công');?>',
                            'success');
                        // Tải lại trang sau 1 giây
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else if (response.refill && response.refill.error) {
                        showMessage(response.refill.error, 'error');
                        btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                        btnConfirm.disabled = false;
                    } else {
                        showMessage('<?=__('Có lỗi xảy ra khi bảo hành đơn hàng');?>',
                            'error');
                        btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                        btnConfirm.disabled = false;
                    }
                },
                error: function(xhr, status, error) {
                    // Đóng modal
                    var refillModal = bootstrap.Modal.getInstance(document.getElementById(
                        'refillOrderModal'));
                    refillModal.hide();

                    showMessage('<?=__('Có lỗi xảy ra trong quá trình xử lý');?>', 'error');
                    btnConfirm.innerHTML = '<?=__('Xác nhận bảo hành');?>';
                    btnConfirm.disabled = false;
                }
            });
        }
    });

    // Xử lý nút khiếu nại đơn hàng
    var complaintButtons = document.querySelectorAll(".report-order");

    complaintButtons.forEach(function(button) {
        button.addEventListener("click", function(e) {
            e.preventDefault();
            var transId = this.getAttribute("data-transid");

            // Tìm thông tin đơn hàng từ DOM
            var orderRow = this.closest('tr');
            var serviceName = orderRow.querySelector('.service-name-short')?.textContent
                ?.trim() ||
                orderRow.querySelector('.service-name-full')?.textContent?.trim() || 'N/A';
            var orderPrice = orderRow.cells[5]?.textContent?.trim() || 'N/A';
            var orderStatus = orderRow.cells[6]?.textContent?.trim() || 'N/A';

            // Điền thông tin vào modal
            document.getElementById('orderTransId').textContent = '#' + transId;
            document.getElementById('orderServiceName').textContent = serviceName;
            document.getElementById('orderPrice').textContent = orderPrice;
            document.getElementById('orderStatus').textContent = orderStatus;
            document.getElementById('complaintOrderId').value = transId;

            // Tự động điền tiêu đề
            document.getElementById('complaintReason').value = '';
            document.getElementById('complaintContent').value = '';

            // Hiển thị modal
            var complaintModal = new bootstrap.Modal(document.getElementById('complaintModal'));
            complaintModal.show();
        });
    });

    // Xử lý submit form khiếu nại
    document.getElementById("submitComplaintBtn").addEventListener("click", function() {
        var form = document.getElementById('complaintForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var subject = document.getElementById('complaintReason').value;
        var reason = document.getElementById('complaintReason').value;
        var content = document.getElementById('complaintContent').value;
        var orderId = document.getElementById('complaintOrderId').value;

        // Lấy text của option được chọn làm subject
        var reasonText = document.getElementById('complaintReason').options[document.getElementById(
            'complaintReason').selectedIndex].text;
        var ticketSubject = reasonText;

        // Tạo nội dung ticket với thông tin chi tiết
        var ticketContent = '<?=__('Lý do khiếu nại');?>: ' + reasonText + '\n\n';
        ticketContent += '<?=__('Chi tiết vấn đề');?>:\n' + content;

        var $btn = $(this);

        // Thay đổi trạng thái nút
        $btn.find('.btn-text').addClass('d-none');
        $btn.find('.btn-spinner').removeClass('d-none');
        $btn.prop('disabled', true);

        $.ajax({
            url: '<?=base_url('ajaxs/client/ticket.php');?>',
            type: 'POST',
            data: {
                action: 'createTicket',
                token: '<?=$getUser['token'];?>',
                subject: ticketSubject,
                category: 'order',
                order_id: orderId,
                content: ticketContent
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    // Đóng modal
                    var complaintModal = bootstrap.Modal.getInstance(document
                        .getElementById('complaintModal'));
                    complaintModal.hide();

                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>!',
                        text: response.msg,
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        window.location.href =
                            '<?=base_url('client/support-tickets');?>';
                    });
                } else {
                    showMessage(response.msg, 'error');
                }
            },
            error: function() {
                showMessage('<?=__('Không thể kết nối đến server');?>', 'error');
            },
            complete: function() {
                // Khôi phục trạng thái nút
                $btn.find('.btn-spinner').addClass('d-none');
                $btn.find('.btn-text').removeClass('d-none');
                $btn.prop('disabled', false);
            }
        });
    });
});

flatpickr("#flatpickr-range", {
    mode: "range",
    dateFormat: "Y-m-d",
    enableTime: false,
    altInput: true,
    altFormat: "d/m/Y",
    defaultDate: "<?=$time?>",
    locale: {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: [
                "<?=__('CN');?>",
                "<?=__('T2');?>",
                "<?=__('T3');?>",
                "<?=__('T4');?>",
                "<?=__('T5');?>",
                "<?=__('T6');?>",
                "<?=__('T7');?>"
            ],
            longhand: [
                "<?=__('Chủ Nhật');?>",
                "<?=__('Thứ 2');?>",
                "<?=__('Thứ 3');?>",
                "<?=__('Thứ 4');?>",
                "<?=__('Thứ 5');?>",
                "<?=__('Thứ 6');?>",
                "<?=__('Thứ 7');?>"
            ]
        },
        months: {
            shorthand: [
                "<?=__('Th1');?>",
                "<?=__('Th2');?>",
                "<?=__('Th3');?>",
                "<?=__('Th4');?>",
                "<?=__('Th5');?>",
                "<?=__('Th6');?>",
                "<?=__('Th7');?>",
                "<?=__('Th8');?>",
                "<?=__('Th9');?>",
                "<?=__('Th10');?>",
                "<?=__('Th11');?>",
                "<?=__('Th12');?>"
            ],
            longhand: [
                "<?=__('Tháng 1');?>",
                "<?=__('Tháng 2');?>",
                "<?=__('Tháng 3');?>",
                "<?=__('Tháng 4');?>",
                "<?=__('Tháng 5');?>",
                "<?=__('Tháng 6');?>",
                "<?=__('Tháng 7');?>",
                "<?=__('Tháng 8');?>",
                "<?=__('Tháng 9');?>",
                "<?=__('Tháng 10');?>",
                "<?=__('Tháng 11');?>",
                "<?=__('Tháng 12');?>"
            ]
        }
    }
});

// Khởi tạo tooltips Bootstrap
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});
</script>