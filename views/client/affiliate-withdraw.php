<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Affiliate Withdraw').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
<style>
.wallet-card {
    border: 2px solid #dee2e6;
    border-radius: 15px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}
.wallet-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}
.error-message {
    color: #dc3545;
    font-size: 80%;
    margin-top: 0.25rem;
    display: block;
}
.form-control.is-invalid, 
.was-validated .form-control:invalid,
.form-select.is-invalid, 
.was-validated .form-select:invalid {
    border-color: #dc3545 !important;
}
.input-group .form-control.is-invalid {
    z-index: 2;
}
.invalid-feedback {
    display: none;
    width: 100%;
    margin-top: 0.25rem;
    font-size: 80%;
    color: #dc3545;
}
.is-invalid ~ .invalid-feedback,
.was-validated :invalid ~ .invalid-feedback,
.invalid-feedback.show {
    display: block !important;
}

/* Widget CSS */
.card-animate {
    transition: all 0.4s;
}
.card-animate:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 10px rgba(30, 32, 37, 0.12);
}
.avatar-sm {
    height: 3rem;
    width: 3rem;
}
.avatar-title {
    align-items: center;
    display: flex;
    font-weight: 500;
    height: 100%;
    justify-content: center;
    width: 100%;
}
.bg-primary-subtle {
    background-color: rgba(85, 110, 230, 0.15) !important;
}
.bg-success-subtle {
    background-color: rgba(10, 179, 156, 0.15) !important;
}
.bg-info-subtle {
    background-color: rgba(41, 156, 219, 0.15) !important;
}
.bg-warning-subtle {
    background-color: rgba(241, 180, 76, 0.15) !important;
}
.fs-2 {
    font-size: 1.75rem !important;
}
.fs-4 {
    font-size: 1.25rem !important;
}
.text-uppercase {
    text-transform: uppercase !important;
}
.fw-medium {
    font-weight: 500 !important;
}
.rounded-2 {
    border-radius: 0.25rem !important;
}
</style>
';
$body['footer'] = '';

if($CMSNT->site('affiliate_status') != 1){
    redirect(base_url());
}


require_once(__DIR__.'/../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

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
$where = " `user_id` = '".$getUser['id']."'  ";
$shortByDate = '';
$transid = '';
$time = '';
$status = '';

if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "'.$status.'" ';
}
if(!empty($_GET['transid'])){
    $transid = check_string($_GET['transid']);
    $where .= ' AND `trans_id` = "'.$transid.'" ';
}
if(!empty($_GET['time'])){
    $time = check_string($_GET['time']);
    $create_gettime_1 = str_replace('-', '/', $time);
    $create_gettime_1 = explode(' to ', $create_gettime_1);
    if($create_gettime_1[0] != $create_gettime_1[1]){
        $create_gettime_1 = [$create_gettime_1[0].' 00:00:00', $create_gettime_1[1].' 23:59:59'];
        $where .= " AND `create_gettime` >= '".$create_gettime_1[0]."' AND `create_gettime` <= '".$create_gettime_1[1]."' ";
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
        $where .= " AND `create_gettime` LIKE '%".$currentDate."%' ";
    }
    if($shortByDate == 2){
        $where .= " AND YEAR(create_gettime) = $currentYear AND WEEK(create_gettime, 1) = $currentWeek ";
    }
    if($shortByDate == 3){
        $where .= " AND MONTH(create_gettime) = '$currentMonth' AND YEAR(create_gettime) = '$currentYear' ";
    }
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `aff_withdraw` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `aff_withdraw` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=affiliate-withdraw&limit=$limit&shortByDate=$shortByDate&time=$time&transid=$transid&"), $from, $totalDatatable, $limit);
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Affiliate Withdraw');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Trang Chủ');?></a></li>
                                <li class="breadcrumb-item"><a href="<?=base_url('client/affiliates');?>"><?=__('Affiliate Program');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Rút Tiền');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-5">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary bg-soft py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?=base_url('assets/img/icon-withdraw.svg');?>"
                                        alt="<?=__('Rút tiền Hoa Hồng');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Rút tiền Hoa Hồng');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Rút tiền nhanh chóng và an toàn');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">

                            <form id="withdrawForm" class="needs-validation" novalidate>
                                <input type="hidden" class="form-control" id="token" value="<?=$getUser['token'];?>">
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-medium"><?=__('Số tiền muốn rút');?> <span class="text-danger">*</span></label>
                                    <div class="input-group mb-1">
                                        <span class="input-group-text"><i class="ri-money-dollar-circle-line"></i></span>
                                        <input type="number" id="amount" value="" onchange="totalAmount()" onkeyup="totalAmount()"
                                            placeholder="<?=__('Nhập số tiền bạn muốn rút');?>" class="form-control" required>
                                        <div class="input-group-text"><?=currencyDefault();?></div>
                                    </div>
                                    <div id="amountFeedback" class="invalid-feedback"><?=__('Vui lòng nhập số tiền cần rút');?></div>
                                    <p class="mb-0"><i><?=__('Tối thiểu');?> <?=format_currency($CMSNT->site('affiliate_min'));?> - <?=__('Tối đa');?> <?=format_currency($getUser['ref_price']);?>.</i></p>
                                </div>
                                
                                <div class="form-group mb-3">
                                    <label class="form-label fw-medium"><?=__('Ngân hàng');?> <span class="text-danger">*</span></label>
                                    <select class="form-control" id="bank" required>
                                        <option value=""><?=__('-- Chọn ngân hàng --');?></option>
                                        <?php $listbank = explode(PHP_EOL, $CMSNT->site('affiliate_banks')); ?>
                                        <?php foreach($listbank as $item): ?>
                                        <option value="<?=$item;?>"><?=$item;?></option>
                                        <?php endforeach ?>
                                    </select>
                                    <div id="bankFeedback" class="invalid-feedback"><?=__('Vui lòng chọn ngân hàng cần rút');?></div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-medium"><?=__('Số tài khoản');?> <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-bank-card-line"></i></span>
                                                <input type="text" class="form-control" id="stk" placeholder="<?=__('Nhập số tài khoản');?>" required>
                                            </div>
                                            <div id="stkFeedback" class="invalid-feedback"><?=__('Vui lòng nhập số tài khoản cần rút');?></div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group mb-3">
                                            <label class="form-label fw-medium"><?=__('Tên chủ tài khoản');?> <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <span class="input-group-text"><i class="ri-user-line"></i></span>
                                                <input type="text" class="form-control" id="name" placeholder="<?=__('Nhập tên chủ tài khoản');?>" required>
                                            </div>
                                            <div id="nameFeedback" class="invalid-feedback"><?=__('Vui lòng nhập tên chủ tài khoản');?></div>
                                        </div>
                                    </div>
                                </div>
                                
                            
                                
                                <div class="mt-4">
                                    <button type="submit" id="btnWithdraw" class="btn btn-primary btn-lg w-100">
                                        <i class="ri-arrow-right-circle-line me-1"></i> <?=__('Xác Nhận Rút Tiền');?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-7" <?=$aos['fade-up'];?>>
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4">
                                <h3 class="mb-2"><?=format_currency($getUser['ref_price']);?></h3>
                                <p class="text-muted fs-14"><?=__('Số tiền hoa hồng khả dụng');?></p>
                            </div>

                            <div class="affiliate-stats mb-4">
                                <div class="row g-3">
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-3 text-center">
                                            <h3 class="fs-5 fw-semibold text-success mb-1"><?=format_currency($getUser['ref_total_price']);?></h3>
                                            <p class="fs-12 text-muted mb-0"><?=__('Tổng hoa hồng đã nhận');?></p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-3 text-center">
                                            <h3 class="fs-5 fw-semibold text-info mb-1"><?=$getUser['ref_click']?></h3>
                                            <p class="fs-12 text-muted mb-0"><?=__('Lượt nhấp liên kết');?></p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-3 text-center">
                                            <h3 class="fs-5 fw-semibold text-danger mb-1">
                                                <?php
                                                $ck = $CMSNT->site('affiliate_ck');
                                                if(getRowRealtime('users', $getUser['id'], 'ref_ck') != 0){
                                                    $ck = getRowRealtime('users', $getUser['id'], 'ref_ck');
                                                }
                                                echo $ck;
                                                ?>%
                                            </h3>
                                            <p class="fs-12 text-muted mb-0"><?=__('Tỷ lệ hoa hồng');?></p>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3">
                                        <div class="border rounded p-3 text-center">
                                            <h3 class="fs-5 fw-semibold text-warning mb-1">
                                                <?=format_cash($CMSNT->get_row(" SELECT COUNT(id) FROM `users` WHERE `ref_id` = '".$getUser['id']."' ")['COUNT(id)']);?>
                                            </h3>
                                            <p class="fs-12 text-muted mb-0"><?=__('Thành viên đã giới thiệu');?></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mt-4">
                                <div class="card bg-light">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm flex-shrink-0">
                                                <span class="avatar-title bg-primary-subtle text-primary rounded-2 fs-2">
                                                    <i class="ri-link-m"></i>
                                                </span>
                                            </div>
                                            <div class="flex-grow-1 ms-3">
                                                <p class="fw-medium mb-2"><?=__('Liên kết giới thiệu của bạn');?></p>
                                                <div class="input-group">
                                                    <input type="text" class="form-control" id="refLink" value="<?=base_url('?ref='.$getUser['id']);?>" readonly>
                                                    <button class="btn btn-primary copy" type="button" data-clipboard-target="#refLink" onclick="copy()"><i class="ri-file-copy-line"></i></button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12" <?=$aos['fade-up'];?>>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ri-history-line text-primary me-2"></i><?=__('Lịch sử rút tiền');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="" method="GET">
                                <input type="hidden" name="action" value="affiliate-withdraw">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control bg-light border-light" value="<?=$transid;?>" name="transid" placeholder="<?=__('Mã giao dịch');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <select class="form-control bg-light border-light" name="status">
                                                <option value=""><?=__('Trạng thái');?></option>
                                                <option <?=$status == 'pending' ? 'selected' : '';?> value="pending"><?=__('Pending');?></option>
                                                <option <?=$status == 'cancel' ? 'selected' : '';?> value="cancel"><?=__('Cancel');?></option>
                                                <option <?=$status == 'completed' ? 'selected' : '';?> value="completed"><?=__('Completed');?></option>
                                            </select>
                                            <i class="ri-filter-3-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control bg-light border-light" id="flatpickr-range" name="time" placeholder="<?=__('Chọn thời gian cần tìm');?>" value="<?=$time;?>" readonly>
                                            <i class="ri-calendar-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="d-flex gap-2">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('?action=affiliate-withdraw');?>" class="btn btn-light waves-effect waves-light">
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
                                                <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000</option>
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
                                                <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1"><?=__('Hôm nay');?></option>
                                                <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2"><?=__('Tuần này');?></option>
                                                <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3"><?=__('Tháng này');?></option>
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
                                                <th class="text-center"><?=__('Mã giao dịch');?></th>
                                                <th class="text-center"><?=__('Thời gian');?></th>
                                                <th class="text-center"><?=__('Số tiền rút');?></th>
                                                <th class="text-center"><?=__('Ngân hàng');?></th>
                                                <th class="text-center"><?=__('Trạng thái');?></th>
                                                <th class="text-center"><?=__('Lý do');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <div class="text-center p-3">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                            colors="primary:#121331,secondary:#08a88a"
                                                            style="width:75px;height:75px">
                                                        </lord-icon>
                                                        <h5 class="mt-2"><?=__('Không tìm thấy kết quả');?></h5>
                                                        <p class="text-muted mb-0">
                                                            <?=__('Không có dữ liệu nào được tìm thấy');?>
                                                        </p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <?php else: ?>
                                            <?php foreach ($listDatatable as $row): ?>
                                            <tr>
                                                <td class="text-center"><?=$row['trans_id'];?></td>
                                                <td class="text-center"><?=$row['create_gettime'];?></td>
                                                <td class="text-center"><b><?=format_currency($row['amount']);?></b></td>
                                                <td class="text-center"><?=$row['bank'];?> - <?=$row['stk'];?></td>
                                                <td class="text-center"><?=display_withdraw($row['status']);?></td>
                                                <td class="text-center"><small><?=$row['reason'];?></small></td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <?php if($totalDatatable > $limit): ?>
                                <div class="d-flex justify-content-between align-items-center mt-4">
                                    <div class="text-muted fs-13">
                                        <?=__('Hiển thị');?> <?=$limit;?> <?=__('trên');?> <?=$totalDatatable;?> <?=__('kết quả');?>
                                    </div>
                                    <div class="pagination">
                                        <?=$urlDatatable;?>
                                    </div>
                                </div>
                                <?php endif; ?>
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

<script type="text/javascript">
$(document).ready(function() {
    // Đảm bảo các thông báo lỗi ẩn khi mới load trang
    $('.invalid-feedback').hide();
    
    // Chỉ sử dụng một event handler để xử lý form
    $("#withdrawForm").on('submit', function(e) {
        e.preventDefault();
        
        // Reset validation state
        $('.is-invalid').removeClass('is-invalid');
        $('.invalid-feedback').hide();
        
        // Validate các trường bắt buộc
        let isValid = true;
        
        // Kiểm tra số tiền
        if (!$('#amount').val().trim()) {
            $('#amount').addClass('is-invalid');
            $('#amountFeedback').show();
            isValid = false;
        }
        
        // Kiểm tra ngân hàng
        if (!$('#bank').val()) {
            $('#bank').addClass('is-invalid');
            $('#bankFeedback').show();
            isValid = false;
        }
        
        // Kiểm tra số tài khoản
        if (!$('#stk').val().trim()) {
            $('#stk').addClass('is-invalid');
            $('#stkFeedback').show();
            isValid = false;
        }
        
        // Kiểm tra tên chủ tài khoản
        if (!$('#name').val().trim()) {
            $('#name').addClass('is-invalid');
            $('#nameFeedback').show();
            isValid = false;
        }
        
        if (!isValid) {
            console.log("Validation failed");
            return false;
        }
        
        // Nếu form hợp lệ, tiến hành gửi AJAX
        $('#btnWithdraw').html('<i class="ri-loader-4-line fa-spin me-1"></i> <?=__("Đang xử lý...");?>').prop('disabled', true);
        
        $.ajax({
            url: "<?=BASE_URL('ajaxs/client/create.php');?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'WithdrawCommission',
                token: $('#token').val(),
                bank: $('#bank').val(),
                stk: $('#stk').val(),
                name: $('#name').val(),
                amount: $('#amount').val()
            },
            success: function(result) {
                if (result.status == 'success') {
                    Swal.fire({
                        title: '<?=__('Thành công!');?>',
                        icon: 'success',
                        text: result.msg,
                        showConfirmButton: false,
                        timer: 3000
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '<?=__('Thất bại!');?>',
                        icon: 'error',
                        text: result.msg,
                        confirmButtonText: 'OK'
                    });
                }
                $('#btnWithdraw').html('<i class="ri-arrow-right-circle-line me-1"></i> <?=__('Xác Nhận Rút Tiền');?>').prop('disabled', false);
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    title: '<?=__('Lỗi!');?>',
                    icon: 'error',
                    text: '<?=__('Có lỗi xảy ra, vui lòng thử lại sau.');?>',
                    confirmButtonText: 'OK'
                });
                $('#btnWithdraw').html('<i class="ri-arrow-right-circle-line me-1"></i> <?=__('Xác Nhận Rút Tiền');?>').prop('disabled', false);
            }
        });
    });
    
    // Reset validation khi người dùng bắt đầu nhập
    $('#withdrawForm input, #withdrawForm select').on('input change', function() {
        $(this).removeClass('is-invalid');
        // Ẩn thông báo lỗi tương ứng
        let id = $(this).attr('id');
        $('#' + id + 'Feedback').hide();
    });
});

// Cập nhật hàm tính toán số tiền (nếu cần)
function totalAmount() {
    var amount = $("#amount").val() || 0;
    // Xử lý tính toán nếu cần
}
</script>
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
    // Validate form
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('.needs-validation');
        Array.prototype.slice.call(forms).forEach(function(form) {
            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    });
</script>
<script>
new ClipboardJS(".copy");

function copy(){
    showMessage('<?=__('Đã sao chép liên kết');?>', 'success');
}
</script>