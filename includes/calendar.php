<?php

echo "<div style='margin-left:20px;'><br />";
echo "<div style='text-align:center;'><a href='index.php?page=calendar' style='font-size:1em; text-decoration:none; color:$highlightColor;'>Go To Calendar Page</a></div><br />";
$m = date('n') + 3;
$y = date('Y');
$twomonths = mktime(23, 59, 59, $m, -1, $y);
$llm = date('n',$time);
$lld = date('j',$time);
$lly = date('Y',$time);
$lowerLimit = mktime(0,0,0,$llm,$lld,$lly);
$z = 0;
$stmt = $db->prepare("SELECT id, startTime, title FROM calendar WHERE startTime >= ? && startTime <= ? && approved = '1' ORDER BY startTime");
$stmt->execute(array($lowerLimit, $twomonths));
while ($row = $stmt->fetch()) {
    $cId = $row['id'];
    $cStartTime = $row['startTime'];
    $cTitle = $row['title'];

    if ($z != 0) {
        echo "<div style='width:100px; margin:15px 40px; height:5px; background-color:#dddddd; border:1px solid $highlightColor;'></div>";
    }
    echo "<div style='text-align:center;'><a href='index.php?page=calendar&h=$cId#c$cId' style='font-size:.85em; font-weight:bold; text-decoration:none;'>$cTitle</a></div>";
    echo "<div style='font-size:.75em; text-align:center;'>" . date('M jS, g:ia', $cStartTime) . "</div>";
    $z++;
}
echo "</div>";
