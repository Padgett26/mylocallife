<?php

$approvePhoto = filter_input(INPUT_POST, 'approvePhoto', FILTER_SANITIZE_NUMBER_INT);
$approve = filter_input(INPUT_POST, 'approve', FILTER_SANITIZE_NUMBER_INT);
$msgToAuthor = filter_input(INPUT_POST, 'msgToAuthor', FILTER_SANITIZE_STRING);

if ($approve == "1") {
    $appr1 = $db->prepare("UPDATE photoJournalism SET approved='1' WHERE id=?");
    $appr1->execute(array($approvePhoto));
} else {
    $appr2 = $db->prepare("SELECT authorId, photoTitle FROM photoJournalism WHERE id=?");
    $appr2->execute(array($approvePhoto));
    $approw2 = $appr2->fetch();
    $apprId = $approw2['authorId'];
    $subject = $approw2['photoTitle'];
    $appr3 = $db->prepare("SELECT firstName, email FROM users WHERE id=?");
    $appr3->execute(array($apprId));
    $approw3 = $appr3->fetch();
    $apprFirstName = $approw3['firstName'];
    $apprEmail = $approw3['email'];
    sendArticleEmail($msgToAuthor, $firstName, $email, $subject);

    if ($approve == '2') {

        $appr5 = $db->prepare("SELECT photoName, photoExt FROM photoList WHERE photoId=?");
        $appr5->execute(array($approvePhoto));
        while ($approw5 = $appr5->fetch()) {
            $apprpic1 = $approw5['photoName'];
            $apprpic1e = $approw5['photoExt'];
            if (file_exists("userPics/$apprId/$apprpic1.$apprpic1e")) {
                unlink("userPics/$apprId/$apprpic1.$apprpic1e");
            }
        }
        $appr6 = $db->prepare("DELETE FROM photoJournalism WHERE id=?");
        $appr6->execute(array($approvePhoto));
    }
}