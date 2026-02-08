<?php
/**
 * bWAPP - HTML Injection (GET)
 * Hiện đại hóa cho PHP 8.x - Giữ nguyên Layout ID
 */

declare(strict_types=1);

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/functions_external.php';
require_once __DIR__. '/selections.php';

// 1. Xử lý Logic điều hướng (Jump to Bug & Security Level)
// Sửa lỗi: Đảm bảo POST action thực thi khi dùng chung action SCRIPT_NAME
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (isset($_POST["form_bug"], $_POST["bug"])) {
        $key = $_POST["bug"];
        if (isset($bugs[$key])) {
            $bug_data = explode(",", trim($bugs[$key]));
            header("Location: " . trim($bug_data[1]));
            exit;
        }
    }
    
    if (isset($_POST["form_security_level"], $_POST["security_level"])) {
        $level = $_POST["security_level"];
        setcookie("security_level", (string)$level, time() + 3600, "/", "", false, false);
        header("Location: " . $_SERVER["SCRIPT_NAME"]);
        exit;
    }
}

// 2. Hàm xử lý HTML Injection dựa trên Security Level (Sử dụng Match cho PHP 8)
function htmli(string $data): string {
    $level = $_COOKIE["security_level"] ?? "0";
    
    return match($level) {
        "1" => xss_check_1($data),
        "2" => xss_check_3($data),
        default => no_check($data),
    };
}

$login_name = isset($_SESSION["login"]) ? ucwords($_SESSION["login"]) : "Guest";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - HTML Injection</title>
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
            <td><a href="credits.php">Credits</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?= htmlspecialchars($login_name) ?></font></td>
        </tr>
    </table>   
</div>

<div id="main">
    <h1>HTML Injection - Reflected (GET)</h1>
    <p>Enter your first and last name:</p>

    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="GET">
        <p>
            <label for="firstname">First name:</label><br />
            <input type="text" id="firstname" name="firstname">
        </p>
        <p>
            <label for="lastname">Last name:</label><br />
            <input type="text" id="lastname" name="lastname">
        </p>
        <button type="submit" name="form" value="submit">Go</button>  
    </form>

    <br />
    <div id="result">
    <?php
    if (isset($_GET["firstname"], $_GET["lastname"])) {   
        $first = $_GET["firstname"];
        $last = $_GET["lastname"];    

        if ($first === "" || $last === "") {
            echo "<p><font color=\"red\">Please enter both fields...</font></p>";       
        } else { 
            // Đây là nơi lỗ hổng xảy ra
            echo "Welcome " . htmli($first) . " " . htmli($last);   
        }
    }
    ?>
    </div>
</div>

<div id="side">    
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="http://be.linkedin.com/in/malikmesellem" target="_blank" class="button"><img src="./images/linkedin.png" alt="LinkedIn"></a>
    <a href="http://www.facebook.com/pages/MME-IT-Audits-Security/104153019664877" target="_blank" class="button"><img src="./images/facebook.png" alt="Facebook"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png" alt="Blogger"></a>
</div>     

<div id="disclaimer">
    <p>bWAPP is licensed under <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank"><img style="vertical-align:middle" src="./images/cc.png" alt="CC"></a> &copy; 2014-2026 MME BVBA / Ask for our cheat sheet!</p>
</div>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee">
</div>

<div id="security_level">
    <form action="<?= htmlspecialchars($_SERVER["SCRIPT_NAME"]) ?>" method="POST">
        <label>Set your security level:</label><br />
        <select name="security_level">
            <option value="0" <?= ($security_level === "0") ? "selected" : "" ?>>low</option>
            <option value="1" <?= ($security_level === "1") ? "selected" : "" ?>>medium</option>
            <option value="2" <?= ($security_level === "2") ? "selected" : "" ?>>high</option> 
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