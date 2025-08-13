<?php

define("IN_SITE", true);
require_once(__DIR__."/../../libs/db.php");
require_once(__DIR__."/../../libs/lang.php");
require_once(__DIR__."/../../libs/helper.php");
require_once(__DIR__."/../../config.php");


// ✅ OPTIMIZATION: Load tất cả service types 1 lần duy nhất để tối ưu hiệu suất
$serviceTypesMapping = [];
$serviceTypesList = $CMSNT->get_list("SELECT * FROM `smm_service_types`");
foreach($serviceTypesList as $serviceType) {
    $serviceTypesMapping[$serviceType['code']] = intval($serviceType['quantity_unit']);
}

/**
 * Helper function để lấy quantity_unit một cách tối ưu
 * @param string $code Service type code
 * @return int quantity_unit (default: 1000)
 */
function getQuantityUnit($code) {
    global $serviceTypesMapping;
    return isset($serviceTypesMapping[$code]) ? $serviceTypesMapping[$code] : 1000;
}

if(!isset($_POST['action'])){
    $data = json_encode([
        'status'    => 'error',
        'msg'       => __('The Request Not Found')
    ]);
    die($data);   
}



if($_POST['action'] == 'totalPrice'){
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $service_id = check_string($service_id);
    if ($service_id <= 0) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('ID dịch vụ không hợp lệ')
        ]));
    }
    $service = $CMSNT->get_row(" SELECT * FROM `services` WHERE `id` = $service_id AND `display` = 'show' ");
    if(!$service){
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Dịch vụ không tồn tại')
        ]));
    }

    $amount = isset($_POST['amount']) ? (int)$_POST['amount'] : 0;
    $amount = check_string($amount);
    if ($amount <= 0) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Số lượng không hợp lệ')
        ]));
    }
    // Trước mắt tính giá chưa discount
    $total_price = $amount * $service['price'];
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
        if($getUser){
            if($getUser['rank_id'] > 0){
                $total_price = $amount * $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $total_price = $total_price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }
    }

    // Tính thuế VAT
    $price          = $total_price; // Số tiền thanh toán ban đầu chưa bao gồm VAT
    $price_vat      = $CMSNT->site('tax_vat') > 0 ? $total_price * $CMSNT->site('tax_vat') / 100 : 0; // Số tiền thuế VAT cần trả thêm
    $total_price    = $price + $price_vat; // Số tiền thanh toán sau khi tính thuế VAT

    die(json_encode([
        'status'        => 'success',
        'msg'           => __('Success'),
        'price'         => format_currency($price),             // Số tiền chưa tính thuế
        'total_price'   => format_currency($total_price),       // Số tiền thanh toán sau khi tính thuế VAT
        'price_vat'     => format_currency($price_vat),         // Số tiền thuế VAT
        'tax_vat'       => floatval($CMSNT->site('tax_vat'))    // Thuế VAT (%)
    ]));
}



if($_POST['action'] == 'getServiceDetails'){
    $service_id = isset($_POST['service_id']) ? (int)$_POST['service_id'] : 0;
    $service_id = check_string($service_id);
    if ($service_id <= 0) {
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('ID dịch vụ không hợp lệ')
        ]));
    }
    $service = $CMSNT->get_row(" SELECT * FROM `services` WHERE `id` = $service_id AND `display` = 'show' ");
    if(!$service){
        die(json_encode([
            'status'    => 'error',
            'msg'       => __('Dịch vụ không tồn tại')
        ]));
    }
    // Giá bán mặc định
    $price = $service['price'];
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
        if(isset($getUser)){
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }
          }
      // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
      $display_price = $price * getQuantityUnit($service['type']);
    
    $data = [
        'type'          => $service['type'],
        'description'   => $service['description'],
        'average_time'  => $service['average_time'] ? timeAgo2($service['average_time']) : 0,
        'price'         => format_currency($display_price),
        'min'           => $service['min'],
        'max'           => $service['max']
    ];
    die(json_encode([
        'status'    => 'success',
        'msg'       => __('Success'),
        'data'      => $data
    ]));
}

if($_POST['action'] == 'searchServices'){
    $keyword = isset($_POST['keyword']) ? check_string($_POST['keyword']) : '';
    
    // SQL query để tìm kiếm dịch vụ
    $sql = " SELECT s.*, c.parent_id as platform_id, c.id as category_id 
              FROM `services` s 
              JOIN `categories` c ON s.category_id = c.id 
              WHERE s.display = 'show' ";
    
    // Nếu có từ khóa thì thêm điều kiện tìm kiếm
    if(!empty($keyword)){
        $sql .= " AND (s.name LIKE '%$keyword%' OR s.id LIKE '%$keyword%') ";
    }
    
    // Sắp xếp và giới hạn số lượng kết quả
    $sql .= " ORDER BY s.id ASC LIMIT 500 ";
    
    $services = $CMSNT->get_list($sql);

    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
    }
    
    $data = [];
    foreach($services as $service){
        $platform = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '".$service['platform_id']."'");
        $category = $CMSNT->get_row("SELECT * FROM `categories` WHERE `id` = '".$service['category_id']."'");
        
        // Giá bán mặc định
        $price = $service['price'];
        if(isset($getUser)){
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }

        // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
        $display_price = $price * getQuantityUnit($service['type']);
        
        $data[] = [
            'id' => $service['id'],
            'name' => $service['name'],
            'text' => $service['name'],
            'price' => format_currency($display_price),
            'platform_id' => $service['platform_id'],
            'category_id' => $service['category_id'],
            'platform_name' => $platform ? $platform['name'] : '',
            'category_name' => $category ? $category['name'] : ''
        ];
    }
    
    die(json_encode([
        'status'    => 'success',
        'msg'       => __('Success'),
        'data'      => $data
    ]));
}

if($_POST['action'] == 'loadInitialData'){
    // ✅ CHECK SETTING: Admin tùy chọn cách load dữ liệu
    $load_data_type = $CMSNT->site('load_data_type');
    
    // ✅ FALLBACK: Nếu chưa cấu hình setting, mặc định dùng 'lazy'
    if (empty($load_data_type)) {
        $load_data_type = 'lazy';
    }
    
    if($load_data_type == 'all') {
        // ➡️ FALLBACK: Sử dụng loadAllData như cũ
        // Thực hiện logic loadAllData trực tiếp
        if(!empty($_POST['token'])){
            $getUserFallback = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
        }
        
        // Load tất cả platforms
        $platforms = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC ");
        
        // Load tất cả categories
        $categories = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` > 0 ORDER BY `stt` DESC ");
        
        // Load tất cả services
        $services = $CMSNT->get_list(" SELECT s.*, c.parent_id as platform_id FROM `services` s JOIN `categories` c ON s.category_id = c.id WHERE s.display = 'show' ORDER BY s.stt DESC ");
        
        $data = [
            'platforms' => [],
            'categories' => [],
            'services' => [],
            'load_mode' => 'all' // ✅ Thông báo cho frontend biết mode
        ];
        
        // Tạo mapping platform icons để tối ưu performance (chỉ load 1 lần)
        $platform_icons = [];
        foreach($platforms as $platform){
            $platform_icons[$platform['id']] = base_url($platform['icon']);
        }
        
        // Format platforms  
        foreach($platforms as $platform){
            $data['platforms'][] = [
                'id' => $platform['id'],
                'name' => $platform['name'],
                'icon' => base_url($platform['icon']),
                'slug' => $platform['slug']
            ];
        }
        
        // Format categories
        foreach($categories as $category){
            $platform_icon = isset($platform_icons[$category['parent_id']]) ? $platform_icons[$category['parent_id']] : '';
            $data['categories'][] = [
                'id' => $category['id'],
                'platform_id' => $category['parent_id'],
                'name' => __($category['name']),
                'icon' => empty($category['icon']) ? $platform_icon : base_url($category['icon']),
                'slug' => $category['slug'],
                'description' => __($category['description'])
            ];
        }
        
        // Format services
        foreach($services as $service){
            // Giá bán mặc định
            $price = $service['price'];
            if(isset($getUserFallback)){
                if($getUserFallback['rank_id'] > 0){
                    $price = $service[getRankTargetById($getUserFallback['rank_id'])]; // Lấy giá theo Rank của User
                }
                if($getUserFallback['discount'] > 0){
                    $price = $price * $getUserFallback['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
                }
            }
            
            // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
            $display_price = $price * getQuantityUnit($service['type']);
            
            $data['services'][] = [
                'id' => $service['id'],
                'name' => __($service['name']),
                'category_id' => $service['category_id'],
                'platform_id' => $service['platform_id'],
                'price' => format_currency($display_price),
                'price_raw' => $price, // Giá không format để tính toán
                'min' => $service['min'],
                'max' => $service['max'],
                'dripfeed' => $service['dripfeed'],
                'refill' => $service['refill'],
                'cancel' => $service['cancel'],
                'type' => $service['type']
            ];
        }
        
        die(json_encode([
            'status' => 'success',
            'msg' => __('Success'),
            'data' => $data
        ]));
    }
    
    // ✅ LAZY MODE: Chỉ load platforms + categories (nhanh hơn)
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
    }
    
    // Load tất cả platforms
    $platforms = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC ");
    
    // Load tất cả categories
    $categories = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` > 0 ORDER BY `stt` DESC ");
    
    $data = [
        'platforms' => [],
        'categories' => [],
        'load_mode' => 'lazy' // ✅ Thông báo cho frontend biết mode
    ];
    
    // Tạo mapping platform icons để tối ưu performance (chỉ load 1 lần)
    $platform_icons = [];
    foreach($platforms as $platform){
        $platform_icons[$platform['id']] = base_url($platform['icon']);
    }
    
    // Format platforms  
    foreach($platforms as $platform){
        $data['platforms'][] = [
            'id' => $platform['id'],
            'name' => $platform['name'],
            'icon' => base_url($platform['icon']),
            'slug' => $platform['slug']
        ];
    }
    
    // Format categories
    foreach($categories as $category){
        $platform_icon = isset($platform_icons[$category['parent_id']]) ? $platform_icons[$category['parent_id']] : '';
        $data['categories'][] = [
            'id' => $category['id'],
            'platform_id' => $category['parent_id'],
            'name' => __($category['name']),
            'icon' => empty($category['icon']) ? $platform_icon : base_url($category['icon']),
            'slug' => $category['slug'],
            'description' => __($category['description'])
        ];
    }
    
    die(json_encode([
        'status' => 'success',
        'msg' => __('Success'),
        'data' => $data
    ]));
}

if($_POST['action'] == 'loadServicesByCategory'){
    // ✅ OPTIMIZATION: Chỉ load services của category cụ thể
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $category_id = check_string($category_id);
    
    if ($category_id <= 0) {
        die(json_encode([
            'status' => 'error',
            'msg' => __('Category ID không hợp lệ')
        ]));
    }
    
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
    }
    
    // Load services của category cụ thể
    $services = $CMSNT->get_list(" SELECT s.*, c.parent_id as platform_id FROM `services` s JOIN `categories` c ON s.category_id = c.id WHERE s.display = 'show' AND s.category_id = $category_id ORDER BY s.stt DESC ");
    
    $data = [];
    
    // Format services
    foreach($services as $service){
        // Giá bán mặc định
        $price = $service['price'];
        if(isset($getUser)){
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }
        
        // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
        $display_price = $price * getQuantityUnit($service['type']);
        
        $data[] = [
            'id' => $service['id'],
            'name' => __($service['name']),
            'category_id' => $service['category_id'],
            'platform_id' => $service['platform_id'],
            'price' => format_currency($display_price),
            'price_raw' => $price, // Giá không format để tính toán
            'min' => $service['min'],
            'max' => $service['max'],
            'dripfeed' => $service['dripfeed'],
            'refill' => $service['refill'],
            'cancel' => $service['cancel'],
            'type' => $service['type']
        ];
    }
    
    die(json_encode([
        'status' => 'success',
        'msg' => __('Success'),
        'data' => $data
    ]));
}

if($_POST['action'] == 'loadServicesChunked'){
    // ✅ OPTIMIZATION: Load services theo chunks với pagination
    $category_id = isset($_POST['category_id']) ? (int)$_POST['category_id'] : 0;
    $page = isset($_POST['page']) ? (int)$_POST['page'] : 0;
    $limit = isset($_POST['limit']) ? (int)$_POST['limit'] : 100;
    
    $category_id = check_string($category_id);
    $page = check_string($page);
    $limit = check_string($limit);
    
    if ($category_id <= 0) {
        die(json_encode([
            'status' => 'error',
            'msg' => __('Category ID không hợp lệ')
        ]));
    }
    
    // Giới hạn limit để tránh abuse
    $limit = min($limit, 500);
    $offset = $page * $limit;
    
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
    }
    
    // Count total services
    $total = $CMSNT->get_row(" SELECT COUNT(*) as total FROM `services` s WHERE s.display = 'show' AND s.category_id = $category_id ")['total'];
    
    // Load services với pagination
    $services = $CMSNT->get_list(" SELECT s.*, c.parent_id as platform_id FROM `services` s JOIN `categories` c ON s.category_id = c.id WHERE s.display = 'show' AND s.category_id = $category_id ORDER BY s.stt DESC LIMIT $limit OFFSET $offset ");
    
    $data = [];
    
    // Format services
    foreach($services as $service){
        // Giá bán mặc định
        $price = $service['price'];
        if(isset($getUser)){
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }
        
        // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
        $display_price = $price * getQuantityUnit($service['type']);
        
        $data[] = [
            'id' => $service['id'],
            'name' => __($service['name']),
            'category_id' => $service['category_id'],
            'platform_id' => $service['platform_id'],
            'price' => format_currency($display_price),
            'price_raw' => $price, // Giá không format để tính toán
            'min' => $service['min'],
            'max' => $service['max'],
            'dripfeed' => $service['dripfeed'],
            'refill' => $service['refill'],
            'cancel' => $service['cancel'],
            'type' => $service['type']
        ];
    }
    
    $has_more = ($offset + $limit) < $total;
    
    die(json_encode([
        'status' => 'success',
        'msg' => __('Success'),
        'data' => [
            'services' => $data,
            'has_more' => $has_more,
            'total' => $total,
            'current_count' => count($data),
            'page' => $page
        ]
    ]));
}

if($_POST['action'] == 'loadAllData'){
    // Load tất cả dữ liệu 1 lần để tối ưu hiệu suất
    if(!empty($_POST['token'])){
        $getUser = $CMSNT->get_row(" SELECT * FROM `users` WHERE `token` = '".check_string($_POST['token'])."' AND `banned` = 0 ");
    }
    
    // Load tất cả platforms
    $platforms = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` = 0 ORDER BY `stt` DESC ");
    
    // Load tất cả categories
    $categories = $CMSNT->get_list(" SELECT * FROM `categories` WHERE `status` = 'show' AND `parent_id` > 0 ORDER BY `stt` DESC ");
    
    // Load tất cả services
    $services = $CMSNT->get_list(" SELECT s.*, c.parent_id as platform_id FROM `services` s JOIN `categories` c ON s.category_id = c.id WHERE s.display = 'show' ORDER BY s.stt DESC ");
    
    $data = [
        'platforms' => [],
        'categories' => [],
        'services' => [],
        'load_mode' => 'all' // ✅ Đánh dấu mode load tất cả
    ];
    
    // Tạo mapping platform icons để tối ưu performance (chỉ load 1 lần)
    $platform_icons = [];
    foreach($platforms as $platform){
        $platform_icons[$platform['id']] = base_url($platform['icon']);
    }
    
    // Format platforms  
    foreach($platforms as $platform){
        $data['platforms'][] = [
            'id' => $platform['id'],
            'name' => $platform['name'],
            'icon' => base_url($platform['icon']),
            'slug' => $platform['slug']
        ];
    }
    
    // Format categories
    foreach($categories as $category){
        $platform_icon = isset($platform_icons[$category['parent_id']]) ? $platform_icons[$category['parent_id']] : '';
        $data['categories'][] = [
            'id' => $category['id'],
            'platform_id' => $category['parent_id'],
            'name' => __($category['name']),
            'icon' => empty($category['icon']) ? $platform_icon : base_url($category['icon']),
            'slug' => $category['slug'],
            'description' => __($category['description'])
        ];
    }
    
    // Format services
    foreach($services as $service){
        // Giá bán mặc định
        $price = $service['price'];
        if(isset($getUser)){
            if($getUser['rank_id'] > 0){
                $price = $service[getRankTargetById($getUser['rank_id'])]; // Lấy giá theo Rank của User
            }
            if($getUser['discount'] > 0){
                $price = $price * $getUser['discount'] / 100; // Tính giá sau khi áp dụng discount nếu có
            }
        }
        
        // Tính giá hiển thị dựa trên quantity_unit của dịch vụ type tương ứng
        $display_price = $price * getQuantityUnit($service['type']);
        
        $data['services'][] = [
            'id' => $service['id'],
            'name' => __($service['name']),
            'category_id' => $service['category_id'],
            'platform_id' => $service['platform_id'],
            'price' => format_currency($display_price),
            'price_raw' => $price, // Giá không format để tính toán
            'min' => $service['min'],
            'max' => $service['max'],
            'dripfeed' => $service['dripfeed'],
            'refill' => $service['refill'],
            'cancel' => $service['cancel'],
            'type' => $service['type']
        ];
    }
    
    die(json_encode([
        'status' => 'success',
        'msg' => __('Success'),
        'data' => $data
    ]));
}



die(json_encode([
    'status'    => 'error',
    'msg'       => __('Invalid data')
]));