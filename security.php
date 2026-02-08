<?php
/**
 * bWAPP - Security Gatekeeper (Modernized for PHP 8.2+)
 * Quản lý phiên làm việc và cơ chế tự động đăng nhập (A.I.M).
 */

declare(strict_types=1);

require_once __DIR__. '/admin/settings.php';

// 1. Cấu hình Session an toàn trước khi start
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true, // Chống XSS đánh cắp session
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

/**
 * 2. Cơ chế A.I.M (Authentication Is Missing)
 * Tự động đăng nhập nếu IP thuộc danh sách trắng hoặc Subnet cho phép.
 */
$remote_ip = $_SERVER["REMOTE_ADDR"] ?? '';
$is_authenticated = false;

// Kiểm tra Subnet (Tối ưu hóa logic tính toán IP)
if (isset($AIM_subnet) && str_contains($AIM_subnet, '/')) {
    [$subnet, $mask] = explode('/', $AIM_subnet);
    $mask = (int)$mask;
    
    $ip_long = ip2long($remote_ip);
    $subnet_long = ip2long($subnet);

    if ($ip_long !== false && $subnet_long !== false) {
        // Sử dụng phép toán Bitwise để kiểm tra IP có thuộc Subnet không (Nhanh hơn vòng lặp for)
        $network_mask = ~((1 << (32 - $mask)) - 1);
        if (($ip_long & $network_mask) === ($subnet_long & $network_mask)) {
            $is_authenticated = true;
        }
    }
}

// Kiểm tra danh sách IP cụ thể
if (isset($AIM_IPs) && is_array($AIM_IPs)) {
    if (in_array($remote_ip, $AIM_IPs, true)) {
        $is_authenticated = true;
    }
}

// 3. Thực hiện đăng nhập tự động nếu thỏa mãn điều kiện A.I.M
if ($is_authenticated) {
    // Tắt hiển thị lỗi để ẩn thông tin hệ thống (Best Practice)
    ini_set("display_errors", "0");

    if (!isset($_SESSION["login"])) {
        $_SESSION["login"] = "A.I.M.";
        $_SESSION["admin"] = "1";
    }
}

/**
 * 4. Kiểm tra quyền truy cập
 * Nếu chưa đăng nhập và không phải ở trang login, đẩy về login.php.
 */
$current_page = basename($_SERVER["PHP_SELF"]);
if (!isset($_SESSION["login"]) || empty($_SESSION["login"])) {
    if ($current_page !== 'login.php') {
        header("Location: login.php");
        exit;
    }
}

// 5. Chống Session Fixation: Làm mới ID sau một khoảng thời gian hoặc khi đăng nhập
// (Tùy chọn: giúp tăng cường bảo mật thực tế)
// session_regenerate_id(false);