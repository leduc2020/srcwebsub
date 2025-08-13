<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}


$body = [
    'title' => __('Nạp tiền bằng thẻ cào').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '

<style>
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
if($CMSNT->site('card_status') != 1){
    redirect(base_url('client/home'));
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
$where = " `user_id` = '".$getUser['id']."'  ";
$shortByDate = '';
$pin = '';
$time = '';
$serial = '';
$status = '';

if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "'.$status.'" ';
}
if(!empty($_GET['pin'])){
    $pin = check_string($_GET['pin']);
    $where .= ' AND `pin` LIKE "%'.$pin.'%" ';
}
if(!empty($_GET['serial'])){
    $serial = check_string($_GET['serial']);
    $where .= ' AND `serial` LIKE "%'.$serial.'%" ';
}
if(!empty($_GET['time'])){
    $time = check_string($_GET['time']);
    $create_date_1 = str_replace('-', '/', $time);
    $create_date_1 = explode(' to ', $create_date_1);
    if($create_date_1[0] != $create_date_1[1]){
        $create_date_1 = [$create_date_1[0].' 00:00:00', $create_date_1[1].' 23:59:59'];
        $where .= " AND `create_date` >= '".$create_date_1[0]."' AND `create_date` <= '".$create_date_1[1]."' ";
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
        $where .= " AND `create_date` LIKE '%".$currentDate."%' ";
    }
    if($shortByDate == 2){
        $where .= " AND YEAR(create_date) = $currentYear AND WEEK(create_date, 1) = $currentWeek ";
    }
    if($shortByDate == 3){
        $where .= " AND MONTH(create_date) = '$currentMonth' AND YEAR(create_date) = '$currentYear' ";
    }
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `cards` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `cards` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=recharge-card&limit=$limit&shortByDate=$shortByDate&time=$time&pin=$pin&serial=$serial&status=$status&"), $from, $totalDatatable, $limit);
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
                        <h4 class="mb-sm-0"><?=__('Nạp tiền bằng thẻ cào');?></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Nạp tiền');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Thẻ cào');?></li>
                            </ol>
                        </div>

                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <div class="col-xl-7">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary bg-soft py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?=base_url('assets/img/icon-sim.svg');?>"
                                        alt="<?=__('Nạp tiền bằng thẻ cào');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Nạp tiền bằng thẻ cào');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Nhận ngay tiền sau khi nạp thẻ thành công');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-secondary bg-soft-secondary border-0 mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-information-line me-3 fs-3"></i>
                                    <div>
                                        <h5 class="alert-heading fs-14 mb-1"><?=__('Hướng dẫn nạp thẻ');?></h5>
                                        <p class="mb-0 fs-13"><?=__('Vui lòng điền đầy đủ thông tin bên dưới để nạp thẻ. Hệ thống xử lý tự động trong vòng 1-2 phút.');?></p>
                                    </div>
                                </div>
                            </div>

                            <form id="recharge-form" onsubmit="rechargeTelco(); return false;" class="needs-validation" novalidate>
                                <div class="row">
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="telco" class="form-label fw-medium"><?=__('Loại thẻ');?> <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg has-validation">
                                                <span class="input-group-text bg-light"><i class="ri-sim-card-line"></i></span>
                                                <select class="form-select form-select-lg bg-light-subtle telco-select" id="telco" required>
                                                    <option value="" selected disabled>-- <?= __('Chọn loại thẻ'); ?> --</option>
                                                    <?php
                                                    // Lấy dữ liệu từ cấu hình
                                                    $list_network_topup_card = $CMSNT->site('list_network_topup_card');
                                                    // Tách các dòng dữ liệu
                                                    $cards = explode("\n", $list_network_topup_card);
                                                    foreach ($cards as $card) {
                                                        $card = trim($card);
                                                        if(!$card) {
                                                            continue;
                                                        }
                                                        // Tách thành mảng theo dấu |
                                                        $arr = explode('|', $card);
                                                        if(count($arr) == 2) {
                                                            $icon = strtolower($arr[0]);
                                                            echo '<option value="'.$arr[0].'" data-icon="'.$icon.'">'.$arr[1].'</option>';
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                                <div class="invalid-feedback"><?=__('Vui lòng chọn loại thẻ');?></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-6">
                                        <div class="mb-4">
                                            <label for="amount" class="form-label fw-medium"><?=__('Mệnh giá');?> <span class="text-danger">*</span></label>
                                            <div class="input-group input-group-lg has-validation">
                                                <span class="input-group-text bg-light"><i class="ri-money-cny-circle-line"></i></span>
                                                <select class="form-select form-select-lg bg-light-subtle" onchange="totalPrice()" id="amount" required>
                                                    <option value="" selected disabled>-- <?=__('Chọn mệnh giá');?> --</option>
                                                    <option value="10000">10.000đ</option>
                                                    <option value="20000">20.000đ</option>
                                                    <option value="30000">30.000đ</option>
                                                    <option value="50000">50.000đ</option>
                                                    <option value="100000">100.000đ</option>
                                                    <option value="200000">200.000đ</option>
                                                    <option value="500000">500.000đ</option>
                                                    <option value="1000000">1.000.000đ</option>
                                                    <option value="2000000">2.000.000đ</option>
                                                </select>
                                                <div class="invalid-feedback"><?=__('Vui lòng chọn mệnh giá thẻ');?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-4">
                                    <label for="serial" class="form-label fw-medium"><?=__('Serial');?> <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light"><i class="ri-barcode-line"></i></span>
                                        <input type="text" class="form-control form-control-lg bg-light-subtle" id="serial" 
                                            placeholder="<?=__('Nhập số serial mặt sau thẻ cào');?>" required>
                                        <div class="invalid-feedback"><?=__('Vui lòng nhập số serial thẻ');?></div>
                                    </div>
                                    <div class="form-text text-muted fs-12"><i class="ri-information-line me-1"></i> <?=__('Nhập chính xác dãy số trên thẻ, không có dấu cách hay ký tự đặc biệt');?></div>
                                </div>

                                <div class="mb-4">
                                    <label for="pin" class="form-label fw-medium"><?=__('Mã thẻ');?> <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light"><i class="ri-key-2-line"></i></span>
                                        <input type="text" class="form-control form-control-lg bg-light-subtle" id="pin" 
                                            placeholder="<?=__('Nhập mã số đã cào trên thẻ');?>" required>
                                        <input type="hidden" id="token" value="<?=$getUser['token'];?>" />
                                        <div class="invalid-feedback"><?=__('Vui lòng nhập mã thẻ');?></div>
                                    </div>
                                    <div class="form-text text-muted fs-12"><i class="ri-information-line me-1"></i> <?=__('Thẻ cào đã sử dụng hoặc sai định dạng sẽ không được xử lý');?></div>
                                </div>

                                <div class="alert border border-dashed mb-4 conversion-box">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line fs-3 me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 fw-medium"><?=__('Số tiền thực nhận ước tính');?></h5>
                                            <div class="fs-13 text-muted">
                                                ~<span id="ketqua" class="text-danger fw-medium fs-16">0</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="d-grid">
                                    <button type="submit" class="btn btn-primary btn-lg create-invoice-btn" id="submit">
                                        <span class="btn-text text-uppercase"><i class="ri-bank-card-line me-1"></i> <?=__('Nạp ngay');?></span>
                                        <span class="btn-spinner d-none">
                                            <span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span>
                                            <?=__('Đang xử lý...');?>
                                        </span>
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
                                <?=$CMSNT->site('card_notice');?>
                            </div>
                            
                            <!-- Thêm các thông tin bổ sung về nạp thẻ -->
                            <div class="card-support mt-4">
                                <h5 class="text-primary fs-14 fw-medium mb-3"><?=__('Các loại thẻ được hỗ trợ:');?></h5>
                                <div class="d-flex flex-wrap gap-2 mb-3">
                                    <?php
                                    // Hiển thị các logo nhà mạng
                                    $cards = explode("\n", $CMSNT->site('list_network_topup_card'));
                                    foreach ($cards as $card) {
                                        $card = trim($card);
                                        if(!$card) continue;
                                        $arr = explode('|', $card);
                                        if(count($arr) == 2) {
                                            $icon = strtolower($arr[0]);
                                            echo '<span class="badge bg-primary px-3 py-2">'.$arr[1].'</span>';
                                        }
                                    }
                                    ?>
                                </div>
                                
                                <div class="d-flex align-items-center mt-4">
                                    <div class="avatar-xs me-3 flex-shrink-0">
                                        <span class="avatar-title rounded-circle bg-soft-success text-success">
                                            <i class="ri-customer-service-2-line"></i>
                                        </span>
                                    </div>
                                    <div>
                                        <h5 class="fs-13 mb-0"><?=__('Cần hỗ trợ?');?></h5>
                                        <p class="text-muted mb-0 fs-12"><?=__('Liên hệ CSKH qua các kênh chăm sóc khách hàng');?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-12">
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?=__('Lịch sử nạp thẻ');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="recharge-card">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="pin" value="<?=$pin;?>"
                                                placeholder="<?=__('Mã thẻ');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="serial" value="<?=$serial;?>"
                                                placeholder="<?=__('Serial');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <select name="status" class="form-control bg-light border-light">
                                                <option value=""><?=__('-- Trạng thái --');?></option>
                                                <option <?=$status == 'pending' ? 'selected' : '';?> value="pending">
                                                    <?=__('Đang chờ xử lý');?></option>
                                                <option <?=$status == 'error' ? 'selected' : '';?> value="error">
                                                    <?=__('Thẻ lỗi');?></option>
                                                <option <?=$status == 'completed' ? 'selected' : '';?> value="completed">
                                                    <?=__('Thành công');?></option>
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
                                            <button type="submit" class="btn btn-primary waves-effect waves-light w-100">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('?action=recharge-card');?>" class="btn btn-light waves-effect waves-light w-100">
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
                                                <th class="text-center"><?=__('Nhà mạng');?></th>
                                                <th class="text-center"><?=__('Serial');?></th>
                                                <th class="text-center"><?=__('Mã thẻ');?></th>
                                                <th class="text-center"><?=__('Mệnh giá');?></th>
                                                <th class="text-center"><?=__('Thực nhận');?></th>
                                                <th class="text-center"><?=__('Trạng thái');?></th>
                                                <th class="text-center"><?=__('Thời gian');?></th>
                                                <th class="text-center"><?=__('Cập nhật');?></th>
                                                <th class="text-center"><?=__('Ghi chú');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="9" class="text-center">
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
                                            <?php foreach($listDatatable as $row2): ?>
                                            <tr>
                                                <td class="text-center"><?=$row2['telco'];?></td>
                                                <td class="text-center"><?=$row2['serial'];?></td>
                                                <td class="text-center"><?=$row2['pin'];?></td>
                                                <td class="text-end"><b
                                                        style="color: red;"><?=format_currency($row2['amount']);?></b></td>
                                                <td class="text-end"><b
                                                        style="color: green;"><?=format_currency($row2['price']);?></b></td>
                                                <td class="text-center"><?=display_card($row2['status']);?></td>
                                                <td class="text-center"><?=date('d/m/Y H:i:s', strtotime($row2['create_date']));?></td>
                                                <td class="text-center"><?=date('d/m/Y H:i:s', strtotime($row2['update_date']));?></td>
                                                <td><?=$row2['reason'];?></td>
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

function totalPrice() {
    try {
        var amount = $("#amount").val();
        if (amount) {
            var ck = <?=$CMSNT->site('card_ck');?>;
            var ketqua = amount - amount * ck / 100;
            $("#ketqua").html(ketqua.toLocaleString('vi-VN') + ' VNĐ');
            $(".conversion-box").addClass("border-primary").removeClass("border-dashed");
        } else {
            $("#ketqua").html('0');
            $(".conversion-box").removeClass("border-primary").addClass("border-dashed");
        }
    } catch (e) {
        $("#ketqua").html('0');
    }
}

function rechargeTelco() {
    var form = document.getElementById('recharge-form');
    if (!form.checkValidity()) {
        return;
    }
    
    var telco = $("#telco").val();
    var amount = $("#amount").val();
    var pin = $("#pin").val();
    var serial = $("#serial").val();
    var token = $("#token").val();
    var $btn = $('#submit');
    
    if (!telco || !amount || !pin || !serial) {
        Swal.fire({
            icon: 'error',
            title: '<?=__('Lỗi');?>',
            text: '<?=__('Vui lòng điền đầy đủ thông tin');?>'
        });
        return;
    }
    
    // Thay đổi trạng thái nút
    $btn.find('.btn-text').addClass('d-none');
    $btn.find('.btn-spinner').removeClass('d-none');
    $btn.prop('disabled', true);
    
    $.ajax({
        url: "<?=BASE_URL('ajaxs/client/recharge.php');?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'nap_the',
            token: token,
            telco: telco,
            amount: amount,
            pin: pin,
            serial: serial
        },
        success: function(respone) {
            if (respone.status == 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '<?=__('Thành công');?>',
                    text: respone.msg
                });
                setTimeout(function() {
                    location.reload();
                }, 1000);
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Thất bại');?>',
                    text: respone.msg
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
                text: '<?=__('Không thể kết nối đến máy chủ, vui lòng thử lại sau');?>'
            });
            
            // Khôi phục trạng thái nút
            $btn.find('.btn-spinner').addClass('d-none');
            $btn.find('.btn-text').removeClass('d-none');
            $btn.prop('disabled', false);
        }
    });
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