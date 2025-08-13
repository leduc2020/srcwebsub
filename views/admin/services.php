<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Gói dịch vụ').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<!-- ckeditor -->
<script src="'.BASE_URL('public/ckeditor/ckeditor.js').'"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<link rel="stylesheet" href="//code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
<style>
    /* Hỗ trợ dark mode */
    [data-theme-mode="dark"] .action-column {
        background-color: rgba(33, 37, 41, 0.95) !important;
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.2);
    }

    [data-theme-mode="dark"] .action-cell {
        background-color: rgba(33, 37, 41, 0.95) !important;
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.2);
    }

    .action-column, .action-cell {
        position: sticky !important;
        right: 0;
        z-index: 10;
    }

    .action-column {
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.05);
    }

    .action-cell {
        box-shadow: -5px 0 10px rgba(0, 0, 0, 0.05);
    }

    .table tr.selected {
        background-color: rgba(0, 94, 234, 0.08) !important;
        position: relative;
        outline: 1px solid #0d6efd !important;
        outline-offset: -1px;
    }

    .table tr.selected td {
        border-color: rgba(13, 110, 253, 0.2) !important;
        color: rgba(0, 0, 0, 0.7);
    }

    .table tr.selected:hover {
        background-color: rgba(13, 110, 253, 0.12) !important;
    }

    .table tr.selected td:first-child {
        border-left: 2px solid #0d6efd !important;
    }

    [data-theme-mode="dark"] .table tr.selected {
        background-color: rgba(13, 110, 253, 0.15) !important;
        outline: 1px solid #3384ff !important;
        color: rgba(255, 255, 255, 0.7);
    }

    [data-theme-mode="dark"] .table tr.selected td {
        border-color: rgba(13, 110, 253, 0.3) !important;
        color: rgba(255, 255, 255, 0.7);
    }

    .table td, .table th {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .table-responsive {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    /* Điều chỉnh chiều rộng tối đa cho một số cột cụ thể để hiển thị tốt hơn */
    .table th:nth-child(3) {
        min-width: 250px; /* Cột tên dịch vụ */
    }

    .table th:nth-child(4) {
        min-width: 180px; /* Cột chi tiết */
    }

    .table th:nth-child(5) {
        min-width: 100px; /* Cột Min/Max */
    }

    .table th:nth-child(7), 
    .table th:nth-child(8), 
    .table th:nth-child(9), 
    .table th:nth-child(10), 
    .table th:nth-child(11) {
        min-width: 100px; /* Các cột giá */
    }

    /* Tạo thanh cuộn mượt mà và hiện đại */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #555;
    }

    /* CSS cho dark mode */
    [data-theme-mode="dark"] .table-responsive::-webkit-scrollbar-track {
        background: #2d3748;
    }

    [data-theme-mode="dark"] .table-responsive::-webkit-scrollbar-thumb {
        background: #4a5568;
    }

    [data-theme-mode="dark"] .table-responsive::-webkit-scrollbar-thumb:hover {
        background: #718096;
    }
    
    /* Loading overlay */
    .loading-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 9999;
        justify-content: center;
        align-items: center;
    }
    
    .loading-overlay.active {
        display: flex;
    }
    
    .loading-spinner {
        width: 50px;
        height: 50px;
        border: 5px solid #f3f3f3;
        border-top: 5px solid #3498db;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }
    
    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
    
    /* Hỗ trợ cho các nút bulk action */
    #bulk-action-buttons {
        transition: all 0.3s ease;
    }
    
    #selected-counter {
        font-size: 13px;
        padding: 3px 8px;
        background-color: rgba(13, 110, 253, 0.1);
        border-radius: 4px;
    }
    
    /* Cursor cho kéo thả */
    .handle {
        cursor: move;
        color: #adb5bd;
    }
    
    .handle:hover {
        color: #0d6efd;
    }
    
    /* Highlight placeholder khi kéo thả */
    .ui-state-highlight {
        background-color: #f8f9fa !important;
        border: 1px dashed #0d6efd !important;
        height: 50px !important;
    }

    /* Styling cho chuyên mục cha (disabled options) */
    #sourceCategorySelect option[disabled],
    #targetCategorySelect option[disabled] {
        font-weight: bold !important;
        color: #0d6efd !important;
        background-color: #f8f9fa !important;
        font-size: 13px !important;
        padding: 8px 12px !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    /* Styling cho chuyên mục con */
    #sourceCategorySelect option:not([disabled]):not([value=""]),
    #targetCategorySelect option:not([disabled]):not([value=""]) {
        padding-left: 25px !important;
        color: #495057 !important;
        font-size: 12px !important;
        background-color: #ffffff !important;
        border-left: 3px solid #e9ecef !important;
        margin-left: 10px !important;
    }
    
    /* Hover effect cho chuyên mục con */
    #sourceCategorySelect option:not([disabled]):not([value=""]):hover,
    #targetCategorySelect option:not([disabled]):not([value=""]):hover {
        background-color: #e3f2fd !important;
        color: #1976d2 !important;
        border-left-color: #2196f3 !important;
    }
    
    /* Select styling */
    #sourceCategorySelect,
    #targetCategorySelect {
        max-height: 350px;
        overflow-y: auto;
        font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif;
    }
    
    /* Loading states */
    .transfer-category-loading {
        opacity: 0.6;
        pointer-events: none;
        background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
        background-size: 200% 100%;
        animation: loading 1.5s infinite;
    }
    
    @keyframes loading {
        0% { background-position: 200% 0; }
        100% { background-position: -200% 0; }
    }
    
 
    
    #modalTransferCategory .modal-content {
        border: none;
        border-radius: 12px;
    }
    
    #modalTransferCategory .modal-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }
    
    #modalTransferCategory .modal-header .btn-close {
        filter: invert(1);
    }
</style>
';
$body['footer'] = '
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Internal Select-2.js -->
<script src="'.base_url('public/theme/').'assets/js/select2.js"></script>
<!-- jQuery UI -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
<!-- jQuery UI Touch Punch để hỗ trợ kéo thả trên thiết bị di động -->
<script>
/*!
 * jQuery UI Touch Punch 0.2.3
 *
 * Copyright 2011–2014, Dave Furfero
 * Dual licensed under the MIT or GPL Version 2 licenses.
 *
 * Depends:
 *  jquery.ui.widget.js
 *  jquery.ui.mouse.js
 */
(function($) {
    // Detect touch support
    $.support.touch = "ontouchend" in document;

    // Ignore browsers without touch support
    if (!$.support.touch) {
        return;
    }

    var mouseProto = $.ui.mouse.prototype,
        _mouseInit = mouseProto._mouseInit,
        _mouseDestroy = mouseProto._mouseDestroy,
        touchHandled;

    /**
     * Simulate a mouse event based on a corresponding touch event
     * @param {Object} event A touch event
     * @param {String} simulatedType The corresponding mouse event
     */
    function simulateMouseEvent(event, simulatedType) {
        // Ignore multi-touch events
        if (event.originalEvent.touches.length > 1) {
            return;
        }

        event.preventDefault();

        var touch = event.originalEvent.changedTouches[0],
            simulatedEvent = document.createEvent("MouseEvents");

        // Initialize the simulated mouse event using the touch event\'s coordinates
        simulatedEvent.initMouseEvent(
            simulatedType, // type
            true, // bubbles                    
            true, // cancelable                 
            window, // view                       
            1, // detail                     
            touch.screenX, // screenX                    
            touch.screenY, // screenY                    
            touch.clientX, // clientX                    
            touch.clientY, // clientY                    
            false, // ctrlKey                    
            false, // altKey                     
            false, // shiftKey                   
            false, // metaKey                    
            0, // button                     
            null // relatedTarget              
        );

        // Dispatch the simulated event to the target element
        event.target.dispatchEvent(simulatedEvent);
    }

    /**
     * Handle the jQuery UI widget\'s touchstart events
     * @param {Object} event The widget element\'s touchstart event
     */
    mouseProto._touchStart = function(event) {
        var self = this;

        // Ignore the event if another widget is already being handled
        if (touchHandled || !self._mouseCapture(event.originalEvent.changedTouches[0])) {
            return;
        }

        // Set the flag to prevent other widgets from inheriting the touch event
        touchHandled = true;

        // Track movement to determine if interaction was a click
        self._touchMoved = false;

        // Simulate the mouseover event
        simulateMouseEvent(event, "mouseover");

        // Simulate the mousemove event
        simulateMouseEvent(event, "mousemove");

        // Simulate the mousedown event
        simulateMouseEvent(event, "mousedown");
    };

    /**
     * Handle the jQuery UI widget\'s touchmove events
     * @param {Object} event The document\'s touchmove event
     */
    mouseProto._touchMove = function(event) {
        // Ignore event if not handled
        if (!touchHandled) {
            return;
        }

        // Interaction was not a click
        this._touchMoved = true;

        // Simulate the mousemove event
        simulateMouseEvent(event, "mousemove");
    };

    /**
     * Handle the jQuery UI widget\'s touchend events
     * @param {Object} event The document\'s touchend event
     */
    mouseProto._touchEnd = function(event) {
        // Ignore event if not handled
        if (!touchHandled) {
            return;
        }

        // Simulate the mouseup event
        simulateMouseEvent(event, "mouseup");

        // Simulate the mouseout event
        simulateMouseEvent(event, "mouseout");

        // If the touch interaction did not move, it should trigger a click
        if (!this._touchMoved) {
            // Simulate the click event
            simulateMouseEvent(event, "click");
        }

        // Unset the flag to allow other widgets to inherit the touch event
        touchHandled = false;
    };

    /**
     * A duck punch of the $.ui.mouse _mouseInit method to support touch events.
     * This method extends the widget with bound touch event handlers that
     * translate touch events to mouse events and pass them to the widget\'s
     * original mouse event handling methods.
     */
    mouseProto._mouseInit = function() {
        var self = this;

        // Delegate the touch handlers to the widget\'s element
        self.element.bind({
            touchstart: $.proxy(self, "_touchStart"),
            touchmove: $.proxy(self, "_touchMove"),
            touchend: $.proxy(self, "_touchEnd")
        });

        // Call the original $.ui.mouse init method
        _mouseInit.call(self);
    };

    /**
     * Remove the touch event handlers
     */
    mouseProto._mouseDestroy = function() {
        var self = this;

        // Delegate the touch handlers to the widget\'s element
        self.element.unbind({
            touchstart: $.proxy(self, "_touchStart"),
            touchmove: $.proxy(self, "_touchMove"),
            touchend: $.proxy(self, "_touchEnd")
        });

        // Call the original $.ui.mouse destroy method
        _mouseDestroy.call(self);
    };
})(jQuery);
</script>
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
if(checkPermission($getUser['admin'], 'view_product') != true){
    die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
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
$order_by = ' ORDER BY stt DESC ';
$name = '';
$created_at = '';
$shortByDate  = '';
$category_id = '';
$supplier_id = '';
$display = '';
$type = '';
$arrange_price = '';
$id = '';

if(!empty($_GET['display'])){
    $display = check_string($_GET['display']);
    $where .= ' AND `display` = "'.$display.'" ';
}
if(!empty($_GET['type'])){
    $type = check_string($_GET['type']);
    $where .= ' AND `type` = "'.$type.'" ';
}
if(!empty($_GET['supplier_id'])){
    $supplier_id = check_string($_GET['supplier_id']);
    $supplier_id_value = $supplier_id == 'none' ? 0 : $supplier_id;
    $where .= ' AND `supplier_id` = "'.$supplier_id_value.'" ';
}
if(!empty($_GET['category_id'])){
    $category_id = check_string($_GET['category_id']);
    $category_id_value = $category_id == 'none' ? 0 : $category_id;
    $where .= ' AND `category_id` = "'.$category_id_value.'" ';
}
if(!empty($_GET['id'])){
    $id = check_string($_GET['id']);
    $where .= ' AND `id` = "'.$id.'" ';
}
if(!empty($_GET['name'])){
    $name = check_string($_GET['name']);
    $where .= ' AND (`name` LIKE "%'.$name.'%" OR `api_name` LIKE "%'.$name.'%") ';
}
if(!empty($_GET['created_at'])){
    $created_at = check_string($_GET['created_at']);
    $createdate = $created_at;
    $created_at_1 = str_replace('-', '/', $created_at);
    $created_at_1 = explode(' to ', $created_at_1);

    if($created_at_1[0] != $created_at_1[1]){
        $created_at_1 = [$created_at_1[0].' 00:00:00', $created_at_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$created_at_1[0]."' AND `created_at` <= '".$created_at_1[1]."' ";
    }
}
if(!empty($_GET['arrange_price'])){
    $arrange_price = check_string($_GET['arrange_price']);
    if($arrange_price == 1){
        $order_by = ' ORDER BY `price` ASC ';
    }else if($arrange_price == 2){
        $order_by = ' ORDER BY `price` DESC ';
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

$listDatatable = $CMSNT->get_list(" SELECT * FROM `services` WHERE $where $order_by LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `services` WHERE $where $order_by ");
$urlDatatable = pagination(base_url_admin("services&limit=$limit&shortByDate=$shortByDate&name=$name&created_at=$created_at&category_id=$category_id&supplier_id=$supplier_id&display=$display&type=$type&"), $from, $totalDatatable, $limit);

?>



<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-cart-shopping"></i>
                <?=__('Gói dịch vụ');?>
            </h1>
            <div class="d-flex justify-content-between align-items-center">
                <div class="btn-list">
                    <button type="button" class="btn btn-sm btn-info btn-wave waves-light waves-effect"
                        id="btnTransferCategory">
                        <i class="ri-exchange-line fw-semibold align-middle"></i>
                        <?=__('Chuyển đổi chuyên mục nhanh');?>
                    </button>
                    <a type="button" href="<?=base_url_admin('service-add');?>"
                        class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light"><i
                            class="ri-add-line fw-semibold align-middle"></i> <?=__('Thêm dịch vụ mới');?></a>
                </div>
            </div>
        </div>
        <?php  if (time() - $CMSNT->site('check_time_cron_scheduled_orders') >= 300 && $CMSNT->site('status_scheduled_orders') == 1):?>
        <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-danger" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z" />
            </svg>
            <?=__('Vui lòng thực hiện');?> <b><a target="_blank" class="text-primary"
                    href="https://help.cmsnt.co/huong-dan/huong-dan-xu-ly-khi-website-bao-loi-cron/">CRON JOB</a></b>
            <?=__('liên kết');?>:
            <a class="text-primary" href="<?=base_url('cron/scheduled-orders.php?key='.$CMSNT->site('key_cron_job'));?>"
                target="_blank">
                <?=base_url('cron/scheduled-orders.php?key='.$CMSNT->site('key_cron_job'));?>
            </a> <?=__('để hệ thống tự động xử lý dơn hàng đặt lịch');?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <?php endif;?>
        <div class="row">

            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="<?=base_url();?>" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="filter-area">
                                <div class="row g-2 mb-3">
                                    <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                    <input type="hidden" name="action" value="services">
                                    <div class="col-lg col-md-4 col-6">
                                        <input class="form-control" value="<?=$id;?>" name="id"
                                            placeholder="<?=__('ID dịch vụ');?>">
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <input class="form-control" value="<?=$name;?>" name="name"
                                            placeholder="<?=__('Tên dịch vụ');?>">
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control" name="type">
                                            <option value=""><?=__('-- Type --');?></option>
                                            <?php foreach(getListServiceType() as $service_type):?>
                                            <option <?=$type == $service_type['code'] ? 'selected' : '';?>
                                                value="<?=$service_type['code'];?>"><?=$service_type['name'];?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control" name="display">
                                            <option value=""><?=__('-- Trạng thái --');?></option>
                                            <option <?=$display == 'show' ? 'selected' : '';?> value="show">
                                                <?=__('Hiển Thị');?></option>
                                            <option <?=$display == 'hide' ? 'selected' : '';?> value="hide">
                                                <?=__('Ẩn');?></option>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control js-example-basic-single" name="category_id">
                                            <option value=""><?=__('-- Chuyên mục --');?></option>
                                            <option value="none" <?=$category_id == 'none' ? 'selected' : '';?>>
                                                <?=__('Chưa có chuyên mục');?></option>
                                            <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ") as $option):?>
                                            <option disabled value="<?=$option['id'];?>"><?=$option['name'];?></option>
                                            <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$option['id']."' ") as $option1):?>
                                            <option <?=$category_id == $option1['id'] ? 'selected' : '';?>
                                                value="<?=$option1['id'];?>">__<?=$option1['name'];?></option>
                                            <?php endforeach?>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control js-example-basic-single" name="supplier_id">
                                            <option value=""><?=__('-- API Supplier --');?></option>
                                            <option value="none" <?=$supplier_id == 'none' ? 'selected' : '';?>>
                                                <?=__('Dịch vụ hệ thống');?></option>
                                            <?php foreach($CMSNT->get_list("SELECT * FROM `suppliers` ") as $supplier):?>
                                            <option <?=$supplier_id == $supplier['id'] ? 'selected' : '';?>
                                                value="<?=$supplier['id'];?>"><?=$supplier['domain'];?></option>
                                            <?php endforeach?>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <input type="text" name="created_at" class="form-control" id="daterange"
                                            value="<?=$created_at;?>" placeholder="<?=__('Chọn thời gian');?>">
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select name="arrange_price" class="form-control">
                                            <option value=""><?=__('Sắp xếp giá');?>
                                            </option>
                                            <option <?=$arrange_price == 1 ? 'selected' : '';?> value="1">
                                                <?=__('Tăng dần');?>
                                            </option>
                                            <option <?=$arrange_price == 2 ? 'selected' : '';?> value="2">
                                                <?=__('Giảm dần');?>
                                            </option>
                                        </select>
                                    </div>
                                </div>
                                <div class="filter-buttons mb-3">
                                    <button class="btn btn-hero btn-wave btn-sm btn-primary"><i
                                            class="fa fa-search"></i>
                                        <?=__('Tìm kiếm');?>
                                    </button>
                                    <a class="btn btn-wave btn-outline-danger btn-sm"
                                        href="<?=base_url_admin('services');?>"><i class="fa fa-trash"></i>
                                        <?=__('Xóa bộ lọc');?>
                                    </a>
                                </div>

                            </div>


                            <!-- Nút hành động hàng loạt -->
                            <div class="d-flex mb-3">
                                <div class="btn-list" id="bulk-action-buttons" style="display: none;">
                                    <button type="button" id="btn_cap_nhat_nhanh"
                                        class="btn btn-outline-primary shadow-primary btn-wave btn-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> <?=__('Cập nhật nhanh');?>
                                    </button>
                                    <button type="button" id="btn_delete_product"
                                        class="btn btn-outline-danger shadow-danger btn-wave btn-sm">
                                        <i class="fa-solid fa-trash"></i> <?=__('Xóa dịch vụ');?>
                                    </button>
                                    <span id="selected-counter" class="ms-2 align-self-center text-primary"></span>
                                </div>
                                <div class="ms-auto">
                                    <button id="select-all-btn" type="button" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-check-double"></i> <?=__('Chọn tất cả');?>
                                    </button>
                                    <button id="deselect-all-btn" type="button"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-xmark"></i> <?=__('Bỏ chọn tất cả');?>
                                    </button>
                                </div>
                            </div>

                            <!-- Thay thế phần services-container bằng table -->
                            <div class="table-responsive mb-3">
                                <table class="table table-bordered table-striped table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 30px;display: none;">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input" id="checkAll">
                                                </div>
                                            </th>
                                            <th colspan="2"><?=__('Gói dịch vụ');?></th>
                                            <th class="text-center"><?=__('Loại');?></th>
                                            <th><?=__('Chi tiết');?></th>
                                            <th><?=__('API');?></th>
                                            <th><?=__('Giá vốn');?></th>
                                            <th><?=__('Giá bán lẻ');?></th>
                                            <th><?=__('Giá');?> <?=__(getRankNameByTarget('price_1'));?></th>
                                            <th><?=__('Giá');?> <?=__(getRankNameByTarget('price_2'));?></th>
                                            <th><?=__('Giá');?> <?=__(getRankNameByTarget('price_3'));?></th>
                                            <th class="text-center"><?=__('Trạng thái');?></th>
                                            <th><?=__('Thời gian tạo');?></th>
                                            <th style="width: 150px; position: sticky; right: 0; background: #f8f9fa; z-index: 10;"
                                                class="action-column"><?=__('Thao tác');?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-services">
                                        <?php foreach ($listDatatable as $product): ?>
                                        <tr class="service-row" data-id="<?=$product['id'];?>">
                                            <td style="display: none;">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input checkbox_product"
                                                        data-id="<?=$product['id'];?>" name="checkbox_product"
                                                        value="<?=$product['id'];?>">
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fa-solid fa-grip-vertical handle"
                                                    style="cursor: move;font-size: 18px;"></i>
                                                <input type="hidden" id="stt<?=$product['id'];?>"
                                                    value="<?=$product['stt'];?>">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <a class="fw-bold text-truncate"
                                                            href="<?=base_url_admin('service-edit&id='.$product['id']);?>"
                                                            style="max-width: 600px; display: inline-block;"><span
                                                                class="badge bg-outline-info me-1">ID:
                                                                <?=$product['id'];?></span><?=$product['name'];?></a>
                                                        <div>
                                                            <?php if($product['category_id'] != 0): ?>
                                                            <a href="<?=base_url_admin('category-edit&id='.$product['category_id']);?>"
                                                                class="badge bg-outline-primary text-decoration-none">
                                                                <?=getRowRealtime('categories', $product['category_id'], 'name');?>
                                                            </a>
                                                            <?php else: ?>
                                                            <span class="badge bg-outline-primary">
                                                                <?=__('Chưa phân loại');?>
                                                            </span>
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <span class="badge bg-primary px-2 py-2" style="font-size: 12px;">
                                                    <i
                                                        class="fa-solid <?= $product['type'] == 'Default' ? 'fa-box' : ($product['type'] == 'Package' ? 'fa-boxes' : ($product['type'] == 'Custom Comments' ? 'fa-comments' : 'fa-search')); ?> me-1"></i>
                                                    <?=$product['type'];?>
                                                </span>
                                            </td>
                                            <td>
                                                <!-- Tính năng -->
                                                <div class="row g-2">
                                                    <div class="col-4">
                                                        <div
                                                            class="feature-item border rounded p-1 text-center <?=($product['dripfeed'] == 1 || $product['dripfeed'] == '1' || $product['dripfeed'] === true) ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10';?>">
                                                            <i
                                                                class="fa-solid fa-droplet <?=($product['dripfeed'] == 1 || $product['dripfeed'] == '1' || $product['dripfeed'] === true) ? 'text-success' : 'text-danger';?>"></i>
                                                            <div class="feature-name small"><?=__('Chậm');?></div>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="feature-item border rounded p-1 text-center <?=($product['refill'] == 1 || $product['refill'] == '1' || $product['refill'] === true) ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10';?>">
                                                            <i
                                                                class="fa-solid fa-rotate <?=($product['refill'] == 1 || $product['refill'] == '1' || $product['refill'] === true) ? 'text-success' : 'text-danger';?>"></i>
                                                            <div class="feature-name small"><?=__('Bảo hành');?></div>

                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div
                                                            class="feature-item border rounded p-1 text-center <?=($product['cancel'] == 1 || $product['cancel'] == '1' || $product['cancel'] === true) ? 'border-success bg-success bg-opacity-10' : 'border-danger bg-danger bg-opacity-10';?>">
                                                            <i
                                                                class="fa-solid fa-ban <?=($product['cancel'] == 1 || $product['cancel'] == '1' || $product['cancel'] === true) ? 'text-success' : 'text-danger';?>"></i>
                                                            <div class="feature-name small"><?=__('Hủy');?></div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                            <td>
                                                <?php if($product['supplier_id'] != 0): ?>
                                                <a href="<?=base_url_admin('supplier-edit&id='.$product['supplier_id']);?>"
                                                    class="badge bg-outline-dark text-decoration-none"><?=getRowRealtime('suppliers', $product['supplier_id'], 'domain');?></a>
                                                <br><span class="badge bg-outline-dark">API ID:
                                                    <?=$product['api_id'];?></span>
                                                <?php else: ?>
                                                <span class="badge bg-outline-dark"><?=__('Hệ thống');?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="fw-bold text-primary"><?=$product['cost'];?> <?=getCurrencyNameDefault();?>
                                            </td>
                                            <td class="fw-bold text-danger"><?=$product['price'];?> <?=getCurrencyNameDefault();?>
                                            </td>
                                            <td class="fw-bold text-info"><?=$product['price_1'];?> <?=getCurrencyNameDefault();?>
                                            </td>
                                            <td class="fw-bold text-success"><?=$product['price_2'];?> <?=getCurrencyNameDefault();?>
                                            </td>
                                            <td class="fw-bold text-warning"><?=$product['price_3'];?> <?=getCurrencyNameDefault();?>
                                            </td>
                                            <td>
                                                <div
                                                    class="custom-toggle-switch d-flex align-items-center justify-content-center">
                                                    <input id="toggleswitch_<?=$product['id'];?>"
                                                        name="toggleswitch_<?=$product['id'];?>" type="checkbox"
                                                        <?=$product['display'] == 'show' ? 'checked' : '';?>
                                                        onchange="toggleStatusCheckbox('<?=$product['id'];?>', this.checked)"
                                                        data-bs-toggle="tooltip"
                                                        title="<?=$product['display'] == 'show' ? __('Hiển thị') : __('Ẩn');?>">
                                                    <label for="toggleswitch_<?=$product['id'];?>"
                                                        class="label-primary mb-0"></label>
                                                </div>
                                            </td>
                                            <td><span class="badge bg-light text-dark" data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="<?=timeAgo(strtotime($product['created_at']));?>"><?=$product['created_at'];?></span>
                                            </td>
                                            <td class="action-cell"
                                                style="position: sticky; right: 0; background: #f8f9fa; z-index: 10;">
                                                <div class="btn-list">
                                                    <a href="<?=base_url_admin('service-edit&id='.$product['id']);?>"
                                                        class="btn btn-info btn-wave btn-sm" data-bs-toggle="tooltip"
                                                        title="<?=__('Chỉnh sửa');?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <button onclick="remove('<?=$product['id'];?>')" type="button"
                                                        class="btn btn-danger btn-wave btn-sm" data-bs-toggle="tooltip"
                                                        title="<?=__('Xóa');?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Phân trang -->
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <p class="dataTables_info"><?=__('Hiển thị');?> <select name="limit"
                                            onchange="this.form.submit()" class="form-select-sm" style="width: 60px;">
                                            <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                            <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                            <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                            <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                            <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                            <option <?=$limit == 500 ? 'selected' : '';?> value="500">500</option>
                                            <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1.000</option>
                                            <option <?=$limit == 2000 ? 'selected' : '';?> value="2000">2.000</option>
                                            <option <?=$limit == 5000 ? 'selected' : '';?> value="5000">5.000</option>
                                            <option <?=$limit == 10000 ? 'selected' : '';?> value="10000">10.000
                                            </option>
                                        </select> <?=__('trên tổng số');?> <?=format_cash($totalDatatable);?>
                                        <?=__('dịch vụ');?></p>
                                </div>
                                <div class="col-sm-12 col-md-7 mb-3">
                                    <?=$totalDatatable > $limit ? $urlDatatable : '';?>
                                </div>
                            </div>
                        </form>

                        <!-- Loading overlay -->
                        <div class="loading-overlay" id="loading-overlay">
                            <div class="loading-spinner"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Cập Nhật Nhanh -->
<div class="modal fade" id="modalUpdateFast" tabindex="-1" aria-labelledby="modalUpdateFastLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase" id="modalUpdateFastLabel"><?=__('Cập Nhật Nhanh Dịch Vụ');?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUpdateFast">
                    <div class="mb-3">
                        <label for="bulkUpdateField" class="form-label"><?=__('Chọn trường cập nhật');?></label>
                        <select class="form-select" id="bulkUpdateField">
                            <option value=""><?=__('-- Chọn trường cần cập nhật --');?></option>
                            <option value="status"><?=__('Trạng thái');?></option>
                            <option value="category_id"><?=__('Chuyên mục');?></option>
                            <option value="price"><?=__('Giá bán lẻ');?></option>
                            <option value="price_1"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_1'));?>
                            </option>
                            <option value="price_2"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_2'));?>
                            </option>
                            <option value="price_3"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_3'));?>
                            </option>
                            <option value="cost"><?=__('Giá vốn');?></option>
                            <option value="min"><?=__('Số lượng mua tối thiểu');?></option>
                            <option value="max"><?=__('Số lượng mua tối đa');?></option>
                            <option value="description"><?=__('Mô tả');?></option>
                        </select>
                    </div>

                    <div id="updateFieldContainer">
                        <!-- Nội dung form sẽ được tạo động tùy thuộc vào loại cập nhật -->
                    </div>

                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> <?=__('Cập nhật sẽ áp dụng cho');?> <span
                                id="selectedProductsCount">0</span> <?=__('dịch vụ đã chọn.');?></small>
                    </div>
                </form>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i> <?= __('Đóng'); ?>
                </button>
                <button type="button" class="btn btn-primary" id="btnSaveBulkUpdate">
                    <i class="fas fa-save me-1"></i> <?= __('Lưu thay đổi'); ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Chuyển Đổi Chuyên Mục Nhanh -->
<div class="modal fade" id="modalTransferCategory" tabindex="-1" aria-labelledby="modalTransferCategoryLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title text-uppercase text-white" id="modalTransferCategoryLabel">
                    <i class="ri-exchange-line me-2"></i><?=__('Chuyển Đổi Chuyên Mục Nhanh');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Bước 1: Chọn chuyên mục nguồn -->
                <div id="step1-select-source" class="transfer-step">
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fa-solid fa-list-ul me-2"></i><?=__('Bước 1: Chọn chuyên mục nguồn');?>
                        </h6>
                        <div class="mb-3">
                            <label for="sourceCategorySelect"
                                class="form-label"><?=__('Chọn chuyên mục con hiện tại');?></label>
                            <select class="form-select" id="sourceCategorySelect">
                                <option value=""><?=__('-- Đang tải dữ liệu... --');?></option>
                            </select>
                            <div class="d-none" id="sourceCategoryLoader">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <small class="text-muted"><?=__('Đang tải danh sách chuyên mục...');?></small>
                            </div>
                            <style>
                            #sourceCategorySelect option[disabled] {
                                font-weight: bold !important;
                                color: #0d6efd !important;
                                background-color: #f8f9fa !important;
                                font-size: 13px;
                            }

                            #sourceCategorySelect option:not([disabled]) {
                                padding-left: 20px;
                                color: #495057;
                            }

                            #targetCategorySelect option[disabled] {
                                font-weight: bold !important;
                                color: #0d6efd !important;
                                background-color: #f8f9fa !important;
                                font-size: 13px;
                            }

                            #targetCategorySelect option:not([disabled]) {
                                padding-left: 20px;
                                color: #495057;
                            }
                            </style>
                        </div>

                        <div id="sourceServiceInfo" class="alert alert-info" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-info-circle me-2"></i>
                                <div>
                                    <strong><?=__('Chuyên mục đã chọn');?>:</strong> <span
                                        id="sourceCategoryName"></span><br>
                                    <small><?=__('Số dịch vụ trong chuyên mục');?>: <span id="sourceServiceCount"
                                            class="badge bg-primary">0</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-primary" id="btnNextToTargetSelect" disabled>
                            <?=__('Tiếp theo');?> <i class="fa-solid fa-arrow-right ms-1"></i>
                        </button>
                    </div>
                </div>

                <!-- Bước 2: Chọn chuyên mục đích -->
                <div id="step2-select-target" class="transfer-step" style="display: none;">
                    <div class="mb-4">
                        <h6 class="text-primary mb-3">
                            <i class="fa-solid fa-arrow-right me-2"></i><?=__('Bước 2: Chọn chuyên mục đích');?>
                        </h6>

                        <div class="alert alert-light border">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-folder-open text-info me-2"></i>
                                <div>
                                    <strong><?=__('Từ chuyên mục');?>:</strong> <span id="fromCategoryName"></span><br>
                                    <small><?=__('Số dịch vụ sẽ chuyển');?>: <span id="fromServiceCount"
                                            class="badge bg-info">0</span></small>
                                </div>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="targetCategorySelect"
                                class="form-label"><?=__('Chọn chuyên mục đích');?></label>
                            <select class="form-select" id="targetCategorySelect">
                                <option value=""><?=__('-- Chọn chuyên mục đích --');?></option>
                            </select>
                            <div class="d-none" id="targetCategoryLoader">
                                <div class="spinner-border spinner-border-sm me-2" role="status"></div>
                                <small class="text-muted"><?=__('Đang tải danh sách chuyên mục...');?></small>
                            </div>
                        </div>

                        <div id="targetServiceInfo" class="alert alert-warning" style="display: none;">
                            <div class="d-flex align-items-center">
                                <i class="fa-solid fa-folder text-warning me-2"></i>
                                <div>
                                    <strong><?=__('Chuyên mục đích');?>:</strong> <span
                                        id="targetCategoryName"></span><br>
                                    <small><?=__('Số dịch vụ hiện tại');?>: <span id="targetServiceCount"
                                            class="badge bg-warning">0</span></small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="btnBackToSourceSelect">
                            <i class="fa-solid fa-arrow-left me-1"></i> <?=__('Quay lại');?>
                        </button>
                        <button type="button" class="btn btn-success" id="btnConfirmTransfer" disabled>
                            <i class="fa-solid fa-exchange-alt me-1"></i> <?=__('Xác nhận chuyển');?>
                        </button>
                    </div>
                </div>

                <!-- Bước 3: Xác nhận -->
                <div id="step3-confirm" class="transfer-step" style="display: none;">
                    <div class="mb-4">
                        <h6 class="text-danger mb-3">
                            <i class="fa-solid fa-exclamation-triangle me-2"></i><?=__('Bước 3: Xác nhận cuối cùng');?>
                        </h6>

                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><?=__('Xác nhận chuyển đổi chuyên mục');?></h6>
                            <hr>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong><?=__('Từ chuyên mục');?>:</strong><br>
                                    <span id="confirmFromCategory" class="text-info"></span><br>
                                    <small class="text-muted"><?=__('Số dịch vụ');?>: <span id="confirmFromCount"
                                            class="badge bg-info">0</span></small>
                                </div>
                                <div class="col-md-6">
                                    <strong><?=__('Đến chuyên mục');?>:</strong><br>
                                    <span id="confirmToCategory" class="text-success"></span><br>
                                    <small class="text-muted"><?=__('Số dịch vụ hiện tại');?>: <span id="confirmToCount"
                                            class="badge bg-success">0</span></small>
                                </div>
                            </div>
                            <hr>
                            <p class="mb-0">
                                <i class="fa-solid fa-warning text-warning me-1"></i>
                                <strong><?=__('Lưu ý');?>:</strong>
                                <?=__('Thao tác này sẽ chuyển tất cả dịch vụ từ chuyên mục nguồn sang chuyên mục đích và không thể hoàn tác.');?>
                            </p>
                        </div>
                    </div>

                    <div class="d-flex justify-content-between">
                        <button type="button" class="btn btn-outline-secondary" id="btnBackToTargetSelect">
                            <i class="fa-solid fa-arrow-left me-1"></i> <?=__('Quay lại');?>
                        </button>
                        <button type="button" class="btn btn-danger" id="btnExecuteTransfer">
                            <i class="fa-solid fa-check me-1"></i> <?=__('Thực hiện chuyển đổi');?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>
<script>
$(function() {
    // Checkbox "check all"
    $('#checkAll').on('change', function() {
        $('.checkbox_product').prop('checked', $(this).prop('checked'));
        updateSelectedRows();
    });

    // Chọn/bỏ chọn hàng khi click vào checkbox
    $(document).on('change', '.checkbox_product', function() {
        updateSelectedRows();
    });

    // Highlight row khi click vào bất kỳ vị trí nào trong hàng (trừ các nút)
    $(document).on('click', '.service-row', function(e) {
        if (!$(e.target).is('a, button, input, i, .handle')) {
            const $checkbox = $(this).find('.checkbox_product');
            $checkbox.prop('checked', !$checkbox.prop('checked'));
            updateSelectedRows();
        }
    });

    function updateSelectedRows() {
        // Highlight các hàng được chọn
        $('.service-row').each(function() {
            if ($(this).find('.checkbox_product').prop('checked')) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });

        // Cập nhật số lượng đã chọn
        var count = $('.checkbox_product:checked').length;

        // Hiển thị/ẩn các nút hành động hàng loạt
        if (count > 0) {
            $('#bulk-action-buttons').fadeIn(200);
            $('#selected-counter').text(count + ' ' + (count == 1 ? '<?=__('dịch vụ');?>' :
                '<?=__('dịch vụ');?>') + ' <?=__('đã chọn');?>');
        } else {
            $('#bulk-action-buttons').fadeOut(200);
            $('#selected-counter').text('');
        }
    }

    // Khởi tạo sortable
    $('#sortable-services').sortable({
        handle: '.handle',
        cursor: 'grabbing',
        axis: 'y',
        opacity: 0.7,
        placeholder: 'ui-state-highlight',
        helper: function(e, ui) {
            // Điều chỉnh độ rộng cột khi kéo
            ui.children().each(function() {
                $(this).width($(this).width());
            });
            return ui;
        },
        update: function(event, ui) {
            var serviceOrder = [];
            var total = $('#sortable-services tr').length;

            $('#sortable-services tr').each(function(index) {
                var id = $(this).data('id');
                if (id !== undefined) {
                    var reversedPosition = total - index;
                    serviceOrder.push({
                        id: id,
                        position: reversedPosition
                    });
                    $('#stt' + id).val(reversedPosition);
                }
            });

            $('#loading-overlay').addClass('active');

            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'updateServiceSTT',
                    order: JSON.stringify(serviceOrder)
                },
                success: function(result) {
                    if (result.status == 'success') {
                        showMessage(result.msg, result.status);
                    } else {
                        showMessage(result.msg || '<?=__('Lỗi không xác định');?>',
                            result.status);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    showMessage('<?=__('Đã xảy ra lỗi khi cập nhật thứ tự:');?> ' +
                        errorThrown, 'error');
                },
                complete: function() {
                    $('#loading-overlay').removeClass('active');
                }
            });
        }
    }).disableSelection();

    // Nút chọn tất cả
    $('#select-all-btn').on('click', function() {
        $('.checkbox_product').prop('checked', true);
        updateSelectedRows();
    });

    // Nút bỏ chọn tất cả
    $('#deselect-all-btn').on('click', function() {
        $('.checkbox_product').prop('checked', false);
        updateSelectedRows();
    });

    // Kích hoạt tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function toggleStatus(id, currentStatus) {
    //$('#loading-overlay').addClass('active');

    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'update_status_table_service',
            id: id,
            status: currentStatus === 'show' ? '' : '1'
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, result.status);

                const newStatus = currentStatus === 'show' ? 'hide' : 'show';
                const $button = $(`button[onclick="toggleStatus('${id}', '${currentStatus}')"]`);

                if (newStatus === 'show') {
                    $button.removeClass('btn-danger').addClass('btn-secondary');
                    $button.find('i').removeClass('fa-eye-slash').addClass('fa-eye');
                    const newTitle = '<?=__('Nhấn vào để ẩn');?>';
                    $button.attr('title', newTitle);
                    $button.attr('data-bs-original-title', newTitle);
                } else {
                    $button.removeClass('btn-secondary').addClass('btn-danger');
                    $button.find('i').removeClass('fa-eye').addClass('fa-eye-slash');
                    const newTitle = '<?=__('Nhấn vào để hiển thị');?>';
                    $button.attr('title', newTitle);
                    $button.attr('data-bs-original-title', newTitle);
                }

                $button.attr('onclick', `toggleStatus('${id}', '${newStatus}')`);

                const tooltip = bootstrap.Tooltip.getInstance($button[0]);
                if (tooltip) {
                    tooltip.dispose();
                }

                // Tạo mới tooltip sau một khoảng thời gian ngắn
                setTimeout(function() {
                    new bootstrap.Tooltip($button[0]);
                }, 100);
            } else {
                showMessage(result.msg, result.status);
            }
        },
        error: function() {
            showMessage('<?=__('Đã xảy ra lỗi khi cập nhật trạng thái');?>', 'error');
        },
        complete: function() {
            //$('#loading-overlay').removeClass('active');
        }
    });
}

// Thêm đoạn JavaScript xử lý nút cập nhật nhanh vào cuối file script
$(document).ready(function() {
    // Xử lý khi nhấn nút cập nhật nhanh
    $('#btn_cap_nhat_nhanh').click(function() {
        var selectedProducts = $('.checkbox_product:checked');
        if (selectedProducts.length == 0) {
            showMessage('<?=__('Vui lòng chọn ít nhất một dịch vụ để cập nhật');?>', 'error');
            return;
        }

        $('#selectedProductsCount').text(selectedProducts.length);
        $('#modalUpdateFast').modal('show');
    });

    // Cleanup CKEditor khi đóng modal
    $('#modalUpdateFast').on('hidden.bs.modal', function () {
        // Destroy CKEditor instance nếu có
        if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.bulkDescription) {
            CKEDITOR.instances.bulkDescription.destroy();
        }
        // Reset form
        $('#formUpdateFast')[0].reset();
        $('#updateFieldContainer').empty();
        $('#bulkUpdateField').val('');
    });

    // Xử lý thay đổi loại cập nhật
    $('#bulkUpdateField').on('change', function() {
        var fieldType = $(this).val();
        var container = $('#updateFieldContainer');
        container.empty();

        if (!fieldType) return;

        switch (fieldType) {
            case 'status':
                container.html(`
                    <div class="mb-3">
                        <label class="form-label">Trạng thái mới</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkStatus" id="statusShow" value="show" checked>
                            <label class="form-check-label" for="statusShow"><?=__('Hiển thị');?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkStatus" id="statusHide" value="hide">
                            <label class="form-check-label" for="statusHide"><?=__('Ẩn');?></label>
                        </div>
                    </div>
                `);
                break;

            case 'category_id':
                var categoryOptions = `<option value=""><?=__('-- Chuyên mục --');?></option>`;
                <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ") as $option): ?>
                categoryOptions +=
                    `<option disabled value="<?=$option['id'];?>"><?=$option['name'];?></option>`;
                <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$option['id']."' ") as $option1): ?>
                categoryOptions +=
                    `<option value="<?=$option1['id'];?>">__<?=$option1['name'];?></option>`;
                <?php endforeach; ?>
                <?php endforeach; ?>

                container.html(`
                    <div class="mb-3">
                        <label for="bulkCategory" class="form-label"><?=__('Chuyên mục mới');?></label>
                        <select class="form-select" id="bulkCategory">
                            ${categoryOptions}
                        </select>
                    </div>
                `);
                break;

            case 'price':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkPrice" class="form-label"><?=__('Giá bán mới');?></label>
                        <input type="number" class="form-control" id="bulkPrice" min="0">
                        <div class="form-text"><?=__('Nhập giá mới');?></div>
                    </div>
                    <div class="mb-3">
                        <label for="priceAction" class="form-label"><?=__('Hoặc điều chỉnh giá theo % (dựa trên giá vốn)');?></label>
                        <div class="input-group">
                            <select class="form-select" id="priceAction" style="max-width: 100px;">
                                <option value="increase"><?=__('Tăng');?></option>
                                <option value="decrease"><?=__('Giảm');?></option>
                            </select>
                            <input type="number" class="form-control" id="pricePercent" min="0" max="100" placeholder="%">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-info"><?=__('Ví dụ: Giá vốn 100, tăng 10% = Giá bán 110');?></div>
                    </div>
                `);
                break;

            case 'price_1':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkPrice_1" class="form-label"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_1'));?></label>
                        <input type="number" class="form-control" id="bulkPrice_1" min="0">
                        <div class="form-text"><?=__('Nhập giá mới');?></div>
                    </div>
                    <div class="mb-3">
                        <label for="priceAction_1" class="form-label"><?=__('Hoặc điều chỉnh giá theo % (dựa trên giá vốn)');?></label>
                        <div class="input-group">
                            <select class="form-select" id="priceAction_1" style="max-width: 100px;">
                                <option value="increase"><?=__('Tăng');?></option>
                                <option value="decrease"><?=__('Giảm');?></option>
                            </select>
                            <input type="number" class="form-control" id="pricePercent_1" min="0" max="100" placeholder="%">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-info"><?=__('Ví dụ: Giá vốn 100, tăng 10% = Giá bán 110');?></div>
                    </div>
                `);
                break;

            case 'price_2':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkPrice_2" class="form-label"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_2'));?></label>
                        <input type="number" class="form-control" id="bulkPrice_2" min="0">
                        <div class="form-text"><?=__('Nhập giá mới');?></div>
                    </div>
                    <div class="mb-3">
                        <label for="priceAction_2" class="form-label"><?=__('Hoặc điều chỉnh giá theo % (dựa trên giá vốn)');?></label>
                        <div class="input-group">
                            <select class="form-select" id="priceAction_2" style="max-width: 100px;">
                                <option value="increase"><?=__('Tăng');?></option>
                                <option value="decrease"><?=__('Giảm');?></option>
                            </select>
                            <input type="number" class="form-control" id="pricePercent_2" min="0" max="100" placeholder="%">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-info"><?=__('Ví dụ: Giá vốn 100, tăng 10% = Giá bán 110');?></div>
                    </div>  
                `);
                break;

            case 'price_3':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkPrice_3" class="form-label"><?=__('Giá bán');?> <?=__(getRankNameByTarget('price_3'));?></label>
                        <input type="number" class="form-control" id="bulkPrice_3" min="0"> 
                        <div class="form-text"><?=__('Nhập giá mới');?></div>
                    </div>
                    <div class="mb-3">
                        <label for="priceAction_3" class="form-label"><?=__('Hoặc điều chỉnh giá theo % (dựa trên giá vốn)');?></label>
                        <div class="input-group">
                            <select class="form-select" id="priceAction_3" style="max-width: 100px;">
                                <option value="increase"><?=__('Tăng');?></option>
                                <option value="decrease"><?=__('Giảm');?></option>
                            </select>
                            <input type="number" class="form-control" id="pricePercent_3" min="0" max="100" placeholder="%">
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-info"><?=__('Ví dụ: Giá vốn 100, tăng 10% = Giá bán 110');?></div>
                    </div>
                `);
                break;

            case 'cost':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkCost" class="form-label"><?=__('Giá vốn mới');?></label>
                        <input type="number" class="form-control" id="bulkCost" min="0">
                        <div class="form-text"><?=__('Nhập giá vốn mới');?></div>
                    </div>
                `);
                break;

            case 'min':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkMin" class="form-label"><?=__('Số lượng mua tối thiểu');?></label>
                        <input type="number" class="form-control" id="bulkMin" min="1">
                    </div>
                `);
                break;

            case 'max':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkMax" class="form-label"><?=__('Số lượng mua tối đa');?></label>
                        <input type="number" class="form-control" id="bulkMax" min="1">
                    </div>
                `);
                break;

            case 'description':
                container.html(`
                    <div class="mb-3">
                        <label for="bulkDescription" class="form-label"><?=__('Mô tả mới');?></label>
                        <textarea class="form-control" id="bulkDescription" rows="6"></textarea>
                    </div>
                `);
                
                // Khởi tạo CKEditor cho textarea mô tả
                setTimeout(function() {
                    if (typeof CKEDITOR !== 'undefined') {
                        // Xóa instance cũ nếu có
                        if (CKEDITOR.instances.bulkDescription) {
                            CKEDITOR.instances.bulkDescription.destroy();
                        }
                        
                        // Tạo instance mới
                        CKEDITOR.replace("bulkDescription", {
                            toolbar: [
                                { name: 'styles', items: ['Format', 'Font', 'FontSize'] },
                                { name: 'basicstyles', items: ['Bold', 'Italic', 'Underline', 'Strike'] },
                                { name: 'paragraph', items: ['NumberedList', 'BulletedList', '-', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock'] },
                                { name: 'links', items: ['Link', 'Unlink'] },
                                { name: 'colors', items: ['TextColor', 'BGColor'] },
                                { name: 'tools', items: ['Maximize'] }
                            ],
                            removeButtons: '',
                            language: 'vi',
                            height: 200,
                            resize_enabled: true
                        });
                    }
                }, 100);
                break;
        }
    });

    // Xử lý nút lưu thay đổi trong modal cập nhật nhanh
    $('#btnSaveBulkUpdate').on('click', function() {
        var selectedProducts = $('.checkbox_product:checked');
        if (selectedProducts.length == 0) {
            showMessage('<?=__('Vui lòng chọn ít nhất một dịch vụ để cập nhật');?>', 'error');
            return;
        }

        var fieldType = $('#bulkUpdateField').val();
        if (!fieldType) {
            showMessage('<?=__('Vui lòng chọn trường cần cập nhật');?>', 'error');
            return;
        }

        // Thu thập dữ liệu từ form
        var updateData = {
            field: fieldType,
            productIds: []
        };

        // Thu thập ID của các dịch vụ được chọn
        selectedProducts.each(function() {
            updateData.productIds.push($(this).data('id'));
        });

        // Thu thập giá trị cập nhật tùy thuộc vào loại trường
        switch (fieldType) {
            case 'status':
                updateData.value = $('input[name="bulkStatus"]:checked').val();
                break;
            case 'category_id':
                updateData.value = $('#bulkCategory').val();
                break;
            case 'price':
                if ($('#bulkPrice').val()) {
                    updateData.value = $('#bulkPrice').val();
                } else if ($('#pricePercent').val()) {
                    updateData.percentValue = $('#pricePercent').val();
                    updateData.percentAction = $('#priceAction').val();
                } else {
                    showMessage('<?=__('Vui lòng nhập giá mới hoặc % điều chỉnh');?>', 'error');
                    return;
                }
                break;
            case 'price_1':
                if ($('#bulkPrice_1').val()) {
                    updateData.value = $('#bulkPrice_1').val();
                } else if ($('#pricePercent_1').val()) {
                    updateData.percentValue = $('#pricePercent_1').val();
                    updateData.percentAction = $('#priceAction_1').val();
                } else {
                    showMessage('<?=__('Vui lòng nhập giá mới hoặc % điều chỉnh');?>', 'error');
                    return;
                }
                break;
            case 'price_2':
                if ($('#bulkPrice_2').val()) {
                    updateData.value = $('#bulkPrice_2').val();
                } else if ($('#pricePercent_2').val()) {
                    updateData.percentValue = $('#pricePercent_2').val();
                    updateData.percentAction = $('#priceAction_2').val();
                } else {
                    showMessage('<?=__('Vui lòng nhập giá mới hoặc % điều chỉnh');?>', 'error');
                    return;
                }
                break;
            case 'price_3':
                if ($('#bulkPrice_3').val()) {
                    updateData.value = $('#bulkPrice_3').val();
                } else if ($('#pricePercent_3').val()) {
                    updateData.percentValue = $('#pricePercent_3').val();
                    updateData.percentAction = $('#priceAction_3').val();
                } else {
                    showMessage('<?=__('Vui lòng nhập giá mới hoặc % điều chỉnh');?>', 'error');
                    return;
                }
                break;
            case 'cost':
                updateData.value = $('#bulkCost').val();
                break;
            case 'min':
                updateData.value = $('#bulkMin').val();
                break;
            case 'max':
                updateData.value = $('#bulkMax').val();
                break;
            case 'description':
                // Lấy dữ liệu từ CKEditor nếu có, ngược lại lấy từ textarea
                if (typeof CKEDITOR !== 'undefined' && CKEDITOR.instances.bulkDescription) {
                    updateData.value = CKEDITOR.instances.bulkDescription.getData();
                } else {
                    updateData.value = $('#bulkDescription').val();
                }
                break;
        }

        // Hiển thị loading
        $('#loading-overlay').addClass('active');

        // Gửi dữ liệu cập nhật lên server
        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'bulkUpdateServices',
                data: JSON.stringify(updateData)
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                    // Đóng modal và làm mới trang sau khi cập nhật thành công
                    $('#modalUpdateFast').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showMessage(result.msg || '<?=__('Lỗi không xác định');?>', result
                        .status);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                showMessage('<?=__('Đã xảy ra lỗi khi cập nhật:');?> ' + errorThrown,
                    'error');
            },
            complete: function() {
                // Ẩn loading
                $('#loading-overlay').removeClass('active');
            }
        });
    });

    // Xử lý nút xóa sản phẩm
    $('#btn_delete_product').click(function() {
        var selectedProducts = $('.checkbox_product:checked');
        if (selectedProducts.length == 0) {
            showMessage('<?=__('Vui lòng chọn ít nhất một dịch vụ để xóa');?>', 'error');
            return;
        }

        cuteAlert({
            type: "question",
            title: "<?=__('Xác Nhận Xóa Dịch Vụ');?>",
            message: "<?=__('Bạn có chắc chắn muốn xóa');?> " + selectedProducts.length +
                " <?=__('dịch vụ đã chọn không?');?>",
            confirmText: "<?=__('Đồng Ý');?>",
            cancelText: "<?=__('Hủy');?>"
        }).then((e) => {
            if (e) {
                $('#loading-overlay').addClass('active');

                var productIds = [];
                selectedProducts.each(function() {
                    productIds.push($(this).data('id'));
                });

                $.ajax({
                    url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'bulkRemoveProducts',
                        productIds: JSON.stringify(productIds)
                    },
                    success: function(result) {
                        if (result.status == 'success') {
                            showMessage(result.msg, 'success');
                            setTimeout(function() {
                                location.reload();
                            }, 1000);
                        } else {
                            showMessage(result.msg ||
                                '<?=__('Lỗi khi xóa dịch vụ');?>', 'error');
                        }
                    },
                    error: function() {
                        showMessage('<?=__('Đã xảy ra lỗi khi xóa dịch vụ');?>',
                            'error');
                    },
                    complete: function() {
                        $('#loading-overlay').removeClass('active');
                    }
                });
            }
        });
    });
});

// Toggle trạng thái dịch vụ bằng checkbox
function toggleStatusCheckbox(id, isChecked) {
    var newStatus = isChecked ? 'show' : 'hide';
    var checkbox = $('#toggleswitch_' + id);
    var label = $('label[for="toggleswitch_' + id + '"]');

    // Disable checkbox trong khi xử lý
    checkbox.prop('disabled', true);
    label.addClass('opacity-50');

    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            id: id,
            status: newStatus,
            action: 'updateServiceStatus'
        },
        success: function(result) {
            if (result.status == 'success') {
                // Cập nhật tooltip
                var newTitle = isChecked ? '<?=__('Hiển thị');?>' : '<?=__('Ẩn');?>';
                checkbox.attr('title', newTitle).attr('data-original-title', newTitle);

                // Hiển thị thông báo thành công nhẹ
                showMessage(result.msg, 'success');
            } else {
                // Revert checkbox nếu có lỗi
                checkbox.prop('checked', !isChecked);
                showMessage(result.msg || '<?=__('Lỗi khi cập nhật trạng thái');?>', 'error');
            }
        },
        error: function() {
            // Revert checkbox nếu có lỗi
            checkbox.prop('checked', !isChecked);
            showMessage('<?=__('Đã xảy ra lỗi khi cập nhật trạng thái');?>', 'error');
        },
        complete: function() {
            // Re-enable checkbox
            checkbox.prop('disabled', false);
            label.removeClass('opacity-50');
        }
    });
}

function remove(id) {
    cuteAlert({
        type: "question",
        title: "<?=__('Xác Nhận Xóa Dịch Vụ');?>",
        message: "<?=__('Bạn có chắc chắn muốn xóa dịch vụ ID');?> " + id + " <?=__('không?');?>",
        confirmText: "<?=__('Đồng Ý');?>",
        cancelText: "<?=__('Hủy');?>"
    }).then((e) => {
        if (e) {
            $('#loading-overlay').addClass('active');
            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    action: 'removeProduct'
                },
                success: function(result) {
                    if (result.status == 'success') {
                        showMessage(result.msg, 'success');
                        setTimeout(function() {
                            location.reload();
                        }, 500);
                    } else {
                        showMessage(result.msg, 'error');
                    }
                },
                error: function() {
                    showMessage('<?=__('Đã xảy ra lỗi khi xóa dịch vụ');?>', 'error');
                },
                complete: function() {
                    $('#loading-overlay').removeClass('active');
                }
            });
        }
    });
}

// Xử lý Modal Chuyển Đổi Chuyên Mục Nhanh
$(document).ready(function() {
    // Biến lưu trữ thông tin chuyển đổi
    var transferData = {
        sourceId: null,
        sourceName: '',
        sourceCount: 0,
        targetId: null,
        targetName: '',
        targetCount: 0
    };

    // Cache cho categories và service counts
    var categoriesCache = null;
    var serviceCountCache = {};

    // Cache DOM elements
    var $modalTransferCategory = $('#modalTransferCategory');
    var $sourceCategorySelect = $('#sourceCategorySelect');
    var $targetCategorySelect = $('#targetCategorySelect');
    var $sourceCategoryLoader = $('#sourceCategoryLoader');
    var $targetCategoryLoader = $('#targetCategoryLoader');

    // Debounce function
    function debounce(func, delay) {
        let timeoutId;
        return function(...args) {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(() => func.apply(this, args), delay);
        };
    }

    // Load categories với cache
    function loadCategories(callback) {
        if (categoriesCache !== null) {
            callback(categoriesCache);
            return;
        }

        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/view.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'loadCategoriesForTransfer'
            },
            success: function(result) {
                if (result.status === 'success') {
                    categoriesCache = result.categories;
                    callback(categoriesCache);
                } else {
                    showMessage(result.msg || '<?=__('Lỗi khi tải danh sách chuyên mục');?>',
                        'error');
                }
            },
            error: function() {
                showMessage('<?=__('Đã xảy ra lỗi khi tải danh sách chuyên mục');?>', 'error');
            }
        });
    }

    // Build options cho select với DocumentFragment - phân loại theo chuyên mục cha
    function buildCategoryOptions(categories, excludeId = null) {
        var fragment = document.createDocumentFragment();
        var emptyOption = document.createElement('option');
        emptyOption.value = '';
        emptyOption.textContent = '<?=__('-- Chọn chuyên mục --');?>';
        fragment.appendChild(emptyOption);

        // Nhóm categories theo parent
        var groupedCategories = {};
        categories.forEach(function(category) {
            if (excludeId && category.id == excludeId) return;

            if (!groupedCategories[category.parent_name]) {
                groupedCategories[category.parent_name] = [];
            }
            groupedCategories[category.parent_name].push(category);
        });

        // Tạo options theo nhóm
        Object.keys(groupedCategories).sort().forEach(function(parentName) {
            // Thêm header cho chuyên mục cha
            var parentHeader = document.createElement('option');
            parentHeader.disabled = true;
            parentHeader.style.fontWeight = 'bold';
            parentHeader.style.color = '#0d6efd';
            parentHeader.style.backgroundColor = '#f8f9fa';
            parentHeader.textContent = '📁 ' + parentName;
            fragment.appendChild(parentHeader);

            // Thêm các chuyên mục con
            groupedCategories[parentName].forEach(function(category) {
                var option = document.createElement('option');
                option.value = category.id;

                // Format hiển thị đẹp hơn với icon
                var icon = '🔹';
                if (category.supplier_name.includes('API:')) {
                    icon = '🌐';
                } else if (category.supplier_name === '<?=__('Hệ thống');?>') {
                    icon = '⚙️';
                }

                var displayText = '   ' + icon + ' ' + category.name;
                if (category.service_count > 0) {
                    displayText += ' (' + category.service_count + ' dịch vụ)';
                } else {
                    displayText += ' (Trống)';
                }
                displayText += ' - ' + category.supplier_name;

                option.textContent = displayText;
                option.style.paddingLeft = '20px';

                // Thêm styling dựa trên trạng thái
                if (category.service_count === 0) {
                    option.style.color = '#6c757d';
                    option.style.fontStyle = 'italic';
                } else if (category.service_count > 10) {
                    option.style.color = '#28a745'; // Xanh lá cho nhiều dịch vụ
                } else if (category.service_count > 5) {
                    option.style.color = '#ffc107'; // Vàng cho trung bình
                }

                fragment.appendChild(option);
            });
        });

        return fragment;
    }

    // Mở modal chuyển đổi chuyên mục
    $('#btnTransferCategory').click(function() {
        resetTransferModal();
        $modalTransferCategory.modal('show');

        // Load categories khi mở modal
        $sourceCategoryLoader.removeClass('d-none');
        $sourceCategorySelect.prop('disabled', true);

        loadCategories(function(categories) {
            var fragment = buildCategoryOptions(categories);
            $sourceCategorySelect.empty().append(fragment);
            $sourceCategoryLoader.addClass('d-none');
            $sourceCategorySelect.prop('disabled', false);
        });
    });

    // Reset modal về trạng thái ban đầu
    function resetTransferModal() {
        $('.transfer-step').hide();
        $('#step1-select-source').show();
        $('#sourceCategorySelect').val('');
        $('#targetCategorySelect').val('');
        $('#sourceServiceInfo').hide();
        $('#targetServiceInfo').hide();
        $('#btnNextToTargetSelect').prop('disabled', true);
        $('#btnConfirmTransfer').prop('disabled', true);
        transferData = {
            sourceId: null,
            sourceName: '',
            sourceCount: 0,
            targetId: null,
            targetName: '',
            targetCount: 0
        };
    }

    // Xử lý khi chọn chuyên mục nguồn với cache
    const handleSourceCategoryChange = debounce(function(categoryId, categoryName) {
        if (!categoryId) {
            $('#sourceServiceInfo').hide();
            $('#btnNextToTargetSelect').prop('disabled', true);
            return;
        }

        // Kiểm tra cache trước
        var cachedCategory = categoriesCache && categoriesCache.find(cat => cat.id == categoryId);
        if (cachedCategory) {
            transferData.sourceId = categoryId;
            transferData.sourceName = categoryName;
            transferData.sourceCount = cachedCategory.service_count;

            $('#sourceCategoryName').text(categoryName);
            $('#sourceServiceCount').text(cachedCategory.service_count);
            $('#sourceServiceInfo').fadeIn();
            $('#btnNextToTargetSelect').prop('disabled', false);
            return;
        }

        // Nếu không có cache, gọi API
        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/view.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'getCategoryServiceCount',
                category_id: categoryId
            },
            success: function(result) {
                if (result.status == 'success') {
                    transferData.sourceId = categoryId;
                    transferData.sourceName = categoryName;
                    transferData.sourceCount = result.count;

                    // Cache kết quả
                    serviceCountCache[categoryId] = result.count;

                    $('#sourceCategoryName').text(categoryName);
                    $('#sourceServiceCount').text(result.count);
                    $('#sourceServiceInfo').fadeIn();
                    $('#btnNextToTargetSelect').prop('disabled', false);
                } else {
                    showMessage(result.msg ||
                        '<?=__('Lỗi khi lấy thông tin chuyên mục');?>', 'error');
                }
            },
            error: function() {
                showMessage('<?=__('Đã xảy ra lỗi khi lấy thông tin chuyên mục');?>',
                    'error');
            }
        });
    }, 300);

    $sourceCategorySelect.change(function() {
        var categoryId = $(this).val();
        var categoryName = $(this).find('option:selected').text().trim();
        handleSourceCategoryChange(categoryId, categoryName);
    });

    // Chuyển đến bước 2
    $('#btnNextToTargetSelect').click(function() {
        $('#step1-select-source').hide();
        $('#step2-select-target').show();

        // Hiển thị thông tin chuyên mục nguồn
        $('#fromCategoryName').text(transferData.sourceName);
        $('#fromServiceCount').text(transferData.sourceCount);

        // Load target categories và loại bỏ source category
        $targetCategoryLoader.removeClass('d-none');
        $targetCategorySelect.prop('disabled', true).addClass('transfer-category-loading');

        loadCategories(function(categories) {
            var fragment = buildCategoryOptions(categories, transferData.sourceId);
            $targetCategorySelect.empty().append(fragment);
            $targetCategoryLoader.addClass('d-none');
            $targetCategorySelect.prop('disabled', false).removeClass(
                'transfer-category-loading');
        });
    });

    // Xử lý khi chọn chuyên mục đích với cache
    const handleTargetCategoryChange = debounce(function(categoryId, categoryName) {
        if (!categoryId) {
            $('#targetServiceInfo').hide();
            $('#btnConfirmTransfer').prop('disabled', true);
            return;
        }

        // Kiểm tra cache trước
        var cachedCategory = categoriesCache && categoriesCache.find(cat => cat.id == categoryId);
        if (cachedCategory) {
            transferData.targetId = categoryId;
            transferData.targetName = categoryName;
            transferData.targetCount = cachedCategory.service_count;

            $('#targetCategoryName').text(categoryName);
            $('#targetServiceCount').text(cachedCategory.service_count);
            $('#targetServiceInfo').fadeIn();
            $('#btnConfirmTransfer').prop('disabled', false);
            return;
        }

        // Nếu không có cache, gọi API
        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'getCategoryServiceCount',
                category_id: categoryId
            },
            success: function(result) {
                if (result.status == 'success') {
                    transferData.targetId = categoryId;
                    transferData.targetName = categoryName;
                    transferData.targetCount = result.count;

                    // Cache kết quả
                    serviceCountCache[categoryId] = result.count;

                    $('#targetCategoryName').text(categoryName);
                    $('#targetServiceCount').text(result.count);
                    $('#targetServiceInfo').fadeIn();
                    $('#btnConfirmTransfer').prop('disabled', false);
                } else {
                    showMessage(result.msg ||
                        '<?=__('Lỗi khi lấy thông tin chuyên mục');?>', 'error');
                }
            },
            error: function() {
                showMessage('<?=__('Đã xảy ra lỗi khi lấy thông tin chuyên mục');?>',
                    'error');
            }
        });
    }, 300);

    $targetCategorySelect.change(function() {
        var categoryId = $(this).val();
        var categoryName = $(this).find('option:selected').text().trim();
        handleTargetCategoryChange(categoryId, categoryName);
    });

    // Quay lại bước 1
    $('#btnBackToSourceSelect').click(function() {
        $('#step2-select-target').hide();
        $('#step1-select-source').show();
        $('#targetCategorySelect').val('');
        $('#targetServiceInfo').hide();
        $('#btnConfirmTransfer').prop('disabled', true);
    });

    // Chuyển đến bước 3 (xác nhận)
    $('#btnConfirmTransfer').click(function() {
        $('#step2-select-target').hide();
        $('#step3-confirm').show();

        // Hiển thị thông tin xác nhận
        $('#confirmFromCategory').text(transferData.sourceName);
        $('#confirmFromCount').text(transferData.sourceCount);
        $('#confirmToCategory').text(transferData.targetName);
        $('#confirmToCount').text(transferData.targetCount);
    });

    // Quay lại bước 2
    $('#btnBackToTargetSelect').click(function() {
        $('#step3-confirm').hide();
        $('#step2-select-target').show();
    });

    // Thực hiện chuyển đổi
    $('#btnExecuteTransfer').click(function() {
        if (!transferData.sourceId || !transferData.targetId) {
            showMessage('<?=__('Thông tin chuyển đổi không hợp lệ');?>', 'error');
            return;
        }

        if (transferData.sourceCount === 0) {
            showMessage('<?=__('Chuyên mục nguồn không có dịch vụ nào để chuyển');?>', 'warning');
            return;
        }

        // Hiển thị loading
        $('#loading-overlay').addClass('active');

        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'transferCategoryServices',
                source_category_id: transferData.sourceId,
                target_category_id: transferData.targetId
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, 'success');
                    $('#modalTransferCategory').modal('hide');
                    setTimeout(function() {
                        location.reload();
                    }, 1500);
                } else {
                    showMessage(result.msg || '<?=__('Lỗi khi chuyển đổi chuyên mục');?>',
                        'error');
                }
            },
            error: function() {
                showMessage('<?=__('Đã xảy ra lỗi khi chuyển đổi chuyên mục');?>', 'error');
            },
            complete: function() {
                $('#loading-overlay').removeClass('active');
            }
        });
    });

    // Reset modal khi đóng
    $('#modalTransferCategory').on('hidden.bs.modal', function() {
        resetTransferModal();
    });

    // Performance optimizations
    // Pre-load categories khi page load (background loading)
    $(document).ready(function() {
        setTimeout(function() {
            if (categoriesCache === null) {
                loadCategories(function() {
                    console.log('Categories pre-loaded for better performance');
                });
            }
        }, 2000); // Load sau 2 giây để không ảnh hưởng trang chính
    });

    // Batch DOM updates để tránh reflow
    function batchDOMUpdate(updates) {
        var fragment = document.createDocumentFragment();
        updates.forEach(function(update) {
            update(fragment);
        });
        return fragment;
    }

    // Intersection Observer cho lazy loading (nếu có nhiều modal)
    if ('IntersectionObserver' in window) {
        var modalObserver = new IntersectionObserver(function(entries) {
            entries.forEach(function(entry) {
                if (entry.isIntersecting && entry.target.id === 'modalTransferCategory') {
                    // Modal đang được hiển thị, có thể pre-load thêm data nếu cần
                    if (categoriesCache === null) {
                        loadCategories(function() {});
                    }
                }
            });
        });

        modalObserver.observe(document.getElementById('modalTransferCategory'));
    }

    // Memory cleanup khi rời khỏi trang
    window.addEventListener('beforeunload', function() {
        categoriesCache = null;
        serviceCountCache = {};
    });
});
</script>