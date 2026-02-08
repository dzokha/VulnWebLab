<?php
/**
 * bWAPP - Portal (Modernized for PHP 8.2+)
 */

declare(strict_types=1);

// Khởi tạo Session an toàn (Nên đặt trong security.php nhưng đặt ở đây để chắc chắn)
if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_samesite' => 'Lax',
    ]);
}

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/selections.php';

// Xử lý chuyển hướng khi chọn Bug
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["form"], $_POST["bug"])) {
    $key = $_POST["bug"];
    
    if (isset($bugs[$key])) {
        $bug_data = explode(",", trim($bugs[$key]));
        $target_page = $bug_data[1] ?? "";

        if (!empty($target_page) && file_exists(__DIR__ . "/" . $target_page)) {
            header("Location: " . $target_page);
            exit;
        }
    }
}

// Lấy thông tin người dùng an toàn
$login = $_SESSION["login"] ?? "Guest";
$current_security_level = $security_level ?? "low"; // Biến từ security_level_check.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js" defer></script>
    <title>bWAPP - Portal</title>
</head>

<body>

<header>
    <h1>bWAPP</h1>
    <h2>an extremely buggy web app !</h2>
</header>

<nav id="menu">
    <table>
        <tr>
            <td><span style="color: #ffb717;">Bugs</span></td>
            <td><a href="password_change.php">Change Password</a></td>
            <td><a href="user_extra.php">Create User</a></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><span style="color: red;">Welcome <?= htmlspecialchars(ucwords($login)); ?></span></td>
        </tr>
    </table>
</nav>

<main id="main">
    <h1>Portal</h1>
    <p><i>Which bug do you want to hack today? :)</i></p>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
        <select name="bug" size="10" id="select_portal" style="width: 100%; max-width: 500px;">
            <?php foreach ($bugs as $key => $value): ?>
                <?php 
                    $bug_info = explode(",", trim($value));
                    $bug_name = $bug_info[0] ?? "Unknown Bug";
                ?>
                <option value="<?= htmlspecialchars((string)$key); ?>">
                    <?= htmlspecialchars($bug_name); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <br><br>
        <button type="submit" name="form" value="submit" class="btn-hack">Hack</button>
    </form>
</main>

<aside id="side">
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png" alt="Blog"></a>
</aside>

<footer id="disclaimer">
    <p>bWAPP is licensed under Creative Commons. Current Security Level: <b><?= htmlspecialchars((string)$current_security_level); ?></b></p>
</footer>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee">
</div>

</body>
</html>