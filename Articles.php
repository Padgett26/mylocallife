<?php
$category = (isset($_GET["category"])) ? htmlspecialchars(trim($_GET["category"]), ENT_QUOTES) : False;

$artId = (filter_input(INPUT_GET, 'artId', FILTER_SANITIZE_NUMBER_INT) >= 1) ? filter_input(INPUT_GET, 'artId', FILTER_SANITIZE_NUMBER_INT) : False;
$stmt = $db->prepare("SELECT COUNT(*),id FROM articles WHERE id = ?");
$stmt->execute(array(
		$artId
));
$row = $stmt->fetch();
$artId = ($row[0] == 1) ? $artId : FALSE;

// $stmt = $db->prepare("SELECT COUNT(*),id FROM articles WHERE slug = ?");
// $stmt->execute(array(
// $slug
// ));
// $row = $stmt->fetch();
// $artId = ($row[0] == 1) ? $row[1] : FALSE;

if ($artId) {
	echo "<div id='mainTableBox' style='padding:10px;'>";
	if (filter_input(INPUT_GET, 'reportId', FILTER_SANITIZE_NUMBER_INT)) {
		if ($myId != '0') {
			$reportId = filter_input(INPUT_POST, 'reportId', FILTER_SANITIZE_NUMBER_INT);
			$whyReported = htmlspecialchars(trim($_POST["whyReported"]));
			$repUp = $db->prepare("INSERT INTO reported VALUES" . "(NULL,?,?,?,?,?,?,'0','0')");
			$repUp->execute(array(
					$myId,
					$artId,
					'0',
					$time,
					'0',
					$whyReported
			));
			echo "<div style='text-align:center; font-weight:bold; font-size:1.5em;'>The article has been reported and will be reviewed by the administrators of this site.  Thank you for your input.</div>";
		}
	} elseif (filter_input(INPUT_GET, 'reportArticle', FILTER_SANITIZE_NUMBER_INT)) {
		$repArt = filter_input(INPUT_GET, 'reportArticle', FILTER_SANITIZE_NUMBER_INT);
		if ($myId != '0' && $repArt == $artId) {
			echo "<div style='text-align:center; font-weight:bold;'>You are about to report the article entitled";
			$stmtrep = $db->prepare("SELECT articleTitle FROM articles WHERE id=?");
			$stmtrep->execute(array(
					$artId
			));
			$rowrep = $stmtrep->fetch();
			$artTitle = $rowrep['articleTitle'];
			echo " '$artTitle'. If you are sure you want to report this article, please let me know why in the field below, and hit the 'report' button.<br />If you hit this link by mistake, or if you have changed your mind, please hit the 'Back' button.<br /><br /><br />";
			echo "<form action='index.php?page=Articles&articleDetail=$artId' method='post'><input type='submit' value='Back' /></form><br /><br /><br />";
			echo "<form action='index.php?page=Articles&articleDetail=$artId' method='post'><textarea name='whyReported' cols='40' rows='10'></textarea><br /><input type='hidden' name='reportId' value='$artId' /><input type='submit' value='Report' /></form><br /><br /><br />";
		}
	} else {

		$repstmt = $db->prepare("SELECT COUNT(*) FROM reported WHERE articleId=? && reportedTime >= 1 && clearedTime = 0");
		$repstmt->execute(array(
				$artId
		));
		$reprow = $repstmt->fetch();
		$reported = $reprow[0];
		if ($reported >= '1') {
			echo "This article is hidden because it has not yet been approved, or has been reported.  If it is approved by an admin the article will again be visible, if it is not approved, it will be deleted.";
		} else {
			$ckLog = $db->prepare("SELECT COUNT(*) FROM articleLog WHERE articleId=? && visitingIP=?");
			$ckLog->execute(array(
					$artId,
					$visitingIP
			));
			$ckLogRow = $ckLog->fetch();
			if ($ckLogRow[0] == 0) {
				$logstmt = $db->prepare("INSERT INTO articleLog VALUES" . "(NULL,?,?,?,'0','0')");
				$logstmt->execute(array(
						$artId,
						$time,
						$visitingIP
				));
				$logstmt2 = $db->prepare("UPDATE articles SET totalViews = totalViews + 1 WHERE id = ?");
				$logstmt2->execute(array(
						$artId
				));
			}
			$stmt = $db->prepare("SELECT * FROM articles WHERE id=?");
			$stmt->execute(array(
					$artId
			));
			$row = $stmt->fetch();
			$articleTitle = $row['articleTitle'];
			$articleText = nl2br(make_links_clickable(html_entity_decode($row['articleText'], ENT_QUOTES)));
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
			$totalViews = $row['totalViews'];
			$embedCode1 = html_entity_decode($row['embedCode1'], ENT_QUOTES);
			if ($embedCode1 != "" && $embedCode1 != " ") {
				$articleText = str_replace("<*embedCode1*>", $embedCode1, $articleText);
			} else {
				$articleText = str_replace("<*embedCode1*>", "", $articleText);
			}
			$substmt = $db->prepare("SELECT firstName, lastName FROM users WHERE id=?");
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
				$gr = $db->prepare("SELECT t1.articleTitle, t1.postedDate, t2.firstName, t2.lastName FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.id=?");
				$gr->execute(array(
						$inReplyTo
				));
				$grow = $gr->fetch();
				$artTitle = $grow['articleTitle'];
				$artPosted = $grow['postedDate'];
				$artFN = $grow['firstName'];
				$artLN = $grow['lastName'];
				echo "<div style='margin:20px 0px;'><a href='index.php?page=Articles&articleDetail=$inReplyTo'>This article is a reply to:<br />- $artTitle<br />\t<span style='font-size:.75em;'>" . date("M j, Y", $artPosted) . "\t by $artFN $artLN</span></a></div>";
			}
			echo "<div id='printArea' class='clearfix'>";
			echo "<header style='text-align:center; margin:20px; font-weight:bold; font-size:1.5em;'>$articleTitle</header>\n";
			?>
			<div style='float:right; margin:3px 10px 0px 0px;' class="fb-share-button" data-href="https://mylocal.life/index.php?page=Articles&articleDetail=<?php

			echo $artId;
			?>" data-layout="icon_link" data-size="small">
				<a target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=https%3A%2F%2Fmylocal.life%2Findex.php%3Fpage%3DArticles%26articleDetail%3D<?php

			echo $artId;
			?>&amp;src=sdkpreparse" class="fb-xfbml-parse-ignore">Facebook</a>
			</div>
			<br><br>
<?php
			echo "<button onclick='print()' style='float:right; margin:0px 5px;'>Print</button>";
			echo "Posted date: " . date("M j, Y", $postedDate) . "<br />";
			if ($editedDate != '0' && date("M j, Y", $postedDate) != date("M j, Y", $editedDate)) {
				echo "Edited<br />";
			} else {
				echo "<br />";
			}
			echo "by: $firstName $lastName<br>";
			if ($myId != '0') {
				$repstmt2 = $db->prepare("SELECT COUNT(*) FROM reported WHERE articleId=? && reportedBy=?");
				$repstmt2->execute(array(
						$artId,
						$myId
				));
				$reprow2 = $repstmt2->fetch();
				$reported = $reprow2[0];
				if ($reported == '0') {
					echo "<div style='float:right; font-size:0.75em;'><a href='index.php?page=Articles&articleDetail=$artId&reportArticle=$artId'>Report this article.</a></div><br>";
				}
			}
			echo "<div style='float:right; margin:3px 10px 0px 0px;'>$totalViews Views</div>";
			echo "<article style='text-align:justify; min-height:500px; width:100%; margin-top:40px;'>";
			if ($youtube != '0') {
				echo "<iframe width='560px' height='315px' src='https://www.youtube.com/embed/$youtube' frameborder='0' allowfullscreen></iframe><br /><br />";
			}
			if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext") || file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
				if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
					list ($width, $height) = (getimagesize("userPics/$authorId/$pic2Name.$pic2Ext") != null) ? getimagesize("userPics/$authorId/$pic2Name.$pic2Ext") : null;
					$Img2width = ($height > $width) ? "50%" : "93%";
				}
				if ($textLen >= 1000) {
					echo substr($articleText, 0, $pos1);
					if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext")) {
						echo "<a href='userPics/$authorId/$pic1Name.$pic1Ext' data-lightbox='images' data-title='$pic1Caption'><figure style='width:50%; margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; float:right;'><img id='articleImage1' src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:100%;' /><figcaption>$pic1Caption</figcaption></figure></a>";
					}
					if ($pos2 > $pos1) {
						echo substr($articleText, $pos1 + 1, $pos2 - $pos1 - 1);
						if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
							echo "<a href='userPics/$authorId/$pic2Name.$pic2Ext' data-lightbox='images' data-title='$pic2Caption'><figure style='width:50%; margin:10px 10px 10px 0px; border:1px solid #aaaaaa; padding:10px; float:left;'><img id='articleImage2' src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:100%;' /><figcaption>$pic2Caption</figcaption></figure></a>";
						}
						echo substr($articleText, $pos2 + 1);
					} else {
						echo substr($articleText, $pos1 + 1);
						if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
							echo "<a href='userPics/$authorId/$pic2Name.$pic2Ext' data-lightbox='images' data-title='$pic2Caption'><figure style='width:$Img2width; margin:10px auto; border:1px solid #aaaaaa; padding:10px; float:right;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:$Img2width;' /><figcaption>$pic2Caption</figcaption></figure></a>";
						}
					}
				} else {
					if (file_exists("userPics/$authorId/$pic1Name.$pic1Ext")) {
						echo "<a href='userPics/$authorId/$pic1Name.$pic1Ext' data-lightbox='images' data-title='$pic1Caption'><figure style='width:50%; margin:10px 0px 10px 10px; border:1px solid #aaaaaa; padding:10px; float:right;'><img id='articleImage1' src='userPics/$authorId/$pic1Name.$pic1Ext' alt='' style='width:100%;' /><figcaption>$pic1Caption</figcaption></figure></a>";
					}
					echo $articleText;
					if (file_exists("userPics/$authorId/$pic2Name.$pic2Ext")) {
						echo "<a href='userPics/$authorId/$pic2Name.$pic2Ext' data-lightbox='images' data-title='$pic2Caption'><figure style='width:$Img2width; margin: 10px auto; border:1px solid #aaaaaa; padding:10px; float:left;'><img src='userPics/$authorId/$pic2Name.$pic2Ext' alt='' style='width:$Img2width;' /><figcaption>$pic2Caption</figcaption></figure></a>";
					}
				}
			} else {
				echo $articleText;
			}
			if (file_exists("userPics/$authorId/$pdf1.pdf") || file_exists("userPics/$authorId/$pdf2.pdf")) {
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

			echo "<div style='margin-top:100px;'>";
			if ($myId != '0') {
				echo "<a href='index.php?page=editArticle&artId=new&inReplyTo=$artId' style='text-decoration:underline;'>Write a reply to this article.</a>";
			} else {
				echo "<a href='#top' style='text-decoration:underline;'>Sign in to reply to this article.</a>";
			}
			$rcount = $db->prepare("SELECT COUNT(*) FROM articles WHERE inReplyTo=?");
			$rcount->execute(array(
					$artId
			));
			$rcountrow = $rcount->fetch();
			if ($rcountrow[0] >= 1) {
				echo "<br /><br /><br /><br /><span style='font-weight:bold;'>Replies to this article.</span><br />";
				getReplies($artId, $db);
			}
			echo "</div>";
			echo "</article>";
			echo "</div>";
		}
	}
	echo "</div>\n";
	include "includes/readMore.php";
} elseif ($category) {
	$stmt = $db->prepare("SELECT id FROM articleCategories WHERE category=?");
	$stmt->execute(array(
			$category
	));
	$row = $stmt->fetch();
	$catId = $row['id'];

	echo "<div style='text-align:center; width:100%; margin-bottom:20px;'><span style='font-weight:bold; color:$highlightColor; font-size:1.5em;'>$category</span><div style='background-color:#dddddd; border:1px solid $highlightColor; border-radius:4px; height:5px; width:60%; margin:10px auto;'></div></div>";

	if ($myZip != 0) {
		list ($getZipCodes1, $getZipCodes2, $getZipCodes3) = getZipAreas($myZip, $db);

		echo "<table id='mainTableBox' cellspacing='5' cellpadding='0' style=''><tr>";
		$x = 3;
		for ($i = 1; $i <= 3; $i ++) {
			${"where" . $i} = " WHERE ";
			$t = 0;
			foreach (${"getZipCodes" . $i} as $k1 => $v1) {
				if ($t == 0) {
					${"where" . $i} .= "(";
				}
				if ($t != 0) {
					${"where" . $i} .= " OR ";
				}
				${"where" . $i} .= "t2.zip=$v1";
				$t ++;
			}
			if (count(${"getZipCodes" . $i}) >= 1) {
				${"where" . $i} .= ")";
			}

			$stmt6 = $db->prepare("SELECT t1.id FROM articles AS t1 INNER JOIN users AS t2 INNER JOIN reported AS t3 ON t1.authorId = t2.id && t1.id = t3.articleId${"where" .$i} && t3.reportedTime >= '1' && t3.clearedTime >= '1' && t1.catId = ? && t1.postedDate <= '$time' ORDER BY t1.postedDate DESC LIMIT 12");
			$stmt6->execute(array(
					$catId
			));
			while ($row6 = $stmt6->fetch()) {
				$id = $row6[0];
				displayArticle($id, $db, $highlightColor);
				$x ++;
				if ($x % $tableCols == 0) {
					echo "</tr><tr>";
				}
			}
		}
		if ($x % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr></table>";
	} else {
		$x = 3;
		echo "<table id='mainTableBox' cellspacing='5'><tr>";
		$stmt18 = $db->prepare("SELECT t1.id FROM articles AS t1 INNER JOIN reported AS t3 ON t1.id = t3.articleId WHERE t3.reportedTime >= '1' && t3.clearedTime >= '1' && t1.catId = ? && t1.postedDate <= '$time' ORDER BY t1.postedDate DESC LIMIT 36");
		$stmt18->execute(array(
				$catId
		));
		while ($row18 = $stmt18->fetch()) {
			$id = $row18[0];
			displayArticle($id, $db, $highlightColor);
			$x ++;
			if ($x % $tableCols == 0) {
				echo "</tr><tr>";
			}
		}
		if ($x % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr></table>";
	}
} else {
	echo "Your article was not found. Please make another selection from the menu above.";
}
