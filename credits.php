<?php
/**
 * bWAPP - Credits Page
 * Hiện đại hóa cho PHP 8.x - Bảo toàn Layout ID & CSS
 */

declare(strict_types=1);

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/selections.php';

// 1. Hệ thống xử lý POST tập trung (Bug Jump & Security Level)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Xử lý chuyển hướng Bug nhanh
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

$login_display = isset($_SESSION["login"]) ? htmlspecialchars(ucwords($_SESSION["login"])) : "Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - Credits</title>
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
            <td><a href="user_extra.php">Create User</a></td>
            <td><a href="security_level_set.php">Set Security Level</a></td>
            <td><a href="reset.php" onclick="return confirm('All settings will be cleared. Are you sure?');">Reset</a></td>          
            <td><font color="#ffb717">Credits</font></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?= $login_display ?></font></td>
        </tr>
    </table>
</div>

<div id="main">
    <h1>Credits</h1>

    <p>O yeah... who am I? Well my name is Malik. I'm a security consultant working for my own company, <a href="" target="_blank">MME</a>.<br />
    We are specialized in Penetration Testing, Ethical Hacking, InfoSec Training, and Evil Bee Hunting.</p>

    <p>Download our <a href="" target="_blank">What is bWAPP?</a> introduction tutorial, including free materials and exercises...<br />
    I'm also happy to give bWAPP talks and workshops at your security convention or seminar!</p>

    <p>Need a training? We offer the following exclusive courses and workshops (on demand, at your location):</p>

    <ul>
        <li>Attacking & Defending Web Apps with bWAPP : 2-day Web Application Security course (<a href="" target="_blank">pdf</a>)</li>
        <li>Plant the Flags with bWAPP : 4-hour offensive Web Application Hacking workshop (<a href="" target="_blank">pdf</a>)</li>
        <li>Ethical Hacking Basics : 1-day Ethical Hacking course (<a href="" target="_blank">pdf</a>)</li>
        <li>Ethical Hacking Advanced : 1-day comprehensive Ethical Hacking course (<a href="" target="_blank">pdf</a>)</li>
        <li>Windows Server 2012 Security : 2-day Windows Security course (<a href="" target="_blank">pdf</a>)</li>
    </ul>

    <p>Special thanks to the Netsparker team!</p>
</div>

<div id="side">
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="http://be.linkedin.com/in/malikmesellem" target="_blank" class="button"><img src="./images/linkedin.png" alt="LinkedIn"></a>
    <a href="http://www.facebook.com/pages/MME-IT-Audits-Security/104153019664877" target="_blank" class="button"><img src="./images/facebook.png" alt="Facebook"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png" alt="Blogger"></a>
</div>

<div id="disclaimer">
    <p>bWAPP is licensed under Creative Commons. &copy; 2014-2026 MME BVBA / Follow <a href="http://twitter.com/MME_IT" target="_blank">@MME_IT</a> on Twitter!</p>
</div>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee">
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
        <font size="4">Current: <b><?= htmlspecialchars($security_level) ?></b></font>
    </form>
</div>

<div id="bug">
    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <label>Choose your bug:</label><br />
        <select name="bug">
            <?php
            foreach ($bugs as $key => $value) {
               $bug_entry = explode(",", trim($value));
               echo "<option value='" . htmlspecialchars((string)$key) . "'>" . htmlspecialchars($bug_entry[0]) . "</option>";
            }
            ?>
        </select>
        <button type="submit" name="form_bug" value="submit">Hack</button>
    </form>
</div>

</body>
</html>