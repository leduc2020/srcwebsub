<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Cấp bậc'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
 

';
$body['footer'] = '
 
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
 
if(checkPermission($getUser['admin'], 'view_rank') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>


<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-crown"></i> <?=__('Cấp bậc');?></h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item active" aria-current="page"><?=__('Cấp bậc');?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <!-- Info Alert -->
                <div class="alert alert-info border-0 mb-4" style="background: linear-gradient(135deg, rgba(13, 202, 240, 0.1), rgba(13, 110, 253, 0.05));">
                    <div class="d-flex align-items-start">
                        <div class="avatar avatar-sm bg-info-transparent rounded me-3 mt-1">
                            <i class="ri-information-line"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="fw-semibold mb-2 text-info">
                                <i class="ri-lightbulb-line me-2"></i><?=__('Tính năng mới: Chi tiết cấp bậc');?>
                            </h6>
                            <p class="mb-2"><?=__('Bây giờ bạn có thể thêm mô tả chi tiết cho từng cấp bậc trong mục "Chi tiết cấp bậc" khi chỉnh sửa.');?></p>
                            <small class="text-muted">
                                <i class="ri-arrow-right-line me-1"></i><?=__('Nội dung sẽ hiển thị trong modal chi tiết khi khách hàng click "Xem chi tiết" trên trang dịch vụ');?>
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('DANH SÁCH CẤP BẬC');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered border-primary" id="rankTable">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="fw-semibold"><?=__('Tên cấp bậc');?></th>
                                        <th class="fw-semibold"><?=__('Liên kết');?></th>
                                        <th class="fw-semibold"><?=__('Tổng nạp');?></th>
                                        <th class="text-center fw-semibold"><?=__('Trạng thái');?></th>
                                        <th class="fw-semibold"><?=__('Cập nhật');?></th>
                                        <th class="text-center fw-semibold"><?=__('Thao tác');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($CMSNT->get_list(" SELECT * FROM `ranks` ") as $row): ?>
                                    <tr class="align-middle">
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="avatar avatar-sm bg-primary-transparent rounded-circle text-primary me-2">
                                                    <i class="fa-solid fa-crown"></i>
                                                </span>
                                                <span class="fw-semibold"><?=$row['name'];?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-light text-dark p-2"><?=$row['target'];?></span></td>
                                        <td>
                                            <span class="badge bg-success-transparent text-success p-2 fs-13">
                                                <i class="fa-solid fa-arrow-up me-1"></i>
                                                <?=format_currency($row['min']);?>+
                                            </span>
                                        </td>
                                        <td class="text-center"><?=display_status_rank($row['status']);?></td>
                                        <td>
                                            <span class="text-muted fs-12"><i class="fa-regular fa-clock me-1"></i><?=$row['updated_at'];?></span>
                                        </td>
                                        <td class="text-center">
                                            <a type="button" href="<?=base_url_admin('rank-edit&id='.$row['id']);?>"
                                                class="btn btn-sm btn-primary" data-bs-toggle="tooltip"
                                                title="<?=__('Edit');?>">
                                                <i class="fa-solid fa-pen-to-square me-1"></i> <?=__('Chỉnh sửa');?>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endforeach?>
                                </tbody>
                            </table>
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
 
