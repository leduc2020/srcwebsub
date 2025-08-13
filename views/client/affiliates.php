<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Affiliate Program').' | '.$CMSNT->site('title'),
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
.copy-btn {
    transition: all 0.3s ease;
    overflow: hidden;
}
.copy-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.3);
}
.social-share a {
    width: 40px;
    height: 40px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin: 0 5px;
    color: white;
    transition: all 0.3s ease;
}
.social-share a i {
    color: #fff;
    font-size: 20px;
}
.social-share a:hover {
    transform: translateY(-3px);
}
.social-share .facebook { background: #1877F2; }
.social-share .twitter { background: #1DA1F2; }
.social-share .linkedin { background: #0A66C2; }
.social-share .whatsapp { background: #25D366; }
.social-share .telegram { background: #0088cc; }
.table-container {
    background: #fff;
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
$where = " `ref_id` = '".$getUser['id']."'  ";
$shortByDate = '';
 
$time = '';
 
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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `users` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `users` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination_client(base_url("?action=affiliates&limit=$limit&shortByDate=$shortByDate&time=$time&"), $from, $totalDatatable, $limit);
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Affiliate Program');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Trang Chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Affiliate Program');?></li>
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
                                    <img src="<?=base_url('assets/img/icon-link.webp');?>"
                                        alt="<?=__('Liên kết giới thiệu');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Liên kết giới thiệu');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Chia sẻ và nhận hoa hồng');?></p>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-secondary bg-soft-secondary border-0 mb-4" role="alert">
                                <div class="d-flex align-items-center">
                                    <i class="ri-information-line me-3 fs-3"></i>
                                    <div>
                                        <h5 class="alert-heading fs-14 mb-1"><?=__('Hướng Dẫn Giới Thiệu');?></h5>
                                        <p class="mb-0 fs-13"><?=__('Chia sẻ liên kết của bạn và nhận hoa hồng khi có người đăng ký thành công.');?></p>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-medium"><?=__('Liên kết giới thiệu của bạn');?></label>
                                <div class="input-group">
                                    <input type="text" class="form-control form-control-lg" id="urlRef" readonly value="<?=base_url('?aff='.$getUser['id']);?>">
                                    <button class="btn btn-primary copy-btn" onclick="copy()" data-clipboard-target="#urlRef">
                                        <i class="ri-file-copy-line me-1"></i><?=__('Sao chép');?>
                                    </button>
                                </div>
                            </div>

                            <div class="text-center social-share mb-4">
                                <p class="fs-14 text-muted mb-3"><?=__('Chia sẻ qua mạng xã hội:');?></p>
                                <div class="social-share">
                                    <a href="https://www.facebook.com/sharer/sharer.php?u=<?=urlencode(base_url('join/'.$getUser['id']));?>" target="_blank" class="facebook" title="<?=__('Chia sẻ lên Facebook');?>">
                                        <i class="ri-facebook-fill"></i>
                                    </a>
                                    <a href="https://twitter.com/intent/tweet?url=<?=urlencode(base_url('join/'.$getUser['id']));?>" target="_blank" class="twitter" title="<?=__('Chia sẻ lên Twitter');?>">
                                        <i class="ri-twitter-fill"></i>
                                    </a>
                                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?=urlencode(base_url('join/'.$getUser['id']));?>" target="_blank" class="linkedin" title="<?=__('Chia sẻ lên LinkedIn');?>">
                                        <i class="ri-linkedin-fill"></i>
                                    </a>
                                    <a href="https://api.whatsapp.com/send?text=<?=urlencode(__('Tham gia ngay: ').base_url('join/'.$getUser['id']));?>" target="_blank" class="whatsapp" title="<?=__('Chia sẻ qua WhatsApp');?>">
                                        <i class="ri-whatsapp-line"></i>
                                    </a>
                                    <a href="https://t.me/share/url?url=<?=urlencode(base_url('join/'.$getUser['id']));?>&text=<?=urlencode(__('Tham gia ngay!'));?>" target="_blank" class="telegram" title="<?=__('Chia sẻ qua Telegram');?>">
                                        <i class="ri-telegram-line"></i>
                                    </a>
                                </div>
                            </div>

                            <div class="alert alert-light-subtle border-start border-primary border-1 p-3">
                                <div class="d-flex">
                                    <div class="flex-shrink-0 text-primary fs-2">
                                        <i class="ri-information-line"></i>
                                    </div>
                                    <div class="ms-3">
                                        <h5 class="fs-14 mb-1"><?=__('Lưu ý quan trọng');?></h5>
                                        <div class="text-muted fs-13">
                                            <?=$CMSNT->site('affiliate_note');?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-xl-5">
                    <div class="card">
                        <div class="card-body">
                            <div class="text-center mb-4 position-relative py-3">
                                <div class="mb-4">
                                    <div class="position-relative d-inline-block">
                                        <h3 class="display-6 mb-0 fw-semibold"><?=format_currency($getUser['ref_price']);?></h3>
                                    </div>
                                    <p class="text-muted fs-14 mt-2"><?=__('Số tiền hoa hồng khả dụng');?></p>
                                </div>
                                
                                <div class="progress-wrap mb-4">
                                    <?php
                                    $minAmount = $CMSNT->site('affiliate_min');
                                    $currentAmount = $getUser['ref_price'];
                                    $percentage = min(100, ($currentAmount / $minAmount) * 100);
                                    ?>
                                    
                                    <div class="d-flex justify-content-between mb-2">
                                        <span class="fs-12 text-muted"><?=__('Tiến độ đạt mức tối thiểu để rút');?></span>
                                        <span class="fs-12 fw-medium"><?=round($percentage)?>%</span>
                                    </div>
                                    
                                    <div class="progress bg-soft-primary" style="height: 8px;">
                                        <div class="progress-bar bg-primary" role="progressbar" 
                                            style="width: <?=$percentage?>%;" 
                                            aria-valuenow="<?=$percentage?>" 
                                            aria-valuemin="0" 
                                            aria-valuemax="100">
                                        </div>
                                    </div>
                                    
                                    <div class="d-flex justify-content-between mt-1">
                                        <span class="fs-12 text-muted">0</span>
                                        <span class="fs-12 text-muted"><?=format_currency($minAmount)?></span>
                                    </div>
                                </div>
                                
                                <a href="<?=base_url('client/affiliate-withdraw');?>" 
                                    data-bs-toggle="tooltip" 
                                    data-bs-placement="top" 
                                    title="<?=__('Số dư tối thiểu để rút:').' '.format_currency($CMSNT->site('affiliate_min'));?>" 
                                    class="btn btn-primary mt-2 shadow-sm">
                                    <i class="ri-bank-card-line me-1"></i> <?=__('Rút Tiền');?>
                                </a>
                                
                                <?php if($currentAmount < $minAmount): ?>
                                <div class="mt-2 text-muted fs-12">
                                    <i class="ri-information-line me-1"></i>
                                    <?=__('Bạn cần thêm');?> <span class="text-danger fw-medium"><?=format_currency($minAmount - $currentAmount)?></span> <?=__('để đạt mức tối thiểu');?>
                                </div>
                                <?php endif; ?>
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

                            
                        </div>
                    </div>
                </div>

                <div class="col-12" <?=$aos['fade-up'];?>>
                    <div class="card">
                        <div class="card-header">
                            <h4 class="card-title mb-0"><i class="las la-users text-primary me-2"></i><?=__('Thành viên mà bạn giới thiệu');?></h4>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="affiliates">
                                <div class="row g-3">
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
                                            <a href="<?=base_url('?action=affiliates');?>" class="btn btn-light waves-effect waves-light">
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
                                                <th class="text-center"><?=__('Khách hàng');?></th>
                                                <th class="text-center"><?=__('Ngày đăng ký');?></th>
                                                <th class="text-center"><?=__('Hoa hồng');?></th>
                                            </tr>
                                        </thead>
                                        <tbody class="list form-check-all" id="invoice-list-data">
                                            <?php if(empty($listDatatable)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center">
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
                                            <?php foreach ($listDatatable as $row2): ?>
                                            <tr>
                                                <td class="text-center"><?=$row2['username'];?></td>
                                                <td class="text-center"><?=$row2['create_date'];?></td>
                                                <td class="text-center">
                                                    <span class="text-success fw-medium"><?=format_cash($row2['ref_amount']);?></span>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                            <?php endif; ?>
                                        </tbody>
                                    </table>
                                </div>
                                <?php if($totalDatatable > 0): ?>
                                <div class="mt-1">
                                    <div class="p-3 bg-soft-info rounded-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <span class="text-muted me-3"><?=__('Tổng hoa hồng:');?></span>
                                                <span class="fw-bold text-danger fs-16"><?=format_currency($CMSNT->get_row(" SELECT SUM(`ref_amount`) FROM `users` WHERE $where ")['SUM(`ref_amount`)']);?></span>
                                            </div>
                                            <div>
                                                <span class="text-muted me-3"><?=__('Tổng thành viên:');?></span>
                                                <span class="fw-bold text-primary fs-16"><?=format_cash($CMSNT->get_row(" SELECT COUNT(id) FROM `users` WHERE $where ")['COUNT(id)']);?></span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>
                                
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

<?php require_once(__DIR__.'/footer.php');?>

<script>
new ClipboardJS(".copy-btn");

function copy(){
    showMessage('<?=__('Đã sao chép liên kết');?>', 'success');
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