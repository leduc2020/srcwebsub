<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

require_once(__DIR__.'/../../models/is_user.php');



if($CMSNT->site('support_tickets_status') == 0){
    redirect(base_url());
}


// Lấy ID ticket từ URL
$ticket_id = intval(check_string($_GET['id']));

if (!$ticket_id) {
    redirect(base_url('client/support-tickets'));
}

// Kiểm tra ticket có thuộc về user hiện tại không
$ticket = $CMSNT->get_row("SELECT * FROM `support_tickets` WHERE `id` = '$ticket_id' AND `user_id` = '".$getUser['id']."'");

if (!$ticket) {
    redirect(base_url('client/support-tickets'));
}

$body = [
    'title' => __('Chi tiết Ticket').' #'.$ticket_id.' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];

$body['header'] = '
<style>
.waiting-response-card {
    position: relative;
}

.pulse-animation {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 100px;
    height: 100px;
    border: 2px solid rgba(13, 110, 253, 0.3);
    border-radius: 50%;
    animation: pulse 2s infinite;
}

.pulse-animation::before {
    content: "";
    position: absolute;
    top: -8px;
    left: -8px;
    right: -8px;
    bottom: -8px;
    border: 2px solid rgba(13, 110, 253, 0.2);
    border-radius: 50%;
    animation: pulse 2s infinite 0.5s;
}

@keyframes pulse {
    0% {
        transform: translate(-50%, -50%) scale(0.8);
        opacity: 1;
    }
    100% {
        transform: translate(-50%, -50%) scale(1.4);
        opacity: 0;
    }
}

.waiting-response-card .alert {
    animation: fadeInUp 0.6s ease-out 0.5s both;
}

/* Transition cho visual feedback */
#replyMessage {
    transition: background-color 0.3s ease;
}

/* Visual feedback khi Ctrl+Enter */
#replyMessage.bg-success-subtle {
    background-color: rgba(25, 135, 84, 0.1) !important;
    border-color: #28a745 !important;
}

/* Animation cho tin nhắn mới */
.new-message {
    animation: fadeInUp 0.5s ease-out;
}

/* Styling cho kbd elements */
kbd {
    font-size: 0.75rem;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
    border: 1px solid rgba(255,255,255,0.3);
    font-weight: 600;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
';

$body['footer'] = '

';

require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');

// Lấy danh sách tin nhắn
$messages = $CMSNT->get_list("SELECT sm.*, u.username FROM `support_messages` sm LEFT JOIN `users` u ON (sm.sender_id = u.id AND sm.sender_type = 'user') WHERE sm.ticket_id = '$ticket_id' ORDER BY sm.created_at ASC");

// Debug - hiển thị thông tin ticket và messages
// echo "Ticket content: " . $ticket['content'] . "<br>";
// echo "Messages count: " . count($messages) . "<br>";
// var_dump($messages);

// Config trạng thái
$status_config = [
    'open' => ['name' => __('Đang mở'), 'class' => 'status-open'],
    'pending' => ['name' => __('Chờ xử lý'), 'class' => 'status-pending'],
    'answered' => ['name' => __('Đã trả lời'), 'class' => 'status-answered'],
    'closed' => ['name' => __('Đã đóng'), 'class' => 'status-closed']
];

?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Chi tiết Ticket');?> #<?=$ticket['id'];?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?=base_url('client/profile');?>"><?=__('Tài khoản');?></a></li>
                                <li class="breadcrumb-item"><a href="<?=base_url('client/support-tickets');?>"><?=__('Yêu cầu Hỗ trợ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Chi tiết Ticket');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->

            <!-- Thông tin ticket -->
            <div class="row">
                <!-- Cột trái: Cuộc hội thoại -->
                <div class="col-lg-8">
                    <!-- Chat container -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-primary border-0 py-3">
                            <div class="d-flex align-items-center justify-content-between">
                                <h6 class="card-title mb-0 text-white fw-semibold">
                                    <i class="ri-chat-3-line me-2 fs-16"></i><?=__('Cuộc hội thoại');?>
                                </h6>
                                <a href="<?=base_url('client/support-tickets');?>" class="btn btn-light btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i><?=__('Quay lại');?>
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="chat-conversation p-3" data-simplebar style="max-height: 500px;">
                                <ul class="list-unstyled chat-conversation-list">
                                    <!-- Tin nhắn đầu tiên từ user -->
                                    <?php if(!empty($ticket['content'])): ?>
                                    <li class="chat-list right">
                                        <div class="conversation-list">
                                            <div class="d-flex">
                                                <div class="flex-grow-1 me-3">
                                                    <div class="d-flex align-items-center gap-2 mb-1 justify-content-end">
                                                        <small class="text-muted me-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=timeAgo(strtotime($ticket['created_at']));?>"><?=date('H:i d/m/Y', strtotime($ticket['created_at']));?></small>
                                                        <span class="badge bg-info-subtle text-info fs-10"><?=__('Nội dung ban đầu');?></span>
                                                        <h6 class="mb-0 text-primary"><?=$getUser['username'];?></h6>
                                                    </div>
                                                    <div class="p-3 bg-primary-subtle rounded-3 text-end">
                                                        <p class="mb-0"><?=nl2br(htmlspecialchars($ticket['content']));?></p>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <img src="<?=getGravatarUrl($getUser['email']);?>" alt="<?=$getUser['username'];?>" class="avatar-title rounded-circle no-pointer-events">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>

                                    <?php if (!empty($messages)): ?>
                                    <?php foreach($messages as $msg): ?>
                                    <?php if($msg['sender_type'] == 'user'): ?>
                                    <!-- Tin nhắn từ user -->
                                    <li class="chat-list right" data-message-id="<?=$msg['id'];?>">
                                        <div class="conversation-list">
                                            <div class="d-flex">
                                                <div class="flex-grow-1 me-3">
                                                    <div class="d-flex align-items-center gap-2 mb-1 justify-content-end">
                                                        <small class="text-muted me-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=timeAgo(strtotime($msg['created_at']));?>"><?=date('H:i d/m/Y', strtotime($msg['created_at']));?></small>
                                                        <h6 class="mb-0 text-primary"><?=$msg['username'] ?: __('User');?></h6>
                                                    </div>
                                                    <div class="p-3 bg-primary-subtle rounded-3 text-end">
                                                        <p class="mb-0"><?=nl2br(htmlspecialchars($msg['message']));?></p>
                                                    </div>
                                                </div>
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <img src="<?=getGravatarUrl($getUser['email']);?>" alt="<?=$msg['username'] ?: 'User';?>" class="avatar-title rounded-circle no-pointer-events">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php else: ?>
                                    <!-- Tin nhắn từ admin -->
                                    <li class="chat-list left" data-message-id="<?=$msg['id'];?>">
                                        <div class="conversation-list">
                                            <div class="d-flex">
                                                <div class="flex-shrink-0">
                                                    <div class="avatar-xs">
                                                        <span class="avatar-title rounded-circle bg-success-subtle text-success">
                                                            <i class="ri-admin-line"></i>
                                                        </span>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <div class="d-flex align-items-center gap-2 mb-1">
                                                        <h6 class="mb-0 text-success"><?=__('Admin Support');?></h6>
                                                        <small class="text-muted ms-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=timeAgo(strtotime($msg['created_at']));?>" ><?=date('H:i d/m/Y', strtotime($msg['created_at']));?></small>
                                                    </div>
                                                    <div class="p-3 bg-success-subtle rounded-3">
                                                        <p class="mb-0"><?=nl2br(htmlspecialchars($msg['message']));?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                    <?php endforeach; ?>
                                    <?php endif; ?>

                                    <?php if (empty($messages) && empty($ticket['content'])): ?>
                                    <li class="text-center py-4">
                                        <div class="avatar-lg mx-auto mb-3">
                                            <div class="avatar-title bg-light rounded-circle">
                                                <i class="ri-message-line text-muted" style="font-size: 24px;"></i>
                                            </div>
                                        </div>
                                        <p class="text-muted"><?=__('Chưa có tin nhắn nào...');?></p>
                                    </li>
                                    <?php elseif (empty($messages) && !empty($ticket['content'])): ?>
                                    <li class="text-center py-5">
                                        <div class="waiting-response-card">
                                            <div class="position-relative mb-4">
                                                <div class="avatar-xl mx-auto">
                                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle">
                                                        <img src="<?=base_url('assets/img/icon-support.svg');?>" alt="<?=__('Đang chờ phản hồi');?>" class="img-fluid no-pointer-events">
                                                    </div>
                                                </div>
                                                <div class="pulse-animation"></div>
                                            </div>
                                            
                                            <h5 class="mb-2 fw-semibold"><?=__('Đang chờ phản hồi');?></h5>
                                            <p class="text-muted mb-3 fs-14"><?=__('Đội ngũ hỗ trợ sẽ phản hồi trong thời gian sớm nhất');?></p>
                                            
                                            <div class="d-flex justify-content-center align-items-center gap-3 mb-3">
                                                <div class="d-flex align-items-center">
                                                    <i class="ri-time-line text-warning me-2"></i>
                                                    <small class="text-muted"><?=__('Thời gian phản hồi: 2-24 giờ');?></small>
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-info border-0 bg-info-subtle mx-auto" style="max-width: 300px;">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-lightbulb-line text-info me-2 mt-1"></i>
                                                    <div>
                                                        <h6 class="alert-heading mb-1 fs-13 text-info"><?=__('Mẹo hữu ích');?></h6>
                                                        <p class="mb-0 fs-12 text-muted"><?=__('Để được hỗ trợ nhanh hơn, hãy cung cấp thông tin chi tiết về vấn đề');?></p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>

                        <!-- Form trả lời -->
                        <div class="card-footer border-top-dashed">
                            <form class="reply-ticket-form">
                                <input type="hidden" name="ticket_id" value="<?=$ticket['id'];?>">
                                <div class="row g-2">
                                    <div class="col">
                                        <div class="position-relative">
                                            <textarea id="replyMessage" name="message" class="form-control border-0 bg-light resize-none" 
                                                rows="1" placeholder="<?=__('Nhập tin nhắn của bạn...');?>" required></textarea>
                                        </div>
                                    </div>
                                    <div class="col-auto align-self-end">
                                        <button type="submit" class="btn btn-dark">
                                            <i class="ri-send-plane-line"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="ri-information-line me-1"></i><?=__('Phím tắt gửi nhanh');?>:
                                        <span class="ms-2 text-success">
                                            <i class="ri-keyboard-line me-1"></i>
                                            <kbd class="bg-success text-white">Ctrl</kbd> + <kbd class="bg-success text-white">Enter</kbd>
                                            <span class="text-muted mx-1">hoặc</span>
                                            <kbd class="bg-success text-white">⌘</kbd> + <kbd class="bg-success text-white">Enter</kbd>
                                        </span>
                                    </small>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Cột phải: Thông tin ticket -->
                <div class="col-lg-4">
                    <!-- Thông tin ticket -->
                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-warning-subtle border-0 py-3">
                            <h6 class="card-title mb-0 text-warning-emphasis fw-semibold">
                                <i class="ri-information-line me-2 fs-16"></i><?=__('Thông tin ticket');?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- ID & Status -->
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div>
                                    <span class="badge bg-dark fs-12">#<?=$ticket['id'];?></span>
                                </div>
                                <div>
                                    <?=display_status_support_tickets($ticket['status']);?>
                                </div>
                            </div>

                            <!-- Tiêu đề -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><?=__('Tiêu đề');?></h6>
                                <h5 class="mb-0"><?=$ticket['subject'];?></h5>
                            </div>

                            <!-- Danh mục -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><?=__('Danh mục');?></h6>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="ri-tag-line me-1"></i><?=$config_category_support_tickets[$ticket['category']] ?? $ticket['category'];?>
                                </span>
                            </div>

                            <!-- Đơn hàng -->
                            <?php if ($ticket['order_id']): ?>
                            <?php $order_info = $CMSNT->get_row("SELECT * FROM `orders` WHERE `id` = '".$ticket['order_id']."' AND `user_id` = '".$getUser['id']."'"); ?>
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><?=__('Đơn hàng liên quan');?></h6>
                                <span class="badge bg-success-subtle text-success">
                                    <i class="ri-shopping-cart-line me-1"></i>#<?=$order_info['trans_id'];?>
                                </span>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="ri-shopping-cart-line me-1"></i><?=$order_info['service_name'];?>
                                </span>
                                <span class="badge bg-secondary-subtle text-secondary">
                                    <?=__('Số lượng:');?> <?=$order_info['quantity'];?>
                                </span>
                            </div>
                            <?php endif; ?>

                            <!-- Thời gian -->
                            <div class="mb-3">
                                <h6 class="text-muted mb-1"><?=__('Ngày tạo');?></h6>
                                <p class="mb-0">
                                    <i class="ri-calendar-line text-muted me-1"></i>
                                    <?=date('d/m/Y H:i', strtotime($ticket['created_at']));?>
                                </p>
                            </div>

                            <?php if ($ticket['updated_at'] != $ticket['created_at']): ?>
                            <div class="mb-0">
                                <h6 class="text-muted mb-1"><?=__('Cập nhật cuối');?></h6>
                                <p class="mb-0">
                                    <i class="ri-time-line text-muted me-1"></i>
                                    <?=date('d/m/Y H:i', strtotime($ticket['updated_at']));?>
                                    <small class="text-muted ms-2">(<?=timeAgo(strtotime($ticket['updated_at']));?>)</small>
                                </p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Thông tin tài khoản -->
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-success-subtle border-0 py-3">
                            <h6 class="card-title mb-0 text-success-emphasis fw-semibold">
                                <i class="ri-user-line me-2 fs-16"></i><?=__('Tài khoản của tôi');?>
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- User info -->
                            <div class="d-flex align-items-center mb-3">
                                <div class="flex-shrink-0">
                                    <div class="avatar-sm">
                                        <img src="<?=getGravatarUrl($getUser['email']);?>" alt="<?=$getUser['username'];?>" class="avatar-title rounded-circle no-pointer-events">
                                    </div>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1"><?=$getUser['username'];?></h6>
                                    <small class="text-muted"><?=$getUser['email'];?></small>
                                </div>
                            </div>
                            
                            <!-- Stats -->
                            <div class="row g-2 mb-3">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-primary-subtle rounded">
                                        <div class="fs-14 fw-semibold"><?=format_cash($getUser['money']);?></div>
                                        <small class="text-muted"><?=__('Số dư');?></small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-success-subtle rounded">
                                        <div class="fs-14 fw-semibold text-success"><?=$CMSNT->get_row("SELECT COUNT(id) FROM support_tickets WHERE user_id = '".$getUser['id']."'")['COUNT(id)'];?></div>
                                        <small class="text-muted"><?=__('Tickets');?></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="d-grid">
                                <a href="<?=base_url('client/support-tickets');?>" class="btn btn-outline-secondary btn-sm">
                                    <i class="ri-arrow-left-line me-1"></i><?=__('Quay lại danh sách');?>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
</div>

<?php
require_once(__DIR__.'/footer.php');
?> 



<!-- Âm thanh thông báo -->
<audio id="facebook-message-sound" class="d-none">
    <source src="<?=base_url('assets/audio/facebook-messenger.mp3');?>" type="audio/mpeg">
</audio>
<script>
var audioEnabled = false;

// Hàm phát âm thanh khi có tin nhắn
function playFacebookMessageSound() {
    try {
        var sound = document.getElementById('facebook-message-sound');
        if (sound && audioEnabled) {
            // Đặt lại âm thanh về đầu để đảm bảo phát được
            sound.currentTime = 0;
            sound.volume = 0.5; // Giảm volume xuống 50%
   
            // Phát âm thanh
            var playPromise = sound.play();
            
            // Xử lý lỗi có thể xảy ra khi phát âm thanh
            if (playPromise !== undefined) {
                playPromise.then(function() {
                }).catch(function(error) {
                    console.log('Không thể phát âm thanh:', error.message);
                    // Fallback: Hiện browser notification nếu không phát được âm thanh
                    showBrowserNotification();
                });
            }
        } else if (!audioEnabled) {
            console.log('Âm thanh chưa được kích hoạt. Hãy click vào trang để kích hoạt âm thanh thông báo.');
            // Fallback: Hiện browser notification
            showBrowserNotification();
        }
    } catch (e) {
        console.error('Lỗi khi phát âm thanh thông báo:', e);
        // Fallback: Hiện browser notification
        showBrowserNotification();
    }
}

// Hàm hiển thị browser notification như fallback
function showBrowserNotification() {
    // Kiểm tra browser có hỗ trợ notifications không
    if (!("Notification" in window)) {
        console.log("Browser không hỗ trợ notifications");
        return;
    }

    // Nếu đã được cấp quyền
    if (Notification.permission === "granted") {
        createNotification();
    }
    // Nếu chưa từ chối quyền, thì xin quyền
    else if (Notification.permission !== "denied") {
        Notification.requestPermission().then(function (permission) {
            if (permission === "granted") {
                createNotification();
            }
        });
    }
}

// Tạo notification
function createNotification() {
    var notification = new Notification("<?=__('Tin nhắn mới từ Admin Support');?>", {
        body: "<?=__('Bạn có tin nhắn mới trong ticket hỗ trợ');?>",
        icon: "<?=base_url($CMSNT->site('logo_light'));?>",
        tag: "admin-reply",
        requireInteraction: false
    });

    // Auto close sau 5 giây
    setTimeout(function() {
        notification.close();
    }, 5000);

    // Click notification để focus vào tab
    notification.onclick = function() {
        window.focus();
        notification.close();
    };
}

// Kích hoạt âm thanh khi user tương tác lần đầu
function enableAudio() {
    if (!audioEnabled) {
        audioEnabled = true;
        console.log('✅ Âm thanh thông báo đã được kích hoạt!');
    }
}

// Lắng nghe user interaction để kích hoạt audio (chỉ chạy 1 lần)
document.addEventListener('click', enableAudio, { once: true });
document.addEventListener('touchstart', enableAudio, { once: true });
document.addEventListener('keydown', enableAudio, { once: true });
</script>


<script>
$(document).ready(function() {
    // Phát hiện hệ điều hành và cập nhật hiển thị phím tắt
    var isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
    
    // Tự động cuộn đến tin nhắn mới nhất
    function scrollToBottom() {
        // Tìm tin nhắn cuối cùng
        var lastMessage = $(".chat-conversation-list li:last-child");
        if (lastMessage.length) {
            // Scroll đến tin nhắn cuối cùng
            lastMessage[0].scrollIntoView({ 
                behavior: 'smooth', 
                block: 'end' 
            });
        }
        
        // Backup: scroll container nếu có
        var chatContainer = $(".chat-conversation");
        if (chatContainer.length) {
            setTimeout(function() {
                chatContainer.scrollTop(chatContainer[0].scrollHeight);
            }, 100);
        }
    }
    
    // Cuộn đến cuối khi trang load
    setTimeout(function() {
        scrollToBottom();
    }, 100);
    
    // Backup scroll khi window load
    $(window).on('load', function() {
        setTimeout(scrollToBottom, 500);
    });
    
    // Auto load tin nhắn mới
    var lastMessageId = 0;
    var isLoadingMessages = false;
    var justSentMessage = false; // Flag để tránh load ngay sau khi gửi
    
    // Lấy ID tin nhắn cuối cùng hiện tại (bỏ qua tin nhắn tạm thời)
    function getLastMessageId() {
        var messages = $(".chat-conversation-list li[data-message-id]");
        var realLastId = 0;
        
        messages.each(function() {
            var msgId = $(this).attr('data-message-id');
            // Bỏ qua tin nhắn có ID tạm thời (bắt đầu với "new-")
            if (msgId && !msgId.startsWith('new-')) {
                var numId = parseInt(msgId);
                if (!isNaN(numId) && numId > realLastId) {
                    realLastId = numId;
                }
            }
        });
        
        return realLastId;
    }
    
    // Load tin nhắn mới
    function loadNewMessages() {
        // Skip nếu vừa gửi tin nhắn hoặc đang loading
        if (isLoadingMessages || justSentMessage) {
            return;
        }
        
        isLoadingMessages = true;
        var currentLastId = getLastMessageId();
        
        $.ajax({
            url: "<?=base_url('ajaxs/client/ticket.php');?>",
            type: "POST",
            data: {
                action: 'getNewMessages',
                ticket_id: "<?=$ticket['id'];?>",
                last_message_id: currentLastId,
                token: "<?=$getUser['token'];?>"
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success" && response.messages && response.messages.length > 0) {
                    var chatList = $(".chat-conversation-list");
                    var hasNewAdminMessages = false;
                    
                    $.each(response.messages, function(index, msg) {
                        // Chỉ xử lý tin nhắn từ ADMIN
                        if (msg.sender_type !== 'admin') {
                            return; // Skip tất cả tin nhắn user
                        }
                        
                        // Kiểm tra duplicate
                        if ($(`li[data-message-id="${msg.id}"]`).length > 0) {
                            return; // Skip nếu đã có
                        }
                        
                        // Phát âm thanh thông báo
                        playFacebookMessageSound();
                        
                        // Tạo HTML cho tin nhắn admin
                        var messageHtml = `
                        <li class="chat-list left new-message" data-message-id="${msg.id}">
                            <div class="conversation-list">
                                <div class="d-flex">
                                    <div class="flex-shrink-0">
                                        <div class="avatar-xs">
                                            <span class="avatar-title rounded-circle bg-success-subtle text-success">
                                                <i class="ri-admin-line"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="mb-0 text-success"><?=__('Admin Support');?></h6>
                                            <small class="text-muted ms-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="${msg.time_ago}">${msg.formatted_time}</small>
                                        </div>
                                        <div class="p-3 bg-success-subtle rounded-3">
                                            <p class="mb-0">${msg.message}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>`;
                        
                        chatList.append(messageHtml);
                        hasNewAdminMessages = true;
                    });
                    
                    // Chỉ scroll nếu có tin nhắn admin mới
                    if (hasNewAdminMessages) {
                        setTimeout(scrollToBottom, 200);
                        $('[data-bs-toggle="tooltip"]').tooltip();
                    }
                }
            },
            error: function() {
                // Silent fail
            },
            complete: function() {
                isLoadingMessages = false;
            }
        });
    }
    
    // Polling tin nhắn mới mỗi 5 giây (tăng lên để giảm tần suất)
    setInterval(loadNewMessages, 5000);
    
    // Load ngay lần đầu sau 3 giây
    setTimeout(loadNewMessages, 3000);
    
    // Xử lý trả lời ticket
    $(".reply-ticket-form").on("submit", function(e) {
        e.preventDefault();
        
        var form = $(this);
        var message = $("#replyMessage").val().trim();
        var ticket_id = $("input[name='ticket_id']").val();
        var submitBtn = form.find("button[type=submit]");
        
        // Validate
        if (!message) {
            Swal.fire({
                icon: "warning",
                title: "<?=__('Cảnh báo!');?>",
                text: "<?=__('Vui lòng nhập tin nhắn');?>"
            });
            return;
        }
        
        // Set flag để tránh auto-load trong 10 giây
        justSentMessage = true;
        setTimeout(function() {
            justSentMessage = false;
        }, 10000);
        
        // Disable button và hiện loading
        submitBtn.prop("disabled", true).html('<i class="me-1 spinner-border spinner-border-sm"></i>Đang gửi...');
        
        $.ajax({
            url: "<?=base_url('ajaxs/client/ticket.php');?>",
            type: "POST",
            data: {
                action: 'replyTicket',
                token: '<?=$getUser['token'];?>',
                ticket_id: ticket_id,
                message: message
            },
            dataType: "json",
            success: function(response) {
                if (response.status == "success") {
                    // Thêm tin nhắn mới vào chat ngay lập tức
                    var chatList = $(".chat-conversation-list");
                    var currentTime = new Date();
                    var timeString = currentTime.toLocaleTimeString('vi-VN', {
                        hour: '2-digit', 
                        minute: '2-digit'
                    }) + ' ' + currentTime.toLocaleDateString('vi-VN');
                    
                    // Sử dụng ID unique để tránh conflict
                    var uniqueId = `user-${Date.now()}-${Math.random().toString(36).substr(2, 9)}`;
                    
                    var newMessageHtml = `
                    <li class="chat-list right new-message" data-message-id="${uniqueId}">
                        <div class="conversation-list">
                            <div class="d-flex">
                                <div class="flex-grow-1 me-3">
                                    <div class="d-flex align-items-center gap-2 mb-1 justify-content-end">
                                        <small class="text-muted me-auto" data-bs-toggle="tooltip" data-bs-placement="top" title="Vừa gửi">${timeString}</small>
                                        <h6 class="mb-0 text-primary"><?=$getUser['username'];?></h6>
                                    </div>
                                    <div class="p-3 bg-primary-subtle rounded-3 text-end">
                                        <p class="mb-0">${message.replace(/\n/g, '<br>')}</p>
                                    </div>
                                </div>
                                <div class="flex-shrink-0">
                                    <div class="avatar-xs">
                                        <img src="<?=getGravatarUrl($getUser['email']);?>" alt="<?=$getUser['username'];?>" class="avatar-title rounded-circle no-pointer-events">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>`;
                    
                    // Thêm tin nhắn vào cuối danh sách
                    chatList.append(newMessageHtml);
                    
                    // Clear form
                    $("#replyMessage").val("");
                    
                    // Auto scroll đến tin nhắn mới
                    setTimeout(scrollToBottom, 100);
                    
                    // Initialize tooltips cho tin nhắn mới
                    $('[data-bs-toggle="tooltip"]').tooltip();
                    
                } else {
                    showMessage(response.msg, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "<?=__('Lỗi kết nối');?>!",
                    text: "<?=__('Không thể kết nối đến server');?>"
                });
            },
            complete: function() {
                // Reset button
                submitBtn.prop("disabled", false).html('<i class="ri-send-plane-line"></i>');
                // Reset flag sau khi hoàn thành
                setTimeout(function() {
                    justSentMessage = false;
                }, 3000);
            }
        });
    });
    
    // Shortcut Ctrl+Enter (Windows/Linux) hoặc Cmd+Enter (macOS) để gửi
    $("#replyMessage").on("keydown", function(e) {
        // Kiểm tra cả Ctrl (Windows/Linux) và Cmd (macOS)
        var isShortcut = (e.ctrlKey || e.metaKey) && e.key === 'Enter';
        
        if (isShortcut) {
            e.preventDefault();
            e.stopPropagation();
            
            // Kiểm tra nếu đang gửi tin nhắn
            if ($(this).closest('form').find('button[type=submit]').prop('disabled')) {
                return;
            }
            
            // Visual feedback
            $(this).addClass('bg-success-subtle');
            setTimeout(() => {
                $(this).removeClass('bg-success-subtle');
            }, 300);
            
            $(".reply-ticket-form").submit();
        }
    });
    
    // Focus event để hiển thị hint
    $("#replyMessage").on("focus", function() {
        if (!$(this).hasClass('focused-once')) {
            $(this).addClass('focused-once');
            
            // Phát hiện hệ điều hành để hiển thị phím tắt phù hợp
            var shortcutText = isMac ? 'Cmd+Enter' : 'Ctrl+Enter';
            
            // Hiển thị tooltip hint lần đầu focus
            setTimeout(() => {
                if ($(this).is(':focus')) {
                    var tooltip = $('<div class="custom-tooltip position-absolute bg-dark text-white px-2 py-1 rounded small" style="bottom: calc(100% + 8px); left: 50%; transform: translateX(-50%); z-index: 1000; opacity: 0;">💡 Mẹo: ' + shortcutText + ' để gửi nhanh</div>');
                    $(this).parent().css('position', 'relative').append(tooltip);
                    
                    tooltip.animate({opacity: 1}, 300);
                    
                    setTimeout(() => {
                        tooltip.animate({opacity: 0}, 300, function() {
                            $(this).remove();
                        });
                    }, 3000);
                }
            }, 800);
        }
    });
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
});
</script>