<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => 'AI Chat Assistant',
    'desc'   => 'CMSNT Panel - AI Chat',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co, ai chat, chatgpt'
];
$body['header'] = '
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<style>
.chat-container {
    height: 70vh;
    max-height: 600px;
    overflow-y: auto;
    border: 1px solid #e2e8f0;
    border-radius: 0.75rem;
    padding: 0;
    background: linear-gradient(to bottom, #f7fafc 0%, #edf2f7 100%);
    scroll-behavior: smooth;
    position: relative;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.05);
}

.chat-messages {
    padding: 1.5rem;
    min-height: 100%;
    display: flex;
    flex-direction: column;
}

.load-more-container {
    text-align: center;
    padding: 1rem;
    background: #f8f9fc;
    border-bottom: 1px solid #e3e6f0;
    margin: 0;
}

.load-more-btn {
    background: #5a6acf;
    border: none;
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
}

.load-more-btn:hover {
    background: #4c63d2;
    transform: translateY(-1px);
}

.load-more-btn:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
}

.loading-spinner {
    width: 16px;
    height: 16px;
    border: 2px solid transparent;
    border-top: 2px solid #ffffff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    display: inline-block;
    margin-right: 0.5rem;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.chat-message {
    display: flex;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    max-width: 100%;
}

.chat-message.user {
    flex-direction: row-reverse;
    justify-content: flex-start;
}

.chat-message.ai {
    justify-content: flex-start;
}

.message-wrapper {
    display: flex;
    flex-direction: column;
    max-width: calc(100% - 50px);
    min-width: 0;
}

.chat-message.user .message-wrapper {
    align-items: flex-end;
}

.chat-message.ai .message-wrapper {
    align-items: flex-start;
}

.message-content {
    padding: 0.75rem 1rem;
    word-wrap: break-word;
    font-size: 0.875rem;
    line-height: 1.4;
    position: relative;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    max-width: 100%;
    min-width: 0;
}

.chat-message.user .message-content {
    background: #5a6acf;
    color: white;
    border-radius: 1.125rem 1.125rem 0.25rem 1.125rem;
    margin-left: 0.5rem;
}

.chat-message.ai .message-content {
    background: #ffffff;
    color: #2d3748;
    border-radius: 1.125rem 1.125rem 1.125rem 0.25rem;
    border: 1px solid #e2e8f0;
    margin-right: 0.5rem;
}

.message-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.1rem;
    flex-shrink: 0;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-top: 0.25rem;
    border: 3px solid #ffffff;
}

.message-avatar.user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.message-avatar.ai {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

.message-time {
    font-size: 0.6875rem;
    color: #a0aec0;
    margin-top: 0.375rem;
    font-weight: 500;
    padding: 0 0.5rem;
}

.chat-message.user .message-time {
    text-align: right;
}

.chat-message.ai .message-time {
    text-align: left;
}

.chat-input-container {
    position: relative;
    background: #ffffff;
    border: 1px solid #e3e6f0;
    border-radius: 0.375rem;
    padding: 0.5rem;
    margin-top: 1rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.05);
}

.chat-input {
    border: none;
    outline: none;
    padding: 0.5rem 3rem 0.5rem 0.75rem;
    resize: none;
    min-height: 2.5rem;
    max-height: 120px;
    background: transparent;
    font-size: 0.875rem;
    line-height: 1.5;
    width: 100%;
    font-family: inherit;
    transition: all 0.2s ease;
}

.chat-input:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    background: #f8f9fc;
}

.chat-input::placeholder {
    color: #858796;
}

.send-button {
    position: absolute;
    right: 0.5rem;
    top: 50%;
    transform: translateY(-50%);
    border: none;
    background: #5a6acf;
    color: white;
    width: 2.25rem;
    height: 2.25rem;
    border-radius: 0.375rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s ease;
    font-size: 0.875rem;
}

.send-button:hover {
    background: #4c63d2;
}

.send-button:disabled {
    background: #858796;
    cursor: not-allowed;
}

.typing-indicator {
    padding: 0.75rem 1rem;
    background: #ffffff;
    border-radius: 1.125rem 1.125rem 1.125rem 0.25rem;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 2px rgba(0,0,0,0.1);
    margin-right: 0.5rem;
}

#typingIndicator {
    display: none !important;
}

#typingIndicator.show {
    display: flex !important;
    align-items: flex-start;
    margin-bottom: 1.5rem;
    justify-content: flex-start;
}

.typing-dots {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.typing-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #858796;
    animation: typing 1.4s infinite;
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
        opacity: 0.5;
    }
    30% {
        transform: translateY(-4px);
        opacity: 1;
    }
}



.clear-chat-btn {
    border-radius: 0.375rem;
    padding: 0.375rem 0.75rem;
    font-size: 0.8125rem;
}

.ai-status {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #1cc88a;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: #1cc88a;
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

.model-info {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.8125rem;
    color: #5a5c69;
    background: #f8f9fc;
    padding: 0.375rem 0.75rem;
    border-radius: 0.375rem;
    border: 1px solid #e3e6f0;
    height: 31px;
    min-width: fit-content;
}

.memory-toggle {
    cursor: pointer;
    transition: all 0.2s ease;
    user-select: none;
}

.memory-toggle:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.memory-toggle.disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.memory-on {
    background: #e8f5e8;
    border-color: #1cc88a;
    color: #1cc88a;
}

.memory-off {
    background: #f8f9fc;
    border-color: #858796;
    color: #858796;
}

.message-content pre {
    background: #2d3748;
    color: #e2e8f0;
    padding: 1rem;
    border-radius: 0.5rem;
    overflow-x: auto;
    font-size: 0.8125rem;
    margin: 0.75rem 0;
    border: none;
    font-family: monospace;
    line-height: 1.5;
}

.message-content code {
    background: #edf2f7;
    color: #2d3748;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.8125rem;
    font-family: monospace;
    font-weight: 500;
}

.message-content pre code {
    background: none;
    color: inherit;
    padding: 0;
    border: none;
}

.chat-container::-webkit-scrollbar {
    width: 8px;
}

.chat-container::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
    border-radius: 4px;
}

.chat-container::-webkit-scrollbar-thumb {
    background: rgba(0,0,0,0.2);
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: content-box;
}

.chat-container::-webkit-scrollbar-thumb:hover {
    background: rgba(0,0,0,0.3);
    background-clip: content-box;
}

.chart-container {
    max-width: 100%;
    height: 400px;
    margin: 1rem 0;
    padding: 1rem;
    background: #f8f9fc;
    border-radius: 0.5rem;
    border: 1px solid #e3e6f0;
}

.chart-title {
    font-size: 0.875rem;
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.75rem;
    text-align: center;
}

.chart-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    height: 200px;
    color: #858796;
    font-size: 0.875rem;
}

.chart-actions {
    display: flex;
    gap: 0.5rem;
    margin-top: 0.75rem;
    justify-content: center;
}

.chart-btn {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
    border: 1px solid #e3e6f0;
    border-radius: 0.25rem;
    background: white;
    color: #5a5c69;
    cursor: pointer;
    transition: all 0.2s ease;
}

.chart-btn:hover {
    background: #f8f9fc;
    border-color: #5a6acf;
    color: #5a6acf;
}

.highlight-section {
    background: linear-gradient(135deg, #5a6acf 0%, #764ba2 100%);
    color: white;
    padding: 0.5rem 1rem;
    border-radius: 0.5rem;
    margin: 0.5rem 0;
    font-weight: 500;
}

/* Dark Mode Styles - Tông màu đen thực tế */
[data-theme-mode="dark"] .chat-container {
    background: linear-gradient(to bottom, #1a1a1a 0%, #2d2d2d 100%);
    border: 1px solid #404040;
    box-shadow: inset 0 1px 3px rgba(0,0,0,0.4);
}

[data-theme-mode="dark"] .chat-messages {
    background: transparent;
}

[data-theme-mode="dark"] .load-more-container {
    background: #1a1a1a;
    border-bottom: 1px solid #404040;
}

[data-theme-mode="dark"] .load-more-btn {
    background: #6c5ce7;
    color: white;
}

[data-theme-mode="dark"] .load-more-btn:hover {
    background: #5a4fcf;
}

[data-theme-mode="dark"] .load-more-btn:disabled {
    background: #4a5568;
}

[data-theme-mode="dark"] .chat-message.ai .message-content {
    background: #2d2d2d;
    color: #e2e8f0;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .chat-message.user .message-content {
    background: #6c5ce7;
    color: white;
}

[data-theme-mode="dark"] .message-time {
    color: #9ca3af;
}

[data-theme-mode="dark"] .chat-input-container {
    background: #2d2d2d;
    border: 1px solid #404040;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

[data-theme-mode="dark"] .chat-input {
    background: transparent;
    color: #e2e8f0;
}

[data-theme-mode="dark"] .chat-input:disabled {
    background: #1a1a1a;
    color: #9ca3af;
}

[data-theme-mode="dark"] .chat-input::placeholder {
    color: #9ca3af;
}

[data-theme-mode="dark"] .send-button {
    background: #6c5ce7;
    color: white;
}

[data-theme-mode="dark"] .send-button:hover {
    background: #5a4fcf;
}

[data-theme-mode="dark"] .send-button:disabled {
    background: #4a5568;
}

[data-theme-mode="dark"] .typing-indicator {
    background: #2d2d2d;
    border: 1px solid #404040;
    box-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

[data-theme-mode="dark"] .typing-dot {
    background: #9ca3af;
}



[data-theme-mode="dark"] .model-info {
    background: #2d2d2d;
    border: 1px solid #404040;
    color: #e2e8f0;
}

[data-theme-mode="dark"] .memory-toggle {
    background: #2d2d2d;
    border: 1px solid #404040;
    color: #e2e8f0;
}

[data-theme-mode="dark"] .memory-toggle:hover {
    box-shadow: 0 2px 4px rgba(0,0,0,0.3);
    background: #3a3a3a;
}

[data-theme-mode="dark"] .memory-on {
    background: #1a4d3a;
    border-color: #1cc88a;
    color: #1cc88a;
}

[data-theme-mode="dark"] .memory-off {
    background: #2d2d2d;
    border-color: #9ca3af;
    color: #9ca3af;
}

[data-theme-mode="dark"] .message-content pre {
    background: #1a1a1a;
    color: #e2e8f0;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .message-content code {
    background: #1a1a1a;
    color: #e2e8f0;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .message-content pre code {
    background: none;
    color: inherit;
    border: none;
}

[data-theme-mode="dark"] .chat-container::-webkit-scrollbar {
    width: 8px;
}

[data-theme-mode="dark"] .chat-container::-webkit-scrollbar-track {
    background: rgba(0,0,0,0.3);
    border-radius: 4px;
}

[data-theme-mode="dark"] .chat-container::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.2);
    border-radius: 4px;
    border: 2px solid transparent;
    background-clip: content-box;
}

[data-theme-mode="dark"] .chat-container::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.3);
    background-clip: content-box;
}

[data-theme-mode="dark"] .chart-container {
    background: #2d2d2d;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .chart-title {
    color: #e2e8f0;
}

[data-theme-mode="dark"] .chart-loading {
    color: #9ca3af;
}

[data-theme-mode="dark"] .chart-btn {
    background: #2d2d2d;
    border: 1px solid #404040;
    color: #e2e8f0;
}

[data-theme-mode="dark"] .chart-btn:hover {
    background: #1a1a1a;
    border-color: #6c5ce7;
    color: #6c5ce7;
}

[data-theme-mode="dark"] .highlight-section {
    background: linear-gradient(135deg, #6c5ce7 0%, #5a4fcf 100%);
    color: white;
}

/* Dark mode cho message avatars */
[data-theme-mode="dark"] .message-avatar {
    border: 3px solid #2d2d2d;
    box-shadow: 0 2px 4px rgba(0,0,0,0.4);
}

[data-theme-mode="dark"] .message-avatar.user {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

[data-theme-mode="dark"] .message-avatar.ai {
    background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
}

/* Dark mode cho card và container */
[data-theme-mode="dark"] .card {
    background: #2d2d2d;
    border: 1px solid #404040;
}

[data-theme-mode="dark"] .card-body {
    background: transparent;
}

/* Dark mode cho text content */
[data-theme-mode="dark"] .message-content strong {
    color: #ffffff;
}

[data-theme-mode="dark"] .message-content em {
    color: #e2e8f0;
}

/* Dark mode cho links */
[data-theme-mode="dark"] .message-content a {
    color: #6c5ce7;
}

[data-theme-mode="dark"] .message-content a:hover {
    color: #5a4fcf;
}

/* Dark mode cho loading spinner */
[data-theme-mode="dark"] .loading-spinner {
    border-top: 2px solid #ffffff;
}

/* Dark mode cho welcome message */
[data-theme-mode="dark"] .chat-message.ai .message-content {
    color: #e2e8f0;
}

[data-theme-mode="dark"] .chat-message.ai .message-content strong {
    color: #ffffff;
}

/* Dark mode cho page title */
[data-theme-mode="dark"] .page-title {
    color: #e2e8f0;
}

/* Dark mode cho các button actions */
[data-theme-mode="dark"] .btn-danger {
    background: #e74c3c;
    border-color: #e74c3c;
}

[data-theme-mode="dark"] .btn-danger:hover {
    background: #c0392b;
    border-color: #a93226;
}

/* Đảm bảo text trong dark mode được hiển thị rõ ràng */
[data-theme-mode="dark"] .main-content {
    color: #e2e8f0;
}

[data-theme-mode="dark"] .container-fluid {
    color: #e2e8f0;
}

/* Dark mode cho page header */
[data-theme-mode="dark"] .page-header-breadcrumb {
    color: #e2e8f0;
}

/* Dark mode cho icons */
[data-theme-mode="dark"] .ri-robot-line,
[data-theme-mode="dark"] .ri-cpu-line,
[data-theme-mode="dark"] .ri-brain-line,
[data-theme-mode="dark"] .ri-delete-bin-line {
    color: #e2e8f0;
}

/* Dark mode cho status indicators */
[data-theme-mode="dark"] .status-dot {
    background: #1cc88a;
}

[data-theme-mode="dark"] .ai-status {
    color: #1cc88a;
}

/* Dark mode cho breadcrumb và header elements */
[data-theme-mode="dark"] .d-md-flex h1 {
    color: #e2e8f0;
}

[data-theme-mode="dark"] .fs-18 {
    color: #e2e8f0;
}

/* Dark mode cho welcome message cụ thể */
[data-theme-mode="dark"] .message-content {
    color: #e2e8f0;
}

/* Dark mode cho emoji trong message */
[data-theme-mode="dark"] .message-content {
    color: #e2e8f0;
}

/* Dark mode cho table/list content nếu có */
[data-theme-mode="dark"] .message-content table {
    color: #e2e8f0;
    border-color: #404040;
}

[data-theme-mode="dark"] .message-content table td,
[data-theme-mode="dark"] .message-content table th {
    border-color: #404040;
}

/* Dark mode cho blockquote */
[data-theme-mode="dark"] .message-content blockquote {
    background: #1a1a1a;
    border-left: 4px solid #6c5ce7;
    color: #e2e8f0;
}

/* Dark mode cho hr */
[data-theme-mode="dark"] .message-content hr {
    border-color: #404040;
}

/* Dark mode cho các list */
[data-theme-mode="dark"] .message-content ul,
[data-theme-mode="dark"] .message-content ol {
    color: #e2e8f0;
}

/* Dark mode cho các number và currency */
[data-theme-mode="dark"] .message-content .number,
[data-theme-mode="dark"] .message-content .currency {
    color: #74c0fc;
}

/* Dark mode cho các highlight text */
[data-theme-mode="dark"] .message-content .highlight {
    background: #6c5ce7;
    color: white;
    padding: 2px 4px;
    border-radius: 3px;
}

/* Dark mode cho các gap và margin */
[data-theme-mode="dark"] .gap-2 {
    color: #e2e8f0;
}

/* Dark mode cho toàn bộ content area */
[data-theme-mode="dark"] .app-content {
    background: transparent;
    color: #e2e8f0;
}

/* Dark mode cho các button group */
[data-theme-mode="dark"] .d-flex .btn {
    border-color: #404040;
}

/* Dark mode cho các icon trong button */
[data-theme-mode="dark"] .btn i {
    color: inherit;
}

/* Dark mode responsive improvements */
@media (max-width: 768px) {
    [data-theme-mode="dark"] .chat-container {
        height: 60vh;
    }
    
    [data-theme-mode="dark"] .model-info {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
    
    [data-theme-mode="dark"] .memory-toggle {
        font-size: 0.75rem;
        padding: 0.25rem 0.5rem;
    }
}

/* Dark mode cho custom scrollbar cho mobile */
[data-theme-mode="dark"] .chat-container {
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.2) transparent;
}

/* Dark mode cho focus states */
[data-theme-mode="dark"] .chat-input:focus {
    border-color: #6c5ce7;
    box-shadow: 0 0 0 0.2rem rgba(108,92,231,0.25);
}

[data-theme-mode="dark"] .send-button:focus {
    box-shadow: 0 0 0 0.2rem rgba(108,92,231,0.25);
}

/* Dark mode cho selection text */
[data-theme-mode="dark"] .message-content::selection {
    background: #6c5ce7;
    color: white;
}

[data-theme-mode="dark"] .chat-input::selection {
    background: #6c5ce7;
    color: white;
}

/* Debug Info Box Styles */
.debug-info-box {
    background: #1a1a1a;
    border: 1px solid #333;
    border-radius: 0.5rem;
    padding: 1rem;
    margin: 1rem 0;
    font-family: "Courier New", monospace;
    font-size: 0.8125rem;
    color: #a0a0a0;
    position: relative;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.debug-info-box .debug-title {
    color: #ff6b6b;
    font-weight: bold;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
}

.debug-info-box .debug-content {
    color: #e2e8f0;
    line-height: 1.4;
}

.debug-info-box .debug-item {
    margin-bottom: 0.5rem;
    padding: 0.25rem 0;
    border-bottom: 1px solid #333;
}

.debug-info-box .debug-item:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.debug-info-box .debug-label {
    color: #74c0fc;
    font-weight: bold;
}

.debug-info-box .debug-value {
    color: #a0a0a0;
    word-break: break-all;
}

.debug-info-box .debug-sql {
    background: #0f0f0f;
    border: 1px solid #333;
    border-radius: 0.25rem;
    padding: 0.5rem;
    margin-top: 0.5rem;
    color: #90ee90;
    font-family: "Courier New", monospace;
    font-size: 0.75rem;
    overflow-x: auto;
}

.debug-info-box .debug-collapse {
    position: absolute;
    top: 0.5rem;
    right: 0.5rem;
    background: #333;
    border: none;
    color: #a0a0a0;
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    cursor: pointer;
    font-size: 0.75rem;
}

.debug-info-box .debug-collapse:hover {
    background: #444;
    color: #fff;
}

.debug-info-box.collapsed .debug-content {
    display: none;
}

/* Dark mode cho debug info */
[data-theme-mode="dark"] .debug-info-box {
    background: #0f0f0f;
    border: 1px solid #2d2d2d;
    color: #9ca3af;
}

[data-theme-mode="dark"] .debug-info-box .debug-title {
    color: #ff6b6b;
}

[data-theme-mode="dark"] .debug-info-box .debug-content {
    color: #e2e8f0;
}

[data-theme-mode="dark"] .debug-info-box .debug-item {
    border-bottom: 1px solid #2d2d2d;
}

[data-theme-mode="dark"] .debug-info-box .debug-label {
    color: #74c0fc;
}

[data-theme-mode="dark"] .debug-info-box .debug-value {
    color: #9ca3af;
}

[data-theme-mode="dark"] .debug-info-box .debug-sql {
    background: #1a1a1a;
    border: 1px solid #2d2d2d;
    color: #90ee90;
}

[data-theme-mode="dark"] .debug-info-box .debug-collapse {
    background: #2d2d2d;
    color: #9ca3af;
}

[data-theme-mode="dark"] .debug-info-box .debug-collapse:hover {
    background: #3a3a3a;
    color: #fff;
}


</style>
';
$body['footer'] = '
 
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');

// Kiểm tra DEBUG constant
if (!defined('DEBUG')) {
    define('DEBUG', false);
}

// Lấy thông tin model hiện tại
$current_model = $CMSNT->site('chatgpt_model');
if (empty($current_model)) {
    $current_model = 'gpt-3.5-turbo';
}

// Danh sách models có sẵn
$available_models = [
    'Khuyến nghị - Mới nhất 2025' => [
        'gpt-4o-2024-11-20' => '🔥 GPT-4o (2024-11-20) - $2.50/$5.00 per 1M tokens'
    ],
    'GPT-4 Series - Cao cấp' => [
        'gpt-4o' => 'GPT-4o - $2.50/$5.00 per 1M tokens',
        'gpt-4o-2024-08-06' => 'GPT-4o (Aug 2024) - $2.50/$5.00 per 1M tokens',
        'gpt-4o-mini' => 'GPT-4o Mini - $0.15/$0.60 per 1M tokens',
        'gpt-4-turbo' => 'GPT-4 Turbo - $10.00/$30.00 per 1M tokens [Legacy]'
    ],
    'GPT-3.5 Series - Tiết kiệm' => [
        'gpt-3.5-turbo' => 'GPT-3.5 Turbo - $0.50/$1.50 per 1M tokens',
        'gpt-3.5-turbo-0125' => 'GPT-3.5 Turbo (0125) - $0.50/$1.50 per 1M tokens'
    ],
    'o1 Series - Lý luận phức tạp' => [
        'o1' => 'o1 - $15.00/$60.00 per 1M tokens',
        'o1-preview' => 'o1 Preview - $15.00/$60.00 per 1M tokens',
        'o1-mini' => 'o1 Mini - $3.00/$12.00 per 1M tokens'
    ]
];

// Kiểm tra cấu hình memory
$memory_enabled = $CMSNT->site('ai_memory_enabled') ?? '1';
$memory_status = $memory_enabled == '1' ? 'Memory ON' : 'Memory OFF';
$memory_color = $memory_enabled == '1' ? '#1cc88a' : '#858796';
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-2">
                <i class="ri-robot-line me-2"></i>AI Chat Assistant
            </h1>
            <div class="d-flex align-items-center gap-2">
                <div class="dropdown">
                    <button class="model-info btn btn-sm dropdown-toggle" type="button" id="modelDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="border: 1px solid #e3e6f0;">
                        <i class="ri-cpu-line"></i>
                        <span id="currentModelSpan"><?=$current_model;?></span>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="modelDropdown">
                        <?php foreach($available_models as $group => $models): ?>
                            <li><h6 class="dropdown-header"><?=__($group);?></h6></li>
                            <?php foreach($models as $value => $label): ?>
                                <li><a class="dropdown-item model-select-item" href="#" data-model="<?=$value;?>"><?=__($label);?></a></li>
                            <?php endforeach; ?>
                            <?php if(next($available_models)): ?>
                            <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div id="memoryToggle"
                    class="model-info memory-toggle <?=$memory_enabled == '1' ? 'memory-on' : 'memory-off';?>">
                    <i class="ri-brain-line"></i>
                    <span id="memoryStatus"><?=$memory_status;?></span>
                </div>
                <button id="clearChatBtn" class="btn btn-sm btn-danger clear-chat-btn">
                    <i class="ri-delete-bin-line me-1"></i><?=__('Xóa chat');?>
                </button>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">

                    <div class="card-body">
                        <!-- Chat Container -->
                        <div id="chatContainer" class="chat-container mb-3">
                            <!-- Load More Button -->
                            <div id="loadMoreContainer" class="load-more-container" style="display: none;">
                                <button id="loadMoreBtn" class="load-more-btn">
                                    <i class="ri-arrow-up-line me-1"></i><?=__('Tải thêm tin nhắn');?>
                                </button>
                            </div>

                            <!-- Chat Messages -->
                            <div id="chatMessages" class="chat-messages">
                                <!-- Welcome Message -->
                                <div class="chat-message ai">
                                    <div class="message-avatar ai"><i class="ri-robot-fill"></i></div>
                                    <div class="message-wrapper">
                                        <div class="message-content">
                                            <strong>🚀 Chào mừng bạn đến với AI Chat Assistant!</strong><br><br>

                                            <strong>📊 THỐNG KÊ & PHÂN TÍCH DỮ LIỆU:</strong><br>
                                            Tôi có thể trực tiếp truy vấn database và phân tích dữ liệu của bạn:<br><br>

                                            <strong>💰 Doanh thu:</strong> "doanh thu hôm nay", "lợi nhuận tháng
                                            này"<br>
                                            <strong>👥 Người dùng:</strong> "user nào nạp tiền nhiều nhất", "top 10
                                            khách hàng VIP"<br>
                                            <strong>📦 Đơn hàng:</strong> "đơn hàng hôm nay", "dịch vụ bán chạy
                                            nhất"<br>
                                            <strong>💳 Giao dịch:</strong> "lịch sử nạp tiền của User", "nhật ký số dư
                                            user abc"<br><br>

                                            <strong>🎯 TÍNH NĂNG KHÁC:</strong> Tư vấn kỹ thuật, debug code, chiến lược
                                            kinh doanh<br><br>

                                            <?php if ($memory_enabled == '1'): ?>
                                            <strong>🧠 Memory AI:</strong> Tôi sẽ nhớ 5 cuộc trò chuyện gần nhất của
                                            bạn.<br><br>
                                            <?php endif; ?>

                                            <strong>Hãy thử hỏi: "doanh thu hôm nay" hoặc "user nào nạp tiền nhiều
                                                nhất"</strong>
                                        </div>
                                        <div class="message-time"><?=date('H:i d/m/Y')?></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Chat Input -->
                        <div class="chat-input-container">
                            <textarea id="chatInput" class="form-control chat-input" placeholder="Nhập tin nhắn..."
                                rows="1"></textarea>
                            <button id="sendButton" class="send-button" type="button">
                                <i class="ri-send-plane-fill"></i>
                            </button>
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
class AIChatManager {
    constructor() {
        this.chatContainer = document.getElementById("chatContainer");
        this.chatMessages = document.getElementById("chatMessages");
        this.chatInput = document.getElementById("chatInput");
        this.sendButton = document.getElementById("sendButton");
        this.loadMoreBtn = document.getElementById("loadMoreBtn");
        this.loadMoreContainer = document.getElementById("loadMoreContainer");

        // typing indicator sẽ được tạo trong createTypingIndicator()
        this.typingIndicator = null;

        this.messageCount = 0;
        this.totalTokens = 0;
        this.isUserScrolling = false;
        this.currentPage = 1;
        this.messagesPerPage = 10;
        this.hasMoreMessages = true;
        this.isLoading = false;

        // Debug mode từ PHP
        this.debugMode = <?=DEBUG ? 'true' : 'false';?>;

        this.initEventListeners();
        this.loadInitialData();

        // Tạo typing indicator
        this.createTypingIndicator();

        // Đảm bảo input state ban đầu đúng
        this.setInputState(true);

        // Global reference for chart functions
        window.aiChat = this;

        // Auto scroll to bottom on page load
        setTimeout(() => {
            this.scrollToBottom(false);
        }, 300);

        // Auto focus vào input khi trang load xong
        setTimeout(() => {
            this.chatInput.focus();
        }, 500);
    }

    initEventListeners() {
        // Send message events
        this.sendButton.addEventListener("click", () => this.sendMessage());
        this.chatInput.addEventListener("keypress", (e) => {
            if (e.key === "Enter" && !e.shiftKey) {
                e.preventDefault();
                this.sendMessage();
            }
        });

        // Auto-resize textarea
        this.chatInput.addEventListener("input", () => {
            this.chatInput.style.height = "auto";
            this.chatInput.style.height = this.chatInput.scrollHeight + "px";
        });

        // Clear chat
        document.getElementById("clearChatBtn").addEventListener("click", () => {
            this.clearChat();
        });

        // Memory toggle
        document.getElementById("memoryToggle").addEventListener("click", () => {
            this.toggleMemory();
        });

        // Model selection
        document.querySelectorAll('.model-select-item').forEach(item => {
            item.addEventListener('click', (e) => {
                e.preventDefault();
                const selectedModel = e.target.getAttribute('data-model');
                this.changeModel(selectedModel);
            });
        });

        // Load more messages
        this.loadMoreBtn.addEventListener("click", () => {
            this.loadMoreMessages();
        });

        // Detect user scrolling
        this.chatContainer.addEventListener("scroll", () => {
            const {
                scrollTop,
                scrollHeight,
                clientHeight
            } = this.chatContainer;
            this.isUserScrolling = scrollTop < scrollHeight - clientHeight - 50;
        });
    }

    async sendMessage() {
        const message = this.chatInput.value.trim();
        if (!message) return;

        // Disable input
        this.setInputState(false);

        // Add user message
        this.addMessage("user", message);
        this.chatInput.value = "";
        this.chatInput.style.height = "auto";

        // Show typing indicator
        this.showTypingIndicator();

        try {
            const response = await this.callAIAPI(message);
            this.hideTypingIndicator();

            if (response.success) {
                let aiResponse = response.response;

                // Add AI message
                this.addMessage("ai", aiResponse);

                // Add debug info nếu có (ở ô riêng biệt) và debug mode được bật
                if (this.debugMode && response.debug_info) {
                    this.addDebugInfo(response.debug_info);
                }

                this.messageCount++;
                this.totalTokens += response.tokens || 0;
            } else {
                this.addMessage("ai", "Lỗi: " + response.message);
            }
        } catch (error) {
            this.hideTypingIndicator();
            this.addMessage("ai", "Có lỗi xảy ra khi kết nối với AI. Vui lòng thử lại.");
        }

        // Re-enable input
        this.setInputState(true);

        // Auto focus vào input để tiếp tục chat
        this.chatInput.focus();
    }

    async callAIAPI(message) {
        const formData = new FormData();
        formData.append("action", "send_message");
        formData.append("message", message);

        const response = await fetch("ajaxs/admin/ai-chat.php", {
            method: "POST",
            body: formData
        });

        return await response.json();
    }



    async loadInitialData() {
        try {
            const formData = new FormData();
            formData.append("action", "load_history");
            formData.append("page", "1");
            formData.append("limit", this.messagesPerPage.toString());

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            if (data.success && data.history) {
                data.history.forEach(chat => {
                    this.addMessage("user", chat.message, chat.created_at, false);
                    this.addMessage("ai", chat.response, chat.created_at, false);

                    // Add debug info nếu có trong history và debug mode được bật
                    if (this.debugMode && chat.debug_info) {
                        try {
                            const debugInfo = typeof chat.debug_info === 'string' ?
                                JSON.parse(chat.debug_info) : chat.debug_info;
                            this.addDebugInfo(debugInfo, false);
                        } catch (e) {
                            console.log('Error parsing debug info:', e);
                        }
                    }
                });
                this.messageCount = data.history.length;
                this.hasMoreMessages = data.has_more || false;
                this.updateLoadMoreButton();

                // Đảm bảo typing indicator ở cuối
                if (this.typingIndicator && this.typingIndicator.parentNode === this.chatMessages) {
                    this.chatMessages.appendChild(this.typingIndicator);
                }

                // Auto scroll to bottom after loading history
                setTimeout(() => {
                    this.scrollToBottom(false);
                }, 100);
            }
        } catch (error) {
            console.error("Error loading chat history:", error);
        }
    }

    async loadMoreMessages() {
        if (this.isLoading || !this.hasMoreMessages) return;

        this.isLoading = true;
        this.showLoadingSpinner();

        try {
            const formData = new FormData();
            formData.append("action", "load_history");
            formData.append("page", (this.currentPage + 1).toString());
            formData.append("limit", this.messagesPerPage.toString());

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            if (data.success && data.history) {
                const currentScrollHeight = this.chatContainer.scrollHeight;

                // Add messages at the beginning
                data.history.reverse().forEach(chat => {
                    this.prependMessage("user", chat.message, chat.created_at);
                    this.prependMessage("ai", chat.response, chat.created_at);

                    // Add debug info nếu có trong history (prepend) và debug mode được bật
                    if (this.debugMode && chat.debug_info) {
                        try {
                            const debugInfo = typeof chat.debug_info === 'string' ?
                                JSON.parse(chat.debug_info) : chat.debug_info;
                            this.prependDebugInfo(debugInfo);
                        } catch (e) {
                            console.log('Error parsing debug info:', e);
                        }
                    }
                });

                this.currentPage++;
                this.hasMoreMessages = data.has_more || false;
                this.messageCount += data.history.length;

                // Đảm bảo typing indicator ở cuối
                if (this.typingIndicator && this.typingIndicator.parentNode === this.chatMessages) {
                    this.chatMessages.appendChild(this.typingIndicator);
                }

                // Maintain scroll position
                const newScrollHeight = this.chatContainer.scrollHeight;
                this.chatContainer.scrollTop = newScrollHeight - currentScrollHeight;
            }
        } catch (error) {
            console.error("Error loading more messages:", error);
        } finally {
            this.isLoading = false;
            this.hideLoadingSpinner();
            this.updateLoadMoreButton();
        }
    }

    showLoadingSpinner() {
        this.loadMoreBtn.innerHTML = '<div class="loading-spinner"></div>Đang tải...';
        this.loadMoreBtn.disabled = true;
    }

    hideLoadingSpinner() {
        this.loadMoreBtn.innerHTML = '<i class="ri-arrow-up-line me-1"></i>Tải thêm tin nhắn';
        this.loadMoreBtn.disabled = false;
    }

    updateLoadMoreButton() {
        if (this.hasMoreMessages) {
            this.loadMoreContainer.style.display = 'block';
        } else {
            this.loadMoreContainer.style.display = 'none';
        }
    }

    addMessage(type, content, timestamp = null, autoScroll = true) {
        const messageDiv = this.createMessageElement(type, content, timestamp);

        // Insert before typing indicator if it exists
        if (this.typingIndicator && this.typingIndicator.parentNode === this.chatMessages) {
            this.chatMessages.insertBefore(messageDiv, this.typingIndicator);
        } else {
            this.chatMessages.appendChild(messageDiv);
        }

        if (autoScroll && !this.isUserScrolling) {
            this.scrollToBottom();
        }
    }

    addDebugInfo(debugInfo, autoScroll = true) {
        const debugDiv = document.createElement("div");
        debugDiv.className = "debug-info-box";

        let debugContent = `
            <div class="debug-title">
                <i class="ri-bug-line"></i>
                Debug Information
                <button class="debug-collapse" onclick="this.parentElement.parentElement.classList.toggle('collapsed')">
                    Thu gọn
                </button>
            </div>
            <div class="debug-content">
                <div class="debug-item">
                    <span class="debug-label">Detection:</span>
                    <span class="debug-value">${debugInfo.detection || 'N/A'}</span>
                </div>
                <div class="debug-item">
                    <span class="debug-label">Is DB Query:</span>
                    <span class="debug-value">${debugInfo.is_db_query ? 'YES' : 'NO'}</span>
                </div>
                <div class="debug-item">
                    <span class="debug-label">Tokens Used:</span>
                    <span class="debug-value">${debugInfo.tokens_used || 0}</span>
                </div>
        `;

        if (debugInfo.sql_generated) {
            debugContent += `
                <div class="debug-item">
                    <span class="debug-label">SQL Generated:</span>
                    <div class="debug-sql">${debugInfo.sql_generated}</div>
                </div>
            `;
        }

        if (debugInfo.rows_returned) {
            debugContent += `
                <div class="debug-item">
                    <span class="debug-label">Rows Returned:</span>
                    <span class="debug-value">${debugInfo.rows_returned}</span>
                </div>
            `;
        }

        debugContent += `
            </div>
        `;

        debugDiv.innerHTML = debugContent;

        // Insert before typing indicator if it exists
        if (this.typingIndicator && this.typingIndicator.parentNode === this.chatMessages) {
            this.chatMessages.insertBefore(debugDiv, this.typingIndicator);
        } else {
            this.chatMessages.appendChild(debugDiv);
        }

        if (autoScroll && !this.isUserScrolling) {
            this.scrollToBottom();
        }
    }

    prependMessage(type, content, timestamp = null) {
        const messageDiv = this.createMessageElement(type, content, timestamp);

        // Insert at beginning but before typing indicator position consideration
        const firstChild = this.chatMessages.firstChild;
        if (firstChild && firstChild.id === 'typingIndicator') {
            // If first child is typing indicator, insert after load more container
            const loadMore = this.chatMessages.querySelector('#loadMoreContainer');
            if (loadMore && loadMore.nextSibling) {
                this.chatMessages.insertBefore(messageDiv, loadMore.nextSibling);
            } else {
                this.chatMessages.insertBefore(messageDiv, firstChild);
            }
        } else {
            this.chatMessages.insertBefore(messageDiv, firstChild);
        }
    }

    createMessageElement(type, content, timestamp = null) {
        const messageDiv = document.createElement("div");
        messageDiv.className = `chat-message ${type}`;

        const avatarDiv = document.createElement("div");
        avatarDiv.className = `message-avatar ${type}`;
        avatarDiv.innerHTML = type === "user" ? '<i class="ri-user-fill"></i>' : '<i class="ri-robot-fill"></i>';

        const messageWrapper = document.createElement("div");
        messageWrapper.className = "message-wrapper";

        const contentDiv = document.createElement("div");
        contentDiv.className = "message-content";

        // Format content for AI messages (support markdown-like formatting)
        if (type === "ai") {
            contentDiv.innerHTML = this.formatAIMessage(content);
        } else {
            contentDiv.textContent = content;
        }

        const timeDiv = document.createElement("div");
        timeDiv.className = "message-time";
        timeDiv.textContent = timestamp ? new Date(timestamp).toLocaleString() : new Date().toLocaleString();

        messageWrapper.appendChild(contentDiv);
        messageWrapper.appendChild(timeDiv);

        messageDiv.appendChild(avatarDiv);
        messageDiv.appendChild(messageWrapper);

        return messageDiv;
    }

    formatAIMessage(content) {
        // Basic markdown-like formatting
        let formatted = content
            .replace(/\*\*(.*?)\*\*/g, "<strong>$1</strong>")
            .replace(/\*(.*?)\*/g, "<em>$1</em>")
            .replace(/`([^`]+)`/g, "<code>$1</code>")
            .replace(/\n/g, "<br>");

        // Format code blocks
        formatted = formatted.replace(/```([^```]+)```/g, "<pre><code>$1</code></pre>");

        // Simple formatting for better readability
        formatted = formatted.replace(/(=== .* ===)/g, '<div class="highlight-section">$1</div>');

        return formatted;
    }



    createTypingIndicator() {
        // Tạo typing indicator element
        const typingDiv = document.createElement("div");
        typingDiv.id = "typingIndicator";
        typingDiv.className = "chat-message ai";
        typingDiv.style.display = "none";

        typingDiv.innerHTML = `
            <div class="message-avatar ai"><i class="ri-robot-fill"></i></div>
            <div class="message-wrapper">
                <div class="typing-indicator">
                    <div class="typing-dots">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>
        `;

        // Append vào chatMessages
        this.chatMessages.appendChild(typingDiv);

        // Cập nhật reference
        this.typingIndicator = typingDiv;
    }

    showTypingIndicator() {
        if (!this.typingIndicator) {
            this.createTypingIndicator();
        }

        // Đảm bảo typing indicator ở cuối cùng
        if (this.typingIndicator.parentNode !== this.chatMessages) {
            this.chatMessages.appendChild(this.typingIndicator);
        }

        this.typingIndicator.classList.add("show");
        if (!this.isUserScrolling) {
            this.scrollToBottom();
        }
    }

    hideTypingIndicator() {
        if (this.typingIndicator) {
            this.typingIndicator.classList.remove("show");
        }
    }

    setInputState(enabled) {
        this.chatInput.disabled = !enabled;
        this.sendButton.disabled = !enabled;
        this.chatInput.placeholder = enabled ? "Nhập tin nhắn..." : "Đang gửi...";

        if (enabled) {
            this.chatInput.style.height = "auto";
        }
    }

    scrollToBottom(smooth = true) {
        if (smooth) {
            this.chatContainer.scrollTo({
                top: this.chatContainer.scrollHeight,
                behavior: "smooth"
            });
        } else {
            this.chatContainer.scrollTop = this.chatContainer.scrollHeight;
        }
    }



    async clearChat() {
        if (!confirm("Bạn có chắc muốn xóa toàn bộ lịch sử chat?")) return;

        try {
            const formData = new FormData();
            formData.append("action", "clear_history");

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            if (data.success) {
                this.chatMessages.innerHTML = "";
                this.messageCount = 0;
                this.totalTokens = 0;
                this.currentPage = 1;
                this.hasMoreMessages = false;
                this.updateLoadMoreButton();
                this.isUserScrolling = false;

                // Tạo lại typing indicator
                this.createTypingIndicator();

                // Add welcome message back
                this.addWelcomeMessage();

                // Auto focus vào input sau khi clear chat
                setTimeout(() => {
                    this.chatInput.focus();
                }, 300);

                cuteToast({
                    type: "success",
                    title: "Thành công",
                    message: "Đã xóa lịch sử chat",
                    timer: 3000
                });
            }
        } catch (error) {
            cuteToast({
                type: "error",
                title: "Lỗi",
                message: "Không thể xóa lịch sử chat",
                timer: 3000
            });
        }
    }

    async getMemoryStatus() {
        try {
            const formData = new FormData();
            formData.append("action", "get_memory_status");

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();
            return data;
        } catch (error) {
            console.error("Error getting memory status:", error);
            return {
                success: false,
                memory_enabled: '1',
                memory_status: 'Memory ON'
            };
        }
    }

    async toggleMemory() {
        const memoryToggle = document.getElementById("memoryToggle");
        const memoryStatus = document.getElementById("memoryStatus");

        // Disable button during request
        memoryToggle.classList.add("disabled");

        try {
            const formData = new FormData();
            formData.append("action", "toggle_memory");

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                // Update UI
                memoryStatus.textContent = data.memory_status;

                if (data.memory_enabled === '1') {
                    memoryToggle.classList.remove("memory-off");
                    memoryToggle.classList.add("memory-on");
                } else {
                    memoryToggle.classList.remove("memory-on");
                    memoryToggle.classList.add("memory-off");
                }

                cuteToast({
                    type: "success",
                    title: "Thành công",
                    message: data.message,
                    timer: 3000
                });
            } else {
                cuteToast({
                    type: "error",
                    title: "Lỗi",
                    message: data.message,
                    timer: 3000
                });
            }
        } catch (error) {
            console.error("Error toggling memory:", error);
            cuteToast({
                type: "error",
                title: "Lỗi",
                message: "Không thể thay đổi trạng thái Memory",
                timer: 3000
            });
        } finally {
            // Re-enable button
            memoryToggle.classList.remove("disabled");
        }
    }

    async addWelcomeMessage() {
        const memoryStatus = await this.getMemoryStatus();
        const memoryText = memoryStatus.memory_enabled === '1' ?
            '<strong>🧠 Memory AI:</strong> Tôi sẽ nhớ 5 cuộc trò chuyện gần nhất của bạn để đưa ra câu trả lời phù hợp với ngữ cảnh.<br><br>' :
            '';

        const welcomeHTML = `
            <div class="chat-message ai">
                <div class="message-avatar ai"><i class="ri-robot-fill"></i></div>
                <div class="message-wrapper">
                    <div class="message-content">
                        <strong>🚀 Chào mừng bạn đến với AI Chat Assistant!</strong><br><br>
                        
                        <strong>📊 THỐNG KÊ & PHÂN TÍCH DỮ LIỆU:</strong><br>
                        Tôi có thể trực tiếp truy vấn database và phân tích dữ liệu của bạn:<br><br>
                        
                        <strong>💰 Doanh thu:</strong> "doanh thu hôm nay", "lợi nhuận tháng này"<br>
                        <strong>👥 Người dùng:</strong> "user nào nạp tiền nhiều nhất", "top 10 khách hàng VIP"<br>
                        <strong>📦 Đơn hàng:</strong> "đơn hàng hôm nay", "dịch vụ bán chạy nhất"<br>
                        <strong>💳 Giao dịch:</strong> "lịch sử nạp tiền của 0123456789", "nhật ký số dư user abc"<br><br>
                        
                        <strong>🎯 TÍNH NĂNG KHÁC:</strong> Tư vấn kỹ thuật, debug code, chiến lược kinh doanh<br><br>
                        
                        ${memoryText}
                        <strong>Hãy thử hỏi: "doanh thu hôm nay" hoặc "user nào nạp tiền nhiều nhất"</strong>
                    </div>
                    <div class="message-time">${new Date().toLocaleString()}</div>
                </div>
            </div>
        `;
        this.chatMessages.innerHTML = welcomeHTML;

        // Tạo lại typing indicator sau khi set innerHTML
        this.createTypingIndicator();

        setTimeout(() => {
            this.scrollToBottom(false);
            // Auto focus vào input
            this.chatInput.focus();
        }, 100);
    }

    prependDebugInfo(debugInfo) {
        const debugDiv = document.createElement("div");
        debugDiv.className = "debug-info-box";

        let debugContent = `
            <div class="debug-title">
                <i class="ri-bug-line"></i>
                Debug Information
                <button class="debug-collapse" onclick="this.parentElement.parentElement.classList.toggle('collapsed')">
                    Thu gọn
                </button>
            </div>
            <div class="debug-content">
                <div class="debug-item">
                    <span class="debug-label">Detection:</span>
                    <span class="debug-value">${debugInfo.detection || 'N/A'}</span>
                </div>
                <div class="debug-item">
                    <span class="debug-label">Is DB Query:</span>
                    <span class="debug-value">${debugInfo.is_db_query ? 'YES' : 'NO'}</span>
                </div>
                <div class="debug-item">
                    <span class="debug-label">Tokens Used:</span>
                    <span class="debug-value">${debugInfo.tokens_used || 0}</span>
                </div>
        `;

        if (debugInfo.sql_generated) {
            debugContent += `
                <div class="debug-item">
                    <span class="debug-label">SQL Generated:</span>
                    <div class="debug-sql">${debugInfo.sql_generated}</div>
                </div>
            `;
        }

        if (debugInfo.rows_returned) {
            debugContent += `
                <div class="debug-item">
                    <span class="debug-label">Rows Returned:</span>
                    <span class="debug-value">${debugInfo.rows_returned}</span>
                </div>
            `;
        }

        debugContent += `
            </div>
        `;

        debugDiv.innerHTML = debugContent;

        // Insert at beginning but before typing indicator position consideration
        const firstChild = this.chatMessages.firstChild;
        if (firstChild && firstChild.id === 'typingIndicator') {
            // If first child is typing indicator, insert after load more container
            const loadMore = this.chatMessages.querySelector('#loadMoreContainer');
            if (loadMore && loadMore.nextSibling) {
                this.chatMessages.insertBefore(debugDiv, loadMore.nextSibling);
            } else {
                this.chatMessages.insertBefore(debugDiv, firstChild);
            }
        } else {
            this.chatMessages.insertBefore(debugDiv, firstChild);
        }
    }

    async changeModel(newModel) {
        try {
            const formData = new FormData();
            formData.append("action", "change_model");
            formData.append("model", newModel);

            const response = await fetch("ajaxs/admin/ai-chat.php", {
                method: "POST",
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                document.getElementById('currentModelSpan').textContent = data.new_model;
                cuteToast({
                    type: "success",
                    title: "Thành công",
                    message: data.message,
                    timer: 3000
                });
            } else {
                 cuteToast({
                    type: "error",
                    title: "Lỗi",
                    message: data.message,
                    timer: 3000
                });
            }
        } catch (error) {
             cuteToast({
                type: "error",
                title: "Lỗi",
                message: "Không thể thay đổi model AI. Vui lòng thử lại.",
                timer: 3000
            });
        }
    }
}

// Initialize chat when DOM is loaded
document.addEventListener("DOMContentLoaded", function() {
    new AIChatManager();
});
</script>