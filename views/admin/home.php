<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Dashboard').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>
';
$body['footer'] = '
 
 
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

?>

<style>
/* AI Chat Modal Styles */
.ai-chat-container {
    height: 400px;
    overflow-y: auto;
    padding: 1rem;
    background: #f8f9fa;
}

.ai-chat-loading {
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.ai-chat-avatar {
    width: 32px;
    height: 32px;
    border-radius: 50%;
    display: flex !important;
    align-items: center;
    justify-content: center;
    color: white !important;
    font-size: 14px;
    flex-shrink: 0;
    margin-top: 0;
}

.ai-chat-message-user .ai-chat-avatar {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
}

.ai-chat-message-ai .ai-chat-avatar {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
}

/* Đảm bảo icon trong avatar hiển thị */
.ai-chat-avatar i {
    display: block !important;
    color: white !important;
    font-size: 14px !important;
}

.ai-chat-bubble {
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    font-size: 0.9rem;
    line-height: 1.4;
    word-wrap: break-word;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
}

.ai-chat-message-user .ai-chat-bubble {
    background: #007bff;
    color: white;
    border-bottom-right-radius: 0.25rem;
}

.ai-chat-message-ai .ai-chat-bubble {
    background: white;
    color: #333;
    border: 1px solid #e3e6f0;
    border-bottom-left-radius: 0.25rem;
}

.ai-chat-bubble-ai {
    background: white;
    color: #333;
    border: 1px solid #e3e6f0;
    border-bottom-left-radius: 0.25rem;
}

.ai-chat-time {
    font-size: 0.75rem;
    padding: 0 0.5rem;
}

.ai-chat-message-user .ai-chat-time {
    text-align: right;
}

.ai-chat-message-ai .ai-chat-time {
    text-align: left;
}

.ai-chat-input {
    border: 0;
    outline: 0;
    resize: none;
    min-height: 40px;
    max-height: 120px;
}

.ai-chat-input:focus {
    box-shadow: none;
    border-color: #007bff;
}

/* Typing Animation */
.typing-dots {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.5rem;
}

.typing-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #6c757d;
    animation: typing 1.4s infinite ease-in-out;
    opacity: 0.4;
}

.typing-dot:nth-child(1) {
    animation-delay: 0s;
}

.typing-dot:nth-child(2) {
    animation-delay: 0.2s;
}

.typing-dot:nth-child(3) {
    animation-delay: 0.4s;
}

@keyframes typing {
    0%, 60%, 100% {
        transform: translateY(0);
        opacity: 0.4;
    }
    30% {
        transform: translateY(-6px);
        opacity: 1;
    }
}

/* Code styling */
.ai-chat-bubble code {
    background: #f8f9fa;
    color: #e83e8c;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.85rem;
}

.ai-chat-message-user .ai-chat-bubble code {
    background: rgba(255,255,255,0.2);
    color: #ffffff;
}

/* Scrollbar */
.ai-chat-container::-webkit-scrollbar {
    width: 6px;
}

.ai-chat-container::-webkit-scrollbar-track {
    background: #f1f3f4;
}

.ai-chat-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.ai-chat-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* Dark mode support */
[data-theme-mode="dark"] .ai-chat-container {
    background: #1a1a1a;
}

[data-theme-mode="dark"] .ai-chat-message-ai .ai-chat-bubble,
[data-theme-mode="dark"] .ai-chat-bubble-ai {
    background: #2d2d2d;
    color: #e2e8f0;
    border-color: #404040;
}

[data-theme-mode="dark"] .ai-chat-bubble code {
    background: #1a1a1a;
    color: #74c0fc;
}

[data-theme-mode="dark"] .ai-chat-time {
    color: #9ca3af !important;
}

[data-theme-mode="dark"] .typing-dot {
    background: #9ca3af;
}

[data-theme-mode="dark"] .modal-content {
    background: #2d2d2d;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .modal-header {
    border-bottom: 1px solid #404040;
}

[data-theme-mode="dark"] .modal-footer {
    border-top: 1px solid #404040;
}

[data-theme-mode="dark"] .ai-chat-input {
    background: #1a1a1a;
    color: #e2e8f0;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .ai-chat-input:focus {
    border-color: #17a2b8;
    box-shadow: 0 0 0 0.2rem rgba(23,162,184,0.25);
}

/* --- BỔ SUNG: Typing indicator chuẩn cho popup AI Chat --- */
.typing-dots {
    display: flex;
    align-items: center;
    gap: 0.3rem;
    padding: 0.5rem 0.75rem;
}
.typing-dot {
    width: 8px !important;
    height: 8px !important;
    border-radius: 50% !important;
    background: #6c757d !important;
    animation: typing 1.4s infinite ease-in-out !important;
    opacity: 0.4 !important;
}
.typing-dot:nth-child(1) { animation-delay: 0s !important; }
.typing-dot:nth-child(2) { animation-delay: 0.2s !important; }
.typing-dot:nth-child(3) { animation-delay: 0.4s !important; }
@keyframes typing {
    0%, 60%, 100% { transform: translateY(0); opacity: 0.4; }
    30% { transform: translateY(-6px); opacity: 1; }
}
[data-theme-mode="dark"] .typing-dot {
    background: #9ca3af !important;
}
/* --- END --- */

/**** CSS bổ sung cho typing indicator hiển thị đúng ****/
.ai-chat-typing { display: none; }
.ai-chat-typing.show { display: flex !important; align-items: flex-start; margin-bottom: 1rem; }
/**** END ****/
</style>


<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-2 page-header-breadcrumb">

        </div>
        <?php if(checkPermission($getUser['admin'], 'view_license') == true):?>
        <div class="alert alert-secondary alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <div class="d-flex align-items-center mb-3">
                <i class="fas fa-code-branch text-primary me-2"></i>
                <h5 class="mb-0"><?=$config['project'];?> <span class="badge bg-primary"><?=$config['version'];?></span>
                </h5>
            </div>
            <?php if($CMSNT->site('status_update') == 1):?>
            <div class="alert alert-light border-0 py-2">
                <i class="fas fa-sync-alt text-success me-1"></i>
                <small><?=__('Hệ thống sẽ tự động cập nhật phiên bản mới khi bạn truy cập trang này. Để tắt tính năng này, vào');?>
                    <strong><?=__('Cài Đặt');?></strong> &rarr; <strong><?=__('Cài đặt chung');?></strong> &rarr;
                    <strong><?=__('Cập nhật phiên bản tự động');?></strong> &rarr;
                    <strong><?=__('OFF');?></strong>.</small>
            </div>
            <?php else:?>
            <div class="alert alert-light border-0 py-2">
                <i class="fas fa-sync-alt text-danger me-1"></i>
                <small><?=__('Bạn đang tắt chức năng cập nhật phiên bản. Để bật tính năng này, vào');?>
                    <strong><?=__('Cài Đặt');?></strong> &rarr; <strong><?=__('Cài đặt chung');?></strong> &rarr;
                    <strong><?=__('Cập nhật phiên bản tự động');?></strong> &rarr;
                    <strong><?=__('ON');?></strong>.</small>
            </div>
            <?php endif?>

            <div class="mt-3">
                <div class="row g-2 mt-2">
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-telegram text-primary me-2"></i>
                            <small>Kênh thông báo cập nhật:</small>
                            <?=$CMSNT->site('status_demo') == 1 ? '<span class="badge bg-warning">Chỉ áp dụng cho website chính hãng</span>' : '<a href="https://t.me/cmsntco" target="_blank">[CMSNT] Changelog - Notification</a>';?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-telegram text-primary me-2"></i>
                            <small>Nhóm tìm kiếm API:</small>
                            <?=$CMSNT->site('status_demo') == 1 ? '<span class="badge bg-warning">Chỉ áp dụng cho website chính hãng</span>' : '<a href="https://t.me/+LVON7y2BKWU3ZDY9" target="_blank">[CMSNT] Tìm kiếm API - Suppliers</a>';?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-rocketchat text-success me-2"></i>
                            <small>Nhóm Zalo thông báo:</small>
                            <?=$CMSNT->site('status_demo') == 1 ? '<span class="badge bg-warning">Chỉ áp dụng cho website chính hãng</span>' : '<a href="https://zalo.me/g/idapcx933" target="_blank">[CMSNT] Changelog - Notification</a>';?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-2 bg-light">
                            <i class="fab fa-rocketchat text-success me-2"></i>
                            <small>Nhóm Zalo trao đổi API:</small>
                            <?=$CMSNT->site('status_demo') == 1 ? '<span class="badge bg-warning">Chỉ áp dụng cho website chính hãng</span>' : '<a href="https://zalo.me/g/eululb377" target="_blank">[CMSNT] Trao đổi API - Suppliers</a>';?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="d-flex gap-2 mt-3">
                <a class="btn btn-primary btn-sm"
                    href="https://www.messenger.com/t/101939031161934?ref=kiemtrabanquyenwebsite" target="_blank">
                    <i class="fab fa-facebook-messenger me-1"></i> Kiểm tra bản quyền
                </a>
                <a class="btn btn-secondary btn-sm" href="https://t.me/cmsnt_bot" target="_blank">
                    <i class="fab fa-telegram me-1"></i> Bot kiểm tra bản quyền
                </a>
                <button class="btn btn-info btn-sm" id="copyLicenseKey" onclick="copyLicenseKey()">
                    <i class="fas fa-copy me-1"></i> Sao chép giấy phép
                </button>
                <button class="btn btn-warning btn-sm ms-auto" id="hideAlert24h">
                    <i class="fas fa-eye-slash me-1"></i> Ẩn trong 24 giờ
                </button>
                <script>
                function copyLicenseKey() {
                    const licenseKey = '<?=$CMSNT->site('license_key');?>';
                    navigator.clipboard.writeText(licenseKey).then(function() {
                        // Hiển thị thông báo thành công
                        const button = document.getElementById('copyLicenseKey');
                        const originalText = button.innerHTML;
                        button.innerHTML = '<i class="fas fa-check me-1"></i> Đã sao chép';
                        button.classList.remove('btn-info');
                        button.classList.add('btn-success');

                        // Khôi phục lại trạng thái ban đầu sau 2 giây
                        setTimeout(function() {
                            button.innerHTML = originalText;
                            button.classList.remove('btn-success');
                            button.classList.add('btn-info');
                        }, 2000);
                    }).catch(function(err) {
                        console.error('Không thể sao chép: ', err);
                        alert('Không thể sao chép giấy phép. Vui lòng thử lại.');
                    });
                }
                </script>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <script>
        document.getElementById('hideAlert24h').addEventListener('click', function() {
            // Lưu thời gian ẩn vào localStorage
            localStorage.setItem('hideAlertUntil', Date.now() + 24 * 60 * 60 * 1000);
            // Ẩn thông báo
            this.closest('.alert').style.display = 'none';
        });

        // Kiểm tra xem thông báo có nên hiển thị không khi trang tải
        document.addEventListener('DOMContentLoaded', function() {
            const hideUntil = localStorage.getItem('hideAlertUntil');
            const alertElement = document.querySelector('.alert-secondary');

            if (hideUntil && Date.now() < parseInt(hideUntil) && alertElement) {
                alertElement.style.display = 'none';
            }
        });
        </script>
        <?php endif?>
        <?php if($CMSNT->site('smtp_status') != 1):?>
        <div class="alert alert-warning alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-warning" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path d="M1 21h22L12 2 1 21zm12-3h-2v-2h2v2zm0-4h-2v-4h2v4z" />
            </svg>
            Vui lòng cấu hình <b>SMTP</b> để sử dụng toàn bộ tiện ích từ Mail:
            <a class="text-primary"
                href="https://help.cmsnt.co/huong-dan/huong-dan-cau-hinh-smtp-vao-website-shopclone7/"
                target="_blank">Xem Hướng Dẫn</a>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i
                    class="bi bi-x"></i></button>
        </div>
        <?php endif?>
        <?php if($CMSNT->site('debug_auto_bank') == 1):?>
        <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-danger" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z" />
            </svg>
            Vui lòng tắt chức năng <b>Debug Auto Bank</b> khi không gặp vấn đề về Auto Bank. Tắt chức năng này trong Cài
            Đặt -> ON/OFF Debug Auto Bank
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i
                    class="bi bi-x"></i></button>
        </div>
        <?php endif?>
        <?php if(DEBUG):?>
        <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-danger" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z" />
            </svg>
            Vui lòng tắt <b>DEBUG</b> trong file <code>.env</code> nếu trong môi trường <b>PRODUCTION</b>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i
                    class="bi bi-x"></i></button>
        </div>
        <?php endif?>

        <?php if(file_exists(__DIR__ . '/../../installer.php')): ?>
        <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-danger" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z" />
            </svg>
            <strong><?=__('CẢNH BÁO BẢO MẬT NGHIÊM TRỌNG!');?></strong> <?=__('Vui lòng xóa file');?> <b
                style="color:red;">installer.php</b>
            <?=__('trong thư mục gốc ngay lập tức để bảo vệ bảo mật website trong môi trường');?> <b>PRODUCTION</b>
            <div class="mt-2">
                <button type="button" class="btn btn-sm btn-light" onclick="deleteInstallerFile()">
                    <i class="ri-delete-bin-line me-1"></i><?=__('Xóa ngay');?>
                </button>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"><i
                    class="bi bi-x"></i></button>
        </div>
        <script>
        function deleteInstallerFile() {
            Swal.fire({
                title: '<?=__('Xác nhận xóa file installer.php');?>',
                text: '<?=__('Bạn có chắc chắn muốn xóa file installer.php? Hành động này không thể hoàn tác.');?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: '<?=__('Xóa ngay');?>',
                cancelButtonText: '<?=__('Hủy');?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "<?=BASE_URL('ajaxs/admin/remove.php');?>",
                        method: "POST",
                        dataType: "JSON",
                        data: {
                            action: 'deleteInstallerFile',
                            token: '<?=$getUser['token'];?>'
                        },
                        success: function(response) {
                            if (response.status == 'success') {
                                Swal.fire({
                                    title: '<?=__('Thành công!');?>',
                                    text: response.msg,
                                    icon: 'success',
                                    confirmButtonText: '<?=__('Đóng');?>'
                                }).then(() => {
                                    location.reload();
                                });
                            } else {
                                Swal.fire('<?=__('Lỗi!');?>', response.msg, 'error');
                            }
                        },
                        error: function() {
                            Swal.fire('<?=__('Lỗi!');?>', '<?=__('Có lỗi xảy ra khi xóa file');?>',
                                'error');
                        }
                    });
                }
            });
        }
        </script>
        <?php endif; ?>

        <?php  if (time() - $CMSNT->site('check_time_cron_cron') >= 120):?>
        <div class="alert alert-danger alert-dismissible fade show custom-alert-icon shadow-sm" role="alert">
            <svg class="svg-danger" xmlns="http://www.w3.org/2000/svg" height="1.5rem" viewBox="0 0 24 24"
                width="1.5rem" fill="#000000">
                <path d="M0 0h24v24H0z" fill="none" />
                <path
                    d="M15.73 3H8.27L3 8.27v7.46L8.27 21h7.46L21 15.73V8.27L15.73 3zM12 17.3c-.72 0-1.3-.58-1.3-1.3 0-.72.58-1.3 1.3-1.3.72 0 1.3.58 1.3 1.3 0 .72-.58 1.3-1.3 1.3zm1-4.3h-2V7h2v6z" />
            </svg>
            <?=__('Vui lòng thực hiện');?> <b><a target="_blank" class="text-primary"
                    href="https://help.cmsnt.co/huong-dan/huong-dan-xu-ly-khi-website-bao-loi-cron/">CRON JOB</a></b>
            <?=__('liên kết');?>:
            <a class="text-primary" href="<?=base_url('cron/cron.php?key='.$CMSNT->site('key_cron_job'));?>"
                target="_blank">
                <?=base_url('cron/cron.php?key='.$CMSNT->site('key_cron_job'));?>
            </a> <?=__('1 phút 1 lần để hệ thống tự động xử lý các tác vụ quan trọng của website.');?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                <i class="bi bi-x"></i>
            </button>
        </div>
        <?php endif;?>
        <div class="row">
            <?php if(checkPermission($getUser['admin'], 'view_statistical') == true):?>
            <div class="col-12">
                <div class="text-right mb-3">
                    <img src="<?=base_url('assets/img/gif-live.gif');?>" width="60px">
                </div>
            </div>
            <!-- Thành viên đăng ký toàn thời gian -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Thành viên đăng ký');?></span>
                                <h5 class="fw-semibold mb-2" id="total_users_all">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent"><?=__('Toàn thời gian');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng đã bán toàn thời gian -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-info">
                                    <i class="fa-solid fa-cart-shopping fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Đơn hàng đã bán');?></span>
                                <h5 class="fw-semibold mb-2" id="total_orders_all">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-info-transparent"><?=__('Toàn thời gian');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu đơn hàng toàn thời gian -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-warning">
                                    <i class="fa-solid fa-chart-simple fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Doanh thu đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="total_pay_all">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-warning-transparent"><?=__('Toàn thời gian');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lợi nhuận đơn hàng toàn thời gian -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Lợi nhuận đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="profit_all">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent"><?=__('Toàn thời gian');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Thành viên đăng ký tháng này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Thành viên đăng ký');?></span>
                                <h5 class="fw-semibold mb-2" id="new_users_month">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent"><?=__('Tháng');?>
                                        <?=date('m', time());?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Đơn hàng đã bán tháng này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-info">
                                    <i class="fa-solid fa-cart-shopping fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Đơn hàng đã bán');?></span>
                                <h5 class="fw-semibold mb-2" id="total_orders_month">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-info-transparent"><?=__('Tháng');?>
                                        <?=date('m', time());?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Doanh thu đơn hàng tháng này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-warning">
                                    <i class="fa-solid fa-chart-simple fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Doanh thu đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="total_pay_month">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-warning-transparent"><?=__('Tháng');?>
                                        <?=date('m', time());?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lợi nhuận đơn hàng tháng này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Lợi nhuận đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="profit_month">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent"><?=__('Tháng');?>
                                        <?=date('m', time());?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Thành viên đăng ký tuần này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Thành viên đăng ký');?></span>
                                <h5 class="fw-semibold mb-2" id="new_users_week">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent"><?=__('Tuần này');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Đơn hàng đã bán tuần này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-info">
                                    <i class="fa-solid fa-cart-shopping fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Đơn hàng đã bán');?></span>
                                <h5 class="fw-semibold mb-2" id="total_orders_week">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-info-transparent"><?=__('Tuần này');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu đơn hàng tuần này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-warning">
                                    <i class="fa-solid fa-chart-simple fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Doanh thu đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="total_pay_week">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-warning-transparent"><?=__('Tuần này');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lợi nhuận đơn hàng tuần này -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Lợi nhuận đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="profit_week">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent"><?=__('Tuần này');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- ========================= Kết thúc Thống kê tuần này ========================= -->


            <!-- Thành viên đăng ký hôm nay -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card primary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-primary">
                                    <i class="fa-solid fa-users fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Thành viên đăng ký');?></span>
                                <h5 class="fw-semibold mb-2" id="new_users_today">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-primary-transparent"><?=__('Hôm nay');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Đơn hàng đã bán hôm nay -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card secondary">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-info">
                                    <i class="fa-solid fa-cart-shopping fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Đơn hàng đã bán');?></span>
                                <h5 class="fw-semibold mb-2" id="total_orders_today">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-info-transparent"><?=__('Hôm nay');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doanh thu đơn hàng hôm nay -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card warning">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-warning">
                                    <i class="fa-solid fa-chart-simple fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Doanh thu đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="total_pay_today">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-warning-transparent"><?=__('Hôm nay');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Lợi nhuận đơn hàng hôm nay -->
            <div class="col-xxl-3 col-xl-3 col-lg-6 col-md-6 col-sm-6">
                <div class="card custom-card hrm-main-card danger">
                    <div class="card-body">
                        <div class="d-flex align-items-top">
                            <div class="me-3">
                                <span class="avatar bg-danger">
                                    <i class="fa-solid fa-money-bill-trend-up fs-18"></i>
                                </span>
                            </div>
                            <div class="flex-fill">
                                <span class="fw-semibold text-muted d-block mb-2"><?=__('Lợi nhuận đơn hàng');?></span>
                                <h5 class="fw-semibold mb-2" id="profit_today">
                                    <div class="spinner" style="display: none;">
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </div>
                                </h5>
                                <p class="mb-0">
                                    <span class="badge bg-danger-transparent"><?=__('Hôm nay');?></span>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <script>
            function show_thong_ke_dashboard() {
                $.ajax({
                    url: '<?=base_url('ajaxs/admin/view.php');?>',
                    method: 'POST',
                    dataType: 'JSON',
                    data: {
                        action: 'show_thong_ke_dashboard',
                        token: '<?=$getUser['token'];?>'
                    },
                    beforeSend: function() {
                        $('.spinner').show(); // Hiển thị icon spin trước khi gửi yêu cầu
                    },
                    success: function(data) {
                        if (data.status !== 'error') {
                            // Cập nhật dữ liệu Toàn thời gian
                            $('#total_users_all').text(data.total_users_all);
                            $('#total_orders_all').text(data.total_orders_all);
                            $('#total_pay_all').text(data.total_pay_all);
                            $('#profit_all').text(data.profit_all);

                            // Cập nhật dữ liệu Tháng này
                            $('#new_users_month').text(data.new_users_month);
                            $('#total_orders_month').text(data.total_orders_month);
                            $('#total_pay_month').text(data.total_pay_month);
                            $('#profit_month').text(data.profit_month);

                            // Cập nhật dữ liệu Hôm nay
                            $('#new_users_today').text(data.new_users_today);
                            $('#total_orders_today').text(data.total_orders_today);
                            $('#total_pay_today').text(data.total_pay_today);
                            $('#profit_today').text(data.profit_today);

                            // Cập nhật dữ liệu Tuần này
                            $('#new_users_week').text(data.new_users_week);
                            $('#total_orders_week').text(data.total_orders_week);
                            $('#total_pay_week').text(data.total_pay_week);
                            $('#profit_week').text(data.profit_week);
                        } else {
                            // Xử lý khi có lỗi từ phía backend
                            alert(data.msg);
                        }
                    },
                    complete: function() {
                        $('.spinner').hide(); // Ẩn icon spin sau khi hoàn thành yêu cầu
                    },
                    error: function(xhr, status, error) {
                        console.error('AJAX Error:', error);
                        $('.spinner').hide(); // Ẩn icon spin trong trường hợp lỗi
                    }
                });
            }

            $(document).ready(function() {
                show_thong_ke_dashboard(); // Cập nhật dữ liệu ngay khi tải trang
                setInterval(show_thong_ke_dashboard, 5000); // Cập nhật dữ liệu mỗi 5 giây
            });
            </script>


            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0" id="chart-order-title"><?=__('THỐNG KÊ ĐƠN HÀNG THÁNG');?>
                            <?=date('m');?></h6>
                        <div class="dropdown">
                            <select id="chart-time-range" class="form-select form-select-sm">
                                <option value="week"><?=__('7 ngày gần đây');?></option>
                                <option value="month" selected><?=__('Tháng');?> <?=date('m');?></option>
                                <option value="year"><?=__('Năm');?> <?=date('Y');?></option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Thêm hiệu ứng loading cho chart đơn hàng -->
                        <div id="chart-order-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"><?=__('Đang tải...');?></span>
                            </div>
                            <div class="mt-2"><?=__('Đang tải dữ liệu biểu đồ...');?></div>
                        </div>
                        <canvas id="chartjs-line" class="chartjs-chart" style="height: 300px;"></canvas>
                        <script>
                        (function() {
                            document.addEventListener('DOMContentLoaded', function() {
                                let myChart;

                                function loadChartData(timeRange) {
                                    // Cập nhật tiêu đề
                                    let titleText;
                                    switch (timeRange) {
                                        case 'week':
                                            titleText = '<?=__('THỐNG KÊ ĐƠN HÀNG 7 NGÀY GẦN ĐÂY');?>';
                                            break;
                                        case 'month':
                                            titleText =
                                                '<?=__('THỐNG KÊ ĐƠN HÀNG THÁNG');?> <?=date('m');?>';
                                            break;
                                        case 'year':
                                            titleText = '<?=__('THỐNG KÊ ĐƠN HÀNG NĂM');?> <?=date('Y');?>';
                                            break;
                                    }
                                    document.getElementById('chart-order-title').innerText = titleText;

                                    // Hiển thị loader và ẩn chart
                                    document.getElementById('chart-order-loader').style.display = 'block';
                                    document.getElementById('chartjs-line').style.opacity = '0';

                                    // Hủy biểu đồ cũ nếu tồn tại
                                    if (myChart) {
                                        myChart.destroy();
                                    }

                                    // Gọi API lấy dữ liệu mới
                                    $.ajax({
                                        url: '<?=base_url('ajaxs/admin/view.php');?>',
                                        method: 'POST',
                                        dataType: 'json',
                                        data: {
                                            action: 'view_chart_thong_ke_don_hang',
                                            token: '<?=$getUser['token'];?>',
                                            time_range: timeRange
                                        },
                                        success: function(response) {
                                            // Ẩn loader và hiển thị biểu đồ
                                            document.getElementById('chart-order-loader').style
                                                .display = 'none';
                                            document.getElementById('chartjs-line').style
                                                .opacity = '1';

                                            const labels = response.labels;
                                            const revenues = response.revenues;
                                            const profits = response.profits;
                                            const data = {
                                                labels: labels,
                                                datasets: [{
                                                        label: '<?=__('Lợi nhuận');?>',
                                                        backgroundColor: 'rgb(73,182,245)',
                                                        borderColor: 'rgb(73,182,245)',
                                                        data: profits,
                                                    },
                                                    {
                                                        label: '<?=__('Doanh thu');?>',
                                                        backgroundColor: 'rgb(132, 90, 223)',
                                                        borderColor: 'rgb(132, 90, 223)',
                                                        data: revenues,
                                                    }
                                                ]
                                            };
                                            const config = {
                                                type: 'bar',
                                                data: data,
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false
                                                }
                                            };
                                            myChart = new Chart(
                                                document.getElementById('chartjs-line'),
                                                config
                                            );
                                        },
                                        error: function() {
                                            // Ẩn loader khi có lỗi
                                            document.getElementById('chart-order-loader').style
                                                .display = 'none';
                                            document.getElementById('chartjs-line').style
                                                .opacity = '1';

                                            // Hiển thị biểu đồ thông báo lỗi
                                            const config = {
                                                type: 'bar',
                                                data: {
                                                    labels: [
                                                        '<?=__('Không có dữ liệu');?>'],
                                                    datasets: []
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        title: {
                                                            display: true,
                                                            text: '<?=__('Không thể tải dữ liệu biểu đồ. Vui lòng thử lại sau.');?>',
                                                            color: '#dc3545',
                                                            font: {
                                                                size: 16
                                                            }
                                                        }
                                                    }
                                                }
                                            };

                                            myChart = new Chart(
                                                document.getElementById('chartjs-line'),
                                                config
                                            );
                                        }
                                    });
                                }

                                // Xử lý sự kiện khi người dùng thay đổi khoảng thời gian
                                document.getElementById('chart-time-range').addEventListener('change',
                                    function() {
                                        loadChartData(this.value);
                                    });

                                // Khởi tạo biểu đồ với tháng hiện tại
                                setTimeout(function() {
                                    Chart.defaults.borderColor = "rgba(142, 156, 173,0.1)";
                                    Chart.defaults.color = "#8c9097";
                                    loadChartData('month');
                                }, 5);
                            });
                        })();
                        </script>
                    </div>
                </div>
            </div>

            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header d-flex align-items-center justify-content-between">
                        <h6 class="card-title mb-0" id="chart-deposit-title"><?=__('THỐNG KÊ NẠP TIỀN THÁNG');?>
                            <?=date('m')?></h6>
                        <div class="dropdown">
                            <select id="chart-deposit-time-range" class="form-select form-select-sm">
                                <option value="week"><?=__('7 ngày gần đây');?></option>
                                <option value="month" selected><?=__('Tháng');?> <?=date('m')?></option>
                                <option value="year"><?=__('Năm');?> <?=date('Y')?></option>
                            </select>
                        </div>
                    </div>
                    <div class="card-body">
                        <!-- Thêm hiệu ứng loading cho chart nạp tiền -->
                        <div id="chart-deposit-loader" class="text-center py-5" style="display: none;">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden"><?=__('Đang tải...');?></span>
                            </div>
                            <div class="mt-2"><?=__('Đang tải dữ liệu biểu đồ...');?></div>
                        </div>
                        <canvas id="chartjs-naptien" class="chartjs-chart" style="height: 300px;"></canvas>
                        <script>
                        (function() {
                            document.addEventListener('DOMContentLoaded', function() {
                                let myChart;

                                function loadChartData(timeRange) {
                                    // Cập nhật tiêu đề
                                    let titleText;
                                    switch (timeRange) {
                                        case 'week':
                                            titleText = '<?=__('THỐNG KÊ NẠP TIỀN 7 NGÀY GẦN ĐÂY');?>';
                                            break;
                                        case 'month':
                                            titleText =
                                            '<?=__('THỐNG KÊ NẠP TIỀN THÁNG');?> <?=date('m')?>';
                                            break;
                                        case 'year':
                                            titleText = '<?=__('THỐNG KÊ NẠP TIỀN NĂM');?> <?=date('Y')?>';
                                            break;
                                    }
                                    document.getElementById('chart-deposit-title').innerText = titleText;

                                    // Hiển thị loader và ẩn chart
                                    document.getElementById('chart-deposit-loader').style.display = 'block';
                                    document.getElementById('chartjs-naptien').style.opacity = '0';

                                    // Hủy biểu đồ cũ nếu tồn tại
                                    if (myChart) {
                                        myChart.destroy();
                                    }

                                    // Gọi API lấy dữ liệu mới
                                    $.ajax({
                                        url: '<?=base_url('ajaxs/admin/view.php');?>',
                                        method: 'POST',
                                        dataType: 'json',
                                        data: {
                                            action: 'view_chart_thong_ke_nap_tien',
                                            token: '<?=$getUser['token'];?>',
                                            time_range: timeRange
                                        },
                                        success: function(response) {
                                            // Ẩn loader và hiển thị biểu đồ
                                            document.getElementById('chart-deposit-loader')
                                                .style.display = 'none';
                                            document.getElementById('chartjs-naptien').style
                                                .opacity = '1';

                                            const labels = response.labels;
                                            const revenues = response.amount;
                                            const data = {
                                                labels: labels,
                                                datasets: [{
                                                    label: '<?=__('Doanh thu');?>',
                                                    backgroundColor: 'rgb(29, 78, 216)',
                                                    borderColor: 'rgb(29, 78, 216)',
                                                    data: revenues,
                                                }]
                                            };
                                            const config = {
                                                type: 'bar',
                                                data: data,
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false
                                                }
                                            };
                                            myChart = new Chart(
                                                document.getElementById('chartjs-naptien'),
                                                config
                                            );
                                        },
                                        error: function() {
                                            // Ẩn loader khi có lỗi
                                            document.getElementById('chart-deposit-loader')
                                                .style.display = 'none';
                                            document.getElementById('chartjs-naptien').style
                                                .opacity = '1';

                                            // Hiển thị biểu đồ thông báo lỗi
                                            const config = {
                                                type: 'bar',
                                                data: {
                                                    labels: [
                                                        '<?=__('Không có dữ liệu');?>'],
                                                    datasets: []
                                                },
                                                options: {
                                                    responsive: true,
                                                    maintainAspectRatio: false,
                                                    plugins: {
                                                        title: {
                                                            display: true,
                                                            text: '<?=__('Không thể tải dữ liệu biểu đồ. Vui lòng thử lại sau.');?>',
                                                            color: '#dc3545',
                                                            font: {
                                                                size: 16
                                                            }
                                                        }
                                                    }
                                                }
                                            };

                                            myChart = new Chart(
                                                document.getElementById('chartjs-naptien'),
                                                config
                                            );
                                        }
                                    });
                                }

                                // Xử lý sự kiện khi người dùng thay đổi khoảng thời gian
                                document.getElementById('chart-deposit-time-range').addEventListener(
                                    'change',
                                    function() {
                                        loadChartData(this.value);
                                    });

                                // Khởi tạo biểu đồ với tháng hiện tại
                                setTimeout(function() {
                                    Chart.defaults.borderColor = "rgba(142, 156, 173,0.1)";
                                    Chart.defaults.color = "#8c9097";
                                    loadChartData('month');
                                }, 5);
                            });
                        })();
                        </script>
                    </div>
                </div>
            </div>



            <?php endif?>
            <?php if(checkPermission($getUser['admin'], 'view_recent_transactions') == true):?>
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title"><?=__('ĐƠN HÀNG GẦN ĐÂY');?></div>
                        <div class="ms-auto">
                            <img class="text-right" src="<?=base_url('assets/img/gif-live.gif');?>" width="60px">
                        </div>
                    </div>
                </div>
                <ul class="timeline list-unstyled orders-timeline"
                    style="height:500px;overflow-x:hidden;overflow-y:auto;">

                </ul>
            </div>
            <div class="col-xl-6">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title"><?=__('NẠP TIỀN GẦN ĐÂY');?></div>
                        <div class="ms-auto">
                            <img class="text-right" src="<?=base_url('assets/img/gif-live.gif');?>" width="60px">
                        </div>
                    </div>
                </div>
                <ul class="timeline list-unstyled deposits-timeline"
                    style="height:500px;overflow-x:hidden;overflow-y:auto;">

                </ul>
            </div>
            <script>
            function fetchOrders() {
                $.ajax({
                    url: "<?=base_url('ajaxs/admin/view.php');?>",
                    method: 'POST',
                    data: {
                        action: 'view_don_hang_gan_day',
                        token: '<?=$getUser['token'];?>'
                    },
                    success: function(html) {
                        $('.orders-timeline').html(html);
                    }
                });
            }

            function fetchDeposits() {
                $.ajax({
                    url: "<?=base_url('ajaxs/admin/view.php');?>",
                    method: 'POST',
                    data: {
                        action: 'view_nap_tien_gan_day',
                        token: '<?=$getUser['token'];?>'
                    },
                    success: function(html) {
                        $('.deposits-timeline').html(html);
                    }
                });
            }
            setInterval(fetchOrders, 5000);
            setInterval(fetchDeposits, 5000);

            $(document).ready(function() {
                fetchOrders();
                fetchDeposits();
            });
            </script>
            <?php endif?>
        </div>
    </div>
</div>

<!-- Floating buttons -->
<div class="position-fixed" style="bottom: 80px; right: 20px; z-index: 1000;">
    <!-- Button Suppliers Stats -->
    <div class="mb-3">
        <button type="button" class="btn btn-warning btn-icon rounded-circle shadow-lg" onclick="showSuppliersStats()"
            data-toggle="tooltip" data-placement="left"
            title="<?=__('Thống kê nhà cung cấp hôm nay')?>">
            <i class="fas fa-truck fs-18"></i>
        </button>
    </div>

    <!-- Button Top Services -->
    <div class="mb-3">
        <button type="button" class="btn btn-success btn-icon rounded-circle shadow-lg" onclick="showTopServices()"
            data-toggle="tooltip" data-placement="left"
            title="<?=__('Top 50 dịch vụ bán chạy nhất hôm nay')?>">
            <i class="fas fa-chart-bar fs-18"></i>
        </button>
    </div>
    
    <!-- Button Leaderboard -->
    <div class="mb-3">
        <button type="button" class="btn btn-primary btn-icon rounded-circle shadow-lg" onclick="showLeaderboard()"
            data-toggle="tooltip" data-placement="left"
            title="<?=__('Top 50 khách hàng chi tiêu nhiều nhất hôm nay')?>">
            <i class="fas fa-trophy fs-18"></i>
        </button>
    </div>
    
    <!-- Button AI Chat -->
    <div>
        <button type="button" class="btn btn-dark btn-icon rounded-circle shadow-lg" onclick="showAIChatPopup()"
            data-toggle="tooltip" data-placement="left"
            title="<?=__('Chat với AI Assistant')?>">
            <i class="fas fa-robot fs-18"></i>
        </button>
    </div>
</div>

<!-- Modal Bảng xếp hạng -->
<div class="modal fade" id="leaderboardModal" tabindex="-1" aria-labelledby="leaderboardModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="leaderboardModalLabel">
                    <i class="fas fa-trophy text-warning me-2"></i><?=__('Bảng xếp hạng ngày')?> <span
                        id="leaderboard-date"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="leaderboard-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"><?=__('Đang tải...')?></span>
                    </div>
                    <div class="mt-2"><?=__('Đang tải bảng xếp hạng...')?></div>
                </div>
                
                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong><?=__('Top 50 khách hàng chi tiêu nhiều nhất hôm nay')?></strong><br/>
                        <small><?=__('Danh sách được cập nhật theo thời gian thực')?></small>
                    </div>
                </div>
                
                <div id="leaderboard-content" class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-primary sticky-top">
                            <tr>
                                <th class="text-center" style="width: 80px;"><?=__('Hạng')?></th>
                                <th style="width: 170px;"><?=__('ID - Tên đăng nhập')?></th>
                                <th><?=__('Địa chỉ Email')?></th>
                                <th class="text-end" style="width: 140px;"><?=__('Tổng chi tiêu')?></th>
                                <th class="text-center" style="width: 100px;"><?=__('Số đơn hàng')?></th>
                            </tr>
                        </thead>
                        <tbody id="leaderboard-table-body">
                            <!-- Dữ liệu sẽ được tải bằng AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <div id="leaderboard-empty" class="text-center py-4" style="display: none;">
                    <i class="fas fa-inbox text-muted mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted"><?=__('Chưa có dữ liệu bảng xếp hạng cho ngày hôm nay')?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i><?=__('Đóng')?>
                </button>
                <button type="button" class="btn btn-primary" onclick="loadLeaderboard()">
                    <i class="fas fa-sync-alt me-1"></i><?=__('Làm mới')?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Suppliers Stats -->
<div class="modal fade" id="suppliersStatsModal" tabindex="-1" aria-labelledby="suppliersStatsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="suppliersStatsModalLabel">
                    <i class="fas fa-truck text-warning me-2"></i><?=__('Thống kê nhà cung cấp')?> <span id="suppliers-date"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="suppliers-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-warning" role="status">
                        <span class="visually-hidden"><?=__('Đang tải...')?></span>
                    </div>
                    <div class="mt-2"><?=__('Đang tải thống kê nhà cung cấp...')?></div>
                </div>
                
                <div class="alert alert-warning d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong><?=__('Thống kê nhà cung cấp hôm nay')?></strong><br/>
                        <small><?=__('Được sắp xếp theo doanh thu từ cao đến thấp')?></small>
                    </div>
                </div>
                
                <div id="suppliers-content" class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-warning sticky-top">
                            <tr>
                                <th class="text-center" style="width: 60px;"><?=__('Hạng')?></th>
                                <th style="width: 320px;"><?=__('Nhà cung cấp')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Doanh thu')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Chi phí')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Lợi nhuận')?></th>
                                <th class="text-center" style="width: 100px;"><?=__('Số đơn')?></th>
                                <th class="text-center" style="width: 100px;"><?=__('Tỷ lệ LN')?></th>
                            </tr>
                        </thead>
                        <tbody id="suppliers-table-body">
                            <!-- Dữ liệu sẽ được tải bằng AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <div id="suppliers-empty" class="text-center py-4" style="display: none;">
                    <i class="fas fa-truck text-muted mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted"><?=__('Chưa có đơn hàng nào từ nhà cung cấp trong ngày hôm nay')?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i><?=__('Đóng')?>
                </button>
                <button type="button" class="btn btn-warning" onclick="loadSuppliersStats()">
                    <i class="fas fa-sync-alt me-1"></i><?=__('Làm mới')?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Top Services -->
<div class="modal fade" id="topServicesModal" tabindex="-1" aria-labelledby="topServicesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="topServicesModalLabel">
                    <i class="fas fa-chart-bar text-success me-2"></i><?=__('Top dịch vụ bán chạy nhất')?> <span id="services-date"></span>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="max-height: 600px; overflow-y: auto;">
                <div id="services-loading" class="text-center py-4" style="display: none;">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden"><?=__('Đang tải...')?></span>
                    </div>
                    <div class="mt-2"><?=__('Đang tải danh sách dịch vụ...')?></div>
                </div>
                
                <div class="alert alert-success d-flex align-items-center mb-3" role="alert">
                    <i class="fas fa-info-circle me-2"></i>
                    <div>
                        <strong><?=__('Top 50 dịch vụ có doanh thu cao nhất hôm nay')?></strong><br/>
                        <small><?=__('Được sắp xếp theo tổng doanh thu từ cao đến thấp')?></small>
                    </div>
                </div>
                
                <div id="services-content" class="table-responsive">
                    <table class="table table-striped table-hover table-sm">
                        <thead class="table-success sticky-top">
                            <tr>
                                <th class="text-center" style="width: 60px;"><?=__('Hạng')?></th>
                                <th style="width: 320px;"><?=__('ID - Tên dịch vụ')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Doanh thu')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Chi phí')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Lợi nhuận')?></th>
                                <th class="text-center" style="width: 100px;"><?=__('Số đơn')?></th>
                                <th class="text-end" style="width: 120px;"><?=__('Giá TB')?></th>
                            </tr>
                        </thead>
                        <tbody id="services-table-body">
                            <!-- Dữ liệu sẽ được tải bằng AJAX -->
                        </tbody>
                    </table>
                </div>
                
                <div id="services-empty" class="text-center py-4" style="display: none;">
                    <i class="fas fa-box-open text-muted mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted"><?=__('Chưa có dịch vụ nào được bán trong ngày hôm nay')?></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i><?=__('Đóng')?>
                </button>
                <button type="button" class="btn btn-success" onclick="loadTopServices()">
                    <i class="fas fa-sync-alt me-1"></i><?=__('Làm mới')?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function showLeaderboard() {
    // Đóng các modal khác nếu đang mở
    if ($('#topServicesModal').hasClass('show')) {
        $('#topServicesModal').modal('hide');
    }
    if ($('#suppliersStatsModal').hasClass('show')) {
        $('#suppliersStatsModal').modal('hide');
    }
    
    // Hiển thị modal và tải dữ liệu
    $('#leaderboardModal').modal('show');
    loadLeaderboard();
}

function loadLeaderboard() {
    // Hiển thị loading
    $('#leaderboard-loading').show();
    $('#leaderboard-content').hide();
    $('#leaderboard-empty').hide();

    $.ajax({
        url: '<?=base_url('ajaxs/admin/view.php')?>',
        method: 'POST',
        dataType: 'JSON',
        data: {
            action: 'get_daily_leaderboard',
            token: '<?=$getUser['token']?>'
        },
        success: function(response) {
            // Ẩn loading
            $('#leaderboard-loading').hide();

            if (response.status === 'success') {
                // Cập nhật ngày
                $('#leaderboard-date').text(response.date);

                if (response.data.length > 0) {
                    // Hiển thị bảng
                    $('#leaderboard-content').show();

                    // Xóa dữ liệu cũ
                    $('#leaderboard-table-body').empty();

                    // Thêm dữ liệu mới
                    response.data.forEach(function(user) {
                        let rankClass = '';
                        let rankIcon = '';

                        if (user.rank === 1) {
                            rankClass = 'text-warning fw-bold';
                            rankIcon = '<i class="fas fa-crown text-warning me-1"></i>';
                        } else if (user.rank === 2) {
                            rankClass = 'text-secondary fw-bold';
                            rankIcon = '<i class="fas fa-medal text-secondary me-1"></i>';
                        } else if (user.rank === 3) {
                            rankClass = 'text-danger fw-bold';
                            rankIcon = '<i class="fas fa-medal text-danger me-1"></i>';
                        }

                        let row = `
                            <tr>
                                <td class="text-center ${rankClass}">
                                    ${rankIcon}${user.rank}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?=base_url_admin('user-edit&id=')?>${user.id}" 
                                           class="text-decoration-none fw-bold text-primary" data-toggle="tooltip" data-placement="left"
                                           title="<?=__('Chỉnh sửa thành viên')?>">
                                            ${user.username}
                                        </a>
                                        <small class="text-muted"><?=__('ID:')?> ${user.id}</small>
                                    </div>
                                </td>
                                <td>${user.email}</td>
                                <td class="text-end fw-bold text-success">${user.total_spent}</td>
                                <td class="text-center">
                                    <span class="badge bg-primary">${user.total_orders}</span>
                                </td>
                            </tr>
                        `;
                        $('#leaderboard-table-body').append(row);
                    });
                } else {
                    // Hiển thị thông báo trống
                    $('#leaderboard-empty').show();
                }
            } else {
                // Hiển thị lỗi
                $('#leaderboard-empty').show();
                $('#leaderboard-empty').html(`
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted">${response.msg}</p>
                `);
            }
        },
        error: function() {
            // Ẩn loading và hiển thị lỗi
            $('#leaderboard-loading').hide();
            $('#leaderboard-empty').show();
            $('#leaderboard-empty').html(`
                <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 3rem;"></i>
                <p class="text-muted"><?=__('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.')?></p>
            `);
        }
    });
}

// Tự động tải bảng xếp hạng khi modal được mở
$('#leaderboardModal').on('shown.bs.modal', function() {
    if ($('#leaderboard-table-body').is(':empty')) {
        loadLeaderboard();
    }
});

// ========== Suppliers Stats Functions ==========
function showSuppliersStats() {
    // Đóng các modal khác nếu đang mở
    if ($('#leaderboardModal').hasClass('show')) {
        $('#leaderboardModal').modal('hide');
    }
    if ($('#topServicesModal').hasClass('show')) {
        $('#topServicesModal').modal('hide');
    }
    
    // Hiển thị modal và tải dữ liệu
    $('#suppliersStatsModal').modal('show');
    loadSuppliersStats();
}

function loadSuppliersStats() {
    // Hiển thị loading
    $('#suppliers-loading').show();
    $('#suppliers-content').hide();
    $('#suppliers-empty').hide();
    
    $.ajax({
        url: '<?=base_url('ajaxs/admin/view.php')?>',
        method: 'POST',
        dataType: 'JSON',
        data: {
            action: 'get_daily_suppliers_stats',
            token: '<?=$getUser['token']?>'
        },
        success: function(response) {
            // Ẩn loading
            $('#suppliers-loading').hide();
            
            if (response.status === 'success') {
                // Cập nhật ngày
                $('#suppliers-date').text(response.date);
                
                if (response.data.length > 0) {
                    // Hiển thị bảng
                    $('#suppliers-content').show();
                    
                    // Xóa dữ liệu cũ
                    $('#suppliers-table-body').empty();
                    
                    // Thêm dữ liệu mới
                    response.data.forEach(function(supplier) {
                        let rankClass = '';
                        let rankIcon = '';
                        
                        if (supplier.rank === 1) {
                            rankClass = 'text-warning fw-bold';
                            rankIcon = '<i class="fas fa-crown text-warning me-1"></i>';
                        } else if (supplier.rank === 2) {
                            rankClass = 'text-secondary fw-bold';
                            rankIcon = '<i class="fas fa-medal text-secondary me-1"></i>';
                        } else if (supplier.rank === 3) {
                            rankClass = 'text-danger fw-bold';
                            rankIcon = '<i class="fas fa-medal text-danger me-1"></i>';
                        }
                        
                        // Màu lợi nhuận: xanh nếu > 0, đỏ nếu < 0
                        let profitClass = 'text-success';
                        if (supplier.profit.indexOf('-') === 0) {
                            profitClass = 'text-danger';
                        }
                        
                        // Xác định màu sắc cho số dư (price chứa text như "0 VND" hoặc "489101 VND")
                        let balanceText = supplier.price || '0';
                        let balanceClass = 'text-muted';
                        
                        // Trích xuất số từ text (ví dụ: "489101 VND" -> 489101)
                        let balanceMatch = balanceText.match(/[\d,.-]+/);
                        let balanceValue = 0;
                        if (balanceMatch) {
                            balanceValue = parseFloat(balanceMatch[0].replace(/,/g, ''));
                        }
                        
                        if (balanceValue > 0) {
                            balanceClass = 'text-success';
                        } else if (balanceValue < 0) {
                            balanceClass = 'text-danger';
                        }
                        
                        let row = `
                            <tr>
                                <td class="text-center ${rankClass}">
                                    ${rankIcon}${supplier.rank}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?=base_url_admin('supplier-manager&id=')?>${supplier.supplier_id}" 
                                           class="text-decoration-none fw-bold text-primary" data-toggle="tooltip" data-placement="left"
                                           title="<?=__('Quản lý nhà cung cấp')?>">
                                            ${supplier.supplier_name}
                                        </a>
                                        <small class="text-muted"><?=__('Loại:')?> ${supplier.type}</small>
                                        <small class="${balanceClass}"><i class="fas fa-wallet me-1"></i><?=__('Số dư:')?> ${supplier.price}</small>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-primary">${supplier.total_revenue}</td>
                                <td class="text-end text-warning">${supplier.total_cost}</td>
                                <td class="text-end fw-bold ${profitClass}">${supplier.profit}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">${supplier.total_orders}</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-success">${supplier.profit_margin}%</span>
                                </td>
                            </tr>
                        `;
                        $('#suppliers-table-body').append(row);
                    });
                } else {
                    // Hiển thị thông báo trống
                    $('#suppliers-empty').show();
                }
            } else {
                // Hiển thị lỗi
                $('#suppliers-empty').show();
                $('#suppliers-empty').html(`
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted">${response.msg}</p>
                `);
            }
        },
        error: function() {
            // Ẩn loading và hiển thị lỗi
            $('#suppliers-loading').hide();
            $('#suppliers-empty').show();
            $('#suppliers-empty').html(`
                <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 3rem;"></i>
                <p class="text-muted"><?=__('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.')?></p>
            `);
        }
    });
}

// Tự động tải thống kê suppliers khi modal được mở
$('#suppliersStatsModal').on('shown.bs.modal', function() {
    if ($('#suppliers-table-body').is(':empty')) {
        loadSuppliersStats();
    }
});

// ========== Top Services Functions ==========
function showTopServices() {
    // Đóng các modal khác nếu đang mở
    if ($('#leaderboardModal').hasClass('show')) {
        $('#leaderboardModal').modal('hide');
    }
    if ($('#suppliersStatsModal').hasClass('show')) {
        $('#suppliersStatsModal').modal('hide');
    }
    
    // Hiển thị modal và tải dữ liệu
    $('#topServicesModal').modal('show');
    loadTopServices();
}

function loadTopServices() {
    // Hiển thị loading
    $('#services-loading').show();
    $('#services-content').hide();
    $('#services-empty').hide();
    
    $.ajax({
        url: '<?=base_url('ajaxs/admin/view.php')?>',
        method: 'POST',
        dataType: 'JSON',
        data: {
            action: 'get_daily_top_services',
            token: '<?=$getUser['token']?>'
        },
        success: function(response) {
            // Ẩn loading
            $('#services-loading').hide();
            
            if (response.status === 'success') {
                // Cập nhật ngày
                $('#services-date').text(response.date);
                
                if (response.data.length > 0) {
                    // Hiển thị bảng
                    $('#services-content').show();
                    
                    // Xóa dữ liệu cũ
                    $('#services-table-body').empty();
                    
                    // Thêm dữ liệu mới
                    response.data.forEach(function(service) {
                        let rankClass = '';
                        let rankIcon = '';
                        
                        if (service.rank === 1) {
                            rankClass = 'text-warning fw-bold';
                            rankIcon = '<i class="fas fa-crown text-warning me-1"></i>';
                        } else if (service.rank === 2) {
                            rankClass = 'text-secondary fw-bold';
                            rankIcon = '<i class="fas fa-medal text-secondary me-1"></i>';
                        } else if (service.rank === 3) {
                            rankClass = 'text-danger fw-bold';
                            rankIcon = '<i class="fas fa-medal text-danger me-1"></i>';
                        }
                        
                        // Màu lợi nhuận: xanh nếu > 0, đỏ nếu < 0
                        let profitClass = 'text-success';
                        if (service.profit.indexOf('-') === 0) {
                            profitClass = 'text-danger';
                        }
                        
                        let row = `
                            <tr>
                                <td class="text-center ${rankClass}">
                                    ${rankIcon}${service.rank}
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <a href="<?=base_url_admin('service-edit&id=')?>${service.service_id}" 
                                           class="text-decoration-none fw-bold text-primary"  data-toggle="tooltip" data-placement="left"
                                           title="<?=__('Chỉnh sửa dịch vụ')?>">
                                            #${service.service_id} - ${service.service_name}
                                        </a>
                                    </div>
                                </td>
                                <td class="text-end fw-bold text-primary">${service.total_revenue}</td>
                                <td class="text-end text-warning">${service.total_cost}</td>
                                <td class="text-end fw-bold ${profitClass}">${service.profit}</td>
                                <td class="text-center">
                                    <span class="badge bg-info">${service.total_orders}</span>
                                </td>
                                <td class="text-end text-muted">${service.avg_price}</td>
                            </tr>
                        `;
                        $('#services-table-body').append(row);
                    });
                } else {
                    // Hiển thị thông báo trống
                    $('#services-empty').show();
                }
            } else {
                // Hiển thị lỗi
                $('#services-empty').show();
                $('#services-empty').html(`
                    <i class="fas fa-exclamation-triangle text-warning mb-2" style="font-size: 3rem;"></i>
                    <p class="text-muted">${response.msg}</p>
                `);
            }
        },
        error: function() {
            // Ẩn loading và hiển thị lỗi
            $('#services-loading').hide();
            $('#services-empty').show();
            $('#services-empty').html(`
                <i class="fas fa-exclamation-triangle text-danger mb-2" style="font-size: 3rem;"></i>
                <p class="text-muted"><?=__('Có lỗi xảy ra khi tải dữ liệu. Vui lòng thử lại.')?></p>
            `);
        }
    });
}

// Tự động tải danh sách services khi modal được mở
$('#topServicesModal').on('shown.bs.modal', function() {
    if ($('#services-table-body').is(':empty')) {
        loadTopServices();
    }
});

// Event listeners để đảm bảo chỉ một modal mở tại một thời điểm
$('#topServicesModal').on('show.bs.modal', function() {
    $('#leaderboardModal').modal('hide');
});

$('#leaderboardModal').on('show.bs.modal', function() {
    $('#topServicesModal').modal('hide');
});

// ========== AI Chat Popup Functions ==========
function showAIChatPopup() {
    // Đóng các modal khác nếu đang mở
    if ($('#leaderboardModal').hasClass('show')) {
        $('#leaderboardModal').modal('hide');
    }
    if ($('#suppliersStatsModal').hasClass('show')) {
        $('#suppliersStatsModal').modal('hide');
    }
    if ($('#topServicesModal').hasClass('show')) {
        $('#topServicesModal').modal('hide');
    }
    
    // Hiển thị modal AI Chat
    $('#aiChatModal').modal('show');
    
    // Tự động focus vào input khi modal mở
    $('#aiChatModal').on('shown.bs.modal', function() {
        $('#aiChatInput').focus();
    });
}

// Class quản lý AI Chat Popup
class AIChatPopup {
    constructor() {
        this.modal = $('#aiChatModal');
        this.chatContainer = $('#aiChatContainer');
        this.chatInput = $('#aiChatInput');
        this.sendButton = $('#aiChatSendBtn');
        this.loadMoreBtn = $('#aiChatLoadMoreBtn');
        this.loadingIndicator = $('#aiChatLoading');
        this.typingIndicator = $('#aiChatTyping');
        
        this.messagesPerPage = 5;
        this.currentPage = 1;
        this.hasMoreMessages = true;
        this.isLoading = false;
        this.isSending = false;
        
        this.initEventListeners();
    }
    
    initEventListeners() {
        // Send message
        this.sendButton.on('click', () => this.sendMessage());
        this.chatInput.on('keypress', (e) => {
            if (e.which === 13 && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });
        
        // Load more messages
        this.loadMoreBtn.on('click', () => this.loadMoreMessages());
        
        // Auto-resize textarea
        this.chatInput.on('input', () => {
            this.chatInput.css('height', 'auto');
            this.chatInput.css('height', this.chatInput[0].scrollHeight + 'px');
        });
        
        // Load initial messages when modal opens
        this.modal.on('shown.bs.modal', () => {
            if (this.chatContainer.find('.ai-chat-message').length === 0) {
                this.loadInitialMessages();
            }
        });
        
        // Clear typing indicator when modal closes
        this.modal.on('hidden.bs.modal', () => {
            this.hideTypingIndicator();
        });
    }
    
    async loadInitialMessages() {
        this.showLoading();
        this.hideLoadMoreButton();
        
        try {
            const response = await this.callAPI('load_history', {
                page: 1,
                limit: this.messagesPerPage
            });
            
            if (response.success && response.history) {
                this.displayMessages(response.history, false);
                this.hasMoreMessages = response.has_more || false;
                
                if (this.hasMoreMessages) {
                    this.showLoadMoreButton();
                }
            } else {
                this.showWelcomeMessage();
            }
        } catch (error) {
            console.error('Error loading messages:', error);
            this.showWelcomeMessage();
        } finally {
            this.hideLoading();
            this.scrollToBottom();
        }
    }
    
    async loadMoreMessages() {
        if (this.isLoading || !this.hasMoreMessages) return;
        
        this.isLoading = true;
        this.loadMoreBtn.html('<i class="fas fa-spinner fa-spin"></i> Đang tải...');
        
        try {
            const response = await this.callAPI('load_history', {
                page: this.currentPage + 1,
                limit: this.messagesPerPage
            });
            
            if (response.success && response.history) {
                this.displayMessages(response.history, true);
                this.currentPage++;
                this.hasMoreMessages = response.has_more || false;
                
                if (!this.hasMoreMessages) {
                    this.hideLoadMoreButton();
                }
            }
        } catch (error) {
            console.error('Error loading more messages:', error);
        } finally {
            this.isLoading = false;
            this.loadMoreBtn.html('<i class="fas fa-arrow-up"></i> Tải thêm tin nhắn');
        }
    }
    
    displayMessages(messages, prepend = false) {
        const container = this.chatContainer;
        
        messages.forEach(chat => {
            const userMessage = this.createMessageElement('user', chat.message, chat.created_at);
            const aiMessage = this.createMessageElement('ai', chat.response, chat.created_at);
            
            if (prepend) {
                container.prepend(aiMessage);
                container.prepend(userMessage);
            } else {
                container.append(userMessage);
                container.append(aiMessage);
            }
        });
    }
    
    createMessageElement(type, content, timestamp) {
        const isUser = type === 'user';
        const messageClass = isUser ? 'ai-chat-message-user' : 'ai-chat-message-ai';
        const alignClass = isUser ? 'justify-content-end' : 'justify-content-start';
        
        if (isUser) {
            return $(`
                <div class="ai-chat-message ${messageClass} d-flex ${alignClass} mb-3">
                    <div class="d-flex align-items-start" style="max-width: 80%;">
                        <div class="ai-chat-content">
                            <div class="ai-chat-bubble">
                                ${this.formatMessage(content, type)}
                            </div>
                            <div class="ai-chat-time text-muted small mt-1">
                                ${this.formatTime(timestamp)}
                            </div>
                        </div>
                        <div class="ai-chat-avatar ms-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; flex-shrink: 0;">
                            <i class="fas fa-user"></i>
                        </div>
                    </div>
                </div>
            `);
        } else {
            return $(`
                <div class="ai-chat-message ${messageClass} d-flex ${alignClass} mb-3">
                    <div class="d-flex align-items-start" style="max-width: 80%;">
                        <div class="ai-chat-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; flex-shrink: 0;">
                            <i class="fas fa-robot"></i>
                        </div>
                        <div class="ai-chat-content">
                            <div class="ai-chat-bubble">
                                ${this.formatMessage(content, type)}
                            </div>
                            <div class="ai-chat-time text-muted small mt-1">
                                ${this.formatTime(timestamp)}
                            </div>
                        </div>
                    </div>
                </div>
            `);
        }
    }
    
    formatMessage(content, type) {
        if (type === 'ai') {
            return content
                .replace(/\*\*(.*?)\*\*/g, '<strong>$1</strong>')
                .replace(/\*(.*?)\*/g, '<em>$1</em>')
                .replace(/`([^`]+)`/g, '<code>$1</code>')
                .replace(/\n/g, '<br>');
        }
        return content.replace(/\n/g, '<br>');
    }
    
    formatTime(timestamp) {
        return new Date(timestamp).toLocaleString('vi-VN', {
            hour: '2-digit',
            minute: '2-digit',
            day: '2-digit',
            month: '2-digit'
        });
    }
    
    async sendMessage() {
        const message = this.chatInput.val().trim();
        if (!message || this.isSending) return;
        
        this.isSending = true;
        this.setInputState(false);
        
        // Add user message
        const userMessage = this.createMessageElement('user', message, new Date());
        this.chatContainer.append(userMessage);
        this.chatInput.val('');
        this.chatInput.css('height', 'auto');
        
        // Hiển thị typing indicator NGAY LẬP TỨC
        this.showTypingIndicator();
        
        try {
            const startTime = Date.now();
            const response = await this.callAPI('send_message', { message });
            const elapsed = Date.now() - startTime;
            // Đảm bảo hiệu ứng typing tối thiểu 700ms
            const minTypingTime = 700;
            const waitTime = elapsed < minTypingTime ? minTypingTime - elapsed : 0;
            
            setTimeout(() => {
                this.hideTypingIndicator();
                let aiHtml;
                if (response.success) {
                    aiHtml = this.createMessageElement('ai', response.response, new Date());
                } else {
                    aiHtml = this.createMessageElement('ai', 'Xin lỗi, có lỗi xảy ra. Vui lòng thử lại.', new Date());
                }
                this.chatContainer.append(aiHtml);
                this.scrollToBottom();
            }, waitTime);
        } catch (error) {
            console.error('Error sending message:', error);
            this.hideTypingIndicator();
            const errorMessage = this.createMessageElement('ai', 'Không thể kết nối với AI. Vui lòng thử lại.', new Date());
            this.chatContainer.append(errorMessage);
        } finally {
            this.setInputState(true);
            this.isSending = false;
            this.scrollToBottom();
            this.chatInput.focus();
        }
    }
    
    async callAPI(action, data) {
        const formData = new FormData();
        formData.append('action', action);
        
        Object.keys(data).forEach(key => {
            formData.append(key, data[key]);
        });
        
        const response = await fetch('<?=base_url('ajaxs/admin/ai-chat.php')?>', {
            method: 'POST',
            body: formData
        });
        
        return await response.json();
    }
    
    showWelcomeMessage() {
        const welcomeMessage = this.createMessageElement('ai', 
            '🤖 Chào bạn! Tôi là AI Assistant. Tôi có thể giúp bạn:\n\n' +
            '📊 Phân tích dữ liệu và thống kê\n' +
            '💰 Theo dõi doanh thu, lợi nhuận\n' +
            '👥 Quản lý khách hàng\n' +
            '🔍 Tìm kiếm và báo cáo\n\n' +
            'Hãy hỏi tôi bất cứ điều gì!', 
            new Date()
        );
        this.chatContainer.append(welcomeMessage);
    }
    
    showLoading() {
        this.loadingIndicator.show();
    }
    
    hideLoading() {
        this.loadingIndicator.hide();
    }
    
    showLoadMoreButton() {
        this.loadMoreBtn.show();
    }
    
    hideLoadMoreButton() {
        this.loadMoreBtn.hide();
    }
    
    showTypingIndicator() {
        console.log('Showing typing indicator...');
        this.typingIndicator.addClass('show');
        this.scrollToBottom();
    }
    
    hideTypingIndicator() {
        console.log('Hiding typing indicator...');
        this.typingIndicator.removeClass('show');
    }
    
    setInputState(enabled) {
        this.chatInput.prop('disabled', !enabled);
        this.sendButton.prop('disabled', !enabled);
        
        if (enabled) {
            this.chatInput.attr('placeholder', 'Nhập tin nhắn...');
        } else {
            this.chatInput.attr('placeholder', 'Đang gửi...');
        }
    }
    
    scrollToBottom() {
        const container = this.chatContainer[0];
        container.scrollTop = container.scrollHeight;
    }
}

// Khởi tạo AI Chat Popup khi DOM loaded
$(document).ready(function() {
    window.aiChatPopup = new AIChatPopup();
});

</script>

<!-- AI Chat Modal -->
<div class="modal fade" id="aiChatModal" tabindex="-1" aria-labelledby="aiChatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="aiChatModalLabel">
                    <i class="fas fa-robot text-dark me-2"></i>
                    AI Chat Assistant
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0">
                <!-- Chat Container -->
                <div id="aiChatContainer" class="ai-chat-container">
                    <!-- Loading Indicator -->
                    <div id="aiChatLoading" class="ai-chat-loading">
                        <div class="text-center py-4">
                            <i class="fas fa-spinner fa-spin text-dark mb-2" style="font-size: 2rem;"></i>
                            <p class="text-muted"><?=__('Đang tải tin nhắn...')?></p>
                        </div>
                    </div>
                    
                    <!-- Load More Button -->
                    <div class="text-center py-2" id="aiChatLoadMoreContainer">
                        <button id="aiChatLoadMoreBtn" class="btn btn-sm btn-outline-dark" style="display: none;">
                            <i class="fas fa-arrow-up"></i> <?=__('Tải thêm tin nhắn')?>
                        </button>
                    </div>
                    
                    <!-- Messages will be loaded here -->
                    
                    <!-- Typing Indicator -->
                    <div id="aiChatTyping" class="ai-chat-typing" style="display: none;">
                        <div class="d-flex justify-content-start mb-3">
                            <div class="d-flex align-items-start" style="max-width: 80%;">
                                <div class="ai-chat-avatar me-2" style="width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 14px; flex-shrink: 0;">
                                    <i class="fas fa-robot"></i>
                                </div>
                                <div class="ai-chat-content">
                                    <div class="ai-chat-bubble ai-chat-bubble-ai">
                                        <div class="typing-dots">
                                            <div class="typing-dot"></div>
                                            <div class="typing-dot"></div>
                                            <div class="typing-dot"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <!-- Chat Input -->
                <div class="ai-chat-input-container w-100">
                    <div class="input-group">
                        <textarea id="aiChatInput" class="form-control ai-chat-input" 
                                placeholder="<?=__('Nhập tin nhắn...')?>" rows="1"></textarea>
                        <button id="aiChatSendBtn" class="btn btn-dark" type="button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



<?php
require_once(__DIR__.'/footer.php');
?>
<script type="text/javascript">
new ClipboardJS(".copy");

function copy() {
    showMessage('<?=__('Đã sao chép vào bộ nhớ tạm');?>', 'success');
}
</script>