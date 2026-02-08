<?php
/**
 * bWAPP - Modernized for PHP 8.2+
 * Chuyên gia khuyến cáo: Luôn dùng Prepared Statements để chống SQL Injection.
 */


declare(strict_types=1);
session_start();

// Sử dụng require_once để đảm bảo file tồn tại

require_once __DIR__. '/connect_i.php';
require_once __DIR__. '/admin/settings.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["form"])) {
    
    $login = $_POST["login"] ?? "";
    $password = $_POST["password"] ?? "";
    $security_level_input = $_POST["security_level"] ?? "0";

    try {
        // 1. Sử dụng Prepared Statements thay vì cộng chuỗi SQL
        // Lưu ý: Trong bWAPP Lab, dòng này sẽ làm mất lỗi SQLi, 
        // nhưng đây là cách viết "đúng" trong thực tế.
        $stmt = $link->prepare("SELECT id, login, password, admin, activated FROM users WHERE login = ?");
        $stmt->bind_param("s", $login);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($row = $result->fetch_object()) {
            
            // 2. Kiểm tra mật khẩu (Sử dụng hash hiện đại hoặc SHA1 cũ tùy cấu hình Lab)
            // Ở đây tôi dùng SHA1 để khớp với Database hiện tại của bạn:
            $hashed_input = hash("sha1", $password);

            if ($hashed_input === $row->password && (int)$row->activated === 1) {
                
                // 3. Bảo mật Session: Chống Session Fixation
                session_regenerate_id(true);

                $token = bin2hex(random_bytes(20)); // Tạo token an toàn hơn uniqid

                $_SESSION["login"] = $row->login;
                $_SESSION["admin"] = $row->admin;
                $_SESSION["token"] = $token;
                $_SESSION["amount"] = 1000;

                // 4. Xử lý Security Level
                $security_level = in_array($security_level_input, ["0", "1", "2"]) ? $security_level_input : "0";

                // 5. Thiết lập Cookie với các cờ bảo mật (HttpOnly, SameSite)
                $cookie_val = ($evil_bee === 1) ? "666" : $security_level;
                setcookie("security_level", $cookie_val, [
                    'expires' => time() + 86400 * 365,
                    'path' => '/',
                    'domain' => '', 
                    'secure' => false,   // Đặt true nếu dùng HTTPS
                    'httponly' => true,  // Chống XSS đánh cắp Cookie
                    'samesite' => 'Lax'
                ]);

                header("Location: portal.php");
                exit;
            } else {
                $message = "<span style='color:red'>Invalid credentials or user not activated!</span>";
            }
        } else {
            $message = "<span style='color:red'>Invalid credentials!</span>";
        }
        $stmt->close();
    } catch (Exception $e) {
        $message = "<span style='color:red'>System Error: " . htmlspecialchars($e->getMessage()) . "</span>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" />
    <title>bWAPP - Login (Modernized)</title>
</head>
<body>

<header>
    <h1>bWAPP</h1>
    <h2>an extremely buggy web app !</h2>
</header>

<div id="menu">
    <table>
        <tr>
            <td><span style="color:#ffb717">Login</span></td>
            <td><a href="user_new.php">New User</a></td>
            <td><a href="info.php">Info</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
        </tr>
    </table>
</div>

<div id="main">
    <h1>Login</h1>
    <p>Enter your credentials <i>(bee/bug)</i>.</p>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <p>
            <label for="login">Login:</label><br />
            <input type="text" id="login" name="login" size="20" required autocomplete="username">
        </p> 

        <p>
            <label for="password">Password:</label><br />
            <input type="password" id="password" name="password" size="20" required autocomplete="current-password">
        </p>

        <p>
            <label for="security_level">Security Level:</label><br />
            <select name="security_level" id="security_level">
                <option value="0">Low (Vulnerable)</option>
                <option value="1">Medium</option>
                <option value="2">High (Secure)</option>
            </select>
        </p>

        <button type="submit" name="form" value="submit">Login</button>
    </form>
    <br />
    <?= $message; ?>
</div>

<?php if (isset($link)) $link->close(); ?>
</body>
</html>