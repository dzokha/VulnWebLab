<?php
/**
 * bWAPP - Reset Script
 * Hiện đại hóa cho PHP 8.2+ - Bảo toàn Layout ID & CSS
 */

declare(strict_types=1);

require_once __DIR__. '/security.php';
require_once __DIR__. '/security_level_check.php';
require_once __DIR__. '/selections.php';
require_once __DIR__. '/connect_i.php';

$message = "";
$db_reset = false;

// 1. Hệ thống xử lý POST tập trung (Bug Jump & Security Level)
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
        $level = (string)$_POST["security_level"];
        setcookie("security_level", $level, time() + 3600, "/", "", false, false);
        header("Location: " . $_SERVER["SCRIPT_NAME"]);
        exit;
    }
}

// 2. Xử lý xóa Cookies (Dọn dẹp dấu vết bài Lab)
$cookies_to_delete = [
    "admin", "movie_genre", "secret", "top_security", 
    "top_security_nossl", "top_security_ssl"
];
foreach ($cookies_to_delete as $cookie) {
    setcookie($cookie, "", time() - 3600, "/");
}

// 3. Kiểm tra quyền Admin (Chỉ Admin mới được Reset hệ thống)
if ((int)($_SESSION["admin"] ?? 0) !== 1) {
    $message = "<p>You don't have enough privileges for this action!</p><p>Contact your master bee...</p>";
    if (isset($link)) $link->close();
} else {
    // Xử lý File (Xóa các file do các bài tập tạo ra)
    $files_to_unlink = [
        "documents/.htaccess",
        "passwords/accounts.txt",
        "ssii.shtml",
        "logs/visitors.txt"
    ];
    foreach ($files_to_unlink as $file) {
        if (file_exists($file)) {
            @unlink($file);
        }
    }

    // 4. Reset Database (Nếu có tham số secret=bWAPP)
    if (isset($_GET["secret"]) && $_GET["secret"] === "bWAPP") {
        $link->query("DROP DATABASE IF EXISTS bWAPP");
        $link->query("CREATE DATABASE IF NOT EXISTS bWAPP CHARACTER SET utf8");
        $link->select_db("bWAPP");

        // Tái tạo bảng Users
        $sql_users = "CREATE TABLE users (
            id int(10) NOT NULL AUTO_INCREMENT,
            login varchar(100), password varchar(100), 
            email varchar(100), secret varchar(100), 
            activation_code varchar(100), activated tinyint(1) DEFAULT '0',
            reset_code varchar(100), admin tinyint(1) DEFAULT '0',
            PRIMARY KEY (id)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
        $link->query($sql_users);

        $sql_ins_users = "INSERT INTO users (login, password, email, secret, activated, admin) VALUES
            ('A.I.M.', '6885858486f31043e5839c735d99457f045affd0', 'bwapp-aim@mailinator.com', 'A.I.M.', 1, 1),
            ('bee', '6885858486f31043e5839c735d99457f045affd0', 'bwapp-bee@mailinator.com', 'Any bugs?', 1, 1)";
        $link->query($sql_ins_users);

        $db_reset = true;
    }

    // Luôn Reset các bảng Lab khác
    $tables = [
        "blog" => "id int(10) NOT NULL AUTO_INCREMENT, owner varchar(100), entry varchar(500), date datetime, PRIMARY KEY (id)",
        "visitors" => "id int(10) NOT NULL AUTO_INCREMENT, ip_address varchar(50), user_agent varchar(500), date datetime, PRIMARY KEY (id)",
        "movies" => "id int(10) NOT NULL AUTO_INCREMENT, title varchar(100), release_year varchar(100), genre varchar(100), main_character varchar(100), imdb varchar(100), tickets_stock int(10), PRIMARY KEY (id)",
        "heroes" => "id int(10) NOT NULL AUTO_INCREMENT, login varchar(100), password varchar(100), secret varchar(100), PRIMARY KEY (id)"
    ];

    foreach ($tables as $name => $schema) {
        $link->query("DROP TABLE IF EXISTS $name");
        $link->query("CREATE TABLE $name ($schema) ENGINE=InnoDB DEFAULT CHARSET=utf8");
    }

    // Chèn dữ liệu Movies
    $sql_populate_movies = "INSERT INTO movies (title, release_year, genre, main_character, imdb, tickets_stock) VALUES 
        ('G.I. Joe: Retaliation', '2013', 'action', 'Cobra Commander', 'tt1583421', 100),
        ('Iron Man', '2008', 'action', 'Tony Stark', 'tt0371746', 53),
        ('Man of Steel', '2013', 'action', 'Clark Kent', 'tt0770828', 78),
        ('Terminator Salvation', '2009', 'sci-fi', 'John Connor', 'tt0438488', 100),
        ('The Amazing Spider-Man', '2012', 'action', 'Peter Parker', 'tt0948470', 13),
        ('The Cabin in the Woods', '2011', 'horror', 'Some zombies', 'tt1259521', 666),
        ('The Dark Knight Rises', '2012', 'action', 'Bruce Wayne', 'tt1345836', 3),
        ('The Fast and the Furious', '2001', 'action', 'Brian O\'Connor', 'tt0232500', 40),
        ('The Incredible Hulk', '2008', 'action', 'Bruce Banner', 'tt0800080', 23),
        ('World War Z', '2013', 'horror', 'Gerry Lane', 'tt0816711', 0)";
    $link->query($sql_populate_movies);

    $sql_populate_heroes = "INSERT INTO heroes (login, password, secret) VALUES
        ('neo', 'trinity', 'Oh why didn\'t I took that BLACK pill?'),
        ('alice', 'loveZombies', 'There\'s a cure!'),
        ('thor', 'Asgard', 'Oh, no... this is Earth... isn\'t it?'),
        ('wolverine', 'Log@N', 'What\'s a Magneto?'),
        ('johnny', 'm3ph1st0ph3l3s', 'I\'m the Ghost Rider!'),
        ('seline', 'm00n', 'It wasn\'t the Lycans. It was you.')";
    $link->query($sql_populate_heroes);

    $link->close();

    // Nếu reset toàn bộ DB -> Thoát session và về Login
    if ($db_reset) {
        session_unset();
        session_destroy();
        header("Location: login.php");
        exit;
    }

    $message = "<p class='success'>The application has been reset!</p>";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - Reset</title>
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
            <td><font color="#ffb717">Reset</font></td>
            <td><a href="credits.php">Credits</a></td>
            <td><a href="logout.php" onclick="return confirm('Are you sure you want to leave?');">Logout</a></td>
            <td><font color="red">Welcome <?= htmlspecialchars(ucwords($_SESSION["login"] ?? "Guest")) ?></font></td>
        </tr>
    </table>
</div>

<div id="main">
    <h1>Reset</h1>
    <div id="message_area"><?= $message ?></div>
</div>

<div id="side">
    <a href="http://twitter.com/MME_IT" target="_blank" class="button"><img src="./images/twitter.png"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" class="button"><img src="./images/blogger.png"></a>
</div>

<div id="disclaimer">
    <p>bWAPP is licensed under Creative Commons. Current Level: <b><?= htmlspecialchars($security_level) ?></b></p>
</div>

<div id="bee"><img src="./images/bee_1.png"></div>

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

</body>
</html>