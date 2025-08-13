<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Quản lý API nhà cung cấp'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<style>
    .stats-card {
        border-radius: 12px;
        border: none;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .stats-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 20px rgba(0,0,0,0.12);
    }
    .stats-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        color: white;
    }
    .chart-container {
        min-height: 300px;
    }
    .top-products-table {
        font-size: 14px;
    }
    .top-products-table .badge {
        font-size: 11px;
    }
    
    /* Product Cards Styling */
    .product-card {
        transition: all 0.3s ease;
        border-radius: 12px;
    }
    .product-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
    }
    
    .ranking-badge {
        width: 45px;
        height: 45px;
        border-radius: 8px;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: relative;
    }
    .ranking-badge .ranking-number {
        font-size: 9px;
        font-weight: 600;
        margin-top: 1px;
    }
    
    .metric-box {
        padding: 8px;
        border-radius: 6px;
        background: rgba(var(--bs-light-rgb), 0.5);
        transition: all 0.2s ease;
    }
    .metric-box:hover {
        background: rgba(var(--bs-light-rgb), 0.8);
    }
    
    .metric-icon {
        width: 28px;
        height: 28px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 6px;
        font-size: 14px;
    }
    
    .metric-value {
        font-size: 16px;
        font-weight: 700;
        line-height: 1.2;
    }
    
    .financial-metrics {
        background: rgba(var(--bs-light-rgb), 0.3);
        padding: 10px;
        border-radius: 6px;
    }
    
    .margin-progress .progress {
        border-radius: 3px;
        overflow: hidden;
    }
    .margin-progress .progress-bar {
        transition: width 0.6s ease;
    }
    
    .performance-badges .badge {
        font-size: 11px;
        padding: 4px 8px;
    }
    
    .product-name {
        color: var(--bs-dark);
        font-size: 13px;
        line-height: 1.3;
        max-width: 150px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    
    /* Card body compact */
    .product-card .card-body {
        padding: 12px;
    }
    
    /* View Toggle Buttons */
    .btn-list .btn {
        border-radius: 6px;
        padding: 6px 10px;
    }
    
    /* Animation for view switching */
    #cardsView, #tableView {
        transition: all 0.3s ease;
    }
    
    /* Responsive improvements */
    @media (max-width: 768px) {
        .ranking-badge {
            width: 40px;
            height: 40px;
        }
        .metric-value {
            font-size: 14px;
        }
        .product-name {
            max-width: 120px;
            font-size: 12px;
        }
        .product-card .card-body {
            padding: 10px;
        }
        .financial-metrics {
            padding: 8px;
        }
    }
    
    /* Compact spacing for cards */
    .product-card .row.g-3 {
        --bs-gutter-x: 0.75rem;
        --bs-gutter-y: 0.75rem;
    }
    
    .product-card .financial-metrics .d-flex {
        margin-bottom: 8px !important;
    }
    
    .product-card .financial-metrics .d-flex:last-of-type {
        margin-bottom: 12px !important;
    }
    
    /* Chart loading styles */
    .chart-loading {
        display: flex;
        justify-content: center;
        align-items: center;
        height: 300px;
        flex-direction: column;
    }
    
    .chart-loading .spinner-border {
        margin-bottom: 1rem;
    }
</style>
';
$body['footer'] = '
 
';
require_once(__DIR__.'/../../models/is_admin.php');
 
if (isset($_GET['id'])) {
    $id = check_string($_GET['id']);
    if (!$supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '$id' ")) {
        redirect(base_url_admin('suppliers'));
    }
} else {
    redirect(base_url_admin('suppliers'));
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
if(checkPermission($getUser['admin'], 'manager_suppliers') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}

// Lấy dữ liệu thống kê
$supplier_id = $supplier['id'];

// Thống kê tổng quan
$total_orders = $CMSNT->num_rows("SELECT * FROM `orders` WHERE `supplier_id` = '$supplier_id'");
$total_revenue = $CMSNT->get_row("SELECT SUM(pay) as total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND `status` IN ('Completed', 'In progress', 'Processing')")['total'] ?? 0;
$total_cost = $CMSNT->get_row("SELECT SUM(cost) as total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND `status` IN ('Completed', 'In progress', 'Processing')")['total'] ?? 0;
$total_profit = $total_revenue - $total_cost;

// Thống kê 30 ngày qua
$date_30_days_ago = date('Y-m-d', strtotime('-30 days'));
$orders_30_days = $CMSNT->num_rows("SELECT * FROM `orders` WHERE `supplier_id` = '$supplier_id' AND `created_at` >= '$date_30_days_ago'");
$revenue_30_days = $CMSNT->get_row("SELECT SUM(pay) as total FROM `orders` WHERE `supplier_id` = '$supplier_id' AND `created_at` >= '$date_30_days_ago' AND `status` IN ('Completed', 'In progress', 'Processing')")['total'] ?? 0;

// Dữ liệu biểu đồ doanh thu sẽ được load bằng AJAX

// Thống kê trạng thái đơn hàng
$status_stats = [];
$status_list = ['Pending', 'In progress', 'Processing', 'Completed', 'Canceled', 'Partial'];
foreach($status_list as $status) {
    $count = $CMSNT->num_rows("SELECT * FROM `orders` WHERE `supplier_id` = '$supplier_id' AND `status` = '$status'");
    if($count > 0) {
        $status_stats[] = [
            'status' => $status,
            'count' => $count,
            'label' => $config_status_order[$status] ?? $status
        ];
    }
}

// Top 9 sản phẩm bán chạy
$top_products = $CMSNT->get_list("
    SELECT 
        service_id,
        service_name,
        COUNT(*) as total_orders,
        SUM(pay) as total_revenue,
        SUM(cost) as total_cost,
        SUM(quantity) as total_quantity
    FROM `orders` 
    WHERE `supplier_id` = '$supplier_id' AND `status` IN ('Completed', 'In progress', 'Processing')
    GROUP BY service_id 
    ORDER BY total_orders DESC 
    LIMIT 9
");

// Dữ liệu biểu đồ doanh thu vs lợi nhuận sẽ được load bằng AJAX

?>



<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button"
                    class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1"
                    href="<?=base_url_admin('suppliers');?>"><i class="fa-solid fa-arrow-left"></i></a>
                <?=__('Quản lý API nhà cung cấp');?>
                <?=$supplier['domain'];?>
            </h1>
        </div>

        <!-- Thống kê tổng quan -->
        <div class="row mb-4">
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-primary me-3">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 text-muted fs-13"><?=__('Tổng đơn hàng');?></p>
                                <h4 class="mb-0 text-dark fw-semibold"><?=format_cash($total_orders);?></h4>
                                <small class="text-muted"><?=format_cash($orders_30_days);?> <?=__('trong 30 ngày');?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-success me-3">
                                <i class="fa-solid fa-dollar-sign"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 text-muted fs-13"><?=__('Tổng doanh thu');?></p>
                                <h4 class="mb-0 text-dark fw-semibold"><?=format_currency($total_revenue);?></h4>
                                <small class="text-muted"><?=format_currency($revenue_30_days);?> <?=__('trong 30 ngày');?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-warning me-3">
                                <i class="fa-solid fa-coins"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 text-muted fs-13"><?=__('Chi phí');?></p>
                                <h4 class="mb-0 text-dark fw-semibold"><?=format_currency($total_cost);?></h4>
                                <small class="text-muted"><?=number_format(($total_cost / ($total_revenue ?: 1)) * 100, 1);?>% <?=__('của doanh thu');?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-lg-6 col-md-6 col-sm-12">
                <div class="card stats-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="stats-icon bg-info me-3">
                                <i class="fa-solid fa-chart-line"></i>
                            </div>
                            <div class="flex-1">
                                <p class="mb-1 text-muted fs-13"><?=__('Lợi nhuận');?></p>
                                <h4 class="mb-0 text-dark fw-semibold"><?=format_currency($total_profit);?></h4>
                                <small class="text-<?=$total_profit >= 0 ? 'success' : 'danger';?>"><?=number_format(($total_profit / ($total_revenue ?: 1)) * 100, 1);?>% <?=__('margin');?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts -->
        <div class="row mb-4">
            <!-- Chart doanh thu theo thời gian -->
            <div class="col-xl-8 col-lg-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex align-items-center">
                        <h5 class="card-title mb-0 text-uppercase"><?=__('Doanh thu theo thời gian');?></h5>
                        <div>
                            <select class="form-select form-select-sm" id="timeRangeSelect" onchange="loadRevenueChart()">
                                <option value="7_days"><?=__('7 ngày qua');?></option>
                                <option value="30_days"><?=__('30 ngày qua');?></option>
                                <option value="1_year"><?=__('1 năm qua');?></option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueChart" class="chart-container">
                            <div class="chart-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden"><?=__('Đang tải...');?></span>
                                </div>
                                <small class="text-muted"><?=__('Đang tải dữ liệu...');?></small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Chart trạng thái đơn hàng -->
            <div class="col-xl-4 col-lg-12">
                <div class="card custom-card">
                    <div class="card-header">
                        <h5 class="card-title mb-0 text-uppercase"><?=__('Trạng thái đơn hàng');?></h5>
                    </div>
                    <div class="card-body">
                        <div id="statusChart" class="chart-container"></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart doanh thu vs lợi nhuận -->
        <div class="row mb-4">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between d-flex align-items-center">
                        <h5 class="card-title mb-0 text-uppercase"><?=__('Doanh thu vs Lợi nhuận');?></h5>
                        <div>
                            <select class="form-select form-select-sm" id="profitTimeRangeSelect" onchange="loadRevenueProfitChart()">
                                <option value="7_days">7 ngày qua</option>
                                <option value="30_days">30 ngày qua</option>
                                <option value="1_year" selected>1 năm qua</option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <div id="revenueVsProfitChart" class="chart-container">
                            <div class="chart-loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Đang tải...</span>
                                </div>
                                <small class="text-muted">Đang tải dữ liệu...</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top sản phẩm bán chạy -->
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0 text-uppercase"><?=__('Top sản phẩm bán chạy');?></h5>
                        <div class="btn-list">
                            <button class="btn btn-sm btn-light" id="viewTypeTable">
                                <i class="fa-solid fa-table"></i>
                            </button>
                            <button class="btn btn-sm btn-primary" id="viewTypeCards">
                                <i class="fa-solid fa-grip"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <?php if(!empty($top_products)): ?>
                            <!-- Card View (Default) -->
                            <div id="cardsView">
                                <div class="row g-3">
                                    <?php foreach($top_products as $index => $product): ?>
                                        <?php 
                                        $margin = ($product['total_revenue'] > 0) ? (($product['total_revenue'] - $product['total_cost']) / $product['total_revenue']) * 100 : 0;
                                        $margin_class = $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger');
                                                                                                $ranking_colors = ['primary', 'info', 'warning', 'danger', 'secondary', 'success', 'dark', 'purple'];
                                                        $ranking_icons = ['fa-crown', 'fa-medal', 'fa-award', 'fa-star', 'fa-certificate', 'fa-trophy', 'fa-gem', 'fa-fire'];
                                        ?>
                                        <div class="col-xl-4 col-lg-6 col-md-6 col-sm-12">
                                            <div class="card border shadow-sm product-card h-100">
                                                <div class="card-body">
                                                    <!-- Header với ranking -->
                                                    <div class="d-flex justify-content-between align-items-start mb-3">
                                                        <div class="d-flex align-items-center">
                                                            <div class="ranking-badge bg-<?=$ranking_colors[$index];?>-transparent text-<?=$ranking_colors[$index];?> me-3">
                                                                <i class="fa-solid <?=$ranking_icons[$index];?> fs-20"></i>
                                                                <span class="ranking-number">#<?=$index + 1;?></span>
                                                            </div>
                                                                                                                         <div>
                                                                 <h6 class="mb-1 fw-semibold product-name" title="<?=$product['service_name'];?>"><?=$product['service_name'];?></h6>
                                                                 <span class="badge bg-light text-muted small">ID: <?=$product['service_id'];?></span>
                                                             </div>
                                                        </div>
                                                        <div class="dropdown">
                                                            <button class="btn btn-sm btn-light" data-bs-toggle="dropdown">
                                                                <i class="fa-solid fa-ellipsis-vertical"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end">
                                                                <li><a class="dropdown-item" href="<?=base_url_admin('service-edit&id='.$product['service_id']);?>">
                                                                    <i class="fa-solid fa-edit me-2"></i><?=__('Chỉnh sửa');?>
                                                                </a></li>
                                                                <li><a class="dropdown-item" href="<?=base_url_admin('orders&product_id='.$product['service_id']);?>">
                                                                    <i class="fa-solid fa-list me-2"></i><?=__('Xem đơn hàng');?>
                                                                </a></li>
                                                            </ul>
                                                        </div>
                                                    </div>

                                                    <!-- Metrics Grid -->
                                                    <div class="row g-3 mb-3">
                                                        <div class="col-6">
                                                            <div class="metric-box text-center">
                                                                <div class="metric-icon bg-primary-transparent text-primary">
                                                                    <i class="fa-solid fa-shopping-cart"></i>
                                                                </div>
                                                                <h4 class="metric-value text-primary mb-0"><?=format_cash($product['total_orders']);?></h4>
                                                                <small class="text-muted"><?=__('Đơn hàng');?></small>
                                                            </div>
                                                        </div>
                                                        <div class="col-6">
                                                            <div class="metric-box text-center">
                                                                <div class="metric-icon bg-info-transparent text-info">
                                                                    <i class="fa-solid fa-hashtag"></i>
                                                                </div>
                                                                <h4 class="metric-value text-info mb-0"><?=format_cash($product['total_quantity']);?></h4>
                                                                <small class="text-muted"><?=__('Số lượng');?></small>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Financial Metrics -->
                                                    <div class="financial-metrics">
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted"><?=__('Doanh thu');?></span>
                                                            <span class="fw-semibold text-success"><?=format_currency($product['total_revenue']);?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                                            <span class="text-muted"><?=__('Chi phí');?></span>
                                                            <span class="fw-semibold text-warning"><?=format_currency($product['total_cost']);?></span>
                                                        </div>
                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <span class="text-muted"><?=__('Lợi nhuận');?></span>
                                                            <span class="fw-semibold text-primary"><?=format_currency($product['total_revenue'] - $product['total_cost']);?></span>
                                                        </div>

                                                        <!-- Profit Margin Progress -->
                                                        <div class="margin-progress">
                                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                                <span class="small text-muted"><?=__('Tỷ suất lợi nhuận');?></span>
                                                                <span class="badge bg-<?=$margin_class;?>"><?=number_format($margin, 1);?>%</span>
                                                            </div>
                                                            <div class="progress" style="height: 6px;">
                                                                <div class="progress-bar bg-<?=$margin_class;?>" style="width: <?=min($margin, 100);?>%"></div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <!-- Performance Indicator -->
                                                    <div class="mt-3 pt-3 border-top">
                                                        <div class="d-flex justify-content-between align-items-center">
                                                            <small class="text-muted"><?=__('Hiệu suất');?></small>
                                                            <div class="performance-badges">
                                                                <?php if($margin >= 30): ?>
                                                                    <span class="badge bg-success-transparent text-success">
                                                                        <i class="fa-solid fa-arrow-trend-up me-1"></i><?=__('Xuất sắc');?>
                                                                    </span>
                                                                <?php elseif($margin >= 15): ?>
                                                                    <span class="badge bg-warning-transparent text-warning">
                                                                        <i class="fa-solid fa-minus me-1"></i><?=__('Trung bình');?>
                                                                    </span>
                                                                <?php else: ?>
                                                                    <span class="badge bg-danger-transparent text-danger">
                                                                        <i class="fa-solid fa-arrow-trend-down me-1"></i><?=__('Cần cải thiện');?>
                                                                    </span>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>

                            <!-- Table View (Hidden by default) -->
                            <div id="tableView" style="display: none;">
                                <div class="table-responsive">
                                    <table class="table table-striped top-products-table">
                                        <thead>
                                            <tr>
                                                <th><?=__('Rank');?></th>
                                                <th><?=__('Dịch vụ');?></th>
                                                <th class="text-center"><?=__('Đơn hàng');?></th>
                                                <th class="text-center"><?=__('Số lượng');?></th>
                                                <th class="text-center"><?=__('Doanh thu');?></th>
                                                <th class="text-center"><?=__('Lợi nhuận');?></th>
                                                <th class="text-center"><?=__('Margin');?></th>
                                                <th class="text-center"><?=__('Thao tác');?></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach($top_products as $index => $product): ?>
                                                <?php 
                                                $margin = ($product['total_revenue'] > 0) ? (($product['total_revenue'] - $product['total_cost']) / $product['total_revenue']) * 100 : 0;
                                                $margin_class = $margin >= 30 ? 'success' : ($margin >= 15 ? 'warning' : 'danger');
                                                $ranking_colors = ['primary', 'info', 'warning', 'danger', 'secondary', 'success', 'dark', 'purple'];
                                                ?>
                                                <tr>
                                                    <td>
                                                        <span class="badge bg-<?=$ranking_colors[$index];?> fs-12">#<?=$index + 1;?></span>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div>
                                                                <span class="fw-semibold"><?=$product['service_name'];?></span>
                                                                <br><small class="text-muted">ID: <?=$product['service_id'];?></small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-primary-transparent text-primary"><?=format_cash($product['total_orders']);?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="fw-semibold"><?=format_cash($product['total_quantity']);?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-success fw-semibold"><?=format_currency($product['total_revenue']);?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="text-primary fw-semibold"><?=format_currency($product['total_revenue'] - $product['total_cost']);?></span>
                                                    </td>
                                                    <td class="text-center">
                                                        <span class="badge bg-<?=$margin_class;?>"><?=number_format($margin, 1);?>%</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <div class="btn-list">
                                                            <a href="<?=base_url_admin('service-edit&id='.$product['service_id']);?>" class="btn btn-sm btn-primary">
                                                                <i class="fa-solid fa-edit"></i>
                                                            </a>
                                                            <a href="<?=base_url_admin('orders&product_id='.$product['service_id']);?>" class="btn btn-sm btn-info">
                                                                <i class="fa-solid fa-list"></i>
                                                            </a>
                                                        </div>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-5">
                                <i class="fa-solid fa-chart-simple fs-48 mb-3 opacity-50"></i>
                                <h5 class="text-muted"><?=__('Chưa có dữ liệu sản phẩm');?></h5>
                                <p class="text-muted"><?=__('Dữ liệu sẽ hiển thị khi có đơn hàng từ nhà cung cấp này');?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

      



    </div>

</div>
<script>
// Biến global để lưu chart instance
var revenueChart;
var revenueVsProfitChart;
var token = "<?=$getUser['token'];?>";

// Function load biểu đồ doanh thu theo AJAX
function loadRevenueChart() {
    const timeRange = document.getElementById('timeRangeSelect').value;
    const supplierId = <?=$supplier_id;?>;
    
    // Hiển thị loading
    document.getElementById('revenueChart').innerHTML = '<div class="chart-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><small class="text-muted">Đang tải dữ liệu...</small></div>';
    
    $.ajax({
        url: '<?=base_url('ajaxs/admin/view.php');?>',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'view_chart_supplier_revenue',
            supplier_id: supplierId,
            time_range: timeRange,
            token: token
        },
        success: function(response) {
            if (response.status === 'success') {
                // Hủy chart cũ nếu có
                if (revenueChart) {
                    revenueChart.destroy();
                }
                
                // Tạo biểu đồ mới
                var revenueOptions = {
                    series: [{
                        name: "<?=__('Doanh thu');?>",
                        data: response.revenues
                    }],
                    chart: {
                        type: "area",
                        height: 300,
                        toolbar: { show: false },
                        background: "transparent"
                    },
                    dataLabels: { enabled: false },
                    stroke: {
                        curve: "smooth",
                        width: 3,
                        colors: ["#007bff"]
                    },
                    fill: {
                        type: "gradient",
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.7,
                            opacityTo: 0.1,
                            stops: [0, 90, 100],
                            colorStops: [{
                                offset: 0,
                                color: "#007bff",
                                opacity: 0.4
                            }, {
                                offset: 100,
                                color: "#007bff",
                                opacity: 0.1
                            }]
                        }
                    },
                    xaxis: {
                        categories: response.labels,
                        labels: {
                            style: { fontSize: "12px" }
                        }
                    },
                    yaxis: {
                        labels: {
                            formatter: function(value) {
                                return new Intl.NumberFormat("vi-VN", {
                                    style: "currency",
                                    currency: "VND"
                                }).format(value);
                            }
                        }
                    },
                    grid: { borderColor: "#e7e7e7", strokeDashArray: 5 },
                    tooltip: {
                        y: {
                            formatter: function(value) {
                                return new Intl.NumberFormat("vi-VN", {
                                    style: "currency",
                                    currency: "VND"
                                }).format(value);
                            }
                        }
                    }
                };
                
                // Khởi tạo lại container
                document.getElementById('revenueChart').innerHTML = '';
                revenueChart = new ApexCharts(document.querySelector("#revenueChart"), revenueOptions);
                revenueChart.render();
            } else {
                document.getElementById('revenueChart').innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-solid fa-exclamation-triangle fs-48 mb-3"></i><br>' + response.msg + '</div>';
            }
        },
        error: function() {
            document.getElementById('revenueChart').innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-solid fa-exclamation-triangle fs-48 mb-3"></i><br>Lỗi khi tải dữ liệu</div>';
        }
    });
}

// Function load biểu đồ doanh thu vs lợi nhuận theo AJAX
function loadRevenueProfitChart() {
    const timeRange = document.getElementById('profitTimeRangeSelect').value;
    const supplierId = <?=$supplier_id;?>;
    
    // Hiển thị loading
    document.getElementById('revenueVsProfitChart').innerHTML = '<div class="chart-loading"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Đang tải...</span></div><small class="text-muted">Đang tải dữ liệu...</small></div>';
    
    $.ajax({
        url: '<?=base_url('ajaxs/admin/view.php');?>',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'view_chart_supplier_revenue_profit',
            supplier_id: supplierId,
            time_range: timeRange,
            token: token
        },
        success: function(response) {
            if (response.status === 'success') {
                // Hủy chart cũ nếu có
                if (revenueVsProfitChart) {
                    revenueVsProfitChart.destroy();
                }
                
                // Tạo biểu đồ mới
                var revenueVsProfitOptions = {
                    series: [{
                        name: "<?=__('Doanh thu');?>",
                        type: "column",
                        data: response.revenues
                    }, {
                        name: "<?=__('Lợi nhuận');?>",
                        type: "line",
                        data: response.profits
                    }],
                    chart: {
                        height: 350,
                        type: "line",
                        toolbar: { show: false }
                    },
                    stroke: {
                        width: [0, 4],
                        curve: "smooth"
                    },
                    plotOptions: {
                        bar: {
                            columnWidth: "50%"
                        }
                    },
                    colors: ["#28a745", "#007bff"],
                    dataLabels: { enabled: false },
                    xaxis: {
                        categories: response.labels,
                        labels: {
                            style: { fontSize: "12px" }
                        }
                    },
                    yaxis: [{
                        title: { text: "<?=__('Doanh thu (VND)');?>", },
                        labels: {
                            formatter: function(value) {
                                return new Intl.NumberFormat("vi-VN", {
                                    style: "currency",
                                    currency: "VND",
                                    minimumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    }, {
                        opposite: true,
                        title: { text: "<?=__('Lợi nhuận (VND)');?>", },
                        labels: {
                            formatter: function(value) {
                                return new Intl.NumberFormat("vi-VN", {
                                    style: "currency",
                                    currency: "VND",
                                    minimumFractionDigits: 0
                                }).format(value);
                            }
                        }
                    }],
                    tooltip: {
                        shared: false,
                        intersect: false,
                        y: {
                            formatter: function(value) {
                                return new Intl.NumberFormat("vi-VN", {
                                    style: "currency",
                                    currency: "VND"
                                }).format(value);
                            }
                        }
                    },
                    legend: {
                        position: "top",
                        horizontalAlign: "left"
                    }
                };
                
                // Khởi tạo lại container
                document.getElementById('revenueVsProfitChart').innerHTML = '';
                revenueVsProfitChart = new ApexCharts(document.querySelector("#revenueVsProfitChart"), revenueVsProfitOptions);
                revenueVsProfitChart.render();
            } else {
                document.getElementById('revenueVsProfitChart').innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-solid fa-exclamation-triangle fs-48 mb-3"></i><br>' + response.msg + '</div>';
            }
        },
        error: function() {
            document.getElementById('revenueVsProfitChart').innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-solid fa-exclamation-triangle fs-48 mb-3"></i><br>Lỗi khi tải dữ liệu</div>';
        }
    });
}

document.addEventListener("DOMContentLoaded", function() {
    // Load biểu đồ doanh thu mặc định (7 ngày)
    loadRevenueChart();
    
    // Load biểu đồ doanh thu vs lợi nhuận mặc định (1 năm)
    loadRevenueProfitChart();

    // Chart trạng thái đơn hàng
    var statusData = <?=json_encode($status_stats);?>;
    if (statusData && statusData.length > 0) {
        var statusLabels = statusData.map(function(item) { return item.label; });
        var statusCounts = statusData.map(function(item) { return item.count; });
        
        var statusOptions = {
            series: statusCounts,
            chart: {
                type: "donut",
                height: 300
            },
            labels: statusLabels,
            colors: ["#ff9800", "#2196f3", "#4caf50", "#28a745", "#dc3545", "#6f42c1"],
            dataLabels: {
                enabled: true,
                formatter: function(val) {
                    return Math.round(val) + "%";
                }
            },
            plotOptions: {
                pie: {
                    donut: {
                        size: "70%",
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: "<?=__('Tổng');?>",
                                formatter: function() {
                                    return statusCounts.reduce(function(a, b) { return a + b; }, 0);
                                }
                            }
                        }
                    }
                }
            },
            legend: {
                position: "bottom",
                fontSize: "12px"
            }
        };
        var statusChart = new ApexCharts(document.querySelector("#statusChart"), statusOptions);
        statusChart.render();
    } else {
        document.querySelector("#statusChart").innerHTML = '<div class="text-center text-muted mt-5"><i class="fa-solid fa-chart-pie fs-48 mb-3"></i><br><?=__("Chưa có dữ liệu đơn hàng");?></div>';
    }

    // Chart doanh thu vs lợi nhuận sẽ được load bằng AJAX

    // Handle view toggle for top products
    document.getElementById('viewTypeCards')?.addEventListener('click', function() {
        document.getElementById('cardsView').style.display = 'block';
        document.getElementById('tableView').style.display = 'none';
        
        // Update button states
        this.classList.remove('btn-light');
        this.classList.add('btn-primary');
        document.getElementById('viewTypeTable').classList.remove('btn-primary');
        document.getElementById('viewTypeTable').classList.add('btn-light');
    });

    document.getElementById('viewTypeTable')?.addEventListener('click', function() {
        document.getElementById('cardsView').style.display = 'none';
        document.getElementById('tableView').style.display = 'block';
        
        // Update button states
        this.classList.remove('btn-light');
        this.classList.add('btn-primary');
        document.getElementById('viewTypeCards').classList.remove('btn-primary');
        document.getElementById('viewTypeCards').classList.add('btn-light');
    });
});
</script>

<?php
require_once(__DIR__.'/footer.php');
?>