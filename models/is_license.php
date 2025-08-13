@ -1,269 +1 @@
<?php
if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$CMSNT = new DB();

if($CMSNT->site('license_key') == '' || checkLicenseKey($CMSNT->site('license_key'))['status'] != true){
    if (isset($_POST['btnSaveLicense'])) {
        if ($CMSNT->site('status_demo') != 0) {
            die('<script type="text/javascript">if(!alert("Không được dùng chức năng này vì đây là trang web demo.")){window.history.back().location.reload();}</script>');
        }
        foreach ($_POST as $key => $value) {
            $CMSNT->update("settings", array(
                'value' => $value
            ), " `name` = '$key' ");
        }
        $checkKey = checkLicenseKey($CMSNT->site('license_key'));
        if($checkKey['status'] != true){
            die('<script type="text/javascript">if(!alert("'.$checkKey['msg'].'")){window.history.back().location.reload();}</script>');
        }
        die('<script type="text/javascript">if(!alert("Lưu thành công !")){window.history.back().location.reload();}</script>');
    } ?>

<div class="main-content app-content">
    <div class="container-fluid">
        <div class="d-md-flex d-block align-items-center justify-content-between my-4 page-header-breadcrumb">
            <h1 class="page-title fw-semibold fs-18 mb-0">License</h1>
            <div class="ms-md-1 ms-0">

            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h3 class="card-title">THÔNG TIN BẢN QUYỀN CODE</h3>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="form-group row mb-3">
                                <label class="col-sm-4 col-form-label">Mã bản quyền (license key)</label>
                                <div class="col-sm-8">
                                    <div class="form-line">
                                        <input type="text" name="license_key"
                                            placeholder="Nhập mã bản quyền của bạn để sử dụng chức năng này"
                                            value="<?=$CMSNT->site('license_key');?>" class="form-control" required>
                                    </div>
                                </div>
                            </div>
                            <center>
                                <button type="submit" name="btnSaveLicense" class="btn btn-primary btn-block">
                                    <span>Save</span></button>
                            </center>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card custom-card">
                    <div class="card-header justify-content-between">
                        <h3 class="card-title">HƯỚNG DẪN</h3>
                    </div>
                    <div class="card-body">
                        <p>Quý khách có thể lấy License key tại đây: <a target="_blank"
                                href="https://client.cmsnt.co/clientarea.php?action=products&module=licensing">https://client.cmsnt.co/clientarea.php?action=products&module=licensing</a>
                        </p>
                        <p>Chỉ áp dúng cho những ai mua chính hãng, không hỗ trợ những trường hợp mua lại hay sử dụng mã nguồn
                            lậu.</p>
                        <p>Nếu bạn chưa mua code tại CMSNT.CO, bạn có thể mua giấy phép tại đây: <a target="_blank"
                                href="https://www.cmsnt.co/">CLIENT
                                CMSNT</a></p>
                                <p>Việc mua chính hãng sẽ giúp website bạn uy tín hơn trong mắt khách hàng và đối tác.</p>
                        <img src="https://i.imgur.com/VzDVIx0.png" width="100%">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<?php 
    require_once(__DIR__."/../views/admin/footer.php");
?>
<?php die(); }  ?>
 