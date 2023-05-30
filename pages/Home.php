<?php
if ($myZip != 0) {
    list ($getZipCodes1, $getZipCodes2, $getZipCodes3) = getZipAreas($myZip);

    $t = 0;
    foreach ($getZipCodes1 as $v1) {
        if ($t == 0) {
            $where = "WHERE (";
        }
        if ($t != 0) {
            $where .= " OR ";
        }
        $where .= "t2.zip=$v1";
        $t ++;
    }
    $where .= ") && t1.approved == '1'";
    $stmt5 = $db->prepare(
            "SELECT COUNT(*) FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where");
    $stmt5->execute();
    $row5 = $stmt5->fetch();
    $count5 = $row5[0];
    if ($count5 >= 1) {
        $stmt4 = $db->prepare(
                "SELECT t1.catId FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where ORDER BY RAND()");
        $stmt4->execute();
        $catIdCount = array();
        while ($row4 = $stmt4->fetch()) {
            $catIdCount[] = $row4['catId'];
        }
        $cats = array_count_values($catIdCount);
        arsort($cats);
        foreach ($cats as $k => $v) {
            $stmt2 = $db->prepare(
                    "SELECT category FROM articleCategories WHERE id = ?");
            $stmt2->execute(array(
                    $k
            ));
            $row2 = $stmt2->fetch();
            $catText = $row2['category'];
            echo "<div class='clearfix' style='margin-bottom:40px;'><div style='position:relative; top:2px; z-index:99; padding:10px; background-color:#ffffff; text-align:left;'><a href='index.php?page=Articles&category=$catText' style='font-weight:bold; text-decoration:underline; font-size:1.5em; color:$highlightColor;'>$catText</a></div>";
            $stmt6 = $db->prepare(
                    "SELECT t1.id FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where && t1.catId = ? ORDER BY t1.postedDate DESC LIMIT 6");
            $stmt6->execute(array(
                    $k
            ));
            while ($row6 = $stmt6->fetch()) {
                $id = $row6[0];
                displayArticle($id, $highlightColor);
            }
            echo "</div>";
        }
    }
    $s = 0;
    foreach ($getZipCodes2 as $k => $v) {
        if ($s == 0) {
            $where = "WHERE (";
        }
        if ($s != 0) {
            $where .= " OR ";
        }
        $where .= "t2.zip=$v";
        $s ++;
    }
    $where .= ") && t1.approved = '1'";
    $stmt3 = $db->prepare(
            "SELECT COUNT(*) FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where");
    $stmt3->execute();
    $row3 = $stmt3->fetch();
    $count = $row3[0];
    if ($count >= 1) {
        $stmt4 = $db->prepare(
                "SELECT DISTINCT t1.catId FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where ORDER BY RAND()");
        $stmt4->execute();
        while ($row4 = $stmt4->fetch()) {
            $catId = $row4['catId'];
            $stmt6 = $db->prepare(
                    "SELECT t1.id FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id $where && t1.catId = ? ORDER BY t1.postedDate DESC LIMIT 6");
            $stmt6->execute(array(
                    $catId
            ));
            while ($row6 = $stmt6->fetch()) {
                $id = $row6[0];
                displayArticle($id, $db, $highlightColor);
            }
        }
    }
} else {
    echo "<div class='clearfix' style='margin-bottom:40px;'>";
    echo "<div style='text-align:center; width:100%; margin-bottom:20px; font-weight:bold; font-size:1.5em; color:$highlightColor; text-decoration:none;'>Recent Articles</div><div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div>";
    $st = $db->query(
            "SELECT id, catId FROM articles WHERE approved = '1' postedDate <= '$time' ORDER BY postedDate DESC LIMIT 10");
    while ($r = $st->fetch()) {
        $id = $r[0];
        $catId = $r[1];
        $st2 = $db->prepare(
                "SELECT category FROM articleCategories WHERE id = ?");
        $st2->execute(array(
                $catId
        ));
        $r2 = $st2->fetch();
        $catText = $r2['category'];
        displayRecentArticle($id, $catText, $db, $highlightColor);
    }
    echo "</div>";

    $stmt4 = $db->prepare("SELECT catId FROM articles WHERE approved = '1'");
    $stmt4->execute();
    $catIdCount = array();
    while ($row4 = $stmt4->fetch()) {
        $catIdCount[] = $row4['catId'];
    }
    $cats = array_count_values($catIdCount);
    arsort($cats);
    foreach ($cats as $k => $v) {
        $stmt2 = $db->prepare(
                "SELECT category FROM articleCategories WHERE id = ?");
        $stmt2->execute(array(
                $k
        ));
        $row2 = $stmt2->fetch();
        $catText = $row2['category'];
        $catSlug = slugify($catText);
        echo "<div class='clearfix' style='margin-bottom:40px;'>";
        echo "<div style='text-align:center; width:100%; margin-bottom:20px;'><a href='Articles/category::$catSlug' style='font-weight:bold; font-size:1.5em; color:$highlightColor; text-decoration:none;'>$catText</a></div><div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div>";
        $stmt6 = $db->prepare(
                "SELECT id FROM articles WHERE approved = '1' postedDate <= '$time' ORDER BY postedDate DESC LIMIT 4");
        $stmt6->execute(array(
                $k
        ));
        while ($row6 = $stmt6->fetch()) {
            $id = $row6[0];
            displayArticle($id, $db, $highlightColor);
        }
        echo "</div>";
    }
}

$stmt21 = $db->prepare(
        "SELECT COUNT(*) FROM photoJournalism WHERE approved = ?");
$stmt21->execute(array(
        '1'
));
$row21 = $stmt21->fetch();
$pCount = $row21[0];
if ($pCount >= 1) {
    echo "<div class='clearfix' style='margin-bottom:40px;'>";
    echo "<div style='text-align:center; width:100%; margin-bottom:20px;'><a href='Photo' style='font-weight:bold; font-size:1.5em; color:$highlightColor; text-decoration:none;'>Photo Show</a></div><div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div>";
    $stmt19 = $db->prepare(
            "SELECT id FROM photoJournalism WHERE approved = ? ORDER BY postedDate DESC LIMIT 4");
    $stmt19->execute(array(
            '1'
    ));
    while ($row19 = $stmt19->fetch()) {
        $id = $row19['id'];
        displayPhoto($id, $db, $highlightColor);
    }
    echo "</div>";
}
