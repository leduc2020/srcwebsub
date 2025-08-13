<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Đặt lại mật khẩu').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '';
$body['footer'] = '';

if(empty($_GET['token'])){
    redirect(base_url());
}
if (!$getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token_forgot_password` = '".check_string($_GET['token'])."' AND `token_forgot_password` IS NOT NULL ")) {
    if(empty($getUser['token_forgot_password'])){
        checkBlockIP('RESET_PASSWORD', 15);
        redirect(base_url());
    }
    checkBlockIP('RESET_PASSWORD', 15);
    redirect(base_url());
}
require_once(__DIR__.'/header.php');
?>

<body>
    <div class="auth-page-wrapper pt-5">
        <!-- auth page bg -->
        <div class="auth-one-bg-position auth-one-bg" id="auth-particles">
            <div class="bg-overlay"></div>

            <div class="shape">
                <svg xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 1440 120">
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
                                    <img src="<?=BASE_URL($CMSNT->site('logo_light'));?>" alt="" height="20">
                                </a>
                            </div>
                            <p class="mt-3 fs-15 fw-medium"><?=$CMSNT->site('title');?></p>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card mt-4 card-bg-fill">
                            <div class="card-body p-4">
                                <div class="text-center mt-2">
                                    <h5 class="text-primary"><?=__('Tạo mật khẩu mới');?></h5>
                                    <p class="text-muted"><?=__('Mật khẩu mới phải khác với mật khẩu đã sử dụng trước đó.');?></p>
                                </div>

                                <div class="p-2">
                                    <form>
                                        <input type="hidden" id="csrf_token" value="<?=generate_csrf_token();?>">
                                        <input type="hidden" id="ChangePassword_token" value="<?=$getUser['token_forgot_password'];?>">
                                        
                                        <div class="mb-3">
                                            <label class="form-label" for="ChangePassword_password"><?=__('Mật khẩu');?></label>
                                            <div class="position-relative auth-pass-inputgroup">
                                                <input type="password" class="form-control pe-5 password-input" 
                                                    
                                                    placeholder="<?=__('Nhập mật khẩu mới');?>" 
                                                    id="ChangePassword_password" 
                                                    aria-describedby="passwordInput" 
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                                    required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon shadow-none" 
                                                    type="button" 
                                                    id="password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                            <div id="passwordInput" class="form-text"><?=__('Phải có ít nhất 8 ký tự.');?></div>
                                        </div>

                                        <div class="mb-3">
                                            <label class="form-label" for="ChangePassword_repassword"><?=__('Xác nhận mật khẩu');?></label>
                                            <div class="position-relative auth-pass-inputgroup mb-3">
                                                <input type="password" class="form-control pe-5 password-input" 
                                                     
                                                    placeholder="<?=__('Xác nhận mật khẩu mới');?>" 
                                                    pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}" 
                                                    id="ChangePassword_repassword" 
                                                    required>
                                                <button class="btn btn-link position-absolute end-0 top-0 text-decoration-none text-muted password-addon shadow-none" 
                                                    type="button" 
                                                    id="confirm-password-addon">
                                                    <i class="ri-eye-fill align-middle"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div id="password-contain" class="p-3 bg-light mb-2 rounded">
                                            <h5 class="fs-13"><?=__('Mật khẩu phải chứa:');?></h5>
                                            <p id="pass-length" class="invalid fs-12 mb-2"><?=__('Tối thiểu');?> <b><?=__('8 ký tự');?></b></p>
                                            <p id="pass-lower" class="invalid fs-12 mb-2"><?=__('Ít nhất');?> <b><?=__('1 chữ thường');?></b> (a-z)</p>
                                            <p id="pass-upper" class="invalid fs-12 mb-2"><?=__('Ít nhất');?> <b><?=__('1 chữ hoa');?></b> (A-Z)</p>
                                            <p id="pass-number" class="invalid fs-12 mb-0"><?=__('Ít nhất');?> <b><?=__('1 số');?></b> (0-9)</p>
                                        </div>

                                        <div class="mt-4">
                                            <button class="btn btn-success w-100" type="button" id="btnChangePassword">
                                                <?=__('Đặt lại mật khẩu');?>
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <p class="mb-0"><?=__('Đợi đã, tôi nhớ mật khẩu rồi...');?> 
                                <a href="<?=BASE_URL('client/login');?>" class="fw-semibold text-primary text-decoration-underline">
                                    <?=__('Đăng Nhập');?>
                                </a>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- footer -->
        <footer class="footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0 text-muted">&copy;
                                <script>document.write(new Date().getFullYear())</script> 
                                <?=$CMSNT->site('title');?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    </div>

    <!-- JAVASCRIPT -->
    <script src="<?=BASE_URL('public/client/assets/');?>libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="<?=BASE_URL('public/client/assets/');?>libs/simplebar/simplebar.min.js"></script>
    <script src="<?=BASE_URL('public/client/assets/');?>libs/node-waves/waves.min.js"></script>
    <script src="<?=BASE_URL('public/client/assets/');?>libs/feather-icons/feather.min.js"></script>
    <script src="<?=BASE_URL('public/client/assets/');?>js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="<?=BASE_URL('public/client/assets/');?>js/plugins.js"></script>

    <!-- particles js -->
    <script src="<?=BASE_URL('public/client/assets/');?>libs/particles.js/particles.js"></script>
    <!-- particles app js -->
    <script src="<?=BASE_URL('public/client/assets/');?>js/pages/particles.app.js"></script>
    <!-- password-addon init -->
    <script src="<?=BASE_URL('public/client/assets/');?>js/pages/password-addon.init.js"></script>

    <!-- Sweet Alerts js -->
    <script src="<?=BASE_URL('public/client/assets/');?>libs/sweetalert2/sweetalert2.min.js"></script>

    <!-- Sweet alert init js-->
    <script src="<?=BASE_URL('public/client/assets/');?>js/pages/sweetalerts.init.js"></script>

    <script type="text/javascript">
    $("#btnChangePassword").on("click", function() {
        const $btn = $('#btnChangePassword');
        const password = $('#ChangePassword_password').val();
        const repassword = $('#ChangePassword_repassword').val();
        const token = $('#ChangePassword_token').val();

        if (password !== repassword) {
            showError('<?=__('Thất bại!');?>', '<?=__('Mật khẩu xác nhận không khớp!');?>');
            return;
        }

        $btn.html('<i class="fa fa-spinner fa-spin"></i> <?=__('Processing...');?>').prop('disabled', true);
        
        $.ajax({
            url: "<?=base_url('ajaxs/client/auth.php');?>",
            method: "POST",
            dataType: "JSON",
            data: {
                action: 'ChangePassword',
                csrf_token: $("#csrf_token").val(),
                token: token,
                newpassword: password,
                renewpassword: repassword
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: '<?=__('Thành công!');?>',
                        text: response.msg,
                        icon: 'success',
                        confirmButtonColor: '#3085d6',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '<?=BASE_URL('client/login');?>';
                        }
                    });
                } else {
                    showError('<?=__('Thất bại!');?>', response.msg);
                }
                $btn.html('<?=__('Đặt lại mật khẩu');?>').prop('disabled', false);
            },
            error: function() {
                showMessage('<?=__('Không thể xử lý');?>', 'error');
                $btn.html('<?=__('Đặt lại mật khẩu');?>').prop('disabled', false);
            }
        });
    });
    </script>
</body>
</html>