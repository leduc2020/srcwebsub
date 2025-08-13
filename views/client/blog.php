<?php if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

if(isset($_GET['slug'])){
    if(!$row = $CMSNT->get_row(" SELECT * FROM `posts` WHERE `slug` = '".check_string($_GET['slug'])."' ")){
        redirect(base_url('blogs'));
    }
    $CMSNT->cong('posts', 'view', 1, " `id` =  '".$row['id']."' ");
    
}else{
    redirect(base_url('blogs'));
} 

$body = [
    'title' => __($row['title']).' | '.$CMSNT->site('title'),
    'desc'   => check_string(substr(base64_decode($row['content']), 0, 300)) . ' ...',
    'keyword' => $CMSNT->site('keywords'),
    'image' => $row['image']
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
?>
<section class="inner-section single-banner" style="background: url(<?=base_url($row['image']);?>) no-repeat center;">
    <div class="container">
        <h2><?=$row['title'];?></h2>
    </div>
</section>
 
<section class="inner-section blog-grid">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="blog-widget">
                    <?=base64_decode($row['content']);?>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="blog-widget">
                    <h3 class="blog-widget-title"><?=__('Bài viết phổ biến');?></h3>
                    <ul class="blog-widget-feed">
                        <?php foreach($CMSNT->get_list(" SELECT * FROM `posts` WHERE `status` = 1 ORDER BY `view` DESC ") as $popular):?>
                        <li>
                            <a class="blog-widget-media" href="<?=base_url('blog/'.$popular['slug']);?>">
                                <img style="height: 100%;" src="<?=base_url($popular['image']);?>" alt="blog-widget">
                            </a>
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
</section>


<?php
require_once(__DIR__.'/footer.php');
?>