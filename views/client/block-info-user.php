<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
?>


<?php if(isset($getUser)):?>
<div class="card border-0 shadow-sm mb-3">
    <!-- <div class="card-header bg-primary bg-soft py-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <img src="<?=base_url('assets/img/icon-user-info.svg');?>"
                    alt="<?=__('Thông tin tài khoản');?>" class="icon-card">
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="card-title mb-0 text-white"><?=__('Tài khoản');?></h4>
                <p class="text-white mb-0 mt-1"><?=__('Thông tin tài khoản của bạn');?></p>
            </div>
        </div>
    </div> -->
    <div class="card-body">
        <div class="row align-items-center mb-4">
            <div class="col-md-3 text-center mb-3 mb-md-0">
                <div class="profile-user position-relative d-inline-block mx-auto">
                    <img src="<?=getGravatarUrl($getUser['email']);?>"
                        class="rounded-circle avatar-xl img-thumbnail user-profile-image no-pointer-events"
                        alt="user-profile-image">
                    <div
                        class="avatar-xs position-absolute bottom-0 end-0 rounded-circle bg-success border border-white">
                        <div class="avatar-title rounded-circle bg-transparent">
                            <i class="ri-checkbox-circle-fill text-white"></i>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9">
                <h5 class="fs-17 mb-1"><?=substr($getUser['username'], 0, 4) . '...';?></h5>
                <!-- <p class="text-muted mb-2">
                    <i class="ri-calendar-line me-1 align-middle"></i><?=__('Tham gia:');?>
                    <?=date('d/m/Y', strtotime($getUser['create_date']));?>
                </p> -->
                <div class="list-group list-group-flush mb-3">
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted"><i class="ri-wallet-3-line me-2 align-middle"></i><?=__('Số dư');?>:</span>
                        <span class="fw-medium"><?=format_currency($getUser['money']);?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted"><i class="ri-arrow-up-circle-line me-2 align-middle"></i><?=__('Tổng nạp');?>:</span>
                        <span class="fw-medium text-success"><?=format_currency($getUser['total_money']);?></span>
                    </div>
                    <div class="list-group-item d-flex justify-content-between align-items-center px-0 py-2">
                        <span class="text-muted"><i class="ri-shield-star-line me-2 align-middle"></i><?=__('Cấp bậc');?>:</span>
                        <span class="fw-medium text-info"><?=getRankNameById(isset($getUser) ? $getUser['rank_id'] : -1);?></span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="javascript:void(0);" onclick="showPaymentGatewaysModal()"
                        class="btn btn-sm btn-primary waves-effect waves-light">
                        <i class="ri-bank-card-line align-bottom me-1"></i> <?=__('Nạp tiền');?>
                    </a>
                    <a href="<?=base_url('client/profile');?>"
                        class="btn btn-sm btn-outline-info waves-effect waves-light">
                        <i class="ri-user-settings-line align-bottom me-1"></i> <?=__('Tài khoản');?>
                    </a>
                </div>
            </div>
        </div>
        <?php
                            // PHP LOGIC FOR RANK PROGRESSION - START
                            $current_user_rank_id = $getUser['rank_id'] ?? null;
                            $current_user_total_money = isset($getUser['total_money']) ? (float)$getUser['total_money'] : 0;

                            $all_ranks_ordered = $CMSNT->get_list("SELECT `id`, `name`, `min` FROM `ranks` WHERE `status` = 1 ORDER BY `min` ASC");

                            $current_rank_details = null;
                            $next_rank_details = null;
                            $min_for_current_rank = 0; 
                            $user_is_highest_rank = false;
                            $calculated_current_rank_index = -1; 

                            if ($all_ranks_ordered && count($all_ranks_ordered) > 0) {
                                // Tìm rank hiện tại của user dựa vào tổng nạp
                                $user_has_achieved_rank = false;
                                for ($i = count($all_ranks_ordered) - 1; $i >= 0; $i--) {
                                    if ($current_user_total_money >= (float)$all_ranks_ordered[$i]['min']) {
                                        $current_rank_details = $all_ranks_ordered[$i];
                                        $min_for_current_rank = (float)$current_rank_details['min'];
                                        $calculated_current_rank_index = $i;
                                        $user_has_achieved_rank = true;
                                        break;
                                    }
                                }
                                
                                // Nếu user chưa đạt rank nào, next rank sẽ là rank đầu tiên
                                if (!$user_has_achieved_rank) {
                                    $next_rank_details = $all_ranks_ordered[0];
                                    $calculated_current_rank_index = -1; // Chưa có rank nào
                                    $min_for_current_rank = 0;
                                } else {
                                    // User đã có rank, tìm rank tiếp theo
                                    if ($calculated_current_rank_index + 1 < count($all_ranks_ordered)) {
                                        $next_rank_details = $all_ranks_ordered[$calculated_current_rank_index + 1];
                                    } else {
                                        $user_is_highest_rank = true; 
                                    }
                                }
                            }

                            $progress_percentage = 0;
                            $amount_needed_for_next_rank = 0;
                            $next_rank_name_display = '';

                            if ($next_rank_details) {
                                $min_for_next_rank = (float)$next_rank_details['min'];
                                $next_rank_name_display = $next_rank_details['name'];
                                
                                if ($user_is_highest_rank) {
                                    $progress_percentage = 100;
                                } else {
                                    // Tính progress dựa trên khoảng từ rank hiện tại đến rank tiếp theo
                                    if ($calculated_current_rank_index == -1) {
                                        // User chưa đạt rank nào, tính từ 0 đến rank đầu tiên
                                        $progress_percentage = ($current_user_total_money / $min_for_next_rank) * 100;
                                    } else {
                                        // User đã có rank, tính từ rank hiện tại đến rank tiếp theo
                                        $money_earned_in_current_tier = $current_user_total_money - $min_for_current_rank;
                                        $span_of_current_tier = $min_for_next_rank - $min_for_current_rank;

                                        if ($span_of_current_tier > 0) {
                                            $progress_percentage = ($money_earned_in_current_tier / $span_of_current_tier) * 100;
                                        } else {
                                            $progress_percentage = ($current_user_total_money >= $min_for_next_rank) ? 100 : 0;
                                        }
                                    }
                                    
                                    $progress_percentage = max(0, min(100, round($progress_percentage)));
                                }
                                
                                $amount_needed_for_next_rank = $min_for_next_rank - $current_user_total_money;
                                if ($amount_needed_for_next_rank < 0) {
                                    $amount_needed_for_next_rank = 0; 
                                }
                            }
                            // PHP LOGIC FOR RANK PROGRESSION - END
                            ?>
        <!-- HTML FOR RANK PROGRESSION - START -->
        <?php if ($next_rank_details || $current_rank_details): ?>
        <div class="progress-info mt-3 pt-3 border-top border-top-dashed">
            <div class="d-flex justify-content-between align-items-center mb-1">
                <span class="fs-13 text-muted"><?=__('Tiến độ lên hạng');?></span>
                <?php if (!$user_is_highest_rank && $next_rank_details): ?>
                <span class="fs-12 badge bg-light text-body"><?=__('Hạng tiếp theo');?>: <strong><?=$next_rank_name_display;?></strong></span>
                <?php endif; ?>
            </div>
            <div class="progress" style="height: 8px;"
                title="<?=sprintf(__('%s%% đã hoàn thành cho hạng tiếp theo'), $progress_percentage);?>">
                <div class="progress-bar bg-success" role="progressbar" style="width: <?=$progress_percentage;?>%;"
                    aria-valuenow="<?=$progress_percentage;?>" aria-valuemin="0" aria-valuemax="100"></div>
            </div>
            <?php if ($user_is_highest_rank): ?>
            <p class="text-muted mt-2 mb-0 fs-12 fst-italic">
                <i
                    class="ri-star-fill text-warning me-1"></i><?=sprintf(__('Chúc mừng! Bạn đã đạt hạng %s, cấp bậc cao nhất của chúng tôi.'), '<strong>'.($current_rank_details['name'] ?? '').'</strong>');?>
            </p>
            <?php elseif ($next_rank_details): ?>
            <?php if ($amount_needed_for_next_rank > 0): ?>
            <p class="text-muted mt-2 mb-0 fs-12">
                <?=sprintf(__('Chỉ cần nạp thêm %s nữa để thăng hạng %s!'), '<strong>'.format_currency($amount_needed_for_next_rank).'</strong>', '<strong>'.$next_rank_name_display.'</strong>');?>
            </p>
            <?php else: ?>
            <p class="text-success mt-2 mb-0 fs-12 fw-medium"><i
                    class="ri-check-double-line me-1"></i><?=sprintf(__('Tuyệt vời! Bạn đã đủ điều kiện để lên hạng %s.'), '<strong>'.$next_rank_name_display.'</strong>');?>
            </p>
            <?php endif; ?>
            <?php else: ?>
            <p class="text-muted mt-2 mb-0 fs-12"><?=__('Không có thông tin về hạng tiếp theo.');?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <!-- HTML FOR RANK PROGRESSION - END -->
    </div>
</div>
<?php else: ?>
<div class="card border-0 shadow-sm mb-3">
    <!-- <div class="card-header bg-primary bg-soft py-3">
        <div class="d-flex align-items-center">
            <div class="flex-shrink-0">
                <img src="<?=base_url('assets/img/icon-user-info.svg');?>"
                    alt="<?=__('Thông tin tài khoản');?>" class="icon-card">
            </div>
            <div class="flex-grow-1 ms-3">
                <h4 class="card-title mb-0 text-white"><?=__('Tài khoản');?></h4>
                <p class="text-white mb-0 mt-1"><?=__('Thông tin tài khoản của bạn');?></p>
            </div>
        </div>
    </div> -->
    <div class="card-body">
        <div class="text-center py-4">
            <div class="avatar-lg mx-auto">
                <div class="avatar-title bg-light-subtle text-primary display-5 rounded-circle">
                    <i class="ri-account-circle-fill"></i>
                </div>
            </div>
            <h5 class="fs-16 mb-2"><?=__('Vui lòng đăng nhập');?></h5>
            <p class="text-muted mb-4">
                <?=__('Đăng nhập để xem thông tin tài khoản và sử dụng dịch vụ của chúng tôi.');?></p>
            <div class="d-flex justify-content-center gap-2">
                <a href="<?=base_url('client/login');?>" class="btn btn-primary waves-effect waves-light">
                    <i class="ri-login-circle-line align-bottom me-1"></i> <?=__('Đăng nhập');?>
                </a>
                <a href="<?=base_url('client/register');?>" class="btn btn-success waves-effect waves-light">
                    <i class="ri-user-add-line align-bottom me-1"></i> <?=__('Đăng ký');?>
                </a>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>