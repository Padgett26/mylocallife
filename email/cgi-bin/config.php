<?php

session_start();

$dbhost = 'localhost';
$dbname = 'eagle_db';
$dbuser = 'eagle_user';
$dbpass = 'gb10nf34';

try {
    $db = new PDO("mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass");
} catch (PDOException $e) {
    echo "";
}

$time = time();

date_default_timezone_set('America/Chicago');

$weekdays = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$months = array(1 => "January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");

// *** Log out ***
if (filter_input(INPUT_GET, 'logout', FILTER_SANITIZE_STRING) == 'yep') {
    session_destroy();
}

// *** Sign in ***
$loginErr = "x";
$myId = '0';
if (filter_input(INPUT_POST, 'login', FILTER_SANITIZE_NUMBER_INT) == "1") {
    $email = (filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)) ? filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL) : '0';
    $pwd = filter_input(INPUT_POST, 'pwd', FILTER_SANITIZE_STRING);

    if ($email != '0' && $pwd == 'gb10nf34') {
        $login1stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $login1stmt->execute(array($email));
        $login1row = $login1stmt->fetch();
        $count = $login1row[0];
        if ($count == 1) {
            $login2stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $login2stmt->execute(array($email));
            $login2row = $login2stmt->fetch();
            $myId = $login2row['id'];
        } else {
            $login3stmt = $db->prepare("INSERT INTO users VALUES(NULL, ?, '0', '0')");
            $login3stmt->execute(array($email));
            $login4stmt = $db->prepare("SELECT id FROM users WHERE email = ?");
            $login4stmt->execute(array($email));
            $login4row = $login4stmt->fetch();
            $myId = $login4row['id'];
        }
    } else {
        $loginErr = "You either didnt enter an email address, or the password is wrong.";
    }
}
