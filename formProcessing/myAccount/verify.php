<?php
$resendId = filter_input(INPUT_GET, 'verId', FILTER_SANITIZE_NUMBER_INT);
$stmt1 = $db->prepare("SELECT firstName, email FROM users WHERE id=?");
$stmt1->execute(array(
    $resendId
));
$row1 = $stmt1->fetch();
$firstName = $row1['firstName'];
$email = $row1['email'];
$stmt2 = $db->prepare("UPDATE users SET verifyCode=? WHERE id=?");
$stmt2->execute(array(
    $time,
    $resendId
));
sendVerificationEmail($resendId, $firstName, $email, $time);