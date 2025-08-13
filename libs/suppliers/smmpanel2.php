<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}


function get_refill_status_smmpanel2($domain, $api_key, $orders, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key, 'action' => 'refill_status', 'refills' => $orders),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function refill_order_smmpanel2($domain, $api_key, $orders, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key, 'action' => 'refill', 'orders' => $orders),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function cancel_order_smmpanel2($domain, $api_key, $orders, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key, 'action' => 'cancel', 'orders' => $orders),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function get_history_smmpanel2($domain, $api_key, $order_id, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key, 'action' => 'status', 'orders' => $order_id),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}

function buy_service_smmpanel2($domain, $api_key, $service, $quantity, $link, $comments, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key, 'action' => 'add', 'service' => $service, 'quantity' => $quantity, 'link' => $link, 'comments' => $comments),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}


function get_services_smmpanel2($domain, $api_key, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key,'action' => 'services'),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}
function get_balance_smmpanel2($domain, $api_key, $proxy = ''){
    // Đảm bảo domain có api/v2
    if (strpos($domain, 'api/v2') === false) {
        $domain = rtrim($domain, '/') . '/api/v2';
    }
    
    $curl = curl_init();
        curl_setopt_array($curl, array(
        CURLOPT_URL => $domain,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('key' => $api_key,'action' => 'balance'),
    ));
    // Thêm proxy nếu có và hợp lệ
    if(!empty($proxy)) {
        $proxy_parts = explode(':', $proxy);
        if(count($proxy_parts) == 4 && !empty($proxy_parts[0]) && !empty($proxy_parts[1]) && !empty($proxy_parts[2]) && !empty($proxy_parts[3])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
            curl_setopt($curl, CURLOPT_PROXYUSERPWD, $proxy_parts[2].':'.$proxy_parts[3]);
        } elseif(count($proxy_parts) == 2 && !empty($proxy_parts[0]) && !empty($proxy_parts[1])) {
            curl_setopt($curl, CURLOPT_PROXY, $proxy_parts[0].':'.$proxy_parts[1]);
        }
    }
    $response = curl_exec($curl);
    curl_close($curl);
    return $response;
}