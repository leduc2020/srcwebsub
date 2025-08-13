<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Quên mật khẩu').' | '.$CMSNT->site('title'),
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
                    <path d="M 0,36 C 144,53.6 432,123.2 720,124 C 1008,124.8 1296,56.8 1440,40L1440 140L0 140z"></path>
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
                                    <h5 class="text-primary"><?=__('Bạn quên mật khẩu?');?></h5>
                                    <p class="text-muted"><?=__('Nhập đầy đủ thông tin để đặt lại mật khẩu');?></p>

                                    <lord-icon src="https://cdn.lordicon.com/rhvddzym.json" trigger="loop"
                                        colors="primary:#0ab39c" class="avatar-xl"></lord-icon>

                                </div>

                                <div class="alert border-0 alert-warning text-center mb-2 mx-2" role="alert">
                                    <?=__('Nhập email của bạn và hướng dẫn sẽ được gửi đến!');?>
                                </div>
                                <div class="p-2">
                                    <form>
                                        <input type="hidden" id="csrf_token" value="<?=generate_csrf_token();?>">
                                        <div class="mb-4">
                                            <label class="form-label"><?=__('Email');?></label>
                                            <input type="email" class="form-control" id="email"
                                                placeholder="<?=__('Nhập Email');?>">
                                        </div>

                                        <div class="text-center mt-4">
                                            <button class="btn btn-success w-100" type="button"
                                                id="btnForgotPassword"><?=__('Xác minh');?></button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0"><?=__('Đợi đã, tôi nhớ mật khẩu rồi...');?> <a
                                    href="<?=BASE_URL('client/login');?>"
                                    class="fw-semibold text-primary text-decoration-underline"><?=__('Đăng Nhập');?></a>
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
$("#btnForgotPassword").on("click", function() {
    const $btn = $('#btnForgotPassword');
    const $email = $('#email').val();
    $btn.html('<i class="fa fa-spinner fa-spin"></i> <?=__('Processing...');?>').prop('disabled', true);
    
    $.ajax({
        url: "<?=base_url('ajaxs/client/auth.php');?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'ForgotPassword',
            csrf_token: $("#csrf_token").val(),
            email: $email
        },
        success: function(response) {
            if (response.status === 'success') {
                showEmailVerificationAlert($email);
            } else {
                showError('<?=__('Thất bại!');?>', response.msg);
            }
            $btn.html('<?=__('Xác minh');?>').prop('disabled', false);
        },
        error: function() {
            showMessage('<?=__('Không thể xử lý');?>', 'error');
            $btn.html('<?=__('Xác minh');?>').prop('disabled', false);
        }
    });
});
</script>
