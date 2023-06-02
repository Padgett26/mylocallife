<?php
$stripId = filter_input(INPUT_GET, 'stripId', FILTER_SANITIZE_STRING);
$stmt = $db->prepare("SELECT * FROM strips WHERE id=? && userId=?");
$stmt->execute(array(
        $stripId,
        $myId
));
$row = $stmt->fetch();
$stripTitle = $row['stripTitle'];
$picName = ($row['picName']) ? $row['picName'] : '0';
$picExt = ($row['picExt']) ? $row['picExt'] : '0';
$displayDayStart = $row['displayDayStart'];
$startDay = date("j", $displayDayStart);
$startMonth = date("n", $displayDayStart);
$startYear = date("Y", $displayDayStart);
$displayDayEnd = $row['displayDayEnd'];
$endDay = date("j", $displayDayEnd);
$endMonth = date("n", $displayDayEnd);
$endYear = date("Y", $displayDayEnd);
$userId = $row['userId'];

if ($userId == $myId || ($myId != '0' && $stripId == "new")) {
    ?>
    <div id='mainTableBox' style="padding:10px;">
        <form action="index.php?page=myAccount" method="post" enctype='multipart/form-data'>
            Strip title: (To be tied to the other strips in a series, they all must have the same title.  A strip with a different title will be treated as being in a different series.)<br />
            <input type="text" name="stripTitle" value="<?php

echo $stripTitle;
    ?>" size="70" max-length="100" /><br /><br />
            <?php
    if (file_exists("userPics/$myId/$picName.$picExt")) {
        echo "Currently loaded strip:<br /><img src='userPics/$myId/$picName.$picExt' alt='' /><br /><br />";
    }
    ?>
            Upload strip (we accept jpg, gif, png) (You are given a space of 800px wide, by 800px tall. If your strip exceeds this, it will be shrunk to fit. To look the best on the site, I would suggest using the full 800px width, and then whatever height that works for your strip, just not over 800px.):<br /><input type="file" name="image" style="background-color:#dddddd;" /><br /><br />
            What days do you want this strip displayed on the page? (we will only display one strip per series at a time on the main comics page, so try not to overlap dates with other strips in this series)<br />
            Display start date: day<select name="startDay" size="1">
                <?php
    $thisyear = date("Y");
    for ($sd = 1; $sd <= 31; $sd ++) {
        echo "<option value='$sd'";
        echo ($sd == $startDay) ? " selected='selected'" : "";
        echo ">$sd</option>\n";
    }
    ?>
            </select> month<select name="startMonth" size="1">
                <?php
    for ($sm = 1; $sm <= 12; $sm ++) {
        echo "<option value='$sm'";
        echo ($sm == $startMonth) ? " selected='selected'" : "";
        echo ">$sm</option>\n";
    }
    ?>
            </select> year<select name="startYear" size="1">
                <?php
    for ($sy = ($thisyear - 1); $sy <= ($thisyear + 1); $sy ++) {
        echo "<option value='$sy'";
        echo ($sy == $startYear) ? " selected='selected'" : "";
        echo ">$sy</option>\n";
    }
    ?>
            </select><br />Display end date: day<select name="endDay" size="1">
                <?php
    for ($ed = 1; $ed <= 31; $ed ++) {
        echo "<option value='$ed'";
        echo ($ed == $endDay) ? " selected='selected'" : "";
        echo ">$ed</option>\n";
    }
    ?>
            </select> month<select name="endMonth" size="1">
                <?php
    for ($em = 1; $em <= 12; $em ++) {
        echo "<option value='$em'";
        echo ($em == $endMonth) ? " selected='selected'" : "";
        echo ">$em</option>\n";
    }
    ?>
            </select> year<select name="endYear" size="1">
                <?php
    for ($ey = ($thisyear - 1); $ey <= ($thisyear + 1); $ey ++) {
        echo "<option value='$ey'";
        echo ($ey == $endYear) ? " selected='selected'" : "";
        echo ">$ey</option>\n";
    }
    ?>
            </select><br /><br />
            You can upload a custom background for the strip series page.  The size limit for the background is 1200px by 1200px.  Remember that everyone uses different sized monitors, with different settings, so your background will look differently for everyone. Keep in mind that the image will repeat along x and y.<br />
            <?php
    $shortTitle = str_replace(" ", "", strtolower($stripTitle));
    if (file_exists("userPics/$myId/back$shortTitle.$backExt")) {
        echo "Currently loaded background for the $stripTitle series:<br /><img src='userPics/$myId/back$shortTitle.$backExt' alt='' /><br /><br />";
        echo "To remove the background pic without uploading a new one, check this checkbox: <input type='checkbox' name='removeBackPic' value='1' /><br /><br />";
    }
    echo 'Upload background pic: <input type="file" name="imageBack" style="background-color:#dddddd;" /><br /><br />';
    if ($stripId != "new") {
        ?>
                Do you want to delete this strip?  This is permanent and cannot be undone.<br />
                YES, <input type="checkbox" name="delStrip" value="1" /> delete this strip.<br /><br />
            <?php

}
    ?>
            <input type="hidden" name="editedStrip" value="<?php

echo $stripId;
    ?>" /><input type="submit" value=" Save changes " />
        </form>
    </div>
    <?php
} else {
    echo "You do not have permission to edit this article.";
}
