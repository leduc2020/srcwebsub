<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Danh sách chuyên mục').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '';
$body['footer'] = '


<!-- Page JS Plugins -->
 

';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

if(checkPermission($getUser['admin'], 'view_product') != true){
    die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
}

if (isset($_POST['submit'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("' . __('Không được dùng chức năng này vì đây là trang web demo.') . '")){window.history.back().location.reload();}</script>');
    }
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
    }
    if ($CMSNT->get_row("SELECT * FROM `categories` WHERE `slug` = '".create_slug(check_string($_POST['name']))."' ")) {
        die('<script type="text/javascript">if(!alert("' . __('Chuyên mục này đã tồn tại trong hệ thống.') . '")){window.history.back().location.reload();}</script>');
    }
    $url_icon = null;
    if (check_img('icon') == true) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $uploads_dir = 'assets/storage/images/icon'.$rand.'.png';
        $tmp_name = $_FILES['icon']['tmp_name'];
        $addlogo = move_uploaded_file($tmp_name, $uploads_dir);
        if ($addlogo) {
            $url_icon = $uploads_dir;
        }
    }
    $isInsert = $CMSNT->insert("categories", [
        'stt'     => check_string($_POST['stt']),
        'icon'          => $url_icon,
        'name'          => check_string($_POST['name']),
        'parent_id'     => check_string($_POST['parent_id']),
        'slug'          => check_string($_POST['slug']),
        'description'   => check_string($_POST['description']),
        'status'        => check_string($_POST['status']),
        'created_at'   => gettime()
    ]);
    if ($isInsert) {
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => "Add Category (".check_string($_POST['name']).")."
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', "Add Category (".check_string($_POST['name']).").", $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);    
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        die('<script type="text/javascript">if(!alert("' . __('Thêm thành công!') . '")){location.href = "";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("' . __('Thêm thất bại!') . '")){window.history.back().location.reload();}</script>');
    }
}
?>


<div class="main-content app-content">
    <div class="container-fluid">
        <!-- Tiêu đề trang -->
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-name fw-semibold fs-18 mb-0">
                <i class="fa-solid fa-sitemap me-1"></i><?=__('Quản lý chuyên mục cha');?>
            </h1>
            <div class="ms-md-1 ms-0">
                <button id="btn-add-parent" class="btn btn-primary btn-sm">
                    <i class="fa-solid fa-plus me-1"></i><?=__('Thêm chuyên mục cha');?>
                </button>
            </div>
        </div>

        <div class="row">
            <!-- Form thêm chuyên mục cha -->
            <div class="col-xl-12" id="card-add-parent" style="display: none;">
                <div class="card custom-card mb-4">
                    <div class="card-header d-flex justify-content-between border-bottom-0">
                        <div class="card-title">
                            <i class="fa-solid fa-folder-plus me-2"></i><?=__('Thêm chuyên mục cha mới');?>
                        </div>
                        <button type="button" class="btn-close" id="btn-close-add-parent"></button>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label" for="stt"><?=__('Ưu tiên:');?></label>
                                        <input type="text" class="form-control" value="0" name="stt" required>
                                        <div class="form-text text-muted"><?=__('Ưu tiên càng cao, chuyên mục càng hiển thị trên cùng');?></div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?=__('Tên chuyên mục cha:');?> <span
                                                class="text-danger">*</span></label>
                                        <input type="text" class="form-control" name="name"
                                            placeholder="<?=__('Nhập tên chuyên mục');?>" required>
                                    </div>
                                    <input type="hidden" name="parent_id" value="0">
                                </div>
                                <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email"><?=__('Slug:');?>
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="slug"
                                        placeholder="<?=__('Nhập slug chuyên mục');?>" required>
                                    <small class="text-muted"><?=__('Slug sẽ được tạo tự động từ tên chuyên mục');?></small>
                                </div>
                            </div>
                            <script>
                            function removeVietnameseTones(str) {
                                return str.normalize('NFD') // Tách tổ hợp ký tự và dấu
                                    .replace(/[\u0300-\u036f]/g, '') // Loại bỏ dấu
                                    .replace(/đ/g, 'd') // Chuyển đổi chữ "đ" thành "d"
                                    .replace(/Đ/g, 'D'); // Chuyển đổi chữ "Đ" thành "D"
                            }

                            document.querySelector('input[name="name"]').addEventListener('input', function() {
                                var categoryName = this.value;

                                // Chuyển tên chuyên mục thành slug
                                var slug = removeVietnameseTones(categoryName.toLowerCase())
                                    .replace(/ /g, '-') // Thay khoảng trắng bằng dấu gạch ngang
                                    .replace(/[^\w-]+/g, ''); // Loại bỏ các ký tự không hợp lệ

                                // Đặt giá trị slug vào trường input slug
                                document.querySelector('input[name="slug"]').value = slug;
                            });
                            </script>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label"><?=__('Icon:');?> <span
                                                class="text-danger">*</span></label>
                                        <input type="file" class="form-control" name="icon" required>
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label"><?=__('Trạng thái:');?> <span
                                                class="text-danger">*</span></label>
                                        <select class="form-select" name="status" required>
                                            <option value="show"><?=__('Hiển thị');?></option>
                                            <option value="hide"><?=__('Ẩn');?></option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="mb-3">
                                        <label class="form-label"><?=__('Description SEO:');?></label>
                                        <textarea class="form-control" rows="3" name="description"
                                            placeholder="<?=__('Mô tả ngắn về chuyên mục này');?>"></textarea>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-3">
                                <button type="submit" name="submit" class="btn btn-primary">
                                    <i class="fa-solid fa-save me-1"></i> <?=__('Thêm chuyên mục');?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Danh sách chuyên mục -->
            <div class="col-xl-12">
                <div class="card custom-card">
                    
                    <div class="card-body">
                        <div class="d-flex mb-3">
                            <button type="button" class="btn btn-sm btn-outline-secondary me-1" id="collapse-all-btn">
                                <i class="fa-solid fa-angles-up me-1"></i><?=__('Đóng tất cả');?>
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-primary" id="expand-all-btn">
                                <i class="fa-solid fa-angles-down me-1"></i><?=__('Mở tất cả');?>
                            </button>
                        </div>
                        <?php
                        $parentCategories = $CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ORDER BY `stt` DESC");
                        if(count($parentCategories) > 0):
                        ?>

                        <div id="category-container">
                            <ul id="sortable-parent-categories" class="list-unstyled mb-0">
                                <?php foreach ($parentCategories as $index => $category): ?>
                                <li class="sortable-parent-item" id="parent-item-<?= $category['id']; ?>"
                                    data-id="<?= $category['id']; ?>">
                                    <div class="card-header p-2 bg-light category-header">
                                        <div class="d-flex align-items-center justify-content-between w-100 category-header-content"
                                            data-bs-toggle="collapse" data-bs-target="#category-<?= $category['id']; ?>"
                                            aria-expanded="false" aria-controls="category-<?= $category['id']; ?>"
                                            style="cursor: pointer;">
                                            <div class="d-flex align-items-center">
                                                <span class="handle-parent" onclick="event.stopPropagation();">
                                                    <i class="fa-solid fa-grip-vertical"></i>
                                                </span>
                                                <img src="<?= base_url($category['icon']); ?>" class="me-2 rounded"
                                                    width="36px" height="36px">
                                                <h5 class="category-name1"><?= $category['name']; ?></h5>
                                            </div>
                                            <div class="d-flex align-items-center flex-wrap category-header-right">
                                                <div class="category-badges me-2">
                                                    <span class="badge bg-primary rounded-pill category-badge">
                                                        <i class="fa-solid fa-folder me-1"></i><?= format_cash($CMSNT->num_rows("SELECT * FROM `categories` WHERE `parent_id` = '".$category['id']."'")); ?>
                                                    </span>
                                                    <span class="badge bg-info rounded-pill category-badge">
                                                        <i class="fa-solid fa-sort-numeric-up me-1"></i><?= $category['stt']; ?>
                                                        <input type="hidden" id="stt<?= $category['id']; ?>" value="<?= $category['stt']; ?>">
                                                    </span>
                                                    <div class="form-check form-switch category-status-switch" onclick="event.stopPropagation();">
                                                        <input class="form-check-input category-status-input" type="checkbox"
                                                            id="status<?= $category['id']; ?>" value="show"
                                                            <?= $category['status'] == 'show' ? 'checked' : ''; ?>
                                                            onchange="updateForm('<?= $category['id']; ?>')"
                                                            title="<?=__('Bật/tắt chuyên mục');?>">
                                                    </div>
                                                </div>
                                                <button class="btn btn-sm btn-light category-collapse-btn" type="button"
                                                    onclick="event.stopPropagation();">
                                                    <i class="fa-solid fa-chevron-down collapse-icon"
                                                        data-category-id="<?= $category['id']; ?>"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div id="category-<?= $category['id']; ?>" class="collapse">
                                        <div class="card-body">
                                            <div class="category-actions">
                                                <a href="<?= base_url_admin('category-add&id=' . $category['id']); ?>"
                                                    class="btn btn-sm btn-outline-primary">
                                                    <i class="fa-solid fa-plus me-1"></i><?=__('Thêm chuyên mục con');?>
                                                </a>
                                                <a href="<?= base_url_admin('category-sub&parent_id=' . $category['id']); ?>"
                                                    class="btn btn-sm btn-outline-success">
                                                    <i class="fa-solid fa-list-ul me-1"></i><?=__('Danh sách chuyên mục con');?>
                                                </a>
                                                <a href="<?= base_url_admin('category-edit&id=' . $category['id']); ?>"
                                                    class="btn btn-sm btn-outline-info">
                                                    <i class="fa-solid fa-edit me-1"></i><?=__('Sửa');?>
                                                </a>
                                                <button onclick="RemoveRow('<?= $category['id']; ?>')"
                                                    class="btn btn-sm btn-outline-danger">
                                                    <i class="fa-solid fa-trash me-1"></i><?=__('Xóa');?>
                                                </button>
                                            </div>

                                            <?php $childCategories = $CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$category['id']."' ORDER BY `stt` DESC"); ?>

                                            <?php if(count($childCategories) > 0): ?>
                                            <div class="table-responsive mt-3">
                                                <table class="table table-striped table-hover border child-table">
                                                    <thead>
                                                        <tr>
                                                            <th colspan="2"><?=__('Chuyên mục con');?></th>
                                                            <th width="10%"><?=__('Ảnh');?></th>
                                                            <th width="10%"><?=__('Dịch vụ');?></th>
                                                            <th width="10%"><?=__('Trạng thái');?></th>
                                                            <th width="10%"><?=__('Thao tác');?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody class="sortable-child-categories" data-parent-id="<?= $category['id']; ?>">
                                                        <?php foreach ($childCategories as $child): ?>
                                                        <tr id="child-item-<?= $child['id']; ?>" class="child-category-row" data-id="<?= $child['id']; ?>">
                                                            <td>
                                                                    <i class="fa-solid fa-grip-vertical handle-child" style="cursor: move;color: #aaa;font-size: 18px;"></i>
                                                                    <input type="hidden"
                                                                        class="form-control form-control-sm"
                                                                         style="display: none;"
                                                                        id="stt<?= $child['id']; ?>"
                                                                        value="<?= $child['stt']; ?>"
                                                                        onchange="updateForm('<?=$child['id'];?>')"
                                                                        readonly>
                                                            </td>
                                                            <td>
                                                                <div class="d-flex align-items-center">
                                                                    <div>
                                                                        <span class="fw-bold text-truncate"
                                                                            style="max-width: 600px; display: inline-block;"><?= $child['name']; ?></span>
                                                                    </div>
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <?php if($child['icon'] != null): ?>
                                                                <img src="<?= base_url($child['icon']); ?>" width="32px"
                                                                    height="32px" class="img-thumbnail">
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <span class="badge bg-primary rounded-pill">
                                                                    <?= format_cash($CMSNT->num_rows("SELECT * FROM `services` WHERE `category_id` = '".$child['id']."'")); ?>
                                                                </span>
                                                            </td>
                                                            <td>
                                                                <div class="form-check form-switch">
                                                                    <input class="form-check-input" type="checkbox"
                                                                        id="status<?= $child['id']; ?>" value="show"
                                                                        <?= $child['status'] == 'show' ? 'checked' : ''; ?>
                                                                        onchange="updateForm('<?= $child['id']; ?>')">
                                                                </div>
                                                            </td>
                                                            <td>
                                                                <div class="btn-list">
                                                                    <a href="<?= base_url_admin('services&category_id=' . $child['id']); ?>"
                                                                        class="btn btn-sm btn-primary"
                                                                        data-bs-toggle="tooltip" title="<?=__('Quản lý gói dịch vụ');?>">
                                                                        <i class="fa-solid fa-bars-progress"></i>
                                                                    </a>
                                                                    <a href="<?= base_url_admin('category-edit&id=' . $child['id']); ?>"
                                                                        class="btn btn-sm btn-info"
                                                                        data-bs-toggle="tooltip" title="<?=__('Sửa');?>">
                                                                        <i class="fa-solid fa-edit"></i>
                                                                    </a>
                                                                    <button onclick="RemoveRow('<?= $child['id']; ?>')"
                                                                        class="btn btn-sm btn-danger"
                                                                        data-bs-toggle="tooltip" title="<?=__('Xóa');?>">
                                                                        <i class="fa-solid fa-trash"></i>
                                                                    </button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                        <?php endforeach; ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                            <?php else: ?>
                                            <div class="alert alert-info mt-3">
                                                <i class="fa-solid fa-info-circle me-2"></i><?=__('Chưa có chuyên mục con nào trong chuyên mục này.');?>
                                            </div>
                                            <?php endif; ?>

                                            <?php if(count($childCategories) > 0): ?>
                                            <div class="alert alert-light border mt-3 fs-sm">
                                                <i class="fa-solid fa-info-circle me-2 text-primary"></i>
                                                <?=__('Để sắp xếp chuyên mục con hoặc cập nhật nhanh chuyên mục con, bạn có thể truy cập vào');?> <a class="text-primary" href="<?= base_url_admin('category-sub&parent_id='.$category['id']);?>"><strong><?=__('đây');?></strong>.</a>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="fa-solid fa-exclamation-circle me-2"></i> <?=__('Chưa có chuyên mục nào trong hệ thống.');?>
                        </div>
                        <?php endif; ?>

                        <div class="alert alert-info mb-2">
                            <i class="fa-solid fa-info-circle me-2"></i>
                            <?=__('Bạn có thể kéo thả các chuyên mục cha để sắp xếp thứ tự. Nhấp vào biểu tượng');?> <i
                                class="fa-solid fa-grip-vertical"></i> <?=__('và kéo thả để thay đổi vị trí.');?>
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

<!-- Thêm jQuery UI Touch Punch để hỗ trợ kéo thả trên thiết bị di động -->
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
    $.support.touch = 'ontouchend' in document;

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
            simulatedEvent = document.createEvent('MouseEvents');

        // Initialize the simulated mouse event using the touch event's coordinates
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
     * Handle the jQuery UI widget's touchstart events
     * @param {Object} event The widget element's touchstart event
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
        simulateMouseEvent(event, 'mouseover');

        // Simulate the mousemove event
        simulateMouseEvent(event, 'mousemove');

        // Simulate the mousedown event
        simulateMouseEvent(event, 'mousedown');
    };

    /**
     * Handle the jQuery UI widget's touchmove events
     * @param {Object} event The document's touchmove event
     */
    mouseProto._touchMove = function(event) {
        // Ignore event if not handled
        if (!touchHandled) {
            return;
        }

        // Interaction was not a click
        this._touchMoved = true;

        // Simulate the mousemove event
        simulateMouseEvent(event, 'mousemove');
    };

    /**
     * Handle the jQuery UI widget's touchend events
     * @param {Object} event The document's touchend event
     */
    mouseProto._touchEnd = function(event) {
        // Ignore event if not handled
        if (!touchHandled) {
            return;
        }

        // Simulate the mouseup event
        simulateMouseEvent(event, 'mouseup');

        // Simulate the mouseout event
        simulateMouseEvent(event, 'mouseout');

        // If the touch interaction did not move, it should trigger a click
        if (!this._touchMoved) {
            // Simulate the click event
            simulateMouseEvent(event, 'click');
        }

        // Unset the flag to allow other widgets to inherit the touch event
        touchHandled = false;
    };

    /**
     * A duck punch of the $.ui.mouse _mouseInit method to support touch events.
     * This method extends the widget with bound touch event handlers that
     * translate touch events to mouse events and pass them to the widget's
     * original mouse event handling methods.
     */
    mouseProto._mouseInit = function() {
        var self = this;

        // Delegate the touch handlers to the widget's element
        self.element.bind({
            touchstart: $.proxy(self, '_touchStart'),
            touchmove: $.proxy(self, '_touchMove'),
            touchend: $.proxy(self, '_touchEnd')
        });

        // Call the original $.ui.mouse init method
        _mouseInit.call(self);
    };

    /**
     * Remove the touch event handlers
     */
    mouseProto._mouseDestroy = function() {
        var self = this;

        // Delegate the touch handlers to the widget's element
        self.element.unbind({
            touchstart: $.proxy(self, '_touchStart'),
            touchmove: $.proxy(self, '_touchMove'),
            touchend: $.proxy(self, '_touchEnd')
        });

        // Call the original $.ui.mouse destroy method
        _mouseDestroy.call(self);
    };
})(jQuery);
</script>

<script>
function updateForm(id) {
    $.ajax({
        url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'updateTableCategory',
            id: id,
            stt: $('#stt' + id).val(),
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

// Cải tiến hàm updateParentCategoryOrder với debounce hiệu quả hơn
let updateCategoryTimer;

function updateParentCategoryOrder(order) {
    clearTimeout(updateCategoryTimer);
    updateCategoryTimer = setTimeout(function() {
        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateCategorySTT',
                order: order
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg, result.status);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showMessage('<?=__('Đã xảy ra lỗi khi cập nhật thứ tự');?>', 'error');
            }
        });
    }, 500);
}

function postRemove(id) {
    $.ajax({
        url: "<?=BASE_URL('ajaxs/admin/remove.php');?>",
        type: 'POST',
        dataType: "JSON",
        data: {
            action: 'removeCategory',
            id: id
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, 'success');
            } else {
                showMessage(result.msg, 'error');
            }
        }
    });
}

function RemoveRow(id) {
    cuteAlert({
        type: "question",
        title: "<?=__('Cảnh báo');?>",
        message: "<?=__('Bạn có chắc chắn muốn xóa chuyên mục ID');?> " + id + " <?=__('này không?');?>",
        confirmText: "<?=__('Đồng ý');?>",
        cancelText: "<?=__('Hủy');?>"
    }).then((e) => {
        if (e) {
            postRemove(id);
            setTimeout(function() {
                location.reload();
            }, 1000);
        }
    })
}

// Hàm debounce để giảm số lần gọi hàm
function debounce(func, wait) {
    let timeout;
    return function() {
        const context = this;
        const args = arguments;
        clearTimeout(timeout);
        timeout = setTimeout(function() {
            func.apply(context, args);
        }, wait);
    };
}

// Xử lý hiển thị/ẩn form thêm chuyên mục cha
document.addEventListener('DOMContentLoaded', function() {
    const btnAddParent = document.getElementById('btn-add-parent');
    const btnCloseAddParent = document.getElementById('btn-close-add-parent');
    const cardAddParent = document.getElementById('card-add-parent');

    btnAddParent.addEventListener('click', function() {
        cardAddParent.style.display = 'block';
        // Cuộn trang lên vị trí form
        cardAddParent.scrollIntoView({
            behavior: 'smooth'
        });
    });

    btnCloseAddParent.addEventListener('click', function() {
        cardAddParent.style.display = 'none';
    });

    // Khởi tạo tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// Tối ưu hiệu năng kéo thả
$(document).ready(function() {

    // Kiểm tra xem thiết bị có hỗ trợ cảm ứng không
    const isTouchDevice = 'ontouchstart' in window || navigator.maxTouchPoints;

    // Đặt cấu hình cho kéo thả một lần trước khi khởi tạo để tăng hiệu suất
    const sortableConfig = {
        handle: '.handle-parent',
        items: 'li.sortable-parent-item',
        axis: 'y',
        cursor: 'move',
        opacity: 0.7, // Giảm opacity để tăng hiệu năng render
        placeholder: 'ui-state-highlight',
        forcePlaceholderSize: true,
        helper: function(e, item) {
            // Tạo helper đơn giản hơn để tăng hiệu năng
            const helper = $(item).clone().addClass('sortable-helper');
            helper.css('height', $(item).outerHeight());
            helper.css('width', $(item).outerWidth());
            // Ẩn nội dung phức tạp khi kéo thả
            helper.find('.collapse').remove();
            helper.find('.category-actions').css('visibility', 'hidden');
            return helper;
        },
        tolerance: 'pointer',
        delay: isTouchDevice ? 200 : 100, // Tăng delay cho thiết bị cảm ứng
        distance: isTouchDevice ? 10 : 5, // Tăng khoảng cách cần thiết để bắt đầu kéo trên mobile
        scroll: true,
        scrollSpeed: 5,
        scrollSensitivity: 80,
        containment: 'parent',
        revert: false, // Tắt hiệu ứng revert để tăng hiệu năng

        // Thiết lập cho thiết bị cảm ứng
        touchStartThreshold: 10, // Ngưỡng cảm ứng để bắt đầu kéo
        cancelClass: "ui-sortable-cancel", // Class để ngăn kéo thả

        start: function(event, ui) {
            // Thêm class để kích hoạt GPU acceleration
            $('body').addClass('sorting-active');
            $(ui.item).addClass('sorting');

            // Ẩn nội dung phức tạp khi kéo để tăng hiệu năng
            $(ui.item).find('.collapse').removeClass('show');
            $(ui.item).find('.collapse-icon').removeClass('rotate-icon');

            // Vô hiệu hóa các hiệu ứng không cần thiết
            $('.sortable-parent-item').addClass('no-transition');
            $('.category-header-content').css('pointer-events', 'none');

            // Vô hiệu hóa scroll trên mobile khi đang kéo
            if (isTouchDevice) {
                $('body').css('overflow', 'hidden');
            }
        },

        stop: function(event, ui) {
            // Xóa class và khôi phục hiệu ứng
            $('body').removeClass('sorting-active');
            $(ui.item).removeClass('sorting');

            // Khôi phục hiệu ứng và tương tác sau khi kéo xong
            setTimeout(function() {
                $('.sortable-parent-item').removeClass('no-transition');
                $('.category-header-content').css('pointer-events', 'auto');

                // Khôi phục scroll trên mobile
                if (isTouchDevice) {
                    $('body').css('overflow', '');
                }
            }, 50);
        },

        update: function(event, ui) {
            var parentOrder = [];
            var total = $('.sortable-parent-item').length;

            // Thu thập dữ liệu thứ tự mới 
            $('.sortable-parent-item').each(function() {
                var id = $(this).data('id');
                var position = $(this).index();
                var reversedPosition = total - position - 1;

                parentOrder.push({
                    id: id,
                    position: reversedPosition
                });

                // Cập nhật UI mà không cần reflow
                $('#stt' + id).val(reversedPosition + 1);
            });

            // Gửi thứ tự mới lên server
            updateParentCategoryOrder(parentOrder);
        }
    };

    // Khởi tạo Sortable với cấu hình đã tối ưu
    // Thêm hỗ trợ jQuery UI Touch Punch để hoạt động trên mobile
    $('#sortable-parent-categories').sortable(sortableConfig).disableSelection();

    // Thêm sự kiện touch đặc biệt để hỗ trợ di động tốt hơn
    if (isTouchDevice) {
        // Ngăn chặn scroll khi đang cố gắng kéo thả trên mobile
        $('.handle-parent').on('touchstart', function(e) {
            // Hiển thị trạng thái di chuyển để người dùng biết có thể kéo
            $(this).addClass('touch-active');
        });

        $('.handle-parent').on('touchend', function(e) {
            $(this).removeClass('touch-active');
        });

        // Đảm bảo các phần tử có thể nhấn được trên mobile
        $('.category-header-content').on('touchstart', function(e) {
            if (!$(e.target).hasClass('handle-parent') && !$(e.target).closest('.handle-parent')
                .length) {
                e.stopPropagation();
            }
        });
    }

    // Tối ưu sự kiện collapse
    const clickHandler = debounce(function() {
        const categoryId = $(this).attr('data-bs-target').replace('#category-', '');
        const icon = $(this).find('.collapse-icon[data-category-id="' + categoryId + '"]');

        setTimeout(function() {
            if ($('#category-' + categoryId).hasClass('show')) {
                icon.addClass('rotate-icon');
                localStorage.setItem('last_opened_category', categoryId);
            } else {
                icon.removeClass('rotate-icon');
                if (localStorage.getItem('last_opened_category') === categoryId) {
                    localStorage.removeItem('last_opened_category');
                }
            }
        }, 300);
    }, 50);

    // Đăng ký sự kiện với debounce để tăng hiệu suất
    $('.category-header-content').off('click').on('click', clickHandler);

    // Khôi phục trạng thái tab cuối cùng được mở
    const lastOpenedCategory = localStorage.getItem('last_opened_category');
    if (lastOpenedCategory) {
        // Đóng tất cả các tab trước
        $('.collapse').removeClass('show');

        // Mở tab đã lưu 
        $('#category-' + lastOpenedCategory).addClass('show');

        // Cập nhật biểu tượng mũi tên
        $('.collapse-icon').removeClass('rotate-icon');
        $('.collapse-icon[data-category-id="' + lastOpenedCategory + '"]').addClass('rotate-icon');
    }

    // Xử lý nút đóng tất cả chuyên mục
    $('#collapse-all-btn').on('click', function() {
        $('.collapse').removeClass('show');
        $('.collapse-icon').removeClass('rotate-icon');
        localStorage.removeItem('last_opened_category');
    });

    // Xử lý nút mở tất cả chuyên mục
    $('#expand-all-btn').on('click', function() {
        $('.collapse').addClass('show');
        $('.collapse-icon').addClass('rotate-icon');
    });

    // Thêm class vào body để tối ưu CSS cho kéo thả
    $('body').addClass('has-sortable');
    
    // Khởi tạo sortable cho chuyên mục con
    initChildCategoriesSortable();
});

// Hàm khởi tạo sortable cho các bảng chuyên mục con
function initChildCategoriesSortable() {
    $('.sortable-child-categories').each(function() {
        const parentId = $(this).data('parent-id');
        if (!parentId) return;
        
        $(this).sortable({
            handle: '.handle-child',
            axis: 'y',
            cursor: 'move',
            opacity: 0.9,
            placeholder: 'ui-state-highlight',
            forcePlaceholderSize: true,
            tolerance: 'pointer',
            helper: function(e, tr) {
                var $helper = $(tr).clone();
                var $cells = $helper.children();
                
                // Sao chép chính xác chiều rộng của từng cột
                tr.children().each(function(i) {
                    $($cells[i]).width($(this).outerWidth());
                });
                
                // Đảm bảo helper có chiều rộng tổng bằng với hàng gốc
                $helper.css({
                    'width': tr.outerWidth(),
                    'background-color': '#f8f9fa',
                    'border': '1px solid #e9ecef',
                    'box-shadow': '0 3px 8px rgba(0,0,0,0.15)'
                });
                
                return $helper;
            },
            start: function(e, ui) {
                ui.placeholder.height(ui.item.height());
                ui.placeholder.css('visibility', 'visible');
            },
            update: function(event, ui) {
                // Thu thập dữ liệu vị trí mới cho các chuyên mục con
                const childOrder = [];
                const total = $(this).find('tr').length;
                
                $(this).find('tr').each(function(index) {
                    const id = $(this).data('id');
                    if (id !== undefined) {
                        const reversedPosition = total - index;
                        childOrder.push({
                            id: id,
                            position: reversedPosition
                        });
                        // Cập nhật giá trị input
                        $('#stt' + id).val(reversedPosition);
                    }
                });
                
                // Gửi dữ liệu vị trí mới lên server
                updateChildCategoryOrder(childOrder, parentId);
            }
        }).disableSelection();
    });
}

// Hàm cập nhật thứ tự chuyên mục con lên server
let updateChildCategoryTimer;
function updateChildCategoryOrder(order, parentId) {
    clearTimeout(updateChildCategoryTimer);
    updateChildCategoryTimer = setTimeout(function() {
        $.ajax({
            url: "<?=BASE_URL("ajaxs/admin/update.php");?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'updateCategorySubSTT',
                order: JSON.stringify(order),
                parent_id: parentId
            },
            success: function(result) {
                if (result.status == 'success') {
                    showMessage(result.msg, result.status);
                } else {
                    showMessage(result.msg || '<?=__("Lỗi không xác định");?>', result.status);
                }
            },
            error: function(xhr) {
                console.error(xhr.responseText);
                showMessage('<?=__("Đã xảy ra lỗi khi cập nhật thứ tự");?>', 'error');
            },
            complete: function() {
                // $('#loading-overlay').removeClass('active');
            }
        });
    }, 500);
}
</script>