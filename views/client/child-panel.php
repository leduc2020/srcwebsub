<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

if($CMSNT->site('child_panel_status') == 0){
    redirect(BASE_URL('order'));
}

$body = [
    'title' => __('Tạo Child Panel').' | '.$CMSNT->site('title')
];
$body['header'] = '
<style>
.step-circle {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: #6c757d;
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    margin: 0 auto;
    transition: all 0.3s ease;
    cursor: pointer;
}

.step-circle.active {
    background: #0d6efd;
    transform: scale(1.1);
}

.step-circle.completed {
    background: #198754;
}

.step-item {
    position: relative;
}

.wizard-step {
    display: none;
    opacity: 0;
    transform: translateX(20px);
    transition: all 0.3s ease;
}

.wizard-step.active {
    display: block;
    opacity: 1;
    transform: translateX(0);
}

.wizard-step.fade-in {
    animation: fadeInSlide 0.3s ease forwards;
}

@keyframes fadeInSlide {
    from {
        opacity: 0;
        transform: translateX(20px);
    }
    to {
        opacity: 1;
        transform: translateX(0);
    }
}

.card-header h6 {
    font-weight: 600;
}

.alert {
    border-radius: 8px;
}

.form-control, .form-select {
    border-radius: 6px;
}

.progress {
    border-radius: 20px;
}

#domain-result {
    min-height: 20px;
}
</style>
<style>
.success-animation {
    margin: 0 auto;
    width: 100px;
    height: 100px;
    position: relative;
}
.success-checkmark {
    width: 80px;
    height: 80px;
    margin: 0 auto;
    position: relative;
}
.success-checkmark .check-icon {
    width: 80px;
    height: 80px;
    position: relative;
    border-radius: 50%;
    box-sizing: content-box;
    border: 4px solid #4CAF50;
}
.success-checkmark .check-icon::before {
    top: 3px;
    left: -2px;
    width: 30px;
    transform-origin: 100% 50%;
    border-radius: 100px 0 0 100px;
}
.success-checkmark .check-icon::after {
    top: 0;
    left: 30px;
    width: 60px;
    transform-origin: 0 50%;
    border-radius: 0 100px 100px 0;
    animation: rotate-circle 4.25s ease-in;
}
.success-checkmark .check-icon::before, .success-checkmark .check-icon::after {
    content: "";
    height: 100px;
    position: absolute;
    background: #FFFFFF;
    transform: rotate(-45deg);
}
.success-checkmark .check-icon .icon-line {
    height: 5px;
    background-color: #4CAF50;
    display: block;
    border-radius: 2px;
    position: absolute;
    z-index: 10;
}
.success-checkmark .check-icon .icon-line.line-tip {
    top: 46px;
    left: 14px;
    width: 25px;
    transform: rotate(45deg);
    animation: icon-line-tip 0.75s;
}
.success-checkmark .check-icon .icon-line.line-long {
    top: 38px;
    right: 8px;
    width: 47px;
    transform: rotate(-45deg);
    animation: icon-line-long 0.75s;
}
@keyframes icon-line-tip {
    0% {
        width: 0;
        left: 1px;
        top: 19px;
    }
    54% {
        width: 0;
        left: 1px;
        top: 19px;
    }
    70% {
        width: 50px;
        left: -8px;
        top: 37px;
    }
    84% {
        width: 17px;
        left: 21px;
        top: 48px;
    }
    100% {
        width: 25px;
        left: 14px;
        top: 46px;
    }
}
@keyframes icon-line-long {
    0% {
        width: 0;
        right: 46px;
        top: 54px;
    }
    65% {
        width: 0;
        right: 46px;
        top: 54px;
    }
    84% {
        width: 55px;
        right: 0px;
        top: 35px;
    }
    100% {
        width: 47px;
        right: 8px;
        top: 38px;
    }
}
</style>
';
$body['footer'] = '';

require_once(__DIR__ . '/../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- Page Title -->
            <div class="row">
                <div class="col-12">
                    <div
                        class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Tạo Child Panel');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?=BASE_URL('');?>"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Tạo Child Panel');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center">
                <div class="col-xl-10">
                    <!-- Header Info -->
                    <div class="card mb-4">
                        <div class="card-body text-center p-4">
                            <div class="row align-items-center">
                                <div class="col-md-8">
                                    <h5 class="mb-2"><?=__('Tạo Child Panel SMM của riêng bạn');?></h5>
                                    <p class="text-muted mb-0">
                                        <?=__('Sở hữu website SMM Panel riêng với tên miền và giao diện tùy chỉnh');?>
                                    </p>
                                </div>
                                <div class="col-md-4 text-md-end">
                                    <div class="mt-3 mt-md-0">
                                        <p class="mb-1 text-muted"><?=__('Phí tạo');?></p>
                                        <h4 class="text-primary mb-0">
                                            <?=($CMSNT->site('child_panel_price') > 0) ? format_currency($CMSNT->site('child_panel_price')).'/1 '.__('Tháng') : __('Miễn phí');?>
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Steps Progress -->
                    <div class="card mb-4">
                        <div class="card-body p-4">
                            <div class="row text-center">
                                <div class="col-3">
                                    <div class="step-item">
                                        <div class="step-circle active" data-step="1">1</div>
                                        <p class="mt-2 mb-0 small fw-medium"><?=__('Chuẩn bị Domain');?></p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-item">
                                        <div class="step-circle" data-step="2">2</div>
                                        <p class="mt-2 mb-0 small fw-medium"><?=__('Trỏ Nameserver');?></p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-item">
                                        <div class="step-circle" data-step="3">3</div>
                                        <p class="mt-2 mb-0 small fw-medium"><?=__('Nhập thông tin');?></p>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="step-item">
                                        <div class="step-circle" data-step="4">4</div>
                                        <p class="mt-2 mb-0 small fw-medium"><?=__('Hoàn thành');?></p>
                                    </div>
                                </div>
                            </div>
                            <!-- Progress bar -->
                            <div class="progress mt-3" style="height: 4px;">
                                <div class="progress-bar bg-primary" id="progress-bar" style="width: 25%;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Wizard Steps Container -->
                    <div class="wizard-container" <?=$aos['fade-right'];?>>
                        <!-- Step 1: Domain Requirements -->
                        <div class="wizard-step active" data-step="1">
                            <div class="card">
                                <div class="card-header text-white">
                                    <h6 class="mb-0"><i
                                            class="ri-global-line me-2"></i><?=__('Bước 1: Chuẩn bị Domain');?></h6>
                                </div>
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><?=__('Domain hợp lệ cần có:');?></h6>
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><i
                                                        class="ri-checkbox-circle-line text-success me-2"></i><?=__('Đã được đăng ký và kích hoạt');?>
                                                </li>
                                                <li class="mb-2"><i
                                                        class="ri-checkbox-circle-line text-success me-2"></i><?=__('Có thể trỏ Nameserver');?>
                                                </li>
                                                <li class="mb-2"><i
                                                        class="ri-checkbox-circle-line text-success me-2"></i><?=__('Không phải domain miễn phí');?>
                                                </li>
                                                <li class="mb-2"><i
                                                        class="ri-checkbox-circle-line text-success me-2"></i><?=__('Không vi phạm bản quyền thương hiệu');?>
                                                </li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h6 class="mb-3"><?=__('Gợi ý nhà cung cấp:');?></h6>
                                            <div class="d-flex flex-wrap gap-2 mb-3">
                                                <span class="badge bg-light text-dark border">Tenten</span>
                                                <span class="badge bg-light text-dark border">Mat Bao</span>
                                                <span class="badge bg-light text-dark border">GoDaddy</span>
                                                <span class="badge bg-light text-dark border">Namecheap</span>
                                            </div>
                                            <div class="alert alert-warning">
                                                <small><i
                                                        class="ri-information-line me-1"></i><?=__('Nếu chưa có domain, hãy đăng ký trước khi tiếp tục');?></small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Domain Check -->
                                    <hr class="my-4">
                                    <h6 class="mb-3"><?=__('Kiểm tra domain của bạn:');?></h6>
                                    <div class="row">
                                        <div class="col-md-8">
                                            <div class="input-group">
                                                <input type="text" class="form-control" id="domain-input"
                                                    placeholder="yoursite.com">
                                                <button class="btn btn-primary" type="button" onclick="checkDomain()">
                                                    <i class="ri-search-line me-1"></i><?=__('Kiểm tra');?>
                                                </button>
                                            </div>
                                            <div id="domain-result" class="mt-2"></div>
                                        </div>
                                    </div>

                                    <!-- Navigation -->
                                    <div class="d-flex justify-content-end mt-4">
                                        <button type="button" class="btn btn-primary" onclick="nextStep()"
                                            id="step1-next" disabled>
                                            <?=__('Tiếp theo');?> <i class="ri-arrow-right-line ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Nameserver -->
                        <div class="wizard-step" data-step="2">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i
                                            class="ri-server-line me-2"></i><?=__('Bước 2: Cấu hình Nameserver');?></h6>
                                </div>
                                <div class="card-body">
                                    <p class="text-muted mb-4">
                                        <?=__('Trỏ nameserver của domain về hệ thống của chúng tôi:');?></p>

                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold"><?=__('Nameserver 1');?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="<?=$CMSNT->site('child_panel_ns1');?>" readonly>
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="copyText('<?=$CMSNT->site('child_panel_ns1');?>')">
                                                        <i class="ri-file-copy-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold"><?=__('Nameserver 2');?></label>
                                                <div class="input-group">
                                                    <input type="text" class="form-control"
                                                        value="<?=$CMSNT->site('child_panel_ns2');?>" readonly>
                                                    <button class="btn btn-outline-secondary" type="button"
                                                        onclick="copyText('<?=$CMSNT->site('child_panel_ns2');?>')">
                                                        <i class="ri-file-copy-line"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="alert alert-info">
                                        <h6 class="alert-heading"><?=__('Cách thực hiện:');?></h6>
                                        <ol class="mb-0">
                                            <li><?=__('Đăng nhập vào tài khoản quản lý domain');?></li>
                                            <li><?=__('Tìm mục DNS/Nameserver Settings');?></li>
                                            <li><?=__('Thay đổi nameserver thành 2 nameserver ở trên');?></li>
                                            <li><?=__('Lưu và chờ 5 phút đến 24h để cập nhật');?></li>
                                        </ol>
                                    </div>

                                    <!-- Confirmation -->
                                    <div class="mt-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="ns-confirm"
                                                onchange="toggleStep2Next()">
                                            <label class="form-check-label" for="ns-confirm">
                                                <?=__('Tôi đã trỏ nameserver cho domain và chờ DNS cập nhật');?>
                                            </label>
                                        </div>
                                    </div>

                                    <!-- Navigation -->
                                    <div class="d-flex justify-content-between mt-4">
                                        <button type="button" class="btn btn-outline-secondary" onclick="prevStep()">
                                            <i class="ri-arrow-left-line me-1"></i><?=__('Quay lại');?>
                                        </button>
                                        <button type="button" class="btn btn-primary" onclick="nextStep()"
                                            id="step2-next" disabled>
                                            <?=__('Tiếp theo');?> <i class="ri-arrow-right-line ms-1"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Create Form -->
                        <div class="wizard-step" data-step="3">
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i
                                            class="ri-settings-3-line me-2"></i><?=__('Bước 3: Thông tin Đăng Nhập');?>
                                    </h6>
                                </div>
                                <div class="card-body">
                                    <form id="childPanelForm" method="POST"
                                        action="<?=BASE_URL('client/child-panel/create');?>"
                                        enctype="multipart/form-data">
                                        <!-- Domain Section -->
                                        <div class="mb-4">
                                            <h6 class="mb-3"><?=__('Thông tin Domain');?></h6>
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <div class="mb-3">
                                                        <label class="form-label"><?=__('Tên miền');?> <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="domain"
                                                            id="final-domain" required readonly>
                                                        <div class="form-text"><?=__('Domain đã kiểm tra từ bước 1');?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Admin Account -->
                                        <div class="mb-4">
                                            <h6 class="mb-3"><?=__('Tài khoản Admin');?></h6>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><?=__('Username');?> <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" name="admin_username"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><?=__('Password');?> <span
                                                                class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <input type="password" class="form-control"
                                                                name="admin_password" id="password" required>
                                                            <button class="btn btn-outline-secondary" type="button"
                                                                onclick="togglePassword()">
                                                                <i class="ri-eye-line" id="eye-icon"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><?=__('Email');?> <span
                                                                class="text-danger">*</span></label>
                                                        <input type="email" class="form-control" name="admin_email"
                                                            required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="mb-3">
                                                        <label class="form-label"><?=__('Số điện thoại');?></label>
                                                        <input type="tel" class="form-control" name="admin_phone">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- Terms -->
                                        <div class="mb-4">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="agree" required>
                                                <label class="form-check-label" for="agree">
                                                    <?=__('Tôi đồng ý với điều khoản sử dụng và xác nhận domain đã trỏ nameserver đúng');?>
                                                </label>
                                            </div>
                                        </div>

                                        <!-- Navigation -->
                                        <div class="d-flex justify-content-between">
                                            <button type="button" class="btn btn-outline-secondary"
                                                onclick="prevStep()">
                                                <i class="ri-arrow-left-line me-1"></i><?=__('Quay lại');?>
                                            </button>
                                            <button type="button" class="btn btn-success btn-lg" onclick="submitForm()"
                                                <?=($getUser['money'] < $CMSNT->site('child_panel_price')) ? 'disabled' : '';?>>
                                                <i class="ri-rocket-line me-2"></i><?=__('Tạo Child Panel');?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Step 4: Success -->
                        <div class="wizard-step" data-step="4">
                            <div class="card">
                                <div class="card-header bg-success text-white">
                                    <h6 class="mb-0"><i class="ri-check-line me-2"></i><?=__('Bước 4: Hoàn thành');?>
                                    </h6>
                                </div>
                                <div class="card-body text-center py-5">
                                    <div class="success-animation">
                                        <div class="success-checkmark">
                                            <div class="check-icon">
                                                <span class="icon-line line-tip"></span>
                                                <span class="icon-line line-long"></span>
                                            </div>
                                        </div>
                                    </div>
                                    <h5 class="mb-3"><?=__('Child Panel đã được tạo thành công!');?></h5>
                                    <p class="text-muted mb-4">
                                        <?=__('Child Panel của bạn đang được thiết lập và sẽ sẵn sàng trong vài phút.');?>
                                    </p>

                                    <div class="d-flex justify-content-center gap-2">
                                        <a href="<?=BASE_URL('client/child-panel');?>" class="btn btn-primary">
                                            <i class="ri-list-check-3 me-1"></i><?=__('Xem danh sách');?>
                                        </a>
                                        <a href="<?=BASE_URL('');?>" class="btn btn-outline-secondary">
                                            <i class="ri-home-line me-1"></i><?=__('Về trang chủ');?>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Existing Panels -->
                    <div class="card mt-4" <?=$aos['fade-up'];?>>
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h6 class="card-title mb-0"><?=__('Child Panel của bạn');?></h6>
                            <a href="<?=BASE_URL('client/child-panel');?>" class="btn btn-outline-primary btn-sm">
                                <i class="ri-refresh-line me-1"></i><?=__('Làm mới');?>
                            </a>
                        </div>
                        <div class="card-body">
                            <?php 
                            $child_panels = $CMSNT->get_list("SELECT * FROM `child_panels` WHERE `user_id` = '".$getUser['id']."' ORDER BY `id` DESC");
                            ?>

                            <?php if($child_panels && count($child_panels) > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th><?=__('Domain');?></th>
                                            <th><?=__('Trạng thái');?></th>
                                            <th><?=__('Ngày tạo');?></th>
                                            <th><?=__('Ngày hết hạn');?></th>
                                            <th><?=__('Thao tác');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        foreach($child_panels as $child_panel):
                                        ?>
                                        <tr>
                                            <td><?=$child_panel['domain'];?></td>
                                            <td><?=display_childpanel_status($child_panel['status']);?></td>
                                            <td>
                                                <span
                                                    class="text-muted small"><?=date('d/m/Y', strtotime($child_panel['created_at']));?></span><br>
                                                <span class="text-muted"
                                                    style="font-size: 11px;"><?=date('H:i:s', strtotime($child_panel['created_at']));?></span>
                                            </td>
                                            <td>
                                                <?php if($child_panel['expired_at']): ?>
                                                <span class="text-muted small" data-toggle="tooltip"
                                                    data-placement="bottom"
                                                    title="<?=timeRemaining(strtotime($child_panel['expired_at']));?>"><?=date('d/m/Y', strtotime($child_panel['expired_at']));?></span><br>
                                                <span class="text-muted"
                                                    style="font-size: 11px;"><?=date('H:i:s', strtotime($child_panel['expired_at']));?></span>
                                                <?php else: ?>
                                                <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <div class="btn-list">
                                                    <button type="button" class="btn btn-primary btn-sm"
                                                        onclick="renewChildPanel(<?=$child_panel['id'];?>)">
                                                        <i class="ri-refresh-line me-1"></i><?=__('Gia hạn');?>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-sm"
                                                        onclick="cancelChildPanel(<?=$child_panel['id'];?>)">
                                                        <i class="ri-close-circle-line me-1"></i><?=__('Yêu cầu hủy');?>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="ri-inbox-line display-4 text-muted mb-3"></i>
                                <h6 class="text-muted"><?=__('Chưa có Child Panel nào');?></h6>
                                <p class="text-muted mb-0"><?=__('Hãy tạo Child Panel đầu tiên của bạn');?></p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
let currentStep = 1;
let validatedDomain = '';

// Initialize wizard
document.addEventListener('DOMContentLoaded', function() {
    showStep(1);
});

function showStep(step) {
    // Hide all steps
    document.querySelectorAll('.wizard-step').forEach(s => {
        s.classList.remove('active', 'fade-in');
    });

    // Show current step with animation
    const currentStepEl = document.querySelector(`.wizard-step[data-step="${step}"]`);
    if (currentStepEl) {
        currentStepEl.classList.add('active');
    }

    // Update step indicators
    document.querySelectorAll('.step-circle').forEach((circle, index) => {
        const stepNum = index + 1;
        circle.classList.remove('active', 'completed');

        if (stepNum < step) {
            circle.classList.add('completed');
        } else if (stepNum === step) {
            circle.classList.add('active');
        }
    });

    // Update progress bar
    const progressPercent = (step / 4) * 100;
    document.getElementById('progress-bar').style.width = progressPercent + '%';

    currentStep = step;
}

function nextStep() {
    if (validateCurrentStep()) {
        if (currentStep < 4) {
            showStep(currentStep + 1);
        }
    }
}

function prevStep() {
    if (currentStep > 1) {
        showStep(currentStep - 1);
    }
}

function validateCurrentStep() {
    switch (currentStep) {
        case 1:
            if (!validatedDomain) {
                showMessage('<?=__('Vui lòng kiểm tra domain trước khi tiếp tục');?>', 'error');
                return false;
            }
            return true;

        case 2:
            const nsConfirm = document.getElementById('ns-confirm');
            if (!nsConfirm.checked) {
                showMessage('<?=__('Vui lòng xác nhận đã trỏ nameserver cho domain');?>', 'error');
                return false;
            }
            return true;

        case 3:
            return validateForm();
    }
    return true;
}

function checkDomain() {
    const domainInput = document.getElementById('domain-input');
    const domain = domainInput.value.trim();
    const resultDiv = document.getElementById('domain-result');

    if (!domain) {
        showMessage('<?=__('Vui lòng nhập tên miền');?>', 'error');
        return;
    }

    // Show loading
    resultDiv.innerHTML =
        '<div class="text-primary d-flex align-items-center"><span class="spinner-border spinner-border-sm me-2" role="status"></span><?=__('Đang kiểm tra...');?></div>';

    // Simulate API call
    setTimeout(() => {
        const domainRegex = /^[a-zA-Z0-9][a-zA-Z0-9-_.]*[a-zA-Z0-9]*\.[a-zA-Z]{2,}$/;
        if (!domainRegex.test(domain)) {
            resultDiv.innerHTML =
                '<div class="alert alert-danger"><i class="ri-close-circle-line me-2"></i><?=__('Định dạng domain không hợp lệ');?></div>';
            document.getElementById('step1-next').disabled = true;
            validatedDomain = '';
            return;
        }

        // Success
        resultDiv.innerHTML =
            '<div class="alert alert-success"><i class="ri-check-circle-line me-2"></i><?=__('Domain hợp lệ! Bạn có thể tiếp tục.');?></div>';
        validatedDomain = domain;
        document.getElementById('step1-next').disabled = false;

        // Auto-fill in step 3
        document.getElementById('final-domain').value = domain;
    }, 1500);
}

function toggleStep2Next() {
    const checkbox = document.getElementById('ns-confirm');
    document.getElementById('step2-next').disabled = !checkbox.checked;
}

function validateForm() {
    const form = document.getElementById('childPanelForm');
    const username = form.admin_username.value.trim();
    const password = form.admin_password.value;
    const email = form.admin_email.value.trim();
    const agree = form.agree.checked;

    if (!username || !password || !email) {
        showMessage('<?=__('Vui lòng điền đầy đủ thông tin bắt buộc');?>', 'error');
        return false;
    }

    if (!agree) {
        showMessage('<?=__('Vui lòng đồng ý với điều khoản sử dụng');?>', 'error');
        return false;
    }

    if (password.length < 6) {
        showMessage('<?=__('Mật khẩu phải có ít nhất 6 ký tự');?>', 'error');
        return false;
    }

    if (!/^[a-zA-Z0-9_.-]+$/.test(username)) {
        showMessage('<?=__('Username chỉ được chứa chữ, số, dấu gạch dưới và dấu chấm');?>', 'error');
        return false;
    }

    return true;
}

function submitForm() {
    if (!validateForm()) {
        return;
    }

    const submitBtn = event.target;
    const originalText = submitBtn.innerHTML;

    // Show loading state
    submitBtn.innerHTML =
        '<span class="spinner-border spinner-border-sm me-1" role="status"></span><?=__('Đang tạo...');?>';
    submitBtn.disabled = true;

    // Prepare form data
    const formData = new FormData();
    formData.append('action', 'CreateChildPanel');
    formData.append('token', '<?=$getUser['token'];?>');
    formData.append('domain', document.getElementById('final-domain').value);
    formData.append('admin_username', document.querySelector('input[name="admin_username"]').value);
    formData.append('admin_password', document.querySelector('input[name="admin_password"]').value);
    formData.append('admin_email', document.querySelector('input[name="admin_email"]').value);
    formData.append('admin_phone', document.querySelector('input[name="admin_phone"]').value || '');

    // Send AJAX request
    fetch('<?=BASE_URL('ajaxs/client/child-panel.php');?>', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                showMessage(data.msg, 'success');
                // Move to success step
                setTimeout(() => {
                    showStep(4);
                }, 1500);
            } else {
                showMessage(data.msg, 'error');
                // Restore button
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
            // Restore button
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
}


function copyText(text) {
    navigator.clipboard.writeText(text).then(() => {
        showMessage('<?=__('Đã sao chép!');?>', 'success');
    });
}

function togglePassword() {
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eye-icon');

    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        eyeIcon.className = 'ri-eye-off-line';
    } else {
        passwordInput.type = 'password';
        eyeIcon.className = 'ri-eye-line';
    }
}

// Allow clicking on step circles to navigate (if step is accessible)
document.querySelectorAll('.step-circle').forEach((circle, index) => {
    circle.addEventListener('click', () => {
        const targetStep = index + 1;
        if (targetStep <= currentStep || circle.classList.contains('completed')) {
            showStep(targetStep);
        }
    });
});

// Cancel child panel function
function renewChildPanel(id) {
    // Get child panel info from the table row
    const row = event.target.closest('tr');
    const domain = row.cells[0].textContent.trim();
    const status = row.cells[1].textContent.trim();
    const expiredAt = row.cells[3].querySelector('span').textContent.trim();

    // Fill modal with child panel info
    document.getElementById('renewChildPanelId').value = id;
    document.getElementById('renewDomainInfo').textContent = domain;
    document.getElementById('renewStatusInfo').innerHTML = status;
    document.getElementById('renewExpiredInfo').textContent = expiredAt;

    // Calculate renewal options
    updateRenewalOptions();

    // Reset form
    document.getElementById('renewChildPanelForm').reset();
    document.getElementById('renewChildPanelId').value = id;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('renewChildPanelModal'));
    modal.show();
}

function updateRenewalOptions() {
    const monthlyPrice = <?=$CMSNT->site('child_panel_price');?>;
    const userBalance = <?=$getUser['money'];?>;

    // Update pricing display
    document.getElementById('price1Month').textContent = '<?=format_currency($CMSNT->site('child_panel_price'));?>';
    document.getElementById('price3Month').textContent = '<?=format_currency($CMSNT->site('child_panel_price') * 3);?>';
    document.getElementById('price6Month').textContent = '<?=format_currency($CMSNT->site('child_panel_price') * 6);?>';
    document.getElementById('price12Month').textContent =
        '<?=format_currency($CMSNT->site('child_panel_price') * 12);?>';

    // Check if user has enough balance for each option
    const options = [{
            id: '1month',
            price: monthlyPrice
        },
        {
            id: '3month',
            price: monthlyPrice * 3
        },
        {
            id: '6month',
            price: monthlyPrice * 6
        },
        {
            id: '12month',
            price: monthlyPrice * 12
        }
    ];

    options.forEach(option => {
        const radio = document.getElementById(option.id);
        const label = radio.closest('.form-check');

        if (userBalance < option.price) {
            radio.disabled = true;
            label.classList.add('text-muted');
            label.querySelector('.badge').textContent = '<?=__('Không đủ số dư');?>';
            label.querySelector('.badge').className = 'badge bg-danger ms-2';
        } else {
            radio.disabled = false;
            label.classList.remove('text-muted');
            if (option.id === '12month') {
                label.querySelector('.badge').textContent = '<?=__('Tiết kiệm nhất');?>';
                label.querySelector('.badge').className = 'badge bg-success ms-2';
            } else if (option.id === '6month') {
                label.querySelector('.badge').textContent = '<?=__('Phổ biến');?>';
                label.querySelector('.badge').className = 'badge bg-primary ms-2';
            }
        }
    });
}

function cancelChildPanel(id) {
    // Get child panel info from the table row
    const row = event.target.closest('tr');
    const domain = row.cells[0].textContent.trim();
    const status = row.cells[1].textContent.trim();

    // Fill modal with child panel info
    document.getElementById('cancelChildPanelId').value = id;
    document.getElementById('cancelDomainInfo').textContent = domain;
    document.getElementById('cancelStatusInfo').innerHTML = status;

    // Reset form
    document.getElementById('cancelChildPanelForm').reset();
    document.getElementById('cancelChildPanelId').value = id;

    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('cancelChildPanelModal'));
    modal.show();
}

// Submit renewal request
document.addEventListener('DOMContentLoaded', function() {
    const renewBtn = document.getElementById('submitRenewalRequest');
    if (renewBtn) {
        renewBtn.addEventListener('click', function() {
            const form = document.getElementById('renewChildPanelForm');
            const selectedPeriod = form.querySelector('input[name="renewal_period"]:checked');
            const confirm = document.getElementById('renewConfirm').checked;

            // Validate form
            if (!selectedPeriod) {
                showMessage('<?=__('Vui lòng chọn thời gian gia hạn');?>', 'error');
                return;
            }

            if (!confirm) {
                showMessage('<?=__('Vui lòng xác nhận gia hạn Child Panel');?>', 'error');
                return;
            }

            // Show loading
            const originalText = renewBtn.innerHTML;
            renewBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status"></span><?=__('Đang xử lý...');?>';
            renewBtn.disabled = true;

            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'RenewChildPanel');
            formData.append('token', '<?=isset($getUser) ? $getUser['token'] : '';?>');
            formData.append('child_panel_id', document.getElementById('renewChildPanelId').value);
            formData.append('renewal_period', selectedPeriod.value);

            // Send request
            fetch('<?=BASE_URL('ajaxs/client/child-panel.php');?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showMessage(data.message, 'success');
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'renewChildPanelModal'));
                        modal.hide();
                        // Reload page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showMessage(data.message, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
                })
                .finally(() => {
                    // Restore button
                    renewBtn.innerHTML = originalText;
                    renewBtn.disabled = false;
                });
        });
    }

    // Submit cancel request
    const submitBtn = document.getElementById('submitCancelRequest');
    if (submitBtn) {
        submitBtn.addEventListener('click', function() {
            const form = document.getElementById('cancelChildPanelForm');
            const reason = document.getElementById('cancelReason').value;
            const description = document.getElementById('cancelDescription').value;
            const confirm = document.getElementById('cancelConfirm').checked;

            // Validate form
            if (!reason) {
                showMessage('<?=__('Vui lòng chọn lý do hủy');?>', 'error');
                return;
            }

            if (!description.trim()) {
                showMessage('<?=__('Vui lòng mô tả chi tiết lý do hủy');?>', 'error');
                return;
            }

            if (!confirm) {
                showMessage('<?=__('Vui lòng xác nhận hiểu về việc hủy Child Panel');?>', 'error');
                return;
            }

            // Show loading
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML =
                '<span class="spinner-border spinner-border-sm me-1" role="status"></span><?=__('Đang gửi...');?>';
            submitBtn.disabled = true;

            // Prepare form data for createTicket action
            const formData = new FormData();
            formData.append('action', 'createTicket');
            formData.append('token', '<?=isset($getUser) ? $getUser['token'] : '';?>');
            formData.append('subject', '<?=__('Yêu cầu hủy Child Panel: ');?>' + document
                .getElementById('cancelDomainInfo').textContent);
            formData.append('category', 'child_panel_cancel');
            formData.append('content', '**<?=__('Lý do hủy');?>:** ' + reason +
                '\n\n**<?=__('Mô tả chi tiết');?>:**\n' + description +
                '\n\n**<?=__('Mức độ ưu tiên');?>:** ' + document.getElementById('cancelPriority')
                .value);

            // Send request
            fetch('<?=BASE_URL('ajaxs/client/ticket.php');?>', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        showMessage(data.msg, 'success');
                        // Close modal
                        const modal = bootstrap.Modal.getInstance(document.getElementById(
                            'cancelChildPanelModal'));
                        modal.hide();
                        // Reload page after 2 seconds
                        setTimeout(() => {
                            location.reload();
                        }, 2000);
                    } else {
                        showMessage(data.msg, 'error');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showMessage('<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
                })
                .finally(() => {
                    // Restore button
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                });
        });
    }
});
</script>

<!-- Modal Gia hạn Child Panel -->
<div class="modal fade" id="renewChildPanelModal" tabindex="-1" aria-labelledby="renewChildPanelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="renewChildPanelModalLabel">
                    <i class="ri-refresh-line me-2"></i><?=__('Gia hạn Child Panel');?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-0 mb-4">
                    <div class="d-flex align-items-start">
                        <i class="ri-information-line text-info me-2 fs-5"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2"><?=__('Thông tin gia hạn');?></h6>
                            <p class="mb-0">
                                <?=__('Chọn thời gian gia hạn phù hợp. Thời gian gia hạn sẽ được cộng thêm vào ngày hết hạn hiện tại.');?>
                            </p>
                        </div>
                    </div>
                </div>

                <form id="renewChildPanelForm">
                    <input type="hidden" id="renewChildPanelId" name="child_panel_id">

                    <!-- Child Panel Info -->
                    <div class="mb-4">
                        <label class="form-label fw-medium"><?=__('Thông tin Child Panel');?></label>
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-4">
                                        <small class="text-muted"><?=__('Domain');?>:</small>
                                        <div class="fw-medium" id="renewDomainInfo">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted"><?=__('Trạng thái');?>:</small>
                                        <div id="renewStatusInfo">-</div>
                                    </div>
                                    <div class="col-md-4">
                                        <small class="text-muted"><?=__('Hết hạn');?>:</small>
                                        <div class="fw-medium text-warning" id="renewExpiredInfo">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Balance Info -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded">
                            <span class="fw-medium"><?=__('Số dư hiện tại');?>:</span>
                            <span class="h5 mb-0 text-primary"><?=format_currency($getUser['money']);?></span>
                        </div>
                    </div>

                    <!-- Renewal Options -->
                    <div class="mb-4">
                        <label class="form-label fw-medium"><?=__('Chọn thời gian gia hạn');?> <span
                                class="text-danger">*</span></label>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="radio" name="renewal_period" id="1month"
                                        value="1">
                                    <label class="form-check-label w-100" for="1month">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium"><?=__('1 Tháng');?></div>
                                                <small class="text-muted"><?=__('Gia hạn cơ bản');?></small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary" id="price1Month">
                                                    <?=format_currency($CMSNT->site('child_panel_price'));?></div>
                                                <span class="badge bg-light text-dark ms-2"><?=__('Cơ bản');?></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="radio" name="renewal_period" id="3month"
                                        value="3">
                                    <label class="form-check-label w-100" for="3month">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium"><?=__('3 Tháng');?></div>
                                                <small class="text-muted"><?=__('Tiết kiệm hơn');?></small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary" id="price3Month">
                                                    <?=format_currency($CMSNT->site('child_panel_price') * 3);?></div>
                                                <span class="badge bg-warning ms-2"><?=__('Khuyến nghị');?></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="radio" name="renewal_period" id="6month"
                                        value="6">
                                    <label class="form-check-label w-100" for="6month">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium"><?=__('6 Tháng');?></div>
                                                <small class="text-muted"><?=__('Lựa chọn phổ biến');?></small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary" id="price6Month">
                                                    <?=format_currency($CMSNT->site('child_panel_price') * 6);?></div>
                                                <span class="badge bg-primary ms-2"><?=__('Phổ biến');?></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <div class="form-check border rounded p-3 h-100">
                                    <input class="form-check-input" type="radio" name="renewal_period" id="12month"
                                        value="12">
                                    <label class="form-check-label w-100" for="12month">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="fw-medium"><?=__('12 Tháng');?></div>
                                                <small class="text-muted"><?=__('Tiết kiệm tối đa');?></small>
                                            </div>
                                            <div class="text-end">
                                                <div class="fw-bold text-primary" id="price12Month">
                                                    <?=format_currency($CMSNT->site('child_panel_price') * 12);?></div>
                                                <span class="badge bg-success ms-2"><?=__('Tiết kiệm nhất');?></span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Confirmation -->
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="renewConfirm" required>
                        <label class="form-check-label" for="renewConfirm">
                            <?=__('Tôi xác nhận gia hạn Child Panel với thời gian đã chọn và số tiền sẽ được trừ từ tài khoản');?>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i><?=__('Đóng');?>
                </button>
                <button type="button" class="btn btn-primary" id="submitRenewalRequest">
                    <i class="ri-refresh-line me-1"></i><?=__('Xác nhận gia hạn');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tạo Ticket Hủy Child Panel -->
<div class="modal fade" id="cancelChildPanelModal" tabindex="-1" aria-labelledby="cancelChildPanelModalLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cancelChildPanelModalLabel">
                    <i class="ri-close-circle-line me-2"></i><?=__('Yêu cầu hủy Child Panel');?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                    aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-0 mb-4">
                    <div class="d-flex align-items-start">
                        <i class="ri-error-warning-line text-warning me-2 fs-5"></i>
                        <div class="flex-grow-1">
                            <h6 class="alert-heading mb-2"><?=__('Lưu ý quan trọng');?></h6>
                            <p class="mb-0">
                                <?=__('Việc hủy Child Panel sẽ xóa toàn bộ dữ liệu và không thể khôi phục. Vui lòng cân nhắc kỹ trước khi gửi yêu cầu.');?>
                            </p>
                        </div>
                    </div>
                </div>

                <form id="cancelChildPanelForm">
                    <input type="hidden" id="cancelChildPanelId" name="child_panel_id">

                    <div class="mb-3">
                        <label class="form-label fw-medium"><?=__('Thông tin Child Panel');?></label>
                        <div class="card bg-light">
                            <div class="card-body py-3">
                                <div class="row">
                                    <div class="col-md-6">
                                        <small class="text-muted"><?=__('Domain');?>:</small>
                                        <div class="fw-medium" id="cancelDomainInfo">-</div>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted"><?=__('Trạng thái');?>:</small>
                                        <div id="cancelStatusInfo">-</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cancelReason" class="form-label fw-medium"><?=__('Lý do hủy');?> <span
                                class="text-danger">*</span></label>
                        <select class="form-select" id="cancelReason" name="reason" required>
                            <option value=""><?=__('Chọn lý do hủy');?></option>
                            <option value="không_sử_dụng"><?=__('Không còn nhu cầu sử dụng');?></option>
                            <option value="lỗi_kỹ_thuật"><?=__('Gặp lỗi kỹ thuật không thể khắc phục');?></option>
                            <option value="chuyển_nhà_cung_cấp"><?=__('Chuyển sang nhà cung cấp khác');?></option>
                            <option value="chi_phí_cao"><?=__('Chi phí quá cao');?></option>
                            <option value="khác"><?=__('Lý do khác');?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="cancelDescription" class="form-label fw-medium"><?=__('Mô tả chi tiết');?> <span
                                class="text-danger">*</span></label>
                        <textarea class="form-control" id="cancelDescription" name="description" rows="4"
                            placeholder="<?=__('Vui lòng mô tả chi tiết lý do hủy Child Panel và các vấn đề gặp phải (nếu có)...');?>"
                            required></textarea>
                        <div class="form-text">
                            <?=__('Thông tin chi tiết sẽ giúp chúng tôi xử lý yêu cầu nhanh chóng hơn');?></div>
                    </div>

                    <div class="mb-3">
                        <label for="cancelPriority" class="form-label fw-medium"><?=__('Mức độ ưu tiên');?></label>
                        <select class="form-select" id="cancelPriority" name="priority">
                            <option value="normal"><?=__('Bình thường');?></option>
                            <option value="high"><?=__('Cao');?></option>
                            <option value="urgent"><?=__('Khẩn cấp');?></option>
                        </select>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="cancelConfirm" required>
                        <label class="form-check-label" for="cancelConfirm">
                            <?=__('Tôi hiểu rằng việc hủy Child Panel sẽ xóa toàn bộ dữ liệu và không thể khôi phục');?>
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="ri-close-line me-1"></i><?=__('Đóng');?>
                </button>
                <button type="button" class="btn btn-danger" id="submitCancelRequest">
                    <i class="ri-send-plane-line me-1"></i><?=__('Gửi yêu cầu hủy');?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>