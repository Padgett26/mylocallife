<?php

$blogUserId = filter_input(INPUT_GET, 'blogUserId', FILTER_SANITIZE_NUMBER_INT);

if ($blogUserId == $myId) {
    $showM = (filter_input(INPUT_GET, 'showM', FILTER_SANITIZE_STRING)) ? filter_input(INPUT_GET, 'showM', FILTER_SANITIZE_STRING) : date("n");
    $showY = (filter_input(INPUT_GET, 'showY', FILTER_SANITIZE_STRING)) ? filter_input(INPUT_GET, 'showY', FILTER_SANITIZE_STRING) : date("Y");
    echo "<div id='mainTableBox' style='padding:10px;'>";
    if ($showM == 'new' && $showY == 'new') {
        echo "<form action='index.php?page=myAccount' method='post' enctype='multipart/form-data'>";
        echo "Title:<br />";
        echo "<input type='text' name='blogEntryTitle' value='' size='70' max-length='230' /><br /><br />";
        echo "Pic 1:<br />";
        echo "Upload a new pic 1: <input type='file' name='image1' /><br />";
        echo "Pic 1 caption: <input type='text' name='picCaption1' value='' size='70' max-length='230' /><br /><br />";
        echo "Pic 2:<br />";
        echo "Upload a new pic 2: <input type='file' name='image2' /><br />";
        echo "Pic 2 caption: <input type='text' name='picCaption2' value='' size='70' max-length='230' /><br /><br />";
        echo "Pic 3:<br />";
        echo "Upload a new pic 3: <input type='file' name='image3' /><br />";
        echo "Pic 3 caption: <input type='text' name='picCaption3' value='' size='70' max-length='230' /><br /><br />";
        echo "Pic 4:<br />";
        echo "Upload a new pic 4: <input type='file' name='image4' /><br />";
        echo "Pic 4 caption: <input type='text' name='picCaption4' value='' size='70' max-length='230' /><br /><br />";
        echo "<span style='font-weight:bold;'>Add a youtube video:</span> (on the youtube page, under the video you want to share, click the 'share' button.  There is an address that shows up under the social media icons, copy it and paste it here under video address)<br />";
        echo "<table>";
        echo "<tr><td style='border:1px solid black;'>video title</td><td style='border:1px solid black;'>video address</td><td style='border:1px solid black;'>video order</td></tr>";
        echo "<tr><td style='border:1px solid black;'><input type='text' name='videoTitleNew' value='' /></td><td style='border:1px solid black;'><input type='text' name='videoAddressNew' value='' /></td><td style='border:1px solid black;'>1<input type='hidden' name='videoOrderNew' value='1' /></td></tr>";
        echo "</table><br /><br />";
        echo "<textarea name='blogEntryText' cols='50' rows='10'></textarea><br /><br />";
        echo "<input type='hidden' name='blogId' value='new' /><input type='submit' value=' Save ' /></form>";
    } else {
        $blog = $db->prepare("SELECT * FROM blog WHERE userId=? && postedMonth=? && postedYear=? ORDER BY postedDate");
        $blog->execute(array($blogUserId, $showM, $showY));
        while ($blogrow = $blog->fetch()) {
            $blogId = $blogrow['id'];
            $blogEntryTitle = $blogrow['blogEntryTitle'];
            $blogEntryText = html_entity_decode($blogrow['blogEntryText'], ENT_QUOTES);
            $postedDate = $blogrow['postedDate'];
            $picName1 = $blogrow['picName1'];
            $picExt1 = $blogrow['picExt1'];
            $picCaption1 = $blogrow['picCaption1'];
            $picName2 = $blogrow['picName2'];
            $picExt2 = $blogrow['picExt2'];
            $picCaption2 = $blogrow['picCaption2'];
            $picName3 = $blogrow['picName3'];
            $picExt3 = $blogrow['picExt3'];
            $picCaption3 = $blogrow['picCaption3'];
            $picName4 = $blogrow['picName4'];
            $picExt4 = $blogrow['picExt4'];
            $picCaption4 = $blogrow['picCaption4'];
            echo "<form action='index.php?page=myAccount' method='post' enctype='multipart/form-data'>";
            echo "<div style='text-align:left; font-weight:bold; margin:10px 0px;'>" . date("j M Y", $postedDate) . "</div>";
            echo "<input type='text' name='blogEntryTitle' value='$blogEntryTitle' /><br /><br />";

            //Pictures
            echo "Pic 1:<br />";
            if (file_exists("userPics/$blogUserId/$picName1.$picExt1")) {
                echo "Currently installed pic 1:<br />";
                echo "<img src='userPics/$blogUserId/$picName1.$picExt1' alt='' style='width:200px; margin:auto;' /><br />";
            }
            echo "Upload a new pic 1: <input type='file' name='image1' /><br />";
            echo "Pic 1 caption: <input type='text' name='picCaption1' value='$picCaption1' /><br /><br />";
            echo "Pic 2:<br />";
            if (file_exists("userPics/$blogUserId/$picName2.$picExt2")) {
                echo "Currently installed pic 2:<br />";
                echo "<img src='userPics/$blogUserId/$picName2.$picExt2' alt='' style='width:200px; margin:auto;' /><br />";
            }
            echo "Upload a new pic 2: <input type='file' name='image2' /><br />";
            echo "Pic 2 caption: <input type='text' name='picCaption2' value='$picCaption2' /><br /><br />";
            echo "Pic 3:<br />";
            if (file_exists("userPics/$blogUserId/$picName3.$picExt3")) {
                echo "Currently installed pic 3:<br />";
                echo "<img src='userPics/$blogUserId/$picName3.$picExt3' alt='' style='width:200px; margin:auto;' /><br />";
            }
            echo "Upload a new pic 3: <input type='file' name='image3' /><br />";
            echo "Pic 3 caption: <input type='text' name='picCaption3' value='$picCaption3' /><br /><br />";
            echo "Pic 4:<br />";
            if (file_exists("userPics/$blogUserId/$picName4.$picExt4")) {
                echo "Currently installed pic 4:<br />";
                echo "<img src='userPics/$blogUserId/$picName4.$picExt4' alt='' style='width:200px; margin:auto;' /><br />";
            }
            echo "Upload a new pic 4: <input type='file' name='image4' /><br />";
            echo "Pic 4 caption: <input type='text' name='picCaption4' value='$picCaption4' /><br /><br />\n";

            //Videos
            $b1 = $db->prepare("SELECT COUNT(*) FROM blogVideos WHERE blogEntryId=?");
            $b1->execute(array($blogId));
            $b1row = $b1->fetch();
            $vidCount = $b1row[0];
            echo "<span style='font-weight:bold;'>Add a youtube video:</span> (on the youtube page, under the video you want to share, click the 'share' button.  There is an address that shows up under the social media icons, copy it and paste it here under video address)<br />\n";
            echo "<table>\n";
            echo "<tr><td style='border:1px solid black;'>video title</td><td style='border:1px solid black;'>video address</td><td style='border:1px solid black;'>video order</td><td style='border:1px solid black;'>delete?</td></tr>\n";
            echo "<tr><td style='border:1px solid black;'><input type='text' name='videoTitleNew' value='' /></td><td style='border:1px solid black;'><input type='text' name='videoAddressNew' value='' /></td><td style='border:1px solid black;'><select name='videoOrderNew' size='1'>";
            for ($i = 1; $i <= ($vidCount + 1); $i++) {
                echo "<option value='$i'";
                if ($i == ($vidCount + 1)) {
                    echo " selected";
                }
                echo ">$i</option>";
            }
            echo "</select></td><td style='border:1px solid black;'></td></tr>\n";
            if ($vidCount >= 1) {
                $b2 = $db->prepare("SELECT * FROM blogVideos WHERE blogEntryId=? ORDER BY videoOrder");
                $b2->execute(array($blogId));
                while ($b2row = $b2->fetch()) {
                    $videoId = $b2row['id'];
                    $videoTitle = $b2row['videoTitle'];
                    $videoAddress = $b2row['videoAddress'];
                    $videoOrder = $b2row['videoOrder'];
                    echo "<tr><td style='border:1px solid black;'><input type='text' name='videoTitle$videoId' value='$videoTitle' /></td><td style='border:1px solid black;'><input type='text' name='videoAddress$videoId' value='https://youtu.be/$videoAddress' /></td><td style='border:1px solid black;'><select name='videoOrder$videoId' size='1'>";
                    for ($i = 1; $i <= ($vidCount + 1); $i++) {
                        echo "<option value='$i'";
                        if ($i == $videoOrder) {
                            echo " selected";
                        }
                        echo ">$i</option>\n";
                    }
                    echo "</select></td><td style='border:1px solid black;'><input type='checkbox' name='videoDel$videoId' value='1' /></td></tr>\n";
                }
            }
            echo "</table><br /><br />\n";

            //Text
            echo "Blog Entry Text:<br /><textarea name='blogEntryText' cols='50' rows='10' maxlength='65500'>$blogEntryText</textarea><br /><br />\n";
            echo "Delete my blog entry <input type='checkbox' name='delBlogEntry' value='1' /> This will delete your blog entry, and any pictures associated with it.<br /><br />\n";
            echo "<input type='hidden' name='blogId' value='$blogId' /><input type='submit' value=' Save ' /></form>\n";
            echo "<div style='width:75%; height:2px; margin:auto; background-color:#cccccc;'></div><br /><br />\n";
        }
    }
    echo "</div>\n";
}