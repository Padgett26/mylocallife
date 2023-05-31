<?php

if (filter_input(INPUT_GET, 'getSeries', FILTER_SANITIZE_NUMBER_INT)) {
    $getId = filter_input(INPUT_GET, 'getSeries', FILTER_SANITIZE_NUMBER_INT);
    $stmt3 = $db->prepare("SELECT stripTitle FROM strips WHERE id = ?");
    $stmt3->execute(array($getId));
    $row3 = $stmt3->fetch();
    $byTitle = $row3['stripTitle'];
    echo "<div style='text-align:left; font-weight:bold; font-size:1.25em;'>$byTitle</div>";
    $stmt4 = $db->prepare("SELECT * FROM strips WHERE stripTitle = ? ORDER BY displayDayStart");
    $stmt4->execute(array($byTitle));
    while ($row4 = $stmt4->fetch()) {
        $userId = $row2['userId'];
        $picName = $row4['picName'];
        $picExt = $row4['picExt'];
        $displayDayStart = $row4['displayDayStart'];
        $displayDayEnd = $row4['displayDayEnd'];
        echo "<div style='margin: 20px auto; width:800px; text-align:left; font-weight:bold; font-size:1.25em; background-color:#ffffff;'>";
        echo (date("M j, Y", $displayDayStart) == date("M j, Y", $displayDayEnd)) ? date("M j, Y", $displayDayStart) : date("M j, Y", $displayDayStart) . " - " . date("M j, Y", $displayDayEnd);
        echo "<br /><img src='userPics/$userId/$picName.$picExt' alt='' />";
        echo "</div>";
    }
} else {
    $stmt1 = $db - prepare("SELECT DISTINCT stripTitle FROM strips WHERE displayDayStart <= ? && displayDayEnd >= ? ORDER BY RAND()");
    $stmt1->execute(array($time, $time));
    while ($row1 = $stmt1->fetch()) {
        $stripTitle = $row1['stripTitle'];
        $stmt2 = $db->prepare("SELECT * FROM strips WHERE stripTitle = ? && displayDayStart <= ? && displayDayEnd >= ? LIMIT 1");
        $stmt2->execute(array($stripTitle, $time, $time));
        $row2 = $stmt2->fetch();
        $stripId = $row2['id'];
        $userId = $row2['userId'];
        $picName = $row2['picName'];
        $picExt = $row2['picExt'];
        echo "<div style='margin: 20px auto; width:800px; text-align:left; font-weight:bold; font-size:1.25em; background-color:#ffffff;'>";
        echo "<div style='float:right;'><a href='index.php?page=ComicStrips&getSeries=$stripId'>View entire series</a></div>";
        echo "$stripTitle<br /><img src='userPics/$userId/$picName.$picExt' alt='' />";
        echo "</div>";
    }
}