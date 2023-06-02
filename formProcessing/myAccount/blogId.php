<?php
$blogId = filter_input(INPUT_POST, 'blogId', FILTER_SANITIZE_STRING);
$blogEntryTitle = filter_input(INPUT_POST, 'blogEntryTitle',
        FILTER_SANITIZE_STRING);
$a2 = htmlEntities(trim($_POST['blogEntryText']), ENT_QUOTES);
$blogEntryText = filter_var($a2, FILTER_SANITIZE_STRING);
$picCaption1 = filter_input(INPUT_POST, 'picCaption1', FILTER_SANITIZE_STRING);
$picCaption2 = filter_input(INPUT_POST, 'picCaption2', FILTER_SANITIZE_STRING);
$picCaption3 = filter_input(INPUT_POST, 'picCaption3', FILTER_SANITIZE_STRING);
$picCaption4 = filter_input(INPUT_POST, 'picCaption4', FILTER_SANITIZE_STRING);
$delBlogEntry = (filter_input(INPUT_POST, 'delBlogEntry',
        FILTER_SANITIZE_NUMBER_INT) == "1") ? "1" : "0";
if ($delBlogEntry == '1') {
    $get2 = $db->prepare(
            "SELECT picName1, picExt1, picName2, picExt2, picName3, picExt3, picName4, picExt4 FROM blog WHERE userId=? && id=?");
    $get2->execute(array(
            $myId,
            $blogId
    ));
    $get2row = $get2->fetch();
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
    $delb2 = $db->prepare("DELETE FROM blog WHERE userId=? && id=?");
    $delb2->execute(array(
            $myId,
            $blogId
    ));
} else {
    if ($blogId == 'new') {
        $showM = date("n");
        $showY = date("Y");
        $makeBE = $db->prepare(
                "INSERT INTO blog VALUES(NULL, ?, ?, ?, ?, ?, ?, '0', 'jpg', ?, '0', 'jpg', ?, '0', 'jpg', ?, '0', 'jpg', ?, '0', '0', '0')");
        $makeBE->execute(
                array(
                        $myId,
                        $blogEntryTitle,
                        $blogEntryText,
                        $time,
                        $showM,
                        $showY,
                        $picCaption1,
                        $picCaption2,
                        $picCaption3,
                        $picCaption4
                ));
        $getBEid = $db->prepare(
                "SELECT id FROM blog WHERE userId=? ORDER BY id DESC LIMIT 1");
        $getBEid->execute(array(
                $myId
        ));
        $gBErow = $getBEid->fetch();
        $blogId = $gBErow['id'];
    } else {
        $upEntry = $db->prepare(
                "UPDATE blog SET blogEntryTitle=?, blogEntryText=?, picCaption1=?, picCaption2=?, picCaption3=?, picCaption4=? WHERE id=?");
        $upEntry->execute(
                array(
                        $blogEntryTitle,
                        $blogEntryText,
                        $picCaption1,
                        $picCaption2,
                        $picCaption3,
                        $picCaption4,
                        $blogId
                ));
    }
    $tmpFile = $_FILES["image1"]["tmp_name"];
    list ($width1, $height1) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width1 != null && $height1 != null) {
        $imageType1 = getPicType($_FILES["image1"]['type']);
        $imageName1 = $time . "." . $imageType1;
        processPic("userPics/$myId", $imageName1, $tmpFile, 800, 100);
        $p1stmt = $db->prepare(
                "UPDATE blog SET picName1=?, picExt1=? WHERE id=?");
        $p1stmt->execute(array(
                $time,
                $imageType1,
                $blogId
        ));
    }
    $tmpFile2 = $_FILES["image2"]["tmp_name"];
    list ($width2, $height2) = (getimagesize($tmpFile2) != null) ? getimagesize(
            $tmpFile2) : null;
    if ($width2 != null && $height2 != null) {
        $imageType2 = getPicType($_FILES["image2"]['type']);
        $imageName2 = ($time + 1) . "." . $imageType2;
        processPic("userPics/$myId", $imageName2, $tmpFile2, 800, 100);
        $p2stmt = $db->prepare(
                "UPDATE blog SET picName2=?, picExt2=? WHERE id=?");
        $p2stmt->execute(array(
                ($time + 1),
                $imageType2,
                $blogId
        ));
    }
    $tmpFile3 = $_FILES["image3"]["tmp_name"];
    list ($width3, $height3) = (getimagesize($tmpFile3) != null) ? getimagesize(
            $tmpFile3) : null;
    if ($width3 != null && $height3 != null) {
        $imageType3 = getPicType($_FILES["image3"]['type']);
        $imageName3 = ($time + 2) . "." . $imageType3;
        processPic("userPics/$myId", $imageName3, $tmpFile3, 800, 100);
        $p3stmt = $db->prepare(
                "UPDATE blog SET picName3=?, picExt3=? WHERE id=?");
        $p3stmt->execute(array(
                ($time + 2),
                $imageType3,
                $blogId
        ));
    }
    $tmpFile4 = $_FILES["image4"]["tmp_name"];
    list ($width4, $height4) = (getimagesize($tmpFile4) != null) ? getimagesize(
            $tmpFile4) : null;
    if ($width4 != null && $height4 != null) {
        $imageType4 = getPicType($_FILES["image4"]['type']);
        $imageName4 = ($time + 3) . "." . $imageType4;
        processPic("userPics/$myId", $imageName4, $tmpFile4, 800, 100);
        $p4stmt = $db->prepare(
                "UPDATE blog SET picName4=?, picExt4=? WHERE id=?");
        $p4stmt->execute(array(
                ($time + 3),
                $imageType4,
                $blogId
        ));
    }

    // Videos
    $videoTitleNew = filter_input(INPUT_POST, 'videoTitleNew',
            FILTER_SANITIZE_STRING);
    $yt = (filter_input(INPUT_POST, 'videoAddressNew', FILTER_SANITIZE_URL)) ? trim(
            filter_input(INPUT_POST, 'videoAddressNew', FILTER_SANITIZE_URL)) : '///0';
    $videoAddressNew = explode("/", $yt);
    $videoOrderNew = filter_input(INPUT_POST, 'videoOrderNew',
            FILTER_SANITIZE_NUMBER_INT);
    if ($yt != '///0') {
        $van3 = $videoAddressNew[3];
        $bv = $db->prepare(
                "INSERT INTO blogVideos VALUES(NULL,?,?,?,?,'0','0','0')");
        $bv->execute(array(
                $blogId,
                $videoTitleNew,
                $van3,
                $videoOrderNew
        ));
    }
    foreach ($_POST as $k => $v) {
        if (preg_match("/^videoTitle([1-9][0-9]*)$/", $k, $match)) {
            $x = $db->prepare("UPDATE blogVideos SET videoTitle=? WHERE id=?");
            $x->execute(array(
                    $v,
                    $match[1]
            ));
        }
        if (preg_match("/^videoAddress([1-9][0-9]*)$/", $k, $match)) {
            $vidAddy = explode("/", $v);
            $y = $db->prepare("UPDATE blogVideos SET videoAddress=? WHERE id=?");
            $y->execute(array(
                    $vidAddy[3],
                    $match[1]
            ));
        }
        if (preg_match("/^videoOrder([1-9][0-9]*)$/", $k, $match)) {
            $z = $db->prepare("UPDATE blogVideos SET videoOrder=? WHERE id=?");
            $z->execute(array(
                    $v,
                    $match[1]
            ));
        }
    }
    foreach ($_POST as $k => $v) {
        if ($v == '1' && preg_match("/^videoDel([1-9][0-9]*)$/", $k, $match)) {
            $x = $db->prepare("DELETE FROM blogVideos WHERE id=?");
            $x->execute(array(
                    $match[1]
            ));
        }
    }
}