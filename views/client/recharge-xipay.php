<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Recharge XiPay').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<style>
.crypto-logo-container {
    position: relative;
    padding: 10px;
    margin-bottom: 25px;
}
.crypto-logo {
    transition: transform 0.3s ease;
}
.crypto-logo:hover {
    transform: scale(1.05);
}
.crypto-badge {
    position: absolute;
    bottom: -12px;
    left: 50%;
    transform: translateX(-50%);
    font-size: 11px;
    padding: 5px 15px;
    border-radius: 20px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}
.conversion-box {
    transition: all 0.3s ease;
}
.conversion-box:hover {
    background-color: #f8f9fa;
    border-color: #0d6efd !important;
}
.create-invoice-btn {
    transition: all 0.3s ease;
    overflow: hidden;
}
.create-invoice-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}
.create-invoice-btn .btn-text, 
.create-invoice-btn .btn-spinner {
    transition: opacity 0.3s ease;
}
input:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.1) !important;
    border-color: #86b7fe !important;
}
.method-card {
    border: 2px solid #dee2e6;
    border-radius: 15px;
    padding: 1.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
  
    display: flex;
    align-items: center;
    gap: 1rem;
}

.method-card:hover {
    border-color: #0d6efd;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.method-card.active {
    border-color: #0d6efd;
}

.method-logo {
    flex-shrink: 0;
}
</style>
';
$body['footer'] = '
 
';
require_once(__DIR__.'/../../models/is_user.php');
if($CMSNT->site('gateway_xipay_status') != 1){
    redirect(base_url('client/'));
}
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
$where = " `user_id` = '".$getUser['id']."' AND `status` = 1 ";
$shortByDate = '';
$trans_id = '';
$time = '';
$amount = '';
$billName  = '';



if(!empty($_GET['trans_id'])){
    $trans_id = check_string($_GET['trans_id']);
    $where .= ' AND `out_trade_no` = "'.$trans_id.'" ';
}
if(!empty($_GET['amount'])){
    $amount = check_string($_GET['amount']);
    $where .= ' AND `amount` = '.$amount.' ';
}
if(!empty($_GET['time'])){
    $time = check_string($_GET['time']);
    $create_gettime_1 = str_replace('-', '/', $time);
    $create_gettime_1 = explode(' to ', $create_gettime_1);
    if($create_gettime_1[0] != $create_gettime_1[1]){
        $create_gettime_1 = [$create_gettime_1[0].' 00:00:00', $create_gettime_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$create_gettime_1[0]."' AND `created_at` <= '".$create_gettime_1[1]."' ";
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
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_xipay` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_xipay` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=recharge-xipay&limit=$limit&shortByDate=$shortByDate&time=$time&trans_id=$trans_id&amount=$amount&"), $from, $totalDatatable, $limit);

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
                        <h4 class="mb-sm-0"><?=__('Nạp tiền qua AliPay & WeChat Pay');?></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Nạp tiền');?></a></li>
                                <li class="breadcrumb-item active"><?=__('XiPay');?></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <?php require_once(__DIR__.'/block-promotion.php');?>

                <div class="col-xl-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary bg-soft py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?=base_url('assets/img/icon-alipay.webp');?>"
                                        alt="<?=__('Nạp tiền qua AliPay & WeChat Pay');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Nạp tiền qua AliPay & WeChat Pay');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Thanh toán nhanh chóng và an toàn');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-secondary bg-soft-secondary border-0 mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-information-line me-3 fs-3"></i>
                                    <div>
                                        <h5 class="alert-heading fs-14 mb-1"><?=__('Hướng dẫn nạp XiPay');?></h5>
                                        <p class="mb-0 fs-13"><?=__('Nhập số tiền và chọn phương thức thanh toán phù hợp để bắt đầu nạp tiền.');?></p>
                                    </div>
                                </div>
                            </div>

                            <?php 
                            if(isset($_POST['WIDtotal_fee'])){

                                require_once("libs/xipay.php");
                                $epay_config['apiurl'] = 'https://pay.xipay.cc/';
                                $epay_config['pid'] = $CMSNT->site('gateway_xipay_pid');
                                $epay_config['key'] = $CMSNT->site('gateway_xipay_md5key');

                                $notify_url = base_url('api/callback_xipay.php');
                                $return_url = base_url('?action=recharge-xipay');
                                $out_trade_no = random('QWERTYUIOPASDFGHKZXCVBNM123456789', 16);
                                // Lấy loại thanh toán từ POST, kiểm tra nếu không phải 'alipay' hoặc 'wxpay' thì mặc định 'alipay'
                                $type = check_string($_POST['type']);
                                if ($type !== 'alipay' && $type !== 'wxpay') {
                                    $type = 'alipay';
                                }
                                $name = __('Deposit money into').' '.$getUser['username'];
                                $amount = check_string($_POST['WIDtotal_fee']);
                                $price = $amount * $CMSNT->site('gateway_xipay_rate');
                                $price = checkPromotion($price); // Bao gồm khuyến mãi nạp tiền
                                
                                $parameter = array(
                                    "pid" => $epay_config['pid'],
                                    "type" => $type,
                                    "notify_url" => $notify_url,
                                    "return_url" => $return_url,
                                    "out_trade_no" => $out_trade_no,
                                    "name" => $name,
                                    "money"	=> $amount,
                                );

                                $CMSNT->insert('payment_xipay', [
                                    'user_id'       => $getUser['id'],
                                    'out_trade_no'  => $out_trade_no,
                                    'type'          => $type,
                                    'price'         => $price,
                                    'amount'        => $amount,
                                    'product_name'  => $name,
                                    'status'        => 0,
                                    'created_at'    => gettime()
                                ]);
                                
                                $epay = new EpayCore($epay_config);
                                $html_text = $epay->pagePay($parameter);
                                echo $html_text;
                                die('<p>'.__('Redirecting you to the payment page, please wait...').'</p>');
                            }
                            ?>


                            <form action="<?=base_url('?action=recharge-xipay');?>" method="POST" id="recharge-form" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="WIDtotal_fee" class="form-label fw-medium"><?=__('Số tiền nạp (元)');?> <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light"><i class="ri-money-cny-circle-line"></i></span>
                                        <input type="number" class="form-control form-control-lg bg-light-subtle" id="WIDtotal_fee" name="WIDtotal_fee" placeholder="<?=__('Nhập số tiền cần nạp');?>" required step="0.01" min="1">
                                        <span class="input-group-text bg-light fw-medium"><?=__('元');?></span>
                                        <input type="hidden" id="token" value="<?=$getUser['token'];?>">
                                        <div class="invalid-feedback"><?=__('Vui lòng nhập số tiền hợp lệ');?></div>
                                    </div>
                                    <div class="d-flex justify-content-between mt-2">
                                        <div class="form-text text-muted fs-12"><?=__('Tỷ giá: 1 元 =');?><span class="text-danger fw-medium"> <?=format_currency($CMSNT->site('gateway_xipay_rate'));?></span></div>
                                        <div class="form-text text-muted fs-12"><?=__('Tối thiểu:');?> <span class="text-danger fw-medium">1 <?=__('元');?></span></div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label class="form-label fw-medium"><?=__('Chọn phương thức thanh toán');?> <span class="text-danger">*</span></label>
                                    <div class="payment-methods">
                                        <div class="row g-3">
                                            <!-- Alipay Card -->
                                            <div class="col-md-6">
                                                <div class="method-card active" data-value="alipay">
                                                    <div class="method-logo">
                                                        <i class="ri-alipay-line text-primary fs-3"></i>
                                                    </div>
                                                    <div class="method-info">
                                                        <h5 class="mb-1 fs-14">Alipay</h5>
                                                        <small class="text-muted"><?=__('Thanh toán qua Alipay');?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- WeChat Pay Card -->
                                            <div class="col-md-6">
                                                <div class="method-card" data-value="wxpay">
                                                    <div class="method-logo">
                                                        <i class="ri-wechat-line text-success fs-3"></i>
                                                    </div>
                                                    <div class="method-info">
                                                        <h5 class="mb-1 fs-14">WeChat Pay</h5>
                                                        <small class="text-muted"><?=__('Thanh toán qua WeChat');?></small>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" id="type_value" name="type" value="alipay" required>
                                </div>

                                <div class="alert alert-light border border-dashed mb-4 conversion-box">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line text-primary fs-3 me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 fw-medium"><?=__('Số tiền thực nhận ước tính');?></h5>
                                            <div class="fs-13 text-muted">
                                                ~<span id="vnd_amount" class="text-danger fw-medium fs-16">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg position-relative create-invoice-btn">
                                        <span class="btn-text text-uppercase"><i class="ri-secure-payment-line me-1"></i> <?=__('Tạo giao dịch');?></span>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="ri-alert-line text-warning me-2"></i><?=__('Lưu ý');?></h4>
                        </div>
                        <div class="card-body">
                            <div class="notice-content">
                                <?=$CMSNT->site('xipay_notice');?>
                            </div>
                            
                            <div class="card-support mt-4">
                                <div class="d-flex align-items-center mt-3">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title rounded-circle bg-soft-success text-success">
                                            <i class="ri-shield-check-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="fs-13 mb-0"><?=__('Thanh toán an toàn');?></h5>
                                        <p class="text-muted mb-0 fs-12"><?=__('Giao dịch được xử lý qua cổng thanh toán an toàn');?></p>
                                    </div>
                                </div>
                                
                                <div class="d-flex align-items-center mt-3">
                                    <div class="avatar-xs flex-shrink-0 me-3">
                                        <span class="avatar-title rounded-circle bg-soft-primary text-primary">
                                            <i class="ri-time-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="fs-13 mb-0"><?=__('Xử lý tức thì');?></h5>
                                        <p class="text-muted mb-0 fs-12"><?=__('Tiền được cộng vào tài khoản ngay sau khi thanh toán thành công');?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?=__('Lịch sử nạp tiền');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="recharge-xipay">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="trans_id" value="<?=$trans_id;?>"
                                                placeholder="<?=__('Mã giao dịch');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="number" step="0.01" class="form-control search bg-light border-light"
                                                name="amount" value="<?=$amount;?>"
                                                placeholder="<?=__('Số tiền nạp');?>">
                                            <i class="ri-money-cny-circle-line search-icon"></i>
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
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('?action=recharge-xipay');?>" class="btn btn-light waves-effect waves-light w-100">
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
                                                <th class="text-center"><?=__('Phương thức');?></th>
                                                <th class="text-center"><?=__('Số tiền');?></th>
                                                <th class="text-center"><?=__('Thực nhận');?></th>
                                                <th class="text-center"><?=__('Thời gian');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="5" class="text-center">
                                                    <div class="text-center p-3">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop"
                                                            colors="primary:#121331,secondary:#08a88a"
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
                                            <?php foreach($listDatatable as $row): ?>
                                            <tr>
                                                <td class="text-center">
                                                    <span class="text-reset fw-medium"><?=$row['out_trade_no'];?></span>
                                                </td>
                                                <td class="text-center"><?=display_method_xipay($row['type']);?></td>
                                                <td class="text-end fw-medium">¥<?=$row['amount'];?></td>
                                                <td class="text-end"><b class="text-danger"><?=format_currency($row['price']);?></b></td>
                                                <td class="text-center"><?=$row['created_at'];?></td>
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
                                                <span class="fw-bold text-danger fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_xipay` WHERE $where ")['SUM(`price`)']);?></span>
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
// Tính số tiền VND khi nhập số tiền cần nạp
$(document).ready(function() {
    $("#WIDtotal_fee").on("input", function() {
        try {
            var amount = parseFloat($(this).val()) || 0;
            
            if (amount > 0) {
                // Hiển thị trạng thái đang tải
                $("#vnd_amount").html('<small><i class="spinner-border spinner-border-sm"></i></small>');
                
                // Gọi AJAX để tính toán số tiền thực nhận
                $.ajax({
                    url: "<?=base_url('ajaxs/client/recharge.php');?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'getReceivedXipay',
                        token: $("#token").val(),
                        amount: amount
                    },
                    success: function(response) {
                        if (response.status == 'success') {
                            $(".conversion-box").addClass("border-primary").removeClass("border-dashed");
                            $("#vnd_amount").html(response.received.toLocaleString('vi-VN'));
                        }
                    },
                    error: function(xhr, status, error) {
                        // Fallback cho trường hợp lỗi kết nối
                        var vndAmount = amount * <?=$CMSNT->site('gateway_xipay_rate');?>;
                        $(".conversion-box").addClass("border-primary").removeClass("border-dashed");
                        $("#vnd_amount").html(vndAmount.toLocaleString('vi-VN'));
                    }
                });
            } else {
                $(".conversion-box").removeClass("border-primary").addClass("border-dashed");
                $("#vnd_amount").html("0");
            }
        } catch (error) {
            $("#vnd_amount").html("0");
        }
    });
    
    // Xử lý chọn phương thức thanh toán
    $(document).on("click", ".method-card", function() {
        // Remove active class from all cards
        document.querySelectorAll('.method-card').forEach(c => c.classList.remove('active'));
        
        // Add active class to clicked card
        this.classList.add('active');
        
        // Update hidden input value
        document.getElementById('type_value').value = this.dataset.value;
    });
    
 
    
    // Check for new successful payments
    function loadData() {
        $.ajax({
            url: "<?=base_url('ajaxs/client/recharge.php');?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'notication_topup_xipay',
                token: '<?=$getUser['token'];?>'
            },
            success: function(respone) {
                if (respone.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công !');?>',
                        text: respone.msg,
                        showDenyButton: true,
                        confirmButtonText: '<?=__('Nạp Thêm');?>',
                        denyButtonText: `<?=__('Mua Ngay');?>`,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            location.reload();
                        } else if (result.isDenied) {
                            window.location.href = '<?=base_url();?>';
                        }
                    });
                }
                setTimeout(loadData, 5000);
            },
            error: function() {
                setTimeout(loadData, 5000);
            }
        });
    }
    loadData();
});
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