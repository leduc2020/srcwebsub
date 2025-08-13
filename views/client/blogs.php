<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
$body = [
    'title' => __('Blogs').' | '.$CMSNT->site('title'),
    'desc'   => $CMSNT->site('description'),
    'keyword' => $CMSNT->site('keywords')
];
$body['header'] = '
<link rel="stylesheet" href="'.BASE_URL('public/client/').'css/blog-grid.css">
';
$body['footer'] = '
 

 
';

if (isSecureCookie('user_login') == true) {
    require_once(__DIR__ . '/../../models/is_user.php');
}

require_once(__DIR__.'/header.php');
require_once(__DIR__.'/nav.php');


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
$where = " `status` = 1 ";
$keyword = '';
$shortby = 1;
$category = '';

if(!empty($_GET['category'])){
    $category = check_string($_GET['category']);
    $where .= ' AND `category_id` = "'.$category.'" ';
}
if(!empty($_GET['keyword'])){
    $keyword = check_string($_GET['keyword']);
    $where .= ' AND `title` LIKE "%'.$keyword.'%" ';
}
if(!empty($_GET['time'])){
    $time = check_string($_GET['time']);
    $create_date_1 = str_replace('-', '/', $time);
    $create_date_1 = explode(' to ', $create_date_1);
    if($create_date_1[0] != $create_date_1[1]){
        $create_date_1 = [$create_date_1[0].' 00:00:00', $create_date_1[1].' 23:59:59'];
        $where .= " AND `create_gettime` >= '".$create_date_1[0]."' AND `create_gettime` <= '".$create_date_1[1]."' ";
    }
}

if(isset($_GET['shortby'])){
    $shortby = check_string($_GET['shortby']);
}
if($shortby == 1){
    $where .= " ORDER BY `id` DESC ";
}
if($shortby == 2){
    $where .= " ORDER BY `view` DESC ";
}

$listDatatable = $CMSNT->get_list(" SELECT * FROM `posts` WHERE $where LIMIT $from,$limit ");
$totalDatatable = $CMSNT->num_rows(" SELECT * FROM `posts` WHERE $where ");
$urlDatatable = pagination_client(base_url("?action=blogs&limit=$limit&keyword=$keyword&shortby=$shortby&category=$category&"), $from, $totalDatatable, $limit);
?>
<?php if($category != 0):?>
<section class="inner-section single-banner"
    style="background: url(<?=base_url(getRowRealtime('post_category', $category, 'icon'));?>) no-repeat center;">
    <div class="container">
        <h2><?=getRowRealtime('post_category', $category, 'name');?></h2>
    </div>
</section>
<?php endif?>
<section class="inner-section <?=$category == 0 ? 'py-5' : '';?> blog-grid">
    <form action="<?=base_url();?>" method="GET">
        <input type="hidden" name="action" value="blogs">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="top-filter">
                                <div class="filter-show"><label class="filter-label">Show :</label>
                                    <select name="limit" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option <?=$limit == 10 ? 'selected' : '';?> value="10">10</option>
                                        <option <?=$limit == 20 ? 'selected' : '';?> value="20">20</option>
                                        <option <?=$limit == 40 ? 'selected' : '';?> value="40">40</option>
                                        <option <?=$limit == 100 ? 'selected' : '';?> value="100">100</option>
                                        <option <?=$limit == 400 ? 'selected' : '';?> value="400">400</option>
                                        <option <?=$limit == 1000 ? 'selected' : '';?> value="1000">1000</option>
                                    </select>
                                </div>
                                <div class="filter-action"><label class="filter-label">Short by :</label>
                                    <select name="shortby" onchange="this.form.submit()"
                                        class="form-select filter-select">
                                        <option <?=$shortby == 1 ? 'selected' : '';?> value="1"><?=__('Mặc định');?>
                                        </option>
                                        <option <?=$shortby == 2 ? 'selected' : '';?> value="2"><?=__('Phổ biến');?>
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <?php foreach ($listDatatable as $row):?>
                        <div class="col-md-6 col-lg-6">
                            <div class="blog-card">
                                <div class="blog-media"><a class="blog-img"
                                        href="<?=base_url('blog/'.$row['slug']);?>"><img
                                            style="width: 100%; height: 300px;" src="<?=base_url($row['image']);?>"
                                            alt="blog"></a></div>
                                <div class="blog-content">
                                    <ul class="blog-meta">
                                        <li><i
                                                class="fas fa-user"></i><span><?=getRowRealtime('users', $row['user_id'], 'fullname');?></span>
                                        </li>
                                        <li><i class="fas fa-calendar-alt"></i><span><?=$row['create_gettime'];?></span>
                                        </li>
                                    </ul>
                                    <h4 class="blog-title"><a
                                            href="<?=base_url('blog/'.$row['slug']);?>"><?=$row['title'];?></a></h4>
                                    <p class="blog-desc">
                                        <?=strip_tags(substr(base64_decode($row['content']), 0, 200)) . ' ...';?></p><a
                                        class="blog-btn"
                                        href="<?=base_url('blog/'.$row['slug']);?>"><span><?=__('Xem thêm');?></span><i
                                            class="icofont-arrow-right"></i></a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach?>

                    </div>
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="bottom-paginate">
                                <p class="page-info">Showing <?=$limit;?> of <?=$totalDatatable;?> Results</p>
                                <div class="pagination">
                                    <?=$totalDatatable > $limit ? $urlDatatable : '';?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="blog-widget">
                        <h3 class="blog-widget-title"><?=__('Tìm kiếm bài viết');?></h3>
                        <form class="blog-widget-form"></form>
                        <form class="blog-widget-form" action="<?=base_url();?>" method="GET">
                            <input type="hidden" name="action" value="blogs">
                            <input type="text" name="keyword" value="<?=$keyword;?>"
                                placeholder="<?=__('Search blogs');?>">
                            <button class="icofont-search-1"></button>
                        </form>
                    </div>
                    <div class="blog-widget">
                        <h3 class="blog-widget-title"><?=__('Bài viết phổ biến');?></h3>
                        <ul class="blog-widget-feed">
                            <?php foreach($CMSNT->get_list(" SELECT * FROM `posts` WHERE `status` = 1 ORDER BY `view` DESC ") as $popular):?>
                            <li>
                                <a class="blog-widget-media" href="<?=base_url('blog/'.$popular['slug']);?>"><img
                                        style="height: 100%;" src="<?=base_url($popular['image']);?>"
                                        alt="blog-widget"></a>
                                <h6 class="blog-widget-text"><a
                                        href="<?=base_url('blog/'.$popular['slug']);?>"><?=$popular['title'];?></a><span
                                        class="fw-bold text-dark"><?=getRowRealtime('post_category', $popular['category_id'], 'name');?></span>
                                </h6>
                            </li>
                            <?php endforeach?>

                        </ul>
                    </div>
                    <div class="blog-widget">
                        <h3 class="blog-widget-title"><?=__('Chuyên mục');?></h3>
                        <ul class="blog-widget-category">
                            <?php foreach($CMSNT->get_list(" SELECT * FROM `post_category` WHERE `status` = 1 ") as $category):?>
                            <li><a href="<?=base_url('?action=blogs&category='.$category['id']);?>"><?=$category['name'];?>
                                    <span><?=$CMSNT->get_row(" SELECT COUNT(id) FROM `posts` WHERE `category_id` = '".$category['id']."' ")['COUNT(id)'];?></span></a>
                            </li>
                            <?php endforeach?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>
</section>


<?php
require_once(__DIR__.'/footer.php');
?>