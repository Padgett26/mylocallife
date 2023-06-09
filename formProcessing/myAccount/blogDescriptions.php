<?php
$blogTitle = filter_input(INPUT_POST, 'blogTitle', FILTER_SANITIZE_STRING);
$a2 = htmlEntities(trim($_POST['blogDesc']), ENT_QUOTES);
$blogDesc = filter_var($a2, FILTER_SANITIZE_STRING);
$delBlog = (filter_input(INPUT_POST, 'delBlog', FILTER_SANITIZE_NUMBER_INT) ==
        "1") ? "1" : "0";
if ($delBlog == '1') {
    echo "<form action='index.php?page=myAccount' method='post'>This will erase your blog and pictures. Are you sure? <input type='hidden' name='blogDel' value='1' /><input type='submit' value=' Yes ' /></form>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<form action='index.php?page=myAccount' method='post'><input type='submit' value=' No ' /></form>";
} else {
    $check = $db->prepare(
            "SELECT COUNT(*) FROM blogDescriptions WHERE userId=?");
    $check->execute(array(
            $myId
    ));
    $checkrow = $check->fetch();
    if ($checkrow[0] == '1') {
        $upblog = $db->prepare(
                "UPDATE blogDescriptions SET blogTitle=?, blogDesc=? WHERE userId=?");
        $upblog->execute(array(
                $blogTitle,
                $blogDesc,
                $myId
        ));
    } else {
        $upblog = $db->prepare(
                "INSERT INTO blogDescriptions VALUES(NULL, ?, ?, ?, '0', 'jpg', '0', '0', '0')");
        $upblog->execute(array(
                $myId,
                $blogTitle,
                $blogDesc
        ));
    }
    $tmpFile = $_FILES["image"]["tmp_name"];
    list ($width, $height) = (getimagesize($tmpFile) != null) ? getimagesize(
            $tmpFile) : null;
    if ($width != null && $height != null) {
        $imageType = getPicType($_FILES["image"]['type']);
        $imageName = $time . "." . $imageType;
        processPic("userPics/$myId", $imageName, $tmpFile, 800, 100);
        $p1stmt = $db->prepare(
                "UPDATE blogDescriptions SET blogPic=?, blogPicExt=? WHERE userId=?");
        $p1stmt->execute(array(
                $time,
                $imageType,
                $myId
        ));
    }
}