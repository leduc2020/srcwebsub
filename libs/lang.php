<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}
// chọn ngôn ngữ
function setLanguageByCode($code){
    global $CMSNT;
    if ($row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `code` = '$code' AND `status` = 1 ")) {
        $isSet = setcookie('language', $row['lang'], time() + (31536000 * 30), "/"); // 31536000 = 365 ngày
        if ($isSet) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}
function setLanguageById($id){
    return setLanguage($id);
}
function setLanguage($id){
    global $CMSNT;
    if ($row = $CMSNT->get_row("SELECT * FROM `languages` WHERE `id` = '$id' AND `status` = 1 ")) {
        $isSet = setcookie('language', $row['lang'], time() + (31536000 * 30), "/"); // 31536000 = 365 ngày
        if ($isSet) {
            return true;
        } else {
            return false;
        }
    }
    return false;
}

function getLanguageCode(){
    global $CMSNT;
    if (isset($_COOKIE['language'])) {
        $language = check_string($_COOKIE['language']);
        $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang` = '$language' AND `status` = 1 ");
        if ($rowLang) {
            return $rowLang['code'];
        }
    }
    $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1 ");
    if ($rowLang) {
        return $rowLang['code'];
    }
    return 'vi';
}
// lấy ngôn ngữ mặc định
function getLanguage(){
    global $CMSNT;
    if (isset($_COOKIE['language'])) {
        $language = check_string($_COOKIE['language']);
        $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang` = '$language' AND `status` = 1 ");
        if ($rowLang) {
            return $rowLang['lang'];
        }
    }
    $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1 ");
    if ($rowLang) {
        return $rowLang['lang'];
    }
    return false;
}
//hiển thị ngôn ngữ
function __($name){
    global $CMSNT;
    if (isset($_COOKIE['language'])) {
        $language = check_string($_COOKIE['language']);
        $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang` = '$language' AND `status` = 1 ");
        if ($rowLang) {
            $rowTran = $CMSNT->get_row("SELECT * FROM `translate` WHERE `lang_id` = '".$rowLang['id']."' AND `name` = '$name' ");
            if ($rowTran) {
                return $rowTran['value'];
            }
        }
    }
    $rowLang = $CMSNT->get_row("SELECT * FROM `languages` WHERE `lang_default` = 1 ");
    if ($rowLang) {
        $rowTran = $CMSNT->get_row("SELECT * FROM `translate` WHERE `lang_id` = '".$rowLang['id']."' AND `name` = '$name' ");
        if ($rowTran) {
            return $rowTran['value'];
        }
    }

    // if(DEBUG == true){
    //     // Tạm thời: Tự động thêm key ngôn ngữ vào lang.php nếu chưa tồn tại
    //     $langFilePath = __DIR__ . '/../lang.php';
    //     $currentLangDataInFile = []; 

    //     if (file_exists($langFilePath)) {
    //         // Dùng hàm ẩn danh để đọc file lang.php, cô lập phạm vi của biến $langDefault
    //         // và @ để bỏ qua lỗi nếu file lang.php có vấn đề (ví dụ: trống hoặc sai cú pháp)
    //         $currentLangDataInFile = (function($path) {
    //             $langDefault = []; // Khởi tạo mặc định là mảng rỗng
    //             @include $path; 
    //             // Sau khi include, biến $langDefault (từ file lang.php) sẽ có sẵn nếu file hợp lệ
    //             return (isset($langDefault) && is_array($langDefault)) ? $langDefault : [];
    //         })($langFilePath);
    //     }
        
    //     $keyForArray = $name; 
    //     $valueForArray = $name; // Theo yêu cầu: nếu text chưa có, thêm dưới dạng key => text

    //     // Chỉ thực hiện ghi file nếu key chưa tồn tại
    //     if (!array_key_exists($keyForArray, $currentLangDataInFile)) {
    //         $currentLangDataInFile[$keyForArray] = $valueForArray; // Thêm key mới vào mảng (trong bộ nhớ)

    //         // Xây dựng lại nội dung cho file lang.php
    //         $newLangFileContentString = "<?php\n\n// KEY => VALUE\n\$langDefault = [\n";
    //         foreach ($currentLangDataInFile as $k => $v) {
    //             // Escape dấu nháy đơn cho key và value để đảm bảo chuỗi PHP hợp lệ
    //             $escapedKForFile = str_replace("'", "\\'", $k);
    //             $escapedVForFile = str_replace("'", "\\'", $v);
    //             $newLangFileContentString .= "    '$escapedKForFile' => '$escapedVForFile',\n";
    //         }
    //         // Xóa dấu phẩy cuối cùng nếu mảng không rỗng
    //         if (!empty($currentLangDataInFile)) {
    //             $newLangFileContentString = rtrim($newLangFileContentString, ",\n") . "\n";
    //         }
    //         $newLangFileContentString .= "];\n";
            
    //         // Ghi lại nội dung vào file lang.php
    //         // Kiểm tra quyền ghi trước khi thực hiện để tránh lỗi
    //         if ( (file_exists($langFilePath) && is_writable($langFilePath)) || 
    //             (!file_exists($langFilePath) && is_writable(dirname($langFilePath))) ) {
    //             file_put_contents($langFilePath, $newLangFileContentString, LOCK_EX);
    //         } else {
    //             // Bạn có thể muốn ghi log lỗi ở đây nếu không thể ghi file
    //             // error_log("LƯU Ý: Không thể ghi vào file lang.php tại $langFilePath từ hàm __(). Không thể lưu key ngôn ngữ mới: " . $name);
    //         }
    //     }
    //     // Kết thúc đoạn code tạm thời
    // }

    return $name;
}

