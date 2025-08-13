<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

if (isSecureCookie('user_login') == true) {
    redirect(base_url('client/order'));
}


$body = [
    'title' => __('Đăng ký tài khoản').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
';
$body['footer'] = '
<!-- particles js -->
<script src="'.BASE_URL('public/client/assets/').'libs/particles.js/particles.js"></script>
<!-- particles app js -->
<script src="'.BASE_URL('public/client/assets/').'js/pages/particles.app.js"></script>
<!-- password-addon init -->
<script src="'.BASE_URL('public/client/assets/').'js/pages/password-addon.init.js"></script>
';
require_once(__DIR__.'/header.php');

require_once(__DIR__.'/sidebar.php');
?>

<div class="main-content">
    <div class="page-content">
        <div class="auth-page-wrapper pt-5">
            <!-- auth page bg -->
            <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
                <div class="bg-overlay"></div>

                <div class="shape">
                    <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
                        viewBox="0 0 1440 120">
                        <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z">
                        </path>
                    </svg>
                </div>
            </div>

            <!-- auth page content -->
            <div class="auth-page-content">
                <div class="container">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="text-center mt-sm-5 mb-4 text-white-50">
                                <div>
                                    <a href="<?=base_url();?>" class="d-inline-block auth-logo">
                                        <img src="<?=BASE_URL($CMSNT->site('logo_dark'));?>" alt="" height="100">
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- end row -->

                    <div class="row justify-content-center">
                        <div class="col-md-8 col-lg-6 col-xl-5">
                            <div class="card mt-4 card-bg-fill">
                                <div class="card-body p-4">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary"><?=__('Đăng ký tài khoản mới');?></h5>
                                        <p class="text-muted"><?=__('Vui lòng nhập thông tin đăng ký');?></p>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <form class="needs-validation" novalidate>
                                            <input type="hidden" id="csrf_token" value="<?=generate_csrf_token();?>">
                                            <div class="mb-3">
                                                <label for="register-username" class="form-label"><?=__('Tên đăng nhập');?>
                                                    <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="register-username"
                                                    placeholder="<?=__('Nhập tên đăng nhập');?>" required>
                                                <div class="invalid-feedback">
                                                    <?=__('Vui lòng nhập tên đăng nhập');?>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label for="register-email" class="form-label"><?=__('Email');?> <span
                                                        class="text-danger">*</span></label>
                                                <input type="email" class="form-control" id="register-email"
                                                    placeholder="<?=__('Nhập địa chỉ email');?>" required>
                                                <div class="invalid-feedback">
                                                    <?=__('Vui lòng nhập email');?>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="register-password"><?=__('Mật khẩu');?></label>
                                                <div class="position-relative auth-pass-inputgroup">
                                                    <input type="password" class="form-control pe-5 password-input"
                                                        placeholder="<?=__('Nhập mật khẩu');?>" id="register-password"
                                                        aria-describedby="passwordInput"
                                                        pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" required>
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon shadow-none"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                    <div class="invalid-feedback">
                                                        <?=__('Vui lòng nhập mật khẩu');?>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="register-password-confirm"><?=__('Xác nhận mật khẩu');?></label>
                                                <div class="position-relative auth-pass-inputgroup">
                                                    <input type="password" class="form-control pe-5 password-input"
                                                        placeholder="<?=__('Nhập lại mật khẩu');?>"
                                                        id="register-password-confirm" required>
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon shadow-none"
                                                        type="button" id="password-addon-confirm"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                    <div class="invalid-feedback">
                                                        <?=__('Vui lòng xác nhận mật khẩu');?>
                                                    </div>
                                                </div>
                                            </div>

                                            <center class="mb-3"
                                                <?=$CMSNT->site('reCAPTCHA_status') == 1 ? '' : 'style="display:none;"';?>>
                                                <div class="g-recaptcha" id="g-recaptcha-response"
                                                    data-sitekey="<?=$CMSNT->site('reCAPTCHA_site_key');?>"></div>
                                            </center>

                                            <div class="mt-4">
                                                <button class="btn btn-primary w-100" type="button"
                                                    id="btnRegister"><?=__('Đăng Ký');?></button>
                                            </div>
                                            <?php if($CMSNT->site('status_google_login') == 1):?>
                                            <?php
$client = new Google_Client();
$client->setClientId($CMSNT->site('google_login_client_id')); // Client ID của bạn
$client->setClientSecret($CMSNT->site('google_login_client_secret')); // Client Secret của bạn
$client->setRedirectUri(base_url('api/callback_google_login.php')); // URL callback
$client->addScope("email");
$client->addScope("profile");
$login_url = $client->createAuthUrl();
?>
                                            <div class="mt-4 text-center">
                                                <div class="signin-other-title">
                                                    <h5 class="fs-13 mb-4 title text-muted"><?=__('Hoặc');?></h5>
                                                </div>

                                                <div>
                                                    <a type="button" href="<?=$login_url;?>"
                                                        class="btn btn-danger waves-effect waves-light w-100 text-uppercase"><i
                                                            class="ri-google-fill fs-16 me-2"></i><?=__('Đăng ký bằng Google');?></a>
                                                </div>
                                            </div>
                                            <?php endif;?>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                                <p class="mb-0"><?=__('Bạn đã có tài khoản?');?> <a href="<?=base_url('client/login');?>"
                                        class="fw-semibold text-primary text-decoration-underline"><?=__('Đăng Nhập');?></a>
                                </p>
                            </div>
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

<script>
$(document).ready(function() {
    // Khởi tạo các biến
    const $registerBtn = $("#btnRegister");
    const $email = $("#register-email");
    const $password = $("#register-password");
    const $passwordConfirm = $("#register-password-confirm");
    const $recaptcha = $("#g-recaptcha-response");
    const $username = $("#register-username");
    // Hàm validate form
    const validateForm = () => {
        if (!$username.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng nhập tên đăng nhập');?>');
            return false;
        }
        if (!$email.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng nhập email');?>');
            return false;
        }
        if (!$password.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng nhập mật khẩu');?>');
            return false;
        }
        if ($password.val() !== $passwordConfirm.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Mật khẩu xác nhận không khớp');?>');
            return false;
        }
        <?php if($CMSNT->site('reCAPTCHA_status') == 1): ?>
        if (!$recaptcha.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng xác nhận reCAPTCHA');?>');
            return false;
        }
        <?php endif; ?>
        return true;
    };

    // Hàm xử lý đăng ký
    const handleRegister = async () => {
        if (!validateForm()) return;

        try {
            // Disable nút đăng ký và hiển thị loading
            $registerBtn.html('<span class="spinner-border spinner-border-sm me-2" role="status"></span><?=__('Đang xử lý...');?>').prop(
                'disabled', true);

            const response = await $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'Register',
                    csrf_token: $("#csrf_token").val(),
                    recaptcha: $recaptcha.val(),
                    email: $email.val(),
                    username: $username.val(),
                    password: $password.val(),
                    repassword: $passwordConfirm.val()
                }
            });

            if (response.status == 'success') {
                await showCustomSuccessAlert('<?=__('Đăng ký thành công!');?>', response.msg);

                <?php if($CMSNT->site('google_analytics_status') == 1):?>
                // ✅ Gửi sự kiện về Google Analytics
                gtag('event', 'sign_up', {
                    method: 'Website Form'
                });
                <?php endif?>

                <?php if($CMSNT->site('google_ads_status') == 1):?>
                gtag('event', 'conversion', {
                    'send_to': '<?=$CMSNT->site('google_ads_id');?>'
                });
                <?php endif?>
                
                setTimeout(() => {
                    window.location.href = '<?=base_url('client/order');?>';
                }, 1000);
            } else {
                showError('<?=__('Đăng ký thất bại!');?>', response.msg);
            }
        } catch (error) {
            console.error('Register error:', error);
            showError(
                '<?=__('Lỗi hệ thống!');?>',
                '<?=__('Có lỗi xảy ra, vui lòng thử lại sau');?>'
            );
        } finally {
            // Reset trạng thái nút đăng ký
            $registerBtn.html('<?=__('Đăng Ký');?>').prop('disabled', false);
        }
    };

    // Event handler cho nút đăng ký
    $registerBtn.on("click", handleRegister);

    // Thêm event handler cho phím Enter
    $(document).on('keypress', function(e) {
        if (e.which == 13) {
            handleRegister();
        }
    });
});
</script>