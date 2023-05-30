<?php

$myInfoUp = filter_input(INPUT_POST, 'myInfoUp', FILTER_SANITIZE_NUMBER_INT);
$firstName = trim(filter_input(INPUT_POST, 'firstName', FILTER_SANITIZE_STRING));
$lastName = trim(filter_input(INPUT_POST, 'lastName', FILTER_SANITIZE_STRING));
$newEmail = trim(strtolower(filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL)));
$pwd1 = filter_input(INPUT_POST, 'pwd1', FILTER_SANITIZE_STRING);
$pwd2 = filter_input(INPUT_POST, 'pwd2', FILTER_SANITIZE_STRING);
$zip = trim(filter_input(INPUT_POST, 'zip', FILTER_SANITIZE_NUMBER_INT));
$theme = filter_input(INPUT_POST, 'theme', FILTER_SANITIZE_STRING);
$_SESSION['myTheme'] = $theme;
setcookie("myTheme", $_SESSION['myTheme'], $time + 1209600, "/", "mylocal.life", 0);
$deleteMe = (filter_input(INPUT_POST, 'deleteMe', FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";

if ($deleteMe == "1") {
    echo "<div style='margin:10px; padding:10px; border:2px solid red; font-weight:bold; text-align:center;'>Are you sure you want to delete you account on My Local Life?<br />";
    echo "If you have uploaded articles you want to save, I would suggest downloading your submissions before deleting your account.<br /><br />";
    echo "<form action='index.php?page=myAccount' method='post'><input type='radio' name='deleteMyId' value='$myInfoUp' /> Yes, delete me from the site<br />";
    echo "<input type='radio' name='deleteMyId' value='0' checked /> No, I want to stay on the site<br />";
    echo "<input type='submit' value=' Make it so... ' /></form></div>";
} else {
    $stmt = $db->prepare("SELECT email, accessLevel, salt FROM users WHERE id = ?");
    $stmt->execute(array($myInfoUp));
    $row = $stmt->fetch();
    $oldEmail = $row['email'];
    $oldAccessLevel = $row['accessLevel'];
    $salt = $row['salt'];
    if ($newEmail != $oldEmail) {
        if (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)) {
            $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email=?");
            $stmt->execute(array($newEmail));
            $row = $stmt->fetch();
            $email = ($row[0] >= 1) ? 0 : $newEmail;
            if ($email == 0) {
                $emailErrorBox = "border:1px solid red;";
                $errorMsg = "The email you entered seems to already be in use.";
                $myInfoBlock = "block";
            } else {
                if ($oldAccessLevel == '1') {
                    $substmt = $db->prepare("UPDATE users SET email=?, accessLevel=?, verifyCode=? WHERE id=?");
                    $substmt->execute(array($email, "0", $time, $myInfoUp));
                }
                sendVerificationEmail($myInfoUp, $firstName, $email, $time);
            }
        } else {
            $emailErrorBox = "border:1px solid red;";
            $errorMsg = "Please enter a valid email address.";
        }
    }
    $stmt2 = $db->prepare("UPDATE users SET firstName=?, lastName=?, zip=?, theme=? WHERE id=?");
    $stmt2->execute(array($firstName, $lastName, $zip, $theme, $myInfoUp));
    if ($pwd1 != "" && $pwd1 != " " && $pwd1 === $pwd2) {
        $hidepwd = hash('sha512', ($salt . $pwd1), FALSE);
        $stmt3 = $db->prepare("UPDATE users SET password=? WHERE id=?");
        $stmt3->execute(array($hidepwd, $myInfoUp));
    } elseif ($pwd1 != "" && $pwd1 != " " && $pwd1 != $pwd2) {
        $pwdErrorBox = "border:1px solid red;";
        $errorMsg = "Your passwords did not match.";
        $myInfoBlock = "block";
    }
}