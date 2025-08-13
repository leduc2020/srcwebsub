<?php

if (!defined('IN_SITE')) {
    die('The Request Not Found');
}

// Kiểm tra DEBUG từ file .env
$env = parse_ini_file(__DIR__.'/../.env');
define('DEBUG', isset($env['DEBUG']) ? $env['DEBUG'] : 0);

include_once(__DIR__.'/../vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__.'/../');
$dotenv->load();
 

class DB
{
    private $ketnoi;

    // Hàm xử lý lỗi
    private function handleError($sql) {
        if(DEBUG == true) {
            $error = [
                'message' => mysqli_error($this->ketnoi),
                'code' => mysqli_errno($this->ketnoi),
                'file' => debug_backtrace()[1]['file'],
                'line' => debug_backtrace()[1]['line'],
                'query' => $sql,
                'time' => date('Y-m-d H:i:s')
            ];
            
            echo '<div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); font-family: monospace;">';
            echo '<h3 style="color: #dc3545; margin-bottom: 15px;">Database Error</h3>';
            echo '<div style="margin-bottom: 10px;"><strong>Message:</strong> ' . $error['message'] . '</div>';
            echo '<div style="margin-bottom: 10px;"><strong>Error Code:</strong> ' . $error['code'] . '</div>';
            echo '<div style="margin-bottom: 10px;"><strong>File:</strong> ' . $error['file'] . '</div>';
            echo '<div style="margin-bottom: 10px;"><strong>Line:</strong> ' . $error['line'] . '</div>';
            echo '<div style="margin-bottom: 10px;"><strong>Query:</strong> ' . $error['query'] . '</div>';
            echo '<div style="margin-bottom: 10px;"><strong>Time:</strong> ' . $error['time'] . '</div>';
            echo '</div>';
            return false;
        } 
        else {
            echo '<div style="background: #fff; padding: 20px; border-radius: 5px; box-shadow: 0 0 10px rgba(0,0,0,0.1); font-family: monospace;">';
            echo '<h3 style="color: #dc3545; margin-bottom: 15px;">Database Error</h3>';
            echo '<div style="margin-bottom: 10px;">Không thể kết nối đến máy chủ hoặc đang quá tải, vui lòng thử lại sau.</div>';
            echo '</div>';
            return false;
        }
    }

    public function connect()
    {
        if (!$this->ketnoi) {
            $this->ketnoi = mysqli_connect($_ENV['DB_HOST'], $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $_ENV['DB_DATABASE']) or $this->handleError('Unable to connect to Database');
            // sau khi kết nối xong
            mysqli_set_charset($this->ketnoi, 'utf8mb4');
            mysqli_query($this->ketnoi, "SET NAMES 'utf8mb4' COLLATE 'utf8mb4_unicode_ci'");
        }
    }
    public function dis_connect()
    {
        if ($this->ketnoi) {
            mysqli_close($this->ketnoi);
        }
    }
    public function site($data)
    {
        $this->connect();
        $sql = "SELECT * FROM `settings` WHERE `name` = '$data'";
        $result = $this->ketnoi->query($sql);
        if (!$result) {
            $this->handleError($sql);
            return false;
        }
        $row = $result->fetch_array();
        if (!$row) {
            $this->handleError("Không tìm thấy setting với name = '$data'");
            return false;
        }
        return $row['value'];
    }
    public function query($sql)
    {
        $this->connect();
        $result = $this->ketnoi->query($sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function cong($table, $data, $sotien, $where)
    {
        $this->connect();
        $sql = "UPDATE `$table` SET `$data` = `$data` + '$sotien' WHERE $where";
        $result = $this->ketnoi->query($sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function tru($table, $data, $sotien, $where)
    {
        $this->connect();
        $sql = "UPDATE `$table` SET `$data` = `$data` - '$sotien' WHERE $where";
        $result = $this->ketnoi->query($sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function insert($table, $data)
    {
        $this->connect();
        $field_list = '';
        $value_list = '';
        foreach ($data as $key => $value) {
            $field_list .= ",$key";
            $value_list .= ",'".mysqli_real_escape_string($this->ketnoi, $value)."'";
        }
        $sql = 'INSERT INTO '.$table. '('.trim($field_list, ',').') VALUES ('.trim($value_list, ',').')';

        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function update($table, $data, $where)
    {
        $this->connect();
        $sql = '';
        foreach ($data as $key => $value) {
            $sql .= "$key = '".mysqli_real_escape_string($this->ketnoi, $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where;
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function update_value($table, $data, $where, $value1)
    {
        $this->connect();
        $sql = '';
        foreach ($data as $key => $value) {
            $sql .= "$key = '".mysqli_real_escape_string($this->ketnoi, $value)."',";
        }
        $sql = 'UPDATE '.$table. ' SET '.trim($sql, ',').' WHERE '.$where.' LIMIT '.$value1;
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function remove($table, $where)
    {
        $this->connect();
        $sql = "DELETE FROM $table WHERE $where";
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        return $result;
    }
    public function get_list($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        $return = array();
        while ($row = mysqli_fetch_assoc($result)) {
            $return[] = $row;
        }
        mysqli_free_result($result);
        return $return;
    }
    public function get_row($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        $row = mysqli_fetch_assoc($result);
        mysqli_free_result($result);
        if ($row) {
            return $row;
        }
        return false;
    }
    public function num_rows($sql)
    {
        $this->connect();
        $result = mysqli_query($this->ketnoi, $sql);
        if (!$result) {
            $this->handleError($sql);
        }
        $row = mysqli_num_rows($result);
        mysqli_free_result($result);
        if ($row) {
            return $row;
        }
        return false;
    }
}

