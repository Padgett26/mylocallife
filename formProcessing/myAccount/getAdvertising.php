<?php
$adId = filter_input(INPUT_POST, 'getAdvertising', FILTER_SANITIZE_STRING);
$slot = filter_input(INPUT_POST, 'slot', FILTER_SANITIZE_STRING);
$linkText = filter_input(INPUT_POST, 'linkText', FILTER_SANITIZE_STRING);
$linkLocal = (filter_input(INPUT_POST, 'linkLocal', FILTER_SANITIZE_NUMBER_INT) ==
        '0') ? '0' : '1';
$salesRepId = filter_input(INPUT_POST, 'salesRepId', FILTER_SANITIZE_NUMBER_INT);
$delAd = (filter_input(INPUT_POST, 'delAd', FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';

if ($delAd == '1') {
    echo "<div style='border:2px solid red; text-align:center; font-weight:bold;'>Are you sure you want to delete this advertisement? Clicking YES will delete the ad, and any credit associated with it.<br />";
    echo "<form action='index.php?page=myAccount' method='post'><input type='hidden' name='delAdvert' value='$adId' /><input type='submit' value=' YES ' /></form>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<form action='index.php?page=myAccount' method='post'><input type='submit' value=' NO ' /></form>";
} else {
    if ($adId == 'new') {
        $ad1 = $db->prepare(
                "INSERT INTO advertising VALUES(NULL, ?, ?, ?, '0', ?, ?, ?, ?, '0', '0', '0')");
        $ad1->execute(
                array(
                        $myId,
                        $slot,
                        $time,
                        'jpg',
                        $linkText,
                        $linkLocal,
                        $salesRepId
                ));
        $ad2 = $db->prepare(
                "SELECT id FROM advertising WHERE userId=? && activeUntil=? ORDER BY id DESC LIMIT 1");
        $ad2->execute(array(
                $myId,
                $time
        ));
        $ad2row = $ad2->fetch();
        $adId = $ad2row['id'];
    } else {
        $ad3 = $db->prepare(
                "UPDATE advertising SET linkLocal=?, linkText=?, salesRepId=? WHERE id=?");
        $ad3->execute(array(
                $linkLocal,
                $linkText,
                $salesRepId,
                $adId
        ));
    }
    $adImage = $_FILES["adImage"]["tmp_name"];
    list ($width, $height) = (getimagesize($adImage) != null) ? getimagesize(
            $adImage) : null;
    if ($width != null && $height != null) {
        $imageType = getPicType($_FILES["adImage"]['type']);
        if ($slot == 'top') {
            $x = 1100;
            $y = 100;
        } else {
            $x = 200;
            $y = 300;
        }
        processPic("userPics/$myId", $time . '.' . $imageType, $adImage,
                1100, 300);
        $astmt = $db->prepare(
                "UPDATE advertising SET adName=?, adExt=? WHERE id=?");
        $astmt->execute(array(
                $time,
                $imageType,
                $adId
        ));
    }
}