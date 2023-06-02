<?php
$myBusinessUp = filter_input(INPUT_POST, 'myBusinessUp',
        FILTER_SANITIZE_NUMBER_INT);
$busiName = trim(
        filter_input(INPUT_POST, 'busiName', FILTER_SANITIZE_STRING));
$busiPhone = trim(
        filter_input(INPUT_POST, 'busiPhone', FILTER_SANITIZE_STRING));
$hoursOfOperation = trim(
        filter_input(INPUT_POST, 'hoursOfOperation', FILTER_SANITIZE_STRING));
$busiAddress1 = trim(
        filter_input(INPUT_POST, 'busiAddress1', FILTER_SANITIZE_STRING));
$busiAddress2 = trim(
        filter_input(INPUT_POST, 'busiAddress2', FILTER_SANITIZE_STRING));
$a2 = htmlEntities(trim($_POST['busiDescText']), ENT_QUOTES);
$busiDescText = filter_var($a2, FILTER_SANITIZE_STRING);
$busiEmail = trim(
        filter_input(INPUT_POST, 'busiEmail', FILTER_SANITIZE_EMAIL));
$busiCat = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

$stmt = $db->prepare(
        "UPDATE busiListing SET busiName=?, busiPhone=?, hoursOfOperation=?, busiAddress1=?, busiAddress2=?, busiDescText=?, busiEmail=?, category=? WHERE userId=?");
$stmt->execute(
        array(
                $busiName,
                $busiPhone,
                $hoursOfOperation,
                $busiAddress1,
                $busiAddress2,
                $busiDescText,
                $busiEmail,
                $busiCat,
                $myId
        ));

$tmpFile = $_FILES["image1"]["tmp_name"];
list ($width1, $height1) = (getimagesize($tmpFile) != null) ? getimagesize(
        $tmpFile) : null;
if ($width1 != null && $height1 != null) {
    $image1Type = getPicType($_FILES["image1"]['type']);
    $image1Name = ($time + 1) . "." . $image1Type;
    processPic("userPics/$myId", $image1Name, $tmpFile, 800, 150);
    $p1stmt = $db->prepare(
            "UPDATE busiListing SET busiPic1=?, busiPicExt1=? WHERE userId=?");
    $p1stmt->execute(array(
            ($time + 1),
            $image1Type,
            $myId
    ));
}
$tmpFile = $_FILES["image2"]["tmp_name"];
list ($width2, $height2) = (getimagesize($tmpFile) != null) ? getimagesize(
        $tmpFile) : null;
if ($width2 != null && $height2 != null) {
    $image2Type = getPicType($_FILES["image2"]['type']);
    $image2Name = ($time + 2) . "." . $image2Type;
    processPic("userPics/$myId", $image2Name, $tmpFile, 800, 150);
    $p2stmt = $db->prepare(
            "UPDATE busiListing SET busiPic2=?, busiPicExt2=? WHERE userId=?");
    $p2stmt->execute(array(
            ($time + 2),
            $image2Type,
            $myId
    ));
}