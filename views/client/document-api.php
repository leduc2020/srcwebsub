<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Tài liệu API').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '

';
$body['footer'] = '

';

if($CMSNT->site('isLoginRequiredToViewProduct') == 1) {
    require_once(__DIR__ . '/../../models/is_user.php');
}else{
    if (isSecureCookie('user_login') == true) {
        require_once(__DIR__ . '/../../models/is_user.php');
    }
}

require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
?>

<style>
.api-endpoint-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-radius: 12px;
    overflow: hidden;
}

.api-info-grid {
    background: var(--vz-light, #f8f9fc);
    border-radius: 8px;
    padding: 20px;
    transition: background-color 0.3s ease;
}

[data-bs-theme="dark"] .api-info-grid {
    background: var(--vz-dark-bg-subtle, #2a2f3a);
}

.api-info-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid var(--vz-border-color, #e9ecef);
}

[data-bs-theme="dark"] .api-info-item {
    border-bottom-color: var(--vz-border-color-dark, #404954);
}

.api-info-item:last-child {
    border-bottom: none;
}

.api-info-label {
    font-weight: 600;
    color: var(--vz-body-color, #495057);
    min-width: 120px;
}

[data-bs-theme="dark"] .api-info-label {
    color: var(--vz-body-color-dark, #adb5bd);
}

.api-info-value {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    background: var(--vz-white, #fff);
    padding: 8px 12px;
    border-radius: 6px;
    border: 1px solid var(--vz-border-color, #dee2e6);
    flex: 1;
    margin-left: 15px;
    color: var(--vz-body-color, #212529);
}

[data-bs-theme="dark"] .api-info-value {
    background: var(--vz-dark-bg, #32394e);
    border-color: var(--vz-border-color-dark, #404954);
    color: var(--vz-body-color-dark, #adb5bd);
}

.code-block {
    background: #1a202c;
    border: 1px solid #2d3748;
    color: #f7fafc;
    border-radius: 12px;
    padding: 25px;
    padding-top: 45px;
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', 'Courier New', monospace;
    font-size: 15px;
    line-height: 1.6;
    overflow-x: auto;
    position: relative;
    box-shadow: 0 4px 16px rgba(0, 0, 0, 0.15);
    white-space: pre-wrap;
    word-wrap: break-word;
}

.code-block::before {
    content: "";
    position: absolute;
    top: 15px;
    left: 15px;
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: #ff5f56;
    box-shadow: 20px 0 #ffbd2e, 40px 0 #27ca3f;
}

/* JSON Syntax Highlighting */
.code-block .json-key {
    color: #9f7aea;
    font-weight: 600;
}

.code-block .json-string {
    color: #68d391;
}

.code-block .json-number {
    color: #90cdf4;
}

.code-block .json-boolean {
    color: #f687b3;
}

.code-block .json-null {
    color: #a0aec0;
}

.code-block .json-punctuation {
    color: #e2e8f0;
}

.nav-pills-custom .nav-link {
    border-radius: 25px;
    padding: 12px 24px;
    margin: 0 5px;
    transition: all 0.3s ease;
    color: var(--vz-nav-link-color, #6c757d);
    background: transparent;
    border: 2px solid transparent;
}

[data-bs-theme="dark"] .nav-pills-custom .nav-link {
    color: var(--vz-nav-link-color-dark, #adb5bd);
}

.nav-pills-custom .nav-link:hover {
    background: rgba(102, 126, 234, 0.1);
    color: #667eea;
    border-color: rgba(102, 126, 234, 0.3);
}

.nav-pills-custom .nav-link.active {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-color: transparent;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.parameter-table {
    box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    border-radius: 8px;
    overflow: hidden;
}

[data-bs-theme="dark"] .parameter-table {
    box-shadow: 0 2px 10px rgba(0,0,0,0.2);
}

.parameter-table th {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    padding: 15px;
    font-weight: 600;
}

.parameter-table td {
    padding: 15px;
    border-bottom: 1px solid var(--vz-border-color, #f1f3f4);
    vertical-align: middle;
    background: var(--vz-white, #fff);
    color: var(--vz-body-color, #212529);
}

[data-bs-theme="dark"] .parameter-table td {
    background: var(--vz-dark-bg, #32394e);
    border-bottom-color: var(--vz-border-color-dark, #404954);
    color: var(--vz-body-color-dark, #adb5bd);
}

.parameter-table tr:last-child td {
    border-bottom: none;
}

.parameter-table .param-name {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    background: var(--vz-light, #f8f9fa);
    padding: 4px 8px;
    border-radius: 4px;
    color: #e83e8c;
    font-weight: 600;
}

[data-bs-theme="dark"] .parameter-table .param-name {
    background: var(--vz-dark-bg-subtle, #2a2f3a);
    color: #f687b3;
}

.api-section-card {
    border: none;
    box-shadow: 0 4px 20px rgba(0,0,0,0.08);
    border-radius: 12px;
    overflow: hidden;
    margin-bottom: 30px;
    background: var(--vz-white, #fff);
}

[data-bs-theme="dark"] .api-section-card {
    background: var(--vz-dark-bg, #32394e);
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.section-divider {
    height: 3px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 2px;
    margin: 30px 0;
}

.copy-btn {
    position: absolute;
    top: 15px;
    right: 15px;
    background: rgba(255,255,255,0.15);
    border: 1px solid rgba(255,255,255,0.3);
    color: white;
    padding: 8px 16px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
    transition: all 0.3s ease;
    z-index: 10;
}

.copy-btn:hover {
    background: rgba(255,255,255,0.25);
    color: white;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.api-key-input-group {
    position: relative;
}

.api-key-input-group .form-control {
    font-family: 'Monaco', 'Menlo', 'Ubuntu Mono', monospace;
    background: var(--vz-light, #f8f9fa);
    border: 2px solid var(--vz-border-color, #e9ecef);
    padding-right: 50px;
    color: var(--vz-body-color, #212529);
}

[data-bs-theme="dark"] .api-key-input-group .form-control {
    background: var(--vz-dark-bg, #32394e);
    border-color: var(--vz-border-color-dark, #404954);
    color: var(--vz-body-color-dark, #adb5bd);
}

.api-key-refresh-btn {
    position: absolute;
    right: 5px;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    width: 35px;
    height: 35px;
    border-radius: 6px;
    transition: all 0.3s ease;
}

.api-key-refresh-btn:hover {
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
}

/* Card header dark mode */
[data-bs-theme="dark"] .card-header.bg-light {
    background: var(--vz-dark-bg-subtle, #2a2f3a) !important;
    border-bottom-color: var(--vz-border-color-dark, #404954) !important;
}

[data-bs-theme="dark"] .card-header .card-title {
    color: var(--vz-body-color-dark, #adb5bd) !important;
}

/* Badge dark mode support */
[data-bs-theme="dark"] .badge.bg-primary {
    background: #667eea !important;
}

[data-bs-theme="dark"] .badge.bg-success {
    background: #48bb78 !important;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .code-block {
        font-size: 13px;
        padding: 20px;
        padding-top: 35px;
    }
    
    .code-block::before {
        top: 12px;
        left: 12px;
        width: 10px;
        height: 10px;
        box-shadow: 16px 0 #ffbd2e, 32px 0 #27ca3f;
    }
    
    .copy-btn {
        font-size: 11px;
        padding: 6px 12px;
    }
}

/* Animation cho notification fallback */
@keyframes slideInRight {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes slideOutRight {
    from {
        transform: translateX(0);
        opacity: 1;
    }
    to {
        transform: translateX(100%);
        opacity: 0;
    }
}

/* API Configuration Styles */
.api-config-item {
    margin-bottom: 1.5rem;
}

.api-config-item .form-label {
    margin-bottom: 0.75rem;
    color: var(--vz-gray-800, #343a40);
}

[data-bs-theme="dark"] .api-config-item .form-label {
    color: var(--vz-gray-200, #e9ecef);
}

.api-config-item .form-control {
    border: 2px solid var(--vz-border-color, #dee2e6);
    transition: all 0.3s ease;
}

.api-config-item .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
}

[data-bs-theme="dark"] .api-config-item .form-control {
    background: var(--vz-dark-bg, #32394e);
    border-color: var(--vz-border-color-dark, #404954);
    color: var(--vz-body-color-dark, #adb5bd);
}

.api-config-item .btn {
    border-width: 2px;
    transition: all 0.3s ease;
}

.api-config-item .btn:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.api-key-container .input-group {
    position: relative;
}

.quick-guide-item {
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-guide-item:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    border-color: rgba(102, 126, 234, 0.3);
}

[data-bs-theme="dark"] .quick-guide-item {
    background: var(--vz-dark-bg-subtle, #2a2f3a) !important;
    color: var(--vz-body-color-dark, #adb5bd);
}

[data-bs-theme="dark"] .quick-guide-item:hover {
    border-color: rgba(102, 126, 234, 0.5);
}

/* API Configuration Styles */
.api-config-table {
    margin-bottom: 0;
}

.api-config-table td {
    padding: 0.75rem 0;
    border: none;
    vertical-align: middle;
}

.api-config-table td:first-child {
    color: var(--vz-secondary, #6c757d);
    font-weight: 600;
}

[data-bs-theme="dark"] .api-config-table td:first-child {
    color: var(--vz-secondary-dark, #adb5bd);
}
</style>

<!-- ============================================================== -->
<!-- Start right Content here -->
<!-- ============================================================== -->
<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0 fw-bold">
                            <?=__('Tài liệu API');?>
                        </h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?=base_url('client/home');?>"><?=__('Trang chủ');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Tài liệu API');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->
            <div class="row justify-content-center">
            <div class="col-xl-10">
            <!-- API Endpoint Overview -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card api-endpoint-card">
                        <div class="card-body p-4">
                            <div class="row align-items-center">
                                <div class="col-lg-12">
                                    <h4 class="card-title text-white mb-2">
                                        <i class="ri-global-line me-2"></i>SMM Panel API v2.0
                                    </h4>
                                    <p class="text-white-50 mb-0">
                                        <?=__('Giao diện lập trình ứng dụng mạnh mẽ để tích hợp dịch vụ SMM vào hệ thống của bạn');?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Configuration -->
            <div class="row mb-4">
                <div class="col-12">
                    <div class="card api-section-card">
                        <div class="card-body p-4">
                            <div class="table-responsive">
                                <table class="table table-borderless api-config-table">
                                    <tbody>
                                        <tr>
                                            <td class="fw-semibold text-muted" style="width: 150px;">API URL</td>
                                            <td class="font-monospace"><?=base_url('api/v2');?></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">API Key</td>
                                            <td>
                                                <div class="d-flex align-items-center">
                                                    <span class="font-monospace me-3 text-danger" id="api-key-display">
                                                        <?php 
                                                        $apiKey = $getUser['api_key'] ?? __('Vui lòng đăng nhập để xem API Key');
                                                        if($apiKey !== __('Vui lòng đăng nhập để xem API Key')) {
                                                            echo substr($apiKey, 0, 5) . str_repeat('*', 8);
                                                        } else {
                                                            echo $apiKey;
                                                        }
                                                        ?>
                                                    </span>
                                                    <button class="btn btn-link p-0 text-primary" onclick="toggleApiKeyDisplay()" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=__('Hiện/Ẩn API Key');?>">
                                                        <i class="ri-eye-line" id="toggle-icon"></i>
                                                    </button>
                                                    <button class="btn btn-link p-0 text-primary ms-2" onclick="copyFullApiKey()" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=__('Sao chép API Key');?>">
                                                        <i class="ri-file-copy-line"></i>
                                                    </button>
                                                    <button class="btn btn-link p-0 text-warning ms-2" onclick="changeApiKey()" data-bs-toggle="tooltip" data-bs-placement="top" title="<?=__('Tạo API Key mới');?>">
                                                        <i class="ri-refresh-line"></i>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">HTTP Method</td>
                                            <td><span class="badge bg-primary">POST</span></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Content-Type</td>
                                            <td class="font-monospace"><code>application/x-www-form-urlencoded</code></td>
                                        </tr>
                                        <tr>
                                            <td class="fw-semibold text-muted">Response</td>
                                            <td><span class="badge bg-success">JSON</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- API Methods -->
            <div class="row" <?=$aos['fade-up'];?>>
                <div class="col-12">
                    <div class="card api-section-card">
                        <div class="card-body">
                            <!-- Navigation Pills -->
                            <ul class="nav nav-pills nav-pills-custom justify-content-center mb-4" id="api-tabs" role="tablist">
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link active" id="services-tab" data-bs-toggle="pill" 
                                            data-bs-target="#services" type="button" role="tab">
                                        <i class="ri-list-check me-2"></i>Services
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="add-order-tab" data-bs-toggle="pill" 
                                            data-bs-target="#add-order" type="button" role="tab">
                                        <i class="ri-add-circle-line me-2"></i>Add Order
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="order-status-tab" data-bs-toggle="pill" 
                                            data-bs-target="#order-status" type="button" role="tab">
                                        <i class="ri-search-line me-2"></i>Order Status
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="multiple-orders-tab" data-bs-toggle="pill" 
                                            data-bs-target="#multiple-orders" type="button" role="tab">
                                        <i class="ri-file-list-3-line me-2"></i>Multiple Orders Status
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="create-cancel-tab" data-bs-toggle="pill" 
                                            data-bs-target="#create-cancel" type="button" role="tab">
                                        <i class="ri-close-circle-line me-2"></i>Create Cancel
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="balance-tab" data-bs-toggle="pill" 
                                            data-bs-target="#balance" type="button" role="tab">
                                        <i class="ri-wallet-3-line me-2"></i>Balance
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="create-refill-tab" data-bs-toggle="pill" 
                                            data-bs-target="#create-refill" type="button" role="tab">
                                        <i class="ri-refresh-line me-2"></i>Create Refill
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="create-multiple-refill-tab" data-bs-toggle="pill" 
                                            data-bs-target="#create-multiple-refill" type="button" role="tab">
                                        <i class="ri-file-copy-line me-2"></i>Create Multiple Refill
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="refill-status-tab" data-bs-toggle="pill" 
                                            data-bs-target="#refill-status" type="button" role="tab">
                                        <i class="ri-search-2-line me-2"></i>Refill Status
                                    </button>
                                </li>
                                <li class="nav-item" role="presentation">
                                    <button class="nav-link" id="multiple-refill-status-tab" data-bs-toggle="pill" 
                                            data-bs-target="#multiple-refill-status" type="button" role="tab">
                                        <i class="ri-file-search-line me-2"></i>Multiple Refill Status
                                    </button>
                                </li>
                            </ul>

                            <!-- Tab Content -->
                            <div class="tab-content" id="api-tabsContent">
                                <!-- Services Tab -->
                                <div class="tab-pane fade show active" id="services" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"services"</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative mb-2">
                                                <button class="copy-btn" onclick="copyToClipboard('services-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="services-response" data-raw='[
  {
    "service": 1,
    "name": "Facebook cảm xúc Like 👍 Việt 🇻🇳 | 🕓 lập tức | ⚡ 2K / giờ | ⏏️ 10K | 🛡️",
    "desc": "Like chất lượng cao: người việt, người thật - click bằng tay, tốc độ trung bình, tỉ lệ tụt thấp.",
    "type": "Default",
    "category": "⭐ FB Like + cảm xúc Việt 🇻🇳 | ổn định | S1",
    "platform": "Dịch vụ Facebook",
    "rate": "15000",
    "min": "200",
    "max": "10000",
    "refill": true,
    "cancel": false,
    "dripfeed": false
  },
  {
    "service": 2,
    "name": "Facebook Tăng Bình Luận Việt 🇻🇳 + Tây hỗn hợp | 🕓 lập tức | ⚡ 2K / giờ | ⏏️ 100K | 🛡️",
    "desc": "",
    "type": "Custom Comments",
    "category": "FB Bình luận",
    "platform": "Dịch vụ Facebook",
    "rate": "57500",
    "min": "10",
    "max": "1500",
    "refill": false,
    "cancel": true,
    "dripfeed": true
  }
]'>[
  {
    <span class="json-key">"service"</span><span class="json-punctuation">:</span> <span class="json-number">1</span><span class="json-punctuation">,</span>
    <span class="json-key">"name"</span><span class="json-punctuation">:</span> <span class="json-string">"Facebook cảm xúc Like 👍 Việt 🇻🇳 | 🕓 lập tức | ⚡ 2K / giờ | ⏏️ 10K | 🛡️"</span><span class="json-punctuation">,</span>
    <span class="json-key">"desc"</span><span class="json-punctuation">:</span> <span class="json-string">"Like chất lượng cao: người việt, người thật - click bằng tay, tốc độ trung bình, tỉ lệ tụt thấp."</span><span class="json-punctuation">,</span>
    <span class="json-key">"type"</span><span class="json-punctuation">:</span> <span class="json-string">"Default"</span><span class="json-punctuation">,</span>
    <span class="json-key">"category"</span><span class="json-punctuation">:</span> <span class="json-string">"⭐ FB Like + cảm xúc Việt 🇻🇳 | ổn định | S1"</span><span class="json-punctuation">,</span>
    <span class="json-key">"platform"</span><span class="json-punctuation">:</span> <span class="json-string">"Dịch vụ Facebook"</span><span class="json-punctuation">,</span>
    <span class="json-key">"rate"</span><span class="json-punctuation">:</span> <span class="json-string">"15000"</span><span class="json-punctuation">,</span>
    <span class="json-key">"min"</span><span class="json-punctuation">:</span> <span class="json-string">"200"</span><span class="json-punctuation">,</span>
    <span class="json-key">"max"</span><span class="json-punctuation">:</span> <span class="json-string">"10000"</span><span class="json-punctuation">,</span>
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-boolean">true</span><span class="json-punctuation">,</span>
    <span class="json-key">"cancel"</span><span class="json-punctuation">:</span> <span class="json-boolean">false</span><span class="json-punctuation">,</span>
    <span class="json-key">"dripfeed"</span><span class="json-punctuation">:</span> <span class="json-boolean">false</span>
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"service"</span><span class="json-punctuation">:</span> <span class="json-number">2</span><span class="json-punctuation">,</span>
    <span class="json-key">"name"</span><span class="json-punctuation">:</span> <span class="json-string">"Facebook Tăng Bình Luận Việt 🇻🇳 + Tây hỗn hợp | 🕓 lập tức | ⚡ 2K / giờ | ⏏️ 100K | 🛡️"</span><span class="json-punctuation">,</span>
    <span class="json-key">"desc"</span><span class="json-punctuation">:</span> <span class="json-string">""</span><span class="json-punctuation">,</span>
    <span class="json-key">"type"</span><span class="json-punctuation">:</span> <span class="json-string">"Custom Comments"</span><span class="json-punctuation">,</span>
    <span class="json-key">"category"</span><span class="json-punctuation">:</span> <span class="json-string">"FB Bình luận"</span><span class="json-punctuation">,</span>
    <span class="json-key">"platform"</span><span class="json-punctuation">:</span> <span class="json-string">"Dịch vụ Facebook"</span><span class="json-punctuation">,</span>
    <span class="json-key">"rate"</span><span class="json-punctuation">:</span> <span class="json-string">"57500"</span><span class="json-punctuation">,</span>
    <span class="json-key">"min"</span><span class="json-punctuation">:</span> <span class="json-string">"10"</span><span class="json-punctuation">,</span>
    <span class="json-key">"max"</span><span class="json-punctuation">:</span> <span class="json-string">"1500"</span><span class="json-punctuation">,</span>
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-boolean">false</span><span class="json-punctuation">,</span>
    <span class="json-key">"cancel"</span><span class="json-punctuation">:</span> <span class="json-boolean">true</span><span class="json-punctuation">,</span>
    <span class="json-key">"dripfeed"</span><span class="json-punctuation">:</span> <span class="json-boolean">true</span>
  }
]</div>
                                            </div>
                                            <p><strong>Type:</strong> <?=implode(', ', array_column(getListServiceType(), 'code'));?></p>
                                            <p><strong>Rate:</strong> <?=__('Giá của số lượng 1.000');?> (<?=__('Dịch vụ Default, Custom Comments, Mentions Hashtag, SEO');?>)</p>
                                            <p><strong>Rate:</strong> <?=__('Giá của số lượng 1');?> (<?=__('Dịch vụ Package, Subscriptions, Custom Comments Package');?>)</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Add Order Tab -->
                                <div class="tab-pane fade" id="add-order" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"add"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">service</span></td>
                                                            <td>Service ID</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">link</span></td>
                                                            <td>Link to page</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">comments</span></td>
                                                            <td>Comments (Only for Custom Comments service)</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">quantity</span></td>
                                                            <td>Quantity to be delivered</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('add-order-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="add-order-response" data-raw='{
  "order": 23501
}'>{
  <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">23501</span>
}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Order Status Tab -->
                                <div class="tab-pane fade" id="order-status" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"status"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">order</span></td>
                                                            <td>Order ID</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative mb-2">
                                                <button class="copy-btn" onclick="copyToClipboard('order-status-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="order-status-response" data-raw='{
  "charge": "0.27819",
  "start_count": "3572",
  "status": "Completed",
  "remains": "157"
}'>{
  <span class="json-key">"charge"</span><span class="json-punctuation">:</span> <span class="json-string">"0.27819"</span><span class="json-punctuation">,</span>
  <span class="json-key">"start_count"</span><span class="json-punctuation">:</span> <span class="json-string">"3572"</span><span class="json-punctuation">,</span>
  <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"Completed"</span><span class="json-punctuation">,</span>
  <span class="json-key">"remains"</span><span class="json-punctuation">:</span> <span class="json-string">"157"</span><span class="json-punctuation"></span>
}</div>
                                            </div>
                                            <p><strong>Status:</strong> Pending, Processing, In progress, Completed, Partial, Canceled</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Orders Tab -->
                                <div class="tab-pane fade" id="multiple-orders" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"status"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">orders</span></td>
                                                            <td>Order IDs separated by comma</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('multiple-orders-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="multiple-orders-response" data-raw='{
  "123": {
    "charge": "0.27819",
    "start_count": "3572",
    "status": "Partial",
    "remains": "157"
  },
  "456": {
    "error": "Incorrect order ID"
  },
  "789": {
    "charge": "1.44219",
    "start_count": "234",
    "status": "In progress",
    "remains": "10"
  }
}'>{
  <span class="json-key">"123"</span><span class="json-punctuation">:</span> {
    <span class="json-key">"charge"</span><span class="json-punctuation">:</span> <span class="json-string">"0.27819"</span><span class="json-punctuation">,</span>
    <span class="json-key">"start_count"</span><span class="json-punctuation">:</span> <span class="json-string">"3572"</span><span class="json-punctuation">,</span>
    <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"Partial"</span><span class="json-punctuation">,</span> 
    <span class="json-key">"remains"</span><span class="json-punctuation">:</span> <span class="json-string">"157"</span>
  }<span class="json-punctuation">,</span>
  <span class="json-key">"456"</span><span class="json-punctuation">:</span> {
    <span class="json-key">"error"</span><span class="json-punctuation">:</span> <span class="json-string">"Incorrect order ID"</span>
  }<span class="json-punctuation">,</span>
  <span class="json-key">"789"</span><span class="json-punctuation">:</span> {
    <span class="json-key">"charge"</span><span class="json-punctuation">:</span> <span class="json-string">"1.44219"</span><span class="json-punctuation">,</span>
    <span class="json-key">"start_count"</span><span class="json-punctuation">:</span> <span class="json-string">"234"</span><span class="json-punctuation">,</span>
    <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"In progress"</span><span class="json-punctuation">,</span>
    <span class="json-key">"remains"</span><span class="json-punctuation">:</span> <span class="json-string">"10"</span>
  }
}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Cancel Tab -->
                                <div class="tab-pane fade" id="create-cancel" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"cancel"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">orders</span></td>
                                                            <td>Order IDs (separated by a comma, up to 100 IDs)</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('cancel-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="cancel-response" data-raw='[
  {
    "order": 9,
    "cancel": {
      "error": "Incorrect order ID"
    }
  },
  {
    "order": 2,
    "cancel": 1
  }
]'>[
  {
    <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">9</span><span class="json-punctuation">,</span>
    <span class="json-key">"cancel"</span><span class="json-punctuation">:</span> {
      <span class="json-key">"error"</span><span class="json-punctuation">:</span> <span class="json-string">"Incorrect order ID"</span>
    }
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">2</span><span class="json-punctuation">,</span>
    <span class="json-key">"cancel"</span><span class="json-punctuation">:</span> <span class="json-number">1</span>
  }
]</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Balance Tab -->
                                <div class="tab-pane fade" id="balance" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"balance"</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('balance-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="balance-response" data-raw='{
  "balance": "100.84292",
  "currency": "USD"
}'>{
  <span class="json-key">"balance"</span><span class="json-punctuation">:</span> <span class="json-string">"100.84292"</span><span class="json-punctuation">,</span>
  <span class="json-key">"currency"</span><span class="json-punctuation">:</span> <span class="json-string">"USD"</span>
}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Refill Tab -->
                                <div class="tab-pane fade" id="create-refill" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"refill"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">order</span></td>
                                                            <td>Order ID</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('refill-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="refill-response" data-raw='{
  "refill": "1"
}'>{
  <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-string">"1"</span>
}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Create Multiple Refill Tab -->
                                <div class="tab-pane fade" id="create-multiple-refill" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"refill"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">orders</span></td>
                                                            <td>Order IDs separated by comma</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('multiple-refill-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="multiple-refill-response" data-raw='[
  {
    "order": 1,
    "refill": 1
  },
  {
    "order": 2,
    "refill": 2
  },
  {
    "order": 3,
    "refill": {
      "error": "Incorrect order ID"
    }
  }
]'>[
  {
    <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">1</span><span class="json-punctuation">,</span>
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-number">1</span>
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">2</span><span class="json-punctuation">,</span>
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-number">2</span>
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"order"</span><span class="json-punctuation">:</span> <span class="json-number">3</span><span class="json-punctuation">,</span>
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> {
      <span class="json-key">"error"</span><span class="json-punctuation">:</span> <span class="json-string">"Incorrect order ID"</span>
    }
  }
]</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Refill Status Tab -->
                                <div class="tab-pane fade" id="refill-status" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"refill_status"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">refill</span></td>
                                                            <td>Refill ID</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('refill-status-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="refill-status-response" data-raw='{
  "status": "Completed"
}'>{
  <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"Completed"</span>
}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Multiple Refill Status Tab -->
                                <div class="tab-pane fade" id="multiple-refill-status" role="tabpanel">
                                    <div class="row">
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-settings-4-line me-2"></i>Parameters
                                            </h6>
                                            <div class="table-responsive">
                                                <table class="table parameter-table">
                                                    <thead>
                                                        <tr>
                                                            <th>Parameter</th>
                                                            <th>Description</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td><span class="param-name">key</span></td>
                                                            <td>Your API key</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">action</span></td>
                                                            <td>"refill_status"</td>
                                                        </tr>
                                                        <tr>
                                                            <td><span class="param-name">refills</span></td>
                                                            <td>Refill IDs (separated by a comma, up to 100 IDs)</td>
                                                        </tr>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <h6 class="fw-semibold mb-3">
                                                <i class="ri-code-s-slash-line me-2"></i>Example Response
                                            </h6>
                                            <div class="position-relative">
                                                <button class="copy-btn" onclick="copyToClipboard('multiple-refill-status-response')">
                                                    <i class="ri-file-copy-line me-1"></i>Copy
                                                </button>
                                                <div class="code-block" id="multiple-refill-status-response" data-raw='[
  {
    "refill": 1,
    "status": "Completed"
  },
  {
    "refill": 2,
    "status": "Rejected"
  },
  {
    "refill": 3,
    "status": {
      "error": "Refill not found"
    }
  }
]'>[
  {
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-number">1</span><span class="json-punctuation">,</span>
    <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"Completed"</span>
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-number">2</span><span class="json-punctuation">,</span>
    <span class="json-key">"status"</span><span class="json-punctuation">:</span> <span class="json-string">"Rejected"</span>
  }<span class="json-punctuation">,</span>
  {
    <span class="json-key">"refill"</span><span class="json-punctuation">:</span> <span class="json-number">3</span><span class="json-punctuation">,</span>
    <span class="json-key">"status"</span><span class="json-punctuation">:</span> {
      <span class="json-key">"error"</span><span class="json-punctuation">:</span> <span class="json-string">"Refill not found"</span>
    }
  }
]</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
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
<!-- End main-content -->

<script>
function toggleApiKeyVisibility() {
    const apiKeyInput = document.getElementById('api-key-input');
    const toggleIcon = document.getElementById('toggle-api-key-icon');
    
    if (apiKeyInput.type === 'password') {
        apiKeyInput.type = 'text';
        toggleIcon.className = 'ri-eye-off-line';
    } else {
        apiKeyInput.type = 'password';
        toggleIcon.className = 'ri-eye-line';
    }
}

// Toggle API Key Display (for table format)
function toggleApiKeyDisplay() {
    const displayElement = document.getElementById('api-key-display');
    const toggleIcon = document.getElementById('toggle-icon');
    const fullApiKey = '<?=$getUser['api_key'] ?? '';?>';
    
    if (fullApiKey === '') {
        showNotification('<?=__("❌ Không có API Key để hiển thị!");?>', 'error');
        return;
    }
    
    if (toggleIcon.className === 'ri-eye-line') {
        // Show full API key
        displayElement.textContent = fullApiKey;
        toggleIcon.className = 'ri-eye-off-line';
    } else {
        // Hide API key
        displayElement.textContent = fullApiKey.substr(0, 5) + '********';
        toggleIcon.className = 'ri-eye-line';
    }
}

// Copy Full API Key
function copyFullApiKey() {
    const fullApiKey = '<?=$getUser['api_key'] ?? '';?>';
    
    if (fullApiKey && fullApiKey !== '') {
        copyText(fullApiKey);
        showNotification('<?=__("✅ Đã sao chép API Key vào clipboard!");?>', 'success');
    } else {
        showNotification('<?=__("❌ Không có API Key để sao chép!");?>', 'error');
    }
}

// Change API Key
function changeApiKey() {
    Swal.fire({
        title: '<?=__("Tạo API Key mới?");?>',
        text: '<?=__("API Key cũ sẽ không còn hoạt động sau khi tạo mới!");?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#667eea',
        cancelButtonColor: '#f46a6a',
        confirmButtonText: '<?=__("Tạo mới");?>',
        cancelButtonText: '<?=__("Hủy");?>'
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '<?=__("Đang tạo API Key mới...");?>',
                allowOutsideClick: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });
            
            $.ajax({
                url: "<?=base_url('ajaxs/client/auth.php');?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'changeAPIKey',
                    token: '<?=$getUser['token'] ?? '';?>',
                },
                success: function(result) {
                    if (result.status == 'success') {
                        Swal.fire({
                            title: '<?=__("Thành công!");?>',
                            text: '<?=__("API Key mới đã được tạo thành công!");?>',
                            icon: 'success',
                            confirmButtonColor: '#667eea',
                            timer: 2000
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: '<?=__("Lỗi!");?>',
                            text: result.msg || '<?=__("Có lỗi xảy ra khi tạo API Key mới");?>',
                            icon: 'error',
                            confirmButtonColor: '#f46a6a'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: '<?=__("Lỗi!");?>',
                        text: '<?=__("Có lỗi xảy ra, vui lòng thử lại sau");?>',
                        icon: 'error',
                        confirmButtonColor: '#f46a6a'
                    });
                }
            });
        }
    });
}

// Copy Text Helper
function copyText(text) {
    if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(function() {
            // Success handled by caller
        }).catch(function(err) {
            console.error('Clipboard API failed: ', err);
            fallbackCopy(text);
        });
    } else {
        fallbackCopy(text);
    }
}

// Show Notification
function showNotification(message, type = 'success') {
    const bgColor = type === 'success' ? 
        'linear-gradient(135deg, #48bb78 0%, #38a169 100%)' : 
        'linear-gradient(135deg, #f56565 0%, #e53e3e 100%)';
        
    if (typeof Toastify !== 'undefined') {
        Toastify({
            text: message,
            duration: 3000,
            close: true,
            gravity: "top",
            position: "right",
            style: {
                background: bgColor,
                borderRadius: "8px",
                fontWeight: "500"
            }
        }).showToast();
    } else {
        // Fallback notification
        const notification = document.createElement('div');
        notification.innerHTML = message;
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: ${bgColor};
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            font-weight: 500;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            animation: slideInRight 0.3s ease;
            max-width: 300px;
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOutRight 0.3s ease';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }, 3000);
    }
}

// Auto-scroll smooth behavior for tabs
document.querySelectorAll('[data-bs-toggle="pill"]').forEach(function(tab) {
    tab.addEventListener('shown.bs.tab', function() {
        tab.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });
});

// Initialize tooltips
document.addEventListener('DOMContentLoaded', function() {
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

function copyToClipboard(elementId) {
    const element = document.getElementById(elementId);
    const button = element.parentElement.querySelector('.copy-btn');
    
    // Lấy plain text từ data-raw attribute
    const text = element.getAttribute('data-raw');
    
    if (!text) {
        console.error('Không tìm thấy dữ liệu để copy');
        return;
    }
    
    // Tạm thời thay đổi nút để show feedback
    const originalContent = button.innerHTML;
    button.innerHTML = '<i class="ri-check-line me-1"></i>Copied!';
    button.style.background = 'rgba(72, 187, 120, 0.8)';
    
    // Copy to clipboard
    copyText(text);
    showNotification('<?=__("✅ Đã sao chép JSON vào clipboard!");?>', 'success');
    
    // Reset button sau 2 giây
    setTimeout(function() {
        button.innerHTML = originalContent;
        button.style.background = '';
    }, 2000);
}

function fallbackCopy(text) {
    const textArea = document.createElement('textarea');
    textArea.value = text;
    textArea.style.position = 'fixed';
    textArea.style.left = '-999999px';
    textArea.style.top = '-999999px';
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    
    try {
        document.execCommand('copy');
    } catch (err) {
        console.error('Fallback copy failed: ', err);
    }
    
    document.body.removeChild(textArea);
}
</script>

<?php
require_once(__DIR__.'/footer.php');
?>