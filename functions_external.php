<?php
/**
 * bWAPP - Security Functions (Modernized for PHP 8.2+)
 */

declare(strict_types=1);

/** --- XỬ LÝ XSS (Cross-Site Scripting) --- */

// Cấp độ Low: Chỉ thay thế cơ bản, dễ bị bypass qua encoding
function xss_check_1(string $data): string {
    $input = str_replace(["<", ">"], ["&lt;", "&gt;"], $data);
    return urldecode($input); // LỖI BẢO MẬT CỐ Ý: urldecode cho phép Double Encoding Attack
}

// Cấp độ Medium: Chuyển mọi ký tự có thể thành HTML entities
function xss_check_2(string $data): string {
    return htmlentities($data, ENT_QUOTES | ENT_HTML5, "UTF-8");
}

// Cấp độ High: Tiêu chuẩn vàng hiện nay
function xss_check_3(string $data, string $encoding = "UTF-8"): string {
    return htmlspecialchars($data, ENT_QUOTES | ENT_SUBSTITUTE, $encoding);
}

/** --- XỬ LÝ SQL INJECTION --- */

// Cấp độ Low: Sử dụng hàm cổ điển (Không an toàn hoàn toàn)
function sqli_check_1(string $data): string {
    return addslashes($data);
}

// Cấp độ High: Sử dụng MySQLi Real Escape (Yêu cầu biến $link)
function sqli_check_3(mysqli $link, string $data): string {
    return $link->real_escape_string($data);
}

/** --- XỬ LÝ COMMAND INJECTION --- */

function commandi_check_2(string $data): string {
    return escapeshellcmd($data);
}

/** --- XỬ LÝ FILE UPLOAD (PHP 8+ Syntax) --- */

function file_upload_check_modern(array $file, array $allowed_ext = ["jpg", "jpeg", "png", "gif"], string $dir = "images"): string {
    if (empty($file["name"])) return "Please select a file...";
    
    // PHP 8 logic match cho lỗi upload
    $file_error = match ($file["error"]) {
        UPLOAD_ERR_OK => "",
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => "File too large.",
        UPLOAD_ERR_PARTIAL => "Partial upload.",
        UPLOAD_ERR_NO_TMP_DIR => "Missing temp folder.",
        default => "Unknown upload error."
    };

    if ($file_error) return $file_error;

    $ext = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    if (!in_array($ext, $allowed_ext, true)) {
        return "Extension not allowed. Only: " . implode(", ", $allowed_ext);
    }

    if (is_file("$dir/" . basename($file["name"]))) {
        return "File already exists. Rename it.";
    }

    return "";
}

/** --- CƠ CHẾ SINH CHUỖI NGẪU NHIÊN (Chuẩn bảo mật 2026) --- */

function random_string(int $length = 16): string {
    // Không dùng rand() hay shuffle() vì không an toàn cho mật mã
    // Dùng random_bytes (Cryptographically Secure Pseudo-Random Number Generator)
    return bin2hex(random_bytes($length / 2));
}

/** --- KIỂM TRA DIRECTORY TRAVERSAL --- */

function directory_traversal_check_modern(string $user_path, string $base_path = ""): string {
    $real_base = realpath($base_path);
    $real_user = realpath($user_path);

    // Kiểm tra xem đường dẫn user chọn có nằm "trong" thư mục gốc không
    if (!$real_user || !$real_base || !str_starts_with($real_user, $real_base)) {
        return "<span style='color:red'>Directory Traversal detected!</span>";
    }

    return "";
}

/** --- CÁC HÀM TIỆN ÍCH KHÁC --- */

// Thay thế ereg (đã bị xóa) bằng preg_match hiện đại
function email_check_modern(string $data): bool {
    return (bool)filter_var($data, FILTER_VALIDATE_EMAIL);
}

// Tối ưu hóa hàm SID (Windows Security Identifier)
function bin_sid_to_text_modern(string $binsid): string {
    $hex = bin2hex($binsid);
    $rev = hexdec(substr($hex, 0, 2));
    $subcount = hexdec(substr($hex, 2, 2));
    $auth = hexdec(substr($hex, 4, 12));
    
    // Trích xuất phần cuối (RID) theo cách hiện đại
    $offset = 16 + (($subcount - 1) * 8);
    $last_part = substr($hex, $offset, 8);
    
    // Đảo ngược Little-Endian
    $rid_hex = implode('', array_reverse(str_split($last_part, 2)));
    return (string)hexdec($rid_hex);
}