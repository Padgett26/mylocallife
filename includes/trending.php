<div style="text-align:center; font-size:0.75em; margin-top:15px;">Articles</div>
<?php
$delT = ($time - 1209600); // 2 weeks ago
$aLog = array();
$stmt1 = $db->prepare("DELETE FROM articleLog WHERE clickDate <= ?");
$stmt1->execute(array($delT));
$stmt2 = $db->prepare("SELECT articleId FROM articleLog");
$stmt2->execute();
while ($row2 = $stmt2->fetch()) {
    $aLog[] = $row2['articleId'];
}
$aCount = array_count_values($aLog);
arsort($aCount);
$t = 0;
foreach ($aCount as $k => $v) {
    $stmt = $db->prepare("SELECT articleTitle FROM articles WHERE id = ?");
    $stmt->execute(array($k));
    $row = $stmt->fetch();
    $articleTitle = $row['articleTitle'];
    $stmt3 = $db->prepare("SELECT COUNT(*) FROM articleLog WHERE articleId=?");
    $stmt3->execute(array($k));
    $row3 = $stmt3->fetch();
    $aCount = $row3[0];
    echo "<a style='color:black;' href='index.php?page=Articles&articleDetail=$k' title='$aCount views during the past 2 weeks'>\n<article style='overflow:hidden; padding:10px; margin:5px 0px; text-align:center; border:1px solid $highlightColor; width:95%; box-shadow: 5px 5px 5px grey; cursor:help;'>\n";
    echo "<header style='font-weight:bold; text-align:center; font-size:0.75em;'>$articleTitle</header></article></a>";
    $t++;
    if ($t == 5) {
        break;
    }
}
?>
<div style="text-align:center; font-size:0.75em; margin-top:15px;">Photo Show</div>
<?php
$delTp = ($time - 1209600); // 2 weeks ago
$pLog = array();
$stmtp = $db->prepare("DELETE FROM photoLog WHERE clickDate <= ?");
$stmtp->execute(array($delTp));
$stmtp2 = $db->prepare("SELECT photoId FROM photoLog");
$stmtp2->execute();
while ($rowp2 = $stmtp2->fetch()) {
    $pLog[] = $rowp2['photoId'];
}
$pCount = array_count_values($pLog);
arsort($pCount);
$p = 0;
foreach ($pCount as $k => $v) {
    $stmt = $db->prepare("SELECT photoTitle FROM photoJournalism WHERE id=?");
    $stmt->execute(array($k));
    $row = $stmt->fetch();
    $photoTitle = $row['photoTitle'];
    $stmt3 = $db->prepare("SELECT COUNT(*) FROM photoLog WHERE photoId=?");
    $stmt3->execute(array($k));
    $row3 = $stmt3->fetch();
    $aCount = $row3[0];
    echo "<a style='color:black;' href='index.php?page=Photo&photoShow=$k' title='$aCount views during the past 2 weeks'>\n<article style='overflow:hidden; padding:10px; margin:5px 0px; text-align:center; border:1px solid $highlightColor; width:95%; box-shadow: 5px 5px 5px grey; cursor:help;'>\n";
    echo "<header style='font-weight:bold; text-align:center; font-size:0.75em;'>$photoTitle</header></article></a>\n";
    $p++;
    if ($p == 5) {
        break;
    }
}
?>
<div style="text-align:center; font-size:0.75em; margin-top:15px;">Blogs</div>
<?php
$delTb = ($time - 1209600); // 2 weeks ago
$bLog = array();
$stmtb = $db->prepare("DELETE FROM blogLog WHERE clickDate <= ?");
$stmtb->execute(array($delTb));
$stmtb2 = $db->prepare("SELECT blogUserId FROM blogLog");
$stmtb2->execute();
while ($rowb2 = $stmtb2->fetch()) {
    $bLog[] = $rowb2['blogUserId'];
}
$bCount = array_count_values($bLog);
arsort($bCount);
$b = 0;
foreach ($bCount as $k => $v) {
    $stmt = $db->prepare("SELECT blogTitle FROM blogDescriptions WHERE userId=?");
    $stmt->execute(array($k));
    $row = $stmt->fetch();
    $blogTitle = $row['blogTitle'];
    $stmt3 = $db->prepare("SELECT COUNT(*) FROM blogLog WHERE blogUserId=?");
    $stmt3->execute(array($k));
    $row3 = $stmt3->fetch();
    $aCount = $row3[0];
    echo "<a style='color:black;' href='index.php?page=Blog&blogUserId=$k' title='$aCount views during the past 2 weeks'>\n<article style='overflow:hidden; padding:10px; margin:5px 0px; text-align:center; border:1px solid $highlightColor; width:95%; box-shadow: 5px 5px 5px grey; cursor:help;'>\n";
    echo "<header style='font-weight:bold; text-align:center; font-size:0.75em;'>$blogTitle</header></article></a>\n";
    $b++;
    if ($b == 5) {
        break;
    }
}