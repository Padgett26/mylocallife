<?php
$getAuthor = (filter_input ( INPUT_GET, 'author', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_GET, 'author', FILTER_SANITIZE_NUMBER_INT ) : 0;
$getBook = (filter_input ( INPUT_GET, 'book', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_GET, 'book', FILTER_SANITIZE_NUMBER_INT ) : 0;
$getPart = (filter_input ( INPUT_GET, 'part', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_GET, 'part', FILTER_SANITIZE_NUMBER_INT ) : 0;
$getChapter = (filter_input ( INPUT_GET, 'chapter', FILTER_SANITIZE_NUMBER_INT ) >= 1) ? filter_input ( INPUT_GET, 'chapter', FILTER_SANITIZE_NUMBER_INT ) : 0;

if ($getAuthor >= 1 && $getBook >= 1) {
	if ($getChapter == 0) {
		$firstCh = $db->prepare ( "SELECT chapter FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter LIMIT 1" );
		$firstCh->execute ( array (
				$getAuthor,
				$getBook,
				$getPart
		) );
		$firstChR = $firstCh->fetch ();
		$getChapter = $firstChR ['chapter'];
	}

	$get1 = $db->prepare ( "SELECT firstName, lastName FROM users WHERE id = ?" );
	$get1->execute ( array (
			$getAuthor
	) );
	$get1R = $get1->fetch ();
	$firstName = html_entity_decode ( $get1R ['firstName'], ENT_QUOTES );
	$lastName = html_entity_decode ( $get1R ['lastName'], ENT_QUOTES );

	$get2 = $db->prepare ( "SELECT title, showParts FROM myWritings WHERE authorId = ? AND bookId = ? LIMIT 1" );
	$get2->execute ( array (
			$getAuthor,
			$getBook
	) );
	$get2R = $get2->fetch ();
	$title = html_entity_decode ( $get2R ['title'], ENT_QUOTES );
	$showParts = $get2R ['showParts'];

	echo "<div style='font-weight:bold; text-align:center; font-size:1.5em; margin-top:30px;'>$title</div>";
	echo "<div style='font-weight:bold; text-align:center; margin-bottom:30px;'>by $firstName $lastName</div>";

	$countCh = $db->prepare ( "SELECT COUNT(*) FROM myWritings WHERE authorId = ? AND bookId = ?" );
	$countCh->execute ( array (
			$getAuthor,
			$getBook
	) );
	$cCh = $countCh->fetch ();
	$chCount = $cCh [0];

	if ($chCount >= 2) {
		$getParts = $db->prepare ( "SELECT DISTINCT part FROM myWritings WHERE authorId = ? AND bookId = ? ORDER BY part" );
		$getParts->execute ( array (
				$getAuthor,
				$getBook
		) );
		while ( $getPartsR = $getParts->fetch () ) {
			$menuPart = $getPartsR ['part'];
			echo ($showParts == 1) ? "<a href='index.php?page=Writings&author=$getAuthor&book=$getBook&part=$menuPart&chapter=0' style='font-size:1.25em;'>Part $menuPart</a><br>" : "";
			$getChapters = $db->prepare ( "SELECT chapter, chTitle FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter" );
			$getChapters->execute ( array (
					$getAuthor,
					$getBook,
					$menuPart
			) );
			while ( $getChaptersR = $getChapters->fetch () ) {
				$menuCh = $getChaptersR ['chapter'];
				$menuChTitle = html_entity_decode ( $getChaptersR ['chTitle'], ENT_QUOTES );
				echo ($menuChTitle != "" && $menuChTitle != " ") ? "<a href='index.php?page=Writings&author=$getAuthor&book=$getBook&part=$menuPart&chapter=$menuCh'>$menuChTitle</a><br>" : " <a href='index.php?page=Writings&author=$getAuthor&book=$getBook&part=$menuPart&chapter=$menuCh'>Chapter $menuCh</a> ";
			}
		}
	}
	$getText = $db->prepare ( "SELECT ptTitle, chTitle, chText FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? AND chapter = ?" );
	$getText->execute ( array (
			$getAuthor,
			$getBook,
			$getPart,
			$getChapter
	) );
	$getTextR = $getText->fetch ();
	$ptTitle = html_entity_decode ( $getTextR ['ptTitle'], ENT_QUOTES );
	$chTitle = html_entity_decode ( $getTextR ['chTitle'], ENT_QUOTES );
	$chText = nl2br ( html_entity_decode ( $getTextR ['chText'], ENT_QUOTES ) );

	if ($chCount >= 2) {
		if ($showParts == 1) {
			echo "<div style='color:#222222; text-align:center; font-size:1em; margin-top:30px;'>Part</div>";
			echo "<div style='color:#000000; text-align:center; font-size:1.25em;'>";
			echo ($ptTitle != "" && $ptTitle != " ") ? $ptTitle : $getPart;
			echo "</div>";
		}
		echo "<div style='color:#222222; text-align:center; font-size:1em; margin-top:10px;'>Chapter</div>";
		echo "<div style='color:#000000; text-align:center; font-size:1.25em;'>";
		echo ($chTitle != "" && $chTitle != " ") ? $chTitle : $getChapter;
		echo "</div>";
	}
	echo "<div style='color:#000000; text-align:left; font-size:1em; margin-top:30px;'>$chText</div>";
} else {
	echo "<table style='width:100%;'><tr><td style='width:50%; padding:10px;'>";
	echo "<div style='color:#000000; text-align:left; font-size:1.25em;'>Listed by author:</div>";
	$getAuthList = $db->prepare ( "SELECT DISTINCT t1.authorId FROM myWritings AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.approved = '1' ORDER BY t2.lastName" );
	$getAuthList->execute ();
	while ( $authList = $getAuthList->fetch () ) {
		$aId = $authList [0];
		$getAuthor = $db->prepare ( "SELECT firstName, lastName FROM users WHERE id = ?" );
		$getAuthor->execute ( array (
				$aId
		) );
		$authorR = $getAuthor->fetch ();
		$fName = $authorR ['firstName'];
		$lName = $authorR ['lastName'];
		echo "<div style='color:#000000; text-align:left; font-size:1em; font-weight:bold; cursor:pointer;' onclick='toggleview(\"auth$aId\")'>$fName $lName</div>";
		echo "<div style='color:#000000; text-align:left; font-size:1em; padding-left:10px; display:none;' id='auth$aId'>";
		$getBooks = $db->prepare ( "SELECT DISTINCT bookId FROM myWritings WHERE authorId = ? ORDER BY title" );
		$getBooks->execute ( array (
				$aId
		) );
		while ( $books = $getBooks->fetch () ) {
			$bId = $books ['bookId'];
			$getBookTitle = $db->prepare ( "SELECT title FROM myWritings WHERE authorId = ? AND bookId = ? LIMIT 1" );
			$getBookTitle->execute ( array (
					$aId,
					$bId
			) );
			$bookT = $getBookTitle->fetch ();
			$bTitle = $bookT ['title'];

			$getLowPart = $db->prepare ( "SELECT part FROM myWritings WHERE authorId = ? AND bookId = ? ORDER BY part DESC LIMIT 1" );
			$getLowPart->execute ( array (
					$aId,
					$bId
			) );
			$getLP = $getLowPart->fetch ();
			$lowP = $getLP ['part'];

			$getLowCh = $db->prepare ( "SELECT chapter FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter DESC LIMIT 1" );
			$getLowCh->execute ( array (
					$aId,
					$bId,
					$lowP
			) );
			$getLC = $getLowCh->fetch ();
			$lowC = $getLC ['chapter'];

			echo "<a href='index.php?page=Writings&author=$aId&book=$bId&part=$lowP&chapter=$lowC' style=''>$bTitle</a><br>";
		}
		echo "</div>";
	}
	echo "</td><td style='width:50%; padding:10px;'>";
	echo "<div style='color:#000000; text-align:left; font-size:1.25em;'>Listed by category:</div>";
	$getCatList = $db->prepare ( "SELECT DISTINCT category FROM myWritings WHERE approved = '1' ORDER BY RAND()" );
	$getCatList->execute ();
	while ( $catList = $getCatList->fetch () ) {
		$cId = $catList [0];
		$getCatName = $db->prepare ( "SELECT category FROM writingCategories WHERE id = ?" );
		$getCatName->execute ( array (
				$cId
		) );
		$catName = $getCatName->fetch ();
		$cName = $catName ['category'];
		echo "<div style='color:#000000; text-align:left; font-size:1em; font-weight:bold; cursor:pointer;' onclick='toggleview(\"cat$cId\")'>$cName</div>";
		echo "<div style='color:#000000; text-align:left; font-size:1em; padding-left:10px; display:none;' id='cat$cId'>";
		$bId = array ();
		$getA = $db->prepare ( "SELECT DISTINCT authorId FROM myWritings WHERE category = ?" );
		$getA->execute ( array (
				$cId
		) );
		while ( $getAR = $getA->fetch () ) {
			$a = $getAR ['authorId'];
			$getB = $db->prepare ( "SELECT DISTINCT bookId FROM myWritings WHERE authorId = ? AND category = ?" );
			$getB->execute ( array (
					$a,
					$cId
			) );
			while ( $getBR = $getB->fetch () ) {
				$b = $getBR ['bookId'];
				$getId = $db->prepare ( "SELECT id FROM myWritings WHERE authorId = ? AND bookId = ? LIMIT 1" );
				$getId->execute ( array (
						$a,
						$b
				) );
				$getI = $getId->fetch ();
				$bId [] = $getI ['id'];
			}
		}
		shuffle ( $bId );
		foreach ( $bId as $k => $v ) {
			$getInfo = $db->prepare ( "SELECT t1.authorId, t1.bookId, t1.title, t2.firstName, t2.lastName FROM myWritings AS t1 INNER JOIN users AS t2 ON t1.authorId = t2.id WHERE t1.id = ?" );
			$getInfo->execute ( array (
					$v
			) );
			$getI = $getInfo->fetch ();
			$authorid = $getI [0];
			$bookid = $getI [1];
			$title = $getI [2];
			$firstname = $getI [3];
			$lastname = $getI [4];

			$getLowPart = $db->prepare ( "SELECT part FROM myWritings WHERE authorId = ? AND bookId = ? ORDER BY part DESC LIMIT 1" );
			$getLowPart->execute ( array (
					$authorid,
					$bookid
			) );
			$getLP = $getLowPart->fetch ();
			$lowP = $getLP ['part'];

			$getLowCh = $db->prepare ( "SELECT chapter FROM myWritings WHERE authorId = ? AND bookId = ? AND part = ? ORDER BY chapter DESC LIMIT 1" );
			$getLowCh->execute ( array (
					$authorid,
					$bookid,
					$lowP
			) );
			$getLC = $getLowCh->fetch ();
			$lowC = $getLC ['chapter'];
			echo "<a href='index.php?page=Writings&author=$authorid&book=$bookid&part=$lowP&chapter=$lowC' style=''>$title -by $firstname $lastname</a><br>";
		}
		echo "</div>";
	}
	echo "</td></tr></table>";
}