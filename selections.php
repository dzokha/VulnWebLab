<?php
/**
 * bWAPP - Selections & Security Level Logic (Modernized for PHP 8.2+)
 */

declare(strict_types=1);

require_once __DIR__. '/admin/settings.php';

// 1. Xử lý đọc danh sách Bug (Sử dụng kiểm tra file tồn tại để tránh lỗi Fatal)
$bugs_file = __DIR__ . "/bugs.txt";
$bugs = file_exists($bugs_file) ? file($bugs_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];

/**
 * 2. Điều hướng bài tập (Bug Selection)
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["form_bug"], $_POST["bug"])) {
    $key = (int)$_POST["bug"];
    
    if (isset($bugs[$key])) {
        $bug_data = explode(",", trim($bugs[$key]));
        $target = $bug_data[1] ?? "";

        if (!empty($target) && file_exists(__DIR__ . "/" . $target)) {
            header("Location: " . $target);
            exit;
        }
    }
}

/**
 * 3. Thiết lập Security Level (Form Security Level)
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["form_security_level"], $_POST["security_level"])) {
    $level_input = (string)$_POST["security_level"];

    // Sử dụng MATCH expression (PHP 8+) để thay thế Switch/Case: Gọn và an toàn hơn
    $security_level_cookie = match ($level_input) {
        "1"     => "1",
        "2"     => "2",
        default => "0",
    };

    // Chế độ Evil Bee (666)
    $final_level = (isset($evil_bee) && $evil_bee == 1) ? "666" : $security_level_cookie;

    // Thiết lập Cookie với chuẩn bảo mật hiện đại
    setcookie("security_level", $final_level, [
        'expires' => time() + 31536000, // 1 year
        'path' => '/',
        'domain' => '', 
        'secure' => false, // Đổi thành true nếu chạy HTTPS
        'httponly' => true, 
        'samesite' => 'Lax'
    ]);

    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
}

/**
 * 4. Chuyển đổi mã Cookie thành chuỗi hiển thị
 */
$cookie_val = $_COOKIE["security_level"] ?? null;

$security_level = match ($cookie_val) {
    "0"     => "low",
    "1"     => "medium",
    "2"     => "high",
    "666"   => "666",
    null    => "not set",
    default => "low",
};