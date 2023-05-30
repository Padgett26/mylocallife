<?php

$reportArt = filter_input(INPUT_POST, 'reportArt', FILTER_SANITIZE_NUMBER_INT);
$approve = filter_input(INPUT_POST, 'approve', FILTER_SANITIZE_NUMBER_INT);
$msgToAuthor = filter_input(INPUT_POST, 'msgToAuthor', FILTER_SANITIZE_STRING);
$reportId = filter_input(INPUT_POST, 'reportId', FILTER_SANITIZE_NUMBER_INT);

$appr2 = $db->prepare("SELECT authorId, articleTitle FROM articles WHERE id=?");
$appr2->execute(array($reportArt));
$approw2 = $appr2->fetch();
$apprId = $approw2['authorId'];
$subject = $approw2['articleTitle'];
$appr3 = $db->prepare("SELECT firstName, email FROM users WHERE id=?");
$appr3->execute(array($apprId));
$approw3 = $appr3->fetch();
$apprFirstName = $approw3['firstName'];
$apprEmail = $approw3['email'];
sendArticleEmail($msgToAuthor, $firstName, $email, $subject);

if ($approve == "1") {
    $appr1 = $db->prepare("UPDATE reported SET clearedTime=? WHERE id=?");
    $appr1->execute(array($time, $reportId));
} elseif ($approve == '2') {
    $appr4 = $db->prepare("DELETE FROM reported WHERE id=?");
    $appr4->execute(array($reportId));

    $appr5 = $db->prepare("SELECT pic1Name, pic1Ext, pic2Name, pic2Ext FROM articles WHERE id=?");
    $appr5->execute(array($reportArt));
    $approw5 = $appr5->fetch();
    $apprpic1 = $approw5['pic1Name'];
    $apprpic1e = $approw5['pic1Ext'];
    $apprpic2 = $approw5['pic2Name'];
    $apprpic2e = $approw5['pic2Ext'];
    if (file_exists("userPics/$apprId/$apprpic1.$apprpic1e")) {
        unlink("userPics/$apprId/$apprpic1.$apprpic1e");
    }
    if (file_exists("userPics/$apprId/$apprpic2.$apprpic2e")) {
        unlink("userPics/$apprId/$apprpic2.$apprpic2e");
    }

    $appr6 = $db->prepare("DELETE FROM articles WHERE id=?");
    $appr6->execute(array($reportArt));
} elseif ($approve == '0') {
    $appr7 = $db->prepare("SELECT whyReported FROM reported WHERE id=?");
    $appr7->execute(array($reportId));
    $approw7 = $appr7->fetch();
    $newmsg = $approw7['whyReported'] . "\n\n\n" . $msgToAuthor;
    $appr8 = $db->prepare("UPDATE reported SET whyReported=? WHERE id=?");
    $appr8->execute(array($newmsg, $reportId));
}