<?php
// Tắt hiển thị lỗi ra màn hình để tránh lỗi 500 nếu có cảnh báo nhỏ, 
// nhưng vẫn ghi log để debug.

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$message = 'Click <a href="install.php?install=yes">here</a> to install bWAPP.';
$db_status = 0; 

if (isset($_REQUEST["install"]) && $_REQUEST["install"] == "yes") {

    // Phải dùng đường dẫn tuyệt đối để tránh lỗi file không tìm thấy
    $configFile = __DIR__ . '/config.inc.php';
    if (file_exists($configFile)) {
        require_once $configFile;
    } else {
        die("Lỗi: Không tìm thấy file config.inc.php tại " . __DIR__);
    }

    // Thiết lập cổng mặc định nếu trong config quên chưa ghi
    $db_port = isset($port) ? (int)$port : 3306;

    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

    try {
        // Kết nối - Sử dụng 127.0.0.1 thay vì localhost để nhanh hơn trên Mac
        $link = new mysqli($server, $username, $password, "", $db_port);
        
        // SỬA LỖI TÊN BIẾN Ở ĐÂY: Dùng $link thay vì $mysqli
        $link->set_charset('utf8mb4');

        // Kiểm tra DB bWAPP
        $check_db = $link->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'bWAPP'");

        if ($check_db->num_rows == 0) {
            $link->query("CREATE DATABASE IF NOT EXISTS bWAPP CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            $link->select_db("bWAPP");

            // Tạo bảng Users
            $link->query("CREATE TABLE IF NOT EXISTS users (
                id int(10) NOT NULL AUTO_INCREMENT,
                login varchar(100), password varchar(100), email varchar(100), 
                secret varchar(100), activated tinyint(1) DEFAULT '0', 
                admin tinyint(1) DEFAULT '0', PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

            // --- Tạo bảng USERS ---
            $sql_users = "CREATE TABLE IF NOT EXISTS users (
                id int(10) NOT NULL AUTO_INCREMENT,
                login varchar(100) DEFAULT NULL,
                password varchar(100) DEFAULT NULL,
                email varchar(100) DEFAULT NULL,
                secret varchar(100) DEFAULT NULL,
                activation_code varchar(100) DEFAULT NULL,
                activated tinyint(1) DEFAULT '0',
                reset_code varchar(100) DEFAULT NULL,
                admin tinyint(1) DEFAULT '0',
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1";
            $link->query($sql_users);

            // Chèn dữ liệu mẫu Users
            $sql_populate_users = "INSERT INTO users (login, password, email, secret, activated, admin) VALUES
                ('A.I.M.', '6885858486f31043e5839c735d99457f045affd0', 'bwapp-aim@mailinator.com', 'A.I.M. or Authentication Is Missing', 1, 1),
                ('bee', '6885858486f31043e5839c735d99457f045affd0', 'bwapp-bee@mailinator.com', 'Any bugs?', 1, 1)";
            $link->query($sql_populate_users);

            // --- Tạo bảng BLOG ---
            $sql_blog = "CREATE TABLE IF NOT EXISTS blog (
                id int(10) NOT NULL AUTO_INCREMENT,
                owner varchar(100) DEFAULT NULL,
                entry varchar(500) DEFAULT NULL,
                date datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $link->query($sql_blog);

            // --- Tạo bảng VISITORS ---
            $sql_visitors = "CREATE TABLE IF NOT EXISTS visitors (
                id int(10) NOT NULL AUTO_INCREMENT,
                ip_address varchar(50) DEFAULT NULL,
                user_agent varchar(500) DEFAULT NULL,
                date datetime DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $link->query($sql_visitors);

            // --- Tạo bảng MOVIES ---
            $sql_movies = "CREATE TABLE IF NOT EXISTS movies (
                id int(10) NOT NULL AUTO_INCREMENT,
                title varchar(100) DEFAULT NULL,
                release_year varchar(100) DEFAULT NULL,
                genre varchar(100) DEFAULT NULL,
                main_character varchar(100) DEFAULT NULL,
                imdb varchar(100) DEFAULT NULL,
                tickets_stock int(10) DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $link->query($sql_movies);

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

            // --- Tạo bảng HEROES ---
            $sql_heroes = "CREATE TABLE IF NOT EXISTS heroes (
                id int(10) NOT NULL AUTO_INCREMENT,
                login varchar(100) DEFAULT NULL,
                password varchar(100) DEFAULT NULL,
                secret varchar(100) DEFAULT NULL,
                PRIMARY KEY (id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
            $link->query($sql_heroes);

            $sql_populate_heroes = "INSERT INTO heroes (login, password, secret) VALUES
                ('neo', 'trinity', 'Oh why didn\'t I took that BLACK pill?'),
                ('alice', 'loveZombies', 'There\'s a cure!'),
                ('thor', 'Asgard', 'Oh, no... this is Earth... isn\'t it?'),
                ('wolverine', 'Log@N', 'What\'s a Magneto?'),
                ('johnny', 'm3ph1st0ph3l3s', 'I\'m the Ghost Rider!'),
                ('seline', 'm00n', 'It wasn\'t the Lycans. It was you.')";
            $link->query($sql_populate_heroes);

            $message = "<b style='color:green'>bWAPP has been installed successfully!</b>";
            $db_status = 1;
        } else {
            $message = "<b style='color:orange'>The bWAPP database already exists...</b>";
            $db_status = 1;
        }
        $link->close();
    } catch (Exception $e) {
        $message = "<b style='color:red'>Lỗi kết nối MySQL: " . $e->getMessage() . " (Port đang dùng: $db_port)</b>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />
    <script src="js/html5.js"></script>
    <title>bWAPP - Installation</title>
</head>

<body>
<header>
    <h1>bWAPP</h1>
    <h2>an extremely buggy web app !</h2>
</header>

<div id="menu">
    <table>
        <tr>
        <?php
        // Đổi $db thành $db_status để khớp với phần xử lý PHP bên trên
        if(isset($db_status) && $db_status == 1)
        {
        ?>
            <td><a href="login.php">Login</a></td>
            <td><a href="user_new.php">New User</a></td>
            <td><a href="info.php">Info</a></td>
            <td><a href="training.php">Talks & Training</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
        <?php
        }
        else
        {
        ?>
            <td><font color="#ffb717">Install</font></td>
            <td><a href="info_install.php">Info</a></td>
            <td><a href="training_install.php">Talks & Training</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank">Blog</a></td>
        <?php
        }
        ?>
        </tr>
    </table>
</div> 

<div id="main">
    <h1>Installation</h1>
    <p><?php echo (isset($message)) ? $message : ""; ?></p>
</div>
    
<div id="side">
    <a href="http://twitter.com/MME_IT" target="blank_" class="button"><img src="./images/twitter.png"></a>
    <a href="http://be.linkedin.com/in/malikmesellem" target="blank_" class="button"><img src="./images/linkedin.png"></a>
    <a href="http://www.facebook.com/pages/MME-IT-Audits-Security/104153019664877" target="blank_" class="button"><img src="./images/facebook.png"></a>
    <a href="http://itsecgames.blogspot.com" target="blank_" class="button"><img src="./images/blogger.png"></a>
</div>     

<div id="disclaimer">
    <p>bWAPP is licensed under <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank"><img style="vertical-align:middle" src="./images/cc.png"></a> &copy; 2014 MME BVBA / Follow <a href="http://twitter.com/MME_IT" target="_blank">@MME_IT</a> on Twitter</p>
</div>
    
<div id="bee">
    <img src="./images/bee_1.png">
</div>

</body>
</html>