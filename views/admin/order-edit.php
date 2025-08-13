<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Chỉnh sửa đơn hàng'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
 
<!-- Choices Css -->
<link rel="stylesheet" href="'.base_url('public/theme/').'assets/libs/choices.js/public/assets/styles/choices.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
';
$body['footer'] = '
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Internal Select-2.js -->
<script src="'.base_url('public/theme/').'assets/js/select2.js"></script>

';
require_once(__DIR__.'/../../models/is_admin.php');
if (isset($_GET['id'])) {
    $id = check_string($_GET['id']);
    $row = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '$id' ");
    if (!$row) {
        redirect(base_url_admin('orders'));
    }
} else {
    redirect(base_url_admin('orders'));
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
if(checkPermission($getUser['admin'], 'edit_orders_product') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>
<?php
if (isset($_POST['SaveOrder'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }

    // Kiểm tra nếu đơn hàng đã ở trạng thái Canceled hoặc Partial
    if ($row['status'] == 'Canceled' || $row['status'] == 'Partial') {
        // Chỉ cho phép cập nhật một số thông tin, không cho phép thay đổi trạng thái
        $isUpdate = $CMSNT->update("orders", [
            'start_count'   => check_string($_POST['start_count']),
            'remains'       => check_string($_POST['remains']),
            'note'          => check_string($_POST['note']),
            'reason'        => check_string($_POST['reason']),
            'updated_at'    => gettime()
        ], " `id` = '$id' ");

        if ($isUpdate) {
        
            $CMSNT->insert("logs", [
                'user_id'       => $getUser['id'],
                'ip'            => myip(),
                'device'        => getUserAgent(),
                'createdate'    => gettime(),
                'action'        => sprintf(__('Cập nhật thông tin đơn hàng #%s'), $row['trans_id'])
            ]);
            die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.location.href = window.location.href;}</script>');
        }
    } else {
        // Đơn hàng chưa ở trạng thái Canceled hoặc Partial, cho phép cập nhật đầy đủ
        $newStatus = check_string($_POST['status']);
        
        // Kiểm tra nếu đang cập nhật thành trạng thái Canceled và cần hoàn tiền
        if ($newStatus == 'Canceled' && $row['status'] != 'Canceled' && $row['pay'] > 0) {
            // Thực hiện hoàn tiền
            $User = new users();
            $isRefund = $User->RefundCredits(
                $row['user_id'], 
                $row['pay'], 
                '[Admin] '.sprintf(__("Hoàn tiền đơn hàng #%s"), $row['trans_id']), 
                'Canceled_'.$row['trans_id']
            );
            
            if ($isRefund) {
                // Cập nhật trạng thái đơn hàng
                $isUpdate = $CMSNT->update("orders", [
                    'status'        => $newStatus,
                    'start_count'   => check_string($_POST['start_count']),
                    'remains'       => check_string($_POST['remains']),
                    'note'          => check_string($_POST['note']),
                    'pay'           => 0,
                    'cost'          => 0,
                    'updated_at'    => gettime()
                ], " `id` = '$id' ");
                
                if ($isUpdate) {
                
                    $CMSNT->insert("logs", [
                        'user_id'       => $getUser['id'],
                        'ip'            => myip(),
                        'device'        => getUserAgent(),
                        'createdate'    => gettime(),
                        'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s và hoàn tiền tự động'), $row['trans_id'], $newStatus)
                    ]);
                    die('<script type="text/javascript">if(!alert("'.__('Lưu thành công! Đã hoàn tiền cho đơn hàng').'")){window.location.href = window.location.href;}</script>');
                }
            } else {
                die('<script type="text/javascript">if(!alert("'.__('Không thể hoàn tiền cho người dùng').'")){window.history.back();}</script>');
            }
        }
        // Kiểm tra nếu đang cập nhật thành trạng thái Partial và cần hoàn tiền một phần
        else if ($newStatus == 'Partial') {
            $remains = intval($_POST['remains']);
            $originalQty = $row['quantity'];
            
            // Kiểm tra giá trị remains hợp lệ
            if ($remains < 0 || $remains >= $originalQty) {
                die('<script type="text/javascript">if(!alert("'.__('Số lượng còn lại không hợp lệ').'")){window.history.back();}</script>');
            }
            
            // Tính số lượng cần hoàn
            $refundQty = $originalQty - $remains;
            
            // Tính số tiền cần hoàn lại
            $refundAmount = 0;
            if ($originalQty > 0) {
                $pricePerUnit = $row['pay'] / $originalQty; // Giá trên mỗi đơn vị
                $refundAmount = $pricePerUnit * $refundQty;
            }
            
            if ($refundAmount > 0) {
                // Thực hiện hoàn tiền
                $User = new users();
                $isRefund = $User->RefundCredits(
                    $row['user_id'], 
                    $refundAmount, 
                    '[Admin] '.sprintf(__("Hoàn tiền một phần cho đơn hàng #%s (Số lượng còn lại: %d)"), $row['trans_id'], $remains), 
                    'Partial_'.$row['trans_id']
                );
                
                if ($isRefund) {
                    // Tính toán giá vốn mới
                    $newCost = 0;
                    if ($originalQty > 0) {
                        $costPerUnit = $row['cost'] / $originalQty;
                        $newCost = $costPerUnit * $remains;
                    }
                    
                    // Cập nhật trạng thái đơn hàng
                    $isUpdate = $CMSNT->update("orders", [
                        'status'        => $newStatus,
                        'start_count'   => check_string($_POST['start_count']),
                        'remains'       => 0,
                        'quantity'      => $originalQty - $remains,
                        'pay'           => $row['pay'] - $refundAmount,
                        'cost'          => $newCost,
                        'note'          => check_string($_POST['note']),
                        'updated_at'    => gettime()
                    ], " `id` = '$id' ");
                    
                    if ($isUpdate) {
                    
                        $CMSNT->insert("logs", [
                            'user_id'       => $getUser['id'],
                            'ip'            => myip(),
                            'device'        => getUserAgent(),
                            'createdate'    => gettime(),
                            'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s và hoàn tiền một phần (Số lượng còn lại: %d)'), $row['trans_id'], $newStatus, $remains)
                        ]);
                        die('<script type="text/javascript">if(!alert("'.__('Lưu thành công! Đã hoàn tiền một phần cho đơn hàng').'")){window.location.href = window.location.href;}</script>');
                    }
                } else {
                    die('<script type="text/javascript">if(!alert("'.__('Không thể hoàn tiền cho người dùng').'")){window.history.back();}</script>');
                }
            } else {
                // Nếu không cần hoàn tiền, chỉ cập nhật trạng thái
                $isUpdate = $CMSNT->update("orders", [
                    'status'        => $newStatus,
                    'start_count'   => check_string($_POST['start_count']),
                    'remains'       => check_string($_POST['remains']),
                    'note'          => check_string($_POST['note']),
                    'reason'        => check_string($_POST['reason']),
                    'updated_at'    => gettime()
                ], " `id` = '$id' ");
                
                if ($isUpdate) {
                
                    $CMSNT->insert("logs", [
                        'user_id'       => $getUser['id'],
                        'ip'            => myip(),
                        'device'        => getUserAgent(),
                        'createdate'    => gettime(),
                        'action'        => sprintf(__('Cập nhật trạng thái đơn hàng #%s thành %s (Số lượng còn lại: %d)'), $row['trans_id'], $newStatus, check_string($_POST['remains']))
                    ]);
                    die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.location.href = window.location.href;}</script>');
                }
            }
        }else if($newStatus == 'Completed'){
            $isUpdate = $CMSNT->update("orders", [
                'status'        => $newStatus,
                'remains'       => 0,
                'note'          => check_string($_POST['note']),
                'reason'        => check_string($_POST['reason']),
                'updated_at'    => gettime()
            ], " `id` = '$id' ");
            if ($isUpdate) {
                $CMSNT->insert("logs", [
                    'user_id'       => $getUser['id'],
                    'ip'            => myip(),
                    'device'        => getUserAgent(),
                    'createdate'    => gettime(),
                    'action'        => sprintf(__('Cập nhật đơn hàng #%s thành trạng thái %s'), $row['trans_id'], $newStatus)
                ]);
                die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.location.href = window.location.href;}</script>');
            }
        }
        // Cập nhật thông thường nếu không phải trạng thái đặc biệt
        else {
            $isUpdate = $CMSNT->update("orders", [
                'status'        => $newStatus,
                'start_count'   => check_string($_POST['start_count']),
                'remains'       => check_string($_POST['remains']),
                'note'          => check_string($_POST['note']),
                'reason'        => check_string($_POST['reason']),
                'updated_at'    => gettime()
            ], " `id` = '$id' ");
            
            if ($isUpdate) {
            
                $CMSNT->insert("logs", [
                    'user_id'       => $getUser['id'],
                    'ip'            => myip(),
                    'device'        => getUserAgent(),
                    'createdate'    => gettime(),
                    'action'        => sprintf(__('Cập nhật đơn hàng #%s thành trạng thái %s'), $row['trans_id'], $newStatus)
                ]);
                die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.location.href = window.location.href;}</script>');
            }
        }
    }
    
    // Nếu không có return trước đó, có lỗi xảy ra
    die('<script type="text/javascript">if(!alert("'.__('Có lỗi xảy ra, vui lòng thử lại').'")){window.history.back();}</script>');
}
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Page Header -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <div>
                <h1 class="page-title fw-semibold fs-18 mb-2">
                    <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                    <?=__('Chỉnh sửa đơn hàng');?>
                </h1>
                <p class="text-muted mb-0"><?=__('Quản lý và cập nhật thông tin đơn hàng');?> #<?=$row['trans_id'];?></p>
            </div>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item">
                            <a href="<?=base_url_admin('orders');?>" class="text-decoration-none">
                                <i class="fa fa-shopping-cart me-1"></i><?=__('Đơn hàng');?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active fw-semibold" aria-current="page">
                            #<?=$row['trans_id'];?>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>

        <form action="" method="POST" enctype="multipart/form-data">
            <div class="row">
                <!-- Thông tin chính -->
                <div class="col-xl-8">
                    <!-- Thông tin đơn hàng cơ bản -->
                    <div class="card custom-card shadow-sm">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-info-circle me-2 text-primary"></i>
                                <?=__('Thông tin đơn hàng');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-hashtag me-1 text-primary"></i><?=__('Mã đơn hàng');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary-transparent">
                                            <i class="fa-solid fa-receipt"></i>
                                        </span>
                                        <input type="text" value="<?=$row['trans_id'];?>" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-user me-1 text-info"></i><?=__('Thông tin người dùng');?>
                                    </label>
                                    <?php if($row['user_id'] > 0): ?>
                                    <div class="input-group">
                                        <span class="input-group-text bg-info-transparent">
                                            <i class="fa-solid fa-user-circle"></i>
                                        </span>
                                        <input type="text"
                                            value="<?=getRowRealtime('users', $row['user_id'], 'username');?> [ID: <?=$row['user_id'];?>]"
                                            class="form-control bg-light" readonly>
                                        <a href="<?=base_url_admin('user-edit&id='.$row['user_id']);?>"
                                            class="btn btn-outline-info" data-bs-toggle="tooltip" title="<?=__('Xem thông tin user');?>">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                    <?php else: ?>
                                    <div class="input-group">
                                        <span class="input-group-text bg-secondary-transparent">
                                            <i class="fa-solid fa-robot"></i>
                                        </span>
                                        <input type="text" value="<?=__('Hệ thống');?>" class="form-control bg-light" readonly>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-cogs me-1 text-success"></i><?=__('Dịch vụ');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success-transparent">
                                            <i class="fa-solid fa-tools"></i>
                                        </span>
                                        <input type="text"
                                            value="<?=$row['service_name'];?> [ID: <?=$row['service_id'];?>]"
                                            class="form-control bg-light" readonly>
                                        <a href="<?=base_url_admin('service-edit&id='.$row['service_id']);?>"
                                            class="btn btn-outline-success" data-bs-toggle="tooltip" title="<?=__('Xem thông tin dịch vụ');?>">
                                            <i class="fas fa-external-link-alt"></i>
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-link me-1 text-warning"></i><?=__('Liên kết');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning-transparent">
                                            <i class="fa-solid fa-globe"></i>
                                        </span>
                                        <input id="link-input" type="text" value="<?=$row['link'];?>" class="form-control bg-light" readonly>
                                        <button type="button" class="btn btn-outline-warning" id="copy-link-btn" 
                                                data-bs-toggle="tooltip" title="<?=__('Sao chép liên kết');?>">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </div>
                                </div>
                                
                                <?php if(isset($row['comment']) && $row['comment'] != ''): ?>
                                <div class="col-md-12 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-comment me-1 text-secondary"></i><?=__('Bình luận');?>
                                    </label>
                                    <textarea class="form-control bg-light" rows="3" readonly><?=$row['comment'];?></textarea>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin số lượng -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-calculator me-2 text-info"></i>
                                <?=__('Thông tin số lượng');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-shopping-bag me-1 text-primary"></i><?=__('Số lượng đặt');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-primary-transparent">QTY</span>
                                        <input type="number" value="<?=$row['quantity'];?>" class="form-control bg-light" readonly>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-hourglass-half me-1 text-warning"></i><?=__('Số lượng còn lại');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning-transparent">REMAIN</span>
                                        <input name="remains" type="number" value="<?=$row['remains'];?>"
                                               class="form-control" placeholder="0"
                                               <?=($row['status'] == 'Canceled' || $row['status'] == 'Partial') ? 'readonly' : '';?>>
                                    </div>
                                </div>
                                
                                <div class="col-md-4 mb-4">
                                    <label class="form-label fw-semibold">
                                        <i class="fa-solid fa-play me-1 text-success"></i><?=__('Số đếm ban đầu');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success-transparent">START</span>
                                        <input name="start_count" type="number" value="<?=$row['start_count'];?>"
                                               class="form-control" placeholder="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Thông tin thanh toán -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-dollar-sign me-2 text-success"></i>
                                <?=__('Thông tin thanh toán');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-success">
                                        <i class="fa-solid fa-money-bill me-1"></i><?=__('Số tiền thanh toán');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-success-transparent">
                                            <i class="fa-solid fa-credit-card"></i>
                                        </span>
                                        <input type="text" value="<?=format_currency($row['pay']);?>"
                                               class="form-control bg-light text-end fw-bold" readonly>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <label class="form-label fw-semibold text-warning">
                                        <i class="fa-solid fa-coins me-1"></i><?=__('Giá vốn');?>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-warning-transparent">
                                            <i class="fa-solid fa-calculator"></i>
                                        </span>
                                        <input type="text" value="<?=format_currency($row['cost']);?>"
                                               class="form-control bg-light text-end fw-bold" readonly>
                                        <span class="input-group-text">VNĐ</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Ghi chú -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-sticky-note me-2 text-secondary"></i>
                                <?=__('Ghi chú và lý do');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-comment-dots me-1"></i><?=__('Ghi chú của thành viên');?>
                                </label>
                                <textarea name="note" class="form-control" rows="3" 
                                          placeholder="<?=__('Nhập ghi chú...');?>"><?=$row['note'];?></textarea>
                            </div>
                            
                            <div class="mb-0" id="reason-section" style="display: <?=$row['status'] == 'Canceled' ? 'block' : 'none';?>;">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-exclamation-triangle me-1 text-danger"></i><?=__('Lý do hoàn tiền');?>
                                </label>
                                <textarea name="reason" class="form-control" rows="3"
                                          placeholder="<?=__('Nhập lý do hoàn tiền...');?>"><?=$row['reason'];?></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-xl-4">
                    <!-- Trạng thái đơn hàng -->
                    <div class="card custom-card shadow-sm">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-traffic-light me-2 text-warning"></i>
                                <?=__('Trạng thái đơn hàng');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-flag me-1"></i><?=__('Trạng thái hiện tại');?>
                                    <span class="text-danger">*</span>
                                </label>
                                <select class="form-control form-select" name="status" id="status"
                                        <?=($row['status'] == 'Canceled' || $row['status'] == 'Partial') ? 'disabled' : '';?>>
                                    <?php foreach($config_status_order as $status => $name): ?>
                                    <option value="<?=$status;?>" <?=$row['status'] == $status ? 'selected' : '';?>>
                                        <?=$name;?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                                
                                <div id="refund-alert-canceled" class="alert alert-info mt-3" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-info-circle me-2 text-info"></i>
                                        <small><?=__('Khi chuyển trạng thái sang Hủy, hệ thống sẽ tự động hoàn lại toàn bộ tiền cho người dùng.');?></small>
                                    </div>
                                </div>
                                
                                <div id="refund-alert-partial" class="alert alert-warning mt-3" style="display: none;">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-exclamation-triangle me-2 text-warning"></i>
                                        <small><?=__('Khi chuyển trạng thái sang Hoàn tiền một phần, hệ thống sẽ tự động hoàn lại tiền dựa trên số lượng còn lại. Mỗi đơn vị số lượng còn lại sẽ hoàn cho User').' '.format_currency($row['pay']/$row['quantity']);?></small>
                                    </div>
                                </div>
                                
                                <?php if($row['status'] == 'Canceled' || $row['status'] == 'Partial'): ?>
                                <div class="alert alert-danger mt-3">
                                    <div class="d-flex align-items-center">
                                        <i class="fas fa-lock me-2 text-danger"></i>
                                        <small><?=__('Đơn hàng đã ở trạng thái Hủy hoặc Hoàn tiền một phần, không thể thay đổi trạng thái.');?></small>
                                    </div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>

                    <?php if($row['supplier_id'] > 0): ?>
                    <!-- Thông tin API -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-plug me-2 text-info"></i>
                                <?=__('Thông tin API');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-server me-1"></i><?=__('API Supplier');?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-info-transparent">
                                        <i class="fa-solid fa-cloud"></i>
                                    </span>
                                    <input type="text"
                                        value="<?=getRowRealtime('suppliers', $row['supplier_id'], 'domain');?> [ID: <?=$row['supplier_id'];?>]"
                                        class="form-control bg-light" readonly>
                                    <a href="<?=base_url_admin('supplier-edit&id='.$row['supplier_id']);?>"
                                        class="btn btn-outline-info" data-bs-toggle="tooltip" title="<?=__('Xem thông tin supplier');?>">
                                        <i class="fas fa-external-link-alt"></i>
                                    </a>
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label fw-semibold">
                                    <i class="fa-solid fa-key me-1"></i><?=__('Mã đơn hàng API');?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-secondary-transparent">
                                        <i class="fa-solid fa-hashtag"></i>
                                    </span>
                                    <input type="text" value="<?=$row['order_id'];?>" class="form-control bg-light" readonly>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Thông tin thời gian -->
                    <div class="card custom-card shadow-sm mt-4">
                        <div class="card-header bg-light">
                            <div class="card-title text-uppercase">
                                <i class="fa-solid fa-clock me-2 text-secondary"></i>
                                <?=__('Thông tin thời gian');?>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="fa-solid fa-calendar-plus me-1"></i><?=__('Thời gian tạo');?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-clock"></i>
                                    </span>
                                    <input type="text" value="<?=$row['created_at'];?>" 
                                           class="form-control bg-light" readonly>
                                </div>
                            </div>
                            
                            <div class="mb-0">
                                <label class="form-label fw-semibold text-muted">
                                    <i class="fa-solid fa-calendar-check me-1"></i><?=__('Cập nhật cuối');?>
                                </label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light">
                                        <i class="fa-solid fa-sync"></i>
                                    </span>
                                    <input type="text" value="<?=$row['updated_at'];?>" 
                                           class="form-control bg-light" readonly>
                                </div>
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
                            <div class="alert alert-light border">
                                <h6 class="alert-heading mb-2">
                                    <i class="fa-solid fa-lightbulb me-1 text-warning"></i>
                                    <?=__('Lưu ý quan trọng');?>
                                </h6>
                                <hr class="my-2">
                                <small class="mb-0 text-muted">
                                    • <?=__('Cập nhật trạng thái "Hủy" sẽ hoàn tiền tự động')?>
                                    <br>• <?=__('Trạng thái "Partial" sẽ hoàn tiền theo số lượng còn lại')?>
                                    <br>• <?=__('Đơn hàng đã hủy/partial không thể thay đổi trạng thái')?>
                                    <br>• <?=__('Số đếm ban đầu và còn lại có thể điều chỉnh')?>
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
                            <a href="<?=base_url_admin('orders');?>" 
                               class="btn btn-light btn-wave me-2">
                                <i class="fa fa-arrow-left me-1"></i>
                                <?=__('Quay lại danh sách');?>
                            </a>
                            <button type="submit" name="SaveOrder" 
                                    class="btn btn-primary btn-wave">
                                <i class="fa fa-save me-1"></i>
                                <?=__('Lưu thay đổi');?>
                            </button>
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

        // Xử lý nút sao chép liên kết
        $('#copy-link-btn').on('click', function() {
            var linkInput = document.getElementById('link-input');
            linkInput.select();
            linkInput.setSelectionRange(0, 99999); // Cho mobile
            
            // Sao chép vào clipboard
            navigator.clipboard.writeText(linkInput.value).then(function() {
                // Hiển thị thông báo
                showMessage('<?=__("Đã sao chép liên kết vào bộ nhớ tạm");?>', 'success');
                
                // Thay đổi icon tạm thời
                var btn = document.getElementById('copy-link-btn');
                var originalHTML = btn.innerHTML;
                btn.innerHTML = '<i class="fas fa-check text-success"></i>';
                
                setTimeout(function() {
                    btn.innerHTML = originalHTML;
                }, 1500);
            }).catch(function() {
                // Fallback cho trình duyệt cũ
                document.execCommand('copy');
                showMessage('<?=__("Đã sao chép liên kết");?>', 'success');
            });
        });
        
        // Hiển thị thông báo hoàn tiền khi chọn trạng thái
        checkRefundStatus();
        
        // Lắng nghe sự kiện thay đổi trạng thái
        $('#status').on('change', function() {
            checkRefundStatus();
        });
        
        // Hàm kiểm tra trạng thái và hiển thị thông báo tương ứng
        function checkRefundStatus() {
            var selectedStatus = $('#status').val();
            
            // Ẩn tất cả thông báo trước
            $('#refund-alert-canceled, #refund-alert-partial').hide();
            
            // Ẩn/hiện textarea lý do hủy
            if (selectedStatus === 'Canceled') {
                $('#refund-alert-canceled').fadeIn();
                $('#reason-section').fadeIn(100);
            } else {
                $('#reason-section').fadeOut(100);
            }
            
            // Hiển thị thông báo dựa trên trạng thái được chọn
            if (selectedStatus === 'Partial') {
                $('#refund-alert-partial').fadeIn();
                
                // Hiển thị thêm gợi ý về việc nhập số lượng còn lại
                if (!$('#remains-instruction').length) {
                    $('input[name="remains"]').after(
                        '<div id="remains-instruction" class="form-text text-info mt-1">' +
                        '<i class="fa-solid fa-info-circle me-1"></i>' +
                        '<?=__('Vui lòng nhập số lượng còn lại để hệ thống tính toán số tiền hoàn lại.');?>' +
                        '</div>'
                    );
                }
            } else {
                // Xóa gợi ý về số lượng còn lại nếu không ở trạng thái Partial
                $('#remains-instruction').remove();
            }
        }
    });
    
    // Khởi tạo tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (tooltipTriggerEl) {
        new bootstrap.Tooltip(tooltipTriggerEl);
    });
</script>