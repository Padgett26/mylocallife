<?php
$clsId = filter_input(INPUT_POST, 'editedClassified', FILTER_SANITIZE_STRING);
$classifiedTitle = trim(
        filter_input(INPUT_POST, 'classifiedTitle', FILTER_SANITIZE_STRING));
$a2 = htmlEntities(trim($_POST['classifiedText']), ENT_QUOTES);
$classifiedText = filter_var($a2, FILTER_SANITIZE_STRING);
$activateClassified = filter_input(INPUT_POST, 'activateClassified',
        FILTER_SANITIZE_NUMBER_INT);
$goodUntil = ($activateClassified == "1") ? ($time + 1209600) : '0'; // advance
                                                                     // goodUntil
                                                                     // two
                                                                     // weeks
$catId = filter_input(INPUT_POST, 'catId', FILTER_SANITIZE_NUMBER_INT);
$delClassified = (filter_input(INPUT_POST, 'delClassified',
        FILTER_SANITIZE_NUMBER_INT) == '1') ? "1" : "0";

if ($delClassified == "1") {
    $gs = $db->prepare("SELECT picName, picExt FROM classifieds WHERE id=?");
    $gs->execute(array(
            $clsId
    ));
    $gr = $gs->fetch();
    $pn = $gr['picName'];
    $pe = $gr['picExt'];
    if (file_exists("userPics/$myId/$pn.$pe")) {
        unlink("userPics/$myId/$pn.$pe");
    }
    if (file_exists("userPics/$myId/thumb/$pn.$pe")) {
        unlink("userPics/$myId/thumb/$pn.$pe");
    }
    $substmt = $db->prepare("DELETE FROM classifieds WHERE id=?");
    $substmt->execute(array(
            $clsId
    ));
} else {
    $getchr = $db->prepare(
            "SELECT classifiedTextLength FROM classifieds WHERE id=?");
    $getchr->execute(array(
            $clsId
    ));
    $gcrow = $getchr->fetch();
    $chrlen = $gcrow['classifiedTextLength'];
    $t = substr($classifiedText, 0, ($chrlen + 20));
    $clsstmt = $db->prepare(
            "UPDATE classifieds SET classifiedTitle=?, classifiedText=?, displayUntil=?, catId=? WHERE id=?");
    $clsstmt->execute(
            array(
                    $classifiedTitle,
                    $t,
                    $goodUntil,
                    $catId,
                    $clsId
            ));
    if ($chrlen >= 251) {
        $tmpFile = $_FILES["image"]["tmp_name"];
        list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                $tmpFile) : null;
        if ($width != null && $height != null) {
            $imageType = getPicType($_FILES["image"]['type']);
            $imageName = $time . "." . $imageType;
            processPic("userPics/$myId", $imageName, $tmpFile, 250, 100);
            $p1stmt = $db->prepare(
                    "UPDATE classifieds SET picName=?, picExt=? WHERE id=?");
            $p1stmt->execute(array(
                    $time,
                    $imageType,
                    $clsId
            ));
        }
    }
}