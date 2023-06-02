<?php
if (filter_input(INPUT_POST, 'eUpdate', FILTER_SANITIZE_NUMBER_INT)) {
    $eventId = filter_input(INPUT_POST, 'eUpdate', FILTER_SANITIZE_NUMBER_INT);
    $upTitle = filter_input(INPUT_POST, 'title', FILTER_SANITIZE_STRING);
    $upStartHour = filter_input(INPUT_POST, 'startHour',
            FILTER_SANITIZE_NUMBER_INT);
    $upStartMinute = filter_input(INPUT_POST, 'startMinute',
            FILTER_SANITIZE_NUMBER_INT);
    $upStartMonth = filter_input(INPUT_POST, 'startMonth',
            FILTER_SANITIZE_NUMBER_INT);
    $upStartDay = filter_input(INPUT_POST, 'startDay',
            FILTER_SANITIZE_NUMBER_INT);
    $upStartYear = filter_input(INPUT_POST, 'startYear',
            FILTER_SANITIZE_NUMBER_INT);
    $upStartTime = mktime($upStartHour, $upStartMinute, 00, $upStartMonth,
            $upStartDay, $upStartYear);
    $a2 = htmlEntities(trim($_POST['writeUp']), ENT_QUOTES);
    $upWriteUp = filter_var($a2, FILTER_SANITIZE_STRING);
    $delEvent = (filter_input(INPUT_POST, 'delEvent', FILTER_SANITIZE_NUMBER_INT) ==
            '1') ? '1' : '0';

    if ($delEvent == '1') {
        $getId = $db->prepare("DELETE FROM calendar WHERE id=?");
        $getId->execute(array(
                $eventId
        ));
    } else {

        $getId = $db->prepare(
                "UPDATE calendar SET startTime=?, title=?, writeUp=? WHERE id=?");
        $getId->execute(array(
                $upStartTime,
                $upTitle,
                $upWriteUp,
                $eventId
        ));

        $tmpFile = $_FILES["image"]["tmp_name"];
        list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
                $tmpFile) : null;
        if ($width != null && $height != null) {
            $imageType = getPicType($_FILES["image"]['type']);
            $imageName = $time . "." . $imageType;
            processPic("userPics/$myId", $imageName, $tmpFile, 800, 150);
            $p1stmt = $db->prepare("UPDATE calendar SET picture=? WHERE id=?");
            $p1stmt->execute(array(
                    $imageName,
                    $eventId
            ));
        }
    }
}