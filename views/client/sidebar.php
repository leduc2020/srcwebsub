<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}?>


<body>

    <!-- Begin page -->
    <div id="layout-wrapper">

        <header id="page-topbar">
            <div class="layout-width">
                <div class="navbar-header">
                    <div class="d-flex">
                        <!-- LOGO -->
                        <div class="navbar-brand-box horizontal-logo">
                            <a href="<?=base_url();?>" class="logo logo-dark">
                                <span class="logo-sm">
                                    <img src="<?=base_url($CMSNT->site('favicon'));?>" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?=base_url($CMSNT->site('logo_light'));?>" alt=""
                                        height="50">
                                </span>
                            </a>

                            <a href="<?=base_url();?>" class="logo logo-light">
                                <span class="logo-sm">
                                    <img src="<?=base_url($CMSNT->site('favicon'));?>" alt=""
                                        height="22">
                                </span>
                                <span class="logo-lg">
                                    <img src="<?=base_url($CMSNT->site('logo_dark'));?>" alt=""
                                        height="50">
                                </span>
                            </a>
                        </div>

                        <button type="button"
                            class="btn btn-sm px-3 fs-16 header-item vertical-menu-btn topnav-hamburger material-shadow-none"
                            id="topnav-hamburger-icon">
                            <span class="hamburger-icon">
                                <span></span>
                                <span></span>
                                <span></span>
                            </span>
                        </button>

                    </div>

                    <div class="d-flex align-items-center">

                         
                        <div class="dropdown ms-1 topbar-head-dropdown header-item">
                            <?php if($CMSNT->site('language_type') == 'manual'):?>
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-global-line fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header">
                                    <h6 class="text-overflow text-muted mb-0 text-uppercase"><?=__('Chọn ngôn ngữ');?>
                                    </h6>
                                </div>
                                <?php foreach ($CMSNT->get_list("SELECT * FROM `languages` WHERE `status` = 1 ORDER BY `stt` DESC") as $lang):?>
                                <a href="javascript:void(0);" class="dropdown-item notify-item language py-2"
                                    onclick="changeLanguage(<?=$lang['id'];?>)" title="<?=$lang['lang'];?>">
                                    <img src="<?=base_url($lang['icon']);?>" alt="<?=$lang['lang'];?>"
                                        class="me-2 rounded" height="18">
                                    <span
                                        class="align-middle <?=getLanguage() == $lang['lang'] ? 'fw-bold text-primary' : '';?>"><?=$lang['lang'];?></span>
                                </a>
                                <?php endforeach;?>
                            </div>
                            <?php endif?>
                        </div>

                        <div class="dropdown ms-1 topbar-head-dropdown header-item">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-money-dollar-circle-line fs-22"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <div class="dropdown-header">
                                    <h6 class="text-overflow text-muted mb-0 text-uppercase"><?=__('Chọn tiền tệ');?>
                                    </h6>
                                </div>
                                <?php foreach ($CMSNT->get_list("SELECT * FROM `currencies` WHERE `display` = 1 ") as $currency):?>
                                <a href="javascript:void(0);" class="dropdown-item notify-item language py-2"
                                    onclick="changeCurrency(<?=$currency['id'];?>)" title="<?=$currency['name'];?>">
                                    <span
                                        class="align-middle <?=getCurrency() == $currency['id'] ? 'fw-bold text-primary' : '';?>"><?=$currency['code'];?>
                                        (<?=$currency['symbol_left'] ? $currency['symbol_left'] : $currency['symbol_right'];?>)</span>
                                </a>
                                <?php endforeach;?>
                            </div>
                        </div>


                        <div class="ms-1 header-item">
                            <button type="button"
                                class="btn btn-icon btn-topbar material-shadow-none btn-ghost-secondary rounded-circle light-dark-mode">
                                <i class='bx bx-moon fs-22'></i>
                            </button>
                        </div>
                        <div class="dropdown ms-sm-3 header-item topbar-user">
                            <button type="button" class="btn material-shadow-none" id="page-header-user-dropdown"
                                data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <span class="d-flex align-items-center">
                                    <?php if(isset($getUser)):?>
                                    <img class="rounded-circle header-profile-user no-pointer-events"
                                        src="<?=getGravatarUrl($getUser['email']);?>" alt="<?=$getUser['email'];?>">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?=substr($getUser['username'], 0, 6) . '...';?></span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"><?=__('Thành viên');?></span>
                                    </span>
                                    <?php else:?>
                                    <img class="rounded-circle header-profile-user no-pointer-events"
                                        src="<?=base_url($CMSNT->site('avatar'));?>"
                                        alt="Header Avatar">
                                    <span class="text-start ms-xl-2">
                                        <span
                                            class="d-none d-xl-inline-block ms-1 fw-medium user-name-text"><?=__('Chào mừng');?></span>
                                        <span
                                            class="d-none d-xl-block ms-1 fs-12 user-name-sub-text"><?=__('Khách');?></span>
                                    </span>
                                    <?php endif?>
                                </span>
                            </button>
                            <div class="dropdown-menu dropdown-menu-end">
                                <?php if(isset($getUser)):?>
                                <?php if($getUser['admin'] > 0 && $CMSNT->site('status_show_button_admin_panel') == 1):?>
                                <a class="dropdown-item" href="<?=base_url_admin('home');?>"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Admin Panel');?></span></a>
                                <?php endif?>
                                <div class="dropdown-divider"></div>
                                <!-- item-->
                                <h6 class="dropdown-header"><?=__('Chào mừng');?> <?=$getUser['username'];?></h6>
                                <a class="dropdown-item" href="<?=base_url('client/profile');?>"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Tài khoản của tôi');?></span></a>
                                <a class="dropdown-item" href="<?=base_url('client/logs');?>"><i
                                        class="mdi mdi-history text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Nhật ký hoạt động');?></span></a>
                                <a class="dropdown-item" href="<?=base_url('client/transactions');?>"><i
                                        class="mdi mdi-cash-refund text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Biến động số dư');?></span></a>
                                <a class="dropdown-item" href="<?=base_url('client/orders');?>"><i
                                        class="mdi mdi-shopping text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Đơn hàng đã mua');?></span></a>
                                <div class="dropdown-divider"></div>
                                <?php if($CMSNT->site('affiliate_status') == 1):?>
                                <a class="dropdown-item" href="<?=base_url('client/affiliates');?>"><i
                                class="mdi mdi-link text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle"><?=__('Tiếp thị liên kết');?></span></a>
                                <?php endif?>
                                <?php if($CMSNT->site('support_tickets_status') == 1):?>
                                <a class="dropdown-item" href="<?=base_url('client/support-tickets');?>"><i
                                class="mdi mdi-ticket text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle"><?=__('Yêu cầu hỗ trợ');?></span></a>
                                <?php endif?>
                                <?php if($CMSNT->site('api_status') == 1):?>
                                <a class="dropdown-item" href="<?=base_url('client/document-api');?>"><i
                                class="mdi mdi-file-document text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle"><?=__('Tài liệu API');?></span></a>
                                <?php endif?>
                                <?php if($CMSNT->site('child_panel_status') == 1):?>
                                <a class="dropdown-item" href="<?=base_url('client/child-panel');?>"><i
                                class="ri ri-global-fill text-muted fs-16 align-middle me-1"></i> <span
                                class="align-middle"><?=__('Tạo web riêng');?></span></a>
                                <?php endif?>
                                <div class="dropdown-divider"></div>
                                <a class="dropdown-item" href="javascript:void(0);" onclick="showPaymentGatewaysModal()"><i
                                        class="mdi mdi-wallet text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Số dư hiện tại');?> : <b
                                            class="text-danger"><?=format_currency($getUser['money']);?></b></span></a>

                                <a class="dropdown-item" href="<?=base_url('client/logout');?>"><i
                                        class="mdi mdi-logout text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle" data-key="t-logout"><?=__('Đăng xuất');?></span></a>
                                <?php else:?>
                                <a class="dropdown-item" href="<?=base_url('client/login');?>"><i
                                        class="mdi mdi-login text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Đăng nhập');?></span></a>
                                <a class="dropdown-item" href="<?=base_url('client/register');?>"><i
                                        class="mdi mdi-account-circle text-muted fs-16 align-middle me-1"></i> <span
                                        class="align-middle"><?=__('Đăng ký');?></span></a>
                                <?php endif?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- removeNotificationModal -->
        <div id="removeNotificationModal" class="modal fade zoomIn" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            id="NotificationModalbtn-close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mt-2 text-center">
                            <lord-icon src="https://cdn.lordicon.com/gsqxdxog.json" trigger="loop"
                                colors="primary:#f7b84b,secondary:#f06548" style="width:100px;height:100px"></lord-icon>
                            <div class="mt-4 pt-2 fs-15 mx-4 mx-sm-5">
                                <h4>Are you sure ?</h4>
                                <p class="text-muted mx-4 mb-0">Are you sure you want to remove this Notification ?</p>
                            </div>
                        </div>
                        <div class="d-flex gap-2 justify-content-center mt-4 mb-2">
                            <button type="button" class="btn w-sm btn-light" data-bs-dismiss="modal">Close</button>
                            <button type="button" class="btn w-sm btn-danger" id="delete-notification">Yes, Delete
                                It!</button>
                        </div>
                    </div>

                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
        <!-- ========== App Menu ========== -->
        <div class="app-menu navbar-menu">
            <!-- LOGO -->
            <div class="navbar-brand-box">
                <!-- Dark Logo-->
                <a href="<?=base_url();?>" class="logo logo-dark">
                    <span class="logo-sm">
                        <img src="<?=base_url($CMSNT->site('favicon'));?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?=base_url($CMSNT->site('logo_light'));?>" alt="" height="50">
                    </span>
                </a>
                <!-- Light Logo-->
                <a href="<?=base_url();?>" class="logo logo-light">
                    <span class="logo-sm">
                        <img src="<?=base_url($CMSNT->site('favicon'));?>" alt="" height="22">
                    </span>
                    <span class="logo-lg">
                        <img src="<?=base_url($CMSNT->site('logo_dark'));?>" alt="" height="50">
                    </span>
                </a>
                <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
                    id="vertical-hover">
                    <i class="ri-record-circle-line"></i>
                </button>
            </div>



            <div id="scrollbar">
                <div class="container-fluid">


                    <div id="two-column-menu">
                    </div>
                    <ul class="navbar-nav" id="navbar-nav">
                        <li class="menu-title"><span data-key="t-menu"><?=__('Menu');?></span></li>

                        <!-- <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['home']);?>"
                                href="<?=base_url('client/home');?>">
                                <img src="<?=base_url('assets/img/icon-home.webp');?>" alt="<?=__('Bảng điều khiển');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-widgets"><?=__('Bảng điều khiển');?></span>
                            </a>
                        </li> -->

                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['order']);?>"
                                href="<?=base_url('order');?>">
                                <img src="<?=base_url('assets/img/icon-order.webp');?>" alt="<?=__('Tạo đơn hàng');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-widgets"><?=__('Tạo đơn hàng');?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['recharge-bank', 'recharge-card', 'recharge-crypto', 'recharge-paypal', 'recharge-xipay', 'recharge-korapay', 'recharge-tmweasyapi', 'recharge-openpix', 'recharge-bakong']);?>"
                                href="javascript:void(0);" onclick="showPaymentGatewaysModal()">
                                <img src="<?=base_url('assets/img/icon-topup.webp');?>" alt="<?=__('Nạp tiền');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-topup"><?=__('Nạp tiền');?></span>
                            </a>
                        </li> <!-- end Dashboard Menu -->

                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['orders']);?>"
                                href="<?=base_url('client/orders');?>">
                                <img src="<?=base_url('assets/img/icon-orders.webp');?>" alt="<?=__('Đơn hàng');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-orders"><?=__('Đơn hàng đã mua');?></span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['services']);?>"
                                href="<?=base_url('client/services');?>">
                                <img src="<?=base_url('assets/img/icon-services.webp');?>"
                                    alt="<?=__('Bảng giá dịch vụ');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-affiliates"><?=__('Bảng giá dịch vụ');?></span>
                            </a>
                        </li>
                        <?php if($CMSNT->site('affiliate_status') == 1):?>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['affiliates', 'affiliate-withdraw']);?>"
                                href="<?=base_url('client/affiliates');?>">
                                <img src="<?=base_url('assets/img/icon-link.webp');?>"
                                    alt="<?=__('Tiếp thị liên kết');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-affiliates"><?=__('Tiếp thị liên kết');?></span>
                            </a>
                        </li>

                        <?php endif?>
                        <?php if($CMSNT->site('support_tickets_status') == 1):?>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['support-tickets', 'ticket-detail']);?>"
                                href="<?=base_url('client/support-tickets');?>">
                                <img src="<?=base_url('assets/img/icon-support.webp');?>"
                                    alt="<?=__('Yêu cầu Hỗ trợ');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-support-tickets"><?=__('Yêu cầu Hỗ trợ');?></span>
                            </a>
                        </li>
                        <?php endif?>
                        <?php if($CMSNT->site('api_status') == 1):?>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['document-api']);?>"
                                href="<?=base_url('client/document-api');?>">
                                <img src="<?=base_url('assets/img/icon-api.webp');?>" alt="<?=__('Tài liệu API');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-support-tickets"><?=__('Tài liệu API');?></span>
                            </a>
                        </li>
                        <?php endif?>
                        <?php if($CMSNT->site('child_panel_status') == 1):?>
                        <li class="nav-item">
                            <a class="nav-link menu-link <?=active_sidebar_client(['child-panel']);?>"
                                href="<?=base_url('client/child-panel');?>">
                                <img src="<?=base_url('assets/img/icon-child.webp');?>" alt="<?=__('Tạo web riêng');?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"><span
                                    data-key="t-support-tickets"><?=__('Tạo web riêng');?></span>
                            </a>
                        </li>
                        <?php endif?>
                        <?php if($CMSNT->site('data-sidebar-service-show') == 1):?>
                        <li class="menu-title"><span data-key="t-dich-vu"><?=__('Dịch vụ');?></span></li>
                        <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC") as $category):?>
                        <li class="nav-item">
                            <a class="nav-link menu-link collapsed <?=isset($platformSlug) && $platformSlug == $category['slug'] ? 'active' : '';?>"
                                href="#sidebar<?=$category['id'];?>" data-bs-toggle="collapse" role="button"
                                aria-expanded="false" aria-controls="sidebar<?=$category['id'];?>">
                                <img src="<?=base_url($category['icon']);?>" alt="<?=$category['name'];?>"
                                    class="img-fluid img-thumbnail icon-sidebar me-2"> <span
                                    data-key="t-<?=$category['id'];?>"><?=$category['name'];?></span>
                            </a>
                            <div class="collapse menu-dropdown <?=isset($platformSlug) && $platformSlug == $category['slug'] ? 'show' : '';?>"
                                id="sidebar<?=$category['id'];?>">
                                <ul class="nav nav-sm flex-column">
                                    <?php foreach($CMSNT->get_list("SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = ".$category['id']." ORDER BY `stt` DESC") as $sub_category):?>
                                    <li class="nav-item">
                                        <a class="nav-link <?=isset($categorySlug) && $categorySlug == $sub_category['slug'] ? 'active' : '';?>"
                                            href="<?=base_url('service/'.$category['slug'].'/'.$sub_category['slug']);?>">
                                            <span
                                                data-key="t-<?=$sub_category['id'];?>"><?=$sub_category['name'];?></span>
                                        </a>
                                    </li>
                                    <?php endforeach;?>
                                </ul>
                            </div>
                        </li> <!-- end Dashboard Menu -->
                        <?php endforeach;?>
                        <?php endif;?>




                    </ul>
                </div>
                <!-- Sidebar -->
            </div>

            <div class="sidebar-background"></div>
        </div>
        <!-- Left Sidebar End -->
        <!-- Vertical Overlay-->
        <div class="vertical-overlay"></div>

        <script type="text/javascript">
        function logout() {
            Swal.fire({
                title: '<?=__('Bạn có chắc không?');?>',
                text: "<?=__('Bạn sẽ bị đăng xuất khỏi tài khoản khi nhấn Đồng Ý');?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?=__('Đồng ý');?>',
                cancelButtonText: '<?=__('Huỷ bỏ');?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location = "<?=base_url('client/logout');?>";
                }
            })
        }
        </script>


        <script>
        function changeLanguage(id) {
            $.ajax({
                url: "<?=BASE_URL("ajaxs/client/update.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'changeLanguage',
                    id: id
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: respone.msg,
                            confirmButtonText: "<?=__('Đồng ý');?>"
                        });
                    }
                },
                error: function() {
                    alert(html(response));
                    history.back();
                }
            });
        }
        </script>
        <script>
        function changeCurrency(id) {
            $.ajax({
                url: "<?=BASE_URL("ajaxs/client/update.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'changeCurrency',
                    id: id
                },
                success: function(respone) {
                    if (respone.status == 'success') {
                        location.reload();
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "<?=__('Lỗi');?>",
                            text: respone.msg,
                            confirmButtonText: "<?=__('Đồng ý');?>"
                        });
                    }
                },
                error: function() {
                    alert(html(response));
                    history.back();
                }
            });
        }
        </script>