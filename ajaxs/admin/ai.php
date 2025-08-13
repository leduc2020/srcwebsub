<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");
require_once(__DIR__.'/../../models/is_admin.php');

if ($CMSNT->site('status_demo') == 1) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Chức năng này không khả dụng trong chế độ demo')
    ]);
    die($data);
}

// Function để loại bỏ markdown code blocks và cấu trúc HTML không cần thiết
function cleanAIContent($content) {
    // Loại bỏ ```html ở đầu
    $content = preg_replace('/^```html\s*/i', '', $content);
    // Loại bỏ ``` ở đầu 
    $content = preg_replace('/^```\s*/i', '', $content);
    // Loại bỏ ``` ở cuối
    $content = preg_replace('/\s*```$/i', '', $content);
    
    // Loại bỏ cấu trúc HTML cơ bản (cho script footer)
    $content = preg_replace('/^\s*<!DOCTYPE[^>]*>/i', '', $content);
    $content = preg_replace('/^\s*<html[^>]*>/i', '', $content);
    $content = preg_replace('/^\s*<head[^>]*>/i', '', $content);
    $content = preg_replace('/^\s*<\/head>/i', '', $content);
    $content = preg_replace('/^\s*<body[^>]*>/i', '', $content);
    $content = preg_replace('/\s*<\/body>\s*$/i', '', $content);
    $content = preg_replace('/\s*<\/html>\s*$/i', '', $content);
    
    // Loại bỏ meta tags không cần thiết
    $content = preg_replace('/<meta[^>]*>/i', '', $content);
    
    // Loại bỏ các tiêu đề HTML thường gặp ở đầu
    $content = preg_replace('/^<h[1-6][^>]*>.*?<\/h[1-6]>\s*/i', '', $content);
    
    // Loại bỏ các thẻ title
    $content = preg_replace('/<title[^>]*>.*?<\/title>/i', '', $content);
    
    // Trim khoảng trắng và xuống dòng thừa
    $content = trim($content);
    $content = preg_replace('/^\s*\n+/', '', $content);
    $content = preg_replace('/\n+\s*$/', '', $content);
    
    return $content;
}

if ($CMSNT->site('status_demo') != 0) {
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('Chức năng này không khả dụng trong chế độ demo')
    ]);
    die($data);
}
if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => 'The Request Not Found'
    ]);
    die($data);   
}

// Xử lý tạo HTML detail cho cấp bậc
if($_POST['action'] == 'generateHTMLdetail') {
    if(!isset($_POST['description']) || empty(trim($_POST['description']))) {
        $data = json_encode([
            'success' => false,
            'message' => __('Vui lòng nhập mô tả về cấp bậc')
        ]);
        die($data);
    }
    
    $description = trim($_POST['description']);
    
    // Tạo prompt cho AI
    $prompt = "Tạo HTML cho ưu đãi: '$description'

Sử dụng template này và tùy chỉnh cho phù hợp:
<div class='benefit-item d-flex align-items-center mb-3 p-2 rounded-3' style='background: rgba(108, 117, 125, 0.08);'> 
  <div class='benefit-icon me-3' style='width: 32px; height: 32px; background: linear-gradient(135deg, #fd6e14, #ff5107); border-radius: 8px; display: flex; align-items: center; justify-content: center;'>
    <i class='ri-checkbox-circle-fill text-white' style='font-size: 14px;'></i>
   </div>
   <div>
     <div class='fw-medium' style='font-size: 13px;'>$description</div>
     <small class='text-muted'>Mô tả chi tiết liên quan</small>
   </div>
</div>

Yêu cầu:
1. Thay đổi màu gradient phù hợp với loại ưu đãi
2. Chọn icon RemixIcon phù hợp (ri-customer-service-line cho hỗ trợ, ri-percent-line cho giảm giá, ri-vip-crown-line cho VIP...)
3. Viết mô tả chi tiết hấp dẫn trong thẻ small
4. Giữ nguyên cấu trúc HTML và các class

CHỈ TRA VỀ HTML THUẦN TÚY, KHÔNG GIẢI THÍCH, KHÔNG SỬ DỤNG MARKDOWN CODE BLOCKS.";

    // Gọi hàm tạo nội dung AI
    $result = generateAIContent($prompt);
    $response = json_decode($result, true);
    
    if($response['success']) {
        // Loại bỏ markdown code blocks
        $content = cleanAIContent($response['description']);
        
        $data = json_encode([
            'success' => true,
            'content' => $content
        ]);
    } else {
        $data = json_encode([
            'success' => false,
            'message' => $response['message']
        ]);
    }
    
    die($data);
}

// Xử lý tạo nội dung thông báo Telegram
if($_POST['action'] == 'generateTelegramNotification') {
    if(!isset($_POST['type']) || empty(trim($_POST['type']))) {
        $data = json_encode([
            'success' => false,
            'message' => __('Vui lòng chọn loại thông báo')
        ]);
        die($data);
    }
    
    $type = trim(check_string($_POST['type']));
    
    // Định nghĩa các loại thông báo và prompt tương ứng
    $notifications = [
        'noti_buy_service_manual' => [
            'title' => 'Thông báo đơn hàng thủ công cho Admin',
            'variables' => '{domain}, {username}, {trans_id}, {service}, {link}, {comment}, {quantity}, {pay}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram chuyên nghiệp cho Admin khi có đơn hàng thủ công mới. Nội dung cần ngắn gọn, rõ ràng và thông tin quan trọng. Sử dụng emoji phù hợp.'
        ],
        'noti_buy_service_api' => [
            'title' => 'Thông báo đơn hàng API cho Admin', 
            'variables' => '{domain}, {username}, {trans_id}, {service}, {supplier}, {link}, {comment}, {quantity}, {pay}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram chuyên nghiệp cho Admin khi có đơn hàng API mới. Nội dung cần ngắn gọn nhưng đầy đủ thông tin về đơn hàng tự động.'
        ],
        'noti_recharge' => [
            'title' => 'Thông báo nạp tiền cho Admin',
            'variables' => '{domain}, {username}, {trans_id}, {method}, {amount}, {price}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho Admin khi có giao dịch nạp tiền mới. Cần thể hiện rõ thông tin về số tiền và phương thức thanh toán.'
        ],
        'noti_action' => [
            'title' => 'Thông báo hành động cho Admin',
            'variables' => '{domain}, {username}, {action}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho Admin về các hành động quan trọng của người dùng trên hệ thống. Nội dung cần rõ ràng về hành động được thực hiện.'
        ],
        'noti_affiliate_withdraw' => [
            'title' => 'Thông báo rút hoa hồng cho Admin',
            'variables' => '{domain}, {username}, {bank}, {account_number}, {account_name}, {amount}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho Admin khi có yêu cầu rút hoa hồng. Cần thể hiện đầy đủ thông tin ngân hàng và số tiền rút.'
        ],
        'telegram_noti_login_user' => [
            'title' => 'Thông báo đăng nhập cho User',
            'variables' => '{domain}, {username}, {ip}, {device}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram thân thiện cho người dùng khi đăng nhập thành công. Nội dung cần tạo cảm giác an toàn và chào đón.'
        ],
        'noti_buy_service_to_user' => [
            'title' => 'Thông báo đơn hàng cho User',
            'variables' => '{domain}, {username}, {trans_id}, {service}, {link}, {comment}, {quantity}, {pay}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram thân thiện cho người dùng khi user mua dịch vụ. Nội dung cần tạo cảm giác an toàn và chuyên nghiệp.'
        ],
        'support_tickets_telegram_message' => [
            'title' => 'Thông báo khi có ticket mới cho Admin',
            'variables' => '{domain}, {username}, {subject}, {content}, {status}, {category}, {ip}, {time}, {device}',
            'prompt' => 'Tạo nội dung thông báo Telegram thân thiện cho Admin khi có ticket mới. Nội dung cần tạo cảm giác phải chuyên nghiệp.'
        ],
        'noti_child_panel_create' => [
            'title' => 'Thông báo tạo Child Panel cho User',
            'variables' => '{domain}, {username}, {api_key}, {email}, {phone}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram thân thiện cho Admin khi có yêu cầu tạo Child Panel trong SMM Panel từ User. Nội dung phải chuyên nghiệp.'
        ],
        'noti_child_panel_renewal' => [
            'title' => 'Thông báo gia hạn Child Panel cho User',
            'variables' => '{domain}, {username}, {email}, {phone}, {renewal_period}, {renewal_cost}, {new_expired_at}, {ip}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho Admin biết khi có yêu cầu gia hạn Child Panel trong SMM Panel từ User. Nội dung phải chuyên nghiệp và cho Admin biết rõ vấn đề gì đang xảy ra.'
        ],
        'noti_user_child_panel_expired' => [
            'title' => 'Thông báo khi Child Panel hết hạn cho User',
            'variables' => '{domain}, {username}, {email_user}, {phone_user}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram thân thiện cho người dùng khi Child Panel của họ đã hết hạn. Nội dung cần tạo cảm giác an toàn và chuyên nghiệp.'
        ],
        'noti_user_admin_reply_ticket' => [
            'title' => 'Thông báo khi Admin reply ticket cho User',
            'variables' => '{username}, {subject}, {message}, {time}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho người dùng khi Admin reply ticket của họ. Tin nhắn message phải giống như Admin đang trả lời họ trên Telegram và phải vào đúng trọng tâm.'
        ],
        'support_tickets_telegram_message_reply' => [
            'title' => 'Thông báo khi User reply ticket cho Admin',
            'variables' => '{username}, {subject}, {message}, {category}, {ip}, {time}, {device}',
            'prompt' => 'Tạo nội dung thông báo Telegram cho Admin khi User reply ticket của họ. Tin nhắn phải vào đúng trọng tâm và message phải giống như User đang trả lời họ trên Telegram.'
        ]
    ];
    
    if(!isset($notifications[$type])) {
        $data = json_encode([
            'success' => false,
            'message' => __('Loại thông báo không hợp lệ')
        ]);
        die($data);
    }
    
    $config = $notifications[$type];
    

    // Tạo prompt cho AI
    $prompt = $config['prompt'] . "

Yêu cầu:
1. Sử dụng các biến có sẵn: " . $config['variables'] . "
2. Nội dung phải bằng tiếng Việt
3. Sử dụng emoji phù hợp để tạo điểm nhấn
4. Định dạng rõ ràng, dễ đọc
5. Độ dài phù hợp cho thông báo Telegram
6. Thể hiện tính chuyên nghiệp của hệ thống
7. Định dạng phải là Markdown phù hợp với quy định của Telegram

CHỈ TRA VỀ NỘI DUNG THÔNG BÁO THUẦN TÚY, KHÔNG GIẢI THÍCH.";

    // Gọi hàm tạo nội dung AI
    $result = generateAIContent($prompt);
    $response = json_decode($result, true);
    
    if($response['success']) {
        // Loại bỏ markdown code blocks
        $content = cleanAIContent($response['description']);
        
        $data = json_encode([
            'success' => true,
            'content' => $content
        ]);
    } else {
        $data = json_encode([
            'success' => false,
            'message' => $response['message']
        ]);
    }
    
    die($data);
}

// Xử lý tạo nội dung thông báo Email
if($_POST['action'] == 'generateEmailNotification') {
    if(!isset($_POST['type']) || empty(trim($_POST['type']))) {
        $data = json_encode([
            'success' => false,
            'message' => __('Vui lòng chọn loại thông báo')
        ]);
        die($data);
    }
    
    $type = trim(check_string($_POST['type']));
    
    // Định nghĩa các loại thông báo email và prompt tương ứng
    $notifications = [
        'email_temp_content_warning_ticket' => [
            'title' => 'Thông báo Admin khi User tạo ticket',
            'variables' => '{domain}, {title}, {username}, {ip}, {device}, {time}, {subject}, {category}, {order_id}, {content}',
            'prompt' => 'Tạo nội dung body email chuyên nghiệp để thông báo cho Admin khi có ticket mới được tạo. KHÔNG BAO GỒM TIÊU ĐỀ, chỉ tạo nội dung email. Email cần thể hiện tính cấp bách và đầy đủ thông tin về ticket.'
        ],
        'email_temp_content_reply_ticket' => [
            'title' => 'Thông báo Admin khi User trả lời ticket',
            'variables' => '{domain}, {title}, {username}, {ip}, {device}, {time}, {subject}, {category}, {order_id}, {content}',
            'prompt' => 'Tạo nội dung body email thông báo cho Admin khi có phản hồi mới từ User trong ticket. KHÔNG BAO GỒM TIÊU ĐỀ, chỉ tạo nội dung email. Email cần ngắn gọn nhưng đầy đủ thông tin cần thiết.'
        ],
        'email_temp_content_warning_login' => [
            'title' => 'Thông báo đăng nhập',
            'variables' => '{domain}, {title}, {username}, {ip}, {device}, {time}',
            'prompt' => 'Tạo nội dung body email thông báo bảo mật khi có đăng nhập mới. KHÔNG BAO GỒM TIÊU ĐỀ, chỉ tạo nội dung email. Email cần tạo cảm giác an toàn và hướng dẫn người dùng kiểm tra tài khoản.'
        ],
        'email_temp_content_otp_mail' => [
            'title' => 'Gửi OTP xác minh đăng nhập',
            'variables' => '{domain}, {title}, {username}, {otp}, {ip}, {device}, {time}',
            'prompt' => 'Tạo nội dung body email gửi mã OTP xác minh đăng nhập. KHÔNG BAO GỒM TIÊU ĐỀ, chỉ tạo nội dung email. Email cần rõ ràng về cách sử dụng OTP và thời gian hiệu lực.'
        ],
        'email_temp_content_forgot_password' => [
            'title' => 'Khôi phục mật khẩu',
            'variables' => '{domain}, {title}, {username}, {link}, {ip}, {device}, {time}',
            'prompt' => 'Tạo nội dung body email khôi phục mật khẩu. KHÔNG BAO GỒM TIÊU ĐỀ, chỉ tạo nội dung email. Email cần hướng dẫn rõ ràng cách thức đặt lại mật khẩu và lưu ý về bảo mật.'
        ]
    ];
    
    if(!isset($notifications[$type])) {
        $data = json_encode([
            'success' => false,
            'message' => __('Loại thông báo không hợp lệ')
        ]);
        die($data);
    }
    
    $config = $notifications[$type];
    
    // Lấy thông tin website
    $site_info = "
    
Thông tin website để tham khảo:
- Hotline: " . $CMSNT->site('hotline') . "
- Email: " . $CMSNT->site('email') . "
- Địa chỉ: " . $CMSNT->site('address') . "
- Fanpage: " . $CMSNT->site('fanpage') . "
    
Có thể sử dụng các thông tin này khi phù hợp.";

    // Tạo prompt cho AI
    $prompt = $config['prompt'] . $site_info . "

Yêu cầu:
1. Sử dụng các biến có sẵn: " . $config['variables'] . "
2. Nội dung phải bằng tiếng Việt
3. Định dạng HTML đẹp mắt và thêm CSS theo phong cách Material Design, chuyên nghiệp
4. Sử dụng cấu trúc email phù hợp với từng loại thông báo
5. Tone văn phong phù hợp: trang trọng cho thông báo bảo mật, thân thiện cho OTP
6. Bao gồm lời chào và lời kết thích hợp
7. TUYỆT ĐỐI KHÔNG BAO GỒM TIÊU ĐỀ HAY HEADING, CHỈ TẠO NỘI DUNG BODY

CHỈ TRA VỀ NỘI DUNG EMAIL THUẦN TÚY, KHÔNG TIÊU ĐỀ, KHÔNG GIẢI THÍCH, KHÔNG SỬ DỤNG MARKDOWN CODE BLOCKS.";

    // Gọi hàm tạo nội dung AI
    $result = generateAIContent($prompt);
    $response = json_decode($result, true);
    
    if($response['success']) {
        // Loại bỏ markdown code blocks
        $content = cleanAIContent($response['description']);
        
        $data = json_encode([
            'success' => true,
            'content' => $content
        ]);
    } else {
        $data = json_encode([
            'success' => false,
            'message' => $response['message']
        ]);
    }
    
    die($data);
}

// Xử lý tạo nội dung cho System Pages
if($_POST['action'] == 'generateSystemPageContent') {
    if(!isset($_POST['type']) || empty(trim($_POST['type']))) {
        $data = json_encode([
            'success' => false,
            'message' => __('Vui lòng chọn loại trang')
        ]);
        die($data);
    }
    
    $type = trim(check_string($_POST['type']));
    
    // Định nghĩa các loại trang hệ thống và prompt tương ứng
    $pages = [
        'page_contact' => [
            'title' => 'Nội dung trang liên hệ',
            'prompt' => 'Tạo nội dung trang liên hệ chuyên nghiệp cho website dịch vụ SMM Panel. Nội dung cần bao gồm thông tin liên hệ, phương thức hỗ trợ khách hàng, giờ làm việc và lời nhắn thân thiện. Sử dụng HTML để định dạng đẹp mắt.'
        ],
        'page_policy' => [
            'title' => 'Nội dung trang chính sách',
            'prompt' => 'Tạo nội dung trang chính sách dịch vụ cho website SMM Panel. Bao gồm các điều khoản sử dụng, quyền và nghĩa vụ của khách hàng, chính sách hoàn tiền, bảo mật thông tin. Nội dung phải rõ ràng, dễ hiểu và tuân thủ pháp luật.'
        ],
        'page_privacy' => [
            'title' => 'Nội dung trang quyền riêng tư',
            'prompt' => 'Tạo nội dung trang chính sách quyền riêng tư cho website SMM Panel. Giải thích cách thu thập, sử dụng và bảo vệ thông tin cá nhân của khách hàng. Bao gồm cookie policy, chia sẻ thông tin với bên thứ ba và quyền của người dùng.'
        ],
        'page_faq' => [
            'title' => 'Nội dung trang FAQ',
            'prompt' => 'Tạo nội dung trang câu hỏi thường gặp (FAQ) cho website SMM Panel. Bao gồm các câu hỏi về cách sử dụng dịch vụ, thanh toán, thời gian xử lý đơn hàng, chính sách hỗ trợ và những vấn đề khách hàng quan tâm nhất.'
        ]
    ];
    
    if(!isset($pages[$type])) {
        $data = json_encode([
            'success' => false,
            'message' => __('Loại trang không hợp lệ')
        ]);
        die($data);
    }
    
    $config = $pages[$type];
    
    // Lấy thông tin website
    $site_info = "
    
Thông tin website cần sử dụng:
- Hotline: " . $CMSNT->site('hotline') . "
- Email: " . $CMSNT->site('email') . "
- Địa chỉ: " . $CMSNT->site('address') . "
- Fanpage: " . $CMSNT->site('fanpage') . "
    
Hãy sử dụng các thông tin này trong nội dung khi cần thiết.";

    // Tạo prompt cho AI
    $prompt = $config['prompt'] . $site_info . "

Yêu cầu:
1. Nội dung phải bằng tiếng Việt
2. Sử dụng HTML để định dạng đẹp mắt với các thẻ như <h2>, <h3>, <p>, <ul>, <li>
3. Nội dung chuyên nghiệp, phù hợp với dịch vụ SMM Panel
4. Cấu trúc rõ ràng, dễ đọc và hiểu
5. Độ dài phù hợp (khoảng 300-500 từ)
6. Tone văn phong thân thiện nhưng chuyên nghiệp
7. KHÔNG BAO GỒM TIÊU ĐỀ CHÍNH, chỉ tạo nội dung body

CHỈ TRA VỀ NỘI DUNG TRANG THUẦN TÚY, KHÔNG TIÊU ĐỀ, KHÔNG GIẢI THÍCH, KHÔNG SỬ DỤNG MARKDOWN CODE BLOCKS.";

    // Gọi hàm tạo nội dung AI
    $result = generateAIContent($prompt);
    $response = json_decode($result, true);
    
    if($response['success']) {
        // Loại bỏ markdown code blocks
        $content = cleanAIContent($response['description']);
        
        $data = json_encode([
            'success' => true,
            'content' => $content
        ]);
    } else {
        $data = json_encode([
            'success' => false,
            'message' => $response['message']
        ]);
    }
    
    die($data);
}

// Xử lý tạo Script/HTML Footer
if($_POST['action'] == 'generateFooterScript') {
    if(!isset($_POST['description']) || empty(trim($_POST['description']))) {
        $data = json_encode([
            'success' => false,
            'message' => __('Vui lòng nhập mô tả về script cần tạo')
        ]);
        die($data);
    }
    
    $description = trim($_POST['description']);
    
    // Lấy thông tin website
    $site_info = "
    
Thông tin website để tham khảo:
- Hotline: " . $CMSNT->site('hotline') . "
- Email: " . $CMSNT->site('email') . "
- Địa chỉ: " . $CMSNT->site('address') . "
- Fanpage: " . $CMSNT->site('fanpage') . "
    
Có thể sử dụng các thông tin này khi phù hợp.";

    // Tạo prompt cho AI
    $prompt = "Tạo script/HTML cho yêu cầu: '$description'" . $site_info . "

Yêu cầu:
1. CHỈ TẠO SCRIPT/HTML FRAGMENT, KHÔNG TẠO CẢ TRANG WEB (không có <!DOCTYPE>, <html>, <head>, <body>)
2. Nếu cần CSS, đặt trong thẻ <style> 
3. Nếu cần JavaScript, đặt trong thẻ <script>
4. Code phải tối ưu và không ảnh hưởng đến hiệu suất website
5. Sử dụng jQuery nếu cần (đã có sẵn trên website)
6. Code phải responsive và tương thích đa trình duyệt
7. Bao gồm comment giải thích ngắn gọn
8. Nếu là hiệu ứng, tạo đơn giản và đẹp mắt
9. Code phải an toàn, không chứa mã độc

VÍ DỤ ĐỊNH DẠNG ĐÚNG:
<style>
/* CSS cho hiệu ứng */
</style>

<script>
// JavaScript cho hiệu ứng
</script>

HOẶC CHỈ:
<script>
// JavaScript đơn giản
</script>

Lưu ý: Đây là code sẽ được chèn vào footer trang web, chỉ tạo phần script cần thiết.

CHỈ TRA VỀ SCRIPT/HTML FRAGMENT THUẦN TÚY, KHÔNG GIẢI THÍCH THÊM, KHÔNG SỬ DỤNG MARKDOWN CODE BLOCKS.";

    // Gọi hàm tạo nội dung AI
    $result = generateAIContent($prompt);
    $response = json_decode($result, true);
    
    if($response['success']) {
        // Loại bỏ markdown code blocks
        $content = cleanAIContent($response['description']);
        
        $data = json_encode([
            'success' => true,
            'content' => $content
        ]);
    } else {
        $data = json_encode([
            'success' => false,
            'message' => $response['message']
        ]);
    }
    
    die($data);
} 