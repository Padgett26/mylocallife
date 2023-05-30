<?php

$deladId = filter_input(INPUT_POST, 'delAdvert', FILTER_SANITIZE_NUMBER_INT);
$ad1 = $db->prepare("SELECT adName, adExt FROM advertising WHERE id=? && userId=?");
$ad1->execute(array($deladId, $myId));
$ad1row = $ad1->fetch();
if (file_exists("userPics/$myId/" . $ad1row['adName'] . "." . $ad1row['adExt'])) {
    unlink("userPics/$myId/" . $ad1row['adName'] . "." . $ad1row['adExt']);
}
$ad2 = $db->prepare("DELETE FROM advertising WHERE id=? && userId=?");
$ad2->execute(array($deladId, $myId));
