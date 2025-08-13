<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

if (isSecureCookie('user_login') == true) {
    redirect(base_url('client/order'));
}
$body = [
    'title' => __('Đăng nhập').' | '.$CMSNT->site('title'),
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
                            <div class="card mt-4">

                                <div class="card-body p-4">
                                    <div class="text-center mt-2">
                                        <h5 class="text-primary"><?=__('Chào mừng trở lại!');?></h5>
                                        <p class="text-muted"><?=__('Đăng nhập để tiếp tục');?></p>
                                    </div>
                                    <div class="p-2 mt-4">
                                        <form>
                                            <div class="mb-3">
                                                <label for="username"
                                                    class="form-label"><?=__('Tên tài khoản');?></label>
                                                <input type="text" class="form-control" id="username"
                                                    autocomplete="username"
                                                    value="<?=$CMSNT->site('status_demo') == 1 ? 'admin' : '';?>"
                                                    placeholder="<?=__('Nhập tên tài khoản');?>">
                                            </div>

                                            <div class="mb-3">
                                                <div class="float-end">
                                                    <a href="<?=base_url('client/forgot-password');?>"
                                                        class="text-muted"><?=__('Quên mật khẩu?');?></a>
                                                </div>
                                                <label class="form-label"
                                                    for="password-input"><?=__('Mật khẩu');?></label>
                                                <div class="position-relative auth-pass-inputgroup mb-3">
                                                    <input type="password" class="form-control pe-5 password-input"
                                                        value="<?=$CMSNT->site('status_demo') == 1 ? 'admin' : '';?>"
                                                        autocomplete="current-password"
                                                        placeholder="<?=__('Nhập mật khẩu');?>" id="password-input">
                                                    <button
                                                        class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted shadow-none password-addon"
                                                        type="button" id="password-addon"><i
                                                            class="ri-eye-fill align-middle"></i></button>
                                                </div>
                                            </div>
                                            <?php if($CMSNT->site('status_demo') == 1):?>
                                            <div class="mb-3">
                                                <div class="alert alert-warning" role="alert">
                                                    <strong>Thông báo:</strong> Vui lòng nhấn nút <strong>Đăng
                                                        nhập</strong> để truy cập giao diện quản trị và trang khách hàng
                                                    <strong>DEMO</strong>.<br><br>
                                                    Chúc quý khách có trải nghiệm tuyệt vời khi tham quan hệ thống của
                                                    chúng tôi!
                                                </div>
                                            </div>

                                            <?php endif?>
                                            <center class="mb-3"
                                                <?=$CMSNT->site('reCAPTCHA_status') == 1 ? '' : 'style="display:none;"';?>>
                                                <div class="g-recaptcha" id="g-recaptcha-response"
                                                    data-sitekey="<?=$CMSNT->site('reCAPTCHA_site_key');?>"></div>
                                            </center>


                                            <div class="mt-4">
                                                <button class="btn btn-primary w-100" type="button"
                                                    id="btnLoginPage"><?=__('Đăng Nhập');?></button>
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
                                                            class="ri-google-fill fs-16 me-2"></i><?=__('Đăng nhập bằng Google');?></a>
                                                </div>
                                            </div>
                                            <?php endif;?>
                                        </form>
                                    </div>
                                </div>
                                <!-- end card body -->
                            </div>
                            <!-- end card -->

                            <div class="mt-4 text-center">
                                <p class="mb-0"><?=__('Bạn chưa có tài khoản ?');?> <a
                                        href="<?=base_url('client/register');?>"
                                        class="fw-semibold text-primary text-decoration-underline">
                                        <?=__('Đăng Ký Ngay');?> </a>
                                </p>
                            </div>

                        </div>
                    </div>
                    <!-- end row -->
                </div>
                <!-- end container -->
            </div>
            <!-- end auth page content -->
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>

<script type="text/javascript">
$(document).ready(function() {
    // Khởi tạo các biến
    const $loginBtn = $("#btnLoginPage");
    const $username = $("#username");
    const $password = $("#password-input");
    const $recaptcha = $("#g-recaptcha-response");

    // Hàm validate form
    const validateForm = () => {
        if (!$username.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng nhập tên tài khoản');?>');
            return false;
        }
        if (!$password.val()) {
            showError('<?=__('Thất bại!');?>', '<?=__('Vui lòng nhập mật khẩu');?>');
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

    // Hàm xử lý đăng nhập
    const handleLogin = async () => {
        if (!validateForm()) return;

        try {
            // Disable nút đăng nhập và hiển thị loading
            $loginBtn.html(
                '<span class="spinner-border spinner-border-sm me-2" role="status"></span><?=__('Đang xử lý...');?>'
                ).prop(
                'disabled', true);

            const response = await $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'Login',
                    csrf_token: $("#csrf_token").val(),
                    recaptcha: $recaptcha.val(),
                    username: $username.val(),
                    password: $password.val()
                }
            });

            switch (response.status) {
                case 'success':
                    await showCustomSuccessAlert('<?=__('Đăng nhập thành công!');?>', response.msg);

                    <?php if($CMSNT->site('google_analytics_status') == 1):?>
                    // ✅ Gửi sự kiện về Google Analytics
                    gtag('event', 'sign_in', {
                        method: 'Website Form'
                    });
                    <?php endif?>
                    window.location.href = '<?=base_url('client/order');?>';
                    break;

                case 'verify_otp_mail':
                    await showEmailVerificationAlert($username.val());
                    window.location.href = response.url;
                    break;

                case 'verify_2fa':
                    await showWarning('<?=__('Xác minh 2FA');?>', response.msg);
                    window.location.href = response.url;
                    break;

                default:
                    showError('<?=__('Thất bại!');?>', response.msg);
                    break;
            }
        } catch (error) {
            console.error('Login error:', error);
            showError(
                '<?=__('Lỗi hệ thống!');?>',
                '<?=__('Có lỗi xảy ra, vui lòng thử lại sau');?>'
            );
        } finally {
            // Reset trạng thái nút đăng nhập
            $loginBtn.html('<?=__('Đăng Nhập');?>').prop('disabled', false);
        }
    };

    // Event handler cho nút đăng nhập
    $loginBtn.on("click", handleLogin);

    // Thêm event handler cho phím Enter
    $(document).on('keypress', function(e) {
        if (e.which == 13) {
            handleLogin();
        }
    });
});
</script>