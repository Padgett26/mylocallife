<?php

$deladId = filter_input(INPUT_POST, 'delAdvert', FILTER_SANITIZE_NUMBER_INT);
$ad1 = $db->prepare("SELECT userId, adName, adExt FROM advertising WHERE id=?");
$ad1->execute(array($deladId));
$ad1row = $ad1->fetch();
if (file_exists("userPics/" . $ad1row['userId'] . "/" . $ad1row['adName'] . "." . $ad1row['adExt'])) {
    unlink("userPics/" . $ad1row['userId'] . "/" . $ad1row['adName'] . "." . $ad1row['adExt']);
}
$ad2 = $db->prepare("DELETE FROM advertising WHERE id=?");
$ad2->execute(array($deladId));
