<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Tạo chuyên mục mới'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '

';
$body['footer'] = '
<!-- bs-custom-file-input -->
<script src="'.BASE_URL('public/AdminLTE3/').'plugins/bs-custom-file-input/bs-custom-file-input.min.js"></script>
<!-- Page specific script -->
<script>
$(function () {
  bsCustomFileInput.init();
});
</script> 
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

if(checkPermission($getUser['admin'], 'view_product') != true){
    die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back().location.reload();}</script>');
}
$id = 0;
if(isset($_GET['id'])){
    $id = check_string($_GET['id']);
}
?>
<?php
if (isset($_POST['submit'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("' . __('Không được dùng chức năng này vì đây là trang web demo.') . '")){window.history.back().location.reload();}</script>');
    }
    if(checkPermission($getUser['admin'], 'edit_product') != true){
        die('<script type="text/javascript">if(!alert("' . __('Bạn không có quyền sử dụng tính năng này') . '")){window.history.back();}</script>');
    }
    if ($CMSNT->get_row("SELECT * FROM `categories` WHERE `name` = '".check_string($_POST['name'])."' ")) {
        die('<script type="text/javascript">if(!alert("' . __('Chuyên mục này đã tồn tại trong hệ thống.') . '")){window.history.back().location.reload();}</script>');
    }
    $url_icon = null;
    if (check_img('icon') == true) {
        $rand = random('0123456789QWERTYUIOPASDGHJKLZXCVBNM', 4);
        $ext = pathinfo($_FILES['icon']['name'], PATHINFO_EXTENSION);
        $uploads_dir = 'assets/storage/images/category/'.$rand.'.'.$ext;
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
        die('<script type="text/javascript">if(!alert("' . __('Thêm thành công!') . '")){location.href = "'.base_url_admin('categories').'";}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("' . __('Thêm thất bại!') . '")){window.history.back().location.reload();}</script>');
    }
}
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><a type="button"
                    class="btn btn-dark btn-raised-shadow btn-wave btn-sm me-1"
                    href="<?=base_url_admin('categories');?>"><i class="fa-solid fa-arrow-left"></i></a> <?=__('Tạo chuyên mục');?></h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('TẠO CHUYÊN MỤC');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="mb-4">
                                <label class="form-label" for="stt"><?=__('Ưu tiên:');?></label>
                                <input type="text" class="form-control" value="0" name="stt" required>
                                <small><?=__('Lưu ý: Ưu tiên càng cao, chuyên mục càng hiển thị trên cùng');?></small>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email"><?=__('Tên chuyên mục con:');?>
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="name"
                                        placeholder="<?=__('Nhập tên chuyên mục');?>" required>
                                </div>
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
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email"><?=__('Chuyên mục cha:');?>
                                    <span class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control mb-2" name="parent_id" required>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = 0 ") as $option):?>
                                        <option value="<?=$option['id'];?>"
                                            <?=$id == $option['id'] ? 'selected' : '';?>><?=$option['name'];?></option>
                                        <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `parent_id` = '".$option['id']."' ") as $option1):?>
                                        <option disabled value="<?=$option1['id'];?>">__<?=$option1['name'];?></option>
                                        <?php endforeach?>
                                        <?php endforeach?>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email"><?=__('Icon:');?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <input type="file" class="custom-file-input" name="icon" required>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="example-hf-email"><?=__('Description SEO:');?></label>
                                <div class="col-sm-12">
                                    <textarea class="form-control" rows="3" name="description"></textarea>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email"><?=__('Status:');?> <span
                                        class="text-danger">*</span></label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status" required>
                                        <option value="show">ON</option>
                                        <option value="hide">OFF</option>
                                    </select>
                                </div>
                            </div>
                            <a type="button" class="btn btn-danger" href="<?=base_url_admin('categories');?>"><i
                                    class="fa fa-fw fa-undo me-1"></i> <?=__('Back');?></a>
                            <button type="submit" name="submit" class="btn btn-primary"><i
                                    class="fa fa-fw fa-save me-1"></i> <?=__('Submit');?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>



<?php
require_once(__DIR__.'/footer.php');
?>