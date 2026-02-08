<?php
/**
 * bWAPP - Change Password
 * Hiện đại hóa cho PHP 8.x - Giữ nguyên Layout ID
 */

declare(strict_types=1);

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/connect_i.php';
require_once __DIR__. '/selections.php';

$message = "";

// 1. Xử lý Logic hệ thống (Jump to Bug & Security Level)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Xử lý chuyển hướng Bug
    if (isset($_POST["form_bug"], $_POST["bug"])) {
        $key = $_POST["bug"];
        if (isset($bugs[$key])) {
            $bug_data = explode(",", trim($bugs[$key]));
            header("Location: " . trim($bug_data[1]));
            exit;
        }
    }
    
    // Xử lý đổi Security Level
    if (isset($_POST["form_security_level"], $_POST["security_level"])) {
        $level = (string)$_POST["security_level"];
        setcookie("security_level", $level, time() + 3600, "/", "", false, false);
        header("Location: " . $_SERVER["SCRIPT_NAME"]);
        exit;
    }
}

// 2. Xử lý Logic đổi mật khẩu
if (isset($_REQUEST["action"]) && $_REQUEST["action"] === "change") {
    $password_new = $_REQUEST["password_new"] ?? "";
    $password_conf = $_REQUEST["password_conf"] ?? "";
    $password_curr = $_REQUEST["password_curr"] ?? "";

    if ($password_new === "") {
        $message = "<font color=\"red\">Please enter a new password...</font>";
    } elseif ($password_new !== $password_conf) {
        $message = "<font color=\"red\">The passwords don't match!</font>";
    } else {
        $login = $_SESSION["login"];
        
        // Hash mật khẩu (SHA-1 theo logic gốc của bWAPP)
        $password_new_hashed = hash("sha1", $password_new);
        $password_curr_hashed = hash("sha1", $password_curr);

        // Chuẩn bị SQL kiểm tra mật khẩu hiện tại
        // Lưu ý: bWAPP để lỗ hổng SQLi ở đây mục đích để học tập
        $sql = "SELECT password FROM users WHERE login = '" . $login . "' AND password = '" . $password_curr_hashed . "'";
        
        $recordset = $link->query($sql);

        if (!$recordset) {
            die("Error: " . $link->error);
        }

        if ($recordset->fetch_object()) {
            // Cập nhật mật khẩu mới
            $sql_update = "UPDATE users SET password = '" . $password_new_hashed . "' WHERE login = '" . $login . "'";
            $result_update = $link->query($sql_update);

            if (!$result_update) {
                die("Error: " . $link->error);
            }

            $message = "<font color=\"green\">The password has been changed!</font>";
        } else {
            $message = "<font color=\"red\">The current password is not valid!</font>";
        }
    }
}

$login_display = isset($_SESSION["login"]) ? htmlspecialchars(ucwords($_SESSION["login"])) : "Guest";
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - Change Password</title>
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
            <td><font color="#ffb717">Change Password</font></td>
            <td><a href="user_extra.php">Create User</a></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?= $login_display ?></font></td>
        </tr>
    </table>
</div>

<div id="main">
    <h1>Change Password</h1>
    <p>Please change your password <b><?= $login_display ?></b>.</p>

    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <p>
            <label for="password_curr">Current password:</label><br />
            <input type="password" id="password_curr" name="password_curr">
        </p>

        <p>
            <label for="password_new">New password:</label><br />
            <input type="password" id="password_new" name="password_new">
        </p>

        <p>
            <label for="password_conf">Re-type new password:</label><br />
            <input type="password" id="password_conf" name="password_conf">
        </p>

        <button type="submit" name="action" value="change">Change</button>
    </form>

    <br />
    <div id="message_area">
        <?= $message ?>
    </div>
</div>

<div id="side">
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png"></a>
</div>

<div id="disclaimer">
    <p>bWAPP is licensed under Creative Commons. Current Level: <b><?= htmlspecialchars($security_level) ?></b></p>
</div>

<div id="bee">
    <img src="./images/bee_1.png">
</div>

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