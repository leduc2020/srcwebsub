<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => __('Đơn hàng đã lên lịch').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<style>
.cursor-pointer {
    cursor: pointer;
}
.close-full-name {
    position: absolute;
    right: 4px;
    top: 4px;
}

/* Cải thiện giao diện table */
.order-row {
    transition: all 0.3s ease;
}

.order-row:hover {
    background-color: rgba(13, 110, 253, 0.05);
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}


.badge {
    transition: all 0.3s ease;
    border-radius: 8px;
}

.badge:hover {
    transform: scale(1.05);
}

/* Animation cho copy buttons */
.copy-link-btn, .copy-comment-btn {
    transition: all 0.3s ease;
}

.copy-link-btn:hover, .copy-comment-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.2);
}

/* Style cho input link */
.input-group input.copy-link {
    font-size: 0.85rem;
    transition: all 0.3s ease;
}

.input-group input.copy-link:focus {
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Style cho status container */
.status-container {
    transition: all 0.3s ease;
}

/* Animation cho modal */
.modal.fade .modal-dialog {
    transition: transform 0.3s ease-out;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .table-responsive {
        font-size: 0.85rem;
    }
    
    .badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
    }
    
    .btn-sm {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Loading animation */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.loading {
    animation: pulse 1.5s infinite;
}

/* Success animation */
@keyframes success-flash {
    0% { background-color: transparent; }
    50% { background-color: #d4edda; }
    100% { background-color: transparent; }
}

.success-flash {
    animation: success-flash 0.6s ease-in-out;
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
if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "'.$status.'" ';
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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `scheduled_orders` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `scheduled_orders` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=scheduled-orders&limit=$limit&shortByDate=$shortByDate&time=$time&status=$status&service_id=$service_id&"), $from, $totalDatatable, $limit);




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
                        <h4 class="mb-sm-0"><?=__('Đơn hàng đã lên lịch');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Đơn hàng đã lên lịch');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row">
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="scheduled-orders">
                                <div class="row g-3">
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
                                            <select name="status" class="form-control bg-light border-light">
                                                <option value=""><?=__('-- Trạng thái --');?></option>
                                                <?php foreach($config_status_scheduled_orders as $key => $value): ?>
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
                                            <button type="submit"
                                                class="btn btn-primary waves-effect waves-light w-100">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('?action=scheduled-orders');?>"
                                                class="btn btn-light waves-effect waves-light w-100">
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
                                                <th class="text-center" style="min-width: 140px;">
                                                    <i class="ri-calendar-schedule-line me-1"></i><?=__('Thời gian tạo');?>
                                                </th>
                                                <th class="text-center" style="min-width: 160px;">
                                                    <i class="ri-time-line me-1"></i><?=__('Thời gian chạy');?>
                                                </th>
                                                <th style="min-width: 200px;">
                                                    <i class="ri-service-line me-1"></i><?=__('Dịch vụ');?>
                                                </th>
                                                <th class="text-center" style="min-width: 100px;">
                                                    <i class="ri-hashtag me-1"></i><?=__('Số lượng');?>
                                                </th>
                                                <th class="text-center" style="min-width: 150px;">
                                                    <i class="ri-link me-1"></i><?=__('Liên kết');?>
                                                </th>
                                                <th class="text-center" style="min-width: 120px;">
                                                    <i class="ri-chat-3-line me-1"></i><?=__('Bình luận');?>
                                                </th>
                                                <th class="text-center" style="min-width: 120px;">
                                                    <i class="ri-flag-line me-1"></i><?=__('Trạng thái');?>
                                                </th>
                                                <th class="text-center" style="min-width: 100px;">
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
                                                            <?=__('Không có đơn hàng nào được đặt lịch');?>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach($listDatatable as $order): ?>
                                            <tr class="order-row">
                                                <td class="text-center">
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span class="badge bg-light text-body border fw-normal"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="<?=timeAgo(strtotime($order['created_at']));?>">
                                                            <i class="ri-calendar-line me-1"></i>
                                                            <?=date('d/m/Y', strtotime($order['created_at']));?>
                                                        </span>
                                                        <small class="text-muted mt-1">
                                                            <?=date('H:i:s', strtotime($order['created_at']));?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php 
                                                        $time_next = strtotime($order['schedule_time'])-time();
                                                        $schedule_date = date('d/m/Y', strtotime($order['schedule_time']));
                                                        $schedule_time = date('H:i:s', strtotime($order['schedule_time']));
                                                        
                                                        if($time_next > 0){
                                                            $time_next_text = __('Dịch vụ sẽ chạy sau').' '.timeAgo2($time_next);
                                                            $badge_class = 'bg-warning text-dark';
                                                            $icon = 'ri-timer-line';
                                                        } else {
                                                            $time_next_text = __('Đã đến hạn');
                                                            $badge_class = 'bg-danger text-white';
                                                            $icon = 'ri-alarm-warning-line';
                                                        }
                                                    ?>
                                                    <div class="d-flex flex-column align-items-center">
                                                        <span class="badge <?=$badge_class;?> fw-normal"
                                                            data-bs-toggle="tooltip" data-bs-placement="top"
                                                            title="<?=$time_next_text;?>">
                                                            <i class="<?=$icon;?> me-1"></i>
                                                            <?=$schedule_date;?>
                                                        </span>
                                                        <small class="text-muted mt-1">
                                                            <?=$schedule_time;?>
                                                        </small>
                                                    </div>
                                                </td>
                                                <td class="text-left">
                                                    <div class="d-flex align-items-center">
                                                        <span class="badge bg-primary bg-gradient text-white me-2 px-2 py-1">
                                                            <i class="ri-hashtag me-1"></i>ID <?=$order['service_id'];?>
                                                        </span>
                                                        <div class="flex-grow-1">
                                                            <span class="fw-medium">
                                                                <?=getRowRealtime('services', $order['service_id'], 'name');?>
                                                            </span>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info bg-gradient text-white fs-6 px-3 py-2">
                                                        <i class="ri-numbers-line me-1"></i>
                                                        <?=format_cash($order['quantity']);?>
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="input-group input-group-sm">
                                                        <input type="text" class="form-control copy-link border-0 bg-light" 
                                                            value="<?=substr($order['link'], 0, 30) . (strlen($order['link']) > 30 ? '...' : '');?>" 
                                                            data-full-link="<?=$order['link'];?>" readonly 
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="<?=$order['link'];?>">
                                                        <button class="btn btn-outline-secondary btn-sm copy-link-btn" type="button"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="<?=__('Sao chép link');?>">
                                                            <i class="ri-file-copy-line"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <?php if(!empty($order['comment'])): ?>
                                                    <div class="comment-container">
                                                        <button type="button" class="btn btn-light btn-sm view-comment-btn" 
                                                            data-comment="<?=htmlspecialchars($order['comment'], ENT_QUOTES);?>"
                                                            title="<?=__('Xem bình luận');?>">
                                                            <i class="ri-chat-3-line me-1"></i>
                                                            <?=__('Xem');?>
                                                        </button>
                                                    </div>
                                                    <?php else: ?>
                                                    <span class="text-muted"><i class="ri-chat-off-line"></i> <?=__('Không có');?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center">
                                                    <div class="status-container">
                                                        <?=display_status_scheduled_orders($order['status'], $order['reason'] ?? '');?>
                                                        <?php if($order['status'] == 'executed' && !empty($order['order_id'])): ?>
                                                        <div class="mt-1">
                                                            <small class="text-muted">
                                                                <i class="ri-external-link-line"></i> 
                                                                Order: #<?=$order['order_id'];?>
                                                            </small>
                                                        </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <div class="action-buttons">
                                                        <?php if($order['status'] == 'pending'): ?>
                                                        <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" data-bs-target="#cancelOrderModal"
                                                            data-transid="<?=$order['id'];?>"
                                                            data-bs-toggle="tooltip" data-bs-placement="top" 
                                                            title="<?=__('Hủy đơn hàng đã đặt lịch');?>">
                                                            <i class="ri-close-circle-line me-1"></i><?=__('Hủy');?>
                                                        </button>
                                                        <?php else: ?>
                                                        <span class="text-muted">
                                                            <i class="ri-forbid-line"></i> <?=__('Không thể thao tác');?>
                                                        </span>
                                                        <?php endif; ?>
                                                    </div>
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

<!-- Modal xác nhận hủy đơn hàng -->
<div class="modal fade" id="cancelOrderModal" tabindex="-1" aria-labelledby="cancelOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="cancelOrderModalLabel"><?=__('Xác nhận hủy đặt lịch đơn hàng');?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="text-center">
                    <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop" 
                        colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px">
                    </lord-icon>
                    <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                        <h4><?=__('Bạn có chắc chắn muốn hủy đặt lịch đơn hàng này?');?></h4>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=__('Không');?></button>
                <button type="button" id="confirmCancelBtn" class="btn btn-danger"><?=__('Có, hủy đơn hàng');?></button>
            </div>
        </div>
    </div>
</div>

<script>

document.addEventListener("DOMContentLoaded", function() {
    // Xử lý copy link với button mới
    var copyLinkBtns = document.querySelectorAll(".copy-link-btn");
    copyLinkBtns.forEach(function(btn) {
        btn.addEventListener("click", function() {
            var input = this.parentElement.querySelector('.copy-link');
            var fullLink = input.getAttribute('data-full-link');
            
            // Copy to clipboard
            navigator.clipboard.writeText(fullLink).then(function() {
                // Success animation
                btn.classList.add('success-flash');
                btn.innerHTML = '<i class="ri-check-line"></i>';
                showMessage("<?=__('Đã sao chép liên kết');?>", "success");
                
                setTimeout(function() {
                    btn.innerHTML = '<i class="ri-file-copy-line"></i>';
                    btn.classList.remove('success-flash');
                }, 1000);
            }).catch(function() {
                // Fallback for older browsers
                input.select();
                document.execCommand("copy");
                showMessage("<?=__('Đã sao chép liên kết');?>", "success");
            });
        });
    });

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
                btn.innerHTML = '<i class="ri-file-copy-line me-1"></i><?=__('Sao chép');?>';
            }, 1000);
        }).catch(function() {
            // Fallback for older browsers
            textarea.select();
            document.execCommand("copy");
            showMessage("<?=__('Đã sao chép bình luận');?>", "success");
        });
    });

    // Legacy support cho old copy functionality
    var copyLinks = document.querySelectorAll(".copy-link");
    copyLinks.forEach(function(input) {
        input.addEventListener("click", function() {
            var fullLink = this.getAttribute('data-full-link') || this.value;
            navigator.clipboard.writeText(fullLink).then(function() {
                input.classList.add('success-flash');
                showMessage("<?=__('Đã sao chép liên kết');?>", "success");
                setTimeout(function() {
                    input.classList.remove('success-flash');
                }, 600);
            });
        });
    });

    // Xử lý sự kiện mở modal hủy đơn hàng
    var cancelButtons = document.querySelectorAll('[data-bs-target="#cancelOrderModal"]');
    var currentOrderId = null;
    
    cancelButtons.forEach(function(button) {
        button.addEventListener('click', function() {
            currentOrderId = this.getAttribute('data-transid');
        });
    });

    // Xử lý sự kiện xác nhận hủy đơn hàng
    document.getElementById('confirmCancelBtn').addEventListener('click', function() {
        if (!currentOrderId) {
            return;
        }

        // Hiển thị loading
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?=__('Đang xử lý...');?>';
        this.disabled = true;

        // Gửi request hủy đơn hàng
        fetch('<?=base_url('ajaxs/client/remove.php');?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'action=cancelScheduledOrder&id=' + encodeURIComponent(currentOrderId) + '&token=<?=isset($getUser) ? $getUser['token'] : '';?>'
        })
        .then(response => response.json())
        .then(data => {
            // Đóng modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
            modal.hide();

            if (data.status === 'success') {
                // Hiển thị thông báo thành công
                Swal.fire({
                    title: '<?=__('Thành công');?>',
                    text: data.msg,
                    icon: 'success',
                    confirmButtonText: '<?=__('OK');?>'
                }).then(() => {
                    // Reload trang để cập nhật danh sách
                    location.reload();
                });
            } else {
                // Hiển thị thông báo lỗi
                Swal.fire({
                    title: '<?=__('Lỗi');?>',
                    text: data.msg,
                    icon: 'error',
                    confirmButtonText: '<?=__('OK');?>'
                });
            }
        })
        .catch(error => {
            // Đóng modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('cancelOrderModal'));
            modal.hide();

            // Hiển thị thông báo lỗi
            Swal.fire({
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Có lỗi xảy ra khi kết nối đến máy chủ');?>',
                icon: 'error',
                confirmButtonText: '<?=__('OK');?>'
            });
        })
        .finally(() => {
            // Khôi phục trạng thái nút
            document.getElementById('confirmCancelBtn').innerHTML = '<?=__('Có, hủy đơn hàng');?>';
            document.getElementById('confirmCancelBtn').disabled = false;
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