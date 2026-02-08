<?php
// Cấu hình kết nối Database

$configFile = __DIR__ . '/admin/settings.php';
if (file_exists($configFile)) {
    require_once $configFile;
} else {
    die("Lỗi: Không tìm thấy file config.inc.php tại " . __DIR__);
}


$server   = $db_server; 
$username = $db_username;     
$password = $db_password;          
$database = $db_name;
$port = $db_port;

// $server   = "127.0.0.1"; // Dùng IP để tránh lỗi phân giải DNS chậm trên Mac
// $username = "root";      // Mặc định của XAMPP/MAMP
// $password = "";          // Mặc định thường để trống, nếu có hãy điền vào
// $database = "bWAPP"; 
?>