<?php
/**
 * bWAPP - Security Level Check (Modernized for PHP 8.2+)
 * Kiểm tra xem người dùng đã chọn mức độ bảo mật chưa.
 */

declare(strict_types=1);

require_once __DIR__. '/admin/settings.php';

$remote_ip = $_SERVER["REMOTE_ADDR"] ?? '';
$is_aim_user = false;

/**
 * 1. Kiểm tra dải IP (A.I.M Logic)
 * Sử dụng Bitwise thay vì tạo mảng IP khổng lồ để tối ưu bộ nhớ.
 */
if (isset($AIM_subnet) && str_contains($AIM_subnet, '/')) {
    [$subnet, $mask] = explode('/', $AIM_subnet);
    $ip_long = ip2long($remote_ip);
    $subnet_long = ip2long($subnet);
    $mask = (int)$mask;

    if ($ip_long !== false && $subnet_long !== false) {
        $network_mask = ~((1 << (32 - $mask)) - 1);
        if (($ip_long & $network_mask) === ($subnet_long & $network_mask)) {
            $is_aim_user = true;
        }
    }
}

// Kiểm tra danh sách IP cụ thể (Whitelist)
if (!$is_aim_user && isset($AIM_IPs) && is_array($AIM_IPs)) {
    if (in_array($remote_ip, $AIM_IPs, true)) {
        $is_aim_user = true;
    }
}

/**
 * 2. Xác định Security Level
 * Ưu tiên lấy từ Cookie, nếu không có sẽ kiểm tra quyền ưu tiên của A.I.M.
 */
$security_level = $_COOKIE["security_level"] ?? null;

// Nếu không có Cookie và không phải User A.I.M -> Bắt buộc phải đi thiết lập mức bảo mật
if ($security_level === null && !$is_aim_user) {
    header("Location: security_level_set.php");
    exit;
}

/**
 * 3. Gán biến global $security_level để các trang bài tập sử dụng
 * Mặc định là '0' (low) nếu là user A.I.M mà chưa có cookie.
 */
if ($security_level === null) {
    $security_level = "0";
}

// Chống XSS khi sử dụng biến này ở các trang khác
$security_level = htmlspecialchars((string)$security_level);