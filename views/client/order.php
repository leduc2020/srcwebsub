<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => __('Tạo đơn hàng').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
// Lấy thông tin platform từ tham số GET
$platformSlug = isset($_GET['platform']) ? check_string($_GET['platform']) : '';
// Lấy thông tin category từ tham số GET
$categorySlug = isset($_GET['category']) ? check_string($_GET['category']) : '';
// Lấy thông tin service_id từ tham số GET
$serviceId = isset($_GET['service_id']) ? check_string($_GET['service_id']) : '';

$body['header'] = '
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="'.base_url('mod/css/order.css?v=1.0.5').'">
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
';
$body['footer'] = '
<!--select2 cdn-->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
// Cấu hình và nhãn dành cho JavaScript
const ORDER_CONFIG = {
    base_url: "'.base_url().'",
    serviceIdFromUrl: "'.$serviceId.'",
    categorySlug: "'.$categorySlug.'",
    platformSlug: "'.$platformSlug.'"
};

const ORDER_LABELS = {
    supportCancel: "'.__('Hỗ trợ hủy').'",
    warranty: "'.__('Bảo hành').'",
    slowMode: "'.__('Lên chậm').'",
    error: "'.__('Lỗi').'",
    success: "'.__('Thành công').'",
    processing: "'.__('Đang xử lý').'",
    loading: "'.__('Đang tải...').'",
    selectPlatform: "'.__('Vui lòng chọn nền tảng').'",
    selectCategory: "'.__('Vui lòng chọn phân loại').'",
    noCategories: "'.__('Không có phân loại nào').'",
    noServices: "'.__('Không có dịch vụ nào').'",
    quickSearchPlaceholder: "'.__('Nhập tên dịch vụ để tìm kiếm').'",
    errorLoadData: "'.__('Không thể tải dữ liệu, vui lòng thử lại').'",
    errorLoadServiceDetails: "'.__('Lỗi kết nối, không thể tải chi tiết dịch vụ').'",
    errorCalculatePrice: "'.__('Lỗi kết nối, không thể tính tổng giá').'",
    noServiceInfo: "'.__('Dịch vụ này không có thông tin nền tảng hoặc phân loại').'",
    selectingService: "'.__('Đang chọn dịch vụ, vui lòng đợi...').'",
    serviceSelected: "'.__('Đã chọn dịch vụ thành công').'",
    pleaseSelectService: "'.__('Vui lòng chọn dịch vụ').'",
    pleaseEnterLink: "'.__('Vui lòng nhập liên kết cần tăng').'",
    pleaseEnterValidQuantity: "'.__('Vui lòng nhập số lượng hợp lệ').'",
    minQuantity: "'.__('Số lượng tối thiểu là').'",
    maxQuantity: "'.__('Số lượng tối đa là').'",
    pleaseSelectScheduleTime: "'.__('Vui lòng chọn thời gian cho đơn hàng đặt lịch').'",
    yes: "'.__('Có').'",
    no: "'.__('Không').'",
    confirmOrder: "'.__('Xác nhận đặt hàng').'",
    ok: "'.__('OK').'",
    cancel: "'.__('Hủy').'",
    loginRequired: "'.__('Vui lòng đăng nhập để sử dụng tính năng này').'",
    connectionError: "'.__('Có lỗi xảy ra khi kết nối đến máy chủ').'",
    notification: "'.__('Thông báo').'",
    scheduleTimeWarning: "'.__('Thời gian đặt lịch phải sau thời gian hiện tại ít nhất 5 phút').'",
    weekdaysShort: ["'.__('CN').'", "'.__('T2').'", "'.__('T3').'", "'.__('T4').'", "'.__('T5').'", "'.__('T6').'", "'.__('T7').'"],
    weekdaysLong: ["'.__('Chủ Nhật').'", "'.__('Thứ 2').'", "'.__('Thứ 3').'", "'.__('Thứ 4').'", "'.__('Thứ 5').'", "'.__('Thứ 6').'", "'.__('Thứ 7').'"],
    monthsShort: ["'.__('Th1').'", "'.__('Th2').'", "'.__('Th3').'", "'.__('Th4').'", "'.__('Th5').'", "'.__('Th6').'", "'.__('Th7').'", "'.__('Th8').'", "'.__('Th9').'", "'.__('Th10').'", "'.__('Th11').'", "'.__('Th12').'"],
    monthsLong: ["'.__('Tháng 1').'", "'.__('Tháng 2').'", "'.__('Tháng 3').'", "'.__('Tháng 4').'", "'.__('Tháng 5').'", "'.__('Tháng 6').'", "'.__('Tháng 7').'", "'.__('Tháng 8').'", "'.__('Tháng 9').'", "'.__('Tháng 10').'", "'.__('Tháng 11').'", "'.__('Tháng 12').'"],
    // Multi-order labels
    waiting: "'.__('Chờ xử lý').'",
    processing: "'.__('Đang xử lý').'",
    completed: "'.__('Hoàn thành').'",
    failed: "'.__('Thất bại').'",
    paused: "'.__('Đã tạm dừng').'",
    stopped: "'.__('Đã dừng').'",
    enterLinks: "'.__('Vui lòng nhập danh sách liên kết').'",
    invalidDelay: "'.__('Thời gian chờ phải lớn hơn 0.1 giây').'",
    confirmStartMulti: "'.__('Bạn có chắc chắn muốn bắt đầu mua {count} đơn hàng?').'",
    confirmStopMulti: "'.__('Bạn có chắc chắn muốn dừng tiến trình mua đơn?').'",
    multiOrderStarted: "'.__('Đã bắt đầu tiến trình mua nhiều đơn hàng').'",
    multiOrderPaused: "'.__('Đã tạm dừng tiến trình').'",
    multiOrderResumed: "'.__('Đã tiếp tục tiến trình').'",
    multiOrderStopped: "'.__('Đã dừng tiến trình mua đơn').'",
    multiOrderCompleted: "'.__('Đã hoàn thành tất cả đơn hàng').'",
    orderCreated: "'.__('Tạo đơn thành công').'",
    orderFailed: "'.__('Tạo đơn thất bại').'",
    balanceInsufficient: "'.__('Số dư không đủ').'",
    serviceNotSelected: "'.__('Vui lòng chọn dịch vụ trước khi mua nhiều đơn').'",
    invalidQuantity: "'.__('Số lượng không hợp lệ').'",
    commentRequired: "'.__('Vui lòng nhập comment cho dịch vụ này').'",
    processingOrder: "'.__('Đang xử lý đơn hàng...').'",
    orderNumber: "'.__('Đơn số').'",
    totalPrice: "'.__('Tổng tiền:').'",
    currentBalance: "'.__('Số dư hiện tại:').'",
    remainingBalance: "'.__('Số dư còn lại:').'",
    confirmClearAll: "'.__('Bạn có chắc chắn muốn xóa tất cả đơn hàng?').'",
    orderProcessingWarning: "'.__('Đơn hàng đang được xử lý. Nếu bạn tắt trang web, bạn có thể mất tiền. Bạn có chắc chắn muốn rời khỏi trang?').'",
    orderProcessingTitle: "'.__('Đang xử lý đơn hàng').'",
    orderProcessingMessage: "'.__('Vui lòng không tắt trang web trong quá trình xử lý để tránh mất tiền').'",
    autoQuantityMessage: "'.__('Số lượng sẽ được tính tự động theo số dòng comment').'",
    fixedQuantityMessage: "'.__('Dịch vụ này có số lượng cố định = 1').'",
    minLabel: "'.__('Tối thiểu').'",
    maxLabel: "'.__('Tối đa').'",
    allOrdersProcessed: "'.__('Tất cả đơn hàng đã được xử lý. Vui lòng làm mới danh sách để bắt đầu lại.').'",
    // Clipboard labels
    pasteButtonText: "'.__('Dán').'",
    pasteSuccess: "'.__('Đã dán liên kết thành công!').'",
    pasteEmpty: "'.__('Clipboard trống hoặc không có nội dung hợp lệ.').'",
    pastePermissionDenied: "'.__('Không có quyền truy cập clipboard. Vui lòng cấp quyền hoặc dán thủ công (Ctrl+V).').'",
    pasteNotFound: "'.__('Clipboard trống hoặc không có nội dung text.').'",
    pasteError: "'.__('Không thể truy cập clipboard. Vui lòng dán thủ công (Ctrl+V).').'"
};
</script>
<script src="'.base_url('mod/js/order.js?v=1.0.7').'"></script>
';

if($CMSNT->site('isLoginRequiredToViewProduct') == 1) {
    require_once(__DIR__ . '/../../models/is_user.php');
}else{
    if (isSecureCookie('user_login') == true) {
        require_once(__DIR__ . '/../../models/is_user.php');
    }
}

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
                        <h4 class="mb-sm-0"><?=__('Đặt hàng dịch vụ');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="#"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Đặt hàng dịch vụ');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <div class="row">
                <?php if($CMSNT->site('notice_home') != ''):?>
                <div class="col-xl-12">
                    <!-- Secondary Alert -->
                    <div class="alert alert-secondary alert-dismissible border-2 bg-body-secondary fade show material-shadow"
                        role="alert">
                        <?=$CMSNT->site('notice_home');?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
                <?php endif;?>

                <?php if(empty($getUser['telegram_chat_id'])):?>
                <div class="col-xl-12" id="telegramNoticeCard" style="display: none;">
                    <div class="card overflow-hidden">
                        <div class="card-body bg-warning-subtle d-flex">
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-start">
                                    <i class="fab fa-telegram-plane fa-lg me-3 mt-1 text-warning"
                                        aria-hidden="true"></i>
                                    <div>
                                        <h5 class="alert-heading mb-2" style="font-weight: 600;"><i
                                                class="ri-telegram-fill me-2 text-warning"></i><?=__('Liên kết Telegram');?>
                                        </h5>
                                        <div class="mb-2">
                                            <p class="mb-2">
                                                <?=__('Bạn chưa liên kết tài khoản Telegram để nhận thông báo quan trọng về đơn hàng và tài khoản.');?>
                                            </p>
                                        </div>
                                        <a href="<?=base_url('client/profile#security');?>"
                                            class="btn btn-warning btn-sm">
                                            <i class="fab fa-telegram-plane me-1"></i><?=__('Liên kết ngay');?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <div class="flex-shrink-0 align-self-center">
                                <button type="button" class="btn-close" onclick="hideTelegramNotice();"
                                    aria-label="Close"></button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif;?>
                <div class="col-xl-7" <?=$aos['fade-up'];?>>
                    <div class="card border-0 shadow-sm">
                        <!-- <div class="card-header bg-primary bg-soft py-3">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <img src="<?=base_url('assets/img/icon-order.webp');?>"
                                        alt="<?=__('Đặt hàng dịch vụ');?>" class="icon-card">
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h4 class="card-title mb-0 text-white"><?=__('Đặt hàng dịch vụ');?></h4>
                                    <p class="text-white mb-0 mt-1"><?=__('Chọn dịch vụ cần mua và đặt hàng');?></p>
                                </div>
                            </div>
                        </div> -->
                        <div class="card-body">
                            <div class="form-container">
                                <div class="form-loader">
                                    <div class="text-center">
                                        <div class="spinner-border" role="status">
                                            <span class="visually-hidden"><?=__('Đang tải...');?></span>
                                        </div>
                                        <div class="loading-text"><?=__('Đang tải dữ liệu...');?></div>
                                    </div>
                                </div>
                                <form id="order-form" onsubmit="submitOrder(); return false;" novalidate>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium"><?=__('Tìm nhanh dịch vụ');?></label>
                                        <input type="text" class="form-control"
                                            placeholder="<?=__('Nhập tên dịch vụ để tìm kiếm nhanh');?>"
                                            id="quick-search-service" name="quick_search_service">
                                        <div class="form-text">
                                            <?=__('Nhập tên hoặc ID dịch vụ để tìm kiếm nhanh và tự động chọn');?></div>
                                    </div>
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-medium"><?=__('Nền tảng');?></label>
                                            <select class="form-select select2-platform" id="platform" name="platform">
                                                <?php foreach($CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC") as $category):?>
                                                <option value="<?=$category['id'];?>"
                                                    data-image="<?=base_url($category['icon']);?>"
                                                    <?=$platformSlug == $category['slug'] ? 'selected' : '';?>>
                                                    <?=$category['name'];?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label class="form-label fw-medium"><?=__('Phân loại');?></label>
                                            <select class="form-select select2-platform" id="category" name="category">
                                            </select>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium"><?=__('Dịch vụ');?></label>
                                        <select class="form-select select2-platform" id="service" name="service">
                                        </select>
                                    </div> 
                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2"> 
                                            <label
                                                class="form-label fw-medium mb-0"><?=__('Liên kết cần tăng');?></label>
                                            <?php if($CMSNT->site('isMultipleOrder') == 1):?>
                                            <small class="text-muted">
                                                <a href="javascript:void(0)"
                                                    onclick="showMultiLinkModal();"><?=__('Bạn muốn mua 1 lần nhiều link?');?></a>
                                            </small>
                                            <?php endif;?>
                                        </div>
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="link" name="link" required 
                                                placeholder="<?=__('Nhập liên kết cần tăng tương tác...');?>">
                                            <button type="button" class="btn btn-dark" id="pasteFromClipboard" 
                                                title="<?=__('Dán liên kết từ clipboard');?>">
                                                <i class="ri-clipboard-line me-1"></i><?=__('Dán');?>
                                            </button>
                                        </div>
                                        <input type="hidden" class="form-control" id="token" name="token"
                                            value="<?=isset($getUser) ? $getUser['token'] : '';?>" disabled>
                                        <input type="hidden" class="form-control" id="api_key" name="api_key"
                                            value="<?=isset($getUser) ? $getUser['api_key'] : '';?>" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label fw-medium"><?=__('Số lượng');?></label>
                                        <input type="number" class="form-control" id="quantity" name="quantity"
                                            value="1" min="100" max="4000" required>
                                        <div class="form-text">
                                            <?=__('Tối thiểu:').' <span id="min-quantity"></span> - '.__('Tối đa:').' <span id="max-quantity"></span>';?>
                                        </div>
                                    </div>
                                    <div class="mb-3" id="comment-container" style="display: none;">
                                        <label
                                            class="form-label fw-medium"><?=__('Nhập mỗi dòng là một bình luận');?></label>
                                        <textarea class="form-control" id="comments" name="comments"
                                            rows="6"></textarea>
                                    </div>
                                    <div class="mb-3">
                                        <!-- Price Summary Container -->
                                        <div
                                            class="border border-dashed border-primary rounded p-3 bg-light bg-opacity-25">
                                            <!-- Giá trị đơn hàng -->
                                            <div class="d-flex align-items-center justify-content-between mb-2"
                                                id="price-detail-row" style="display: none;">
                                                <span class="text-muted fs-6">
                                                    <?=__('Giá trị đơn hàng:');?>
                                                </span>
                                                <span class="text-muted fs-6">
                                                    <span id="price">0</span>
                                                </span>
                                            </div>
                                            <!-- Thuế VAT - nhỏ hơn -->
                                            <div class="d-flex align-items-center justify-content-between mb-2"
                                                id="tax-detail-row" style="display: none;">
                                                <span class="text-muted fs-6">
                                                    <?=__('Thuế VAT:');?>
                                                </span>
                                                <span class="text-muted fs-6">
                                                    <span id="price-vat">0</span> (<span id="tax-vat">0</span>%)
                                                </span>
                                            </div>

                                            <!-- Separator line khi có thuế -->
                                            <hr class="my-2 border-dashed" id="price-separator" style="display: none;">

                                            <!-- Tổng tiền - nổi bật -->
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-bold fs-5">
                                                    <?=__('Tổng tiền cần thanh toán:');?>
                                                </span>
                                                <span class="fw-bold fs-4">
                                                    <span id="total-price">0</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="form-check form-switch mb-2"
                                        <?=$CMSNT->site('status_scheduled_orders') != 1 ? 'style="display: none;"' : '';?>>
                                        <input class="form-check-input" type="checkbox" id="schedule" name="schedule">
                                        <label class="form-check-label" for="schedule"><?=__('Đặt lịch chạy');?>.
                                            <?=__('Múi giờ:');?> <?=$CMSNT->site('timezone');?></label>
                                    </div>
                                    <div id="schedule-time-container" class="mt-2" style="display: none;">
                                        <div class="input-group">
                                            <input type="text" class="form-control" id="schedule-time"
                                                name="schedule_time" min=""
                                                placeholder="<?=__('Nhấn để chọn ngày giờ');?>">
                                            <span class="input-group-text">
                                                <i class="ri-calendar-2-line"></i>
                                            </span>
                                        </div>
                                        <div class="form-text"><?=__('Chọn thời gian để đặt lịch chạy đơn hàng.');?> <a
                                                href="<?=base_url('client/scheduled-orders');?>"
                                                class="link-secondary fw-semibold"><?=__('Xem đơn hàng đang lên lịch');?></a>
                                        </div>
                                    </div>
                                    <div class="mb-4"></div>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-primary"><?=__('Đặt hàng');?></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-5" <?=$aos['fade-left'];?>>
                    <?php if($CMSNT->site('data-order-block-user-info') == 1):?>
                    <?php require_once __DIR__.'/block-info-user.php';?>
                    <?php endif;?>
                    <div class="card border-0 shadow-sm mb-3" id="serviceDetailsCard">
                        <div class="card-body">
                            <div class="service-details-container">
                                <!-- Loading State -->
                                <div class="text-center py-4" id="serviceDetailsLoading">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden"><?=__('Đang tải...');?></span>
                                    </div>
                                    <!-- <div class="mt-2 text-muted"><?=__('Đang tải chi tiết dịch vụ...');?></div> -->
                                </div>

                                <!-- Service Details Content -->
                                <div id="serviceDetailsContent" style="display: none;">
                                    <!-- Service ID và Name -->
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <span class="text-muted small"><?=__('ID dịch vụ');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="fw-medium text-info" id="serviceDetailId">-</span>
                                        </div>
                                    </div>

                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <span class="text-muted small"><?=__('Tên dịch vụ');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="fw-medium" id="serviceDetailName">-</span>
                                        </div>
                                    </div>

                                    <!-- Service Type -->
                                    <div class="row mb-3" id="serviceTypeRow" style="display: none;">
                                        <div class="col-4">
                                            <span class="text-muted small"><?=__('Loại dịch vụ');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="badge bg-secondary" id="serviceDetailType">-</span>
                                        </div>
                                    </div>

                                    <!-- Average Time -->
                                    <div class="row mb-3" id="averageTimeRow" style="display: none;">
                                        <div class="col-4">
                                            <span class="text-muted small" data-toggle="tooltip" data-placement="bottom"
                                                title="<?=__('Trung bình thời gian hoàn thành của 10 đơn hàng có số lượng 1.000');?>"><?=__('Thời gian hoàn thành');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="text-success fw-medium" id="serviceAverageTime">-</span>
                                        </div>
                                    </div>

                                    <!-- Min/Max Quantity -->
                                    <div class="row mb-3">
                                        <div class="col-4">
                                            <span class="text-muted small"><?=__('Giới hạn số lượng');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="text-primary" id="serviceQuantityRange">-</span>
                                        </div>
                                    </div>

                                    <!-- Price -->
                                    <div class="row">
                                        <div class="col-4">
                                            <span class="text-muted small"><?=__('Giá mỗi 1000');?>:</span>
                                        </div>
                                        <div class="col-8">
                                            <span class="text-danger fw-bold" id="serviceDetailPrice">-</span>
                                        </div>
                                    </div>


                                    <!-- Description -->
                                    <div class="service-description my-3" id="serviceDetailDescription"
                                        style="display: none;">
                                        <div class="mb-2">
                                            <span class="text-muted small"><?=__('Mô tả dịch vụ');?>:</span>
                                        </div>
                                        <div class="description-content p-3 bg-light rounded">
                                            <div id="serviceDescriptionContent"></div>
                                        </div>
                                    </div>
                                </div> <!-- End serviceDetailsContent -->
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

<!-- Modal Mua nhiều đơn hàng -->
<div class="modal fade" id="multiOrderModal" tabindex="-1" aria-labelledby="multiOrderModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="multiOrderModalLabel">
                    <i class="ri-shopping-cart-2-line me-2"></i><?=__('Mua nhiều đơn hàng cùng lúc');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">


                <div class="row">
                    <!-- Cột trái: Form nhập thông tin -->
                    <div class="col-md-6">
                        <div class="card border-0 bg-light">
                            <div class="card-body">
                                <!-- Thông tin dịch vụ đang chọn -->
                                <div class="card border-primary border-opacity-25 bg-primary-subtle mb-3"
                                    id="multiServiceInfo" style="display: none;">
                                    <div class="card-body py-3">
                                        <div class="d-flex align-items-start">
                                            <div class="flex-grow-1">
                                                <p class="mb-2 fw-medium" id="multiServiceName">-</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <form id="multiOrderForm">
                                    <div class="mb-3">
                                        <label class="form-label"><?=__('Danh sách liên kết');?> <span
                                                class="text-danger">*</span></label>
                                        <textarea class="form-control" id="multiLinks" rows="5"
                                            placeholder="<?=__('Nhập mỗi liên kết trên một dòng');?>&#10;https://example.com/post1&#10;https://example.com/post2&#10;https://example.com/post3"></textarea>
                                        <div class="form-text">
                                            <?=__('Mỗi dòng là một liên kết. Tổng cộng:');?> <span id="linkCount"
                                                class="fw-bold">0</span> <?=__('liên kết');?>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"><?=__('Số lượng mỗi đơn');?> <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="multiQuantity" value="1"
                                                    min="1">
                                                <div class="form-text">
                                                    <?=__('Tối thiểu:').' <span id="multi-min-quantity">-</span> - '.__('Tối đa:').' <span id="multi-max-quantity">-</span>';?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label"><?=__('Thời gian chờ (giây)');?> <span
                                                        class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="multiDelay" value="1"
                                                    min="0.1" step="0.1">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-4" id="multiCommentContainer" style="display: none;">
                                        <label class="form-label"><?=__('Bình luận cho tất cả đơn');?></label>
                                        <textarea class="form-control" id="multiComments" rows="3"></textarea>
                                    </div>

                                    <div class="mb-4">
                                        <!-- Tổng số đơn hàng -->
                                        <div
                                            class="d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                            <span class="text-muted fw-medium"><?=__('Tổng số đơn hàng:');?></span>
                                            <span class="badge bg-primary fs-6" id="totalOrders">0</span>
                                        </div>

                                        <!-- Price Summary Container giống form chính -->
                                        <div
                                            class="border border-dashed border-primary rounded p-3 bg-light bg-opacity-25 mt-3">
                                            <!-- Giá trị đơn hàng -->
                                            <div class="d-flex align-items-center justify-content-between mb-2"
                                                id="multi-price-detail-row" style="display: none;">
                                                <span class="text-muted fs-6">
                                                    <?=__('Giá trị đơn hàng:');?>
                                                </span>
                                                <span class="text-muted fs-6">
                                                    <span id="multiPrice">0</span>
                                                </span>
                                            </div>
                                            <!-- Thuế VAT -->
                                            <div class="d-flex align-items-center justify-content-between mb-2"
                                                id="multi-tax-detail-row" style="display: none;">
                                                <span class="text-muted fs-6">
                                                    <?=__('Thuế VAT:');?>
                                                </span>
                                                <span class="text-muted fs-6">
                                                    <span id="multiPriceVat">0</span> (<span id="multiTaxVat">0</span>%)
                                                </span>
                                            </div>

                                            <!-- Separator line khi có thuế -->
                                            <hr class="my-2 border-dashed" id="multi-price-separator"
                                                style="display: none;">

                                            <!-- Tổng tiền - nổi bật -->
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span class="fw-bold fs-5">
                                                    <?=__('Tổng tiền cần thanh toán:');?>
                                                </span>
                                                <span class="fw-bold fs-4">
                                                    <span id="totalEstimatedPrice"><?=format_currency(0);?></span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="d-grid gap-2 mt-4">
                                        <button type="button" class="btn btn-primary" id="startMultiOrder">
                                            <i class="ri-play-line me-1"></i><?=__('Bắt đầu mua đơn');?>
                                        </button>
                                        <button type="button" class="btn btn-warning" id="pauseMultiOrder"
                                            style="display: none;">
                                            <i class="ri-pause-line me-1"></i><?=__('Tạm dừng');?>
                                        </button>
                                        <button type="button" class="btn btn-success" id="resumeMultiOrder"
                                            style="display: none;">
                                            <i class="ri-play-line me-1"></i><?=__('Tiếp tục');?>
                                        </button>
                                        <button type="button" class="btn btn-danger" id="stopMultiOrder"
                                            style="display: none;">
                                            <i class="ri-stop-line me-1"></i><?=__('Dừng hoàn toàn');?>
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>

                    <!-- Cột phải: Danh sách đơn hàng và trạng thái -->
                    <div class="col-md-6">
                        <div class="card border-0">
                            <div class="card-body">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h6 class="mb-0"><?=__('Danh sách đơn hàng');?></h6>
                                    <div class="text-end">
                                        <small class="text-muted"><?=__('Tiến trình:');?></small>
                                        <div class="progress mt-1" style="height: 4px;">
                                            <div class="progress-bar" id="multiOrderProgress" role="progressbar"
                                                style="width: 0%" aria-valuenow="0" aria-valuemin="0"
                                                aria-valuemax="100"></div>
                                        </div>
                                        <small class="text-muted">
                                            <span id="processedCount">0</span> / <span id="totalCount">0</span>
                                        </small>
                                    </div>
                                </div>

                                <div id="ordersList" class="multi-orders-list"
                                    style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center text-muted py-4">
                                        <i class="ri-shopping-cart-line fs-1"></i>
                                        <p class="mt-2"><?=__('Nhập danh sách liên kết để bắt đầu');?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Cảnh báo quan trọng -->
                <div class="alert alert-warning border-0 mb-3" style="border-left: 4px solid #ffc107 !important;">
                    <div class="d-flex align-items-start">
                        <i class="ri-error-warning-line text-warning me-2  "></i>
                        <div class="flex-grow-1">
                            <p class="mb-0 text-muted">
                                <?=__('Vui lòng KHÔNG đóng trang hoặc tab này trong quá trình mua nhiều đơn hàng. Việc đóng trang có thể làm gián đoạn quá trình.');?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                <button type="button" class="btn btn-info" id="clearAllOrders">
                    <i class="ri-refresh-line me-1"></i><?=__('Xóa tất cả');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Xác nhận đặt hàng -->
<div class="modal fade" id="confirmOrderModal" tabindex="-1" aria-labelledby="confirmOrderModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="confirmOrderModalLabel"><?=__('Thông tin đơn hàng');?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <div class="row mb-3">
                        <div class="col-4 text-muted"><?=__('Dịch vụ');?>:</div>
                        <div class="col-8 fw-medium" id="modal-service-info">-</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted"><?=__('Liên kết');?>:</div>
                        <div class="col-8 fw-medium">
                            <textarea id="modal-link" class="form-control form-control-sm" rows="2"
                                readonly>-</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted"><?=__('Số lượng');?>:</div>
                        <div class="col-8 fw-medium" id="modal-quantity">-</div>
                    </div>
                    <div class="row mb-3" id="modal-comment-row" style="display: none;">
                        <div class="col-4 text-muted"><?=__('Bình luận');?>:</div>
                        <div class="col-8">
                            <textarea id="modal-comment" class="form-control form-control-sm" rows="3"
                                readonly>-</textarea>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-4 text-muted"><?=__('Đặt lịch');?>:</div>
                        <div class="col-8 fw-medium" id="modal-schedule">-</div>
                    </div>
                </div>

                <!-- Price Summary Container cho Modal -->
                <div class="border border-dashed border-primary rounded p-3 bg-light bg-opacity-25">
                    <!-- Giá trị đơn hàng -->
                    <div class="d-flex align-items-center justify-content-between mb-2" id="modal-price-detail-row"
                        style="display: none;">
                        <span class="text-muted fs-6">
                            <?=__('Giá trị đơn hàng:');?>
                        </span>
                        <span class="text-muted fs-6">
                            <span id="modal-price">0</span>
                        </span>
                    </div>
                    <!-- Thuế VAT - nhỏ hơn -->
                    <div class="d-flex align-items-center justify-content-between mb-2" id="modal-tax-detail-row"
                        style="display: none;">
                        <span class="text-muted fs-6">
                            <?=__('Thuế VAT:');?>
                        </span>
                        <span class="text-muted fs-6">
                            <span id="modal-price-vat">0</span> (<span id="modal-tax-vat">0</span>%)
                        </span>
                    </div>

                    <!-- Separator line khi có thuế -->
                    <hr class="my-2 border-dashed" id="modal-price-separator" style="display: none;">

                    <!-- Tổng tiền - nổi bật -->
                    <div class="d-flex align-items-center justify-content-between">
                        <span class="fw-bold fs-5">
                            <?=__('Tổng tiền cần thanh toán:');?>
                        </span>
                        <span class="fw-bold fs-4 text-danger">
                            <span id="modal-total-price">-</span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?=__('Hủy');?></button>
                <button type="button" id="confirm-order-btn"
                    class="btn btn-primary px-4"><?=__('Xác nhận đặt hàng');?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Đặt hàng thành công -->
<div id="success-order-modal" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center p-5">
                <div class="text-end">
                    <button type="button" class="btn-close text-end" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="mt-2">
                    <lord-icon src="https://cdn.lordicon.com/tqywkdcz.json" trigger="loop"
                        style="width:150px;height:150px">
                    </lord-icon>
                    <h4 class="mb-3 mt-4" id="success-order-title"><?=__('Đặt hàng thành công!');?></h4>
                    <p class="text-muted fs-15 mb-4" id="success-order-msg"></p>
                    <div class="hstack gap-2 justify-content-center">
                        <button class="btn btn-primary" id="view-orders-btn">
                            <i class="ri-file-list-3-line align-bottom me-1"></i> <?=__('Xem lịch sử đơn hàng');?>
                        </button>
                        <button class="btn btn-soft-success" id="new-order-btn">
                            <i class="ri-add-circle-line align-bottom me-1"></i> <?=__('Đặt hàng tiếp');?>
                        </button>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light p-3 justify-content-center">
                <p class="mb-0 text-muted"><?=__('Cần hỗ trợ?');?> <a href="<?=base_url('client/contact');?>"
                        class="link-secondary fw-semibold"><?=__('Liên hệ với chúng tôi');?></a></p>
            </div>
        </div>
    </div>
</div>


<?php if($CMSNT->site('popup_status') == 1):?>
<div class="modal fade" id="modal_notification" tabindex="-1" aria-labelledby="notificationModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 overflow-hidden">
            <div class="modal-header p-3">
                <h4 class="card-title mb-0" id="notificationModalLabel"
                    style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; font-size: 1.4rem; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    <i class="ri-notification-3-line me-2"
                        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; animation: pulse 2s infinite;"></i><?=__('Thông báo hệ thống');?>
                </h4>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <style>
            @keyframes pulse {
                0% {
                    transform: scale(1);
                }

                50% {
                    transform: scale(1.1);
                }

                100% {
                    transform: scale(1);
                }
            }
            </style>
            <div class="modal-body">
                <?=$CMSNT->site('popup_noti');?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger"
                    id="dontShowAgainBtn"><?=__('Không hiển thị lại trong 2 giờ');?></button>
            </div>
        </div>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function() {
    var modal = document.getElementById('modal_notification');
    var dontShowAgainBtn = document.getElementById('dontShowAgainBtn');
    var modalClosedTime = localStorage.getItem('modalClosedTime');

    // Nếu modalClosedTime chưa được lưu hoặc đã quá 2 giờ, hiển thị modal
    if (!modalClosedTime || (Date.now() - parseInt(modalClosedTime) > 2 * 60 * 60 * 1000)) {
        var bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }

    // Lưu thời gian khi modal được đóng khi người dùng click vào nút "Không hiển thị lại" và ẩn modal
    dontShowAgainBtn.addEventListener('click', function() {
        localStorage.setItem('modalClosedTime', Date.now());
        var bootstrapModal = bootstrap.Modal.getInstance(modal);
        bootstrapModal.hide();
    });
});
</script>
<?php endif?>


<!-- Âm thanh thông báo -->
<audio id="success-sound" class="d-none">
    <source src="<?=base_url('assets/audio/applepay.mp3');?>" type="audio/mpeg">
</audio>

<script>
// Kiểm tra trạng thái đăng nhập cho modal mua nhiều đơn hàng
const USER_LOGGED_IN = <?=isset($getUser) && $getUser ? 'true' : 'false';?>;
</script>