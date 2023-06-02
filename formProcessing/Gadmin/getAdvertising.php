<?php
$adId = filter_input(INPUT_POST, 'getAdvertising', FILTER_SANITIZE_STRING);
$adUserId = filter_input(INPUT_POST, 'adUserId', FILTER_SANITIZE_NUMBER_INT);
$slot = filter_input(INPUT_POST, 'slot', FILTER_SANITIZE_STRING);
$linkText = filter_input(INPUT_POST, 'linkText', FILTER_SANITIZE_URL);
$linkLocal = (filter_input(INPUT_POST, 'linkLocal', FILTER_SANITIZE_NUMBER_INT) ==
        '0') ? '0' : '1';
$salesRepId = filter_input(INPUT_POST, 'salesRepId', FILTER_SANITIZE_NUMBER_INT);
$delAd = (filter_input(INPUT_POST, 'delAd', FILTER_SANITIZE_NUMBER_INT) == '1') ? '1' : '0';

if ($delAd == '1') {
    echo "<div style='border:2px solid red; text-align:center; font-weight:bold;'>Are you sure you want to delete this advertisement? Clicking YES will delete the ad, and any credit associated with it.<br />";
    echo "<form action='index.php?page=Gadmin' method='post'><input type='hidden' name='delAdvert' value='$adId' /><input type='submit' value=' YES ' /></form>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<form action='index.php?page=Gadmin' method='post'><input type='submit' value=' NO ' /></form>";
} else {
    if ($adId == 'new') {
        $ad1 = $db->prepare(
                "INSERT INTO advertising VALUES(NULL, ?, ?, ?, '0', 'jpg', ?, ?, ?, '0','0','0')");
        $ad1->execute(
                array(
                        $adUserId,
                        $slot,
                        $time,
                        $linkText,
                        $linkLocal,
                        $salesRepId
                ));
        $ad2 = $db->prepare(
                "SELECT id FROM advertising WHERE userId=? && activeUntil=? ORDER BY id DESC LIMIT 1");
        $ad2->execute(array(
                $adUserId,
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
        $imageName = $time . "." . $imageType;
        processPic("userPics/$adUserId", $imageName, $tmpFile, 1100, 300);
        $astmt = $db->prepare(
                "UPDATE advertising SET adName=?, adExt=? WHERE id=?");
        $astmt->execute(array(
                $time,
                $imageType,
                $adId
        ));
    }
}