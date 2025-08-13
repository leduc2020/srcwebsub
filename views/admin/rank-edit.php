<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Chỉnh sửa cấp bậc'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<!-- CodeMirror CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/theme/monokai.min.css">
<style>
    .CodeMirror {
        border: 1px solid #ddd;
        border-radius: 6px;
        font-family: "Fira Code", "Consolas", "Monaco", monospace;
        font-size: 14px;
        line-height: 1.5;
    }
    .CodeMirror-focused {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
    }
</style>
';
$body['footer'] = '
 <!-- CodeMirror JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/codemirror.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/xml/xml.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/mode/htmlmixed/htmlmixed.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/closetag.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/edit/matchtags.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/fold/foldcode.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/fold/foldgutter.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.13/addon/fold/xml-fold.min.js"></script>
';
require_once(__DIR__.'/../../models/is_admin.php');
if (isset($_GET['id'])) {
    $id = check_string($_GET['id']);
    $row = $CMSNT->get_row("SELECT * FROM `ranks` WHERE `id` = '$id' ");
    if (!$row) {
        redirect(base_url_admin('ranks'));
    }
} else {
    redirect(base_url_admin('ranks'));
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
if(checkPermission($getUser['admin'], 'edit_rank') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>
<?php
if (isset($_POST['SaveRank'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('Chức năng này không thể sử dụng vì đây là trang web demo').'")){window.history.back().location.reload();}</script>');
    }
    if(empty($_POST['min'])){
        die('<script type="text/javascript">if(!alert("'.__('Tổng nạp không hợp lệ').'")){window.history.back().location.reload();}</script>');
    }
    $min = check_string($_POST['min']);
    if($min <= 0){
        die('<script type="text/javascript">if(!alert("'.__('Tổng nạp không hợp lệ').'")){window.history.back().location.reload();}</script>');
    }
    //
    if(empty($_POST['name'])){
        die('<script type="text/javascript">if(!alert("'.__('Vui lòng nhập tên cấp bậc').'")){window.history.back().location.reload();}</script>');
    }
    $name = check_string($_POST['name']);
    // Lọc detail: giữ HTML nhưng loại bỏ script và các thẻ nguy hiểm
    $detail = trim($_POST['detail']);
    $detail = strip_tags($detail, '<h1><h2><h3><h4><h5><h6><p><br><strong><b><em><i><u><ul><ol><li><div><span><a><img><table><tr><td><th><thead><tbody><tfoot><blockquote><code><pre><small><mark><del><ins><sub><sup><hr>');
    // Loại bỏ các thuộc tính nguy hiểm nhưng giữ class và style cơ bản
    $detail = preg_replace('/on[a-z]+\s*=\s*["\'][^"\']*["\']/i', '', $detail); // Loại bỏ onclick, onload, etc.
    $detail = preg_replace('/javascript:/i', '', $detail); // Loại bỏ javascript: trong href
    $status = check_string($_POST['status']);
    //
    $isUpdate = $CMSNT->update("ranks", [
        'name'          => $name,
        'detail'        => $detail,
        'min'           => $min,
        'status'        => $status,
        'updated_at'    => gettime()
    ], " `id` = '$id' ");
    if ($isUpdate) {
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => "Edit Rank (".$row['name'].")."
        ]);
        /** NOTE ACTION */
        $my_text = $CMSNT->site('noti_action');
        $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
        $my_text = str_replace('{username}', $getUser['username'], $my_text);
        $my_text = str_replace('{action}', "Edit Rank (".$row['name'].").", $my_text);
        $my_text = str_replace('{ip}', myip(), $my_text);    
        $my_text = str_replace('{time}', gettime(), $my_text);
        sendMessAdmin($my_text);
        die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.history.back().location.reload();}</script>');
    } else {
        die('<script type="text/javascript">if(!alert("'.__('Lưu thất bại!').'")){window.history.back().location.reload();}</script>');
    }
}
?>


<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><i class="fa-solid fa-edit"></i> <?=__('Chỉnh sửa cấp bậc');?>
                '<b style="color:red;"><?=$row['name'];?></b>'</h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?=base_url_admin('ranks');?>"><?=__('Cấp bậc');?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?=__('Chỉnh sửa cấp bậc');?></li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('CHỈNH SỬA CẤP BẬC');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email"><?=__('Tên cấp bậc');?>
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" name="name" value="<?=$row['name'];?>"
                                            required>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label"
                                    for="detail_field"><?=__('Chi tiết cấp bậc');?></label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <textarea class="form-control" id="detail_field" name="detail" rows="8"
                                            placeholder="<?=__('Nhập mô tả chi tiết về cấp bậc này, các ưu đãi và quyền lợi... Hỗ trợ HTML!');?>"><?=$row['detail'];?></textarea>
                                    </div>
                                    <small class="text-muted mt-2">
                                        <i class="ri-information-line me-1"></i>
                                        <?=__('Hỗ trợ HTML an toàn. Các thẻ được phép: h1-h6, p, br, strong, b, em, i, u, ul, ol, li, div, span, a, img, table, tr, td, th, code, pre, small, mark, hr');?>
                                        <br><strong><?=__('Ví dụ:');?></strong>
                                        <code>&lt;h5&gt;Tiêu đề&lt;/h5&gt; &lt;ul&gt;&lt;li&gt;Mục 1&lt;/li&gt;&lt;/ul&gt; &lt;strong class="text-success"&gt;VIP&lt;/strong&gt;</code>
                                    </small>

                                    <!-- Preview Section -->
                                    <div class="mt-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <button type="button" class="btn btn-sm btn-outline-info"
                                                onclick="togglePreview()">
                                                <i class="ri-eye-off-line me-1"></i><?=__('Ẩn xem trước');?>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-success ms-2"
                                                onclick="insertDefaultHTML()">
                                                <i class="ri-code-s-slash-line me-1"></i><?=__('HTML mặc định');?>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-dark ms-2"
                                                onclick="openAIModal()">
                                                <i class="ri-magic-line me-1"></i><?=__('Generated by AI');?>
                                            </button>
                                        </div>
                                        <div id="detail_preview" class="border rounded p-3 bg-light"
                                            style="display: block; line-height: 1.7;">
                                            <em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email"><?=__('Tổng nạp');?>
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <div class="input-group">
                                        <input type="text" class="form-control" value="<?=$row['min'];?>" name="min"
                                            required>
                                        <span class="input-group-text">
                                            <?=currencyDefault();?>
                                        </span>
                                    </div>
                                    <small><?=__('Tổng nạp tối thiểu để áp dụng cấp bậc');?></small>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <label class="col-sm-4 col-form-label" for="example-hf-email"><?=__('Trạng thái');?>
                                    (<span class="text-danger">*</span>)</label>
                                <div class="col-sm-8">
                                    <select class="form-control" name="status" required>
                                        <option value="0" <?=$row['status'] == 0 ? 'selected' : '';?>><?=__('Ẩn');?>
                                        </option>
                                        <option value="1" <?=$row['status'] == 1 ? 'selected' : '';?>>
                                            <?=__('Hiển thị');?></option>
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-4">
                                <div class="col-sm-8 offset-sm-4">
                                    <a type="button" class="btn btn-danger shadow-danger btn-wave"
                                        href="<?=base_url_admin('ranks');?>"><i class="fa fa-fw fa-undo me-1"></i>
                                        <?=__('Quay lại');?></a>
                                    <button type="submit" name="SaveRank"
                                        class="btn btn-primary shadow-primary btn-wave"><i
                                            class="fa fa-fw fa-save me-1"></i> <?=__('Lưu');?></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal AI Generate -->
<div class="modal fade" id="aiGenerateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg"
            style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px;">
            <!-- Header với gradient -->
            <div class="modal-header border-0 text-white pb-2">
                <div class="d-flex align-items-center">
                    <div class="me-3 p-2 rounded-circle" style="background: rgba(255,255,255,0.2);">
                        <i class="ri-robot-line" style="font-size: 24px;"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 fw-bold"><?=__('AI Generator');?></h5>
                        <small class="opacity-75"><?=__('Tạo nội dung thông minh');?></small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body với background trắng -->
            <div class="modal-body bg-white m-3 rounded-4 shadow-sm">
                <div class="text-center mb-3">
                    <div class="d-inline-flex align-items-center justify-content-center mb-2"
                        style="width: 60px; height: 60px; background: linear-gradient(45deg, #667eea, #764ba2); border-radius: 50%;">
                        <i class="ri-magic-line text-white" style="font-size: 24px;"></i>
                    </div>
                    <p class="text-muted mb-0"><?=__('Mô tả ưu đãi bạn muốn tạo');?></p>
                </div>

                <textarea class="form-control border-2" id="aiDescription" rows="3"
                    placeholder="VD: Hỗ trợ riêng 24/7, Giảm giá 20%, Website con miễn phí..."
                    style="border-color: #667eea; border-radius: 12px; resize: none;"></textarea>
            </div>

            <!-- Footer với nút gradient -->
            <div class="modal-footer border-0 pt-0 px-4 pb-4">
                <button type="button" class="btn btn-light me-2" data-bs-dismiss="modal" style="border-radius: 25px;">
                    <i class="ri-close-line me-1"></i><?=__('Hủy');?>
                </button>
                <button type="button" class="btn text-white fw-bold px-4" onclick="generateAIContent()" id="generateBtn"
                    style="background: linear-gradient(45deg, #667eea, #764ba2); border: none; border-radius: 25px; min-width: 140px;">
                    <i class="ri-magic-line me-1"></i><?=__('Tạo ngay');?>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>



<script>
function togglePreview() {
    const preview = document.getElementById('detail_preview');
    const detailField = document.getElementById('detail_field');
    const btn = event.target.closest('button');

    if (preview.style.display === 'none') {
        // Show preview
        const content = detailField.value;
        if (content.trim() === '') {
            preview.innerHTML = '<em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>';
        } else {
            // Check if content contains HTML tags
            if (content.includes('<') && content.includes('>')) {
                // Content has HTML tags, display as HTML
                preview.innerHTML = content;
            } else {
                // Plain text content, convert line breaks to <br>
                const formattedContent = content.replace(/\n/g, '<br>');
                preview.innerHTML = formattedContent;
            }
        }
        preview.style.display = 'block';
        btn.innerHTML = '<i class="ri-eye-off-line me-1"></i><?=__('Ẩn xem trước');?>';
    } else {
        // Hide preview
        preview.style.display = 'none';
        btn.innerHTML = '<i class="ri-eye-line me-1"></i><?=__('Xem trước');?>';
    }
}

// Auto-update preview when typing
document.getElementById('detail_field').addEventListener('input', function() {
    const preview = document.getElementById('detail_preview');
    if (preview.style.display !== 'none') {
        const content = this.value;
        if (content.trim() === '') {
            preview.innerHTML = '<em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>';
        } else {
            // Check if content contains HTML tags
            if (content.includes('<') && content.includes('>')) {
                // Content has HTML tags, display as HTML
                preview.innerHTML = content;
            } else {
                // Plain text content, convert line breaks to <br>
                const formattedContent = content.replace(/\n/g, '<br>');
                preview.innerHTML = formattedContent;
            }
        }
    }
});
</script>



<script>
// Khởi tạo CodeMirror cho textarea detail
var detailEditor = CodeMirror.fromTextArea(document.getElementById('detail_field'), {
    mode: "htmlmixed",
    theme: "monokai", // Sử dụng theme default thay vì monokai cho sáng hơn
    lineNumbers: true,
    lineWrapping: true,
    autoCloseTags: true,
    matchTags: true,
    indentUnit: 2,
    tabSize: 2,
    foldGutter: true,
    gutters: ["CodeMirror-linenumbers", "CodeMirror-foldgutter"],
    extraKeys: {
        "Ctrl-Space": "autocomplete",
        "Tab": function(cm) {
            if (cm.somethingSelected()) {
                cm.indentSelection("add");
            } else {
                cm.replaceSelection("  ", "end");
            }
        }
    },
    placeholder: "Nhập HTML cho chi tiết cấp bậc...\n\nVí dụ:\n<h5>Ưu đãi đặc biệt</h5>\n<ul>\n  <li><strong>Giảm 20%</strong> tất cả dịch vụ</li>\n  <li>Website con miễn phí</li>\n</ul>"
});

// Cập nhật preview với nội dung hiện có khi load trang
setTimeout(function() {
    var preview = document.getElementById('detail_preview');
    var content = detailEditor.getValue();
    if (content.trim() === '') {
        preview.innerHTML = '<em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>';
    } else {
        // Check if content contains HTML tags
        if (content.includes('<') && content.includes('>')) {
            // Content has HTML tags, display as HTML
            preview.innerHTML = content;
        } else {
            // Plain text content, convert line breaks to <br>
            const formattedContent = content.replace(/\n/g, '<br>');
            preview.innerHTML = formattedContent;
        }
    }
}, 100);

// Cập nhật preview khi thay đổi trong CodeMirror
detailEditor.on('change', function() {
    var preview = document.getElementById('detail_preview');
    if (preview.style.display !== 'none' && preview.style.display !== '') {
        var content = detailEditor.getValue();
        if (content.trim() === '') {
            preview.innerHTML = '<em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>';
        } else {
            // Check if content contains HTML tags
            if (content.includes('<') && content.includes('>')) {
                // Content has HTML tags, display as HTML
                preview.innerHTML = content;
            } else {
                // Plain text content, convert line breaks to <br>
                const formattedContent = content.replace(/\n/g, '<br>');
                preview.innerHTML = formattedContent;
            }
        }
    }
});

// Cập nhật togglePreview function để hoạt động với CodeMirror
function togglePreview() {
    const preview = document.getElementById('detail_preview');
    const btn = event.target.closest('button');

    if (preview.style.display === 'none' || preview.style.display === '') {
        // Show preview
        const content = detailEditor.getValue();
        if (content.trim() === '') {
            preview.innerHTML = '<em class="text-muted"><?=__('Chưa có nội dung để xem trước');?></em>';
        } else {
            // Check if content contains HTML tags
            if (content.includes('<') && content.includes('>')) {
                // Content has HTML tags, display as HTML
                preview.innerHTML = content;
            } else {
                // Plain text content, convert line breaks to <br>
                const formattedContent = content.replace(/\n/g, '<br>');
                preview.innerHTML = formattedContent;
            }
        }
        preview.style.display = 'block';
        btn.innerHTML = '<i class="ri-eye-off-line me-1"></i><?=__('Ẩn xem trước');?>';
    } else {
        // Hide preview
        preview.style.display = 'none';
        btn.innerHTML = '<i class="ri-eye-line me-1"></i><?=__('Xem trước');?>';
    }
}


// Function để chèn HTML template mặc định  
function insertDefaultHTML() {
    const defaultHTML = `<div class="benefit-item d-flex align-items-center mb-3 p-2 rounded-3" style="background: rgba(108, 117, 125, 0.08);"> 
  <div class="benefit-icon me-3" style="width: 32px; height: 32px; background: linear-gradient(135deg, #fd6e14, #ff5107); border-radius: 8px; display: flex; align-items: center; justify-content: center;">
    <i class="ri-checkbox-circle-fill text-white" style="font-size: 14px;"></i>
  </div>
  <div>
    <div class="fw-medium" style="font-size: 13px;">Hỗ trợ tạo website con</div>
    <small class="text-muted">Chi phí gia hạn 100.000đ 1 tháng</small>
  </div>
</div>`;

    // Lấy nội dung hiện tại và thêm template mới vào cuối
    const currentContent = detailEditor.getValue();
    const newContent = currentContent + (currentContent ? '\n\n' : '') + defaultHTML;
    detailEditor.setValue(newContent);

    // Focus vào editor và đặt cursor ở cuối
    detailEditor.focus();
    detailEditor.setCursor(detailEditor.lineCount(), 0);
    showMessage("<?=__('Đã thay thế bằng HTML template mặc định');?>", 'success');
}

// Hàm mở modal AI
function openAIModal() {
    document.getElementById('aiDescription').value = '';
    var modal = new bootstrap.Modal(document.getElementById('aiGenerateModal'));
    modal.show();
}

// Hàm tạo nội dung AI
function generateAIContent() {
    const description = document.getElementById('aiDescription').value.trim();
    const generateBtn = document.getElementById('generateBtn');

    if (description === '') {
        toastr.error('<?=__('Vui lòng nhập mô tả về cấp bậc');?>');
        return;
    }

    // Disable button và show loading
    generateBtn.disabled = true;
    generateBtn.innerHTML = '<i class="me-1 spinner-border spinner-border-sm"></i><?=__('Đang tạo...');?>';
    generateBtn.style.background = 'linear-gradient(45deg, #9ca3af, #6b7280)';
    generateBtn.style.minWidth = '140px';

    // Gọi AJAX
    $.ajax({
        url: '<?=base_url('ajaxs/admin/ai.php');?>',
        type: 'POST',
        dataType: 'json',
        data: {
            action: 'generateHTMLdetail',
            description: description
        },
        success: function(response) {
            if (response.success) {
                // Thêm nội dung AI vào cuối nội dung hiện tại
                const currentContent = detailEditor.getValue();
                const newContent = currentContent + (currentContent ? '\n\n' : '') + response.content;
                detailEditor.setValue(newContent);

                // Focus vào editor và đặt cursor ở cuối
                detailEditor.focus();
                detailEditor.setCursor(detailEditor.lineCount(), 0);

                // Đóng modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('aiGenerateModal'));
                modal.hide();

                showMessage('<?=__('Tạo nội dung thành công!');?>', 'success');
            } else {
                showMessage(response.message || '<?=__('Có lỗi xảy ra');?>', 'error');
            }
        },
        error: function(xhr, status, error) {
            console.error('AJAX Error:', error);
            showMessage('<?=__('Có lỗi xảy ra khi kết nối đến server');?>', 'error');
        },
        complete: function() {
            // Enable button
            generateBtn.disabled = false;
            generateBtn.innerHTML = '<i class="ri-magic-line me-1"></i><?=__('Tạo ngay');?>';
            generateBtn.style.background = 'linear-gradient(45deg, #667eea, #764ba2)';
            generateBtn.style.minWidth = '140px';
        }
    });
}
</script>