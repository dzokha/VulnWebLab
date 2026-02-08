<?php
/**
 * bWAPP - Secure Logout (Modernized for PHP 8.2+)
 * Mục tiêu: Xóa sạch Session và tất cả Cookies liên quan đến Lab.
 */

declare(strict_types=1);

// 1. Nhúng các file bảo mật để kiểm tra quyền trước khi logout (nếu cần)
require_once __DIR__. '/security.php';

/**
 * 2. Xử lý Session
 * Thay vì chỉ gán mảng rỗng, chúng ta xóa sạch cookie chứa Session ID trên trình duyệt.
 */
if (session_status() === PHP_SESSION_ACTIVE) {
    // Lấy các tham số của cookie session hiện tại
    $params = session_get_cookie_params();
    
    // Xóa cookie session trên trình duyệt
    setcookie(session_name(), '', [
        'expires'  => time() - 42000,
        'path'     => $params["path"],
        'domain'   => $params["domain"],
        'secure'   => $params["secure"],
        'httponly' => $params["httponly"],
        'samesite' => $params["samesite"] ?? 'Lax'
    ]);

    // Giải phóng bộ nhớ session và hủy file session trên server
    $_SESSION = [];
    session_destroy();
}

/**
 * 3. Xóa các Cookies đặc thù của bWAPP
 * Sử dụng mảng để quản lý danh sách cookie cần xóa, giúp code sạch hơn.
 */
$cookies_to_delete = [
    "admin", 
    "movie_genre", 
    "secret", 
    "top_security", 
    "top_security_nossl", 
    "top_security_ssl",
    "security_level" // Xóa luôn level để người dùng chọn lại khi login
];

foreach ($cookies_to_delete as $cookie_name) {
    if (isset($_COOKIE[$cookie_name])) {
        setcookie($cookie_name, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => false,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
}

// 4. Chống lỗi "Open Redirection" (nếu có tham số redirect trong tương lai)
header("Location: login.php");
exit;