<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Quản lý kết nối API').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<style>
.stats-info {
    font-size: 12px;
    line-height: 1.4;
}
.stat-item {
    display: flex;
    align-items: center;
    gap: 5px;
    margin-bottom: 4px;
}
.stat-item i {
    width: 16px;
    font-size: 12px;
}
.stat-item strong {
    font-size: 11px;
    min-width: 60px;
}
.stat-item .badge {
    font-size: 10px;
    padding: 2px 6px;
}
.stat-item br + span {
    margin-left: 21px;
    font-size: 11px;
}
.dropdown-item {
    padding: 8px 16px;
    font-size: 13px;
    transition: all 0.2s ease;
}
.dropdown-item i {
    width: 18px;
    margin-right: 8px;
    text-align: center;
    font-size: 12px;
}
.dropdown-menu {
    box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(0, 0, 0, 0.08);
    border-radius: 6px;
    min-width: 160px;
}
.dropdown-item:hover {
    background-color: #f8f9fa;
    color: #495057;
}
.dropdown-toggle::after {
    margin-left: 8px;
}
@media (max-width: 768px) {
    .dropdown-menu {
        font-size: 12px;
    }
    .dropdown-item {
        padding: 6px 12px;
    }
}
</style>
';
$body['footer'] = '

';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
if(checkPermission($getUser['admin'], 'manager_suppliers') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
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
$where = " `id` > 0 ";
$shortByDate  = '';


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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `suppliers` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `suppliers` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination(base_url_admin("suppliers&limit=$limit&shortByDate=$shortByDate&"), $from, $totalDatatable, $limit);

?>
 

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-code"></i> <?=__('Kết nối API');?></h1>
        </div>
         
         
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('DANH SÁCH API ĐANG KẾT NỐI');?>
                        </div>
                        <div class="d-flex">
                            <a type="button" href="<?=base_url_admin('supplier-add');?>"
                                class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i
                                    class="ri-add-line fw-semibold align-middle"></i> <?=__('THÊM WEBSITE API');?></a>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?=base_url();?>" class="align-items-center mb-3" name="formSearch"
                            method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                <input type="hidden" name="action" value="suppliers">
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label"><?=__('Show :');?></label>
                                    <select name="limit" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                        <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                        <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                        <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                        <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                        <option <?=$limit == 500 ? 'selected' : '';?> value="500">500</option>
                                        <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label"><?=__('Short by Date');?>:</label>
                                    <select name="shortByDate" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option value=""><?=__('Tất cả');?></option>
                                        <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1">
                                            <?=__('Hôm nay');?>
                                        </option>
                                        <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2">
                                            <?=__('Tuần này');?>
                                        </option>
                                        <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3">
                                            <?=__('Tháng này');?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th><?=__('Website');?></th>
                                        <th class="text-center"><?=__('Type');?></th>
                                        <th class="text-center"><?=__('Số dư');?></th>
                                        <th class="text-center"><?=__('Thống kê');?></th>
                                        <th class="text-center"><?=__('Chi tiết');?></th>
                                        <th class="text-center"><?=__('Trạng thái');?></th>
                                        <th class="text-center"><?=__('Thao tác');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($listDatatable as $supplier): ?>
                                    <?php
                                    // Tính toán thống kê cho từng supplier
                                    $categories_count = $CMSNT->num_rows("SELECT * FROM `categories` WHERE `supplier_id` = '".$supplier['id']."'");
                                    $services_count = $CMSNT->num_rows("SELECT * FROM `services` WHERE `supplier_id` = '".$supplier['id']."'");
                                    $orders_count = $CMSNT->num_rows("SELECT * FROM `orders` WHERE `supplier_id` = '".$supplier['id']."'");
                                    
                                    // Tính doanh thu và lợi nhuận
                                    $revenue_data = $CMSNT->get_row("SELECT SUM(`pay`) as total_revenue, SUM(`pay` - `cost`) as total_profit FROM `orders` WHERE `supplier_id` = '".$supplier['id']."' AND `status` = 'Completed'");
                                    $total_revenue = $revenue_data ? (float)$revenue_data['total_revenue'] : 0;
                                    $total_profit = $revenue_data ? (float)$revenue_data['total_profit'] : 0;
                                    ?>
                                    <tr onchange="updateForm(`<?=$supplier['id'];?>`)">
                                        <td>
                                            <?php if (!empty($supplier['domain'])): ?>
                                                <i class="fa-solid fa-link"></i> Domain: <a class="text-primary"
                                                    href="<?=$supplier['domain'];?>"
                                                    target="_blank"><?=$supplier['domain'];?></a><br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($supplier['username'])): ?>
                                                <i class="fa-solid fa-user"></i> Username:
                                                <strong><?=substr($supplier['username'], 0, 4);?>...</strong><br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($supplier['password'])): ?>
                                                <i class="fa-solid fa-lock"></i> Password:
                                                <strong><?=substr($supplier['password'], 0, 4);?>...</strong><br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($supplier['api_key'])): ?>
                                                <i class="fa-solid fa-key"></i> API Key:
                                                <strong><?=substr($supplier['api_key'], 0, 12);?>...</strong><br>
                                            <?php endif; ?>
                                            
                                            <?php if (!empty($supplier['token'])): ?>
                                                <i class="fa-solid fa-key"></i> Token:
                                                <strong><?=substr($supplier['token'], 0, 12);?>...</strong>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?=$supplier['type'];?></span>
                                        </td>
                                        <td class="text-right">
                                            <?=check_string($supplier['price']);?>
                                        </td>
                                        <td>
                                            <div class="stats-info">
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-layer-group text-primary"></i> 
                                                    <strong><?=__('Chuyên mục:');?></strong> 
                                                    <span class="badge bg-info"><?=format_cash($categories_count);?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-boxes-stacked text-warning"></i> 
                                                    <strong><?=__('Dịch vụ:');?></strong> 
                                                    <span class="badge bg-warning"><?=format_cash($services_count);?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-cart-shopping text-success"></i> 
                                                    <strong><?=__('Đơn hàng:');?></strong> 
                                                    <span class="badge bg-success"><?=format_cash($orders_count);?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-chart-line text-primary"></i> 
                                                    <strong><?=__('Doanh thu:');?></strong>
                                                    <span class="text-success fw-bold"><?=format_cash($total_revenue);?> đ</span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fa-solid fa-coins text-warning"></i> 
                                                    <strong><?=__('Lợi nhuận:');?></strong>
                                                    <span class="text-info fw-bold"><?=format_cash($total_profit);?> đ</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="stats-info">
                                                <?php 
                                                $updateNameBadge = $supplier['update_name'] == 'ON' ? 'success' : 'danger';
                                                $updatePriceBadge = $supplier['update_price'] == 'ON' ? 'success' : 'danger';
                                                $syncCategoryBadge = $supplier['sync_category'] == 'ON' ? 'success' : 'danger';
                                                $syncServiceBadge = $supplier['sync_service'] == 'ON' ? 'success' : 'danger';
                                                ?>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-sync text-success"></i> 
                                                    <strong><?=__('Đồng bộ chuyên mục API:');?></strong> 
                                                    <span class="badge bg-<?=$syncCategoryBadge;?>"><?=$supplier['sync_category'];?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-sync text-success"></i> 
                                                    <strong><?=__('Đồng bộ dịch vụ API:');?></strong> 
                                                    <span class="badge bg-<?=$syncServiceBadge;?>"><?=$supplier['sync_service'];?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-percentage text-primary"></i> 
                                                    <strong><?=__('Tăng giá:');?></strong> 
                                                    <span class="badge bg-outline-primary" data-toggle="tooltip" data-placement="bottom" title="<?=__('Giá bán lẻ');?>"><?=$supplier['discount'];?>%</span>
                                                    <span class="badge bg-outline-primary" data-toggle="tooltip" data-placement="bottom" title="<?=getRankNameByTarget('price_1');?>"><?=$supplier['discount_price_1'];?>%</span>
                                                    <span class="badge bg-outline-primary" data-toggle="tooltip" data-placement="bottom" title="<?=getRankNameByTarget('price_2');?>"><?=$supplier['discount_price_2'];?>%</span>
                                                    <span class="badge bg-outline-primary" data-toggle="tooltip" data-placement="bottom" title="<?=getRankNameByTarget('price_3');?>"><?=$supplier['discount_price_3'];?>%</span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-tag text-info"></i> 
                                                    <strong><?=__('Cập nhật tên:');?></strong> 
                                                    <span class="badge bg-<?=$updateNameBadge;?>"><?=$supplier['update_name'];?></span>
                                                </div>
                                                <div class="stat-item mb-1">
                                                    <i class="fa-solid fa-dollar-sign text-warning"></i> 
                                                    <strong><?=__('Cập nhật giá:');?></strong> 
                                                    <span class="badge bg-<?=$updatePriceBadge;?>"><?=$supplier['update_price'];?></span>
                                                </div>
                                                <div class="stat-item">
                                                    <i class="fa-solid fa-money-bill-transfer text-success"></i> 
                                                    <strong><?=__('Tỷ giá tiền tệ quốc tế:');?></strong> 
                                                    <span class="badge bg-outline-primary"><?=$supplier['rate'];?></span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="form-check form-switch form-check-lg">
                                                <input class="form-check-input" type="checkbox"
                                                    id="status<?=$supplier['id'];?>" value="1"
                                                    <?=$supplier['status'] == 1 ? 'checked=""' : '';?>>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fa-solid fa-gear"></i> <?=__('Thao tác');?>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="<?=base_url_admin('supplier-manager&id='.$supplier['id']);?>">
                                                            <i class="fa-solid fa-chart-line text-primary"></i> <?=__('Thống kê');?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="<?=base_url_admin('category-sub&supplier_id='.$supplier['id']);?>">
                                                            <i class="fa-solid fa-folder text-secondary"></i> <?=__('Chuyên mục');?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="<?=base_url_admin('services&supplier_id='.$supplier['id']);?>">
                                                            <i class="fa-solid fa-bars-progress text-warning"></i> <?=__('Gói dịch vụ');?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="<?=base_url_admin('orders&supplier_id='.$supplier['id']);?>">
                                                            <i class="fa-solid fa-cart-shopping text-success"></i> <?=__('Đơn hàng');?>
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item" target="_blank" href="<?=base_url_admin('supplier-edit&id='.$supplier['id']);?>">
                                                            <i class="fas fa-edit text-info"></i> <?=__('Chỉnh sửa');?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)" 
                                                           onclick="removeCategoriesServices('<?=$supplier['id'];?>', '<?=$categories_count;?>', '<?=$services_count;?>')"
                                                           data-categories="<?=$categories_count;?>" 
                                                           data-services="<?=$services_count;?>">
                                                            <i class="fas fa-trash text-danger"></i> <?=__('Xóa chuyên mục và dịch vụ');?>
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="removeItem('<?=$supplier['id'];?>')">
                                                            <i class="fas fa-trash text-danger"></i> <?=__('Xóa');?>
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endforeach?>
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info"><?=__('Showing');?> <?=$limit;?> <?=__('of');?>
                                    <?=format_cash($totalDatatable);?>
                                    <?=__('Results');?></p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?=$totalDatatable > $limit ? $urlDatatable : '';?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


        <!-- <style>
        .brand-carousel {
            width: 100%;
            overflow: hidden;
            animation: moveCards 25s linear infinite;
            white-space: nowrap;
        }

        .brand-carousel-container {
            width: 100%;
            overflow-x: auto;
        }

        .brand-carousel {
            white-space: nowrap;
            font-size: 0;
            width: max-content;
        }

        .brand-card {
            font-size: 16px;
            display: inline-block;
            vertical-align: top;
            margin-right: 20px;
            transition: all 0.3s ease;
        }

        .brand-carousel:hover {
            animation-play-state: paused;
        }

        @keyframes moveCards {
            0% {
                transform: translateX(0%);
            }

            100% {
                transform: translateX(-100%);
            }
        }

        .brand-card {
            position: relative;
            display: inline-block;
            margin: 10px;
            vertical-align: middle;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
            transition: all 0.3s ease;
        }

        .brand-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        }

        .brand-card img {
            width: 100px;
            height: 100px;
            object-fit: contain;
            margin-bottom: 20px;
        }

        .connect-button,
        .website-button {
            position: absolute;
            bottom: 10px;
            left: 50%;
            transform: translateX(-50%);
            color: #fff;
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            opacity: 0;
            transition: opacity 0.3s ease, transform 0.3s ease;
        }

        .brand-card:hover .connect-button,
        .brand-card:hover .website-button {
            opacity: 1;
        }

        .website-button {
            bottom: 45px;
        }

        .api-section {
            border-left: 4px solid #3498db;
            transition: all 0.3s ease;
        }

        .api-section:hover {
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        </style>
        <div class="row justify-content-center py-4">
            <div class="col-12 text-center mb-3">
                <h4 class="fw-bold"><i class="fa-solid fa-boxes-packing text-primary me-2"></i>Nhà cung cấp API gợi ý</h4>
                <p class="text-muted">Kết nối nhanh với các nhà cung cấp API đáng tin cậy</p>
            </div>
            <div class="brand-carousel-container">
                <div class="brand-carousel animated-carousel">

                </div>
            </div>
            <div class="mt-3 text-center" id="notitcation_suppliers"></div>
        </div>
        <script>
        $(document).ready(function() {
            $('.brand-carousel').html('');
            $.ajax({
                url: 'https://api.cmsnt.co/suppliers.php',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    // Xử lý dữ liệu trả về từ server
                    if (response && response.suppliers.length > 0) {
                        var html = '';
                        $.each(response.suppliers, function(index, brand) {
                            html += '<div class="brand-card">';
                            html += '<img src="' + brand.logo + '" alt="Logo" class="mb-2">';
                            html +=
                                '<a href="<?=base_url_admin("suppliers-add");?>&domain=' +
                                brand.domain + '&type=' + brand.type +
                                '" class="connect-button btn btn-sm btn-danger">Kết nối</a>';
                            html += '<a href="' + brand.domain +
                                '?utm_source=ads_cmsnt" target="_blank" class="website-button btn btn-sm btn-primary">Xem</a>';
                            html += '</div>';
                        });
                        $('.brand-carousel').html(html);
                        $('#notitcation_suppliers').html(response.notication);
                        calculateAndSetAnimationDuration();
                    } else {
                        $('.brand-carousel').html('');
                    }
                },
                error: function() {
                    $('.brand-carousel').html('');
                }
            });
        });
        // Function to calculate carousel width and set animation duration
        function calculateAndSetAnimationDuration() {
            var carousel = $('.animated-carousel');
            var carouselWidth = carousel[0].scrollWidth;
            var cardWidth = carousel.children().first().outerWidth(true); // Including margin
            var numberOfCards = carouselWidth / cardWidth;
            var animationDuration = numberOfCards * 2; // Adjust this multiplier as needed
            carousel.css('animation-duration', animationDuration + 's');
        }
        </script> -->
         
    </div>
</div>


<?php
require_once(__DIR__.'/footer.php');
?>

<script>
function updateForm(id) {
    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'updateTableProductAPI',
            id: id,
            status: $('#status' + id + ':checked').val()
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, result.status);
            } else {
                showMessage(result.msg, result.status);
            }
        },
        error: function() {
            alert(html(result));
            location.reload();
        }
    });
}




var lightboxVideo = GLightbox({
    selector: '.glightbox'
});
</script>



<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger-transparent">
                <h5 class="modal-title text-danger" id="confirmDeleteModalLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <?=__('Xác nhận xóa nhà cung cấp');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-warning">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-exclamation-triangle text-warning me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Cảnh báo quan trọng!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ không thể hoàn tác.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện các hành động sau:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-danger me-2"></i>
                            <?=__('Xóa API này khỏi hệ thống');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-danger me-2"></i>
                            <?=__('Xóa tất cả chuyên mục và dịch vụ của API này');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-danger me-2"></i>
                            <?=__('Xóa thống kê lợi nhuận của API này');?>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmCheckbox">
                        <?=__('Tôi hiểu rủi ro và đồng ý xóa nhà cung cấp này');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" class="btn btn-danger btn-wave" id="confirmDeleteButton" disabled>
                    <i class="fa fa-trash me-1"></i><?=__('Xóa nhà cung cấp');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa categories và services -->
<div class="modal fade" id="confirmDeleteCategoriesServicesModal" tabindex="-1" aria-labelledby="confirmDeleteCategoriesServicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger-transparent">
                <h5 class="modal-title text-danger" id="confirmDeleteCategoriesServicesModalLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <?=__('Xác nhận xóa chuyên mục và dịch vụ');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-warning">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-exclamation-triangle text-warning me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Cảnh báo quan trọng!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ không thể hoàn tác.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện các hành động sau:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-danger me-2"></i>
                            <span id="categoriesText"><?=sprintf(__('Xóa tất cả %s chuyên mục'), '<strong class="text-primary">0</strong>');?></span>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-check text-danger me-2"></i>
                            <span id="servicesText"><?=sprintf(__('Xóa tất cả %s dịch vụ'), '<strong class="text-success">0</strong>');?></span>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-info-circle text-info me-2"></i>
                            <?=sprintf(__('Supplier sẽ được %s'), '<strong class="text-info">' . __('giữ lại') . '</strong>');?>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmCategoriesServicesCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmCategoriesServicesCheckbox">
                        <?=__('Tôi hiểu rủi ro và đồng ý xóa tất cả chuyên mục và dịch vụ');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" class="btn btn-danger btn-wave" id="confirmDeleteCategoriesServicesButton" disabled>
                    <i class="fa fa-trash me-1"></i><?=__('Xóa chuyên mục & dịch vụ');?>
                </button>
            </div>
        </div>
    </div>
</div>
<script>
function removeItem(id) {
    $('#confirmDeleteModal').modal('show');

    $('#confirmDeleteButton').off('click').on('click', function() {
        if ($('#confirmCheckbox').prop('checked')) {
            $('#confirmDeleteButton').html('<i class="fa fa-spinner fa-spin"></i> <?=__('Processing');?>...').prop(
                'disabled',
                true);
            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    action: 'removeSupplier'
                },
                success: function(result) {
                    if (result.status == 'success') {
                        Swal.fire({
                            title: "<?=__('Thành công!');?>",
                            text: result.msg,
                            icon: "success"
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 1000);
                    } else {
                        showMessage(result.msg, result.status);
                        $('#confirmDeleteButton').html('<?=__('Delete');?>').prop(
                            'disabled',
                            false);
                    }
                },
                error: function() {
                    alert(html(result));
                    location.reload();
                }
            });
        }
    });

    $('#confirmCheckbox').off('change').on('change', function() {
        if ($(this).prop('checked')) {
            $('#confirmDeleteButton').prop('disabled', false);
        } else {
            $('#confirmDeleteButton').prop('disabled', true);
        }
    });
}

// Function mới để xóa categories và services của supplier
function removeCategoriesServices(supplierId, categoriesCount, servicesCount) {
    console.log('Supplier ID:', supplierId, 'Categories:', categoriesCount, 'Services:', servicesCount); // Debug log
    
    // Sử dụng sprintf pattern để format text
    var categoriesText = '<?=__('Xóa tất cả %s chuyên mục');?>'.replace('%s', '<strong class="text-primary">' + (categoriesCount || 0) + '</strong>');
    var servicesText = '<?=__('Xóa tất cả %s dịch vụ');?>'.replace('%s', '<strong class="text-success">' + (servicesCount || 0) + '</strong>');
    
    // Cập nhật nội dung trong modal
    $('#categoriesText').html(categoriesText);
    $('#servicesText').html(servicesText);
    
    // Hiển thị modal
    $('#confirmDeleteCategoriesServicesModal').modal('show');
    
    // Reset checkbox và button
    $('#confirmCategoriesServicesCheckbox').prop('checked', false);
    $('#confirmDeleteCategoriesServicesButton').prop('disabled', true);

    // Xử lý sự kiện click nút xác nhận
    $('#confirmDeleteCategoriesServicesButton').off('click').on('click', function() {
        if ($('#confirmCategoriesServicesCheckbox').prop('checked')) {
            $(this).html('<i class="fa fa-spinner fa-spin"></i> <?=__('Đang xử lý');?>...').prop('disabled', true);
            
            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    supplier_id: supplierId,
                    action: 'removeCategoriesServices'
                },
                success: function(result) {
                    if (result.status == 'success') {
                        $('#confirmDeleteCategoriesServicesModal').modal('hide');
                        Swal.fire({
                            title: "<?=__('Thành công!');?>",
                            text: result.msg,
                            icon: "success",
                            timer: 2000,
                            showConfirmButton: false
                        });
                        setTimeout(function() {
                            location.reload();
                        }, 2000);
                    } else {
                        showMessage(result.msg, result.status);
                        $('#confirmDeleteCategoriesServicesButton').html('<i class="fa fa-trash me-1"></i><?=__('Xóa chuyên mục & dịch vụ');?>').prop('disabled', false);
                    }
                },
                error: function() {
                    showMessage('<?=__('Đã xảy ra lỗi khi xử lý yêu cầu');?>', 'error');
                    $('#confirmDeleteCategoriesServicesButton').html('<i class="fa fa-trash me-1"></i><?=__('Xóa chuyên mục & dịch vụ');?>').prop('disabled', false);
                }
            });
        }
    });

    // Xử lý sự kiện thay đổi checkbox
    $('#confirmCategoriesServicesCheckbox').off('change').on('change', function() {
        if ($(this).prop('checked')) {
            $('#confirmDeleteCategoriesServicesButton').prop('disabled', false);
        } else {
            $('#confirmDeleteCategoriesServicesButton').prop('disabled', true);
        }
    });
}
</script>
</script>