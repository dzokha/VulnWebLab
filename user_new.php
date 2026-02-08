<?php
/**
 * bWAPP - New User Registration (Modernized for PHP 8.2+)
 */

declare(strict_types=1);

require_once __DIR__. '/functions_external.php';
require_once __DIR__. '/connect_i.php';
require_once __DIR__. '/admin/settings.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["action"])) {
    $login = trim($_POST["login"] ?? "");
    $password = $_POST["password"] ?? "";
    $password_conf = $_POST["password_conf"] ?? "";
    $email = trim($_POST["email"] ?? "");
    $secret = trim($_POST["secret"] ?? "");
    $mail_activation = isset($_POST["mail_activation"]);

    // 1. Kiểm tra trường rỗng
    if (empty($login) || empty($email) || empty($password) || empty($secret)) {
        $message = "<span style='color:red'>Please enter all the fields!</span>";
    } 
    // 2. Kiểm tra định dạng Login (Regex)
    elseif (!preg_match("/^[a-z\d_]{2,20}$/i", $login)) {
        $message = "<span style='color:red'>Please choose a valid login name (2-20 characters, alphanumeric)!</span>";
    } 
    // 3. Kiểm tra định dạng Email
    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<span style='color:red'>Please enter a valid e-mail address!</span>";
    } 
    // 4. Kiểm tra khớp mật khẩu
    elseif ($password !== $password_conf) {
        $message = "<span style='color:red'>The passwords don't match!</span>";
    } 
    else {
        try {
            // Kiểm tra Login hoặc Email đã tồn tại chưa
            $stmt = $link->prepare("SELECT id FROM users WHERE login = ? OR email = ? LIMIT 1");
            $stmt->bind_param("ss", $login, $email);
            $stmt->execute();
            if ($stmt->get_result()->fetch_row()) {
                $message = "<span style='color:red'>The login or e-mail already exists!</span>";
            } else {
                // Băm mật khẩu bằng thuật toán Bcrypt hiện đại (Mặc định của PHP hiện nay)
                // Lưu ý: Nếu bạn muốn khớp với hệ thống cũ của bWAPP, có thể dùng hash("sha1", $password)
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                if (!$mail_activation) {
                    // Tạo user trực tiếp (đã kích hoạt)
                    $insert = $link->prepare("INSERT INTO users (login, password, email, secret, activated) VALUES (?, ?, ?, ?, 1)");
                    $insert->bind_param("ssss", $login, $hashed_password, $email, $secret);
                    $insert->execute();
                    $message = "<span style='color:green'>User successfully created!</span>";
                } else {
                    // Xử lý kích hoạt qua Email
                    $activation_code = bin2hex(random_bytes(16));
                    
                    // Logic gửi mail (giữ nguyên cấu trúc logic của bạn nhưng tối ưu hóa)
                    $subject = "bWAPP - New User Activation";
                    $server_host = $_SERVER["HTTP_HOST"];
                    $content = "Welcome " . htmlspecialchars(ucwords($login)) . ",\n\n";
                    $content .= "Click the link to activate: http://$server_host/bWAPP/user_activation.php?user=" . urlencode($login) . "&code=$activation_code\n\n";
                    
                    if (@mail($email, $subject, $content, "From: $smtp_sender")) {
                        $insert = $link->prepare("INSERT INTO users (login, password, email, secret, activation_code, activated) VALUES (?, ?, ?, ?, ?, 0)");
                        $insert->bind_param("sssss", $login, $hashed_password, $email, $secret, $activation_code);
                        $insert->execute();
                        $message = "<span style='color:green'>User created! Please check your email for activation code.</span>";
                    } else {
                        $message = "<span style='color:red'>User not created! SMTP mail delivery failed.</span>";
                    }
                }
            }
            $stmt->close();
        } catch (Exception $e) {
            $message = "<span style='color:red'>Database Error: " . htmlspecialchars($e->getMessage()) . "</span>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" />
    <title>bWAPP - New User</title>
</head>
<body>
    <header>
        <h1>bWAPP</h1>
        <h2>an extremely buggy web app !</h2>
    </header>

    <nav id="menu">
        <table>
            <tr>
                <td><a href="login.php">Login</a></td>
                <td><span style="color:#ffb717">New User</span></td>
                <td><a href="info.php">Info</a></td>
                <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
            </tr>
        </table>
    </nav>

    <main id="main">
        <h1>New User</h1>
        <p>Create a new user account.</p>

        <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <table>
                <tr>
                    <td>
                        <label for="login">Login:</label><br />
                        <input type="text" id="login" name="login" required>
                    </td>
                    <td style="width:10px"></td>
                    <td>
                        <label for="email">E-mail:</label><br />
                        <input type="email" id="email" name="email" size="30" required>
                    </td>
                </tr>
                <tr>
                    <td>
                        <label for="password">Password:</label><br />
                        <input type="password" id="password" name="password" required>
                    </td>
                    <td></td>
                    <td>
                        <label for="password_conf">Re-type password:</label><br />
                        <input type="password" id="password_conf" name="password_conf" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label for="secret">Secret (Security Question):</label><br />
                        <input type="text" id="secret" name="secret" size="40" required>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <label>
                            <input type="checkbox" name="mail_activation"> E-mail activation required
                        </label>
                    </td>
                </tr>
            </table>
            <br>
            <button type="submit" name="action" value="create">Create User</button>
        </form>
        <br>
        <?= $message; ?>
    </main>

    <?php if (isset($link)) $link->close(); ?>
</body>
</html>