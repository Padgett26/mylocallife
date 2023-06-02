<?php
if (filter_input(INPUT_GET, 'business', FILTER_SANITIZE_NUMBER_INT)) {
    $bid = filter_input(INPUT_GET, 'business', FILTER_SANITIZE_NUMBER_INT);

    $busi = $db->prepare("SELECT * FROM busiListing WHERE userId=?");
    $busi->execute(array(
            $bid
    ));
    $brow = $busi->fetch();
    $busiName = $brow['busiName'];
    $busiPhone = $brow['busiPhone'];
    $hoursOfOperation = nl2br($brow['hoursOfOperation']);
    $busiAddress1 = $brow['busiAddress1'];
    $busiAddress2 = $brow['busiAddress2'];
    $busiPic1 = $brow['busiPic1'];
    $busiPicExt1 = $brow['busiPicExt1'];
    $busiPic2 = $brow['busiPic2'];
    $busiPicExt2 = $brow['busiPicExt2'];
    $busiDescText = nl2br(
            make_links_clickable(
                    html_entity_decode($brow['busiDescText'], ENT_QUOTES)));
    $textLen = strlen($busiDescText);
    $offset1 = $textLen * (2 / 3);
    $pos1 = strpos($busiDescText, "<br />", $offset1) + 6;
    $busiEmail = $brow['busiEmail'];

    echo "<div id='mainTableBox' style='padding:40px 0px;'><div id='printArea'>";
    echo "<header style='text-align:center; font-weight:bold;'><span style='font-size:2em;'>$busiName</span><br /><br /><span style='font-size:1.5em;'>$busiPhone<br /><br />$busiAddress1<br />$busiAddress2<br /><br /><a href='mailto:$busiEmail'>$busiEmail</a></span></header><br /><br />";
    if (file_exists("userPics/$bid/$busiPic1.$busiPicExt1")) {
        echo "<img src='userPics/$bid/$busiPic1.$busiPicExt1' alt='' style='max-width:500px; max-height:500px; padding:2px; border:1px solid $highlightColor; margin:10px; float:right;' />";
    }
    echo "<article style='font-weight:bold; text-align:center; margin:30px 0px;'>Hours of Operation<br />$hoursOfOperation</article>";
    echo "<article style='text-align:justify; margin:30px 0px;'>" .
            substr($busiDescText, 0, $pos1) . "</article>";
    if (file_exists("userPics/$bid/$busiPic2.$busiPicExt2")) {
        echo "<img src='userPics/$bid/$busiPic2.$busiPicExt2' alt='' style='max-width:500px; max-height:500px; padding:2px; border:1px solid $highlightColor; margin:10px; float:left;' />";
    }
    echo "<article style='text-align:justify; margin:30px 0px;'>" .
            substr($busiDescText, $pos1 + 1) . "</article>";
    echo "</div></div>";
}
