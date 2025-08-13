<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Recharge Tmweasyapi Thailand'),
    'desc'   => 'CMSNT Panel',
    'keyword' => 'cmsnt, CMSNT, cmsnt.co,'
];
$body['header'] = '
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.6/clipboard.min.js"></script>

';
$body['footer'] = '
<!-- ckeditor -->
<script src="'.BASE_URL('public/ckeditor/ckeditor.js').'"></script>
';
require_once(__DIR__.'/../../models/is_admin.php');
require_once(__DIR__.'/header.php');
require_once(__DIR__.'/sidebar.php');
require_once(__DIR__.'/nav.php');
 
if(checkPermission($getUser['admin'], 'view_recharge') != true){
    die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
}


if (isset($_POST['SaveSettings'])) {
    if ($CMSNT->site('status_demo') != 0) {
        die('<script type="text/javascript">if(!alert("'.__('This function cannot be used because this is a demo site').'")){window.history.back().location.reload();}</script>');
    }
    if(checkPermission($getUser['admin'], 'edit_recharge') != true){
        die('<script type="text/javascript">if(!alert("'.__('Bạn không có quyền sử dụng tính năng này').'")){window.history.back();}</script>');
    }
    $CMSNT->insert("logs", [
        'user_id'       => $getUser['id'],
        'ip'            => myip(),
        'device'        => getUserAgent(),
        'createdate'    => gettime(),
        'action'        => __('Cấu hình nạp tiền Tmweasyapi Thailand')
    ]);
    foreach ($_POST as $key => $value) {
        $CMSNT->update("settings", array(
            'value' => $value
        ), " `name` = '$key' ");
    }
    /** NOTE ACTION */
    $my_text = $CMSNT->site('noti_action');
    $my_text = str_replace('{domain}', $_SERVER['SERVER_NAME'], $my_text);
    $my_text = str_replace('{username}', $getUser['username'], $my_text);
    $my_text = str_replace('{action}', __('Cấu hình nạp tiền Tmweasyapi Thailand'), $my_text);
    $my_text = str_replace('{ip}', myip(), $my_text);    
    $my_text = str_replace('{time}', gettime(), $my_text);
    sendMessAdmin($my_text);
    die('<script type="text/javascript">if(!alert("'.__('Lưu thành công!').'")){window.history.back().location.reload();}</script>');
} 

if(isset($_GET['limit'])){
    $limit = intval(check_string($_GET['limit']));
}else{
    $limit = 10;
}
if(isset($_GET['page'])){
    $page = check_string(intval($_GET['page']));
}
else{
    $page = 1;
}
$from = ($page - 1) * $limit;
$where = "  `id` > 0 ";
$shortByDate = '';
$trans_id = '';
$createdate = '';
$amount = '';
$user_id = '';
$username = '';
$status = '';

if(!empty($_GET['status'])){
    $status = check_string($_GET['status']);
    if($status == 1){
        $where .= ' AND `status` = 0 ';
    }else if($status == 2){
        $where .= ' AND `status` = 1 ';
    }else if($status == 3){
        $where .= ' AND `status` = 2 ';
    }
}
if (!empty($_GET['username'])) {
    $username = check_string($_GET['username']);
    if($idUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `username` = '$username' ")){
        $where .= ' AND `user_id` =  "'.$idUser['id'].'" ';
    }else{
        $where .= ' AND `user_id` =  "" ';
    }
}
if(!empty($_GET['user_id'])){
    $user_id = check_string($_GET['user_id']);
    $where .= ' AND `user_id` = '.$user_id.' ';
}

if(!empty($_GET['trans_id'])){
    $trans_id = check_string($_GET['trans_id']);
    $where .= ' AND `trans_id` LIKE "%'.$trans_id.'%" ';
}
if(!empty($_GET['amount'])){
    $amount = check_string($_GET['amount']);
    $where .= ' AND `amount` = '.$amount.' ';
}
if(!empty($_GET['created_at'])){
    $created_at = check_string($_GET['created_at']);
    $createdate = $created_at;
    $created_at_1 = str_replace('-', '/', $created_at);
    $created_at_1 = explode(' to ', $created_at_1);

    if($created_at_1[0] != $created_at_1[1]){
        $created_at_1 = [$created_at_1[0].' 00:00:00', $created_at_1[1].' 23:59:59'];
        $where .= " AND `created_at` >= '".$created_at_1[0]."' AND `created_at` <= '".$created_at_1[1]."' ";
    }
}
if(isset($_GET['shortByDate'])){
    $shortByDate = check_string($_GET['shortByDate']);
    $yesterday = date('Y-m-d', strtotime("-1 day"));
    $currentWeek = date("W");
    $currentMonth = date('m');
    $currentYear = date('Y');
    $currentDate = date("Y-m-d");
    if($shortByDate == 1){
        $where .= " AND `created_at` LIKE '%".$currentDate."%' ";
    }
    if($shortByDate == 2){
        $where .= " AND YEAR(created_at) = $currentYear AND WEEK(created_at, 1) = $currentWeek ";
    }
    if($shortByDate == 3){
        $where .= " AND MONTH(created_at) = '$currentMonth' AND YEAR(created_at) = '$currentYear' ";
    }

}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `payment_tmweasyapi` WHERE $where ORDER BY `id` DESC LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `payment_tmweasyapi` WHERE $where ORDER BY id DESC ");
$urlDatatable = pagination(base_url_admin("recharge-tmweasyapi&limit=$limit&shortByDate=$shortByDate&created_at=$createdate&trans_id=$trans_id&amount=$amount&user_id=$user_id&username=$username&status=$status&"), $from, $totalDatatable, $limit);

$yesterday = date('Y-m-d', strtotime("-1 day")); // hôm qua
$currentWeek = date("W");
$currentMonth = date('m');
$currentYear = date('Y');
$currentDate = date("Y-m-d");


$total_yesterday = intval($CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 AND  `created_at` LIKE '%".$yesterday."%' ")['SUM(price)']);
$total_today = $CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 AND `created_at` LIKE '%".$currentDate."%' ")['SUM(price)'];
$total_all_time = $CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 ")['SUM(price)'];


$checkKey = checkAddonLicense($CMSNT->site('tmweasyapi_license'), 'SMMPANEL2_GATEWAY_tmweasyapi');
 
?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0"><?=__('Phương thức thanh toán Tmweasyapi Thailand');?></h1>
        </div>
        <div class="row">
            <div class="col-xl-12">
                <div class="text-right">
                    <button type="button" id="open-card-config" class="btn btn-primary label-btn mb-3">
                        <i class="ri-settings-4-line label-btn-icon me-2"></i> <?=__('Cấu hình');?> 
                    </button>
                </div>
            </div>
            <div class="col-xl-12" id="card-config" style="display: none;">
                <div class="card custom-card">
                    <div class="card-body">
                        <form action="" method="POST" enctype="multipart/form-data">
                            <div class="row">
                                <?php if($checkKey['status'] != true):?>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-7 col-form-label"
                                            for="example-hf-email"><?=__('Trạng thái');?></label>
                                        <div class="col-sm-5">
                                            <select class="form-control" name="tmweasyapi_status">
                                                <option <?=$CMSNT->site('tmweasyapi_status') == 1 ? 'selected' : '';?>
                                                    value="1">ON
                                                </option>
                                                <option <?=$CMSNT->site('tmweasyapi_status') == 0 ? 'selected' : '';?>
                                                    value="0">
                                                    OFF
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-7 col-form-label" for="example-hf-email"><?=__('Giấy phép kích hoạt Addon');?></label>
                                        <div class="col-sm-5">
                                            <input type="text" class="form-control"
                                                placeholder="921abf4dbff01xxxxxf3c562c356c769"
                                                value="<?=$CMSNT->site('tmweasyapi_license');?>" name="tmweasyapi_license">
                                        </div>
                                        <div
                                            style="margin-top: 10px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; border-radius: 4px; color: #856404;">
                                            <strong><?=__('Chú ý');?>:</strong> <?=__('Bạn cần phải mua giấy phép kích hoạt');?> <a
                                                target="_blank" style="color: #007bff;"
                                                href="https://client.cmsnt.co/cart.php?a=add&pid=86"><?=__('Addon');?></a> <?=__('trước khi sử dụng');?>.
                                        </div>
                                    </div>
                                </div>
                                <?php else:?>
                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-7 col-form-label"
                                            for="example-hf-email"><?=__('Trạng thái');?></label>
                                        <div class="col-sm-5">
                                            <select class="form-control" name="tmweasyapi_status">
                                                <option <?=$CMSNT->site('tmweasyapi_status') == 1 ? 'selected' : '';?>
                                                    value="1">ON
                                                </option>
                                                <option <?=$CMSNT->site('tmweasyapi_status') == 0 ? 'selected' : '';?>
                                                    value="0">
                                                    OFF
                                                </option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="tmweasyapi_username">Username</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="tmweasyapi_username"
                                                value="<?=$CMSNT->site('tmweasyapi_username');?>"
                                                name="tmweasyapi_username">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="tmweasyapi_password">Password</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="tmweasyapi_password"
                                                value="<?=$CMSNT->site('tmweasyapi_password');?>"
                                                name="tmweasyapi_password">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="tmweasyapi_con_id">con_id</label>
                                        <div class="col-sm-8">
                                            <input type="text" class="form-control" id="tmweasyapi_con_id"
                                                value="<?=$CMSNT->site('tmweasyapi_con_id');?>"
                                                name="tmweasyapi_con_id">
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-12 col-xl-6">
                                    <div class="row mb-4">
                                        <label class="col-sm-7 col-form-label"
                                            for="example-hf-email"><?=__('1 THB =');?></label>
                                        <div class="col-sm-5">
                                            <div class="input-group">
                                                <input type="text" class="form-control"
                                                    value="<?=$CMSNT->site('tmweasyapi_rate');?>" name="tmweasyapi_rate"
                                                    placeholder="">
                                                <span class="input-group-text">
                                                    <?=$CMSNT->get_row(" SELECT `code` FROM `currencies` WHERE `display` = 1 AND `default_currency` = 1")['code'];?>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Min</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control"
                                                value="<?=$CMSNT->site('tmweasyapi_min');?>" name="tmweasyapi_min">
                                        </div>
                                    </div>
                                    <div class="row mb-4">
                                        <label class="col-sm-4 col-form-label" for="example-hf-email">Max</label>
                                        <div class="col-sm-8">
                                            <input type="number" class="form-control"
                                                value="<?=$CMSNT->site('tmweasyapi_max');?>" name="tmweasyapi_max">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-12 col-xl-12">
                                    <div class="row mb-4">
                                        <label class="col-sm-6 col-form-label"
                                            for="example-hf-email"><?=__('Note');?></label>
                                        <div class="col-sm-12">
                                            <textarea id="tmweasyapi_notice"
                                                name="tmweasyapi_notice"><?=$CMSNT->site('tmweasyapi_notice');?></textarea>
                                        </div>
                                    </div>
                                </div>
                                <?php endif?>
                            </div>
                            <div class="d-grid gap-2 mb-4">
                                <button type="submit" name="SaveSettings" class="btn btn-primary btn-block"><i
                                        class="fa fa-fw fa-save me-1"></i>
                                    <?=__('Save');?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-xl-5">
                <div class="row">
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?=format_currency($total_all_time);?></p>
                                        <p class="mb-0 text-muted"><?=__('Toàn thời gian');?></p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-danger rounded-circle fs-20"><i
                                                class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?=format_currency($CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 AND MONTH(created_at) = '$currentMonth' AND YEAR(created_at) = '$currentYear' ")['SUM(price)']);?>
                                        </p>
                                        <p class="mb-0 text-muted"><?=__('Tháng');?> <?=date('m');?></p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-info rounded-circle fs-20"><i
                                                class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?=format_currency($CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 AND YEAR(created_at) = $currentYear AND WEEK(created_at, 1) = $currentWeek ")['SUM(price)']);?>
                                        </p>
                                        <p class="mb-0 text-muted"><?=__('Trong tuần');?></p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-warning rounded-circle fs-20"><i
                                                class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-6">
                        <div class="card custom-card">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-fill">
                                        <p class="mb-1 fs-5 fw-semibold text-default">
                                            <?=format_currency($total_today);?></p>
                                        <p class="mb-0 text-muted"><?=__('Hôm nay');?>
                                            <?php 
if ($total_yesterday != 0) {
    $revenueGrowth = ($total_today - $total_yesterday) / $total_yesterday * 100;
    if ($revenueGrowth > 0) {
        // tăng
      echo '<span class="fs-12 text-success ms-2"><i class="ti ti-trending-up me-1 d-inline-block"></i>' . round($revenueGrowth, 2) . '% </span>';
    } else if ($revenueGrowth < 0) {
        // giảm
      echo '<span class="fs-12 text-danger ms-2"><i class="ti ti-trending-down me-1 d-inline-block"></i>' . round(abs($revenueGrowth), 2) . '% </span>';
    }
  }
                            ?>

                                        </p>
                                    </div>
                                    <div class="ms-2">
                                        <span class="avatar text-bg-primary rounded-circle fs-20"><i
                                                class='bx bxs-wallet-alt'></i></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-7">
                <div class="card custom-card">
                    <div class="card-header">
                        <div class="card-title"><?=__('THỐNG KÊ NẠP TIỀN THÁNG');?> <?=date('m');?></div>
                    </div>
                    <div class="card-body">
                        <canvas id="chartjs-line" class="chartjs-chart"></canvas>
                        <script>
                        (function() {
                            /* line chart  */
                            Chart.defaults.borderColor = "rgba(142, 156, 173,0.1)", Chart.defaults.color =
                                "#8c9097";
                            const labels = [
                                <?php
              $month = date('m');
              $year = date('Y');
              $numOfDays = cal_days_in_month(CAL_GREGORIAN, $month, $year);

              for ($day = 1; $day <= $numOfDays; $day++) {
                  echo "\"$day/$month/$year\",";
              }
              ?>
                            ];
                            const data = {
                                labels: labels,
                                datasets: [{
                                    label: 'Paid',
                                    backgroundColor: 'rgb(132, 90, 223)',
                                    borderColor: 'rgb(132, 90, 223)',
                                    data: [
                                        <?php
                  $data = [];
                  for ($day = 1; $day <= $numOfDays; $day++) {
                      $date = "$year-$month-$day";
                      $row = $CMSNT->get_row("SELECT SUM(price) FROM payment_tmweasyapi WHERE `status` = 1 AND DATE(created_at) = '$date' ");
                      $data[$day - 1] = $row['SUM(price)'];
                  }
                  for ($i = 0; $i < $numOfDays; $i++) {
                      echo "$data[$i],";
                  }
                  ?>
                                    ],
                                }]
                            };
                            const config = {
                                type: 'bar',
                                data: data,
                                options: {}
                            };
                            const myChart = new Chart(
                                document.getElementById('chartjs-line'),
                                config
                            );



                        })();
                        </script>
                    </div>
                </div>
            </div>
            <div class="col-xl-12">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <div class="card-title">
                            <?=__('LỊCH SỬ NẠP TIỀN');?>
                        </div>
                    </div>
                    <div class="card-body">
                        <form action="<?= base_url(); ?>" class="align-items-center mb-3" name="formSearch" method="GET">
                            <div class="row row-cols-lg-auto g-3 mb-3">
                                <input type="hidden" name="module" value="<?=$CMSNT->site('path_admin');?>">
                                <input type="hidden" name="action" value="recharge-tmweasyapi">
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?=$user_id;?>" name="user_id"
                                        placeholder="<?=__('Tìm kiếm ID User');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?=$username;?>" name="username"
                                        placeholder="<?=__('Tìm kiếm Username');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?=$trans_id;?>" name="trans_id"
                                        placeholder="<?=__('Mã giao dịch');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <input class="form-control form-control-sm" value="<?=$amount;?>" name="amount"
                                        placeholder="<?=__('Số lượng');?>">
                                </div>
                                <div class="col-md-3 col-6">
                                    <select name="status" class="form-control form-control-sm">
                                        <option value=""><?=__('Trạng thái');?></option>
                                        <option <?=$status == 1 ? 'selected' : '';?> value="1"><?=__('Chờ thanh toán');?>
                                        </option>
                                        <option <?=$status == 2 ? 'selected' : '';?> value="2"><?=__('Đã thanh toán');?>
                                        </option>
                                        <option <?=$status == 3 ? 'selected' : '';?> value="3"><?=__('Hết hạn');?>
                                        </option>
                                    </select>
                                </div>
                                <div class="col-lg col-md-4 col-6">
                                    <input type="text" name="created_at" class="form-control form-control-sm"
                                        id="daterange" value="<?=$createdate;?>" placeholder="<?=__('Chọn thời gian');?>">
                                </div>
                                <div class="col-12">
                                    <button class="btn btn-sm btn-primary"><i class="fa fa-search"></i>
                                        <?=__('Tìm kiếm');?>
                                    </button>
                                    <a class="btn btn-sm btn-danger" href="<?=base_url_admin('recharge-tmweasyapi');?>"><i
                                            class="fa fa-trash"></i>
                                        <?=__('Xóa bộ lọc');?>
                                    </a>
                                </div>
                            </div>
                            <div class="top-filter">
                                <div class="filter-show">
                                    <label class="filter-label"><?=__('Show');?> :</label>
                                    <select name="limit" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option <?=$limit == 5 ? 'selected' : '';?> value="5">5</option>
                                        <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                        <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                        <option <?=$limit == 50 ? 'selected' : '';?> value="50">50</option>
                                        <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                        <option <?=$limit == 500 ? 'selected' : '';?> value="500">500</option>
                                        <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000
                                        </option>
                                    </select>
                                </div>
                                <div class="filter-short">
                                    <label class="filter-label"><?=__('Short by Date');?> :</label>
                                    <select name="shortByDate" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option value=""><?=__('Tất cả');?></option>
                                        <option <?=$shortByDate == 1 ? 'selected' : '';?> value="1">
                                            <?=__('Hôm nay');?>
                                        </option>
                                        <option <?=$shortByDate == 2 ? 'selected' : '';?> value="2">
                                            <?=__('Tuần này');?>
                                        </option>
                                        <option <?=$shortByDate == 3 ? 'selected' : '';?> value="3">
                                            <?=__('Tháng này');?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </form>
                        <div class="table-responsive mb-3">
                            <table class="table text-nowrap table-striped table-hover table-bordered">
                                <thead>
                                    <tr class="text-center">
                                        <th class="text-center"><?=__('Username');?></th>
                                        <th class="text-center"><?=__('Mã giao dịch');?></th>
                                        <th class="text-center"><?=__('Số lượng');?></th>
                                        <th class="text-center"><?=__('Thực nhận');?></th>
                                        <th class="text-center"><?=__('Trạng thái');?></th>
                                        <th class="text-center"><?=__('Create date');?></th>
                                        <th class="text-center"><?=__('Update date');?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php  foreach ($listDatatable as $row): ?>
                                    <tr>
                                        <td class="text-center"><a class="text-primary"
                                                href="<?=base_url_admin('user-edit&id='.$row['user_id']);?>"><?=getRowRealtime("users", $row['user_id'], "username");?>
                                                [ID <?=$row['user_id'];?>]</a>
                                        </td>
                                        <td class="text-center"><b><?=$row['trans_id'];?></b></td>
                                        <td class="text-right"><b style="color:blue;"><?=format_cash($row['amount']);?> bath</b></td>
                                        <td class="text-right"><b style="color:red;"><?=format_currency($row['price']);?></b></td>
                                        <td class="text-center"><?=display_invoice($row['status']);?></td>
                                        <td class="text-center"><?=$row['created_at'];?></td>
                                        <td class="text-center"><?=$row['updated_at'];?></td>
                                    </tr>
                                    <?php endforeach?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="7">
                                            <div class="float-right">
                                                <?=__('Paid:');?>
                                                <strong
                                                    style="color:red;"><?=format_currency($CMSNT->get_row(" SELECT SUM(`price`) FROM `payment_tmweasyapi` WHERE $where AND `status` = 1 ")['SUM(`price`)']);?></strong>

                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-sm-12 col-md-5">
                                <p class="dataTables_info"><?=__('Showing');?> <?=$limit;?> <?=__('of');?>
                                    <?=format_cash($totalDatatable);?>
                                    <?=__('Results');?></p>
                            </div>
                            <div class="col-sm-12 col-md-7 mb-3">
                                <?=$totalDatatable > $limit ? $urlDatatable : '';?>
                            </div>
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
document.addEventListener('DOMContentLoaded', function() {
    var button = document.getElementById('open-card-config');
    var card = document.getElementById('card-config');

    // Thêm sự kiện click cho nút button
    button.addEventListener('click', function() {
        // Kiểm tra nếu card đang hiển thị thì ẩn đi, ngược lại hiển thị
        if (card.style.display === 'none' || card.style.display === '') {
            card.style.display = 'block';
        } else {
            card.style.display = 'none';
        }
    });
});
</script>
<script>
CKEDITOR.replace("tmweasyapi_notice");
</script>

<script type="text/javascript">
new ClipboardJS(".copy");

function copy() {
    showMessage("<?=__('Đã sao chép vào bộ nhớ tạm');?>", 'success');
}
</script>