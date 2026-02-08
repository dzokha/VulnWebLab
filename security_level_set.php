<?php
/**
 * bWAPP - Set Security Level Page (Modernized for PHP 8.2+)
 */

declare(strict_types=1);

require_once __DiR__. '/security.php';
require_once __DiR__. '/selections.php';
require_once __DiR__. '/admin/settings.php';

/**
 * 1. Xử lý Logic thiết lập Security Level
 */
if ($_SERVER["REQUEST_METHOD"] === "POST" && (isset($_POST["form"]) || isset($_POST["form_security_level"])) && isset($_POST["security_level"])) {
    
    $level_input = (string)$_POST["security_level"];

    // Sử dụng MATCH expression (PHP 8.0+) để gán giá trị chính xác
    $security_level_cookie = match ($level_input) {
        "1"     => "1",
        "2"     => "2",
        default => "0",
    };

    // Chế độ Evil Bee (666)
    $final_level = (isset($evil_bee) && $evil_bee == 1) ? "666" : $security_level_cookie;

    // Thiết lập Cookie với cấu hình bảo mật hiện đại
    setcookie("security_level", $final_level, [
        'expires'  => time() + (60 * 60 * 24 * 365), // 1 year
        'path'     => '/',
        'secure'   => false, // Đổi thành true nếu chạy HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    header("Location: " . $_SERVER["SCRIPT_NAME"]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js" defer></script>
    <title>bWAPP - Set Security Level</title>
</head>

<body>

<header>
    <h1>bWAPP</h1>
    <h2>an extremely buggy web app !</h2>
</header>

<nav id="menu">
    <table>
        <tr>
            <td><a href="portal.php">Bugs</a></td>
            <td><a href="password_change.php">Change Password</a></td>
            <td><a href="user_extra.php">Create User</a></td>
            <td><span style="color: #ffb717;">Set Security Level</span></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><span style="color: red">Welcome <?= htmlspecialchars(ucwords($_SESSION["login"] ?? "Guest")) ?></span></td>
        </tr>
    </table>
</nav>

<main id="main">
    <h1>Set Security Level</h1>

    <p>Your current security level is: <strong><?= htmlspecialchars($security_level) ?></strong></p>

    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <fieldset>
            <legend>Adjust Difficulty</legend>
            <p>
                <label for="security_level">New level:</label><br />
                <select name="security_level" id="security_level">
                    <option value="0">low (easy)</option>
                    <option value="1">medium (average)</option>
                    <option value="2">high (difficult)</option>
                </select>
            </p>
            <button type="submit" name="form" value="submit">Update Level</button>
        </fieldset>
    </form>
</main>

<aside id="side">
    <a href="https://twitter.com/MME_IT" target="_blank" rel="noopener" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="https://be.linkedin.com/in/malikmesellem" target="_blank" rel="noopener" class="button"><img src="./images/linkedin.png" alt="LinkedIn"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" rel="noopener" class="button"><img src="./images/blogger.png" alt="Blogger"></a>
</aside>

<footer id="disclaimer">
    <p>
        bWAPP is licensed under <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank"><img style="vertical-align:middle" src="./images/cc.png" alt="CC"></a> 
        &copy; 2014-2026 MME BVBA / Need an exclusive <a href="http://www.mmebvba.com" target="_blank" rel="noopener">training</a>?
    </p>
</footer>

<section id="bug">
    <div id="security_level_toolbar">
        <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
            <label>Quick Set:</label>
            <select name="security_level">
                <option value="0">low</option>
                <option value="1">medium</option>
                <option value="2">high</option>
            </select>
            <button type="submit" name="form_security_level" value="submit">Set</button>
        </form>
    </div>

    <div id="bug_toolbar">
        <form action="portal.php" method="POST">
            <label>Jump to Bug:</label>
            <select name="bug">
                <?php
                foreach ($bugs as $key => $value) {
                    $bug = explode(",", trim($value));
                    echo "<option value='$key'>" . htmlspecialchars($bug[0]) . "</option>";
                }
                ?>
            </select>
            <button type="submit" name="form_bug" value="submit">Hack</button>
        </form>
    </div>
</section>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee">
</div>

</body>
</html>