<?php
$category = filter_input ( INPUT_GET, 'category', FILTER_SANITIZE_STRING );

if ($category) {
	$stmt = $db->prepare ( "SELECT id FROM classifiedCategories WHERE category=?" );
	$stmt->execute ( array (
			$category
	) );
	$row = $stmt->fetch ();
	$catId = $row ['id'];

	if ($myZip != 0) {
		list ( $getZipCodes1, $getZipCodes2, $getZipCodes3 ) = getZipAreas ( $myZip, $db );
		$x = 0;
		echo "<table id='mainTableBox' cellspacing='5'><tr>";
		for($i = 1; $i <= 3; $i ++) {
			${"where" . $i} = " WHERE t1.catId=? && t1.displayUntil >= ? ";
			$t = 0;
			foreach ( ${"getZipCodes" . $i} as $k1 => $v1 ) {
				if ($t == 0) {
					${"where" . $i} .= "&& (";
				}
				if ($t != 0) {
					${"where" . $i} .= " OR ";
				}
				${"where" . $i} .= "t2.zip=$v1";
				$t ++;
			}
			if (count ( ${"getZipCodes" . $i} ) >= 1) {
				${"where" . $i} .= ")";
			}
			$stmt6 = $db->prepare ( "SELECT t1.id, t1.classifiedTitle, t1.classifiedText FROM classifieds AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id${"where" . $i} ORDER BY t1.displayUntil DESC" );
			$stmt6->execute ( array (
					$catId,
					$time
			) );
			while ( $row6 = $stmt6->fetch () ) {
				$id = $row6 [0];
				displayClassified ( $id, $db, $highlightColor );
				$x ++;
				if ($x % $clsCols == 0) {
					echo "</tr><tr>";
				}
			}
		}
		if ($x % $clsCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $clsCols == 2) {
			echo "<td></td>";
		}
		echo "</tr></table>";
	} else {
		$x = 0;
		echo "<table id='mainTableBox' cellspacing='5'><tr>";
		$stmt18 = $db->prepare ( "SELECT id FROM classifieds WHERE catId = ? && displayUntil >= ? ORDER BY displayUntil DESC LIMIT 100" );
		$stmt18->execute ( array (
				$catId,
				$time
		) );
		while ( $row18 = $stmt18->fetch () ) {
			$id = $row18 [0];
			displayClassified ( $id, $db, $highlightColor );
			$x ++;
			if ($x % $clsCols == 0) {
				echo "</tr><tr>";
			}
		}
		if ($x % $clsCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $clsCols == 2) {
			echo "<td></td>";
		}
		echo "</tr></table>";
	}
	if ($category == "Help Wanted") {
		$x = 0;
		echo "<table id='mainTableBox' cellspacing='5'><tr>";
		$ccdcJobs = $db_ccdc->prepare ( "SELECT title, content FROM jobs ORDER BY RAND()" );
		$ccdcJobs->execute ();
		while ( $cJobs = $ccdcJobs->fetch () ) {
			$cTitle = $cJobs ['title'];
			$cContent = nl2br ( make_links_clickable ( html_entity_decode ( $cJobs ['content'], ENT_QUOTES ), $highlightColor ) );

			echo "<article class='pageBoxesHalf' style='float:left; overflow:hidden; margin:10px; padding:20px; font-size:0.75em; text-align:center; border:0px solid $highlightColor; width:370px; box-shadow: 5px 5px 5px grey;'>\n";
			echo "<div style='font-size:0.75em; text-align:center;'>" . $category . "</div><br />";
			echo "<header style='font-weight:bold; text-align:center; margin-bottom:10px;'>$cTitle</header>";
			echo "<article style='text-align:justify;'>$cContent</article></article>\n";
			$x ++;
			if ($x % $clsCols == 0) {
				echo "</tr><tr>";
			}
		}
		if ($x % $clsCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $clsCols == 2) {
			echo "<td></td>";
		}
		echo "</tr></table>";
	}
} else {
	echo "Please select a category of classified you wish to view from the menu.";
}