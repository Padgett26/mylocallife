<?php
$usable = [
		"a",
		"b",
		"c",
		"d",
		"e",
		"f",
		"g",
		"h",
		"i",
		"j",
		"k",
		"l",
		"m",
		"n",
		"o",
		"p",
		"q",
		"r",
		"s",
		"t",
		"u",
		"v",
		"w",
		"x",
		"y",
		"z",
		" ",
		"1",
		"2",
		"3",
		"4",
		"5",
		"6",
		"7",
		"8",
		"9",
		"0"
];

if (filter_input ( INPUT_GET, 'search', FILTER_SANITIZE_STRING )) {
	$sear = str_split ( strtolower ( $_GET ['search'] ) );
	$s = array ();
	for($i = 0; $i < count ( $sear ); $i ++) {
		if (in_array ( $sear [$i], $usable )) {
			$s [] = $sear [$i];
		}
	}
	$sea = implode ( $s );
	echo "<header style='font-weight:bold; font-size:1.5em; text-align:left; margin:30px 10px;'>Closest search results for '$sea'</header>";
	$search = explode ( " ", $sea );

	// Find People
	echo "<table id='mainTableBox1' cellspacing='5' style='margin-top:30px;'>";
	$pGet = array ();
	foreach ( $search as $sp ) {
		$stmt1 = $db->prepare ( "SELECT id FROM directory WHERE showListing=? && (businessName LIKE '%$sp%' || firstName LIKE '%$sp%' || lastName LIKE '%$sp%')" );
		$stmt1->execute ( array (
				'1'
		) );
		while ( $row1 = $stmt1->fetch () ) {
			$pGet [] = $row1 [0];
		}
	}
	$pg = array_unique ( $pGet );
	if (count ( $pg ) >= 1) {
		$z = 0;
		echo "<tr>";
		foreach ( $pg as $p ) {
			displayDirectory ( $p, $db, $highlightColor );
			$z ++;
			if ($z % $directoryCols == 0) {
				echo "</tr><tr>\n";
			}
			if ($z == 41) {
				break;
			}
		}
		if ($z % $directoryCols == 1) {
			echo "<td></td><td></td><td></td>";
		} elseif ($z % $directoryCols == 2) {
			echo "<td></td><td></td>";
		} elseif ($z % $directoryCols == 3) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	// Find Businesses
	echo "<table id='mainTableBox' cellspacing='5' style='margin-top:30px;'>";
	$busiGet = array ();
	foreach ( $search as $sb ) {
		$stmt12 = $db->prepare ( "SELECT t1.id FROM busiListing AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE (t1.busiName LIKE '%$sb%' OR t2.firstName LIKE '%$sb%' OR t2.lastName LIKE '%$sb%') && t2.businessListing='1'" );
		$stmt12->execute ();
		while ( $row12 = $stmt12->fetch () ) {
			$busiGet [] = $row12 [0];
		}
	}
	$bg = array_unique ( $busiGet );
	if (count ( $bg ) >= 1) {
		$z = 0;
		echo "<tr>";
		foreach ( $bg as $b ) {
			displayBusiness ( $b, $db, $highlightColor );
			$z ++;
			if ($z % $tableCols == 0) {
				echo "</tr><tr>\n";
			}
			if ($z == 40) {
				break;
			}
		}
		if ($z % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($z % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	// Find Writings
	echo "<table id='mainTableBox3' cellspacing='5' style='margin-top:30px;'>";
	$wGet = array ();
	foreach ( $search as $sa ) {
		$stmt19 = $db->prepare ( "SELECT t1.id FROM myWritings AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.chText LIKE '%$sa%' OR t1.title LIKE '%$sa%' OR t2.firstName LIKE '%$sa%' OR t2.lastName LIKE '%$sa%'" );
		$stmt19->execute ();
		while ( $row19 = $stmt19->fetch () ) {
			$wGet [] = $row19 [0];
		}
	}
	$wg = array_unique ( $wGet );
	if (count ( $wg ) >= 1) {
		$x = 0;
		echo "<tr>";
		foreach ( $wg as $agid ) {
			$get = $db->prepare ( "SELECT bookId, authorId FROM myWritings WHERE id = ?" );
			$get->execute ( array (
					$agid
			) );
			$getR = $get->fetch ();
			$bookId = $getR ['bookId'];
			$authorId = $getR ['authorId'];
			displayWriting ( $bookId, $authorId, $db, $highlightColor );
			$x ++;
			if ($x % $tableCols == 0) {
				echo "</tr><tr>";
			}
			if ($x == 40) {
				break;
			}
		}
		if ($x % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	// Find articles
	echo "<table id='mainTableBox2' cellspacing='5' style='margin-top:30px;'>";
	$aGet = array ();
	foreach ( $search as $sa ) {
		$stmt18 = $db->prepare ( "SELECT t1.id FROM articles AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.articleText LIKE '%$sa%' OR t1.articleTitle LIKE '%$sa%' OR t2.firstName LIKE '%$sa%' OR t2.lastName LIKE '%$sa%' ORDER BY postedDate DESC" );
		$stmt18->execute ();
		while ( $row18 = $stmt18->fetch () ) {
			if (! isArticleReported ( $row18 [0], $db )) {
				$aGet [] = $row18 [0];
			}
		}
	}
	$ag = array_unique ( $aGet );
	if (count ( $ag ) >= 1) {
		$x = 0;
		echo "<tr>";
		foreach ( $ag as $agid ) {
			displayArticle ( $agid, $db, $highlightColor );
			$x ++;
			if ($x % $tableCols == 0) {
				echo "</tr><tr>";
			}
			if ($x == 40) {
				break;
			}
		}
		if ($x % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";

	// Find photoShow
	echo "<table id='mainTableBox2' cellspacing='5' style='margin-top:30px;'>";
	$pGet = array ();
	foreach ( $search as $sa ) {
		$stmt18 = $db->prepare ( "SELECT t1.id FROM photoJournalism AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.photoText LIKE '%$sa%' OR t1.photoTitle LIKE '%$sa%' OR t2.firstName LIKE '%$sa%' OR t2.lastName LIKE '%$sa%' ORDER BY postedDate DESC" );
		$stmt18->execute ();
		while ( $row18 = $stmt18->fetch () ) {
			$pGet [] = $row18 [0];
		}
	}
	$pg = array_unique ( $pGet );
	if (count ( $pg ) >= 1) {
		$x = 0;
		echo "<tr>";
		foreach ( $pg as $pgid ) {
			displayPhoto ( $pgid, $db, $highlightColor );
			$x ++;
			if ($x % $tableCols == 0) {
				echo "</tr><tr>";
			}
			if ($x == 40) {
				break;
			}
		}
		if ($x % $tableCols == 1) {
			echo "<td></td><td></td>";
		} elseif ($x % $tableCols == 2) {
			echo "<td></td>";
		}
		echo "</tr>";
	}
	echo "</table>";
}
