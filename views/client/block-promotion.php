<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
?>

<?php if($CMSNT->num_rows(" SELECT * FROM `promotions` ") != 0):?>
<div class="col-xl-12 mb-3">
    <div class="card shadow-sm border-0 overflow-hidden">
        <div class="card-header bg-soft-primary py-3">
            <div class="d-flex align-items-center">
                <div class="flex-shrink-0 me-3">
                    <div class="avatar-sm">
                        <div class="avatar-title rounded-circle bg-primary">
                            <i class="ri-gift-line fs-16"></i>
                        </div>
                    </div>
                </div>
                <div class="flex-grow-1">
                    <h4 class="card-title mb-0"><?=__('Khuyến mãi nạp tiền');?></h4>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-sm table-borderless mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-start ps-3 py-2 fs-13 fw-medium"><?=__('Số tiền nạp lớn hơn hoặc bằng');?>
                            </th>
                            <th class="text-end pe-3 py-2 fs-13 fw-medium"><?=__('Khuyến mãi thêm');?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `promotions` ORDER BY `min` DESC ") as $promotion):?>
                        <tr class="promotion-row">
                            <td class="text-start ps-3 py-2">
                                <div class="d-flex align-items-center">
                                    <span class="bg-soft-primary p-1 rounded me-2">
                                        <i class="ri-money-dollar-circle-line text-primary fs-12"></i>
                                    </span>
                                    <span class="fw-medium fs-13"><?=format_currency($promotion['min']);?></span>
                                </div>
                            </td>
                            <td class="text-end pe-3 py-2">
                                <span class="badge bg-soft-success text-success px-2 py-1 fs-12">
                                    <i class="ri-arrow-up-circle-line me-1"></i>
                                    <?=$promotion['discount'];?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach?>
                    </tbody>
                </table>
            </div>
            <div class="p-2 bg-light border-top">
                <div class="d-flex align-items-center justify-content-center">
                    <i class="ri-information-line text-info me-1 fs-12"></i>
                    <span class="text-muted fs-12"><?=__('Khuyến mãi được áp dụng tự động khi bạn nạp tiền');?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.promotion-row {
    transition: all 0.3s ease;
    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
}

.promotion-row:hover {
    background-color: rgba(13, 110, 253, 0.04);
}

.avatar-xs {
    height: 1.8rem;
    width: 1.8rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-sm {
    height: 2.2rem;
    width: 2.2rem;
    display: flex;
    align-items: center;
    justify-content: center;
}

.avatar-title {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}
</style>
<?php endif?>