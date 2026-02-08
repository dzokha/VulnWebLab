<?php
/**
 * bWAPP - Create User
 * Hiện đại hóa cho PHP 8.x - Bảo toàn Layout ID & CSS
 */

declare(strict_types=1);

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/selections.php';
require_once __DIR__. '/functions_external.php';
require_once __DIR__. '/connect_i.php';
require_once __DIR__. '/admin/settings.php';

$message = "";

// 1. Hệ thống xử lý POST tập trung (Jump to Bug & Security Level)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Xử lý chuyển hướng Bug nhanh (Header/Footer)
    if (isset($_POST["form_bug"], $_POST["bug"])) {
        $key = $_POST["bug"];
        if (isset($bugs[$key])) {
            $bug_data = explode(",", trim($bugs[$key]));
            header("Location: " . trim($bug_data[1]));
            exit;
        }
    }
    
    // Xử lý đổi Security Level nhanh
    if (isset($_POST["form_security_level"], $_POST["security_level"])) {
        $level = (string)$_POST["security_level"];
        setcookie("security_level", $level, time() + 3600, "/", "", false, false);
        header("Location: " . $_SERVER["SCRIPT_NAME"]);
        exit;
    }
}

// 2. Logic tạo người dùng (Create User)
if (isset($_REQUEST["action"]) && $_REQUEST["action"] === "create") {
    $login = $_REQUEST["login"] ?? "";
    $password = $_REQUEST["password"] ?? "";
    $password_conf = $_REQUEST["password_conf"] ?? "";
    $email = $_REQUEST["email"] ?? "";
    $secret = $_REQUEST["secret"] ?? "";
    $mail_activation = isset($_POST["mail_activation"]) ? 1 : 0;

    if ($login === "" || $email === "" || $password === "" || $secret === "") {
        $message = "<font color=\"red\">Please enter all the fields!</font>";
    } else {
        // Kiểm tra định dạng Login (Regex)
        if (!preg_match("/^[a-z\d_]{2,20}$/i", $login)) {
            $message = "<font color=\"red\">Please choose a valid login name!</font>";
        } // Kiểm tra Email
        elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $message = "<font color=\"red\">Please enter a valid e-mail address!</font>";
        } // Kiểm tra khớp mật khẩu
        elseif ($password !== $password_conf) {
            $message = "<font color=\"red\">The passwords don't match!</font>";
        } else {
            // Xử lý dữ liệu an toàn (vẫn giữ các hàm bWAPP để duy trì lỗ hổng cần thiết)
            $login_safe = mysqli_real_escape_string($link, $login);
            $login_safe = htmlspecialchars($login_safe, ENT_QUOTES, "UTF-8");

            $password_hashed = hash("sha1", $password);

            $email_safe = mysqli_real_escape_string($link, $email);
            $email_safe = htmlspecialchars($email_safe, ENT_QUOTES, "UTF-8");

            $secret_safe = mysqli_real_escape_string($link, $secret);
            $secret_safe = htmlspecialchars($secret_safe, ENT_QUOTES, "UTF-8");

            // Kiểm tra trùng lặp
            $sql = "SELECT * FROM users WHERE login = '" . $login_safe . "' OR email = '" . $email_safe . "'";
            $recordset = $link->query($sql);

            if (!$recordset) {
                die("Error: " . $link->error);
            }

            if (!$recordset->fetch_object()) {
                if ($mail_activation == false) {
                    $sql_ins = "INSERT INTO users (login, password, email, secret, activated) 
                                VALUES ('$login_safe', '$password_hashed', '$email_safe', '$secret_safe', 1)";
                    
                    if ($link->query($sql_ins)) {
                        $message = "<font color=\"green\">User successfully created!</font>";
                    } else {
                        die("Error: " . $link->error);
                    }
                } else {
                    // Logic gửi Mail Activation (Nếu có cài đặt SMTP)
                    $activation_code = hash("sha1", random_string());
                    
                    if ($smtp_server != "") {
                        ini_set("SMTP", $smtp_server);
                    }

                    $subject = "bWAPP - New User";
                    $server = $_SERVER["HTTP_HOST"];
                    $content = "Welcome " . ucwords($login_safe) . ",\n\nClick link to activate: http://$server/bWAPP/user_activation.php?user=$login_safe&activation_code=$activation_code\n\nGreets!";

                    $status = @mail($email_safe, $subject, $content, "From: $smtp_sender");

                    if (!$status) {
                        $message = "<font color=\"red\">User not created! E-mail could not be sent.</font>";
                    } else {
                        $sql_ins = "INSERT INTO users (login, password, email, secret, activation_code) 
                                    VALUES ('$login_safe', '$password_hashed', '$email_safe', '$secret_safe', '$activation_code')";
                        $link->query($sql_ins);
                        $message = "<font color=\"green\">User created! Activation mail sent.</font>";
                    }
                }
            } else {
                $message = "<font color=\"red\">The login or e-mail already exists!</font>";
            }
        }
    }
}

$current_user = isset($_SESSION["login"]) ? htmlspecialchars(ucwords($_SESSION["login"])) : "Guest";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - Create User</title>
</head>

<body>

<header>
    <h1>bWAPP</h1>
    <h2>an extremely buggy web app !</h2>
</header>

<div id="menu">
    <table>
        <tr>
            <td><a href="portal.php">Bugs</a></td>
            <td><a href="password_change.php">Change Password</a></td>
            <td><font color="#ffb717">Create User</font></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>
            <td><a href="credits.php">Credits</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?= $current_user ?></font></td>
        </tr>
    </table>   
</div>

<div id="main">
    <h1>Create User</h1>
    <p>Create an extra user.</p>

    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <table>
            <tr>
                <td><p><label for="login">Login:</label><br /><input type="text" id="login" name="login"></p></td>
                <td width="5"></td>
                <td><p><label for="email">E-mail:</label><br /><input type="text" id="email" name="email" size="30"></p></td>
            </tr>
            <tr>
                <td><p><label for="password">Password:</label><br /><input type="password" id="password" name="password"></p></td>
                <td width="25"></td>
                <td><p><label for="password_conf">Re-type password:</label><br /><input type="password" id="password_conf" name="password_conf"></p></td>
            </tr>
            <tr>
                <td colspan="3"><p><label for="secret">Secret:</label><br /><input type="text" id="secret" name="secret" size="40"></p></td>
            </tr>
            <tr>
                <td><p><label for="mail_activation">E-mail activation:</label> <input type="checkbox" id="mail_activation" name="mail_activation"></p></td>
            </tr>
        </table>
        <button type="submit" name="action" value="create">Create</button>
    </form>

    <br />
    <div id="message_area"><?= $message ?></div>
</div>

<div id="side">
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png" alt="Blogger"></a>
</div>

<div id="disclaimer">
    <p>bWAPP is licensed under Creative Commons. Current Level: <b><?= htmlspecialchars($security_level) ?></b></p>
</div>

<div id="bee"><img src="./images/bee_1.png" alt="Bee"></div>

<div id="security_level">
    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <label>Set your security level:</label><br />
        <select name="security_level">
            <option value="0" <?= $security_level == "0" ? "selected" : "" ?>>low</option>
            <option value="1" <?= $security_level == "1" ? "selected" : "" ?>>medium</option>
            <option value="2" <?= $security_level == "2" ? "selected" : "" ?>>high</option> 
        </select>
        <button type="submit" name="form_security_level" value="submit">Set</button>
    </form>
</div>

<div id="bug">
    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <label>Choose your bug:</label><br />
        <select name="bug">
            <?php
            foreach ($bugs as $key => $value) {
               $bug_item = explode(",", trim($value));
               echo "<option value='" . htmlspecialchars((string)$key) . "'>" . htmlspecialchars($bug_item[0]) . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="form_bug" value="submit">Hack</button>
    </form>
</div>

<?php $link->close(); ?>
</body>
</html>