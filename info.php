<?php
/**
 * bWAPP - Info Page (Modernized for PHP 8.2+)
 * Mục tiêu: Hiển thị thông tin dự án với cấu trúc HTML5 chuẩn.
 */

declare(strict_types=1);

// Khởi tạo session nếu cần hiển thị thông tin người dùng ở menu
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <link rel="stylesheet" type="text/css" href="stylesheets/stylesheet.css" media="screen" />
    <link rel="shortcut icon" href="images/favicon.ico" type="image/x-icon" />

    <script src="js/html5.js" defer></script>

    <title>bWAPP - Info</title>
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
            <td><a href="user_new.php">New User</a></td>
            <td><span style="color: #ffb717;">Info</span></td>
            <td><a href="training.php">Talks & Training</a></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank" rel="noopener">Blog</a></td>
        </tr>
    </table>
</nav>

<main id="main">
    <h1>Info</h1>

    <section>
        <p>
            bWAPP, or a <i>buggy web application</i>, is a free and open source deliberately insecure web application.<br />
            It helps security enthusiasts, developers and students to discover and to prevent web vulnerabilities.<br />
            bWAPP prepares one to conduct successful penetration testing and ethical hacking projects.
        </p>

        <p>
            What makes bWAPP so unique? Well, it has over 100 web vulnerabilities!<br />
            It covers all major known web bugs, including all risks from the 
            <a href="https://www.owasp.org" target="_blank" rel="noopener">OWASP</a> Top 10 project.
        </p>
    </section>

    

    <section>
        <p>
            bWAPP is a PHP application that uses a MySQL database. It can be hosted on Linux, Windows and Mac with Apache/IIS and MySQL.
            It can also be installed with WAMP or XAMPP.<br />
            Another possibility is to download the <i>bee-box</i>, a custom Linux VM pre-installed with bWAPP.
        </p>

        <p>
            Download our <a href="http://goo.gl/uVBGnq" target="_blank" rel="noopener">What is bWAPP?</a> 
            introduction tutorial, including free exercises...
        </p>
    </section>

    <p>
        bWAPP is for educational purposes. Education, the most powerful weapon which we can use to change the world.<br>
        Have fun with this free and open source project!
    </p>

    <p>Cheers, Malik Mesellem</p>
</main>

<aside id="side">
    <a href="https://twitter.com/MME_IT" target="_blank" rel="noopener" class="button"><img src="./images/twitter.png" alt="Twitter"></a>
    <a href="https://be.linkedin.com/in/malikmesellem" target="_blank" rel="noopener" class="button"><img src="./images/linkedin.png" alt="LinkedIn"></a>
    <a href="http://itsecgames.blogspot.com" target="_blank" rel="noopener" class="button"><img src="./images/blogger.png" alt="Blogger"></a>
</aside>

<footer id="disclaimer">
    <p>
        bWAPP is licensed under 
        <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank">
            <img style="vertical-align:middle" src="./images/cc.png" alt="CC License">
        </a> 
        &copy; 2014-2026 MME BVBA / Follow <a href="https://twitter.com/MME_IT" target="_blank" rel="noopener">@MME_IT</a> 
        on Twitter and ask for our cheat sheet!
    </p>
</footer>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee Logo">
</div>

</body>
</html>