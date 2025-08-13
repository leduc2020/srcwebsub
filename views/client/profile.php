<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

if (!function_exists('get_friendly_user_agent')) {
    function get_friendly_user_agent($ua_string) {
        if (empty($ua_string)) {
            return __('Thiết bị không xác định');
        }

        $ua_string_lower = strtolower($ua_string);

        // Phones
        if (strpos($ua_string_lower, 'iphone') !== false) {
            return __('Điện thoại iPhone');
        }
        if (strpos($ua_string_lower, 'android') !== false && strpos($ua_string_lower, 'mobile') !== false) {
            return __('Điện thoại Android');
        }
        if (strpos($ua_string_lower, 'windows phone') !== false) {
            return __('Điện thoại Windows');
        }
        
        // Tablets
        if (strpos($ua_string_lower, 'ipad') !== false) {
            return __('Máy tính bảng iPad');
        }
        if (strpos($ua_string_lower, 'android') !== false) {
            return __('Máy tính bảng Android');
        }

        // Desktops
        if (strpos($ua_string_lower, 'windows') !== false) {
            return __('Máy tính Windows');
        }
        if (strpos($ua_string_lower, 'macintosh') !== false || strpos($ua_string_lower, 'mac os x') !== false) {
            return __('Máy tính Mac');
        }
        if (strpos($ua_string_lower, 'linux') !== false) {
            return __('Máy tính Linux');
        }
        
        return __('Thiết bị không xác định');
    }
}


$body = [
    'title' => __('Profile').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '

';
$body['footer'] = '

';
require_once(__DIR__.'/../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">

            <div class="position-relative mx-n4 mt-n4">
                <div class="profile-wid-bg profile-setting-img">
                    <img src="<?=BASE_URL('public/client/assets/');?>images/profile-bg.jpg" class="profile-wid-img"
                        alt="">
                </div>
            </div>
            <div class="row">
                <div class="col-xxl-5 mt-n5 mb-5">
                    <?php require_once __DIR__.'/block-info-user.php';?>
                </div>
                <!--end col-->
                <div class="col-xxl-7 mt-n5">
                    <div class="card">
                        <div class="card-header">
                            <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                        <i class="fas fa-home"></i> <?=__('Thông tin cá nhân');?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                        <i class="far fa-user"></i> <?=__('Đổi mật khẩu');?>
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" data-bs-toggle="tab" href="#security" role="tab">
                                        <i class="far fa-envelope"></i> <?=__('Bảo mật');?>
                                    </a>
                                </li>
                            </ul>
                        </div>
                        <div class="card-body p-4">
                            <div class="tab-content">
                                <div class="tab-pane" id="personalDetails" role="tabpanel">
                                    <form action="javascript:void(0);" id="profileForm"
                                        onsubmit="updateProfile(); return false;">
                                        <div class="row">
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="username" class="form-label"><?=__('Tên đăng nhập');?></label>
                                                    <input type="text" class="form-control bg-light" readonly
                                                        value="<?=$getUser['username'];?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="email" class="form-label"><?=__('Email');?></label>
                                                    <input type="email" class="form-control bg-light" readonly
                                                        value="<?=$getUser['email'];?>">
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="fullname"
                                                        class="form-label"><?=__('Họ và tên');?></label>
                                                    <input type="text" class="form-control" id="fullname"
                                                        placeholder="<?=__('Vui lòng nhập họ và tên');?>"
                                                        value="<?=$getUser['fullname'];?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="phone"
                                                        class="form-label"><?=__('Số điện thoại');?></label>
                                                    <input type="text" class="form-control" id="phone"
                                                        placeholder="<?=__('Vui lòng số điện thoại');?>"
                                                        value="<?=$getUser['phone'];?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-6">
                                                <div class="mb-3">
                                                    <label for="registrationDate"
                                                        class="form-label"><?=__('Thời gian đăng ký tài khoản');?></label>
                                                    <input type="text" class="form-control bg-light" readonly
                                                        value="<?=$getUser['create_date'];?>">
                                                </div>
                                            </div>
                                            <!--end col-->

                                            <div class="col-lg-12">
                                                <div class="hstack gap-2 justify-content-end">
                                                    <button type="submit" class="btn btn-dark waves-effect waves-light"
                                                        id="updateProfileBtn">
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            role="status" aria-hidden="true"></span>
                                                        <span class="btn-text"><?=__('Cập nhật');?></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                </div>
                                <!--end tab-pane-->
                                <div class="tab-pane" id="changePassword" role="tabpanel">
                                    <form action="javascript:void(0);">
                                        <div class="row g-2">
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="oldpasswordInput"
                                                        class="form-label"><?=__('Mật khẩu hiện tại');?></label>
                                                    <input type="password" class="form-control" id="oldpasswordInput"
                                                        placeholder="<?=__('Vui lòng nhập mật khẩu hiện tại');?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="newpasswordInput"
                                                        class="form-label"><?=__('Mật khẩu mới');?></label>
                                                    <input type="password" class="form-control" id="newpasswordInput"
                                                        placeholder="<?=__('Vui lòng nhập mật khẩu mới');?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-4">
                                                <div>
                                                    <label for="confirmpasswordInput"
                                                        class="form-label"><?=__('Xác nhận mật khẩu');?></label>
                                                    <input type="password" class="form-control"
                                                        id="confirmpasswordInput"
                                                        placeholder="<?=__('Vui lòng xác nhận mật khẩu');?>">
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="mb-3">
                                                    <a href="<?=base_url('client/forgot-password');?>"
                                                        class="link-primary text-decoration-underline"><?=__('Quên mật khẩu');?>?</a>
                                                </div>
                                            </div>
                                            <!--end col-->
                                            <div class="col-lg-12">
                                                <div class="text-end">
                                                    <button type="submit" class="btn btn-dark waves-effect waves-light"
                                                        id="changePasswordBtn"
                                                        onclick="changePassword(); return false;">
                                                        <span class="spinner-border spinner-border-sm d-none"
                                                            role="status" aria-hidden="true"></span>
                                                        <span class="btn-text"><?=__('Đổi mật khẩu');?></span>
                                                    </button>
                                                </div>
                                            </div>
                                            <!--end col-->
                                        </div>
                                        <!--end row-->
                                    </form>
                                    <div class="mt-4 mb-3 border-bottom pb-2">
                                        <div class="float-end">
                                            <a href="javascript:void(0);" class="link-primary" onclick="logoutAllSessions()"><?=__('Đăng xuất tất cả');?></a>
                                        </div>
                                        <h5 class="card-title"><?=__('Phiên hoạt động');?></h5>
                                    </div>
                                    <div style="max-height: 400px; overflow-y: auto;">
                                    <?php 
                                    $sessions = $CMSNT->get_list("SELECT * FROM `active_sessions` WHERE `user_id` = '".$getUser['id']."' ORDER BY `last_activity` DESC");
                                    foreach($sessions as $session):
                                        // Xác định icon dựa vào user agent
                                        $icon = 'ri-computer-line'; // mặc định là máy tính
                                        if(strpos(strtolower($session['user_agent']), 'mobile') !== false || 
                                           strpos(strtolower($session['user_agent']), 'android') !== false || 
                                           strpos(strtolower($session['user_agent']), 'iphone') !== false) {
                                            $icon = 'ri-smartphone-line';
                                        } else if(strpos(strtolower($session['user_agent']), 'ipad') !== false || 
                                                strpos(strtolower($session['user_agent']), 'tablet') !== false) {
                                            $icon = 'ri-tablet-line';
                                        }
                                    ?>
                                    <div class="d-flex align-items-center mb-3">
                                        <div class="flex-shrink-0 avatar-sm">
                                            <div
                                                class="avatar-title bg-light text-primary rounded-3 fs-18 material-shadow">
                                                <i class="<?=$icon;?>"></i>
                                            </div>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <h6><?=get_device_by_user_agent($session['user_agent']);?></h6>
                                            <p class="text-muted mb-0">
                                                <?=__('Địa chỉ IP');?>: <?=$session['ip_address'];?> -
                                                <?=__('Hoạt động');?>
                                                <?=timeAgo(strtotime($session['last_activity']));?>
                                            </p>
                                        </div>
                                        <div>
                                            <?php if($session['device_token'] != getOrCreateDeviceToken()):?>
                                            <a href="javascript:void(0);"
                                                onclick="logoutSession('<?=$session['id'];?>');" class="text-danger">
                                                <?=__('Đăng xuất');?>
                                            </a>
                                            <?php else:?>
                                            <span class="badge bg-success"><?=__('Phiên hiện tại');?></span>
                                            <?php endif?>
                                        </div>
                                    </div>
                                    <?php endforeach?>
                                    </div>
                                </div>
                                <!--end tab-pane-->

                                <div class="tab-pane" id="security" role="tabpanel">

                                    <div class="mb-3">
                                        <h5 class="card-title text-decoration-underline mb-3">
                                            <?=__('Cấu hình bảo mật:');?>
                                        </h5>
                                        <ul class="list-unstyled mb-0">
                                            <li class="d-flex">
                                                <div class="flex-grow-1">
                                                    <label for="status_otp_mail"
                                                        class="form-check-label fs-14"><?=__('Xác thực hai yếu tố');?></label>
                                                    <p class="text-muted">
                                                        <?=__('Xác minh đăng nhập bằng Google Authenticator');?></p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <a href="javascript:void(0);" class="btn btn-sm btn-primary"
                                                        id="btnToggle2FA"><?=__('Bật');?>
                                                        <?=__('Xác thực hai yếu tố');?></a>
                                                </div>
                                            </li>
                                            <li class="d-flex">
                                                <div class="flex-grow-1">
                                                    <label for="status_noti_login_to_mail"
                                                        class="form-check-label fs-14"><?=__('Thông báo đăng nhập qua email');?></label>
                                                    <p class="text-muted">
                                                        <?=__('Nhận thông báo khi tài khoản của bạn đăng nhập từ thiết bị mới');?>
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="status_noti_login_to_mail"
                                                            <?=$getUser['status_noti_login_to_mail'] == 1 ? 'checked' : ''?> />
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mt-2">
                                                <div class="flex-grow-1">
                                                    <label class="form-check-label fs-14" for="status_otp_mail">
                                                        <?=__('Xác thực OTP qua email');?>
                                                    </label>
                                                    <p class="text-muted">
                                                        <?=__('Yêu cầu mã OTP gửi qua email khi đăng nhập từ thiết bị mới');?>
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="form-check form-switch">
                                                        <input class="form-check-input" type="checkbox" role="switch"
                                                            id="status_otp_mail"
                                                            <?=$getUser['status_otp_mail'] == 1 ? 'checked' : ''?> />
                                                    </div>
                                                </div>
                                            </li>
                                            <li class="d-flex mt-2">
                                                <div class="flex-grow-1">
                                                    <label class="form-check-label fs-14" for="telegram_notification">
                                                        <?=__('Thông báo qua Telegram');?>
                                                    </label>
                                                    <p class="text-muted">
                                                        <?php if(!empty($getUser['telegram_chat_id'])):?>
                                                        <?=__('Tài khoản đã liên kết với Telegram');?>:
                                                        <strong>@<?=htmlspecialchars($getUser['telegram_username'] ?? 'Unknown');?></strong>
                                                        <?php else:?>
                                                        <span
                                                            class="text-warning"><?=__('Tài khoản chưa liên kết với Telegram');?></span>
                                                        <?php endif;?>
                                                    </p>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <?php if(!empty($getUser['telegram_chat_id'])):?>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <div class="form-check form-switch">
                                                            <input class="form-check-input" type="checkbox"
                                                                role="switch" id="telegram_notification"
                                                                <?=($getUser['telegram_notification'] ?? 0) == 1 ? 'checked' : ''?> />
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                                            id="btnUnlinkTelegram">
                                                            <?=__('Hủy liên kết');?>
                                                        </button>
                                                    </div>
                                                    <?php else:?>
                                                    <button type="button" class="btn btn-sm btn-primary"
                                                        id="btnLinkTelegram">
                                                        <i class="fab fa-telegram-plane"></i>
                                                        <?=__('Liên kết Telegram');?>
                                                    </button>
                                                    <?php endif;?>
                                                </div>
                                            </li>

                                        </ul>
                                        <div class="text-end mt-3">
                                            <button type="button" class="btn btn-dark waves-effect waves-light"
                                                id="btnChangeSecurity">
                                                <?=__('Cập nhật');?>
                                            </button>
                                        </div>
                                    </div>

                                </div>
                                <!--end tab-pane-->
                            </div>
                        </div>
                    </div>
                </div>
                <!--end col-->
            </div>
            <!--end row-->

        </div>
        <!-- container-fluid -->
    </div><!-- End Page-content -->






    <?php
require_once(__DIR__.'/footer.php');
?>

    <!-- Modal Telegram -->
    <div class="modal fade" id="modalTelegram" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0"><?=__('Liên kết tài khoản Telegram');?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert alert-info rounded-0 mb-0">
                    <p class="mb-0"><?=__('Liên kết tài khoản Telegram để nhận thông báo quan trọng');?></p>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <i class="fab fa-telegram-plane text-primary" style="font-size: 64px;"></i>
                    </div>
                    <div class="steps-container">
                        <div class="mb-3">
                            <h6 class="fw-semibold"><?=__('Bước 1: Tìm Bot Telegram');?></h6>
                            <p class="text-muted mb-2"><?=__('Tìm và bắt đầu trò chuyện với bot');?>:</p>
                            <div class="d-flex align-items-center gap-2">
                                <code class="bg-light p-2 rounded flex-grow-1" id="botUsername">@YourBot</code>
                                <button class="btn btn-sm btn-secondary" onclick="copyBotUsername()">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-semibold"><?=__('Bước 2: Gửi mã liên kết');?></h6>
                            <p class="text-muted mb-2"><?=__('Gửi mã sau cho bot Telegram');?>:</p>
                            <div class="d-flex align-items-center gap-2">
                                <code class="bg-light p-2 rounded flex-grow-1"
                                    id="linkCode">/link <?=$getUser['api_key'];?></code>
                                <button class="btn btn-sm btn-secondary" onclick="copyLinkCode()">
                                    <i class="ri-file-copy-line"></i>
                                </button>
                            </div>
                        </div>
                        <div class="mb-3">
                            <h6 class="fw-semibold"><?=__('Bước 3: Xác nhận liên kết');?></h6>
                            <p class="text-muted"><?=__('Sau khi gửi mã, bot sẽ xác nhận liên kết thành công.');?></p>
                        </div>
                    </div>
                    <div class="alert alert-warning">
                        <small><?=__('Lưu ý: Vui lòng bảo mật API Key, nếu lộ ra ngoài vui lòng thay đổi API Key ngay.');?></small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                    <button type="button" class="btn btn-success" id="btnCheckTelegramLink"
                        onclick="checkTelegramLink()">
                        <span class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                        <span class="btn-text"><?=__('Kiểm tra liên kết');?></span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal 2FA -->
    <div class="modal fade" id="modal2FA" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 overflow-hidden">
                <div class="modal-header p-3">
                    <h4 class="card-title mb-0"><?=__('Xác thực hai yếu tố');?></h4>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="alert alert-info rounded-0 mb-0">
                    <p class="mb-0"><?=__('Bảo mật tài khoản của bạn bằng xác thực hai yếu tố');?></p>
                </div>
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div id="qrCode" class="mb-3" style="display:none;"></div>
                        <div id="secretKey" class="mb-3" style="display:none;">
                            <label class="form-label"><?=__('Mã bí mật');?></label>
                            <div class="input-group">
                                <input type="text" class="form-control" id="secretKeyInput" readonly>
                                <button class="btn btn-secondary btn-border" type="button" onclick="copySecretKey()">
                                    <i class="ri-file-copy-line"></i> Copy
                                </button>
                            </div>
                        </div>
                        <p class="mt-3" id="qrInstructions"><?=__('Quét mã QR bằng ứng dụng Google Authenticator');?>
                        </p>
                    </div>
                    <div class="form-group">
                        <label for="verificationCode" class="form-label"><?=__('Mã xác minh');?></label>
                        <input type="text" class="form-control" id="verificationCode"
                            placeholder="<?=__('Nhập mã xác minh');?>">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                    <button type="button" class="btn btn-primary" id="btnVerify2FA"><?=__('Xác minh');?></button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function() {
        let is2FAEnabled = <?=$getUser['status_2fa'] == 1 ? 'true' : 'false'?>;

        // Cập nhật trạng thái nút
        function updateButtonState() {
            if (is2FAEnabled) {
                $('#btnToggle2FA').html('<?=__('Tắt');?> <?=__('Xác thực hai yếu tố');?>');
                $('#btnToggle2FA').removeClass('btn-primary').addClass('btn-danger');
            } else {
                $('#btnToggle2FA').html('<?=__('Bật');?> <?=__('Xác thực hai yếu tố');?>');
                $('#btnToggle2FA').removeClass('btn-danger').addClass('btn-primary');
            }
        }

        updateButtonState();

        // Xử lý sự kiện click nút bật/tắt 2FA
        $('#btnToggle2FA').click(function() {
            var $btn = $(this);
            var originalText = $btn.html();
            
            if (!is2FAEnabled) {
                // Thêm hiệu ứng loading
                $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>').prop('disabled', true);
                
                // Hiển thị modal khi bật 2FA
                $.ajax({
                    url: "<?=base_url('ajaxs/client/auth.php');?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'Get2FAQR',
                        token: '<?=$getUser['token'];?>'
                    },
                    success: function(response) {
                        // Xóa hiệu ứng loading
                        $btn.html(originalText).prop('disabled', false);
                        
                        if (response.status == 'success') {
                            $('#qrCode').html(response.qr_code).show();
                            $('#secretKey').show();
                            $('#secretKeyInput').val(response.secret_key);
                            $('#modal2FA').modal('show');
                        } else {
                            Swal.fire('<?=__('Lỗi!');?>', response.msg, 'error');
                        }
                    },
                    error: function() {
                        // Xóa hiệu ứng loading khi có lỗi
                        $btn.html(originalText).prop('disabled', false);
                        Swal.fire('<?=__('Lỗi!');?>', '<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
                    }
                });
            } else {
                // Hiển thị modal xác minh khi tắt 2FA (không cần loading vì chỉ hiển thị modal)
                $('#qrCode').hide();
                $('#secretKey').hide();
                $('#qrInstructions').text('<?=__('Nhập mã xác minh để tắt 2FA');?>');
                $('#modal2FA').modal('show');
            }
        });

        // Xử lý xác minh mã
        $('#btnVerify2FA').click(function() {
            let code = $('#verificationCode').val();
            if (!code) {
                Swal.fire('<?=__('Lỗi!');?>', '<?=__('Vui lòng nhập mã xác minh');?>', 'error');
                return;
            }

            var $btn = $(this);
            var originalText = $btn.html();
            
            // Thêm hiệu ứng loading
            $btn.html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?=__('Đang xác minh...');?>').prop('disabled', true);

            $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'Save2FA',
                    token: '<?=$getUser['token'];?>',
                    status_2fa: is2FAEnabled ? 0 : 1,
                    secret: code
                },
                success: function(response) {
                    // Xóa hiệu ứng loading
                    $btn.html(originalText).prop('disabled', false);
                    
                    if (response.status == 'success') {
                        is2FAEnabled = !is2FAEnabled;
                        updateButtonState();
                        $('#modal2FA').modal('hide');
                        $('#verificationCode').val('');
                        Swal.fire('<?=__('Thành công!');?>', response.msg, 'success');
                    } else {
                        Swal.fire('<?=__('Lỗi!');?>', response.msg, 'error');
                    }
                },
                error: function() {
                    // Xóa hiệu ứng loading khi có lỗi
                    $btn.html(originalText).prop('disabled', false);
                    Swal.fire('<?=__('Lỗi!');?>', '<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
                }
            });
        });

        // Reset form khi đóng modal
        $('#modal2FA').on('hidden.bs.modal', function() {
            $('#verificationCode').val('');
            $('#qrCode').hide();
            $('#secretKey').hide();
            $('#qrInstructions').text('<?=__('Quét mã QR bằng ứng dụng Google Authenticator');?>');
        });

        // Xử lý phím Enter trong form
        $('#profileForm input').keypress(function(e) {
            if (e.which == 13) {
                e.preventDefault();
                updateProfile();
            }
        });

        // Xử lý tab và URL hash
        function handleTabChange() {
            var hash = window.location.hash;
            if (hash) {
                // Kích hoạt tab tương ứng với hash
                $('a[href="' + hash + '"]').tab('show');
            } else {
                // Nếu không có hash, mặc định hiển thị tab đầu tiên
                $('a[href="#personalDetails"]').tab('show');
            }
        }

        // Lắng nghe sự kiện khi tab thay đổi
        $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function(e) {
            var target = $(e.target).attr('href');
            if (window.location.hash !== target) {
                window.location.hash = target;
            }
        });

        // Xử lý khi trang load và khi hash thay đổi
        handleTabChange();
        $(window).on('hashchange', handleTabChange);

        // Xử lý cập nhật cài đặt bảo mật
        $("#btnChangeSecurity").on("click", function() {
            $('#btnChangeSecurity').html(
                    '<span><i class="fa fa-spinner fa-spin"></i> <?=__('Processing...');?></span>')
                .prop('disabled', true);
            $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'changeSecurity',
                    token: '<?=$getUser['token'];?>',
                    status_noti_login_to_mail: $("#status_noti_login_to_mail").is(":checked") ?
                        1 : 0,
                    status_otp_mail: $("#status_otp_mail").is(":checked") ? 1 : 0,
                    telegram_notification: $("#telegram_notification").is(":checked") ? 1 : 0
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        Swal.fire('<?=__('Successful!');?>', respone.msg, 'success');
                    } else {
                        Swal.fire('<?=__('Failure!');?>', respone.msg, 'error');
                    }
                    $('#btnChangeSecurity').html('<?=__('Cập nhật');?>').prop('disabled',
                        false);
                },
                error: function() {
                    Swal.fire('<?=__('Failure!');?>', '<?=__('Không thể xử lý');?>',
                        'error');
                    $('#btnChangeSecurity').html('<?=__('Cập nhật');?>').prop('disabled',
                        false);
                }
            });
        });

        // Xử lý liên kết Telegram
        $('#btnLinkTelegram').on('click', function() {
            // Lấy thông tin bot từ server
            $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'getTelegramBotInfo',
                    token: '<?=$getUser['token'];?>'
                },
                success: function(response) {
                    if (response.status == 'success') {
                        $('#botUsername').text('@' + response.bot_username);
                        $('#modalTelegram').modal('show');
                    } else {
                        Swal.fire('<?=__('Lỗi!');?>', response.msg ||
                            '<?=__('Không thể lấy thông tin bot');?>', 'error');
                    }
                },
                error: function() {
                    Swal.fire('<?=__('Lỗi!');?>',
                        '<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
                }
            });
        });

        // Xử lý hủy liên kết Telegram
        $('#btnUnlinkTelegram').on('click', function() {
            Swal.fire({
                title: '<?=__('Xác nhận');?>',
                text: '<?=__('Bạn có chắc chắn muốn hủy liên kết tài khoản Telegram không?');?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<?=__('Hủy liên kết');?>',
                cancelButtonText: '<?=__('Đóng');?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?=base_url('ajaxs/client/auth.php');?>",
                        method: "POST",
                        dataType: "JSON",
                        data: {
                            action: 'unlinkTelegram',
                            token: '<?=$getUser['token'];?>'
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire({
                                    title: '<?=__('Thành công');?>',
                                    text: response.msg,
                                    icon: 'success',
                                    confirmButtonText: '<?=__('Đóng');?>'
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire('<?=__('Lỗi!');?>', response.msg,
                                'error');
                            }
                        },
                        error: function() {
                            Swal.fire('<?=__('Lỗi!');?>',
                                '<?=__('Có lỗi xảy ra, vui lòng thử lại');?>',
                                'error');
                        }
                    });
                }
            });
        });
    });

    function updateProfile() {
        var fullname = $('#fullname').val();
        var phone = $('#phone').val();
        var $btn = $('#updateProfileBtn');
        var $spinner = $btn.find('.spinner-border');
        var $btnText = $btn.find('.btn-text');

        if (!fullname) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Vui lòng nhập họ và tên');?>'
            });
            return;
        }

        if (!phone) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Vui lòng nhập số điện thoại');?>'
            });
            return;
        }

        // Disable button và hiển thị loading
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('<?=__('Đang xử lý...');?>');

        $.ajax({
            url: '<?=BASE_URL('ajaxs/client/auth.php');?>',
            type: 'POST',
            data: {
                action: 'ChangeProfile',
                token: '<?=$getUser['token'];?>',
                fullname: fullname,
                phone: phone
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: response.msg,
                        showConfirmButton: false,
                        timer: 1500
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Lỗi');?>',
                        text: response.msg
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi');?>',
                    text: '<?=__('Đã xảy ra lỗi, vui lòng thử lại');?>'
                });
            },
            complete: function() {
                // Enable button và ẩn loading
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('<?=__('Cập nhật');?>');
            }
        });
    }

    function changePassword() {
        var oldPassword = $('#oldpasswordInput').val();
        var newPassword = $('#newpasswordInput').val();
        var confirmPassword = $('#confirmpasswordInput').val();
        var $btn = $('#changePasswordBtn');
        var $spinner = $btn.find('.spinner-border');
        var $btnText = $btn.find('.btn-text');

        if (!oldPassword) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Vui lòng nhập mật khẩu hiện tại');?>'
            });
            return;
        }

        if (!newPassword) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Vui lòng nhập mật khẩu mới');?>'
            });
            return;
        }

        if (!confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Vui lòng xác nhận mật khẩu mới');?>'
            });
            return;
        }

        if (newPassword !== confirmPassword) {
            Swal.fire({
                icon: 'error',
                title: '<?=__('Lỗi');?>',
                text: '<?=__('Mật khẩu xác nhận không khớp');?>'
            });
            return;
        }

        // Disable button và hiển thị loading
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('<?=__('Đang xử lý...');?>');

        $.ajax({
            url: '<?=BASE_URL('ajaxs/client/auth.php');?>',
            type: 'POST',
            data: {
                action: 'ChangePasswordProfile',
                token: '<?=$getUser['token'];?>',
                old_password: oldPassword,
                new_password: newPassword,
                confirm_password: confirmPassword
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>',
                        text: response.msg,
                        showConfirmButton: false,
                        timer: 1500
                    }).then(() => {
                        // Xóa trắng các trường input
                        $('#oldpasswordInput').val('');
                        $('#newpasswordInput').val('');
                        $('#confirmpasswordInput').val('');
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<?=__('Lỗi');?>',
                        text: response.msg
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: '<?=__('Lỗi');?>',
                    text: '<?=__('Đã xảy ra lỗi, vui lòng thử lại');?>'
                });
            },
            complete: function() {
                // Enable button và ẩn loading
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('<?=__('Đổi mật khẩu');?>');
            }
        });
    }

    function logoutSession(session_id) {
        Swal.fire({
            title: '<?=__('Xác nhận');?>',
            text: '<?=__('Bạn có chắc chắn muốn đăng xuất phiên này không?');?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?=__('Xác nhận');?>',
            cancelButtonText: '<?=__('Hủy');?>'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?=base_url('ajaxs/client/auth.php');?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'logoutSession',
                        session_id: session_id,
                        token: '<?=$getUser['token'];?>'
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                title: '<?=__('Thành công');?>',
                                text: res.msg,
                                icon: 'success',
                                confirmButtonText: '<?=__('Đóng');?>'
                            }).then((result) => {
                                if (result.isConfirmed || result.isDismissed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: '<?=__('Lỗi');?>',
                                text: res.msg,
                                icon: 'error',
                                confirmButtonText: '<?=__('Đóng');?>'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: '<?=__('Lỗi');?>',
                            text: '<?=__('Có lỗi xảy ra!');?>',
                            icon: 'error',
                            confirmButtonText: '<?=__('Đóng');?>'
                        });
                    }
                });
            }
        });
    }

    function copySecretKey() {
        var secretKey = document.getElementById('secretKeyInput');
        secretKey.select();
        secretKey.setSelectionRange(0, 99999);
        document.execCommand('copy');
        Swal.fire({
            icon: 'success',
            title: '<?=__('Thành công');?>',
            text: '<?=__('Đã sao chép mã bí mật');?>',
            showConfirmButton: false,
            timer: 1500
        });
    }

    function copyBotUsername() {
        var botUsername = document.getElementById('botUsername');
        var textToCopy = botUsername.textContent;
        navigator.clipboard.writeText(textToCopy).then(function() {
            Swal.fire({
                icon: 'success',
                title: '<?=__('Thành công');?>',
                text: '<?=__('Đã sao chép tên bot');?>',
                showConfirmButton: false,
                timer: 1500
            });
        });
    }

    function copyLinkCode() {
        var linkCode = document.getElementById('linkCode');
        var textToCopy = linkCode.textContent;
        navigator.clipboard.writeText(textToCopy).then(function() {
            Swal.fire({
                icon: 'success',
                title: '<?=__('Thành công');?>',
                text: '<?=__('Đã sao chép mã liên kết');?>',
                showConfirmButton: false,
                timer: 1500
            });
        });
    }

    function checkTelegramLink() {
        var $btn = $('#btnCheckTelegramLink');
        var $spinner = $btn.find('.spinner-border');
        var $btnText = $btn.find('.btn-text');

        // Disable button và hiển thị loading
        $btn.prop('disabled', true);
        $spinner.removeClass('d-none');
        $btnText.text('<?=__('Đang kiểm tra...');?>');

        $.ajax({
            url: "<?=base_url('ajaxs/client/auth.php');?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'checkTelegramLink',
                token: '<?=$getUser['token'];?>'
            },
            success: function(response) {
                if (response.status == 'success') {
                    if (response.linked) {
                        Swal.fire({
                            title: '<?=__('Thành công');?>',
                            text: '<?=__('Tài khoản đã được liên kết với Telegram thành công!');?>',
                            icon: 'success',
                            confirmButtonText: '<?=__('Đóng');?>'
                        }).then(() => {
                            $('#modalTelegram').modal('hide');
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '<?=__('Chưa liên kết');?>',
                            text: '<?=__('Tài khoản chưa được liên kết. Vui lòng thực hiện theo hướng dẫn.');?>',
                            icon: 'info',
                            confirmButtonText: '<?=__('Đóng');?>'
                        });
                    }
                } else {
                    Swal.fire('<?=__('Lỗi!');?>', response.msg, 'error');
                }
            },
            error: function() {
                Swal.fire('<?=__('Lỗi!');?>', '<?=__('Có lỗi xảy ra, vui lòng thử lại');?>', 'error');
            },
            complete: function() {
                // Enable button và ẩn loading
                $btn.prop('disabled', false);
                $spinner.addClass('d-none');
                $btnText.text('<?=__('Kiểm tra liên kết');?>');
            }
        });
    }

    function logoutAllSessions() {
        Swal.fire({
            title: '<?=__('Xác nhận đăng xuất');?>',
            text: '<?=__('Bạn có chắc chắn muốn đăng xuất tất cả phiên đăng nhập không? Điều này sẽ đăng xuất khỏi tất cả thiết bị.');?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: '<?=__('Đăng xuất tất cả');?>',
            cancelButtonText: '<?=__('Hủy');?>'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?=base_url('ajaxs/client/auth.php');?>",
                    method: "POST",
                    dataType: "JSON",
                    data: {
                        action: 'logoutAllSessions',
                        token: '<?=$getUser['token'];?>'
                    },
                    success: function(res) {
                        if (res.status == 'success') {
                            Swal.fire({
                                title: '<?=__('Thành công');?>',
                                text: res.msg,
                                icon: 'success',
                                confirmButtonText: '<?=__('Đóng');?>'
                            }).then((result) => {
                                if (result.isConfirmed || result.isDismissed) {
                                    window.location.reload();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: '<?=__('Lỗi');?>',
                                text: res.msg,
                                icon: 'error',
                                confirmButtonText: '<?=__('Đóng');?>'
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            title: '<?=__('Lỗi');?>',
                            text: '<?=__('Có lỗi xảy ra!');?>',
                            icon: 'error',
                            confirmButtonText: '<?=__('Đóng');?>'
                        });
                    }
                });
            }
        });
    }
    </script>