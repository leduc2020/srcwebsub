<?php

define("IN_SITE", true);
require_once(__DIR__ . "/../../libs/db.php");
require_once(__DIR__ . "/../../libs/lang.php");
require_once(__DIR__ . "/../../libs/helper.php");
require_once(__DIR__ . "/../../config.php");
require_once(__DIR__ . '/../../models/is_admin.php');

if (!isset($_POST['action'])) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => 'The Request Not Found'
    ]);
    die($data);
}
if ($CMSNT->site('status_demo') != 0) {
    die(json_encode(['status' => 'error', 'msg' => __('Chức năng này không thể sử dụng trên website demo')]));
}

if ($_POST['action'] == 'removeInvoiceBank') {
    if (checkPermission($getUser['admin'], 'edit_recharge_bank_invoice') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    if (!isset($_POST['id'])) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Dữ liệu không tồn tại trong hệ thống')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `payment_bank_invoice` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Hóa đơn không tồn tại trong hệ thống')
        ]));
    }
    $isRemove = $CMSNT->remove("payment_bank_invoice", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa thành công hóa đơn') . ' (' . $row['trans_id'] . ')'
        ]);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa thành công hóa đơn') . ' (' . $row['trans_id'] . ')'
        ]);
        die($data);
    }
}


if ($_POST['action'] == 'remove_payment_manual') {
    if (checkPermission($getUser['admin'], 'edit_recharge') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    if (!isset($_POST['id'])) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Dữ liệu không tồn tại trong hệ thống')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `payment_manual` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Item does not exist in the system')
        ]));
    }
    $isRemove = $CMSNT->remove("payment_manual", " `id` = '$id' ");
    if ($isRemove) {
        // XÓA LOGO BANK
        unlink("../../" . $row['icon']);

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá trang nạp tiền thủ công') . ' (' . $row['title'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá trang nạp tiền thủ công') . ' (' . $row['title'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa thành công'
        ]);
        die($data);
    }
}


if ($_POST['action'] == 'empty_all_list_die') {
    if (checkPermission($getUser['admin'], 'edit_stock_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $isRemove = $CMSNT->remove("product_die", " `id` > 0 ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa toàn bộ tài khoản DIE')
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  __('Xóa toàn bộ tài khoản DIE'), $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dữ liệu thành công')
        ]);
        die($data);
    }
}


if ($_POST['action'] == 'removeTaskAutomation') {
    if (checkPermission($getUser['admin'], 'edit_automations') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `automations` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại trong hệ thống'
        ]));
    }
    $isRemove = $CMSNT->remove("automations", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Delete Task (' . $row['name'] . ')'
        ]);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dữ liệu thành công')
        ]);
        die($data);
    }
}


if ($_POST['action'] == 'removeAccountSold') {
    if (checkPermission($getUser['admin'], 'edit_stock_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `product_sold` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Tài khoản không tồn tại trong hệ thống'
        ]));
    }
    $isRemove = $CMSNT->remove("product_sold", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => "Xóa tài khoản (" . $row['uid'] . ") khỏi đơn hàng đã bán"
        ]);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa tài khoản ' . $row['uid'] . ' thành công'
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'empty_list_account_stock') {
    if (checkPermission($getUser['admin'], 'edit_stock_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    if (empty($_POST['confirm_empty_list_account'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng nhập nội dung xác minh')]));
    }
    $confirm_empty_list_account = check_string($_POST['confirm_empty_list_account']);
    if ($confirm_empty_list_account != 'toi dong y') {
        die(json_encode(['status' => 'error', 'msg' => __('Nội dung xác minh không chính xác')]));
    }
    $product_code = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `product_stock` WHERE `product_code` = '$product_code' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Kho hàng đang trống')
        ]));
    }
    $isRemove = $CMSNT->remove("product_stock", " `product_code` = '$product_code' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa toàn bộ tài khoản đang bán của kho hàng') . ' (' . $product_code . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  __('Xóa toàn bộ tài khoản đang bán của kho hàng') . ' (' . $product_code . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dữ liệu thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'empty_list_die') {
    if (checkPermission($getUser['admin'], 'edit_stock_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $product_code = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `product_die` WHERE `product_code` = '$product_code' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Không có dữ liệu cần xóa'
        ]));
    }
    $isRemove = $CMSNT->remove("product_die", " `product_code` = '$product_code' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa toàn bộ tài khoản DIE của kho hàng') . ' (' . $product_code . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  __('Xóa toàn bộ tài khoản DIE của kho hàng') . ' (' . $product_code . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dữ liệu thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeProductDiscount') {
    if (checkPermission($getUser['admin'], 'edit_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `product_discount` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại'
        ]));
    }
    $isRemove = $CMSNT->remove("product_discount", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Xóa điều kiện giảm giá sản phẩm (' . getRowRealtime('products', $row['product_id'], 'name') . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Xóa điều kiện giảm giá sản phẩm (' . getRowRealtime('products', $row['product_id'], 'name') . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dữ liệu thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeBlockIP') {
    if (checkPermission($getUser['admin'], 'edit_block_ip') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `block_ip` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại'
        ]));
    }
    $isRemove = $CMSNT->remove("block_ip", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Remove Block IP (' . $row['ip'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Remove Block IP (' . $row['ip'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa IP thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removePromotion') {
    if (checkPermission($getUser['admin'], 'edit_promotion') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `promotions` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại'
        ]));
    }
    $isRemove = $CMSNT->remove("promotions", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Delete Promotion (' . format_currency($row['min']) . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Delete Promotion (' . format_currency($row['min']) . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa promotion thành công')
        ]);
        die($data);
    }
}
if ($_POST['action'] == 'removeSupplier') {
    if (checkPermission($getUser['admin'], 'manager_suppliers') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'ID API không tồn tại trong hệ thống'
        ]));
    }
    if ($supplier['status'] == 1) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Vui lòng tắt trạng thái API trước khi xóa'
        ]));
    }
    $isRemove = $CMSNT->remove("suppliers", " `id` = '$id' ");
    if ($isRemove) {
        sleep(3); // Chờ 3 giây mới xóa sản phẩm tránh sót
        $CMSNT->remove("services", " `supplier_id` = '" . $supplier['id'] . "' "); // Xóa sản phẩm API
        foreach ($CMSNT->get_list(" SELECT * FROM `categories` WHERE `supplier_id` = '$id' ") as $category) {
            if (!empty($category['icon'])) {
                $imagePath = "../../" . $category['icon'];
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath); // Xóa icon chuyên mục nếu có
                }
            }
            $CMSNT->remove("categories", " `id` = '" . $category['id'] . "' "); // Xóa chuyên mục API
        }


        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Remove API Supplier') . ' (' . $supplier['domain'] . ' ID ' . $supplier['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Remove API Supplier') . ' (' . $supplier['domain'] . ' ID ' . $supplier['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);

        die(json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa API thành công!')
        ]));
    }
}

if ($_POST['action'] == 'removeCategoriesServices') {
    if (checkPermission($getUser['admin'], 'manager_suppliers') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    
    $supplier_id = check_string($_POST['supplier_id']);
    if (!$supplier = $CMSNT->get_row("SELECT * FROM `suppliers` WHERE `id` = '$supplier_id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Supplier không tồn tại trong hệ thống')
        ]));
    }
    
    // Đếm số lượng trước khi xóa để hiển thị trong thông báo
    $categories_count = $CMSNT->num_rows("SELECT * FROM `categories` WHERE `supplier_id` = '$supplier_id'");
    $services_count = $CMSNT->num_rows("SELECT * FROM `services` WHERE `supplier_id` = '$supplier_id'");
    
    try {
        // Xóa tất cả services thuộc supplier này
        $CMSNT->remove("services", " `supplier_id` = '$supplier_id' ");
        
        // Xóa tất cả categories thuộc supplier này và xóa icon nếu có
        foreach ($CMSNT->get_list(" SELECT * FROM `categories` WHERE `supplier_id` = '$supplier_id' ") as $category) {
            if (!empty($category['icon'])) {
                $imagePath = "../../" . $category['icon'];
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath); // Xóa icon chuyên mục nếu có
                }
            }
        }
        $CMSNT->remove("categories", " `supplier_id` = '$supplier_id' ");
        
        // Ghi log
        $log_message = sprintf(__('Xóa chuyên mục và dịch vụ của supplier %s - %d chuyên mục, %d dịch vụ'), $supplier['domain'], $categories_count, $services_count);
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => $log_message
        ]);
        
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', $log_message, $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        
        die(json_encode([
            'status'    => 'success',
            'msg'       => sprintf(__('Đã xóa thành công %d chuyên mục và %d dịch vụ thuộc supplier %s'), $categories_count, $services_count, $supplier['domain'])
        ]));
        
         } catch (Exception $e) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Đã xảy ra lỗi trong quá trình xóa') . ': ' . $e->getMessage()
        ]));
    }
}




if ($_POST['action'] == 'removeOrder') {
    if (checkPermission($getUser['admin'], 'delete_order_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$product_order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Đơn hàng không tồn tại trong hệ thống'
        ]));
    }
    $isRemove = $CMSNT->remove("orders", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Delete Order (' . $product_order['trans_id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Delete Order (' . $product_order['trans_id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa đơn hàng thành công!')
        ]);
        die($data);
    }
}
if ($_POST['action'] == 'removeMenu') {
    if (checkPermission($getUser['admin'], 'edit_menu') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `menu` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại'
        ]));
    }
    $isRemove = $CMSNT->remove("menu", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Delete Menu (' . $row['name'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Delete Menu (' . $row['name'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa menu thành công !'
        ]);
        die($data);
    }
}
if ($_POST['action'] == 'removeRole') {
    if (checkPermission($getUser['admin'], 'edit_role') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `admin_role` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'Dữ liệu không tồn tại'
        ]));
    }
    $isRemove = $CMSNT->remove("admin_role", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => 'Delete Role (' . $row['name'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}',  'Delete Role (' . $row['name'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa Role thành công !'
        ]);
        die($data);
    }
}


if ($_POST['action'] == 'removeImageProduct') {
    if (checkPermission($getUser['admin'], 'edit_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `products` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'ID sản phẩm không tồn tại trong hệ thống'
        ]));
    }
    $image = check_string($_POST['image']);
    unlink("../../" . dirImageProduct($image));
    // Xóa giá trị cụ thể khỏi biến $images
    $images = str_replace($image, '', $row['images']);
    // Loại bỏ dấu xuống dòng trống nếu có
    $images = preg_replace('/^\h*\v+/m', '', $images);
    $CMSNT->update('products', [
        'images'    => $images
    ], " `id` = '" . $row['id'] . "' ");
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => 'Delete Image Product (' . $row['name'] . ' ID ' . $row['id'] . ')'
    ]);
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', 'Delete Image Product (' . $row['name'] . ' ID ' . $row['id'] . ')', $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);
    die(json_encode([
        'status'    => 'success',
        'msg'       => __('Xóa sản phẩm thành công')
    ]));
}


if ($_POST['action'] == 'removeProduct') {
    if (checkPermission($getUser['admin'], 'edit_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'ID sản phẩm không tồn tại trong hệ thống'
        ]));
    }
    $isRemove = $CMSNT->remove("services", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá dịch vụ') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá dịch vụ') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);

        die(json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa dịch vụ thành công')
        ]));
    }
}

if ($_POST['action'] == 'removeCategorySub') {
    if (checkPermission($getUser['admin'], 'edit_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Chuyên mục con không tồn tại trong hệ thống')
        ]));
    }
    $isRemove = $CMSNT->remove("categories", " `id` = '$id' ");
    if ($isRemove) {
        if (!empty($row['icon'])) {
            $imagePath = "../../" . $row['icon'];
            if (file_exists($imagePath)) {
                unlink($imagePath);
            }
        }
    }
    $Mobile_Detect = new Mobile_Detect();
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Xoá chuyên mục con') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')'
    ]);
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __('Xoá chuyên mục con') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')', $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);

    die(json_encode([
        'status'    => 'success',
        'msg'       => __('Xóa chuyên mục con thành công')
    ]));
}


if ($_POST['action'] == 'removeCategory') {
    if (checkPermission($getUser['admin'], 'edit_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => 'ID chuyên mục không tồn tại trong hệ thống'
        ]));
    }
    if ($row['parent_id'] == 0) {
        if ($CMSNT->num_rows(" SELECT * FROM `categories` WHERE `parent_id` = '" . $row['id'] . "' ") != 0) {
            die(json_encode([
                'status'    => 'error',
                'msg'       => 'Bạn cần xóa hết chuyên mục con của chuyên mục này trước khi xóa chuyên mục cha'
            ]));
        }
    }
    $isRemove = $CMSNT->remove("categories", " `id` = '$id' ");
    if ($isRemove) {
        if (!empty($row['icon'])) {
            $imagePath = "../../" . $row['icon'];
            if (file_exists($imagePath) && is_file($imagePath)) {
                unlink($imagePath); // Xóa icon chuyên mục nếu có
            }
        }

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá chuyên mục') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá chuyên mục') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa chuyên mục thành công'
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeCategoryBlog') {
    if (checkPermission($getUser['admin'], 'edit_blog') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `post_category` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('ID chuyên mục không tồn tại trong hệ thống')
        ]));
    }
    $isRemove = $CMSNT->remove("post_category", " `id` = '$id' ");
    if ($isRemove) {
        unlink("../../" . $row['icon']);

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá chuyên mục bài viết') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá chuyên mục bài viết') . ' (' . $row['name'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa chuyên mục thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removePost') {
    if (checkPermission($getUser['admin'], 'edit_blog') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `posts` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bài viết không tồn tại trong hệ thống')
        ]));
    }
    $isRemove = $CMSNT->remove("posts", " `id` = '$id' ");
    if ($isRemove) {
        unlink("../../" . $row['image']);

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá bài viết') . ' (' . $row['title'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá bài viết') . ' (' . $row['title'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa bài viết thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeBank') {
    if (checkPermission($getUser['admin'], 'edit_recharge') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `banks` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Item does not exist in the system')
        ]));
    }
    $isRemove = $CMSNT->remove("banks", " `id` = '$id' ");
    if ($isRemove) {
        // XÓA LOGO BANK
        unlink("../../" . $row['image']);

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá ngân hàng') . ' (' . $row['short_name'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá ngân hàng') . ' (' . $row['short_name'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa ngân hàng thành công'
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeLanguage') {
    if (checkPermission($getUser['admin'], 'edit_lang') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('The ID to delete does not exist')
        ]);
        die($data);
    }
    if ($row['lang_default'] == 1) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('You cannot delete the system default language')
        ]);
        die($data);
    }
    $CMSNT->remove("translate", " `lang_id` = '" . $row['id'] . "' ");
    $isRemove = $CMSNT->remove("languages", " `id` = '$id' ");
    if ($isRemove) {
        unlink("../../" . $row['image']);

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá ngôn ngữ') . ' (' . $row['lang'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá ngôn ngữ') . ' (' . $row['lang'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Successful language removal')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeCurrency') {
    if (checkPermission($getUser['admin'], 'edit_currency') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `currencies` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Item does not exist in the system')
        ]));
    }
    $isRemove = $CMSNT->remove("currencies", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá tiền tệ') . ' (' . $row['name'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá tiền tệ') . ' (' . $row['name'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa item thành công'
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeWithdraw') {
    if (checkPermission($getUser['admin'], 'edit_withdraw_affiliate') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    if (empty($_POST['id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('ID không được để trống')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `aff_withdraw` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('ID item không tồn tại trong hệ thống')
        ]);
        die($data);
    }
    $isRemove = $CMSNT->remove("aff_withdraw", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá yêu cầu rút tiền hoa hồng') . ' #' . $row['trans_id'] . ' - ' . format_currency($row['amount']) . ' - ' . $row['status']
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá yêu cầu rút tiền hoa hồng') . ' #' . $row['trans_id'] . ' - ' . format_currency($row['amount']) . ' - ' . $row['status'], $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xoá thành công')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeUser') {
    if (checkPermission($getUser['admin'], 'edit_user') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    if (empty($_POST['id'])) {
        die(json_encode(['status' => 'error', 'msg' => __('ID không được để trống')]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `users` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => 'ID user không tồn tại trong hệ thống'
        ]);
        die($data);
    }
    if ($getUser['admin'] != 99999 && $row['admin'] == 99999) {
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    $isRemove = $CMSNT->remove("users", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá tài khoản') . ' (' . $row['username'] . ' ID ' . $row['id'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá tài khoản') . ' (' . $row['username'] . ' ID ' . $row['id'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => 'Xóa người dùng thành công'
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'removeTranslate') {
    if (checkPermission($getUser['admin'], 'edit_lang') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    $row = $CMSNT->get_row("SELECT * FROM `translate` WHERE `id` = '$id' ");
    if (!$row) {
        $data = json_encode([
            'status'    => 'error',
            'msg'       => __('The ID to delete does not exist')
        ]);
        die($data);
    }
    $isRemove = $CMSNT->remove("translate", " `name` = '" . $row['name'] . "' ");
    //$isRemove = $CMSNT->remove("translate", " `id` = '$id' ");
    if ($isRemove) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá nội dung ngôn ngữ') . ' (' . $row['name'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá nội dung ngôn ngữ') . ' (' . $row['name'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Language removal successful')
        ]);
        die($data);
    }
}

if ($_POST['action'] == 'email_campaigns') {
    if (checkPermission($getUser['admin'], 'edit_email_campaigns') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    $id = check_string($_POST['id']);
    if (!$row = $CMSNT->get_row("SELECT * FROM `email_campaigns` WHERE `id` = '$id' ")) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Item không tồn tại trong hệ thống')
        ]));
    }
    $isRemove = $CMSNT->remove("email_campaigns", " `id` = '$id' ");
    if ($isRemove) {
        $CMSNT->remove('email_sending', " `camp_id` = '" . $row['id'] . "' ");

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xoá chiến dịch Email Marketing') . ' (' . $row['name'] . ')'
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xoá chiến dịch Email Marketing') . ' (' . $row['name'] . ')', $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        $data = json_encode([
            'status'    => 'success',
            'msg'       => __('Xóa item thành công')
        ]);
        die($data);
    }
}

// Xóa hàng loạt sản phẩm
if ($_POST['action'] == 'bulkRemoveProducts') {
    if (checkPermission($getUser['admin'], 'remove_product') != true) {
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    if (empty($_POST['productIds'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Không có sản phẩm nào được chọn')]));
    }

    $productIds = json_decode($_POST['productIds'], true);
    if (!is_array($productIds) || empty($productIds)) {
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }

    $removeCount = 0;
    $errorCount = 0;

    foreach ($productIds as $id) {
        $id = intval($id);
        if ($id <= 0) continue;

        // Kiểm tra xem dịch vụ có tồn tại không
        if (!$service = $CMSNT->get_row("SELECT * FROM `services` WHERE `id` = '$id'")) {
            $errorCount++;
            continue;
        }

        // Tiến hành xóa
        if ($CMSNT->remove("services", " `id` = '$id' ")) {
            $removeCount++;
        } else {
            $errorCount++;
        }
    }

    if ($removeCount > 0) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('dịch vụ')
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('dịch vụ'), $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        die(json_encode([
            'status' => 'success',
            'msg' => __('Đã xóa thành công') . ' ' . $removeCount . ' ' . __('dịch vụ') . ($errorCount > 0 ? ', ' . $errorCount . ' ' . __('dịch vụ lỗi') : "")
        ]));
    }

    die(json_encode(['status' => 'error', 'msg' => __('Không có dịch vụ nào được xóa')]));
}

// Xóa hàng loạt chuyên mục con
if ($_POST['action'] == 'bulkRemoveCategorySub') {
    if (checkPermission($getUser['admin'], 'remove_product') != true) {
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }

    if (empty($_POST['productIds'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Không có chuyên mục con nào được chọn')]));
    }

    $productIds = json_decode($_POST['productIds'], true);
    if (!is_array($productIds) || empty($productIds)) {
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }

    $removeCount = 0;
    $errorCount = 0;

    foreach ($productIds as $id) {
        $id = intval($id);
        if ($id <= 0) continue;

        // Kiểm tra xem dịch vụ có tồn tại không
        if (!$categorySub = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '$id'")) {
            $errorCount++;
            continue;
        }

        // Tiến hành xóa
        if ($CMSNT->remove("categories", " `id` = '$id' ")) {
            if (!empty($categorySub['icon'])) {
                $imagePath = "../../" . $categorySub['icon'];
                if (file_exists($imagePath) && is_file($imagePath)) {
                    unlink($imagePath); // Xóa icon chuyên mục nếu có
                }
            }
            $removeCount++;
        } else {
            $errorCount++;
        }
    }

    if ($removeCount > 0) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('chuyên mục con')
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('chuyên mục con'), $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);

        die(json_encode([
            'status' => 'success',
            'msg' => __('Đã xóa thành công') . ' ' . $removeCount . ' ' . __('chuyên mục con') . ($errorCount > 0 ? ', ' . $errorCount . ' ' . __('chuyên mục con lỗi') : "")
        ]));
    }

    die(json_encode(['status' => 'error', 'msg' => 'Không có chuyên mục con nào được xóa']));
}

// Xóa nhiều đơn hàng cùng lúc
if ($_POST['action'] == 'bulkRemoveOrders') {
    if (checkPermission($getUser['admin'], 'delete_order_product') != true) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Bạn không có quyền sử dụng tính năng này')
        ]));
    }
    
    if (empty($_POST['ids'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Không có đơn hàng nào được chọn')]));
    }
    
    $ids = json_decode($_POST['ids'], true);
    if (!is_array($ids) || empty($ids)) {
        die(json_encode(['status' => 'error', 'msg' => __('Dữ liệu không hợp lệ')]));
    }
    
    $removeCount = 0;
    $errorCount = 0;
    $orderDetails = [];
    
    foreach ($ids as $id) {
        $id = intval($id);
        if ($id <= 0) continue;
        
        // Kiểm tra xem đơn hàng có tồn tại không
        if (!$order = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '$id'")) {
            $errorCount++;
            continue;
        }
        
        // Lưu thông tin đơn hàng để ghi log
        $orderDetails[] = $order['trans_id'];
        
        // Tiến hành xóa
        if ($CMSNT->remove("orders", " `id` = '$id'")) {
            $removeCount++;
        } else {
            $errorCount++;
        }
    }
    
    if ($removeCount > 0) {

        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('đơn hàng') . 
                             ' (' . implode(', ', array_slice($orderDetails, 0, 5)) . 
                             (count($orderDetails) > 5 ? '...' : '') . ')'
        ]);
        
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', check_string($_SERVER['SERVER_NAME']), $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', __('Xóa hàng loạt') . ' ' . $removeCount . ' ' . __('đơn hàng'), $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        
        die(json_encode([
            'status' => 'success', 
            'msg' => __('Đã xóa thành công') . ' ' . $removeCount . ' ' . __('đơn hàng') . 
                   ($errorCount > 0 ? ', ' . $errorCount . ' ' . __('đơn hàng bị lỗi') : "")
        ]));
    }
    
    die(json_encode(['status' => 'error', 'msg' => __('Không có đơn hàng nào được xóa')]));
}

// Bulk delete translates
if($_POST['action'] == 'bulk_delete_translates'){
    if (empty($_POST['token'])) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if (!$getUser = $CMSNT->get_row("SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 AND `admin` != 0 ")) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng đăng nhập để sử dụng tính năng này')]));
    }
    if(checkPermission($getUser['admin'], 'edit_lang') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    $ids = $_POST['ids'];
    if (empty($ids) || !is_array($ids)) {
        die(json_encode(['status' => 'error', 'msg' => __('Vui lòng chọn ít nhất một mục để xóa')]));
    }
    
    $deleted_count = 0;
    foreach($ids as $id) {
        $id = check_string($id);
        if ($CMSNT->num_rows("SELECT * FROM `translate` WHERE `id` = '$id'") > 0) {
            $CMSNT->remove("translate", " `id` = '$id' ");
            $deleted_count++;
        }
    }
    
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => "Bulk Delete $deleted_count Translates."
    ]);
    
    die(json_encode([
        'status' => 'success',
        'msg' => __('Đã xóa thành công') . ' ' . $deleted_count . ' ' . __('bản dịch')
    ]));
}

// Xóa file installer.php
if($_POST['action'] == 'deleteInstallerFile'){
    if(checkPermission($getUser['admin'], 'edit_setting') != true){
        die(json_encode(['status' => 'error', 'msg' => __('Bạn không có quyền sử dụng tính năng này')]));
    }
    
    // Đường dẫn tới file installer.php
    $installer_path = __DIR__ . '/../../installer.php';
    
    // Kiểm tra file có tồn tại không
    if(!file_exists($installer_path)){
        die(json_encode(['status' => 'error', 'msg' => __('File installer.php không tồn tại')]));
    }
    
    // Thử xóa file
    if(unlink($installer_path)){
        // Ghi log hoạt động
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => __('Xóa file installer.php khỏi hệ thống')
        ]);
        
        die(json_encode([
            'status' => 'success', 
            'msg' => __('Đã xóa file installer.php thành công! Bảo mật website đã được tăng cường.')
        ]));
    } else {
        die(json_encode([
            'status' => 'error', 
            'msg' => __('Không thể xóa file installer.php. Vui lòng kiểm tra quyền ghi file hoặc xóa thủ công.')
        ]));
    }
}


die(json_encode([
    'status'    => 'error',
    'msg'       => 'Dữ liệu không hợp lệ'
]));
