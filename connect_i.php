<?php
/**
 * bWAPP Connection Script - Modernized for PHP 8.2+
 * * Sử dụng cơ chế báo lỗi chuẩn và quản lý kết nối an toàn.
 */

declare(strict_types=1);

// 1. Nhúng cấu hình (Sử dụng require_once để dừng script nếu thiếu file quan trọng)
require_once __DIR__ . '/config.inc.php';

// 2. Cấu hình báo lỗi MySQLi (Tiêu chuẩn công nghệ mới)
// MYSQLI_REPORT_STRICT: Quăng ra Exception thay vì chỉ hiện Warning
// MYSQLI_REPORT_ERROR: Báo lỗi chi tiết từ MySQL
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    /**
     * 3. Khởi tạo kết nối
     * Sử dụng (int)$port để đảm bảo kiểu dữ liệu chuẩn cho PHP 8
     * Sử dụng 127.0.0.1 thay vì localhost để tránh lỗi phân giải DNS chậm trên macOS
     */
    $link = new mysqli(
        $server ?? '127.0.0.1', 
        $username ?? 'root', 
        $password ?? '', 
        $database ?? 'bWAPP', 
        isset($port) ? (int)$port : 3307
    );

    $link->set_charset('utf8mb4');
    

} catch (mysqli_sql_exception $e) {
    /**
     * 5. Xử lý lỗi kết nối an toàn
     * Trong môi trường Lab, chúng ta in lỗi. 
     * Trong môi trường thực tế, bạn chỉ nên ghi log và hiện thông báo bảo trì.
     */
    error_log("Database Connection Error: " . $e->getMessage());
    
    die("<h1>Database Connection Failed</h1>" . 
        "<p>Vui lòng kiểm tra MySQL port (hiện tại: " . ($port ?? '3306') . ") và file config.inc.php</p>");
}

/**
 * Lưu ý: Không đóng kết nối ($link->close()) ở đây vì file này sẽ được 
 * include vào các trang chức năng khác để sử dụng biến $link.
 */