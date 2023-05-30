<?php

$get1 = $db->prepare("SELECT blogPic, blogPicExt FROM blogDescriptions WHERE userId=?");
$get1->execute(array($myId));
$get1row = $get1->fetch();
$blogPic = $get1row['blogPic'];
$blogPicExt = $get1row['blogPicExt'];
if (file_exists("userPics/$myId/$blogPic.$blogPicExt")) {
    unlink("userPics/$myId/$blogPic.$blogPicExt");
}
$get2 = $db->prepare("SELECT picName1, picExt1, picName2, picExt2, picName3, picExt3, picName4, picExt4 FROM blog WHERE userId=?");
$get2->execute(array($myId));
while ($get2row = $get2->fetch()) {
    $picName1 = $get2row['picName1'];
    $picExt1 = $get2row['picExt1'];
    $picName2 = $get2row['picName2'];
    $picExt2 = $get2row['picExt2'];
    $picName3 = $get2row['picName3'];
    $picExt3 = $get2row['picExt3'];
    $picName4 = $get2row['picName4'];
    $picExt4 = $get2row['picEx4'];
    if (file_exists("userPics/$myId/$picName1.$picExt1")) {
        unlink("userPics/$myId/$picName1.$picExt1");
    }
    if (file_exists("userPics/$myId/$picName2.$picExt2")) {
        unlink("userPics/$myId/$picName2.$picExt2");
    }
    if (file_exists("userPics/$myId/$picName3.$picExt3")) {
        unlink("userPics/$myId/$picName3.$picExt3");
    }
    if (file_exists("userPics/$myId/$picName4.$picExt4")) {
        unlink("userPics/$myId/$picName4.$picExt4");
    }
    if (file_exists("userPics/$myId/thumb/$picName1.$picExt1")) {
        unlink("userPics/$myId/thumb/$picName1.$picExt1");
    }
    if (file_exists("userPics/$myId/thumb/$picName2.$picExt2")) {
        unlink("userPics/$myId/thumb/$picName2.$picExt2");
    }
    if (file_exists("userPics/$myId/thumb/$picName3.$picExt3")) {
        unlink("userPics/$myId/thumb/$picName3.$picExt3");
    }
    if (file_exists("userPics/$myId/thumb/$picName4.$picExt4")) {
        unlink("userPics/$myId/thumb/$picName4.$picExt4");
    }
}
$delb1 = $db->prepare("DELETE FROM blogDescriptions WHERE userId=?");
$delb1->execute(array($myId));
$delb2 = $db->prepare("DELETE FROM blogFavorites WHERE blogUserId=?");
$delb2->execute(array($myId));
$delb3 = $db->prepare("DELETE FROM blogLog WHERE blogUserId=?");
$delb3->execute(array($myId));
$delb4 = $db->prepare("DELETE FROM blog WHERE userId=?");
$delb4->execute(array($myId));
