<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Nạp tiền bằng PayPal').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<style>

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
if($CMSNT->site('paypal_status') != 1){
    redirect(base_url());
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
$trans_id = '';
$time = '';
$amount = '';
$price  = '';



if(!empty($_GET['trans_id'])){
    $trans_id = check_string($_GET['trans_id']);
    $where .= ' AND `trans_id` LIKE "%'.$trans_id.'%" ';
}
if(!empty($_GET['amount'])){
    $amount = check_string($_GET['amount']);
    $where .= ' AND `amount` = '.$amount.' ';
}
if(!empty($_GET['price'])){
    $price = check_string($_GET['price']);
    $where .= ' AND `price` = '.$price.' ';
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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_paypal` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_paypal` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=recharge-paypal&limit=$limit&shortByDate=$shortByDate&time=$time&trans_id=$trans_id&amount=$amount&price=$price"), $from, $totalDatatable, $limit);

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
                        <h4 class="mb-sm-0"><?=__('Nạp tiền bằng PayPal');?></h4>

                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Nạp tiền');?></a></li>
                                <li class="breadcrumb-item active"><?=__('PayPal');?></li>
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
                                    <img src="<?=base_url('assets/img/icon-paypal.svg');?>"
                                        alt="<?=__('Nạp tiền bằng PayPal');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Nạp tiền qua PayPal');?></h4>
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
                                        <p class="mb-0 fs-13"><?=__('Nhập số tiền và nhấn nút thanh toán để bắt đầu quy trình nạp tiền.');?></p>
                                    </div>
                                </div>
                            </div>

                            <form id="recharge-form" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <label for="amount" class="form-label fw-medium"><?=__('Số tiền nạp (USD)');?> <span class="text-danger">*</span></label>
                                    <div class="input-group input-group-lg has-validation">
                                        <span class="input-group-text bg-light text-primary"><i class="ri-money-dollar-circle-line"></i></span>
                                        <input type="number" class="form-control form-control-lg bg-light-subtle" id="amount" name="amount"
                                            placeholder="<?=__('Vui lòng nhập số tiền cần nạp');?>" required>
                                        <input type="hidden" class="form-control" id="token" value="<?=$getUser['token'];?>">
                                        <div class="invalid-feedback"><?=__('Vui lòng nhập số tiền cần nạp');?></div>
                                    </div>
                                    <div class="d-flex mt-2">
                                        <div class="form-text text-muted"><i class="ri-information-line me-1"></i> <?=__('Số tiền sẽ được quy đổi từ USD sang VNĐ');?></div>
                                    </div>
                                </div>

                                <div class="alert alert-light border border-dashed mb-4 conversion-box">
                                    <div class="d-flex align-items-center">
                                        <div class="flex-shrink-0">
                                            <i class="ri-exchange-dollar-line text-primary fs-3 me-3"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h5 class="fs-14 fw-medium"><?=__('Thanh toán với PayPal');?></h5>
                                            <div class="fs-13 text-muted">
                                                <?=__('Giao dịch được bảo mật 100% qua PayPal');?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <div id="paypal-button-container"></div>
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
                            <?=$CMSNT->site('paypal_note');?>
                        </div>
                    </div>
                </div>
    
                

                <div class="col-xl-12">


                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><?=__('Lịch sử nạp tiền PayPal');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="recharge-paypal">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="trans_id" value="<?=$trans_id;?>"
                                                placeholder="<?=__('Mã giao dịch');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6 col-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="amount" value="<?=$amount;?>"
                                                placeholder="<?=__('Số tiền gửi');?>">
                                            <i class="ri-money-dollar-circle-line search-icon"></i>
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
                                            <a href="<?=base_url('?action=recharge-paypal');?>" class="btn btn-light waves-effect waves-light w-100">
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
                                    <table class="table align-middle table-nowrap">
                                        <thead class="table-light text-muted">
                                            <tr>
                                                <th><?=__('Mã giao dịch');?></th>
                                                <th class="text-center"><?=__('Số tiền gửi');?></th>
                                                <th class="text-center"><?=__('Thực nhận');?></th>
                                                <th class="text-center"><?=__('Thời gian');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="4" class="text-center">
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
                                                <td><?=$row2['trans_id'];?></td>
                                                <td class="text-center"><b><?=$row2['amount'];?> USD</b></td>
                                                <td class="text-center"><b class="text-danger"><?=format_currency($row2['price']);?></b></td>
                                                <td class="text-center"><?=$row2['create_date'];?></td>
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
                                                <span class="fw-bold text-danger fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_paypal` WHERE $where ")['SUM(`price`)']);?></span>
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

<script src="https://www.paypal.com/sdk/js?client-id=<?=$CMSNT->site('paypal_clientId');?>&currency=USD"></script>

<script>
(function($) {
    paypal.Buttons({

        // Sets up the transaction when a payment button is clicked
        createOrder: function(data, actions) {
            return actions.order.create({
                purchase_units: [{
                    amount: {
                        value: $('#amount')
                            .val() // Can reference variables or functions. Example: `value: document.getElementById('...').value`
                    }
                }]
            });
        },

        // Finalize the transaction after payer approval
        onApprove: function(data, actions) {
            return actions.order.capture().then(function(orderData) {
                $.ajax({
                    url: '<?=BASE_URL('ajaxs/client/recharge.php');?>',
                    method: 'POST',
                    data: {
                        action: 'confirmPaypal',
                        token: '<?=$getUser['token'];?>',
                        order: orderData
                    },
                    success: function(response) {
                        const result = JSON.parse(response)
                        if (result.status == 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '<?=__('Thành công');?>',
                                text: result.msg,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<?=__('Lỗi');?>',
                                text: result.msg
                            });
                        }
                    }
                })
            });
        }
    }).render('#paypal-button-container');
})(jQuery)
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

 