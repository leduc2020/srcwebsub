<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => __('Nạp tiền').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<style>
.countdown-container {
    background-color: rgba(245, 159, 0, 0.1);
    border-radius: 6px;
    padding: 6px 15px;
    box-shadow: none;
    margin: 0 auto;
}

.countdown-item {
    text-align: center;
    min-width: 45px;
}

.countdown-value {
    font-size: 22px;
    font-weight: 700;
    color: #f59f00;
    line-height: 1;
    margin-bottom: 2px;
}

.countdown-label {
    font-size: 10px;
    color: #f59f00;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.countdown-separator {
    font-size: 22px;
    font-weight: 700;
    color: #f59f00;
    margin: 0 4px;
    line-height: 1;
    display: flex;
    align-items: center;
}

.create-invoice-btn {
    transition: all 0.3s ease;
    overflow: hidden;
    position: relative;
}
.create-invoice-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}
.create-invoice-btn .btn-text, 
.create-invoice-btn .btn-spinner {
    transition: opacity 0.3s ease;
}
input:focus, select:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    border-color: #86b7fe !important;
}
.avatar-sm {
    height: 2.5rem;
    width: 2.5rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
.bg-light-subtle {
    background-color: #f8f9fa;
}
</style>
';
$body['footer'] = '

';
require_once(__DIR__.'/../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

$trans_id = '';
if(isset($_GET['trans_id'])) {
    $trans_id = $_GET['trans_id'];
    $invoice = $CMSNT->get_row("SELECT * FROM `payment_bank_invoice` WHERE `trans_id` = '$trans_id' AND `user_id` = '".$getUser['id']."' ");
}


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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_bank_invoice` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_bank_invoice` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=recharge-bank&limit=$limit&shortByDate=$shortByDate&time=$time&transid=$transid&status=$status&trans_id=$trans_id&"), $from, $totalDatatable, $limit);



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
                        <h4 class="mb-sm-0"><?=__('Nạp tiền bằng Ngân Hàng');?></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Nạp tiền');?></a></li>
                                <?php if(isset($_GET['trans_id']) && $invoice): ?>
                                <li class="breadcrumb-item"><a
                                        href="<?=base_url('client/recharge-bank');?>"><?=__('Ngân hàng');?></a></li>
                                <li class="breadcrumb-item active"><?=$invoice['trans_id'];?></li>
                                <?php else: ?>
                                <li class="breadcrumb-item active"><?=__('Ngân hàng');?></li>
                                <?php endif; ?>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <?php if(isset($_GET['trans_id']) && $invoice): ?>
                <?php $bank = $CMSNT->get_row("SELECT * FROM `banks` WHERE `id` = '".$invoice['bank_id']."' "); ?>
                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-body">
                            <div class="payment-info-container">
                                <!-- Thông báo hướng dẫn -->
                                <div class="alert alert-info d-flex align-items-center mb-4">
                                    <i class="ri-information-line me-2 fs-4"></i>
                                    <div>
                                        <strong><?=__('Hướng dẫn thanh toán');?>:</strong>
                                        <?=__('Vui lòng chuyển khoản đúng số tiền và nội dung để được cộng tiền tự động. Nếu có vấn đề, vui lòng liên hệ hỗ trợ.');?>
                                    </div>
                                </div>


                                <div class="row">
                                    <!-- Thông tin chuyển khoản -->
                                    <div class="col-lg-7">
                                        <div class="card border-0 shadow-sm mb-4">
                                            <div class="card-header bg-transparent border-bottom-0 pt-4">
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0">
                                                        <img src="<?=base_url('assets/img/icon-qr-code.webp');?>"
                                                            alt="<?=__('Thông tin chuyển khoản');?>"
                                                            class="icon-card border border-primary">
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h5 class="card-title mb-1"><?=__('Thông tin chuyển khoản');?>
                                                        </h5>
                                                        <p class="text-muted mb-0"><?=__('Mã giao dịch');?>: <span
                                                                class="text-muted fw-bold"><?=$invoice['trans_id'];?></span>
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-borderless mb-0">
                                                        <tbody>
                                                            <tr>
                                                                <th scope="row" style="width: 150px;">
                                                                    <?=__('Số tiền');?>:</th>
                                                                <td>
                                                                    <span
                                                                        class="badge bg-success fs-6"><?=format_currency($invoice['amount']);?></span>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><?=__('Ngân hàng');?>:</th>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <span
                                                                            class="fw-medium"><?=$bank['short_name'];?></span>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><?=__('Số tài khoản');?>:</th>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <code
                                                                            class="me-2 fs-6"><?=$bank['accountNumber'];?></code>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-light"
                                                                            onclick="copyToClipboard('<?=$bank['accountNumber'];?>')"
                                                                            title="Sao chép">
                                                                            <i class="ri-file-copy-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><?=__('Chủ tài khoản');?>:</th>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <code
                                                                            class="me-2 fs-6"><?=$bank['accountName'];?></code>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-light"
                                                                            onclick="copyToClipboard('<?=$bank['accountName'];?>')"
                                                                            title="Sao chép">
                                                                            <i class="ri-file-copy-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <tr>
                                                                <th scope="row"><?=__('Nội dung CK');?>:</th>
                                                                <td>
                                                                    <div class="d-flex align-items-center">
                                                                        <code
                                                                            class="me-2 fs-6"><?=$invoice['trans_id'];?></code>
                                                                        <button type="button"
                                                                            class="btn btn-sm btn-light"
                                                                            onclick="copyToClipboard('<?=$invoice['trans_id'];?>')"
                                                                            title="Sao chép">
                                                                            <i class="ri-file-copy-line"></i>
                                                                        </button>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                                <p class="text-muted small mb-0">
                                                    <?=__('Nội dung chuyển khoản chỉ áp dụng cho 1 lần chuyển khoản, nếu bạn cần nạp thêm vui lòng tạo hóa đơn mới bằng cách nhấn vào nút bên dưới.');?>
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Nút tạo hóa đơn mới -->
                                        <div class="d-grid">
                                            <button type="button"
                                                class="btn btn-soft-danger waves-effect waves-light material-shadow-none"
                                                onclick="window.location.href='<?=BASE_URL('client/recharge-bank');?>'">
                                                <i class="ri-add-line me-1"></i> <?=__('Tạo hóa đơn mới');?>
                                            </button>
                                        </div>
                                    </div>

                                    <!-- Mã QR -->
                                    <div class="col-lg-5" <?=$aos['zoom-in'];?>>
                                        <div class="card border-0 shadow-sm h-100">
                                            <div
                                                class="card-body text-center d-flex flex-column justify-content-center">
                                                <h5 class="card-title mb-3"><?=__('Quét mã QR để thanh toán');?></h5>
                                                <div class="qr-container mb-3">
                                                    <?php 
                                                    if(in_array('VietQR', array_column($config_listbank, 'type')) && in_array($bank['short_name'], array_column($config_listbank, 'shortName'))){
                                                        $qr = 'https://api.vietqr.io/'.$bank['short_name'].'/'.$bank['accountNumber'].'/'.$invoice['amount'].'/'.$invoice['trans_id'].'/vietqr_net_2.jpg?accountName='.$bank['accountName'];
                                                    }
                                                    elseif(in_array('PromptPay', array_column($config_listbank, 'type')) && in_array($bank['short_name'], array_column($config_listbank, 'shortName'))){
                                                        $qr = 'https://promptpay.io/'.$bank['accountNumber'].'/'.$invoice['amount'];
                                                    }
                                                    else{
                                                        $qr = base_url($bank['image']);
                                                    }
                                                    ?>
                                                    <img src="<?=$qr;?>" alt="QR Code"
                                                        class="img-fluid no-pointer-events" style="max-width: 220px;"
                                                        id="qrCodeImage">
                                                    <div class="mt-2">
                                                        <button type="button" class="btn btn-sm btn-primary"
                                                            onclick="downloadQRCode()">
                                                            <i
                                                                class="ri-download-2-line me-1"></i><?=__('Tải QR về máy');?>
                                                        </button>
                                                    </div>
                                                </div>

                                                <p class="text-muted small mb-3">
                                                    <?=__('Quét mã QR bằng ứng dụng ngân hàng để thanh toán nhanh chóng');?>
                                                </p>

                                                <!-- Thêm bộ đếm ngược dưới mã QR -->
                                                <div class="countdown-container d-inline-flex">
                                                    <div class="countdown-item">
                                                        <div class="countdown-value" id="countdown-minutes">00</div>
                                                        <div class="countdown-label"><?=__('Phút');?></div>
                                                    </div>
                                                    <div class="countdown-separator">:</div>
                                                    <div class="countdown-item">
                                                        <div class="countdown-value" id="countdown-seconds">00</div>
                                                        <div class="countdown-label"><?=__('Giây');?></div>
                                                    </div>
                                                </div>
                                                <p class="text-muted small mt-2 mb-0">
                                                    <?=__('Thời gian còn lại để thanh toán');?></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Lưu ý -->
                                <div class="alert alert-warning mt-4">
                                    <div class="d-flex">
                                        <i class="ri-alert-line me-2 fs-4"></i>
                                        <div>
                                            <strong><?=__('Lưu ý');?>:</strong>
                                            <?=$CMSNT->site('bank_notice');?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <?php else: ?>
                <?php require_once(__DIR__.'/block-promotion.php');?>
                <div class="col-xl-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary bg-soft py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?=base_url('assets/img/icon-topup.webp');?>"
                                        alt="<?=__('Nạp tiền qua ngân hàng');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Nạp tiền qua ngân hàng');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Thanh toán nhanh chóng và an toàn');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-secondary bg-soft-secondary border-0 mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-information-line me-3 fs-3"></i>
                                    <div>
                                        <h5 class="alert-heading fs-14 mb-1"><?=__('Hướng dẫn nạp tiền');?></h5>
                                        <p class="mb-0 fs-13">
                                            <?=__('Nhập số tiền, chọn ngân hàng và nhấn tạo hóa đơn để bắt đầu quy trình nạp tiền.');?>
                                        </p>
                                    </div>
                                </div>
                            </div>

                            <form id="recharge-form" class="needs-validation" onsubmit="createInvoice(); return false;"
                                novalidate>
                                <div class="mb-4">
                                    <label for="amount" class="form-label fw-medium"><?=__('Số tiền nạp');?> <span
                                            class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light"><i
                                                class="ri-money-cny-circle-line"></i></span>
                                        <input type="number" class="form-control form-control-lg bg-light-subtle"
                                            id="amount" name="amount" placeholder="<?=__('Nhập số tiền cần nạp');?>"
                                            required>
                                        <div class="invalid-feedback"><?=__('Vui lòng nhập số tiền cần nạp');?></div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="form-text text-muted"><i class="ri-information-line me-1"></i>
                                            <?=__('Số tiền tối thiểu:');?> <span
                                                class="text-danger fw-medium"><?=format_currency($CMSNT->site('bank_min'));?></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="bank-select" class="form-label fw-medium"><?=__('Chọn ngân hàng');?>
                                        <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light"><i class="ri-bank-line"></i></span>
                                        <select class="form-select form-select-lg bg-light-subtle" id="bank-select"
                                            name="bank" required>
                                            <?php foreach ($CMSNT->get_list(" SELECT * FROM `banks` WHERE `status` = 1") as $bank):?>
                                            <option value="<?=$bank['id'];?>"><?=$bank['short_name'];?></option>
                                            <?php endforeach;?>
                                        </select>
                                        <div class="invalid-feedback"><?=__('Vui lòng chọn ngân hàng');?></div>
                                    </div>
                                </div>

                                <div class="alert border border-dashed mb-4 conversion-box">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line fs-3 me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 fw-medium"><?=__('Số tiền thực nhận ước tính');?></h5>
                                            <div class="fs-13 text-muted">
                                                ~<span id="received_amount" class="text-danger fw-medium fs-16">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg create-invoice-btn"
                                        id="create-invoice-btn">
                                        <span class="btn-text text-uppercase"><i
                                                class="ri-secure-payment-line me-1"></i> <?=__('Tạo hóa đơn');?></span>
                                        <span class="btn-spinner d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status"
                                                aria-hidden="true"></span>
                                            <?=__('Đang xử lý...');?>
                                        </span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <?=$CMSNT->site('bank_notice');?>
                        </div>
                    </div>
                </div>

                <?php endif; ?>

                <div class="col-xl-12" <?=$aos['fade-up'];?>>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0 text-uppercase"><i
                                    class="ri-history-fill text-primary me-2"></i><?=__('Lịch sử nạp tiền');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="recharge-bank">
                                <input type="hidden" name="trans_id" value="<?=$trans_id;?>">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="transid" value="<?=$transid;?>"
                                                placeholder="<?=__('Mã giao dịch');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <select name="status" class="form-control bg-light border-light">
                                                <option value=""><?=__('-- Trạng thái --');?></option>
                                                <option value="pending" <?=$status == 'pending' ? 'selected' : '';?>>
                                                    <?=__('Chờ xác nhận');?></option>
                                                <option value="completed"
                                                    <?=$status == 'completed' ? 'selected' : '';?>><?=__('Hoàn tất');?>
                                                </option>
                                                <option value="expired" <?=$status == 'expired' ? 'selected' : '';?>>
                                                    <?=__('Hết hạn');?></option>
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
                                            <a href="<?=base_url('?action=recharge-bank');?>"
                                                class="btn btn-light waves-effect waves-light">
                                                <i class="ri-delete-bin-line align-bottom"></i>
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
                                    <table class="table align-middle table-nowrap">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th><?=__('Mã giao dịch');?></th>
                                                <th class="text-center"><?=__('Trạng thái');?></th>
                                                <th><?=__('Ngân hàng');?></th>
                                                <th class="text-center"><?=__('Số tiền cần thanh toán');?></th>
                                                <th class="text-center"><?=__('Số tiền nhận được');?></th>
                                                <th class="text-center"><?=__('Thời gian tạo hóa đơn');?></th>
                                                <th class="text-center"><?=__('Cập nhật');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="7" class="text-center">
                                                    <div class="text-center p-3">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                            trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                            style="width:75px;height:75px">
                                                        </lord-icon>
                                                        <h5 class="mt-2"><?=__('Không tìm thấy kết quả');?></h5>
                                                        <p class="text-muted mb-0">
                                                            <?=__('Không có nhật ký hoạt động nào được tìm thấy');?>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach($listDatatable as $log): ?>
                                            <tr>
                                                <td><a
                                                        href="<?=base_url('payment/'.$log['trans_id']);?>"><?=$log['trans_id'];?></a>
                                                </td>
                                                <td class="text-center"><?=display_invoice($log['status']);?></td>
                                                <td><?=$log['short_name'];?></td>
                                                <td class="text-end"><?=format_currency($log['amount']);?></td>
                                                <td class="text-end"><?=format_currency($log['received']);?></td>
                                                <td class="text-center">
                                                    <?=date('d/m/Y H:i:s', strtotime($log['created_at']));?></td>
                                                <td class="text-center">
                                                    <?=date('d/m/Y H:i:s', strtotime($log['updated_at']));?></td>
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
                                                <span class="text-muted me-3"><?=__('Đã thanh toán');?>:</span>
                                                <span
                                                    class="fw-bold text-danger fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`received`) FROM `payment_bank_invoice` WHERE $where AND `status` = 'completed' ")['SUM(`received`)']);?></span>
                                            </div>
                                            <div>
                                                <span class="text-muted me-3"><?=__('Chưa thanh toán');?>:</span>
                                                <span
                                                    class="fw-bold text-primary fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`received`) FROM `payment_bank_invoice` WHERE $where AND `status` = 'waiting' ")['SUM(`received`)']);?></span>
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

<script>
function copyToClipboard(text) {
    // Tạo một phần tử textarea tạm thời
    const textarea = document.createElement('textarea');
    textarea.value = text;
    textarea.style.position = 'fixed'; // Đặt vị trí cố định để tránh cuộn trang
    textarea.style.opacity = '0'; // Ẩn phần tử
    document.body.appendChild(textarea);

    // Chọn và sao chép nội dung
    textarea.select();
    document.execCommand('copy');

    // Xóa phần tử tạm thời
    document.body.removeChild(textarea);

    showMessage('<?=__('Nội dung đã được sao chép vào clipboard');?>', 'success');
}

function createInvoice() {
    var form = document.getElementById('recharge-form');
    if (!form.checkValidity()) {
        return;
    }

    var amount = $('#amount').val();
    var bank = $('#bank-select').val();
    var $btn = $('#create-invoice-btn');

    // Thay đổi trạng thái nút
    $btn.find('.btn-text').addClass('d-none');
    $btn.find('.btn-spinner').removeClass('d-none');
    $btn.prop('disabled', true);

    $.ajax({
        url: '<?=BASE_URL('ajaxs/client/recharge.php');?>',
        type: 'POST',
        data: {
            action: 'createInvoice',
            token: '<?=$getUser['token'];?>',
            amount: amount,
            bank_id: bank
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '<?=__('Thành công');?>',
                    text: response.msg,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.href = response.payment_url;
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi');?>',
                    text: response.msg
                });

                // Khôi phục trạng thái nút
                $btn.find('.btn-spinner').addClass('d-none');
                $btn.find('.btn-text').removeClass('d-none');
                $btn.prop('disabled', false);
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Đã xảy ra lỗi, vui lòng thử lại');?>'
            });

            // Khôi phục trạng thái nút
            $btn.find('.btn-spinner').addClass('d-none');
            $btn.find('.btn-text').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });
}
</script>

<script>
// Thêm hàm đếm ngược
function startCountdown(createdTime) {
    const minutesElement = document.getElementById('countdown-minutes');
    const secondsElement = document.getElementById('countdown-seconds');
    const expiryTime = createdTime +
        <?=$CMSNT->site('bank_expired_invoice');?>; // Thời gian hết hạn = thời gian tạo + 1 giờ
    let hasShownExpiredAlert = false; // Biến để kiểm tra đã hiển thị thông báo chưa
    let intervalId; // Biến để lưu ID của interval

    function updateCountdown() {
        const now = Math.floor(Date.now() / 1000);
        const timeLeft = expiryTime - now;

        if (timeLeft <= 0) {
            minutesElement.innerHTML = '00';
            secondsElement.innerHTML = '00';

            // Chỉ hiển thị thông báo một lần
            if (!hasShownExpiredAlert) {
                hasShownExpiredAlert = true;
                clearInterval(intervalId); // Dừng interval
            }
            return;
        }

        const minutes = Math.floor(timeLeft / 60);
        const seconds = timeLeft % 60;

        minutesElement.innerHTML = minutes.toString().padStart(2, '0');
        secondsElement.innerHTML = seconds.toString().padStart(2, '0');
    }

    // Cập nhật ngay lập tức
    updateCountdown();
    // Cập nhật mỗi giây
    intervalId = setInterval(updateCountdown, 1000);
}

// Khởi tạo đếm ngược khi trang được tải
document.addEventListener('DOMContentLoaded', function() {
    <?php if(isset($invoice)): ?>
    const createdTime = <?=strtotime($invoice['created_at']);?>; // Lấy thời gian tạo từ PHP
    startCountdown(createdTime);
    <?php endif; ?>
});
</script>

<script>
// Hàm kiểm tra trạng thái hóa đơn
function checkInvoiceStatus() {
    <?php if(isset($invoice)): ?>
    $.ajax({
        url: '<?=BASE_URL('ajaxs/client/recharge.php');?>',
        type: 'POST',
        data: {
            action: 'getInvoice',
            token: '<?=$getUser['token'];?>',
            trans_id: '<?=$invoice['trans_id'];?>'
        },
        dataType: 'json',
        success: function(response) {
            if (response.status == 'success') {
                // Cập nhật trạng thái hóa đơn nếu cần
                if (response.invoice.status == 'completed') {
                    // Nếu hóa đơn đã được thanh toán
                    $('.payment-info-container').html(`
                            <div class="completed-invoice-container text-center py-5">
                                <div class="success-animation">
                                    <div class="success-checkmark">
                                        <div class="check-icon">
                                            <span class="icon-line line-tip"></span>
                                            <span class="icon-line line-long"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-2 mb-4">
                                    <div class="badge bg-success-subtle text-success fs-6 px-3 py-2">
                                        <i class="ri-checkbox-circle-fill me-1"></i> <?=__('Giao dịch thành công');?>
                                    </div>
                                </div>
                                <h3 class="text-success fw-bold mb-3"><?=__('Tài khoản của bạn đã được cộng');?> ` +
                        response.invoice.received + `</h3>
                                <p class="text-muted fs-5 mb-4"><?=__('Cảm ơn bạn đã sử dụng dịch vụ của chúng tôi');?></p>
                                
                                 
                                
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center mt-3">
                                    <button type="button" class="btn btn-primary btn-animation waves-effect waves-light px-4 py-2" onclick="window.location.href='<?=BASE_URL('client/recharge-bank');?>'">
                                        <i class="ri-add-line me-1"></i> <?=__('Tạo hóa đơn mới');?>
                                    </button>
                                    <button type="button" class="btn btn-dark btn-animation waves-effect waves-light px-4 py-2" onclick="window.location.href='<?=BASE_URL('client/home');?>'">
                                        <i class="ri-shopping-cart-2-line me-1"></i> <?=__('Mua hàng ngay');?>
                                    </button>
                                </div>
                            </div>
                            
                            <style>
                                .success-animation {
                                    margin: 0 auto;
                                    width: 100px;
                                    height: 100px;
                                    position: relative;
                                }
                                .success-checkmark {
                                    width: 80px;
                                    height: 80px;
                                    margin: 0 auto;
                                    position: relative;
                                }
                                .success-checkmark .check-icon {
                                    width: 80px;
                                    height: 80px;
                                    position: relative;
                                    border-radius: 50%;
                                    box-sizing: content-box;
                                    border: 4px solid #4CAF50;
                                }
                                .success-checkmark .check-icon::before {
                                    top: 3px;
                                    left: -2px;
                                    width: 30px;
                                    transform-origin: 100% 50%;
                                    border-radius: 100px 0 0 100px;
                                }
                                .success-checkmark .check-icon::after {
                                    top: 0;
                                    left: 30px;
                                    width: 60px;
                                    transform-origin: 0 50%;
                                    border-radius: 0 100px 100px 0;
                                    animation: rotate-circle 4.25s ease-in;
                                }
                                .success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
                                    content: '';
                                    height: 100px;
                                    position: absolute;
                                    background: #FFFFFF;
                                    transform: rotate(-45deg);
                                }
                                .success-checkmark .check-icon .icon-line {
                                    height: 5px;
                                    background-color: #4CAF50;
                                    display: block;
                                    border-radius: 2px;
                                    position: absolute;
                                    z-index: 10;
                                }
                                .success-checkmark .check-icon .icon-line.line-tip {
                                    top: 46px;
                                    left: 14px;
                                    width: 25px;
                                    transform: rotate(45deg);
                                    animation: icon-line-tip 0.75s;
                                }
                                .success-checkmark .check-icon .icon-line.line-long {
                                    top: 38px;
                                    right: 8px;
                                    width: 47px;
                                    transform: rotate(-45deg);
                                    animation: icon-line-long 0.75s;
                                }
                                @keyframes icon-line-tip {
                                    0% {
                                        width: 0;
                                        left: 1px;
                                        top: 19px;
                                    }
                                    54% {
                                        width: 0;
                                        left: 1px;
                                        top: 19px;
                                    }
                                    70% {
                                        width: 50px;
                                        left: -8px;
                                        top: 37px;
                                    }
                                    84% {
                                        width: 17px;
                                        left: 21px;
                                        top: 48px;
                                    }
                                    100% {
                                        width: 25px;
                                        left: 14px;
                                        top: 46px;
                                    }
                                }
                                @keyframes icon-line-long {
                                    0% {
                                        width: 0;
                                        right: 46px;
                                        top: 54px;
                                    }
                                    65% {
                                        width: 0;
                                        right: 46px;
                                        top: 54px;
                                    }
                                    84% {
                                        width: 55px;
                                        right: 0px;
                                        top: 35px;
                                    }
                                    100% {
                                        width: 47px;
                                        right: 8px;
                                        top: 38px;
                                    }
                                }
                            </style>
                        `);
                    // Dừng interval kiểm tra
                    clearInterval(checkInvoiceInterval);
                } else if (response.invoice.status == 'expired') {
                    // Nếu hóa đơn đã hết hạn
                    // Ẩn QR và thông tin chuyển khoản
                    $('.payment-info-container').html(`
                            <div class="expired-invoice-container text-center py-5">
                                <div class="expired-icon mb-4">
                                    <div class="avatar-lg rounded-circle bg-danger bg-opacity-10 d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="ri-time-line text-danger" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                                <h4 class="text-danger mb-3"><?=__('Hóa đơn đã hết hạn');?></h4>
                                <p class="text-muted mb-4"><?=__('Hóa đơn này đã hết hạn, vui lòng tạo hóa đơn mới để tiếp tục thanh toán.');?></p>
                               
                                <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
                                    <button type="button" class="btn btn-soft-danger waves-effect waves-light material-shadow-none px-4" onclick="window.location.href='<?=BASE_URL('client/recharge-bank');?>'">
                                        <i class="ri-add-line me-1"></i> <?=__('Tạo hóa đơn mới');?>
                                    </button>
                                </div>
                            </div>
                        `);

                    // Dừng interval kiểm tra
                    clearInterval(checkInvoiceInterval);
                }
            }
        },
        error: function() {
            console.log('Lỗi khi kiểm tra trạng thái hóa đơn');
        }
    });
    <?php endif; ?>
}

// Biến để lưu ID của interval
let checkInvoiceInterval;

// Thiết lập interval để gọi hàm kiểm tra mỗi 10 giây
$(document).ready(function() {
    <?php if(isset($invoice)): ?>
    // Gọi lần đầu tiên
    checkInvoiceStatus();

    // Thiết lập interval để gọi mỗi 10 giây
    checkInvoiceInterval = setInterval(checkInvoiceStatus, 10000);
    <?php endif; ?>
});
</script>

<?php if(isset($invoice)): ?>
<script>
function downloadQRCode() {
    // Lấy URL của hình ảnh QR
    const qrImageUrl = '<?=$qr;?>';
    const fileName = 'QR_<?=$invoice['trans_id'];?>.jpg';
    // Tạo một phần tử a tạm thời để tải xuống
    fetch(qrImageUrl)
        .then(response => response.blob())
        .then(blob => {
            const blobUrl = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = blobUrl;
            a.download = fileName;
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(blobUrl);
            document.body.removeChild(a);
            // Hiển thị thông báo thành công
            showMessage('<?=__('Ảnh QR đã được tải về máy của bạn');?>', 'success');
        })
        .catch(error => {
            showMessage('<?=__('Không thể tải xuống ảnh QR. Vui lòng thử lại sau.');?>', 'error');
        });
}
</script>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
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
</script>

<script>
// Xử lý tính toán số tiền thực nhận
$(document).ready(function() {
    $("#amount").on("input", function() {
        try {
            var amount = parseFloat($(this).val()) || 0;

            if (amount > 0) {
                // Hiển thị trạng thái đang tải
                $("#received_amount").html(
                    '<small><i class="spinner-border spinner-border-sm"></i> Đang tính...</small>');

                // Gọi AJAX để tính toán số tiền thực nhận
                $.ajax({
                    url: "<?=base_url('ajaxs/client/recharge.php');?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'getReceivedBank',
                        amount: amount
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $(".conversion-box").addClass("border-primary").removeClass(
                                "border-dashed");
                            $("#received_amount").html(response.received);
                        } else {
                            $(".conversion-box").removeClass("border-primary").addClass(
                                "border-dashed");
                            $("#received_amount").html("0");
                        }
                    },
                    error: function() {
                        $(".conversion-box").removeClass("border-primary").addClass(
                            "border-dashed");
                        $("#received_amount").html("0");
                    }
                });
            } else {
                $(".conversion-box").removeClass("border-primary").addClass("border-dashed");
                $("#received_amount").html("0");
            }
        } catch (error) {
            $("#received_amount").html("0");
        }
    });
});
</script>