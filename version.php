<!-- Developer By CMSNT.CO | FB.COM/CMSNT.CO | ZALO.ME/0947838128 | MMO Solution -->
<?php
define("IN_SITE", true);
require_once(__DIR__.'/libs/db.php');
require_once(__DIR__.'/libs/lang.php');
require_once(__DIR__.'/libs/helper.php');
require_once(__DIR__.'/config.php');
require_once(__DIR__.'/libs/database/users.php');
$CMSNT = new DB();
?>
<meta name="robots" content="noindex">
<title>Version</title>
<style>
    table {
        width: 100%;
        border-collapse: collapse;
    }

    th, td {
        border: 1px solid black;
        padding: 8px;
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #f5f5f5; /* Màu nền khi hover */
    }

    th:hover {
        background-color: #ddd; /* Màu nền khi hover trên header */
    }

    td:hover {
        background-color: #eaeaea; /* Màu nền khi hover trên cell */
    }
</style>
</head>
<body>

<h2>Thông tin hệ thống</h2>

<table>
    <tbody>
        <tr>
            <td>Project</td>
            <td><b><?=$config['project'];?></b></td>
        </tr>
        <tr>
            <td>Phiên bản cập nhật</td>
            <td><b style="color:red;"><?=$config['version'];?></b></td>
        </tr>
        <tr>
            <td>Tự động cập nhật phiên bản</td>
            <td><b><?=$CMSNT->site('status_update') == 1 ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>SMTP</td>
            <td><b><?=$CMSNT->site('smtp_status') == 1 ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>Debug Auto Bank</td>
            <td><b><?=$CMSNT->site('debug_auto_bank') == 1 ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>Phiên bản PHP</td>
            <td><b style="color:blue;"><?=phpversion();?></b></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: black;color:white;">Settings</td>
        </tr>
        <tr>
            <td>Số tiền nạp Bank tối thiểu</td>
            <td><b><?=format_currency($CMSNT->site('bank_min'));?></b></td>
        </tr>
        <tr>
            <td>Số tiền nạp Bank tối đa</td>
            <td><b><?=format_currency($CMSNT->site('bank_max'));?></b></td>
        </tr>
        <tr>
            <td>Prefix</td>
            <td><b><?=$CMSNT->site('prefix_autobank');?>{id}</b></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: black;color:white;">PHP Options</td>
        </tr>
        <tr>
            <td>display_errors</td>
            <td><b><?=ini_get('display_errors') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>allow_url_fopen</td>
            <td><b><?=ini_get('allow_url_fopen') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>log_errors</td>
            <td><b><?=ini_get('log_errors') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>max_execution_time</td>
            <td><b><?=ini_get('max_execution_time');?></b></td>
        </tr>
        <tr>
            <td>memory_limit</td>
            <td><b><?=ini_get('memory_limit');?></b></td>
        </tr>
        <tr>
            <td>upload_max_filesize</td>
            <td><b><?=ini_get('upload_max_filesize');?></b></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: black;color:white;">PHP Extension</td>
        </tr>
        <tr>
            <td>Extension calendar</td>
            <td><b><?=extension_loaded('calendar') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>Extension zip</td>
            <td><b><?=extension_loaded('zip') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>Extension gmagick</td>
            <td><b><?=extension_loaded('gmagick') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td>Extension mbstring</td>
            <td><b><?=extension_loaded('mbstring') ? '<span style="color:green;">ON</span>' : '<span style="color:red;">OFF</span>';?></b></td>
        </tr>
        <tr>
            <td colspan="2" style="background-color: black;color:white;">Cron Jobs</td>
        </tr>
    </tbody>
</table>

</body>
</html>
