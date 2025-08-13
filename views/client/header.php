<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}?>
<!doctype html>
<html lang="<?=getLanguageCode();?>" data-layout-style="<?=$CMSNT->site('data-layout-style');?>" data-layout-position="<?=$CMSNT->site('data-layout-position');?>" data-layout="<?=$CMSNT->site('data-layout');?>" data-bs-theme="<?=$CMSNT->site('data-bs-theme');?>" data-topbar="<?=$CMSNT->site('data-topbar');?>" data-sidebar="<?=$CMSNT->site('data-sidebar');?>" data-theme="<?=$CMSNT->site('data-theme');?>" data-sidebar-size="<?=$CMSNT->site('data-sidebar-size');?>" data-sidebar-image="<?=$CMSNT->site('data-sidebar-image');?>" data-preloader="<?=$CMSNT->site('data-preloader');?>">


<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">
    <link rel="canonical" href="<?=url();?>" />
    <title><?=isset($body['title']) ? $body['title'] : $CMSNT->site('title');?></title>
    <meta name="description" content="<?=isset($body['desc']) ? $body['desc'] : $CMSNT->site('description');?>" />
    <meta name="keywords" content="<?=isset($body['keyword']) ? $body['keyword'] : $CMSNT->site('keywords');?>">
    <meta name="copyright" content="<?=$CMSNT->site('author');?>" />
    <meta name="author" content="<?=$CMSNT->site('author');?>" />
    <meta property="og:url" content="<?=base_url('');?>">
    <meta property="og:site_name" content="<?=base_url();?>" />
    <meta property="og:title" content="<?=$body['title'];?>" />
    <meta property="og:type" content="website" />
    <meta property="og:image"
        content="<?=isset($body['image']) ? $body['image'] : BASE_URL($CMSNT->site('image'));?>" />
    <meta property="og:image:secure"
        content="<?=isset($body['image']) ? $body['image'] : BASE_URL($CMSNT->site('image'));?>" />
    <meta name="twitter:title" content="<?=$body['title'];?>" />
    <meta name="twitter:image"
        content="<?=isset($body['image']) ? $body['image'] : BASE_URL($CMSNT->site('image'));?>" />
    <meta name="twitter:image:alt" content="<?=$body['title'];?>" />
    <link rel="icon" type="image/png" href="<?=BASE_URL($CMSNT->site('favicon'));?>" />

    <!-- Sweet Alert css-->
    <link href="<?=BASE_URL('public/client/assets/');?>libs/sweetalert2/sweetalert2.min.css" rel="stylesheet"
        type="text/css" />
    <!-- plugin css -->
    <link href="<?=BASE_URL('public/client/assets/');?>libs/jsvectormap/jsvectormap.min.css" rel="stylesheet"
        type="text/css" />

    <!-- Layout config Js -->
    <script src="<?=BASE_URL('public/client/assets/');?>js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="<?=BASE_URL('public/client/assets/');?>css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="<?=BASE_URL('public/client/assets/');?>css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- aos css -->
    <link rel="stylesheet" href="<?=BASE_URL('public/client/assets/');?>libs/aos/aos.css" />
    <!-- App Css-->
    <link href="<?=BASE_URL('public/client/assets/');?>css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="<?=BASE_URL('public/client/assets/');?>css/custom.min.css" rel="stylesheet" type="text/css" />
    <!-- Main Css-->
    <link rel="stylesheet" href="<?=BASE_URL('public/css/main.css?v=1.0.4');?>">
    <link rel="stylesheet" href="<?=BASE_URL('mod/css/main.css?v=1.0.1');?>">

    <!-- jQuery -->
    <script src="<?=base_url('public/js/jquery-3.6.0.js');?>"></script>

    <!-- Simple Notify CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.css" />
    <!-- Simple Notify JS -->
    <script src="https://cdn.jsdelivr.net/npm/simple-notify@1.0.4/dist/simple-notify.min.js"></script>

 
   


    <?php if($CMSNT->site('google_analytics_status') == 1):?>
    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?=$CMSNT->site('google_analytics_id');?>"></script>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
        dataLayer.push(arguments);
    }
    gtag('js', new Date());

    gtag('config', '<?=$CMSNT->site('google_analytics_id');?>');
    </script>
    <?php endif?>


    <?php if($CMSNT->site('reCAPTCHA_status') == 1):?>
    <!-- reCaptcha -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <?php endif?>


    <?=$body['header'];?>
    <script src="<?=base_url('mod/js/main.js?v=1.0.0');?>"></script>
    <?=$CMSNT->site('javascript_header');?>

</head>

<script>
function showMessage(message, type) {
    const commonOptions = {
        effect: 'fade',
        speed: 300,
        customClass: null,
        customIcon: null,
        showIcon: true,
        showCloseButton: true,
        autoclose: true,
        autotimeout: 3000,
        gap: 20,
        distance: 20,
        type: 'outline',
        position: 'right top'
    };

    const options = {
        success: {
            status: 'success',
            title: '<?=__("Thành công!");?>',
            text: message,
        },
        error: {
            status: 'error',
            title: '<?=__("Thất bại!");?>',
            text: message,
        }
    };
    new Notify(Object.assign({}, commonOptions, options[type]));
}

// Hàm hiển thị thông báo warning với xác nhận (dạng đơn giản)
const showWarning = (title, message, confirmText = "Đồng ý", cancelText = "Hủy") => {
    return Swal.fire({
        title: title,
        text: message,
        icon: "warning",
        showCancelButton: true,
        customClass: {
            confirmButton: "btn btn-primary w-xs me-2 mt-2",
            cancelButton: "btn btn-danger w-xs mt-2"
        },
        confirmButtonText: confirmText,
        cancelButtonText: cancelText,
        buttonsStyling: false,
        showCloseButton: true
    });
};

// Hàm hiển thị thông báo thành công
const showSuccess = (title, message) => {
    return Swal.fire({
        title: title,
        text: message,
        icon: "success",
        showCancelButton: false,
        buttonsStyling: false,
        showCloseButton: true
    });
};

// Hàm hiển thị thông báo lỗi với Lord Icon
const showError = (title, message) => {
    return Swal.fire({
        html: `<div class="mt-3">
                <lord-icon 
                    src="https://cdn.lordicon.com/tdrtiskw.json" 
                    trigger="loop" 
                    colors="primary:#f06548,secondary:#f7b84b" 
                    style="width:120px;height:120px">
                </lord-icon>
                <div class="mt-4 pt-2 fs-15">
                    <h4>${title}</h4>
                    <p class="text-muted mx-4 mb-0">${message}</p>
                </div>
            </div>`,
        showCancelButton: true,
        showConfirmButton: false,
        customClass: {
            cancelButton: "btn btn-primary w-xs mb-1"
        },
        cancelButtonText: "<?=__('Đóng');?>",
        buttonsStyling: false,
        showCloseButton: true
    });
};


// Custom Success Alert with Lord Icon
const showCustomSuccessAlert = (title, message) => {
    return Swal.fire({
        html: `<div class="mt-3">
                <lord-icon 
                    src="https://cdn.lordicon.com/lupuorrc.json" 
                    trigger="loop" 
                    colors="primary:#0ab39c,secondary:#405189" 
                    style="width:120px;height:120px">
                </lord-icon>
                <div class="mt-4 pt-2 fs-15">
                    <h4>${title}</h4>
                    <p class="text-muted mx-4 mb-0">${message}</p>
                </div>
            </div>`,
        showCancelButton: true,
        showConfirmButton: false,
        customClass: {
            cancelButton: "btn btn-primary w-xs mb-1"
        },
        cancelButtonText: "<?=__('Ok');?>",
        buttonsStyling: false,
        showCloseButton: true
    });
};


// Email Verification Alert
const showEmailVerificationAlert = (email) => {
    return Swal.fire({
        html: `<div class="mt-3">
                <div class="avatar-lg mx-auto">
                    <div class="avatar-title bg-light text-success display-5 rounded-circle">
                        <i class="ri-mail-send-fill"></i>
                    </div>
                </div>
                <div class="mt-4 pt-2 fs-15">
                    <h4 class="fs-20 fw-semibold"><?=__('Xác thực Email của bạn');?></h4>
                    <p class="text-muted mb-0 mt-3 fs-13">
                        <?=__('Chúng tôi đã gửi email xác thực cho bạn');?>
                        <span class="fw-medium text-dark">${email}</span>, 
                        <br/> <?=__('Vui lòng kiểm tra nó.');?>
                    </p>
                </div>
            </div>`,
        showCancelButton: false,
        customClass: {
            confirmButton: "btn btn-primary mb-1"
        },
        confirmButtonText: "<?=__('Ok');?>",
        buttonsStyling: false,
        footer: '',
        showCloseButton: true
    });
};

</script>

<style>
:root {
 
    --vz-primary: <?=$CMSNT->site('data-btn-primary');?>;
    --vz-primary-text-emphasis: <?=$CMSNT->site('data-btn-primary-emphasis');?>;
    --vz-link-color: <?=$CMSNT->site('data-link-color');?>;
    --vz-link-hover-color: <?=$CMSNT->site('data-link-hover-color');?>;
    --vz-primary-rgb: <?=$CMSNT->site('data-btn-primary-rgb');?>;

}
body {
    <?=$CMSNT->site('font_family');
    ?>
}

html {
    scroll-behavior: smooth;
}
</style>


<?php 


// Initialize AOS animation attributes
$aos = [];
if($CMSNT->site('data-block-animate') == 1){
    $aos['fade-up'] = 'data-aos="fade-up"';
    $aos['fade-down'] = 'data-aos="fade-down"';
    $aos['fade-left'] = 'data-aos="fade-left"';
    $aos['fade-right'] = 'data-aos="fade-right"';
    $aos['fade-up-right'] = 'data-aos="fade-up-right"';
    $aos['fade-up-left'] = 'data-aos="fade-up-left"';
    $aos['fade-down-right'] = 'data-aos="fade-down-right"';
    $aos['zoom-in'] = 'data-aos="zoom-in"';
} else {
    // Set empty values when animation is disabled
    $aos['fade-up'] = '';
    $aos['fade-down'] = '';
    $aos['fade-left'] = '';
    $aos['fade-right'] = '';
    $aos['fade-up-right'] = '';
    $aos['fade-up-left'] = '';
    $aos['fade-down-right'] = '';
    $aos['zoom-in'] = '';
}
?>
