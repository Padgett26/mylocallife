<?php
if ($myAccess == '3') {

    // Feedback processing
    if (filter_input(INPUT_POST, 'fbUpdate', FILTER_SANITIZE_NUMBER_INT) == '1') {
        include "formProcessing/Gadmin/fbUpdate.php";
    }

    // Quotes processing
    if (filter_input(INPUT_POST, 'qUpdate', FILTER_SANITIZE_NUMBER_INT) == '1') {
        include "formProcessing/Gadmin/qUpdate.php";
    }

    // Events processing
    if (filter_input(INPUT_POST, 'eUpdate', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/eUpdate.php";
    }

    // Factoid processing
    if (filter_input(INPUT_POST, 'fUpdate', FILTER_SANITIZE_NUMBER_INT) == '1') {
        include "formProcessing/Gadmin/fUpdate.php";
    }

    // Article approval processing
    if (filter_input(INPUT_POST, 'approveArt', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/approveArt.php";
    }

    // Photo Show approval processing
    if (filter_input(INPUT_POST, 'approvePhoto', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/approvePhoto.php";
    }

    // Article report processing
    if (filter_input(INPUT_POST, 'reportArt', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/reportArt.php";
    }

    if (filter_input(INPUT_POST, 'getAdvertising', FILTER_SANITIZE_STRING)) {
        include "formProcessing/Gadmin/getAdvertising.php";
    }

    if (filter_input(INPUT_POST, 'delAdvert', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/delAdvert.php";
    }

    // Sudoku puzzel processing
    if (filter_input(INPUT_POST, 'sudokuUpdate', FILTER_SANITIZE_NUMBER_INT)) {
        include "formProcessing/Gadmin/sudokuPuzzels.php";
    }

    // clear unverified users
    if (filter_input(INPUT_POST, 'clearUsers', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $c = $db->prepare("DELETE FROM users WHERE accessLevel = '0'");
        $c->execute();
    }

    // create article slugs
    if (filter_input(INPUT_POST, 'createSlugs', FILTER_SANITIZE_NUMBER_INT) == 1) {
        $c = $db->prepare("SELECT id, articleTitle FROM articles");
        $c->execute();
        while ($cr = $c->fetch()) {
            $id = $cr['id'];
            $slug = slugify($cr['articleTitle']);
            $check = $db->prepare(
                    "SELECT COUNT(*) FROM articles WHERE slug = ?");
            $check->execute(array(
                    $slug
            ));
            $checkR = $check->fetch();
            $count = $checkR[0];
            if ($count >= 1) {
                $slug .= "-" . $count;
            }
            $saveSlug = $db->prepare(
                    "UPDATE articles SET slug = ? WHERE id = ?");
            $saveSlug->execute(array(
                    $slug,
                    $id
            ));
        }
    }

    // Writing processing
    if (filter_input(INPUT_POST, 'writingUp', FILTER_SANITIZE_NUMBER_INT) >= 1) {
        $writingId = filter_input(INPUT_POST, 'writingUp',
                FILTER_SANITIZE_NUMBER_INT);
        $writingApprove = filter_input(INPUT_POST, 'writingApprove',
                FILTER_SANITIZE_NUMBER_INT);
        $wup = $db->prepare("UPDATE myWritings SET approved = ? WHERE id = ?");
        $wup->execute(array(
                $writingApprove,
                $writingId
        ));
    }
    ?>
	<main id='mainTableBox' style="position:relative; top:0px; left:0px; padding:10px 0px 0px 10px;">
		<section>
			<?php
    $stmt0 = $db->prepare(
            "SELECT COUNT(*) FROM reported WHERE reportedBy = ? && clearedTime = ?");
    $stmt0->execute(array(
            '0',
            '0'
    ));
    $row0 = $stmt0->fetch();
    $repArtCount = ($row0[0] >= '1') ? $row0[0] : '0';
    ?>
			<header onclick="toggleview('newArticles')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $repArtCount;
    ?>) - New articles
			</header>
			<article id="newArticles" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    $stmt1 = $db->prepare(
            "SELECT articleId, whyReported FROM reported WHERE reportedBy = ? && clearedTime = ? ORDER BY reportedTime");
    $stmt1->execute(array(
            '0',
            '0'
    ));
    while ($row1 = $stmt1->fetch()) {
        $repArtId = $row1['articleId'];
        $whyReported = $row1['whyReported'];

        $substmt2 = $db->prepare("SELECT * FROM articles WHERE id=?");
        $substmt2->execute(array(
                $repArtId
        ));
        $row = $substmt2->fetch();
        $artId = $row['id'];
        $articleTitle = $row['articleTitle'];
        $articleText = nl2br(
                make_links_clickable(
                        html_entity_decode($row['articleText'], ENT_QUOTES)));
        $pic1Name = $row['pic1Name'];
        $pic1Ext = $row['pic1Ext'];
        $pic1Caption = make_links_clickable($row['pic1Caption']);
        $pic2Name = $row['pic2Name'];
        $pic2Ext = $row['pic2Ext'];
        $pic2Caption = make_links_clickable($row['pic2Caption']);
        $postedDate = $row['postedDate'];
        $editedDate = $row['editedDate'];
        $articleScope = $row['articleScope'];
        $authorId = $row['authorId'];
        $inReplyTo = $row['inReplyTo'];
        $youtube = $row['youtube'];
        $pdf1 = $row['pdf1'];
        $pdfText1 = $row['pdfText1'];
        $pdf2 = $row['pdf2'];
        $pdfText2 = $row['pdfText2'];
        $substmt = $db->prepare(
                "SELECT firstName, lastName FROM users WHERE id=?");
        $substmt->execute(array(
                $authorId
        ));
        $subrow = $substmt->fetch();
        $firstName = $subrow['firstName'];
        $lastName = $subrow['lastName'];
        $textLen = strlen($articleText);
        $offset1 = $textLen / 10;
        $offset2 = $offset1 * 7;
        $pos1 = strpos($articleText, "<br />", $offset1) + 6;
        $pos2 = strpos($articleText, "<br />", $offset2) + 6;

        if ($inReplyTo != '0') {
            $gr = $db->prepare(
                    "SELECT t1.articleTitle, t1.postedDate, t2.firstName, t2.lastName FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.id=?");
            $gr->execute(array(
                    $inReplyTo
            ));
            $grow = $gr->fetch();
            $artTitle = $grow['articleTitle'];
            $artPosted = $grow['postedDate'];
            $artFN = $grow['firstName'];
            $artLN = $grow['lastName'];
            echo "<div style='margin:20px 0px;'><a href='index.php?page=Articles&articleDetail=$inReplyTo'>This article is a reply to:<br />- $artTitle<br />\t<span style='font-size:.75em;'>" .
                    date("M j, Y", $artPosted) .
                    "\t by $artFN $artLN</span></a></div>";
        }
        echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$articleTitle</header>\n";
        echo "Post date: " . date("M j, Y", $postedDate) . "<br />";
        if ($editedDate != '0') {
            echo "Last edit date: " . date("M j, Y", $editedDate) . "<br />";
        }
        echo "<br />by: $firstName $lastName<br /><br />";
        echo "<article style='text-align:justify; min-height:500px;'>";
        if ($youtube != '0') {
            echo "<iframe width='560' height='315' src='https://www.youtube.com/embed/$youtube' frameborder='0' allowfullscreen></iframe><br /><br />";
        }
        if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext") ||
                file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
            if ($textLen >= 1000) {
                echo substr($articleText, 0, $pos1);
                if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext")) {
                    echo "<div style='margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; width:390px; float:right;'><img src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic1Caption</figcaption></div>";
                }
                if ($pos2 > $pos1) {
                    echo substr($articleText, $pos1 + 1, $pos2 - $pos1 - 1);
                    if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
                        echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
                    }
                    echo substr($articleText, $pos2 + 1);
                } else {
                    echo substr($articleText, $pos1 + 1);
                    if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
                        echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
                    }
                }
            } else {
                if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext")) {
                    echo "<div style='margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; width:390px; float:right;'><img src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic1Caption</figcaption></div>";
                }
                echo $articleText;
                if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
                    echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:390px; margin:auto;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
                }
            }
        } else {
            echo $articleText;
        }
        if (file_exists("userPics/$authorId/$pdf1.pdf") ||
                file_exists("userPics/$authorId/$pdf2.pdf")) {
            echo "<div style='margin-top:60px;'>";
            echo "** PDF's available with this article **";
            echo "</div>";
        }
        if (file_exists("userPics/$authorId/$pdf1.pdf")) {
            $pt1 = ($pdfText1 != "") ? $pdfText1 : "PDF 1";
            echo "<div style='margin-top:20px;'>";
            echo "<a href='userPics/$authorId/$pdf1.pdf' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$pt1</a>";
            echo "</div>";
        }
        if (file_exists("userPics/$authorId/$pdf2.pdf")) {
            $pt2 = ($pdfText2 != "") ? $pdfText2 : "PDF 2";
            echo "<div style='margin-top:20px;'>";
            echo "<a href='userPics/$authorId/$pdf2.pdf' target='_blank' style='color:$highlightColor; text-decoration:underline;'>$pt2</a>";
            echo "</div>";
        }
        echo "</article>";
        ?>
					<form style="margin-top:30px;" action="index.php?page=Gadmin" method="post">
						Yes <input type="radio" name="approve" value='1' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No <input type="radio" name="approve" value='0' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No, and delete <input type="radio" name="approve" value='2' /><br /><br />
						If no, msg to author:<br />
						<textarea name='msgToAuthor' cols='90' rows='10'><?php

        echo $whyReported;
        ?></textarea><br /><br />
						<input type='hidden' name='approveArt' value='<?php

        echo $artId;
        ?>' /><input type='submit' value=' Go ' />
					</form>
					<hr style="width:80%;" />
				<?php
    }
    ?>
			</article>
		</section>
		<section>
			<?php
    $p1 = $db->prepare(
            "SELECT COUNT(*) FROM photoJournalism WHERE approved = ?");
    $p1->execute(array(
            '0'
    ));
    $pr1 = $p1->fetch();
    $repPCount = ($pr1[0] >= 1) ? $pr1[0] : 0;
    ?>
			<header onclick="toggleview('newPhoto')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $repPCount;
    ?>) - New Photo Show
			</header>
			<article id="newPhoto" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    $substmt2 = $db->prepare("SELECT * FROM photoJournalism WHERE approved='0'");
    $substmt2->execute();
    while ($row = $substmt2->fetch()) {
        $pId = $row['id'];
        $photoTitle = $row['photoTitle'];
        $postedDate = $row['postedDate'];
        $editedDate = $row['editedDate'];
        $photoText = nl2br(
                make_links_clickable(
                        html_entity_decode($row['photoText'], ENT_QUOTES)));
        $authorId = $row['authorId'];
        $substmt = $db->prepare(
                "SELECT firstName, lastName FROM users WHERE id=?");
        $substmt->execute(array(
                $authorId
        ));
        $subrow = $substmt->fetch();
        $firstName = $subrow['firstName'];
        $lastName = $subrow['lastName'];

        echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$photoTitle</header>\n";
        echo "Posted date: " . date("M j, Y", $postedDate) . "<br />";
        if ($editedDate != '0') {
            echo "Last edit date: " . date("M j, Y", $editedDate) .
                    "<br /><br />";
        }
        echo "<br />by: $firstName $lastName<br /><br />";
        echo "<article style='text-align:justify;'>$photoText</article><br /><br /><br />";
        $pst2 = $db->prepare(
                "SELECT * FROM photoList WHERE photoId = ? ORDER BY photoOrder");
        $pst2->execute(array(
                $pId
        ));
        while ($prow2 = $pst2->fetch()) {
            $photoName = $prow2['photoName'];
            $photoExt = $prow2['photoExt'];
            $photoCaption = nl2br(make_links_clickable($prow2['photoCaption']));

            if (file_exists("userPics/$authorId/$photoName.$photoExt")) {
                echo "<div style='width:100%; text-align:justify; margin-botom:30px;'>";
                echo "<img src='userPics/$authorId/$photoName.$photoExt' alt='' style='margin:10px 5%; border:1px solid $highlightColor; padding:5px; width:90%;' /><br />";
                echo "$photoCaption</div>";
            }
        }
        ?>
					<form style="margin-top:30px;" action="index.php?page=Gadmin" method="post">
						Yes <input type="radio" name="approve" value='1' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No <input type="radio" name="approve" value='0' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No, and delete <input type="radio" name="approve" value='2' /><br /><br />
						If no, msg to author:<br />
						<textarea name='msgToAuthor' cols='90' rows='10'></textarea><br /><br />
						<input type='hidden' name='approvePhoto' value='<?php

        echo $pId;
        ?>' /><input type='submit' value=' Go ' />
					</form>
					<hr style="width:80%;" />
				<?php
    }
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmtw = $db->prepare(
            "SELECT COUNT(*) FROM myWritings WHERE approved = '0'");
    $stmtw->execute();
    $roww = $stmtw->fetch();
    $wCount = ($roww[0] >= '1') ? $roww[0] : '0';
    ?>
			<header onclick="toggleview('w')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $wCount;
    ?>) - Chapters to approve
			</header>
			<article id="w" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<form action='index.php?page=$page' method='post'><table cellspacing='0' cellpadding='10'>";
    $getWr = $db->prepare(
            "SELECT * FROM myWritings WHERE approved = '0' ORDER BY editDate");
    $getWr->execute();
    while ($getWR = $getWr->fetch()) {
        $wId = $getWR['id'];
        $authorId = $getWR['authorId'];
        $title = $getWR['title'];
        $part = $getWR['part'];
        $chapter = $getWR['chapter'];
        $ptTitle = $getWR['ptTitle'];
        $chTitle = $getWR['chTitle'];
        $chText = $getWR['chText'];
        $getAuth = $db->prepare(
                "SELECT firstName, lastName FROM users WHERE id = ?");
        $getAuth->execute(array(
                $authorId
        ));
        $getAuthR = $getAuth->fetch();
        $firstName = $getAuthR['firstName'];
        $lastName = $getAuthR['lastName'];

        echo "<div style='cursor:pointer;' onclick='toggleview(\"writing" . $wId .
                "\")'>$title by $firstName $lastName Part $part Chapter $chapter</div>";
        echo "<div id='writing" . $wId . "' style='display:none;'>";
        echo "$title<br>Part $part $ptTitle<br>Chapter $chapter $chTitle<br>$chText<br><br>";
        echo "<form action='index.php?page=$page' method='post'>";
        echo "Approve <input type='radio' name='writingApprove' value='1'> / Look at later <input type='radio' name='writingApprove' value='0' selected> / No and hide <input type='radio' name='writingApprove' value='2'> <input type='submit' value=' Update '>";
        echo "<input type='hidden' name='writingUp' value='$wId'>";
        echo "</form></div>";
    }
    echo "<tr><td style='border:1px solid $highlightColor;' colspan='4'><input type='hidden' name='qUpdate' value='1' /><input type='submit' value=' Update ' /></td></tr></table></form>";
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmt2 = $db->prepare(
            "SELECT COUNT(*) FROM reported WHERE reportedBy != ? && clearedTime = ?");
    $stmt2->execute(array(
            '0',
            '0'
    ));
    $row2 = $stmt2->fetch();
    $repArtCount2 = ($row2[0] >= '1') ? $row2[0] : '0';
    ?>
			<header onclick="toggleview('reportedArticles')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $repArtCount2;
    ?>) - Reported articles
			</header>
			<article id="reportedArticles" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    $stmt3 = $db->prepare(
            "SELECT id, articleId, whyReported FROM reported WHERE reportedBy != ? && clearedTime = ? ORDER BY reportedTime");
    $stmt3->execute(array(
            '0',
            '0'
    ));
    while ($row3 = $stmt3->fetch()) {
        $repId = $row3['id'];
        $repArtId = $row3['articleId'];
        $whyReported = $row3['whyReported'];

        $substmt4 = $db->prepare("SELECT * FROM articles WHERE id=?");
        $substmt4->execute(array(
                $repArtId
        ));
        $subrow4 = $substmt4->fetch();
        $artId = $subrow4['id'];
        $authorId = $subrow4['authorId'];
        $articleTitle = $subrow4['articleTitle'];
        $articleText = nl2br(
                make_links_clickable(
                        html_entity_decode($subrow4['articleText'], ENT_QUOTES)));
        $catId = $subrow4['catId'];
        $pic1Name = $subrow4['pic1Name'];
        $pic1Ext = $subrow4['pic1Ext'];
        $pic1Caption = $subrow4['pic1Caption'];
        $pic2Name = $subrow4['pic2Name'];
        $pic2Ext = $subrow4['pic2Ext'];
        $pic2Caption = $subrow4['pic2Caption'];
        $postedDate = $subrow4['postedDate'];
        $editedDate = $subrow4['editedDate'];
        $articleScope = $subrow4['articleScope'];
        $textLen = strlen($articleText);
        $offset1 = $textLen / 3;
        $offset2 = $offset1 * 2;
        $pos1 = strpos($articleText, "<br />", $offset1) + 6;
        $pos2 = strpos($articleText, "<br />", $offset2) + 6;

        $substmt5 = $db->prepare(
                "SELECT category FROM articleCategories WHERE id=?");
        $substmt5->execute(array(
                $catId
        ));
        $subrow5 = $substmt5->fetch();
        $catName = $subrow5['category'];

        $substmt6 = $db->prepare(
                "SELECT firstName, lastName FROM users WHERE id=?");
        $substmt6->execute(array(
                $authorId
        ));
        $subrow6 = $substmt6->fetch();
        $artFirstName = $subrow6['firstName'];
        $artLastName = $subrow6['lastName'];

        echo "<header style='font-weight:bold; font-size:1.5em; margin:20px 0px; background-color:#dddddd; border:1px solid #aaaaaa; cursor:pointer;' onclick='toggleview(\"art$artId\")'>$articleTitle</header>";
        echo "<article id='art$artId' style='display:none;'>";
        echo "<header style='text-align:center; margin:20px;'>$articleTitle</header>";
        echo "Posted date: " . date("M j, Y", $postedDate) . "<br />";
        echo "Last edit date: " . date("M j, Y", $editedDate) . "<br /><br />";
        echo "by $artFirstName $artLastName<br /><br />";
        echo "<article style='text-align:justify;'>";
        echo substr($articleText, 0, $pos1);
        if (file_exists("userPics/$myId/$pic1Name.$pic1Ext")) {
            echo "<div style='margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; width:390px; float:right;'><img src='userPics/$myId/$pic1Name.$pic1Ext' alt='' style='max-width:390px; max-height:390px;' /><figcaption style='text-align:center;'>$pic1Caption</figcaption></div>";
        }
        echo substr($articleText, $pos1 + 1, $pos2 - $pos1 - 1);
        if (file_exists("userPics/$myId/$pic2Name.$pic2Ext")) {
            echo "<div style='margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; width:390px; float:left;'><img src='userPics/$myId/$pic2Name.$pic2Ext' alt='' style='max-width:390px; max-height:390px;' /><figcaption style='text-align:center;'>$pic2Caption</figcaption></div>";
        }
        echo substr($articleText, $pos2 + 1);
        echo "</article>";
        ?>
					<form style="margin-top:30px;" action="index.php?page=Gadmin" method="post">
						Clear report:<br />
						Yes <input type="radio" name="approve" value='1' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No <input type="radio" name="approve" value='0' />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;No, and delete <input type="radio" name="approve" value='2' /><br /><br />
						Why reported, and msg to author about decision:<br />
						<textarea name='msgToAuthor' cols='90' rows='10'><?php

        echo $whyReported;
        ?></textarea><br /><br />
						<input type='hidden' name='reportArt' value='<?php

        echo $artId;
        ?>' /><input type='hidden' name='reportId' value='<?php

        echo $repId;
        ?>' /><input type='submit' value=' Go ' />
					</form>
				<?php
        echo "</article>";
    }
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmt28 = $db->prepare("SELECT COUNT(*) FROM calendar WHERE approved = '0'");
    $stmt28->execute();
    $row28 = $stmt28->fetch();
    $eCount = ($row28[0] >= '1') ? $row28[0] : '0';
    ?>
			<header onclick="toggleview('e')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $eCount;
    ?>) - New Events
			</header>
			<article id="e" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<table cellspacing='0' cellpadding='10'>";
    echo "<tr>";
    echo "<td style='border:1px solid $highlightColor;'>Event</td>";
    echo "<td style='border:1px solid $highlightColor;'>Approve</td>";
    echo "<td style='border:1px solid $highlightColor;'>Delete</td>";
    echo "<td style='border:1px solid $highlightColor;'>Submit</td>";
    echo "</tr>";
    $stmt29 = $db->prepare("SELECT * FROM calendar WHERE approved = '0'");
    $stmt29->execute();
    while ($row29 = $stmt29->fetch()) {
        $eId = $row29['id'];
        $title = html_entity_decode($row29['title'], ENT_QUOTES);
        $writeUp = html_entity_decode($row29['writeUp'], ENT_QUOTES);
        $startTime = $row29['startTime'];
        $startHour = date("G", $startTime);
        $startMinute = date("i", $startTime);
        $startMonth = date("n", $startTime);
        $startDay = date("j", $startTime);
        $startYear = date("Y", $startTime);
        $picture = $row29['picture'];
        $eUserId = $row29['userId'];

        echo "<tr>";
        echo "<td style='border:1px solid $highlightColor;'><form action='index.php?page=$page' method='post'><input type='text' name='title' value='$title' size='75' /><br />";
        if (file_exists("userPics/$eUserId/thumbs/$picture")) {
            echo "<img src='userPics/$eUserId/thumbs/$picture' alt='' /><br />";
        }
        ?>
					From:<br />
					<table cellspacing="0">
						<tr>
							<td>
								<div style="text-align:center;">h</div>
							</td>
							<td>
								<div style="text-align:center;">m</div>
							</td>
							<td>
								<div style="text-align:center;">M</div>
							</td>
							<td>
								<div style="text-align:center;">D</div>
							</td>
							<td>
								<div style="text-align:center;">Y</div>
							</td>
						</tr>
						<tr>
							<td>
								<select size="1" name="startHour">
									<?php
        for ($a = 0; $a <= 23; $a ++) {
            echo "<option value='$a'";
            if ($a == $startHour) {
                echo " selected";
            }
            echo ">$a</option>\n";
        }
        ?>
								</select>
							</td>
							<td>
								<select size="1" name="startMinute">
									<?php
        for ($b = 00; $b <= 45; $b = $b + 15) {
            echo "<option value='$b'";
            if ($b == $startMinute) {
                echo " selected";
            }
            echo ">$b</option>\n";
        }
        ?>
								</select>
							</td>
							<td>
								<select size="1" name="startMonth">
									<?php
        for ($c = 1; $c <= 12; $c ++) {
            echo "<option value='$c'";
            if ($c == $startMonth) {
                echo " selected";
            }
            echo ">$c</option>\n";
        }
        ?>
								</select>
							</td>
							<td>
								<select size="1" name="startDay">
									<?php
        for ($d = 1; $d <= 31; $d ++) {
            echo "<option value='$d'";
            if ($d == $startDay) {
                echo " selected";
            }
            echo ">$d</option>\n";
        }
        ?>
								</select>
							</td>
							<td>
								<select size="1" name="startYear">
									<?php
        $thisY = date("Y");
        echo "<option value='$thisY'";
        if ($thisY == $startYear) {
            echo " selected";
        }
        echo ">$thisY</option>\n";
        echo "<option value='" . ($thisY + 1) . "'";
        if ($thisY + 1 == $startYear) {
            echo " selected";
        }
        echo ">" . ($thisY + 1) . "</option>\n";
        ?>
								</select>
							</td>
						</tr>
					</table><br />
					<br />
					<textarea name="writeUp" cols="60" rows="10"><?php

        echo $writeUp;
        ?></textarea>
				<?php
        echo "</td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr' value='1' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr' value='0' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='hidden' name='eUpdate' value='$eId' /><input type='submit' value=' Update ' /></form></td>";
        echo "</tr>";
    }
    echo "</table>";
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmt5 = $db->prepare("SELECT COUNT(*) FROM feedback WHERE seen = '0'");
    $stmt5->execute();
    $row5 = $stmt5->fetch();
    $fbCount = ($row5[0] >= '1') ? $row5[0] : '0';
    ?>
			<header onclick="toggleview('fb')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $fbCount;
    ?>) - Feedback
			</header>
			<article id="fb" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<form action='index.php?page=$page' method='post'><table cellspacing='0' cellpadding='10'>";
    echo "<tr>";
    echo "<td style='border:1px solid $highlightColor;'>Delete</td>";
    echo "<td style='border:1px solid $highlightColor;'>Seen</td>";
    echo "<td style='border:1px solid $highlightColor;'>From</td>";
    echo "<td style='border:1px solid $highlightColor;'>Rating</td>";
    echo "<td style='border:1px solid $highlightColor;'>Text</td>";
    echo "</tr>";
    $stmt6 = $db->prepare("SELECT * FROM feedback ORDER BY fbTime");
    $stmt6->execute();
    while ($row6 = $stmt6->fetch()) {
        $fbId = $row6['id'];
        $fbUserId = $row6['userId'];
        $feedText = nl2br($row6['feedText']);
        $fbRating = $row6['rating'];
        $fbSeen = $row6['seen'];
        $fbTime = $row6['fbTime'];

        $stmt7 = $db->prepare(
                "SELECT firstName, lastName, email FROM users WHERE id=?");
        $stmt7->execute(array(
                $fbUserId
        ));
        $row7 = $stmt7->fetch();
        $fbFirstName = $row7['firstName'];
        $fbLastName = $row7['lastName'];
        $fbEmail = $row7['email'];

        echo "<tr>";
        echo "<td style='border:1px solid $highlightColor;'><input type='checkbox' name='del$fbId' value='1' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='checkbox' name='seen$fbId' value='1'";
        echo ($fbSeen == "1") ? " checked='checked' " : " ";
        echo "/></td>";
        echo "<td style='border:1px solid $highlightColor;'><a href='mailto:$fbEmail'>$fbLastName, $fbFirstName</a></td>";
        echo "<td style='border:1px solid $highlightColor;'>$fbRating</td>";
        echo "<td style='border:1px solid $highlightColor;'>$feedText</td>";
        echo "</tr>";
    }
    echo "<tr><td style='border:1px solid $highlightColor;' colspan='5'><input type='hidden' name='fbUpdate' value='1' /><input type='submit' value=' Update ' /></td></tr></table></form>";
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmt8 = $db->prepare("SELECT COUNT(*) FROM quotes WHERE approved = '0'");
    $stmt8->execute();
    $row8 = $stmt8->fetch();
    $qCount = ($row8[0] >= '1') ? $row8[0] : '0';
    ?>
			<header onclick="toggleview('q')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $qCount;
    ?>) - New Quotes
			</header>
			<article id="q" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<form action='index.php?page=$page' method='post'><table cellspacing='0' cellpadding='10'>";
    echo "<tr>";
    echo "<td style='border:1px solid $highlightColor;'>Quote</td>";
    echo "<td style='border:1px solid $highlightColor;'>Author</td>";
    echo "<td style='border:1px solid $highlightColor;'>Approve</td>";
    echo "<td style='border:1px solid $highlightColor;'>Delete</td>";
    echo "</tr>";
    $stmt9 = $db->prepare("SELECT * FROM quotes WHERE approved = '0'");
    $stmt9->execute();
    while ($row9 = $stmt9->fetch()) {
        $qId = $row9['id'];
        $quote = html_entity_decode($row9['quote'], ENT_QUOTES);
        $author = html_entity_decode($row9['author'], ENT_QUOTES);
        echo "<tr>";
        echo "<td style='border:1px solid $highlightColor;'><input type='text' name='quote$qId' value='$quote' size='75' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='text' name='author$qId' value='$author' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr$qId' value='1' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr$qId' value='0' /></td>";
        echo "</tr>";
    }
    echo "<tr><td style='border:1px solid $highlightColor;' colspan='4'><input type='hidden' name='qUpdate' value='1' /><input type='submit' value=' Update ' /></td></tr></table></form>";
    ?>
			</article>
		</section>
		<section>
			<?php
    $stmt18 = $db->prepare("SELECT COUNT(*) FROM factoids WHERE approved = '0'");
    $stmt18->execute();
    $row18 = $stmt18->fetch();
    $fCount = ($row18[0] >= '1') ? $row18[0] : '0';
    ?>
			<header onclick="toggleview('f')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				(<?php

    echo $fCount;
    ?>) - New Factoids
			</header>
			<article id="f" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<form action='index.php?page=$page' method='post'><table cellspacing='0' cellpadding='10'>";
    echo "<tr>";
    echo "<td style='border:1px solid $highlightColor;'>Factoid</td>";
    echo "<td style='border:1px solid $highlightColor;'>Approve</td>";
    echo "<td style='border:1px solid $highlightColor;'>Delete</td>";
    echo "</tr>";
    $stmt9 = $db->prepare("SELECT * FROM factoids WHERE approved = '0'");
    $stmt9->execute();
    while ($row9 = $stmt9->fetch()) {
        $fId = $row9['id'];
        $factoid = html_entity_decode($row9['factoid'], ENT_QUOTES);
        echo "<tr>";
        echo "<td style='border:1px solid $highlightColor;'><input type='text' name='factoid$fId' value='$factoid' size='75' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr$fId' value='1' /></td>";
        echo "<td style='border:1px solid $highlightColor;'><input type='radio' name='appr$fId' value='0' /></td>";
        echo "</tr>";
    }
    echo "<tr><td style='border:1px solid $highlightColor;' colspan='3'><input type='hidden' name='fUpdate' value='1' /><input type='submit' value=' Update ' /></td></tr></table></form>";
    ?>
			</article>
		</section>
		<section>
			<header onclick="toggleview('ads')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				Advertising
			</header>
			<?php
    $showAds = (filter_input(INPUT_GET, 'showAds', FILTER_SANITIZE_NUMBER_INT) ==
            '1') ? "block" : "none";
    ?>
			<article id="ads" style="display:<?php

    echo $showAds;
    ?>; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				<?php
    echo "<form action='index.php?page=Gadmin&showAds=1' method='post'>View advertising for:<br /><select name='getAdId' size='1'>\n";
    echo "<option value='0'>none</option>\n";
    $ads = $db->prepare(
            "SELECT id, firstName, lastName, email FROM users ORDER BY lastName");
    $ads->execute();
    while ($adsrow = $ads->fetch()) {
        $id = $adsrow['id'];
        $firstName = $adsrow['firstName'];
        $lastName = $adsrow['lastName'];
        $email = $adsrow['email'];
        echo "<option value='$id'";
        if (filter_input(INPUT_POST, 'getAdId', FILTER_SANITIZE_NUMBER_INT) ==
                $id) {
            echo " selected='selected'";
        }
        echo ">$lastName, $firstName - $email</option>\n";
    }
    echo "</select><input type='submit' value=' Get ' />\n";
    if (filter_input(INPUT_POST, 'getAdId', FILTER_SANITIZE_NUMBER_INT)) {
        $userId = filter_input(INPUT_POST, 'getAdId', FILTER_SANITIZE_NUMBER_INT);
        echo "<table style='border:1px solid $highlightColor; width:100%;' cellspacing='5px'>\n";
        echo "<form action='index.php?page=Gadmin' method='post' enctype='multipart/form-data'><tr><td style='border:1px solid $highlightColor; padding:5px;' colspan='3'>";
        echo "Upload a new graphic:<br />For the top ad use a max size of 1100px X 100px.<br />";
        echo "For the side ads use a max size of 200px X 300px.<br /><br />";
        echo "<input type='file' name='adImage' /></td></tr>\n";
        echo "<tr><td style='border:1px solid $highlightColor; padding:5px;'>Location<br /><br /><select name='slot' size='1' onchange='showAdPrices(this.value)'><option value='0'>Pick a spot</option><option value='top'>Top</option><option value='side1'>Side1</option><option value='side2'>Side2</option><option value='side3'>Side3</option></select><div id='showPrice'></div></td>\n";
        echo "<td style='border:1px solid $highlightColor; padding:5px;'>Link address<br /><br />Use web address:<br /><input type='radio' name='linkLocal' value='0' /> <input type='text' name='linkText' value='' /><br /><br />Or use business page on My Local Life<br /><input type='radio' name='linkLocal' value='0' /></td>\n";
        echo "<td style='border:1px solid $highlightColor; padding:5px;'>Did a sales rep help you with your ad? If so, who was it?<br /><select name='salesRepId' size='1'><option value='0'>none</option>";
        $sa = $db->prepare(
                "SELECT id, firstName, lastName FROM users WHERE accessLevel >= ? ORDER BY lastName");
        $sa->execute(array(
                '2'
        ));
        while ($sarow = $sa->fetch()) {
            echo "<option value='" . $sarow['id'] . "'>" . $sarow['lastName'] .
                    ", " . $sarow['firstName'] . "</option>";
        }
        echo "</select><br /><br /><input type='hidden' name='getAdvertising' value='new' /><input type='hidden' name='adUserId' value='$userId' /><input type='submit' value=' Submit ' /></td></tr></form></table>\n";
        echo "<div style='height:20px; width:100%; background-color:#cccccc;'></div>";

        $stmt = $db->prepare("SELECT * FROM advertising WHERE userId=?");
        $stmt->execute(array(
                $userId
        ));
        while ($row = $stmt->fetch()) {
            $Aid = $row['id'];
            $slot = $row['slot'];
            $activeUntil = $row['activeUntil'];
            $adName = $row['adName'];
            $adExt = $row['adExt'];
            $linkText = $row['linkText'];
            $linkLocal = ($row['linkLocal'] == '0') ? '0' : '1';
            $salesRepId = $row['salesRepId'];

            echo "<table style='border:1px solid $highlightColor; width:100%;' cellspacing='5px'>\n";
            echo "<form action='index.php?page=Gadmin' method='post' enctype='multipart/form-data'><tr><td style='border:1px solid $highlightColor; padding:5px;' colspan='4'>";
            if (file_exists("userPics/$userId/" . $adName . "." . $adExt)) {
                echo "<img src='userPics/$userId/" . $adName . "." . $adExt .
                        "' alt='' style='max-height:200px; max-width:500px;' /><br />";
            }
            echo "Upload a new graphic:<br />For the top ad use a max size of 1100px X 100px.<br />";
            echo "For the side ads use a max size of 200px X 300px.<br /><br />";
            echo "<input type='file' name='adImage' /></td></tr>\n";
            echo "<tr><td style='border:1px solid $highlightColor; padding:5px;'>Location<br /><br />$slot</td>";
            echo "<td style='border:1px solid $highlightColor; padding:5px;'>Ad visible on the site until:<br />" .
                    date("M j, Y", $activeUntil) .
                    "<br /><br />Purchase more time:<br />";
            switch ($slot) {
                case 'top':
                    include "includes/ppButtons/adsTop.php";
                    break;
                case 'side1':
                    include "includes/ppButtons/adsSide1.php";
                    break;
                case 'side2':
                    include "includes/ppButtons/adsSide2.php";
                    break;
                case 'side3':
                    include "includes/ppButtons/adsSide3.php";
                    break;
            }
            echo "</td>\n";
            echo "<td style='border:1px solid $highlightColor; padding:5px;'>Link address<br /><br />Use web address:<br /><input type='radio' name='linkLocal' value='0'";
            if ($linkLocal == '0') {
                echo " checked";
            }
            echo " /> <input type='text' name='linkText' value='$linkText' /><br /><br />Or use business page on My Local Life<br /><input type='radio' name='linkLocal' value='1'";
            if ($linkLocal == '1') {
                echo " checked";
            }
            echo " /></td>\n";
            echo "<td style='border:1px solid $highlightColor; padding:5px;'>Did a sales rep help you with your ad? If so, who was it?<br /><select name='salesRepId' size='1'><option value='0'>none</option>";
            $sa = $db->prepare(
                    "SELECT id, firstName, lastName FROM users WHERE accessLevel >= ? ORDER BY lastName");
            $sa->execute(array(
                    '2'
            ));
            while ($sarow = $sa->fetch()) {
                echo "<option value='" . $sarow['id'] . "'";
                if ($salesRepId == $sarow['id']) {
                    echo " selected='selected'";
                }
                echo ">" . $sarow['lastName'] . ", " . $sarow['firstName'] .
                        "</option>";
            }
            echo "</select><br /><br />Delete my ad: <input type='checkbox' name='delAd' value='1' /> This will delete your ad, and any credit you may have associated with it.<br /><br /><input type='hidden' name='getAdvertising' value='$Aid' /><input type='hidden' name='adUserId' value='$userId' /><input type='submit' value=' Submit ' /></td></tr></form></table>";
            echo "<div style='height:20px; width:100%; background-color:#cccccc;'></div>";
        }
    }
    ?>
			</article>
		</section>
		<section>
			<header onclick="toggleview('users')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				Clear Users
			</header>
			<article id="users" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				Clear all users who have not verified their email.<br>
				<form action='index.php?page=Gadmin' method='post'><input type="submit" value=" Clear "><input type="hidden" name="clearUsers" value="1"></form>
			</article>
		</section>
		<!-- <section>
			<header onclick="toggleview('slugs')" style="cursor:pointer; background-color:#dddddd; border:1px solid #aaaaaa; font-weight:bold; font-size:1.5em; padding:10px; margin:10px 0px;">
				Create Article Slugs
			</header>
			<article id="slugs" style="display:none; padding:10px; margin:10px 0px; border:1px solid #aaaaaa;">
				Initially create article slugs<br>
				<form action='index.php?page=Gadmin' method='post'><input type="submit" value=" Clear "><input type="hidden" name="createSlugs" value="1"></form>
			</article>
		</section> -->
	</main>
<?php
} else {
    echo "<div style='text-align:center' font-weight:bold; font-size:1.5em;>Please sign in above.</div>";
}
