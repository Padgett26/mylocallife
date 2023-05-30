<?php
session_start ();

$dbhost = 'localhost';
$dbname = 'mll_db';
$dbuser = 'mll_user';
$dbpass = 'mLl_pWd';

try {
	$db = new PDO ( "mysql:host=$dbhost; dbname=$dbname", "$dbuser", "$dbpass" );
} catch ( PDOException $e ) {
	echo "";
}

$userId = (filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT )) ? filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT ) : '0';

$time = time ();

$alpha = array (
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
		"z"
);

if (filter_input ( INPUT_GET, 'get', FILTER_SANITIZE_STRING ) == "reset") { // Reseting the current puzzle
	for($i = 0; $i < count ( $_SESSION ['quote'] ); $i ++) {
		if (in_array ( strtolower ( $_SESSION ['quote'] [$i] ), $alpha )) {
			$_SESSION ['answer'] [$i] = "";
		} else {
			$_SESSION ['answer'] [$i] = $_SESSION ['quote'] [$i];
		}
	}
} elseif (filter_input ( INPUT_GET, 'get', FILTER_SANITIZE_STRING ) == "new") { // Creating a new puzzle
	if ($userId != '0') {
		$stmt1 = $db->prepare ( "SELECT cipherId FROM gameLog WHERE userId = ?" );
		$stmt1->execute ( array (
				$userId
		) );
		$row1 = $stmt1->fetch ();
		if ($row1) {
			$lastCipherId = $row1 ['cipherId'];
			$stmt2 = $db->prepare ( "SELECT COUNT(*) FROM quotes WHERE id > ?" );
			$stmt2->execute ( array (
					$lastCipherId
			) );
			$row2 = $stmt2->fetch ();
			$c = ($row2) ? $row2 [0] : 0;
			if ($c == 0) {
				$stmt3 = $db->prepare ( "UPDATE gameLog SET cipherId = '0' WHERE userId = ?" );
				$stmt3->execute ( array (
						$userId
				) );
			}
		}
		$stmt4 = $db->prepare ( "SELECT cipherId FROM gameLog WHERE userId = ?" );
		$stmt4->execute ( array (
				$userId
		) );
		$row4 = $stmt4->fetch ();
		if ($row4) {
			$lastCipherId = $row4 ['cipherId'];
			$stmt5 = $db->prepare ( "SELECT id FROM quotes WHERE id > ? ORDER BY id LIMIT 1" );
			$stmt5->execute ( array (
					$lastCipherId
			) );
			$row5 = $stmt5->fetch ();
			if ($row5) {
				$getId = $row5 ['id'];
				$stmt6 = $db->prepare ( "UPDATE gameLog SET cipherId = ? WHERE userId = ?" );
				$stmt6->execute ( array (
						$getId,
						$userId
				) );
			}
		}
	} else {
		$stmt = $db->prepare ( "SELECT id FROM quotes ORDER BY RAND() LIMIT 1" );
		$stmt->execute ();
		$row = $stmt->fetch ();
		$getId = $row ['id'];
	}
	$stmt = $db->prepare ( "SELECT quote,author FROM quotes WHERE id = ?" );
	$stmt->execute ( array (
			$getId
	) );
	$row = $stmt->fetch ();
	$qu = html_entity_decode ( $row ['quote'], ENT_QUOTES );
	$au = html_entity_decode ( $row ['author'], ENT_QUOTES );
	$q = $qu . " -" . $au;
	$_SESSION ['startTime'] = time ();
	$_SESSION ['quote'] = array (); // the clear text quote
	$_SESSION ['key'] = array (); // the mixed up alpha array, to be used as key
	$_SESSION ['answer'] = array (); // the buildable array of the users answers
	$_SESSION ['hints'] = 0;

	$key = array ();
	for($i = 0; $i < count ( $alpha ); $i ++) {
		$key [] = $alpha [$i];
	}
	shuffle ( $key );
	for($i = 0; $i < count ( $key ); $i ++) {
		$_SESSION ['key'] [$i] = $key [$i];
	}

	$quote = str_split ( $q );
	for($i = 0; $i < count ( $quote ); $i ++) {
		$_SESSION ['quote'] [$i] = $quote [$i];
	}

	for($i = 0; $i < count ( $quote ); $i ++) {
		if (in_array ( strtolower ( $quote [$i] ), $alpha )) {
			$_SESSION ['answer'] [$i] = "";
		} else {
			$_SESSION ['answer'] [$i] = $quote [$i];
		}
	}
}

// Set up the array variables
$answer = array ();
for($i = 0; $i < count ( $_SESSION ['answer'] ); $i ++) {
	$answer [$i] = $_SESSION ['answer'] [$i];
}
$quote = array ();
for($i = 0; $i < count ( $_SESSION ['quote'] ); $i ++) {
	$quote [$i] = $_SESSION ['quote'] [$i];
}
$key = array ();
for($i = 0; $i < count ( $_SESSION ['key'] ); $i ++) {
	$key [$i] = $_SESSION ['key'] [$i];
}

// Primary inputs
$char = strtolower ( filter_input ( INPUT_GET, 'char', FILTER_SANITIZE_STRING ) );
$pos = filter_input ( INPUT_GET, 'pos', FILTER_SANITIZE_NUMBER_INT );

// Erasing anything that might be in the way of the new char
if ($pos >= 0) {
	$a = strtolower ( $quote [$pos] );
	for($i = 0; $i < count ( $quote ); $i ++) {
		if ($a == strtolower ( $quote [$i] )) {
			$answer [$i] = "";
		}
	}
	if (in_array ( $char, $alpha )) {
		for($i = 0; $i < count ( $answer ); $i ++) {
			if ($char == strtolower ( $answer [$i] )) {
				$answer [$i] = "";
			}
		}
	}
}

// Adding a new char to your answer
if (in_array ( $char, $alpha )) {
	$a = strtolower ( $quote [$pos] );
	for($i = 0; $i < count ( $quote ); $i ++) {
		if ($a == strtolower ( $quote [$i] )) {
			$answer [$i] = (strcmp ( $a, $quote [$i] ) == 0) ? $char : strtoupper ( $char );
		}
	}
}

// Get a free hint
if (filter_input ( INPUT_GET, 'hint', FILTER_SANITIZE_STRING ) == "freebie") {
	$tempArray = array ();
	for($j = 0; $j < count ( $quote ); $j ++) {
		if (strtolower ( $answer [$j] ) != strtolower ( $quote [$j] )) {
			$tempArray [] = $j;
		}
	}
	$rand = rand ( 0, (count ( $tempArray ) - 1) );
	$b = $tempArray [$rand];
	$a = strtolower ( $quote [$b] );
	for($i = 0; $i < count ( $answer ); $i ++) {
		$answer [$i] = (strtolower ( $answer [$i] ) == $a) ? "" : $answer [$i];
	}
	for($i = 0; $i < count ( $quote ); $i ++) {
		if ($a == strtolower ( $quote [$i] )) {
			$answer [$i] = $quote [$i];
		}
	}
	$_SESSION ['hints'] ++;
}

// Saving the changes made to the answer
for($i = 0; $i < count ( $answer ); $i ++) {
	$_SESSION ['answer'] [$i] = $answer [$i];
}

// Checking to see if you have won the puzzle
$winner = 1;
for($i = 0; $i < count ( $quote ); $i ++) {
	if ($answer [$i] != $quote [$i]) {
		$winner = 0;
		break;
	}
}

// If you have won, display the congratz text
if ($winner == 1) {
	$tot = ($time - $_SESSION ['startTime']);
	$hours = ( int ) ($tot / 3600);
	$mins = ( int ) (($tot - ($hours * 3600)) / 60);
	$secs = ( int ) (($tot - ($hours * 3600)) % 60);
	echo "You won!!!<br />And it only took ";
	$h = 0;
	$m = 0;
	if ($hours != 0) {
		echo "$hours hours";
		$h = 1;
	}
	if ($mins != 0) {
		if ($h == 1) {
			echo ", ";
		}
		echo "$mins mins";
		$m = 1;
	}
	if ($secs != 0) {
		if (($h == 1 && $m == 0) || ($h == 0 && $m == 1)) {
			echo ", ";
		}
		echo "$secs secs";
	}
	echo "<br />With " . $_SESSION ['hints'] . " hints.<br /><button type='button' onclick='puzzleStart(\"new\",\"$userId\")'> Get new cipher </button><br /><br /><br />";
	echo "<span style='font-weight:bold;'>";
	$text = implode ( $quote );
	$newtext = wordwrap ( $text, 130, "<br />\n" );
	echo "$newtext</span><br /><br />";
	if ($userId != '0') {
		$als = $db->prepare ( "SELECT accessLevel FROM users WHERE id = ?" );
		$als->execute ( array (
				$userId
		) );
		$alr = $als->fetch ();
		if ($alr) {
			$al = $alr ['accessLevel'];
			if ($al >= 1) {
				echo "<div style='margin:20px;'>";
				echo "<span style='font-weight:bold;'>Do you have a favorite quote you would like to see as a cipher puzzle on this page? Enter it here.</span><br />";
				echo "<div style='border:2px solid $highlightColor; padding:10px;'><form action='index.php?page=Games&play=cipher' method='post'>";
				echo "Author:<br /><input type='text' name='author' value='' size='70' /><br /><br />";
				echo "Quote:<br /><input type='text' name='quote' value='' size='70' /><br /><br />";
				echo "<input type='hidden' name='newQ' value='$userId' /><input type='submit' value=' Upload New Quote ' /></form>";
				echo "</div></div>";
			}
		}
	}
} else { // If haven't won yet, display the playboard with your update
	for($i = 0; $i < count ( $alpha ); $i ++) {
		$bgc = (in_array ( $alpha [$i], $answer ) || in_array ( strtoupper ( $alpha [$i] ), $answer )) ? "#cccccc" : "#ffffff";
		echo "<div style='float:left; background-color:$bgc; color:#000000; font-weight:bold; font-size:1.25em; text-align:center; padding:5px;'> $alpha[$i] </div>";
	}
	echo "<br /><br /><br /><br /><table cellpadding='0' cellspacing='2'>";
	echo "<tr>";
	$t = 0;

	$x = 0;
	$breaks = array ();
	$b = array ();
	foreach ( $quote as $k => $v ) {
		if ($v == " ") {
			$b [] = $k;
		}
	}
	for($j = 0; $j < count ( $b ); $j ++) {
		$k = $j + 1;
		$spaces = 15;
		$r = $b [$j] - $x;
		$s = $b [$k] - $x;
		if ($r % $spaces > $s % $spaces) {
			$breaks [] = $b [$j];
			$x = $b [$j];
		}
	}

	for($i = 0; $i < count ( $quote ); $i ++) {
		if (in_array ( strtolower ( $quote [$i] ), $alpha )) {
			for($j = 0; $j < count ( $alpha ); $j ++) {
				if (strtolower ( $quote [$i] ) == $alpha [$j]) {
					echo "<td style='text-align:center;'><input type='text' size='1' maxlength='1' style='text-align:center;' value='$answer[$i]' onkeyup='charSelect(this.value, \"p$i\", \"$userId\")' /><br />";
					echo (strcmp ( strtolower ( $quote [$i] ), $quote [$i] ) == 0) ? $key [$j] : strtoupper ( $key [$j] );
					echo "<br /><br /></td>";
					break;
				}
			}
		} else {
			$ci = ($quote [$i] == " ") ? "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" : $quote [$i];
			echo "<td style='text-align:center;'>$ci<br />$ci<br /><br /></td>";
		}
		if (in_array ( $i, $breaks )) {
			echo "</tr><tr>";
		}
		$t ++;
	}
	echo "</tr>";
	echo "</table><br /><br />";
	for($i = 0; $i < count ( $alpha ); $i ++) {
		$bgc = (in_array ( $alpha [$i], $answer ) || in_array ( strtoupper ( $alpha [$i] ), $answer )) ? "#cccccc" : "#ffffff";
		echo "<div style='float:left; background-color:$bgc; color:#000000; font-weight:bold; font-size:1.25em; text-align:center; padding:5px;'> $alpha[$i] </div>";
	}
	echo "<br /><br /><br /><br />";
	if ($_SESSION ['hints'] <= 2) {
		echo "<button type='button' onclick='getHint(\"freebie\",\"$userId\")'> Give me a hint - " . $_SESSION ['hints'] . " used </button><br /><br />";
	}
	echo "<button type='button' onclick='puzzleStart(\"reset\",\"$userId\")'> Reset current cipher </button><br /><br /><button type='button' onclick='puzzleStart(\"new\",\"$userId\")'> Get new cipher </button>";
}