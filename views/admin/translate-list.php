<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Translate'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<link rel="stylesheet" href="https://cdn.datatables.net/1.12.1/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.bootstrap.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.3/css/buttons.bootstrap5.min.css">
<style>
/* Modal max_input_vars warning styles */
.bg-warning-transparent {
    background-color: rgba(255, 193, 7, 0.1);
}
.bg-info-transparent {
    background-color: rgba(13, 202, 240, 0.1);
}
.accordion-button {
    font-weight: 500;
}
.accordion-button:not(.collapsed) {
    background-color: rgba(13, 110, 253, 0.1);
}
.fs-11 {
    font-size: 0.7rem;
}
#inputVarsLimitModal .modal-lg {
    max-width: 800px;
}
#inputVarsLimitModal .card {
    transition: all 0.3s ease;
}
#inputVarsLimitModal .card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* Button groups styling để hiển thị đều nhau */
.uniform-btn {
    min-width: 150px;
    text-align: center;
    white-space: nowrap;
    padding: 0.375rem 0.75rem;
}

/* Filter buttons group */
.filter-buttons-group {
    position: relative;
    display: inline-block;
}

/* Bulk actions styling */
#bulk-actions {
    display: none !important; /* Mặc định ẩn */
}

#bulk-actions.show {
    display: flex !important; /* Hiển thị khi có class show */
}

#bulk-actions .btn {
    min-width: 140px;
    text-align: center;
}

/* Card header buttons container */
.card-header .d-flex {
    align-items: center;
    justify-content: flex-end;
}

/* Responsive cho mobile */
@media (max-width: 768px) {
    .uniform-btn {
        min-width: 120px;
        font-size: 0.75rem;
    }
}
</style>
';
$body['footer'] = '
<script src="https://cdn.datatables.net/1.12.1/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.12.1/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/responsive/2.3.0/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.2.3/js/buttons.print.min.js"></script>
';
require_once(__DIR__.'/../../models/is_admin.php');
if (isset($_GET['id'])) {
    $id = check_string($_GET['id']);
    $row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' ");
    if (!$row) {
        redirect(base_url_admin('language-list'));
    }
} else {
    redirect(base_url_admin('language-list'));
}
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
if(checkPermission($getUser['admin'], 'edit_lang') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}
?>
<?php
if (isset($_POST['addTranslate'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('Không được dùng chức năng này vì đây là trang web demo.').'")){window.history.back().location.reload();}</script>');
    }
    foreach ($CMSNT->get_list("SELECT * FROM `languages` WHERE `id` != '".$row['id']."' ") as $lang) {
        if ($CMSNT->num_rows("SELECT * FROM `translate` WHERE `name` = '".check_string($_POST['name'])."' AND `lang_id` = '".$lang['id']."'  ") < 1) {
            $CMSNT->insert("translate", [
                'value' => check_string($_POST['name']),
                'name'  => check_string($_POST['name']),
                'lang_id'   => $lang['id']
            ]);
        }
    }
    if ($CMSNT->num_rows("SELECT * FROM `translate` WHERE `name` = '".check_string($_POST['name'])."' AND `lang_id` = '".$row['id']."' ") < 1) {
        $isInsert = $CMSNT->insert("translate", [
            'value' => check_string($_POST['value']),
            'name'  => check_string($_POST['name']),
            'lang_id'   => $row['id']
        ]);
    }
    if (isset($isInsert)) {
        $CMSNT->insert("logs", [
            'user_id'       => $getUser['id'],
            'ip'            => myip(),
            'device'        => getUserAgent(),
            'createdate'    => gettime(),
            'action'        => "Add Translate (".check_string($_POST['value']).")."
        ]);
       die('<script type="text/javascript">window.location="'.base_url_admin('translate-list&id='.$id).'";</script>');
    } else {
        $CMSNT->update("translate", [
            'value' => check_string($_POST['value']),
            'name'  => check_string($_POST['name']),
            'lang_id'   => $row['id']
        ], " `name` = '".check_string($_POST['name'])."' AND `lang_id` = '".$row['id']."'  ");
        die('<script type="text/javascript">window.location="'.base_url_admin('translate-list&id='.$id).'";</script>');
    }
}


?>

 

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><?=__('Translates');?> <?=$row['lang'];?></h1>
            <div class="ms-md-1 ms-0">
                <nav>
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="<?=base_url_admin('language-list');?>"><?=__('Languages');?></a></li>
                        <li class="breadcrumb-item"><a href="#"><?=$row['lang'];?></a></li>
                        <li class="breadcrumb-item active" aria-current="page"><?=__('Translates');?></li>
                    </ol>
                </nav>
            </div>
        </div>

        <div class="row">
            <div class="col-xl-7">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('THÊM NỘI DUNG');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group mb-3">
                                <label><?=__('Default');?>:</label>
                                <textarea class="form-control" name="name"
                                    placeholder="<?=__('Nhập nội dung mặc định');?>" required></textarea>
                            </div>
                            <div class="form-group mb-3">
                                <label><?=$row['lang'];?>:</label>
                                <textarea class="form-control" name="value" placeholder="<?=__('Nhập nội dung cần dịch');?>"
                                    required></textarea>
                            </div>
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" name="addTranslate" class="btn btn-primary btn-wave"><?=__('THÊM NGAY');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('LƯU Ý');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <p><?=__('Hệ thống tự động cập nhật nội dung mới khi nội dung bạn thêm vào bị trùng lặp.');?></p>
                        <p><?=__('Quý khách có thể sử dụng tính năng này để thay đổi nội dung trên website.');?></p>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('TRANSLATES');?> <span id="filter-info" class="badge bg-secondary ms-2" style="display: none;"></span>
                        </div>
                        <div class="d-flex gap-2 flex-wrap">
                            <!-- Bulk Action Buttons -->
                            <div id="bulk-actions" class="d-flex gap-2">
                                <button type="button" id="bulk-translate-btn" class="btn btn-sm btn-info btn-wave flex-fill">
                                    <i class="fa-solid fa-language"></i> <?=__('Dịch tự động tất cả');?>
                                </button>
                                <button type="button" id="bulk-delete-btn" class="btn btn-sm btn-danger btn-wave flex-fill">
                                    <i class='bx bx-trash'></i> <?=__('Xóa tất cả');?>
                                </button>
                            </div>
                            
                            <!-- Filter Buttons -->
                            <div class="filter-buttons-group">
                                <button type="button" id="filter-untranslated-btn" class="btn btn-sm btn-warning btn-wave uniform-btn">
                                    <i class="fa-solid fa-filter"></i> <?=__('Chưa dịch');?>
                                </button>
                                <button type="button" id="filter-all-btn" class="btn btn-sm btn-secondary btn-wave uniform-btn" style="display: none;">
                                    <i class="fa-solid fa-list"></i> <?=__('Tất cả');?>
                                </button>
                            </div>
                            
                            <!-- Original Buttons -->
                            <button type="button" data-bs-toggle="modal" data-bs-target="#exampleModalScrollable2"
                                class="btn btn-sm btn-primary btn-wave waves-light waves-effect waves-light uniform-btn"><i
                                    class='bx bx-reset'></i> <?=__('Tạo lại bản dịch');?></button>
                            <button type="button" data-bs-toggle="modal" data-bs-target="#syncTranslateModal"
                                class="btn btn-sm btn-success btn-wave waves-light waves-effect waves-light uniform-btn"><i
                                    class='bx bx-sync'></i> <?=__('Đồng bộ bản dịch');?></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="datatable-basic" class="table table-striped table-hover table-bordered"
                            style="width:100%">
                            <thead>
                                <tr>
                                    <th width="3%"><input type="checkbox" id="select-all-checkbox" class="form-check-input"></th>
                                    <th><?=__('Default');?></th>
                                    <th><?=$row['lang'];?></th>
                                    <th width="20%"><?=__('Action');?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Data will be loaded via AJAX -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php
require_once(__DIR__.'/footer.php');
?>


<div class="modal fade" id="exampleModalScrollable2" tabindex="-1" aria-labelledby="exampleModalScrollable2Label" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary-transparent">
                <h5 class="modal-title text-primary" id="exampleModalScrollable2Label">
                    <i class="fa-solid fa-refresh me-2"></i>
                    <?=__('Tạo lại bản dịch');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-info">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-info-circle text-info me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Thông tin quan trọng!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ không thể hoàn tác.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện các hành động sau:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-trash text-danger me-2"></i>
                            <?=__('Xóa tất cả bản dịch hiện tại của ngôn ngữ');?> <strong class="text-primary"><?=$row['lang'];?></strong>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-copy text-success me-2"></i>
                            <?=__('Sao chép nội dung từ ngôn ngữ mặc định');?> <strong class="text-success"><?=$CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1")['lang'];?></strong>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-refresh text-info me-2"></i>
                            <?=__('Cập nhật toàn bộ danh sách bản dịch mới');?>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmUpdateTranslateCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmUpdateTranslateCheckbox">
                        <?=__('Tôi hiểu rủi ro và đồng ý tạo lại bản dịch từ ngôn ngữ mặc định');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" id="btnUpdateTranslate" class="btn btn-primary btn-wave" onclick="updateTranslateFromDefault()" disabled>
                    <i class="fa fa-refresh me-1"></i><?=__('Tạo lại bản dịch');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal đồng bộ bản dịch -->
<div class="modal fade" id="syncTranslateModal" tabindex="-1" aria-labelledby="syncTranslateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-warning-transparent">
                <h5 class="modal-title text-warning" id="syncTranslateModalLabel">
                    <i class="fa-solid fa-sync me-2"></i>
                    <?=__('Đồng bộ bản dịch từ lang.php');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-warning">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-exclamation-triangle text-warning me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Cảnh báo quan trọng!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ không thể hoàn tác.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện các hành động sau:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-download text-success me-2"></i>
                            <?=__('Đồng bộ nội dung mà ngôn ngữ này chưa có từ file');?> <code class="bg-light px-2 py-1 rounded">lang.php</code>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmSyncTranslateCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmSyncTranslateCheckbox">
                        <?=__('Tôi hiểu rủi ro và đồng ý đồng bộ bản dịch từ lang.php');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" id="btnSyncTranslate" class="btn btn-success btn-wave" onclick="syncTranslateFromLangFile()" disabled>
                    <i class="fa fa-sync me-1"></i><?=__('Đồng bộ bản dịch');?>
                </button>
            </div>
        </div>
    </div>
</div>


<script>
/**
 * Hàm gọi API dịch tự động và hiển thị hiệu ứng spinner trên nút khi đang thực hiện.
 * @param {string} id - ID của bản ghi để cập nhật nội dung dịch vào textarea tương ứng.
 * @param {string} defaultText - Văn bản cần dịch (nội dung mặc định).
 * @param {string} targetLang - Mã ngôn ngữ đích (ví dụ: 'en' hoặc 'vi').
 * @param {HTMLElement} btn - Nút được click để hiển thị hiệu ứng spinner.
 */
function autoTranslate(id, defaultText, targetLang, btn) {
    // Kiểm tra nếu targetLang trống
    if (!targetLang || targetLang.trim() === "") {
        alert("<?=__('Vui lòng cập nhật ISO CODE ngôn ngữ trước khi thực hiện dịch tự động!');?>");
        // Chuyển hướng trang hiện tại
        window.location.href = "<?= base_url_admin("language-edit&id=$id"); ?>";
        return;
    }

    // Lưu lại nội dung ban đầu của nút
    const originalHTML = btn.innerHTML;
    // Vô hiệu hóa nút và thêm spinner (sử dụng lớp của Bootstrap)
    btn.disabled = true;
    btn.innerHTML =
        `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> <?=__('Translating...');?>`;

    // URL của API
    const apiUrl = 'https://api.cmsnt.co/translation-api.php';
    const url = `${apiUrl}?license_key=<?=$CMSNT->site('license_key');?>&q=${encodeURIComponent(defaultText)}&target=${encodeURIComponent(targetLang)}`;

    // Gọi API bằng fetch
    fetch(url)
        .then(response => response.json())
        .then(data => {
            // Nếu có lỗi từ API
            if (data.error) {
                alert("<?=__('Lỗi');?>: " + data.error);
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                return;
            }
            // Xử lý kết quả trả về
            let translatedText = "";
            if (data.data && data.data.translations && data.data.translations.length > 0) {
                translatedText = data.data.translations[0].translatedText;
                // Cập nhật giá trị cho textarea
                document.getElementById("value" + id).value = translatedText;
                // Kích hoạt sự kiện change thủ công (dùng jQuery)
                $("#value" + id).trigger("change");

            } else {
                alert("<?=__('Không nhận được kết quả dịch.');?>");
                btn.disabled = false;
                btn.innerHTML = originalHTML;
                return;
            }
            // Cập nhật kết quả dịch vào textarea tương ứng
            document.getElementById("value" + id).value = translatedText;
            // Khôi phục trạng thái nút
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        })
        .catch(error => {
            console.error("Có lỗi xảy ra: ", error);
            alert("<?=__('Có lỗi xảy ra khi dịch.');?>");
            btn.disabled = false;
            btn.innerHTML = originalHTML;
        });
}
</script>





<script type="text/javascript">
function updateForm(id) {
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'changeTranslate',
            id: id,
            value: $("#value" + id).val()
        },
        success: function(result) {
            if (result.status == 'success') {
                //showMessage(result.msg, result.status);
            } else {
                showMessage(result.msg, result.status);
            }
        },
        error: function() {
            alert(html(result));
            location.reload();
        }
    });
}

function RemoveRow(id, name) {
    cuteAlert({
        type: "question",
        title: "<?=__('Xác Nhận Xóa Ngôn Ngữ');?>",
        message: "<?=__('Bạn có chắc chắn muốn xóa ngôn ngữ');?> (" + name + ") <?=__('không ?');?>",
        confirmText: "<?=__('Đồng Ý');?>",
        cancelText: "<?=__('Hủy');?>"
    }).then((e) => {
        if (e) {
            $.ajax({
                url: "<?=BASE_URL("ajaxs/admin/remove.php");?>",
                method: "POST",
                dataType: "JSON",
                data: {
                    action: 'removeTranslate',
                    id: id
                },
                success: function(result) {
                    if (result.status == 'success') {
                        showMessage(result.msg, result.status);
                        // Reload DataTable thay vì reload toàn trang
                        reloadTranslateTable();
                    } else {
                        showMessage(result.msg, result.status);
                    }
                },
                error: function() {
                    alert(html(result));
                    // Reload DataTable thay vì reload toàn trang
                    reloadTranslateTable();
                }
            });
        }
    })
}
</script>

<script>
// Global variable để lưu trạng thái filter
var currentFilter = 'all';

// Khởi tạo DataTable với server-side processing
var translateTable = $('#datatable-basic').DataTable({
    "processing": true,
    "serverSide": true,
    "ajax": {
        "url": "<?= BASE_URL("ajaxs/admin/view.php"); ?>",
        "type": "POST",
        "data": function(d) {
            d.action = 'load_translate_data';
            d.lang_id = '<?= $row['id']; ?>';
            d.token = '<?= $getUser['token']; ?>';
            d.filter = currentFilter; // Thêm filter parameter
        },
        "error": function(xhr, error, code) {
            console.log('Ajax error:', error);
            showMessage('<?=__("Có lỗi xảy ra khi tải dữ liệu");?>', 'error');
        }
    },
    "columns": [
        { "data": 0, "orderable": false, "searchable": false },
        { "data": 1, "orderable": true, "searchable": true },
        { "data": 2, "orderable": true, "searchable": true },
        { "data": 3, "orderable": false, "searchable": false }
    ],
    "order": [[ 0, "desc" ]],
    "language": {
        searchPlaceholder: "<?=__('Tìm kiếm...');?>",
        sSearch: '',
        processing: "<?=__('Đang xử lý...');?>",
        loadingRecords: "<?=__('Đang tải...');?>",
        lengthMenu: "<?=__('Hiển thị _MENU_ mục');?>",
        zeroRecords: "<?=__('Không tìm thấy dữ liệu');?>",
        info: "<?=__('Hiển thị _START_ đến _END_ của _TOTAL_ mục');?>",
        infoEmpty: "<?=__('Hiển thị 0 đến 0 của 0 mục');?>",
        infoFiltered: "(<?=__('lọc từ _MAX_ tổng số mục');?>)",
        paginate: {
            first: "<?=__('Đầu');?>",
            last: "<?=__('Cuối');?>",
            next: "<?=__('Tiếp');?>",
            previous: "<?=__('Trước');?>"
        }
    },
    "lengthMenu": [[10, 25, 50, 100, 200, 300, 500, 1000, 2000, 5000, 10000], [10, 25, 50, 100, 200, 300, 500, 1000, 2000, 5000, 10000]],
    "pageLength": 10,
    "scrollX": true,
    "drawCallback": function(settings) {
        // Callback sau khi vẽ lại bảng để đảm bảo events hoạt động
        bindTextareaEvents();
        // Đảm bảo bulk actions được ẩn khi reload table
        toggleBulkActions();
    }
});

// Hàm để bind events cho textarea sau khi DataTable được vẽ lại
function bindTextareaEvents() {
    // Unbind trước để tránh duplicate events
    $('textarea[id^="value"]').off('change.translate');
    
    // Bind lại events cho textarea
    $('textarea[id^="value"]').on('change.translate', function() {
        var id = $(this).attr('id').replace('value', '');
        updateForm(id);
    });
}

// Hàm refresh DataTable
function reloadTranslateTable() {
    translateTable.ajax.reload(null, false); // false = giữ nguyên trang hiện tại
    // Reset checkbox states
    $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
}

// Xử lý checkbox select all và individual checkboxes
$(document).ready(function() {
    // Select/Deselect all checkboxes
    $(document).on('change', '#select-all-checkbox', function() {
        var isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        toggleBulkActions();
    });
    
    // Individual checkbox change
    $(document).on('change', '.row-checkbox', function() {
        var totalCheckboxes = $('.row-checkbox').length;
        var checkedCheckboxes = $('.row-checkbox:checked').length;
        
        // Update select all checkbox state
        if (checkedCheckboxes === totalCheckboxes) {
            $('#select-all-checkbox').prop('checked', true).prop('indeterminate', false);
        } else if (checkedCheckboxes === 0) {
            $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
        } else {
            $('#select-all-checkbox').prop('indeterminate', true);
        }
        
        toggleBulkActions();
    });
    
    // Bulk delete action
    $('#bulk-delete-btn').click(function() {
        var selectedIds = [];
        $('.row-checkbox:checked').each(function() {
            selectedIds.push($(this).val());
        });
        
        if (selectedIds.length === 0) {
            alert('<?=__("Vui lòng chọn ít nhất một mục để xóa");?>');
            return;
        }
        
        // Cập nhật số lượng trong modal
        $('#bulkDeleteCount').text(selectedIds.length);
        
        // Lưu selectedIds vào global variable để sử dụng trong confirmBulkDelete
        window.selectedDeleteIds = selectedIds;
        
        // Hiển thị modal
        $('#bulkDeleteModal').modal('show');
    });
    
    // Bulk translate action
    $('#bulk-translate-btn').click(function() {
        var selectedData = [];
        $('.row-checkbox:checked').each(function() {
            selectedData.push({
                id: $(this).val(),
                name: $(this).data('name'),
                code: $(this).data('code')
            });
        });
        
        if (selectedData.length === 0) {
            alert('<?=__("Vui lòng chọn ít nhất một mục để dịch");?>');
            return;
        }
        
        // Kiểm tra giới hạn max_input_vars
        var maxInputVars = <?=ini_get('max_input_vars');?>;
        var maxAllowedItems = Math.floor(maxInputVars / 6); // Mỗi item có khoảng 6 variables
        
        if (selectedData.length > maxAllowedItems) {
            // Hiển thị modal cảnh báo về giới hạn
            $('#maxInputVarsLimit').text(maxInputVars);
            $('#currentSelectedCount').text(selectedData.length);
            $('#maxAllowedItems').text(maxAllowedItems);
            $('#phpIniPath').text('<?=php_ini_loaded_file();?>');
            $('#inputVarsLimitModal').modal('show');
            return;
        }
        
        // Đảm bảo modal ở trạng thái ban đầu
        showBulkTranslateLoading(false);
        
        // Cập nhật số lượng trong modal
        $('#bulkTranslateCount').text(selectedData.length);
        
        // Lưu selectedData vào global variable để sử dụng trong confirmBulkTranslate
        window.selectedTranslateData = selectedData;
        
        // Hiển thị modal
        $('#bulkTranslateModal').modal('show');
    });
    
    // Filter buttons
    $('#filter-untranslated-btn').click(function() {
        currentFilter = 'untranslated';
        $(this).hide();
        $('#filter-all-btn').show();
        
        // Update button text to show current filter
        $(this).html('<i class="fa-solid fa-filter"></i> <?=__('Đang lọc: Chưa dịch');?>');
        
        // Show filter info badge
        $('#filter-info').text('<?=__('Đang lọc: Chưa dịch');?>').removeClass('bg-secondary').addClass('bg-warning').show();
        
        // Reload table with filter
        translateTable.ajax.reload();
        
        // Show message
        showMessage('<?=__("Đã lọc hiển thị chỉ nội dung chưa dịch");?>', 'info');
    });
    
    $('#filter-all-btn').click(function() {
        currentFilter = 'all';
        $(this).hide();
        $('#filter-untranslated-btn').show();
        
        // Reset button text
        $('#filter-untranslated-btn').html('<i class="fa-solid fa-filter"></i> <?=__('Chưa dịch');?>');
        
        // Hide filter info badge
        $('#filter-info').hide();
        
        // Reload table without filter
        translateTable.ajax.reload();
        
        // Show message
        showMessage('<?=__("Đã hiển thị tất cả nội dung");?>', 'info');
    });
});

// Toggle bulk action buttons visibility
function toggleBulkActions() {
    var checkedCount = $('.row-checkbox:checked').length;
    if (checkedCount > 0) {
        $('#bulk-actions').addClass('show');
    } else {
        $('#bulk-actions').removeClass('show');
    }
}

// Bulk delete function
function bulkDeleteTranslates(ids) {
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/remove.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'bulk_delete_translates',
            ids: ids,
            token: '<?= $getUser['token']; ?>'
        },
        beforeSend: function() {
            $('#bulk-delete-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span><?=__("Đang xóa...");?>');
        },
        success: function(result) {
            if (result.status == 'success') {
                showMessage(result.msg, result.status);
                reloadTranslateTable();
                $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
                toggleBulkActions();
            } else {
                showMessage(result.msg, result.status);
            }
        },
        error: function() {
            showMessage('<?=__("Có lỗi xảy ra khi xóa");?>', 'error');
        },
        complete: function() {
            $('#bulk-delete-btn').prop('disabled', false).html('<i class="bx bx-trash"></i> <?=__("Xóa tất cả");?>');
        }
    });
}

// Hàm xác nhận dịch tự động từ modal
function confirmBulkTranslate() {
    if (window.selectedTranslateData && window.selectedTranslateData.length > 0) {
        // Hiển thị trạng thái loading trong modal
        showBulkTranslateLoading(true);
        
        // Thực hiện dịch tự động với callback để xử lý modal
        bulkAutoTranslateWithModal(window.selectedTranslateData);
    }
}

// Hàm hiển thị/ẩn trạng thái loading trong modal dịch tự động
function showBulkTranslateLoading(isLoading) {
    const modal = $('#bulkTranslateModal');
    const modalBody = modal.find('.modal-body');
    const modalFooter = modal.find('.modal-footer');
    
    if (isLoading) {
        // Thay đổi tiêu đề modal
        modal.find('.modal-title').html('<i class="fa-solid fa-spinner fa-spin me-2"></i><?=__('Đang Dịch Tự Động...');?>');
        
        // Ẩn nội dung hiện tại và hiển thị loading
        modalBody.html(`
            <div class="text-center py-4">
                <div class="spinner-border text-primary mb-3" style="width: 3rem; height: 3rem;" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <h5 class="text-primary"><?=__('Đang thực hiện dịch tự động...');?></h5>
                <p class="text-muted mb-0"><?=__('Vui lòng chờ trong giây lát. Quá trình này có thể mất vài phút.');?></p>
                <div class="progress mt-3" style="height: 6px;">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                         role="progressbar" style="width: 100%"></div>
                </div>
            </div>
        `);
        
        // Chỉ hiển thị nút Hủy trong footer
        modalFooter.html(`
            <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal" disabled>
                <i class="fa fa-times me-1"></i><?=__('Đang xử lý...');?>
            </button>
        `);
    } else {
        // Reset lại modal về trạng thái ban đầu
        modal.find('.modal-title').html('<i class="fa-solid fa-language me-2"></i><?=__('Xác Nhận Dịch Tự Động');?>');
        
        // Khôi phục nội dung ban đầu
        modalBody.html(`
            <div class="alert alert-info border-info">
                <div class="d-flex align-items-center">
                    <i class="fa-solid fa-info-circle text-info me-2 fs-4"></i>
                    <div>
                        <h6 class="alert-heading mb-1"><?=__('Thông tin quan trọng!');?></h6>
                        <p class="mb-0"><?=__('Hành động này sẽ dịch tự động tất cả bản dịch đã chọn.');?></p>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <p class="mb-2"><?=__('Hệ thống sẽ thực hiện:');?></p>
                <ul class="list-unstyled">
                    <li class="mb-2">
                        <i class="fa-solid fa-robot text-info me-2"></i>
                        <?=__('Gọi API dịch tự động cho');?> <strong id="bulkTranslateCount" class="text-primary">0</strong> <?=__('bản dịch');?>
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-clock text-warning me-2"></i>
                        <?=__('Quá trình có thể mất vài phút để hoàn thành');?>
                    </li>
                    <li class="mb-2">
                        <i class="fa-solid fa-save text-success me-2"></i>
                        <?=__('Tự động cập nhật kết quả vào database');?>
                    </li>
                </ul>
            </div>
            
            <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                <input class="form-check-input" type="checkbox" value="" id="confirmBulkTranslateCheckbox">
                <label class="form-check-label fw-semibold" for="confirmBulkTranslateCheckbox">
                    <?=__('Tôi đồng ý thực hiện dịch tự động cho tất cả bản dịch đã chọn');?>
                </label>
            </div>
        `);
        
        // Khôi phục footer ban đầu
        modalFooter.html(`
            <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
            </button>
            <button type="button" id="btnConfirmBulkTranslate" class="btn btn-info btn-wave" onclick="confirmBulkTranslate()" disabled>
                <i class="fa fa-language me-1"></i><?=__('Dịch tự động');?>
            </button>
        `);
        
        // Rebind event listener cho checkbox sau khi khôi phục content
        setTimeout(function() {
            $('#confirmBulkTranslateCheckbox').off('change.bulkTranslate').on('change.bulkTranslate', function() {
                const isChecked = $(this).is(':checked');
                $('#btnConfirmBulkTranslate').prop('disabled', !isChecked);
            });
        }, 100);
    }
}

// Bulk auto translate function với modal handling
function bulkAutoTranslateWithModal(translateData) {
    if (translateData.length === 0) return;
    
    var targetLang = translateData[0].code;
    if (!targetLang || targetLang.trim() === "") {
        showBulkTranslateLoading(false);
        alert("<?=__('Vui lòng cập nhật ISO CODE ngôn ngữ trước khi thực hiện dịch tự động!');?>");
        return;
    }
    
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'bulk_auto_translate',
            translate_data: translateData,
            target_lang: targetLang,
            token: '<?= $getUser['token']; ?>'
        },
        beforeSend: function() {
            $('#bulk-translate-btn').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span><?=__("Đang dịch...");?>');
        },
        success: function(result) {
            // Reset modal trước khi đóng
            showBulkTranslateLoading(false);
            
            // Đóng modal
            $('#bulkTranslateModal').modal('hide');
            
            if (result.status == 'success') {
                showMessage(result.msg, result.status);
                reloadTranslateTable();
                $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
                toggleBulkActions();
            } else {
                showMessage(result.msg, result.status);
            }
        },
        error: function() {
            // Reset modal trước khi đóng
            showBulkTranslateLoading(false);
            
            // Đóng modal
            $('#bulkTranslateModal').modal('hide');
            showMessage('<?=__("Có lỗi xảy ra khi dịch");?>', 'error');
        },
        complete: function() {
            // Reset button bulk translate
            $('#bulk-translate-btn').prop('disabled', false).html('<i class="fa-solid fa-language"></i> <?=__("Dịch tự động tất cả");?>');
        }
    });
}
</script>

<!-- Modal xác nhận dịch tự động tất cả -->
<div class="modal fade" id="bulkTranslateModal" tabindex="-1" aria-labelledby="bulkTranslateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-info-transparent">
                <h5 class="modal-title text-info" id="bulkTranslateModalLabel">
                    <i class="fa-solid fa-language me-2"></i>
                    <?=__('Xác Nhận Dịch Tự Động');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info border-info">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-info-circle text-info me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Thông tin quan trọng!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ dịch tự động tất cả bản dịch đã chọn.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-robot text-info me-2"></i>
                            <?=__('Gọi API dịch tự động cho');?> <strong id="bulkTranslateCount" class="text-primary">0</strong> <?=__('bản dịch');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-clock text-warning me-2"></i>
                            <?=__('Quá trình có thể mất vài phút để hoàn thành');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-save text-success me-2"></i>
                            <?=__('Tự động cập nhật kết quả vào database');?>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmBulkTranslateCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmBulkTranslateCheckbox">
                        <?=__('Tôi đồng ý thực hiện dịch tự động cho tất cả bản dịch đã chọn');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" id="btnConfirmBulkTranslate" class="btn btn-info btn-wave" onclick="confirmBulkTranslate()" disabled>
                    <i class="fa fa-language me-1"></i><?=__('Dịch tự động');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal cảnh báo giới hạn max_input_vars -->
<div class="modal fade" id="inputVarsLimitModal" tabindex="-1" aria-labelledby="inputVarsLimitModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-warning-transparent">
                <h5 class="modal-title text-warning" id="inputVarsLimitModalLabel">
                    <i class="fa-solid fa-exclamation-triangle me-2"></i>
                    <?=__('Giới hạn PHP max_input_vars');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning border-warning">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-server text-warning me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Giới hạn cấu hình server');?></h6>
                            <p class="mb-0"><?=__('Số lượng items bạn chọn vượt quá giới hạn của PHP.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <div class="card border-danger">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-list-check text-danger fs-2 mb-2"></i>
                                <h4 class="text-danger mb-1" id="currentSelectedCount">0</h4>
                                <p class="text-muted mb-0 fs-12"><?=__('Items đã chọn');?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-success">
                            <div class="card-body text-center">
                                <i class="fa-solid fa-check text-success fs-2 mb-2"></i>
                                <h4 class="text-success mb-1" id="maxAllowedItems">0</h4>
                                <p class="text-muted mb-0 fs-12"><?=__('Tối đa cho phép');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="card border-info bg-info-transparent">
                    <div class="card-body">
                        <h6 class="text-info mb-3">
                            <i class="fa-solid fa-gear me-2"></i><?=__('Thông tin cấu hình hiện tại');?>
                        </h6>
                        <div class="row">
                            <div class="col-sm-6">
                                <strong><?=__('max_input_vars');?>:</strong>
                            </div>
                            <div class="col-sm-6">
                                <span id="maxInputVarsLimit" class="badge bg-info">0</span>
                            </div>
                        </div>
                        <div class="row mt-2">
                            <div class="col-sm-6">
                                <strong><?=__('File cấu hình');?>:</strong>
                            </div>
                            <div class="col-sm-6">
                                <code id="phpIniPath" class="fs-11"></code>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4">
                    <h6 class="text-primary mb-3">
                        <i class="fa-solid fa-lightbulb me-2"></i><?=__('Giải pháp khuyến nghị');?>
                    </h6>
                    <div class="accordion" id="solutionAccordion">
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                    <i class="fa-solid fa-cog me-2"></i><?=__('Tăng giới hạn max_input_vars');?>
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#solutionAccordion">
                                <div class="accordion-body">
                                    <p class="mb-3"><?=__('Để dịch nhiều items hơn, hãy tăng giá trị max_input_vars trong file php.ini:');?></p>
                                    <div class="bg-dark p-3 rounded">
                                        <code class="text-light">
                                            ; <?=__('Tăng giới hạn từ');?> <?=ini_get('max_input_vars');?> <?=__('lên');?> 3000<br>
                                            max_input_vars = 3000
                                        </code>
                                    </div>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <small>
                                            <i class="fa-solid fa-info-circle me-1"></i>
                                            <?=__('Sau khi chỉnh sửa, hãy khởi động lại web server (Apache/Nginx) để áp dụng thay đổi.');?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                    <i class="fa-solid fa-divide me-2"></i><?=__('Dịch theo từng lô nhỏ');?>
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#solutionAccordion">
                                <div class="accordion-body">
                                    <p class="mb-2"><?=__('Bạn có thể dịch theo các lô nhỏ hơn:');?></p>
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fa-solid fa-check text-success me-2"></i>
                                            <?=__('Chọn tối đa');?> <span id="maxAllowedItems2" class="fw-bold text-primary">0</span> <?=__('items mỗi lần');?>
                                        </li>
                                        <li class="mb-2">
                                            <i class="fa-solid fa-check text-success me-2"></i>
                                            <?=__('Lặp lại quá trình cho các lô tiếp theo');?>
                                        </li>
                                        <li class="mb-0">
                                            <i class="fa-solid fa-check text-success me-2"></i>
                                            <?=__('Sử dụng bộ lọc để chọn items chưa dịch');?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Đóng');?>
                </button>
                <button type="button" class="btn btn-primary btn-wave" onclick="selectMaxAllowedItems()">
                    <i class="fa fa-magic me-1"></i><?=__('Chọn tự động số lượng tối đa');?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal xác nhận xóa tất cả -->
<div class="modal fade" id="bulkDeleteModal" tabindex="-1" aria-labelledby="bulkDeleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger-transparent">
                <h5 class="modal-title text-danger" id="bulkDeleteModalLabel">
                    <i class="fa-solid fa-trash me-2"></i>
                    <?=__('Xác Nhận Xóa Tất Cả');?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger border-danger">
                    <div class="d-flex align-items-center">
                        <i class="fa-solid fa-exclamation-triangle text-danger me-2 fs-4"></i>
                        <div>
                            <h6 class="alert-heading mb-1"><?=__('Cảnh báo nguy hiểm!');?></h6>
                            <p class="mb-0"><?=__('Hành động này sẽ không thể hoàn tác.');?></p>
                        </div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <p class="mb-2"><?=__('Hệ thống sẽ thực hiện:');?></p>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fa-solid fa-trash text-danger me-2"></i>
                            <?=__('Xóa vĩnh viễn');?> <strong id="bulkDeleteCount" class="text-danger">0</strong> <?=__('bản dịch đã chọn');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-database text-warning me-2"></i>
                            <?=__('Dữ liệu sẽ bị xóa khỏi database');?>
                        </li>
                        <li class="mb-2">
                            <i class="fa-solid fa-ban text-danger me-2"></i>
                            <?=__('Không thể khôi phục sau khi xóa');?>
                        </li>
                    </ul>
                </div>
                
                <div class="form-check form-check-lg d-flex align-items-center bg-light p-3 rounded">
                    <input class="form-check-input" type="checkbox" value="" id="confirmBulkDeleteCheckbox">
                    <label class="form-check-label fw-semibold" for="confirmBulkDeleteCheckbox">
                        <?=__('Tôi hiểu rủi ro và đồng ý xóa tất cả bản dịch đã chọn');?>
                    </label>
                </div>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-outline-secondary btn-wave" data-bs-dismiss="modal">
                    <i class="fa fa-times me-1"></i><?=__('Hủy bỏ');?>
                </button>
                <button type="button" id="btnConfirmBulkDelete" class="btn btn-danger btn-wave" onclick="confirmBulkDelete()" disabled>
                    <i class="fa fa-trash me-1"></i><?=__('Xóa tất cả');?>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Hàm xác nhận xóa tất cả từ modal
function confirmBulkDelete() {
    if (window.selectedDeleteIds && window.selectedDeleteIds.length > 0) {
        // Đóng modal
        $('#bulkDeleteModal').modal('hide');
        
        // Thực hiện xóa
        bulkDeleteTranslates(window.selectedDeleteIds);
    }
}

// Xử lý checkbox enable/disable button đồng bộ bản dịch
$(document).ready(function() {
    $('#confirmSyncTranslateCheckbox').change(function() {
        const isChecked = $(this).is(':checked');
        $('#btnSyncTranslate').prop('disabled', !isChecked);
    });
    
    // Reset checkbox khi đóng modal
    $('#syncTranslateModal').on('hidden.bs.modal', function() {
        $('#confirmSyncTranslateCheckbox').prop('checked', false);
        $('#btnSyncTranslate').prop('disabled', true);
    });
    
    // Xử lý checkbox enable/disable button tạo lại bản dịch
    $('#confirmUpdateTranslateCheckbox').change(function() {
        const isChecked = $(this).is(':checked');
        $('#btnUpdateTranslate').prop('disabled', !isChecked);
    });
    
    // Reset checkbox khi đóng modal tạo lại bản dịch
    $('#exampleModalScrollable2').on('hidden.bs.modal', function() {
        $('#confirmUpdateTranslateCheckbox').prop('checked', false);
        $('#btnUpdateTranslate').prop('disabled', true);
    });
    
    // Xử lý checkbox enable/disable button bulk translate
    $('#confirmBulkTranslateCheckbox').change(function() {
        const isChecked = $(this).is(':checked');
        $('#btnConfirmBulkTranslate').prop('disabled', !isChecked);
    });
    
    // Reset checkbox khi đóng modal bulk translate
    $('#bulkTranslateModal').on('hidden.bs.modal', function() {
        $('#confirmBulkTranslateCheckbox').prop('checked', false);
        $('#btnConfirmBulkTranslate').prop('disabled', true);
        // Reset modal về trạng thái ban đầu khi đóng
        showBulkTranslateLoading(false);
    });
    
    // Xử lý checkbox enable/disable button bulk delete
    $('#confirmBulkDeleteCheckbox').change(function() {
        const isChecked = $(this).is(':checked');
        $('#btnConfirmBulkDelete').prop('disabled', !isChecked);
    });
    
    // Reset checkbox khi đóng modal bulk delete
    $('#bulkDeleteModal').on('hidden.bs.modal', function() {
        $('#confirmBulkDeleteCheckbox').prop('checked', false);
        $('#btnConfirmBulkDelete').prop('disabled', true);
    });
});

// Hàm đồng bộ bản dịch từ lang.php
function syncTranslateFromLangFile() {
    const btn = document.getElementById('btnSyncTranslate');
    const originalHTML = btn.innerHTML;
    
    // Hiển thị loading
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><?=__("Đang đồng bộ...");?>';
    
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'syncTranslate',
            lang_id: '<?=$row['id'];?>'
        },
        success: function(result) {
            if (result.status == 'success') {
                // Đóng modal
                $('#syncTranslateModal').modal('hide');
                
                // Hiển thị thông báo thành công
                cuteAlert({
                    type: "success",
                    title: "<?=__('Thành công!');?>",
                    message: result.msg,
                    confirmText: "<?=__('Đóng');?>"
                }).then((e) => {
                    // Reload DataTable để cập nhật danh sách
                    reloadTranslateTable();
                });
            } else {
                // Hiển thị lỗi
                cuteAlert({
                    type: "error",
                    title: "<?=__('Lỗi!');?>",
                    message: result.msg,
                    confirmText: "<?=__('Đóng');?>"
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax Error:', error);
            cuteAlert({
                type: "error",
                title: "<?=__('Lỗi!');?>",
                message: "<?=__('Có lỗi xảy ra khi kết nối đến server');?>",
                confirmText: "<?=__('Đóng');?>"
            });
        },
        complete: function() {
            // Luôn reset button về trạng thái ban đầu
            btn.disabled = true;
            btn.innerHTML = originalHTML;
            // Reset checkbox
            $('#confirmSyncTranslateCheckbox').prop('checked', false);
        }
    });
}

// Hàm tạo lại bản dịch từ ngôn ngữ mặc định
function updateTranslateFromDefault() {
    const btn = document.getElementById('btnUpdateTranslate');
    const originalHTML = btn.innerHTML;
    
    // Hiển thị loading
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span><?=__("Đang tạo lại...");?>';
    
    $.ajax({
        url: "<?= BASE_URL("ajaxs/admin/update.php"); ?>",
        method: "POST",
        dataType: "JSON",
        data: {
            action: 'updateTranslate',
            lang_id: '<?=$row['id'];?>'
        },
        success: function(result) {
            if (result.status == 'success') {
                // Đóng modal
                $('#exampleModalScrollable2').modal('hide');
                
                // Hiển thị thông báo thành công
                cuteAlert({
                    type: "success",
                    title: "<?=__('Thành công!');?>",
                    message: result.msg,
                    confirmText: "<?=__('Đóng');?>"
                }).then((e) => {
                    // Reload DataTable để cập nhật danh sách
                    reloadTranslateTable();
                });
            } else {
                // Hiển thị lỗi
                cuteAlert({
                    type: "error",
                    title: "<?=__('Lỗi!');?>",
                    message: result.msg,
                    confirmText: "<?=__('Đóng');?>"
                });
            }
        },
        error: function(xhr, status, error) {
            console.error('Ajax Error:', error);
            cuteAlert({
                type: "error",
                title: "<?=__('Lỗi!');?>",
                message: "<?=__('Có lỗi xảy ra khi kết nối đến server');?>",
                confirmText: "<?=__('Đóng');?>"
            });
        },
        complete: function() {
            // Luôn reset button về trạng thái ban đầu
            btn.disabled = true;
            btn.innerHTML = originalHTML;
            // Reset checkbox
            $('#confirmUpdateTranslateCheckbox').prop('checked', false);
        }
    });
}

// Hàm tự động chọn số lượng items tối đa
function selectMaxAllowedItems() {
    var maxInputVars = <?=ini_get('max_input_vars');?>;
    var maxAllowedItems = Math.floor(maxInputVars / 6);
    
    // Bỏ chọn tất cả checkbox trước
    $('.row-checkbox').prop('checked', false);
    $('#select-all-checkbox').prop('checked', false).prop('indeterminate', false);
    
    // Chọn số lượng tối đa cho phép
    var checkedCount = 0;
    $('.row-checkbox').each(function() {
        if (checkedCount < maxAllowedItems) {
            $(this).prop('checked', true);
            checkedCount++;
        }
    });
    
    // Cập nhật trạng thái bulk actions
    toggleBulkActions();
    
    // Đóng modal
    $('#inputVarsLimitModal').modal('hide');
    
    // Hiển thị thông báo
    showMessage('<?=__("Đã chọn tự động");?> ' + checkedCount + ' <?=__("items để dịch");?>', 'success');
}

// Cập nhật giá trị maxAllowedItems2 khi modal được hiển thị
$(document).ready(function() {
    $('#inputVarsLimitModal').on('show.bs.modal', function() {
        var maxAllowedItems = Math.floor(<?=ini_get('max_input_vars');?> / 6);
        $('#maxAllowedItems2').text(maxAllowedItems);
    });
});
</script>