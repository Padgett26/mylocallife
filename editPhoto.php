<?php
$photoId = filter_input(INPUT_GET, 'photoId', FILTER_SANITIZE_STRING);
$newId = "edit";
if ($photoId == 'new') {
    $newId = "new";
    $p1 = $db->prepare("INSERT INTO photoJournalism VALUES(NULL,' ',' ',?,'local',?,'0','0','0','0','0','0')");
    $p1->execute(array($time, $myId));
    $p2 = $db->prepare("SELECT id FROM photoJournalism WHERE postedDate=? && authorId=? ORDER BY id DESC LIMIT 1");
    $p2->execute(array($time, $myId));
    $pr2 = $p2->fetch();
    $photoId = $pr2['id'];
}
$pst1 = $db->prepare("SELECT * FROM photoJournalism WHERE authorId=? && id=?");
$pst1->execute(array($myId, $photoId));
$prow1 = $pst1->fetch();
$photoTitle = $prow1['photoTitle'];
$photoText = html_entity_decode($prow1['photoText'], ENT_QUOTES);
$postedDate = $prow1['postedDate'];
$editedDate = $prow1['editedDate'];

echo "<div><form action='index.php?page=myAccount' method='post' enctype='multipart/form-data'>\n";
echo "Posted date: " . date("M j, Y", $postedDate) . "<br /><br />";
if ($editedDate != '0') {
    echo "Last edit date: " . date("M j, Y", $editedDate) . "<br /><br />\n";
}
echo "<header>Title: <input type='text' name='photoTitle' value='$photoTitle' size='70' /></header><br /><br />\n";
echo "<article>Head text:<br /><textarea name='photoText' cols='70' rows='8'>$photoText</textarea></article><br /><br /><br />\n";
echo "You can include photos with captions.<br /><br />If the upload fails, or takes too long, the total upload size may be to big, try reducing the size of your pictures (the site saves your pictures at a max of 800px X 800px, so reduce them to that size) or try uploading your pictures a few at a time.<br /><br />\n";
$pst3 = $db->prepare("SELECT COUNT(*) FROM photoList WHERE photoId = ?");
$pst3->execute(array($photoId));
$prow3 = $pst3->fetch();
$picCount = $prow3[0];
$pst2 = $db->prepare("SELECT * FROM photoList WHERE photoId = ? ORDER BY photoOrder");
$pst2->execute(array($photoId));
while ($prow2 = $pst2->fetch()) {
    $phId = $prow2['id'];
    $photoName = $prow2['photoName'];
    $photoExt = $prow2['photoExt'];
    $photoCaption = $prow2['photoCaption'];
    $photoOrder = $prow2['photoOrder'];

    echo "<div style='width:100%; text-align:justify; margin-bottom:30px;'>";
    echo "Show Order: <select name='photoOrder$photoOrder' size='1'>";
    for ($j = 1; $j <= $picCount; $j++) {
        echo "<option value='$j'";
        if ($j == $photoOrder) {
            echo " selected='selected'";
        }
        echo ">$j</option>\n";
    }
    echo "</select><br />";
    if (file_exists("userPics/$myId/$photoName.$photoExt")) {
        echo "<img src='userPics/$myId/$photoName.$photoExt' alt='' style='margin:10px auto; border:1px solid $highlightColor; padding:5px; width:90%;' /><br />\n";
    }
    echo "Upload new photo: <input type='file' name='image$photoOrder' /><br /><br />\n";
    echo "Photo Caption:<br /><textarea name='photoCaption$photoOrder' cols='70' rows='4'>$photoCaption</textarea></div>\n";
}
for ($i = $picCount + 1; $i < $picCount + 6; $i++) {
    echo "<div style='width:100%; text-align:justify; margin-bottom:30px;'>";
    echo "Upload new photo: <input type='file' name='image$i' /><br /><br />\n";
    echo "Photo Caption:<br /><textarea name='photoCaption$i' cols='70' rows='4'></textarea></div>\n";
}

if ($newId != "new") {
    ?>
    Do you want to delete this Photo Show?  This will erase the article and delete any pictures associated with it.  This is permanent and cannot be undone.<br />
    YES, <input type="checkbox" name="delPhoto" value="1" /> delete this Photo Show.<br /><br />
<?php } else { ?>
    <header style="font-weight:bold; text-align:center;">Terms and Conditions</header>
    <article style="text-align:justify;">
        Initially, your article will be reviewed by an admin of this website.  The admin will decide to either approve the article for publication on mylocal.life, or decide to delete the article due to being inappropriate for mylocal.life.<br /><br />
        My Local Life claims no rights to any data you enter into the site, other than the right to display such data on the mylocal.life website, and delete the data if it is found to be inappropriate for the website during the initial review of the article, or if it is reported and found to be inappropriate for the website at a later time. Users of the site have the right to view such data, and with each article users have the right and ability to share your article on social media.<br /><br />
        Registered users have the ability to report your data for whatever reason.  If an article is reported, it will be hidden from public display on mylocal.life.  A notification of the reported article will go to one of the admins of this site, and the article will be reviewed.  The admin has the right to, and will decide if the article will return to a visible state on mylocal.life, or if it will be deleted from the site.  If your article is deleted, the assigned admin will attempt to notify you of the deletion, but mylocal.life is not accountable if the notification does not reach you for any reason.<br /><br />
        By putting any data on this website, you are stating that you have the right to publish that data to this site.  You either own rights to any collection of words, or digital media, because you are the creator, or you have the permission from the creator of those collections of words or digital media.  If you have a quote from another author, it must be cited.  If a collection of words is found to originate from another author and is not cited, it will be considered plagiarism, and you as the submitter of the article will be responsible to fix the problem, or the article will be deleted.<br /><br />
        Check this box if you agree to these conditions? <input type="checkbox" onchange="agreeToTerms()" />
    </article><br /><br />
    <?php
}
$d = ($newId != "new") ? "" : " disabled";
echo "<input type='hidden' name='photoUp' value='$photoId' /><input type='submit' id='articleSubmit'$d value=' Save changes ' /></form></div>";
