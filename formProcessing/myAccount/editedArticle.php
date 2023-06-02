<?php
$artId = filter_input(INPUT_POST, 'editedArticle', FILTER_SANITIZE_STRING);
$articleTitle = trim(
        filter_input(INPUT_POST, 'articleTitle', FILTER_SANITIZE_STRING));
$slug = slugify($articleTitle);
$a2 = htmlEntities(trim($_POST['articleText']), ENT_QUOTES);
$articleText = filter_var($a2, FILTER_SANITIZE_STRING);
$a3 = htmlEntities(trim($_POST['embedCode1']), ENT_QUOTES);
$embedCode1 = filter_var($a3, FILTER_SANITIZE_STRING);
$pic1Caption = trim(
        filter_input(INPUT_POST, 'pic1Caption', FILTER_SANITIZE_STRING));
$pic2Caption = trim(
        filter_input(INPUT_POST, 'pic2Caption', FILTER_SANITIZE_STRING));
$inReplyTo = filter_input(INPUT_POST, 'inReplyTo', FILTER_SANITIZE_NUMBER_INT);
$pd = filter_input(INPUT_POST, 'postedDate', FILTER_SANITIZE_NUMBER_INT);
$pd2 = explode("-", $pd);
$postedDate = mktime(0, 0, 0, $pd2[1], $pd2[2], $pd2[0]);
$catId = filter_input(INPUT_POST, 'catId', FILTER_SANITIZE_NUMBER_INT);
$yt = filter_input(INPUT_POST, 'youtube', FILTER_SANITIZE_URL) ? trim(
        filter_input(INPUT_POST, 'youtube', FILTER_SANITIZE_URL)) : '///0';
$youtube = explode("/", $yt);
$pdfText1 = trim(filter_input(INPUT_POST, 'pdfText1', FILTER_SANITIZE_STRING));
$pdfText2 = trim(filter_input(INPUT_POST, 'pdfText2', FILTER_SANITIZE_STRING));
$delPdf1 = (filter_input(INPUT_POST, 'delPdf1', FILTER_SANITIZE_NUMBER_INT) ==
        '1') ? "1" : "0";
$delPdf2 = (filter_input(INPUT_POST, 'delPdf2', FILTER_SANITIZE_NUMBER_INT) ==
        '1') ? "1" : "0";
$delArticle = (filter_input(INPUT_POST, 'delArticle', FILTER_SANITIZE_NUMBER_INT) ==
        '1') ? "1" : "0";

if ($delArticle == "1") {
    $stmt = $db->prepare(
            "SELECT pic1Name, pic1Ext, pic2Name, pic2Ext, pdf1, pdf2 FROM articles WHERE id=?");
    $stmt->execute(array(
            $artId
    ));
    $row = $stmt->fetch();
    $p1n = $row['pic1Name'];
    $p1e = $row['pic1Ext'];
    $p2n = $row['pic2Name'];
    $p2e = $row['pic2Ext'];
    $pd1 = $row['pdf1'];
    $pd2 = $row['pdf2'];
    if (file_exists("userPics/$myId/$p1n.$p1e")) {
        unlink("userPics/$myId/$p1n.$p1e");
    }
    if (file_exists("userPics/$myId/$p2n.$p2e")) {
        unlink("userPics/$myId/$p2n.$p2e");
    }
    if (file_exists("userPics/$myId/thumb/$p1n.$p1e")) {
        unlink("userPics/$myId/thumb/$p1n.$p1e");
    }
    if (file_exists("userPics/$myId/thumb/$p2n.$p2e")) {
        unlink("userPics/$myId/thumb/$p2n.$p2e");
    }
    if (file_exists("userPics/$myId/$pd1.pdf")) {
        unlink("userPics/$myId/$pd1.pdf");
    }
    if (file_exists("userPics/$myId/$pd2.pdf")) {
        unlink("userPics/$myId/$pd2.pdf");
    }
    $substmt = $db->prepare("DELETE FROM articles WHERE id=?");
    $substmt->execute(array(
            $artId
    ));
    $substmt = $db->prepare("DELETE FROM reported WHERE articleId=?");
    $substmt->execute(array(
            $artId
    ));
} else {
    if ($artId == "new") {
        $n = 1;
        $newartstmt = $db->prepare(
                "INSERT INTO articles VALUES" .
                "(NULL, 'title', 'text', '0', 'jpg', NULL, '0', 'jpg', NULL, ?, 'local', ?, '0', NULL, '0', '0', '0', NULL, '0', NULL, '0', '', '0', '0', '0', '0')");
        $newartstmt->execute(array(
                $postedDate,
                $myId
        ));
        $getidstmt = $db->prepare(
                "SELECT id FROM articles WHERE postedDate=? && authorId=? ORDER BY id DESC LIMIT 1");
        $getidstmt->execute(array(
                $postedDate,
                $myId
        ));
        $getidrow = $getidstmt->fetch();
        $artId = $getidrow['id'];
        $newartstmt2 = $db->prepare(
                "INSERT INTO reported VALUES" .
                "(NULL, '0', ?, '0', ?, '0', '', '0', '0')");
        $newartstmt2->execute(array(
                $artId,
                $postedDate
        ));
    }
    if ($_FILES['image1']['size'] >= 1000) {
        $tmpFile1 = $_FILES["image1"]["tmp_name"];
        list ($width1, $height1) = (getimagesize($tmpFile1) != null) ? getimagesize(
                $tmpFile1) : null;
        if ($width1 != null && $height1 != null) {
            $image1Type = getPicType($_FILES["image1"]['type']);
            $image1Name = $time . "." . $image1Type;
            processPic("userPics/$myId", $image1Name, $tmpFile1, 800,
                    100);
            $p1stmt = $db->prepare(
                    "UPDATE articles SET pic1Name=?, pic1Ext=? WHERE id=?");
            $p1stmt->execute(array(
                    $time,
                    $image1Type,
                    $artId
            ));
        }
    }
    if ($_FILES['image2']['size'] >= 1000) {
        $tmpFile2 = $_FILES["image2"]["tmp_name"];
        list ($width2, $height2) = (getimagesize($tmpFile2) != null) ? getimagesize(
                $tmpFile2) : null;
        if ($width2 != null && $height2 != null) {
            $image2Type = getPicType($_FILES["image2"]['type']);
            $image2Name = ($time + 1) . "." . $image2Type;
            processPic("userPics/$myId", $image2Name, $tmpFile2, 800,
                    100);
            $p1stmt = $db->prepare(
                    "UPDATE articles SET pic2Name=?, pic2Ext=? WHERE id=?");
            $p1stmt->execute(array(
                    ($time + 1),
                    $image2Type,
                    $artId
            ));
        }
    }

    if (isset($_FILES['newPdf1']['name']) && $delPdf1 != "1") {
        processPdf($myId, $time, '1', $_FILES['newPdf1']['tmp_name'], $artId,
                $db);
    }
    if ($delPdf1 == '1') {
        deletePdf($myId, '1', $artId, $db);
    }
    if (isset($_FILES['newPdf2']['name']) && $delPdf2 != "1") {
        processPdf($myId, $time, '2', $_FILES['newPdf2']['tmp_name'], $artId,
                $db);
    }
    if ($delPdf2 == '1') {
        deletePdf($myId, '2', $artId, $db);
    }

    $isedited = $db->prepare(
            "SELECT COUNT(*) FROM reported WHERE reportedBy = '0' && reportedTime >= '1' && clearedTime = '0'");
    $isedited->execute();
    $isrow = $isedited->fetch();
    $editedDate = ($n != 1) ? $time : "0";
    $artstmt = $db->prepare(
            "UPDATE articles SET articleTitle=?, articleText=?, pic1Caption=?, pic2Caption=?, editedDate=?, catId=?, inReplyTo=?, youtube=?, pdfText1=?, pdfText2=?, embedCode1=?, slug=? WHERE id=?");
    $artstmt->execute(
            array(
                    $articleTitle,
                    $articleText,
                    $pic1Caption,
                    $pic2Caption,
                    $editedDate,
                    $catId,
                    $inReplyTo,
                    $youtube[3],
                    $pdfText1,
                    $pdfText2,
                    $embedCode1,
                    $slug,
                    $artId
            ));
}