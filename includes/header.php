<!-- Beginning of Header -->

<?php
echo "<div class='clearfix' style='width:100%; margin-bottom:30px; text-align:center;'>";
if ($useLocalization == 1) {
    echo "<div class='menu' style='text-align:center; margin-top:-10px;'><form action='Home' method='get'>Home zip<br /><input type='text' name='myZip' size='20' value='$myZip' style='text-align:center; background-color:#eeeeee; border:1px solid $highlightColor; box-shadow: 5px 5px 5px grey;' onchange='submit()' /></form></div>";
}
echo "<div class='menu'><a href='Home' class='menu'>Home</a></div>";
echo "<div class='menu'><a onclick='toggleview(\"categoriesBox\")' class='menu'>Article&nbsp;Categories</a></div>";
if ($showClassifieds == 1) {
    echo "<div class='menu'><a onclick='toggleview(\"classifiedsBox\")' class='menu'>Classifieds</a></div>";
}
if ($showWritings == 1) {
    echo "<div class='menu'><a href='Writings' class='menu'>Writings</a></div>";
}
if ($showBlogs == 1) {
    echo "<div class='menu'><a id='blogLink' onclick='showMenu(\"blogBox\", \"blogLink\")' class='menu'>Blogs</a></div>";
}
if ($showDirectory == 1) {
    echo "<div class='menu'><a href='Directory' class='menu'>Phone&nbsp;Directory</a></div>";
}
if ($showGames == 1) {
    echo "<div class='menu'><a id='gamesLink' onclick='showMenu(\"gamesBox\", \"gamesLink\")' class='menu'>Games</a></div>";
}
if ($showSignUp == 1) {
    echo "<div class='menu'><a href='SignUp' class='menu'>Event Sign Up</a></div>";
}
if ($myId != '0') {
    if (isAdmin($myId)) {
        echo "<div class='menu'><a id='accountLink' onclick='showMenu(\"accountBox\", \"accountLink\")' class='menu'>Admin</a></div>";
    } else {
        echo "<div class='menu'><a href='MyAccount' class='menu'>My&nbsp;account</a></div>";
    }
    echo "<div class='menu'><a href='Home/logout' class='menu'>Log&nbsp;out</a></div>";
} else {
    echo "<div class='menu'><a id='signInLink' onclick='showMenu(\"signInBox\", \"signInLink\")' class='menu'>Sign&nbsp;in&nbsp;/&nbsp;Register</a></div>";
}
echo "<div class='menu' style='text-align:center; margin-top:-15px;'><span style='font-size:.75em;'>Search</span><br /><form action='index.php/SearchResults' method='get'><input type='text' name='search' size='20' value='' style='text-align:center; background-color:#eeeeee; border:1px solid $highlightColor; box-shadow: 5px 5px 5px grey;' onchange='submit()' /><input type='hidden' name='page' value='searchResults' /></form></div>";
echo "</div>";

echo "<div class='clearfix' style='width:100%; margin-bottom:30px;'>";

echo "<div id='categoriesBox' class='subMenu' onmouseout='toggleview(\"categoriesBox\")'>\n";
$stmt = $db->prepare("SELECT DISTINCT catId FROM articles");
$stmt->execute();
$a = 0;
while ($row = $stmt->fetch()) {
    $catId = $row['catId'];
    $substmt = $db->prepare("SELECT category FROM articleCategories WHERE id=?");
    $substmt->execute(array(
            $catId
    ));
    $subrow = $substmt->fetch();
    if ($subrow) {
        $catName = $subrow['category'];
        echo ($a != 0) ? "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;\n" : "";
        echo "<a href='Articles/Category::" . slugify($catName) .
                "' class='submenu'>$catName</a>\n";
        $a ++;
    }
}
echo "</div>";

echo "<div id='classifiedsBox' class='subMenu' onmouseout='toggleview(\"classifiedsBox\")'>\n";
$stmtc = $db->prepare(
        "SELECT DISTINCT catId FROM classifieds WHERE displayUntil >= ?");
$stmtc->execute(array(
        $time
));
$b = 0;
while ($rowc = $stmtc->fetch()) {
    $catId = $rowc['catId'];
    $substmt = $db->prepare(
            "SELECT category FROM classifiedCategories WHERE id=?");
    $substmt->execute(array(
            $catId
    ));
    $subrow = $substmt->fetch();
    $catName = $subrow['category'];
    echo ($b != 0) ? "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;\n" : "";
    echo "<a href='Classifieds/Category::" . slugify($catName) .
            "' class='submenu'>$catName</a>\n";
    $b ++;
}
echo "</div>\n";

if ($myId != '0') {
    echo "<div id='accountBox' class='subMenu' onmouseout='toggleview(\"accountBox\")'>\n";
    echo "<a href='MyAccount' class='submenu'>My&nbsp;account</a>\n";
    if (isAdmin($myId)) {
        echo "&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;<a href='index.php/Gadmin' class='submenu'>Global&nbsp;admin</a>\n";
    }
    echo "</div>\n";
}

echo "<div id='factoidBox' class='subMenu'>\n";
echo "<form action='Home' method='post'>Give us a fact-tot:<br /><input type='text' name='factoid' size='40' /><br /><input type='submit' value=' It is a fact! ' /></form>\n";
echo "</div>\n";

echo "<div id='quoteBox' class='subMenu'>\n";
echo "<form action='Home' method='post'>Give us a quote:<br /><input type='text' name='newQuote' size='40' /><br />Author:<br /><input type='text' name='author' size='40' /><br /><input type='submit' value=' Send it in ' /></form>\n";
echo "</div>\n";

echo "<div id='gamesBox' class='subMenu' onmouseout='toggleview(\"gamesBox\")'>\n";
echo "<a href='Games' class='submenu'>Puzzles</a>\n";
echo "</div>\n";

$blog = $db->prepare(
        "SELECT userId FROM blogDescriptions ORDER BY RAND() LIMIT 1");
$blog->execute();
$blogrow = $blog->fetch();
$bUserId = $blogrow['userId'];
echo "<div id='blogBox' class='subMenu' onmouseout='toggleview(\"blogBox\")'>\n";
echo "<a href='Blog' class='submenu'>Blog&nbsp;Page</a>&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;\n";
echo "<a href='Blog/" . slugify($bUserId) .
        "' class='submenu'>Grab&nbsp;a&nbsp;random&nbsp;blog</a>\n";
echo "</div>\n";

echo "<div id='signInBox' class='subMenu'>";
echo "<form method='post' action='Home'>";
echo "<span style='color:#999; font-weight:bold; font-size:1.25em;'>Sign&nbsp;In</span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
echo "<span style='margin-top:20px;'>Email</span>&nbsp;&nbsp;";
echo "<input name='email' value='' type='email' autocomplete='on' placeholder='Email' required style='margin-left:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;";
echo "<span style='margin-top:20px;'>Password</span>&nbsp;&nbsp;";
echo "<input name='pwd' value='' type='password' placeholder='Password' required style='margin-left:10px; margin-top:10px;' />&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;";
echo "<input type='hidden' name='login' value='1' />";
echo "<input type='submit' style='margin-top:10px;' value=' Sign in ' />";
echo "</form><br /><br />";
echo "<a href='Register'>Register</a>&nbsp;&nbsp;&nbsp;||&nbsp;&nbsp;&nbsp;";
echo "<a href='PWReset'>Forgot your password?</a>";

echo "</div>";
echo "</div>";

if ($showFactoids == 1) {
    if ($myId >= 1) {
        // Process the new factoid
        if (filter_input(INPUT_POST, 'newQuote', FILTER_SANITIZE_STRING)) {
            $h = htmlEntities(
                    trim(
                            filter_input(INPUT_POST, 'newQuote',
                                    FILTER_SANITIZE_STRING)), ENT_QUOTES);
            $hauthor = htmlEntities(
                    trim(
                            filter_input(INPUT_POST, 'author',
                                    FILTER_SANITIZE_STRING)), ENT_QUOTES);
            $hda = $db->prepare(
                    "SELECT COUNT(*) FROM quotes WHERE quote = ? && author = ?");
            $hda->execute(array(
                    $h,
                    $hauthor
            ));
            $hdarow = $hda->fetch();
            $dup = $hdarow[0];
            if ($dup == 0) {
                $hdb = $db->prepare(
                        "INSERT INTO quotes VALUES(NULL,?,?,'0','0','0')");
                $hdb->execute(array(
                        $h,
                        $hauthor
                ));
            }
            echo "&nbsp;&nbsp;&nbsp;<span style='font-weight:bold;'>Thank you for the quote</span>";
        }
        if (filter_input(INPUT_POST, 'factoid', FILTER_SANITIZE_STRING)) {
            $g = htmlEntities(
                    trim(
                            filter_input(INPUT_POST, 'factoid',
                                    FILTER_SANITIZE_STRING)), ENT_QUOTES);
            $gdb = $db->prepare(
                    "INSERT INTO factoids VALUES(NULL,?,'0','0',?,'0','0','0')");
            $gdb->execute(array(
                    $g,
                    $myId
            ));
        }
    }
    echo "<div style='border:1px solid $highlightColor; border-radius:10px; padding:5px; font-size:.75em; text-align:center; width:100%; margin:0px;'>";
    $f = ($myId >= 1) ? 0 : rand(0, 1);
    $f2 = 0;
    if ($f == 0) {
        $f2 = rand(0, 1);
    }
    if ($f2 == 1) {
        $fact = $db->prepare(
                "SELECT quote, author FROM quotes WHERE approved = '1' ORDER BY RAND() LIMIT 1");
        $fact->execute();
        $frow = $fact->fetch();
        $factoid = html_entity_decode($frow['quote'], ENT_QUOTES);
        $author = html_entity_decode($frow['author'], ENT_QUOTES);
        echo "<span style='font-weight:bold;'>Quote:</span>&nbsp;&nbsp;&nbsp;$factoid - $author";
        if ($myId >= 1) {
            echo "&nbsp;&nbsp;&nbsp;<a id='quoteLink' onclick='showMenu(\"quoteBox\", \"quoteLink\")' class='menu'><span style='font-weight:bold;'>Give us a quote</span>";
        }
        echo "</a>";
    } else {
        $fact = $db->prepare(
                "SELECT factoid FROM factoids WHERE mllLocal = ? && approved = '1' ORDER BY RAND() LIMIT 1");
        $fact->execute(array(
                $f
        ));
        $frow = $fact->fetch();
        $factoid = html_entity_decode($frow['factoid'], ENT_QUOTES);
        echo "<span style='font-weight:bold; cursor:help;' title='A trivial fact, or perhaps a fact that has not quite yet reached puberty'>Fact-tots:</span>&nbsp;&nbsp;&nbsp;$factoid";
        if ($myId >= 1) {
            echo "&nbsp;&nbsp;&nbsp;<a id='factoidLink' onclick='showMenu(\"factoidBox\", \"factoidLink\")' class='menu'><span style='font-weight:bold;'>Know a fact-tot you wish to share?</span></a>";
        }
    }
    echo "</div>";
}
if ($showHolidays == 1) {
    $thisMonth = date('n', $CST);
    $thisDay = date('j', $CST);
    $thisYear = date('Y', $CST);
    $f1 = $db->prepare(
            "SELECT COUNT(*) FROM holidays WHERE month = ? && day = ?");
    $f1->execute(array(
            $thisMonth,
            $thisDay
    ));
    $fr = $f1->fetch();
    if ($fr[0] >= 1) {
        $t = 1;
        echo "<div style='text-align:center; margin-top:20px;'><span style='border:1px solid $highlightColor; border-radius:10px; padding:5px; font-size:.75em; font-weight:bold;'>";
        $fact = $db->prepare(
                "SELECT holiday, linkURL FROM holidays WHERE month = ? && day = ? ORDER BY RAND()");
        $fact->execute(array(
                $thisMonth,
                $thisDay
        ));
        while ($frow = $fact->fetch()) {
            $holiday = $frow['holiday'];
            $linkURL = $frow['linkURL'];
            $mark = ($t >= 2) ? "&nbsp;&nbsp;||&nbsp;&nbsp;" : "";
            echo "$mark";
            echo ($linkURL != '0' && $linkURL != "" && $linkURL != " ") ? "<a href='$linkURL' target='_blank'>$holiday</a>" : "$holiday";
            echo "";
            $t ++;
        }
        echo "</span></div>";
    }
}
?>
<!-- End of Header -->
