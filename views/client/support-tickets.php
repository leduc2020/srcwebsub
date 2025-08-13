<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

$body = [
    'title' => __('Yêu cầu Hỗ trợ').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
 <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
';
$body['footer'] = '

';

if($CMSNT->site('support_tickets_status') == 0){
    redirect(base_url());
}

require_once(__DIR__.'/../../models/is_user.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');


// Pagination và filtering
if(isset($_GET['limit'])){
    $limit = intval(check_string($_GET['limit']));
}else{
    $limit = 10;
}
if(isset($_GET['page'])){
    $page = check_string(intval($_GET['page']));
}else{
    $page = 1;
}
$from = ($page - 1) * $limit;

$where = " `user_id` = '".$getUser['id']."' ";
$status_filter = '';
$subject_filter = '';
$category_filter = '';
$time_filter = '';

if(!empty($_GET['status'])){
    $status_filter = check_string($_GET['status']);
    $where .= " AND `status` = '$status_filter' ";
}

if(!empty($_GET['subject'])){
    $subject_filter = check_string($_GET['subject']);
    $where .= " AND `subject` LIKE '%$subject_filter%' ";
}

if(!empty($_GET['category'])){
    $category_filter = check_string($_GET['category']);
    $where .= " AND `category` = '$category_filter' ";
}

if(!empty($_GET['time'])){
    $time_filter = check_string($_GET['time']);
    $create_date_1 = str_replace('-', '/', $time_filter);
    $create_date_1 = explode(' to ', $create_date_1);
    if(count($create_date_1) == 2 && $create_date_1[0] != $create_date_1[1]){
        $create_date_1 = [$create_date_1[0].' 00:00:00', $create_date_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$create_date_1[0]."' AND `created_at` <= '".$create_date_1[1]."' ";
    }
}



$listTickets = $CMSNT->get_list(" SELECT * FROM `support_tickets` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalTickets = $CMSNT->num_rows(" SELECT * FROM `support_tickets` WHERE $where ORDER BY id DESC ");
$urlPagination = pagination_client(base_url("?action=support-tickets&limit=$limit&status=$status_filter&subject=$subject_filter&category=$category_filter&time=$time_filter&"), $from, $totalTickets, $limit);

// Thống kê tickets
$stats = [
    'total' => $CMSNT->num_rows("SELECT * FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."'"),
    'open' => $CMSNT->num_rows("SELECT * FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `status` = 'open'"),
    'pending' => $CMSNT->num_rows("SELECT * FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `status` = 'pending'"),
    'answered' => $CMSNT->num_rows("SELECT * FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `status` = 'answered'"),
    'closed' => $CMSNT->num_rows("SELECT * FROM `support_tickets` WHERE `user_id` = '".$getUser['id']."' AND `status` = 'closed'")
];

?>

<div class="main-content">
    <div class="page-content">
        <div class="container-fluid">
            <!-- start page title -->
            <div class="row">
                <div class="col-12">
                    <div class="page-title-box d-sm-flex align-items-center justify-content-between bg-galaxy-transparent">
                        <h4 class="mb-sm-0"><?=__('Yêu cầu Hỗ trợ');?></h4>
                        <div class="page-title-right">
                            <ol class="breadcrumb m-0">
                                <li class="breadcrumb-item"><a href="<?=base_url('client/profile');?>"><?=__('Tài khoản');?></a></li>
                                <li class="breadcrumb-item active"><?=__('Yêu cầu Hỗ trợ');?></li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
            <!-- end page title -->


            <!-- Thống kê -->
            <div class="row">
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div class="avatar-title border bg-primary-subtle border-primary border-opacity-25 rounded-2 fs-17">
                                    <i class="ri-ticket-line icon-dual-primary"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['total']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Tổng tickets');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div class="avatar-title border bg-info-subtle border-info border-opacity-25 rounded-2 fs-17">
                                    <i class="ri-mail-open-line icon-dual-info"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['open']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đang mở');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div class="avatar-title border bg-warning-subtle border-warning border-opacity-25 rounded-2 fs-17">
                                    <i class="ri-time-line icon-dual-warning"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['pending']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Chờ xử lý');?></p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-3">
                    <div class="card">
                        <div class="card-body d-flex gap-3 align-items-center">
                            <div class="avatar-sm">
                                <div class="avatar-title border bg-success-subtle border-success border-opacity-25 rounded-2 fs-17">
                                    <i class="ri-check-line icon-dual-success"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="fs-15"><?=format_cash($stats['answered']);?></h5>
                                <p class="mb-0 text-muted"><?=__('Đã trả lời');?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>



            <!-- Danh sách tickets -->
            <div class="row">
                <div class="col-lg-12">
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="d-flex align-items-center">
                                <h5 class="card-title mb-0 flex-grow-1 text-uppercase"><?=__('Danh sách tickets');?></h5>
                                <div class="flex-shrink-0">
                                    <button type="button" class="btn btn-danger waves-effect waves-light" data-bs-toggle="modal" data-bs-target="#addTicketModal">
                                        <i class="ri-add-line me-1"></i> <?=__('Tạo yêu cầu hỗ trợ mới');?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body bg-light-subtle border border-dashed border-start-0 border-end-0">
                            <form action="<?=base_url();?>" method="GET">
                                <input type="hidden" name="action" value="support-tickets">
                                <div class="row g-3">
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control search bg-light border-light"
                                                name="subject" value="<?=$subject_filter;?>" placeholder="<?=__('Tìm theo tiêu đề');?>">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-sm-6">
                                        <div>
                                            <select name="status" class="form-select">
                                                <option value=""><?=__('Tất cả trạng thái');?></option>
                                                <?php foreach($config_status_support_tickets as $key => $value):?>
                                                <option value="<?=$key;?>" <?=$status_filter == $key ? 'selected' : '';?>><?=$value;?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-sm-6">
                                        <div>
                                            <select name="category" class="form-select">
                                                <option value=""><?=__('Tất cả chủ đề');?></option>
                                                <?php foreach($config_category_support_tickets as $key => $value):?>
                                                <option value="<?=$key;?>" <?=$category_filter == $key ? 'selected' : '';?>><?=$value;?></option>
                                                <?php endforeach;?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-xxl-3 col-sm-6">
                                        <div class="search-box">
                                            <input type="text" class="form-control bg-light border-light"
                                                id="flatpickr-range" name="time" value="<?=$time_filter;?>"
                                                placeholder="<?=__('Chọn khoảng thời gian');?>" readonly>
                                            <i class="ri-calendar-line search-icon"></i>
                                        </div>
                                    </div>
                                    <div class="col-xxl-2 col-sm-6">
                                        <div class="d-flex gap-1">
                                            <button type="submit" class="btn btn-primary waves-effect waves-light">
                                                <i class="ri-search-line me-1 align-bottom"></i> <?=__('Tìm kiếm');?>
                                            </button>
                                            <a href="<?=base_url('client/support-tickets');?>" class="btn btn-light waves-effect waves-light">
                                                <i class="ri-delete-bin-line me-1 align-bottom"></i> <?=__('Bỏ lọc');?>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive table-card">
                                <table class="table align-middle table-nowrap">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th><?=__('ID');?></th>
                                            <th><?=__('Tiêu đề');?></th>
                                            <th><?=__('Mã đơn hàng');?></th>
                                            <th><?=__('Chủ đề');?></th>
                                            <th class="text-center"><?=__('Trạng thái');?></th>
                                            <th class="text-center"><?=__('Ngày tạo');?></th>
                                            <th class="text-center"><?=__('Cập nhật');?></th>
                                            <th class="text-center"><?=__('Thao tác');?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if(empty($listTickets)): ?>
                                        <tr>
                                            <td colspan="7" class="text-center">
                                                <div class="text-center p-3">
                                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                        trigger="loop" colors="primary:#121331,secondary:#08a88a"
                                                        style="width:75px;height:75px">
                                                    </lord-icon>
                                                    <h5 class="mt-2"><?=__('Không có ticket nào');?></h5>
                                                    <p class="text-muted mb-0"><?=__('Bạn chưa tạo ticket hỗ trợ nào.');?></p>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php else: ?>
                                        <?php foreach($listTickets as $ticket): ?>
                                        <tr>
                                            <td><strong>#<?=$ticket['id'];?></strong></td>
                                            <td>
                                                <a href="<?=base_url('ticket-detail/'.$ticket['id']);?>" class="text-primary fw-medium">
                                                    <?=strlen($ticket['subject']) > 50 ? substr($ticket['subject'], 0, 50).'...' : $ticket['subject'];?>
                                                </a>
                                            </td>
                                            <td><?=$ticket['order_id'] ? getRowRealtime('orders', $ticket['order_id'], 'trans_id') : '<span class="text-muted">'.__('Không có').'</span>';?></td>
                                            <td><?=$config_category_support_tickets[$ticket['category']];?></td>
                                            <td class="text-center">
                                                <?=display_status_support_tickets($ticket['status']);?>
                                            </td>
                                            <td class="text-center"><?=date('d/m/Y H:i', strtotime($ticket['created_at']));?></td>
                                            <td class="text-center"><?=date('d/m/Y H:i', strtotime($ticket['updated_at']));?></td>
                                            <td class="text-center">
                                                <a href="<?=base_url('ticket-detail/'.$ticket['id']);?>" class="btn btn-sm btn-primary">
                                                    <i class="ri-eye-line"></i> <?=__('Xem');?>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?=$urlPagination;?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
        <!-- container-fluid -->
    </div>
    <!-- End Page-content -->
</div>

<!-- Modal Tạo Ticket Mới -->
<div class="modal fade" id="addTicketModal" tabindex="-1" aria-labelledby="addTicketModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header p-3 bg-info-subtle">
                <h5 class="modal-title" id="addTicketModalLabel"><?=__('Tạo yêu cầu hỗ trợ mới');?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="addTicketForm">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subject" class="form-label"><?=__('Tiêu đề').' <span class="text-danger">*</span>';?></label>
                                <input type="text" class="form-control" id="subject" name="subject" placeholder="<?=__('Nhập tiêu đề ticket');?>" required>
                                <input type="hidden" name="token" value="<?=$getUser['token'];?>">
                                <input type="hidden" name="action" value="create-ticket">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="category" class="form-label"><?=__('Chủ đề');?></label>
                                <select class="form-select" id="category" name="category">
                                    <?php foreach($config_category_support_tickets as $key => $value):?>
                                    <option value="<?=$key;?>"><?=$value;?></option>
                                    <?php endforeach;?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12" id="orderIdField" style="display: none;">
                            <div class="mb-3">
                                <label for="orderID" class="form-label"><?=__('Mã đơn hàng');?></label>
                                <input type="text" class="form-control" id="orderID" name="order_id" placeholder="<?=__('Nhập mã đơn hàng (nếu có)');?>" list="orderIDList">
                                <datalist id="orderIDList">
                                    <?php foreach($CMSNT->get_list("SELECT * FROM `orders` WHERE `user_id` = '".$getUser['id']."' ORDER BY `id` DESC LIMIT 20") as $order): ?>
                                    <option value="<?=$order['trans_id'];?>"><?=$order['trans_id'];?> | <?=$order['service_name'];?> | <?=display_service($order['status']);?> | <?=format_cash($order['price']);?></option>
                                    <?php endforeach;?>
                                </datalist>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="ticketContent" class="form-label"><?=__('Nội dung').' <span class="text-danger">*</span>';?></label>
                        <textarea class="form-control" id="ticketContent" name="content" rows="5" placeholder="<?=__('Mô tả chi tiết vấn đề bạn gặp phải...');?>" required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger" data-bs-dismiss="modal"><?=__('Đóng');?></button>
                <button type="button" class="btn btn-primary" id="submitTicket">
                    <span class="btn-spinner d-none">
                        <i class="me-1 spinner-border spinner-border-sm"></i><?=__('Đang tạo...');?>
                    </span>
                    <span class="btn-text">
                        <i class="ri-send-plane-line me-1"></i><?=__('Tạo yêu cầu hỗ trợ');?>
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

<?php
require_once(__DIR__.'/footer.php');
?>



<script>
$(document).ready(function() {
    // Xử lý submit form tạo ticket
    $("#submitTicket").click(function() {
        var form = document.getElementById('addTicketForm');
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }

        var subject = $('#subject').val();
        var category = $('#category').val();
        var order_id = $('#orderID').val();
        var content = $('#ticketContent').val();
        var $btn = $(this);

        // Thay đổi trạng thái nút
        $btn.find('.btn-text').addClass('d-none');
        $btn.find('.btn-spinner').removeClass('d-none');
        $btn.prop('disabled', true);

        $.ajax({
            url: '<?=base_url('ajaxs/client/ticket.php');?>',
            type: 'POST',
            data: {
                action: 'createTicket',
                token: '<?=$getUser['token'];?>',
                subject: subject,
                category: category,
                order_id: order_id,
                content: content
            },
            dataType: 'json',
            success: function(response) {
                if (response.status == 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?=__('Thành công');?>!',
                        text: response.msg,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        $('#addTicketModal').modal('hide');
                        location.reload();
                    });
                } else {
                    showMessage(response.msg, 'error');
                }
            },
            error: function() {
                showMessage('<?=__('Không thể kết nối đến server');?>', 'error');
            },
            complete: function() {
                // Khôi phục trạng thái nút
                $btn.find('.btn-spinner').addClass('d-none');
                $btn.find('.btn-text').removeClass('d-none');
                $btn.prop('disabled', false);
            }
        });
    });
    
    // Reset form khi đóng modal
    $("#addTicketModal").on("hidden.bs.modal", function() {
        $("#addTicketForm")[0].reset();
        $("#orderIdField").hide(); // Ẩn field mã đơn hàng khi đóng modal
    });
    
    // Xử lý thay đổi category
    $("#category").on("change", function() {
        var selectedCategory = $(this).val();
        var orderField = $("#orderIdField");
        
        if (selectedCategory === "order") {
            orderField.show();
        } else {
            orderField.hide();
            $("#orderID").val(""); // Clear giá trị khi ẩn
        }
    });
    
    // Trigger change event khi modal mở để set trạng thái ban đầu
    $("#addTicketModal").on("shown.bs.modal", function() {
        $("#category").trigger("change");
    });

});
</script>

<script>
flatpickr("#flatpickr-range", {
    mode: "range",
    dateFormat: "Y-m-d",
    enableTime: false,
    altInput: true,
    altFormat: "d/m/Y",
    defaultDate: "<?=$time_filter;?>",
    locale: {
        firstDayOfWeek: 1,
        weekdays: {
            shorthand: [
                "<?=__('CN');?>",
                "<?=__('T2');?>",
                "<?=__('T3');?>",
                "<?=__('T4');?>",
                "<?=__('T5');?>",
                "<?=__('T6');?>",
                "<?=__('T7');?>"
            ],
            longhand: [
                "<?=__('Chủ Nhật');?>",
                "<?=__('Thứ 2');?>",
                "<?=__('Thứ 3');?>",
                "<?=__('Thứ 4');?>",
                "<?=__('Thứ 5');?>",
                "<?=__('Thứ 6');?>",
                "<?=__('Thứ 7');?>"
            ]
        },
        months: {
            shorthand: [
                "<?=__('Th1');?>",
                "<?=__('Th2');?>",
                "<?=__('Th3');?>",
                "<?=__('Th4');?>",
                "<?=__('Th5');?>",
                "<?=__('Th6');?>",
                "<?=__('Th7');?>",
                "<?=__('Th8');?>",
                "<?=__('Th9');?>",
                "<?=__('Th10');?>",
                "<?=__('Th11');?>",
                "<?=__('Th12');?>"
            ],
            longhand: [
                "<?=__('Tháng 1');?>",
                "<?=__('Tháng 2');?>",
                "<?=__('Tháng 3');?>",
                "<?=__('Tháng 4');?>",
                "<?=__('Tháng 5');?>",
                "<?=__('Tháng 6');?>",
                "<?=__('Tháng 7');?>",
                "<?=__('Tháng 8');?>",
                "<?=__('Tháng 9');?>",
                "<?=__('Tháng 10');?>",
                "<?=__('Tháng 11');?>",
                "<?=__('Tháng 12');?>"
            ]
        }
    }
});
</script>