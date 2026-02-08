<?php
/**
 * bWAPP - Talks & Training Page (Modernized for PHP 8.2+)
 * Cập nhật cấu trúc Semantic HTML5 và bảo mật liên kết.
 */

declare(strict_types=1);

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

    <title>bWAPP - Talks & Training</title>
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
            <td><a href="info.php">Info</a></td>
            <td><span style="color: #ffb717;">Talks & Training</span></td>
            <td><a href="http://itsecgames.blogspot.com" target="_blank" rel="noopener">Blog</a></td>
        </tr>
    </table>
</nav>

<main id="main">
    <h1>Talks & Training</h1>

    <section>
        <p>
            We are happy to give bWAPP talks and workshops at your security convention or seminar!<br />
            Our team has participated in major events like:
            <strong>B-Sides Orlando</strong>, 
            <strong>Infosecurity Belgium</strong>, 
            <strong>SANS</strong>, and the 
            <strong>TDI Symposium</strong>.
        </p>
    </section>

    

    <section>
        <p>Interested in hands-on skills training? We offer the following exclusive courses and workshops:</p>

        <ul class="training-list">
            <li>
                <strong>Attacking & Defending Web Apps with bWAPP</strong>: 
                2-day Web Application Security course (<a href="" target="_blank" rel="noopener">PDF Brochure</a>)
            </li>
            <li>
                <strong>Plant the Flags with bWAPP</strong>: 
                4-hour offensive Web Application Hacking workshop (<a href="" target="_blank" rel="noopener">PDF Brochure</a>)
            </li>
            <li>
                <strong>Ethical Hacking Basics</strong>: 
                1-day Ethical Hacking course (<a href="" target="_blank" rel="noopener">PDF Brochure</a>)
            </li>
            <li>
                <strong>Ethical Hacking Advanced</strong>: 
                1-day comprehensive Ethical Hacking course (<a href="" target="_blank" rel="noopener">PDF Brochure</a>)
            </li>
            <li>
                <strong>Windows Server Security</strong>: 
                2-day specialized Windows Security course (<a href="" target="_blank" rel="noopener">PDF Brochure</a>)
            </li>
        </ul>
    </section>

    <section class="contact-info">
        <p>
            All our courses and workshops can be scheduled on demand, at your location.<br />
            Don't hesitate to contact us for pricing and availability.
        </p>
        <p><em>Hope to see you soon!</em></p>
    </section>
</main>

<aside id="side">
    <a href="https://twitter.com/MME_IT" target="_blank" rel="noopener" class="button">
        <img src="./images/twitter.png" alt="Twitter">
    </a>
    <a href="https://be.linkedin.com/in/malikmesellem" target="_blank" rel="noopener" class="button">
        <img src="./images/linkedin.png" alt="LinkedIn">
    </a>
    <a href="http://itsecgames.blogspot.com" target="_blank" rel="noopener" class="button">
        <img src="./images/blogger.png" alt="Blogger">
    </a>
</aside>

<footer id="disclaimer">
    <p>
        bWAPP is licensed under 
        <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/4.0/" target="_blank">
            <img style="vertical-align:middle" src="./images/cc.png" alt="CC License">
        </a> 
        &copy; 2014-2026 MME BVBA / Need an exclusive <a href="http://www.mmebvba.com" target="_blank" rel="noopener">training</a>?
    </p>
</footer>

<div id="bee">
    <img src="./images/bee_1.png" alt="Bee Logo">
</div>

</body>
</html>