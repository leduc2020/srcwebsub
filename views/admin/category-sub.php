<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Chuyên mục dịch vụ API') . ' | ' . $CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
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
</style>
';
$body['footer'] = '
<!-- Select2 Cdn -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<!-- Internal Select-2.js -->
<script src="' . base_url('public/theme/') . 'assets/js/select2.js"></script>
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
require_once(__DIR__ . '/../../models/is_admin.php');
require_once(__DIR__ . '/header.php');
require_once(__DIR__ . '/sidebar.php');
if (checkPermission($getUser['admin'], 'view_product') != true) {
    die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
}
if (isset($_GET['limit'])) {
    $limit = intval(check_string($_GET['limit']));
} else {
    $limit = 10;
}
if (isset($_GET['page'])) {
    $page = check_string(intval($_GET['page']));
} else {
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = " `parent_id` != 0 ";
$name = '';
$created_at = '';
$shortByDate  = '';
$parent_id = '';
$supplier_id = '';
$status = '';


if (!empty($_GET['status'])) {
    $status = check_string($_GET['status']);
    $where .= ' AND `status` = "' . $status . '" ';
}
if (!empty($_GET['supplier_id'])) {
    $supplier_id = check_string($_GET['supplier_id']);
    $supplier_id_value = $supplier_id == 'none' ? 0 : $supplier_id;
    $where .= ' AND `supplier_id` = "' . $supplier_id_value . '" ';
}
if (!empty($_GET['parent_id'])) {
    $parent_id = check_string($_GET['parent_id']);
    $parent_id_value = $parent_id == 'none' ? -1 : $parent_id; // Không có chuyên mục cha
    $where .= ' AND `parent_id` = "' . $parent_id_value . '" ';
}
if (!empty($_GET['name'])) {
    $name = check_string($_GET['name']);
    $where .= ' AND (`name` LIKE "%' . $name . '%") ';
}
if (!empty($_GET['created_at'])) {
    $created_at = check_string($_GET['created_at']);
    $createdate = $created_at;
    $created_at_1 = str_replace('-', '/', $created_at);
    $created_at_1 = explode(' to ', $created_at_1);

    if ($created_at_1[0] != $created_at_1[1]) {
        $created_at_1 = [$created_at_1[0] . ' 00:00:00', $created_at_1[1] . ' 23:59:59'];
        $where .= " AND `created_at` >= '" . $created_at_1[0] . "' AND `created_at` <= '" . $created_at_1[1] . "' ";
    }
}
if (isset($_GET['shortByDate'])) {
    $shortByDate = check_string($_GET['shortByDate']);
    $yesterday = date('Y-m-d', strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date('m');
    $currentYear = date('Y');
    $currentDate = date("Y-m-d");
    if ($shortByDate == 1) {
        $where .= " AND `created_at` LIKE '%" . $currentDate . "%' ";
    }
    if ($shortByDate == 2) {
        $where .= " AND YEAR(created_at) = $currentYear AND WEEK(created_at, 1) = $currentWeek ";
    }
    if ($shortByDate == 3) {
        $where .= " AND MONTH(created_at) = '$currentMonth' AND YEAR(created_at) = '$currentYear' ";
    }
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `categories` WHERE $where ORDER BY `stt` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `categories` WHERE $where ORDER BY stt DESC ");
$urlDatatable = pagination(base_url_admin("category-sub&limit=$limit&shortByDate=$shortByDate&name=$name&created_at=$created_at&parent_id=$parent_id&supplier_id=$supplier_id&status=$status&"), $from, $totalDatatable, $limit);

?>



<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i
                    class="fa-solid fa-sitemap me-1"></i><?= __('Chuyên mục con'); ?></h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="<?= base_url(); ?>" class="align-items-center mb-3" name="formSearch"
                            method="GET">
                            <div class="filter-area">
                                <div class="row g-2 mb-3">
                                    <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                    <input type="hidden" name="action" value="category-sub">
                                    <div class="col-lg col-md-4 col-6">
                                        <input class="form-control" value="<?= $name; ?>" name="name"
                                            placeholder="<?= __('Tên chuyên mục'); ?>">
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control" name="status">
                                            <option value=""><?= __('-- Trạng thái --'); ?></option>
                                            <option <?= $status == 1 ? 'selected' : ''; ?> value="1">
                                                <?= __('Hiển Thị'); ?>
                                            </option>
                                            <option <?= $status == 2 ? 'selected' : ''; ?> value="2"><?= __('Ẩn'); ?>
                                            </option>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control js-example-basic-single" name="parent_id">
                                            <option value=""><?= __('-- Chuyên mục cha --'); ?></option>
                                            <option value="none"><?= __('Không có chuyên mục cha'); ?></option>
                                            <?php foreach ($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ") as $option): ?>
                                            <option value="<?= $option['id']; ?>"
                                                <?= $parent_id == $option['id'] ? 'selected' : ''; ?>>
                                                <?= $option['name']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <select class="form-control js-example-basic-single" name="supplier_id">
                                            <option value=""><?= __('-- API Supplier --'); ?></option>
                                            <option value="none"><?= __('Hệ thống'); ?></option>
                                            <?php foreach ($CMSNT->get_list("SELECT * FROM `suppliers` ") as $supplier): ?>
                                            <option <?= $supplier_id == $supplier['id'] ? 'selected' : ''; ?>
                                                value="<?= $supplier['id']; ?>"><?= $supplier['domain']; ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                    <div class="col-lg col-md-4 col-6">
                                        <input type="text" name="created_at" class="form-control" id="daterange"
                                            value="<?= $created_at; ?>" placeholder="Chọn thời gian">
                                    </div>
                                </div>
                                <div class="filter-buttons mb-3">
                                    <button class="btn btn-hero btn-wave btn-sm btn-primary"><i
                                            class="fa fa-search"></i>
                                        <?= __('Tìm kiếm'); ?>
                                    </button>
                                    <a class="btn btn-wave btn-outline-danger btn-sm"
                                        href="<?= base_url_admin('category-sub'); ?>"><i class="fa fa-trash"></i>
                                        <?= __('Xóa bộ lọc'); ?>
                                    </a>
                                </div>

                            </div>


                            <!-- Nút hành động hàng loạt -->
                            <div class="d-flex mb-3">
                                <div class="btn-list" id="bulk-action-buttons" style="display: none;">
                                    <button type="button" id="btn_cap_nhat_nhanh"
                                        class="btn btn-outline-primary shadow-primary btn-wave btn-sm">
                                        <i class="fa-solid fa-pen-to-square"></i> <?= __('Cập nhật nhanh'); ?>
                                    </button>
                                    <button type="button" id="btn_delete_product"
                                        class="btn btn-outline-danger shadow-danger btn-wave btn-sm">
                                        <i class="fa-solid fa-trash"></i> <?= __('Xóa nhanh'); ?>
                                    </button>
                                    <span id="selected-counter" class="ms-2 align-self-center text-primary"></span>
                                </div>
                                <div class="ms-auto">
                                    <button id="select-all-btn" type="button" class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-check-double"></i> <?= __('Chọn tất cả'); ?>
                                    </button>
                                    <button id="deselect-all-btn" type="button"
                                        class="btn btn-sm btn-outline-secondary">
                                        <i class="fa-solid fa-xmark"></i> <?= __('Bỏ chọn tất cả'); ?>
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
                                            <th colspan="2"><?= __('Tên chuyên mục'); ?></th>
                                            <th><?= __('Chuyên mục cha'); ?></th>
                                            <th><?= __('API'); ?></th>
                                            <th class="text-center"><?= __('Số gói dịch vụ'); ?></th>
                                            <th class="text-center"><?= __('Trạng thái'); ?></th>
                                            <th><?= __('Thời gian tạo'); ?></th>
                                            <th><?= __('Cập nhật'); ?></th>
                                            <th style="width: 150px; position: sticky; right: 0; background: #f8f9fa; z-index: 10;"
                                                class="action-column"><?= __('Thao tác'); ?></th>
                                        </tr>
                                    </thead>
                                    <tbody id="sortable-services">
                                        <?php foreach ($listDatatable as $category): ?>
                                        <tr class="service-row" data-id="<?= $category['id']; ?>">
                                            <td style="display: none;">
                                                <div class="form-check">
                                                    <input type="checkbox" class="form-check-input checkbox_product"
                                                        data-id="<?= $category['id']; ?>" name="checkbox_product"
                                                        value="<?= $category['id']; ?>">
                                                </div>
                                            </td>
                                            <td>
                                                <i class="fa-solid fa-grip-vertical handle"
                                                    style="cursor: move;font-size: 18px;"></i>
                                                <input type="hidden" id="stt<?= $category['id']; ?>"
                                                    value="<?= $category['stt']; ?>">
                                            </td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <div>
                                                        <span class="fw-bold text-truncate"
                                                            style="max-width: 600px; display: inline-block;"><?= $category['name']; ?></span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <?php if ($category['parent_id'] > 0): ?>
                                                <a href="<?= base_url_admin('category-edit&id=' . $category['parent_id']); ?>"
                                                    class="badge bg-outline-primary text-decoration-none">
                                                    <?= getRowRealtime('categories', $category['parent_id'], 'name'); ?>
                                                </a>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if ($category['supplier_id'] != 0): ?>
                                                <a href="<?= base_url_admin('supplier-edit&id=' . $category['supplier_id']); ?>"
                                                    class="badge bg-outline-dark text-decoration-none"><?= getRowRealtime('suppliers', $category['supplier_id'], 'domain'); ?></a>
                                                <?php else: ?>
                                                <span class="badge bg-outline-dark"><?= __('Hệ thống'); ?></span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?= format_cash($CMSNT->num_rows("SELECT * FROM `services` WHERE `category_id` = '".$category['id']."' ")); ?>
                                            </td>
                                            <td>
                                                <div class="custom-toggle-switch d-flex align-items-center justify-content-center">
                                                    <input id="toggleswitch_<?= $category['id']; ?>" 
                                                           name="toggleswitch_<?= $category['id']; ?>"
                                                           type="checkbox" 
                                                           <?= $category['status'] == 'show' ? 'checked' : ''; ?>
                                                           onchange="toggleStatusCheckbox('<?= $category['id']; ?>', this.checked)"
                                                           data-bs-toggle="tooltip"
                                                           title="<?= $category['status'] == 'show' ? __('Hiển thị') : __('Ẩn'); ?>">
                                                    <label for="toggleswitch_<?= $category['id']; ?>" class="label-primary mb-0"></label>
                                                </div>
                                            </td>
                                            <td class="fw-bold text-primary"><span class="badge bg-light text-body"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="<?=timeAgo(strtotime($category['created_at']));?>"><?= $category['created_at']; ?></span>
                                            </td>
                                            <td class="fw-bold text-danger"><span class="badge bg-light text-body"
                                                data-toggle="tooltip" data-placement="bottom"
                                                title="<?=timeAgo(strtotime($category['updated_at']));?>"><?= $category['updated_at']; ?></span>
                                            </td>
                                            <td class="action-cell"
                                                style="position: sticky; right: 0; background: #f8f9fa; z-index: 10;">
                                                <div class="btn-list">
                                                    <a href="<?= base_url_admin('services&category_id=' . $category['id']); ?>"
                                                        class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                        title="<?=__('Quản lý gói dịch vụ');?>">
                                                        <i class="fa-solid fa-bars-progress"></i>
                                                    </a>
                                                    <a href="<?= base_url_admin('category-edit&id=' . $category['id']); ?>"
                                                        class="btn btn-info btn-wave btn-sm" data-bs-toggle="tooltip"
                                                        title="<?= __('Chỉnh sửa'); ?>">
                                                        <i class="fa-solid fa-pen-to-square"></i>
                                                    </a>
                                                    <button onclick="remove('<?= $category['id']; ?>')" type="button"
                                                        class="btn btn-danger btn-wave btn-sm" data-bs-toggle="tooltip"
                                                        title="<?= __('Xóa'); ?>">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Phân trang -->
                            <div class="row">
                                <div class="col-sm-12 col-md-5">
                                    <p class="dataTables_info"><?= __('Hiển thị'); ?> <select name="limit"
                                            onchange="this.form.submit()" class="form-select-sm" style="width: 60px;">
                                            <option <?= $limit == 5 ? 'selected' : ''; ?> value="5">5</option>
                                            <option <?= $limit == 10 ? 'selected' : ''; ?> value="10">10</option>
                                            <option <?= $limit == 20 ? 'selected' : ''; ?> value="20">20</option>
                                            <option <?= $limit == 50 ? 'selected' : ''; ?> value="50">50</option>
                                            <option <?= $limit == 100 ? 'selected' : ''; ?> value="100">100</option>
                                            <option <?= $limit == 500 ? 'selected' : ''; ?> value="500">500</option>
                                            <option <?= $limit == 1000 ? 'selected' : ''; ?> value="1000">1.000</option>
                                            <option <?= $limit == 2000 ? 'selected' : ''; ?> value="2000">2.000</option>
                                            <option <?= $limit == 5000 ? 'selected' : ''; ?> value="5000">5.000</option>
                                            <option <?= $limit == 10000 ? 'selected' : ''; ?> value="10000">10.000
                                            </option>
                                        </select> <?= __('trên tổng số'); ?> <?= format_cash($totalDatatable); ?>
                                        <?= __('chuyên mục con'); ?></p>
                                </div>
                                <div class="col-sm-12 col-md-7 mb-3">
                                    <?= $totalDatatable > $limit ? $urlDatatable : ''; ?>
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
                <h5 class="modal-title" id="modalUpdateFastLabel"><?= __('Cập Nhật Nhanh'); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="formUpdateFast">
                    <div class="mb-3">
                        <label for="bulkUpdateField" class="form-label"><?= __('Chọn trường cập nhật'); ?></label>
                        <select class="form-select" id="bulkUpdateField">
                            <option value=""><?= __('-- Chọn trường cần cập nhật --'); ?></option>
                            <option value="status"><?= __('Trạng thái'); ?></option>
                            <option value="category_id"><?= __('Chuyên mục cha'); ?></option>
                            <option value="convert_category_id"><?= __('Chuyển gói dịch vụ sang chuyên mục khác'); ?></option>
                        </select>
                    </div>

                    <div id="updateFieldContainer">
                        <!-- Nội dung form sẽ được tạo động tùy thuộc vào loại cập nhật -->
                    </div>

                    <div class="alert alert-info">
                        <small><i class="fas fa-info-circle"></i> <?= __('Cập nhật sẽ áp dụng cho'); ?> <span
                                id="selectedProductsCount">0</span> <?= __('chuyên mục con đã chọn.'); ?></small>
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

<?php
require_once(__DIR__ . '/footer.php');
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
            $('#selected-counter').text(count + ' ' + (count == 1 ? '<?= __('chuyên mục con'); ?>' :
                '<?= __('chuyên mục con'); ?>') + ' <?= __('đã chọn'); ?>');
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
                url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'updateCategorySubSTT',
                    order: JSON.stringify(serviceOrder)
                },
                success: function(result) {
                    if (result.status == 'success') {
                        showMessage(result.msg, result.status);
                    } else {
                        showMessage(result.msg || '<?= __('Lỗi không xác định'); ?>',
                            result.status);
                    }
                },
                error: function(xhr, textStatus, errorThrown) {
                    showMessage('<?= __('Đã xảy ra lỗi khi cập nhật thứ tự:'); ?> ' +
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



// Thêm đoạn JavaScript xử lý nút cập nhật nhanh vào cuối file script
$(document).ready(function() {
    // Xử lý khi nhấn nút cập nhật nhanh
    $('#btn_cap_nhat_nhanh').click(function() {
        var selectedProducts = $('.checkbox_product:checked');
        if (selectedProducts.length == 0) {
            showMessage('<?= __('Vui lòng chọn ít nhất một dịch vụ để cập nhật'); ?>', 'error');
            return;
        }

        $('#selectedProductsCount').text(selectedProducts.length);
        $('#modalUpdateFast').modal('show');
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
                            <label class="form-check-label" for="statusShow"><?= __('Hiển thị'); ?></label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="bulkStatus" id="statusHide" value="hide">
                            <label class="form-check-label" for="statusHide"><?= __('Ẩn'); ?></label>
                        </div>
                    </div>
                `);
                break;

            case 'category_id':
                var categoryOptions = `<option value=""><?= __('-- Chuyên mục cha --'); ?></option>`;
                <?php foreach ($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ") as $option): ?>
                categoryOptions +=
                    `<option value="<?= $option['id']; ?>"><?= $option['name']; ?></option>`;
                <?php endforeach; ?>

                container.html(`
                    <div class="mb-3">
                        <label for="bulkCategory" class="form-label"><?= __('Chuyên mục cha mới'); ?></label>
                        <select class="form-select" id="bulkCategory">
                            ${categoryOptions}
                        </select>
                    </div>
                `);
                break;
            case 'convert_category_id':
                container.html(`
                    <div class="mb-3">
                        <label for="convert_category_id" class="form-label"><?= __('Chuyên mục mới'); ?></label>
                        <select class="form-select" id="convert_category_id">
                            <option value=""><?= __('-- Đang tải danh sách chuyên mục --'); ?></option>
                        </select>
                    </div>
                    <div class="alert alert-warning mb-3">
                        <?=__('Hệ thống sẽ chuyển toàn bộ gói dịch vụ của chuyên mục bạn chọn lúc đầu sang chuyên mục mới');?>
                    </div>
                `);
                
                // Load categories via AJAX
                loadCategoriesForBulkTransfer();
                break;
        }
    });

    // Xử lý nút lưu thay đổi trong modal cập nhật nhanh
    $('#btnSaveBulkUpdate').on('click', function() {
        var selectedProducts = $('.checkbox_product:checked');
        if (selectedProducts.length == 0) {
            showMessage('<?= __('Vui lòng chọn ít nhất một dịch vụ để cập nhật'); ?>', 'error');
            return;
        }

        var fieldType = $('#bulkUpdateField').val();
        if (!fieldType) {
            showMessage('<?= __('Vui lòng chọn trường cần cập nhật'); ?>', 'error');
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
            case 'convert_category_id':
                updateData.value = $('#convert_category_id').val();
                break;
        }

        // Hiển thị loading
        $('#loading-overlay').addClass('active');

        // Gửi dữ liệu cập nhật lên server
        $.ajax({
            url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'bulkUpdateCategorySub',
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
                    showMessage(result.msg || '<?= __('Lỗi không xác định'); ?>', result
                        .status);
                }
            },
            error: function(xhr, textStatus, errorThrown) {
                showMessage('<?= __('Đã xảy ra lỗi khi cập nhật:'); ?> ' + errorThrown,
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
            showMessage('<?= __('Vui lòng chọn ít nhất một chuyên mục con để xóa'); ?>', 'error');
            return;
        }

        cuteAlert({
            type: "question",
            title: "<?= __('Xác Nhận Xóa Chuyên mục con'); ?>",
            message: "<?= __('Bạn có chắc chắn muốn xóa'); ?> " + selectedProducts.length +
                " <?= __('chuyên mục con đã chọn không?'); ?>",
            confirmText: "<?= __('Đồng Ý'); ?>",
            cancelText: "<?= __('Hủy'); ?>"
        }).then((e) => {
            if (e) {
                $('#loading-overlay').addClass('active');

                var productIds = [];
                selectedProducts.each(function() {
                    productIds.push($(this).data('id'));
                });

                $.ajax({
                    url: "<?= BASE_URL("ajaxs/admin/remove.php"); ?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'bulkRemoveCategorySub',
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
                                '<?= __('Lỗi khi xóa chuyên mục con'); ?>',
                                'error');
                        }
                    },
                    error: function() {
                        showMessage(
                            '<?= __('Đã xảy ra lỗi khi xóa chuyên mục con'); ?>',
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

// Toggle trạng thái chuyên mục con bằng checkbox
function toggleStatusCheckbox(id, isChecked) {
    var newStatus = isChecked ? 'show' : 'hide';
    var checkbox = $('#toggleswitch_' + id);
    var label = $('label[for="toggleswitch_' + id + '"]');
    
    // Disable checkbox trong khi xử lý
    checkbox.prop('disabled', true);
    label.addClass('opacity-50');
    
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            id: id,
            status: newStatus,
            action: 'updateCategoryStatus'
        },
        success: function(result) {
            if (result.status == 'success') {
                // Cập nhật tooltip
                var newTitle = isChecked ? '<?= __('Hiển thị'); ?>' : '<?= __('Ẩn'); ?>';
                checkbox.attr('title', newTitle).attr('data-original-title', newTitle);
                
                // Hiển thị thông báo thành công nhẹ
                showMessage(result.msg, 'success');
            } else {
                // Revert checkbox nếu có lỗi
                checkbox.prop('checked', !isChecked);
                showMessage(result.msg || '<?= __('Lỗi khi cập nhật trạng thái'); ?>', 'error');
            }
        },
        error: function() {
            // Revert checkbox nếu có lỗi
            checkbox.prop('checked', !isChecked);
            showMessage('<?= __('Đã xảy ra lỗi khi cập nhật trạng thái'); ?>', 'error');
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
        title: "<?= __('Xác Nhận Xóa Chuyên Mục Con'); ?>",
        message: "<?= __('Bạn có chắc chắn muốn xóa chuyên mục con ID'); ?> " + id + " <?= __('không?'); ?>",
        confirmText: "<?= __('Đồng Ý'); ?>",
        cancelText: "<?= __('Hủy'); ?>"
    }).then((e) => {
        if (e) {
            $('#loading-overlay').addClass('active');
            $.ajax({
                url: "<?= BASE_URL("ajaxs/admin/remove.php"); ?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    id: id,
                    action: 'removeCategorySub'
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
                    showMessage('<?= __('Đã xảy ra lỗi khi xóa dịch vụ'); ?>', 'error');
                },
                complete: function() {
                    $('#loading-overlay').removeClass('active');
                }
            });
        }
    });
}

// Hàm load danh sách categories cho bulk transfer
function loadCategoriesForBulkTransfer() {
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/view.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'loadCategoriesForTransfer'
        },
        success: function(result) {
            if (result.status == 'success') {
                var categoryOptions = `<option value=""><?= __('-- Vui lòng chọn chuyên mục mới --'); ?></option>`;
                
                // Group categories by parent
                var groupedCategories = {};
                result.categories.forEach(function(category) {
                    if (!groupedCategories[category.parent_name]) {
                        groupedCategories[category.parent_name] = [];
                    }
                    groupedCategories[category.parent_name].push(category);
                });
                
                // Build options with parent groups
                Object.keys(groupedCategories).forEach(function(parentName) {
                    categoryOptions += `<option value="" disabled style="font-weight: bold;">${parentName}</option>`;
                    groupedCategories[parentName].forEach(function(category) {
                        var serviceCountText = category.service_count > 0 ? ` (${category.service_count} <?= __('dịch vụ'); ?>)` : '';
                        var supplierText = category.supplier_name ? ` - ${category.supplier_name}` : '';
                        categoryOptions += `<option value="${category.id}">&nbsp;&nbsp;↳ ${category.name}${serviceCountText}${supplierText}</option>`;
                    });
                });
                
                $('#convert_category_id').html(categoryOptions);
            } else {
                $('#convert_category_id').html(`<option value=""><?= __('-- Lỗi tải danh sách chuyên mục --'); ?></option>`);
                showMessage(result.msg || '<?= __('Không thể tải danh sách chuyên mục'); ?>', 'error');
            }
        },
        error: function() {
            $('#convert_category_id').html(`<option value=""><?= __('-- Lỗi tải danh sách chuyên mục --'); ?></option>`);
            showMessage('<?= __('Đã xảy ra lỗi khi tải danh sách chuyên mục'); ?>', 'error');
        }
    });
}
</script>