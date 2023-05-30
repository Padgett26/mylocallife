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

if (filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT )) {
	$userId = filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT );
}

$time = time ();
function CheckComplete($g, $s, $d, $goal) {
	$c = true;
	// row check to see if complete
	for($i = 0; $i < $s; ++ $i) {
		$t = 0;
		for($j = 0; $j < $s; ++ $j) {
			if (settype ( $g [$i] [$j], "integer" )) {
				$t += $g [$i] [$j];
			}
		}
		$c = ($t == $goal) ? $c : false;
	}

	// col check to see if complete
	for($i = 0; $i < $s; ++ $i) {
		$t = 0;
		for($j = 0; $j < $s; ++ $j) {
			if (settype ( $g [$j] [$i], "integer" )) {
				$t += $g [$j] [$i];
			}
		}
		$c = ($t == $goal) ? $c : false;
	}

	if ($d == "hard") {
		// diagonal check to see if complete
		$t = 0;
		for($i = 0; $i < $s; ++ $i) {
			if (settype ( $g [$i] [$i], "integer" )) {
				$t += $g [$i] [$i];
			}
		}
		$c = ($t == $goal) ? $c : false;

		// diagonal check to see if complete
		$j = $s;
		$t = 0;
		for($i = 0; $i < $s; ++ $i) {
			$j --;
			if (settype ( $g [$i] [$j], "integer" )) {
				$t += $g [$i] [$j];
			}
		}
		$c = ($t == $goal) ? $c : false;
	}
	return $c;
}

if (filter_input ( INPUT_GET, 'get', FILTER_SANITIZE_NUMBER_INT )) { // Create a new puzzle
	$gridSize = filter_input ( INPUT_GET, 'get', FILTER_SANITIZE_NUMBER_INT );
	$difficulty = filter_input ( INPUT_GET, 'diff', FILTER_SANITIZE_STRING );
	if ($gridSize >= 3 && $gridSize <= 9) {
		$_SESSION ['mSquareTime'] = $time;
		$_SESSION ['mSquareSize'] = $gridSize;
		$_SESSION ['mSquareGrid'] = array ();
		$_SESSION ['mSquareDifficulty'] = $difficulty;
		for($i = 0; $i < $gridSize; $i ++) {
			for($j = 0; $j < $gridSize; $j ++) {
				$_SESSION ['mSquareGrid'] [$i] [$j] = 0;
			}
		}
	}
}

$gridSize = $_SESSION ['mSquareSize'];
$difficulty = $_SESSION ['mSquareDifficulty'];

if (filter_input ( INPUT_GET, 'num', FILTER_SANITIZE_NUMBER_INT )) { // Update exixting puzzle
	$num = filter_input ( INPUT_GET, 'num', FILTER_SANITIZE_NUMBER_INT );
	if ($num >= 1 && $num <= ($gridSize * $gridSize)) {
		$posx = filter_input ( INPUT_GET, 'posx', FILTER_SANITIZE_NUMBER_INT );
		$posy = filter_input ( INPUT_GET, 'posy', FILTER_SANITIZE_NUMBER_INT );

		for($i = 0; $i < $gridSize; $i ++) {
			for($j = 0; $j < $gridSize; $j ++) {
				$_SESSION ['mSquareGrid'] [$i] [$j] = ($_SESSION ['mSquareGrid'] [$i] [$j] == $num) ? 0 : $_SESSION ['mSquareGrid'] [$i] [$j];
			}
		}

		$_SESSION ['mSquareGrid'] [$posx] [$posy] = $num;
	}
}

if (filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_NUMBER_INT ) == "1") { // Update exixting puzzle
	for($i = 0; $i < $gridSize; $i ++) {
		for($j = 0; $j < $gridSize; $j ++) {
			$_SESSION ['mSquareGrid'] [$i] [$j] = 0;
		}
	}
}

$grid = array ();
for($i = 0; $i < $gridSize; ++ $i) {
	for($j = 0; $j < $gridSize; ++ $j) {
		if (settype ( $_SESSION ['mSquareGrid'] [$i] [$j], "integer" )) {
			$grid [$i] [$j] = $_SESSION ['mSquareGrid'] [$i] [$j];
		}
	}
}

switch ($gridSize) {
	case 3 :
		$goal = 15;
		break;
	case 6 :
		$goal = 111;
		break;
	case 9 :
		$goal = 369;
		break;
}

if (CheckComplete ( $grid, $gridSize, $difficulty, $goal )) {
	$tot = ($time - $_SESSION ['mSquareTime']);
	$hours = floor ( $tot / 3600 );
	$mins = floor ( ($tot - ($hours * 3600)) / 60 );
	$secs = ($tot - ($hours * 3600)) % 60;
	echo "<span style='font-weight:bold;'>You won!!! And it only took ";
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
		echo "$secs secs</span><br /><br />";
	}
	echo "Would you like to play again?<br /><br />";
	?>
	Select a grid size and difficulty:<br><br>
    Hard Mode: Create puzzle where the diagonals are counted (only 1 known answer(4, if you flip the board)):<br>
    <button onclick='puzzleStart(3, "hard", <?php
	echo $userId;
	?>)'> 3 </button> <button onclick='puzzleStart(6, "hard", <?php
	echo $userId;
	?>)'> 6 </button> <button onclick='puzzleStart(9, "hard", <?php
	echo $userId;
	?>)'> 9 </button><br><br>
    Create puzzle where the diagonals are not counted (many answers):<br>
    <button onclick='puzzleStart(3, "easy", <?php
	echo $userId;
	?>)'> 3 </button> <button onclick='puzzleStart(6, "easy", <?php
	echo $userId;
	?>)'> 6 </button> <button onclick='puzzleStart(9, "easy", <?php
	echo $userId;
	?>)'> 9 </button><br><br>
	<?php
	if ($userId != 0) {
		$als = $db->prepare ( "SELECT accessLevel FROM users WHERE id = ?" );
		$als->execute ( array (
				$userId
		) );
		$alr = $als->fetch ();
		if ($alr) {
			$al = $alr ['accessLevel'];
			if ($al >= 1) {
				$d = ($difficulty == "hard") ? "H" : "E";
				$table = "mSquare" . $d . $gridSize;
				echo "<div style='font-weight:bold; font-size:1.25em;'>My best score:</div>";
				$sb = $db->prepare ( "SELECT $table FROM gameLog WHERE userId = ?" );
				$sb->execute ( array (
						$userId
				) );
				$sbrow = $sb->fetch ();
				if ($sbrow && ($sbrow [0] == 0 || $sbrow [0] > $tot)) {
					$sb1 = $db->prepare ( "UPDATE gameLog SET $table = ? WHERE userId = ?" );
					$sb1->execute ( array (
							$tot,
							$userId
					) );
					$score = $tot;
				} else {
					$score = ($sbrow) ? $sbrow [0] : 0;
				}
				$s = "<span style=''>";
				$hours = floor ( $score / 3600 );
				$mins = floor ( ($score - ($hours * 3600)) / 60 );
				$secs = ($score - ($hours * 3600)) % 60;
				$h = 0;
				$m = 0;
				if ($hours != '0') {
					$s .= "$hours hours";
					$h = '1';
				}
				if ($mins != '0') {
					if ($h == '1') {
						$s .= ", ";
					}
					$s .= "$mins mins";
					$m = '1';
				}
				if ($secs != '0') {
					if ($h == '1' || $m == '1') {
						$s .= ", ";
					}
					$s .= "$secs secs";
				}
				$s .= "</span><br />";
				echo $s;
				echo "<br /><div style='font-weight:bold; font-size:1.25em;'>Global best scores:</div>";
				$sb4 = $db->prepare ( "SELECT t1.userId, t1.$table, t2.firstName, t2.lastName FROM gameLog AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t1.$table != '0' ORDER BY t1.$table LIMIT 10" );
				$sb4->execute ();
				$t = '1';
				while ( $sb4row = $sb4->fetch () ) {
					$u = $sb4row ['userId'];
					$score = $sb4row ['mSquareTime'];
					$fname = $sb4row ['firstName'];
					$lname = str_split ( $sb4row ['lastName'] );
					$s = ($userId == $u) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
					$s .= "$t. ";
					$hours = floor ( $score / 3600 );
					$mins = floor ( ($score - ($hours * 3600)) / 60 );
					$secs = ($score - ($hours * 3600)) % 60;
					if ($hours != '0') {
						$s .= "$hours hours";
						$h = '1';
					}
					if ($mins != '0') {
						if ($h == '1') {
							$s .= ", ";
						}
						$s .= "$mins mins";
						$m = '1';
					}
					if ($secs != '0') {
						if ($h == '1' || $m == '1') {
							$s .= ", ";
						}
						$s .= "$secs secs";
					}
					$s .= " by $fname $lname[0]</span><br />";
					echo $s;
					$t ++;
				}
			}
		}
	}
} else {
	echo "<table cellpadding='0' cellspacing='0' border='0'>";
	echo "<tr><td colspan='$gridSize' style='border:1px solid black; text-align:center; min-width:30px; padding:5px;'>C / R";

	echo ($difficulty == "hard") ? " / D =" : " =";
	echo " $goal</td><td style='border:1px solid black; text-align:center; min-width:30px;'>\n";
	if ($difficulty == "hard") {
		$j = $gridSize;
		$t = 0;
		for($i = 0; $i <= $gridSize; ++ $i) {
			$j --;
			$t += $grid [$j] [$i];
		}
		echo "$t";
	} else {
		echo "X";
	}
	echo "</td></tr>\n";
	for($j = 0; $j <= $gridSize; ++ $j) {
		echo "<tr>\n";
		for($i = 0; $i <= $gridSize; ++ $i) {
			echo "<td style='border:1px solid black; text-align:center; min-width:30px;'>\n";
			if ($i == $gridSize && $j != $gridSize) {
				$s = 0;
				for($k = 0; $k < $gridSize; ++ $k) {
					$s += $grid [$k] [$j];
				}
				echo $s;
			} elseif ($j == $gridSize && $i != $gridSize) {
				$s = 0;
				for($k = 0; $k < $gridSize; ++ $k) {
					$s += $grid [$i] [$k];
				}
				echo $s;
			} elseif ($j == $gridSize && $i == $gridSize && $difficulty == "hard") {
				$s = 0;
				$l = 0;
				for($k = 0; $k < $gridSize; ++ $k) {
					$s += $grid [$k] [$l];
					$l ++;
				}
				echo $s;
			} elseif ($j == $gridSize && $i == $gridSize && $difficulty != "hard") {
				echo "X";
			} elseif ($j == $gridSize && $i == 0 && $difficulty == "hard") {
				$s = 0;
				$l = 0;
				for($k = ($gridSize - 1); $k >= 0; -- $k) {
					$s += $grid [$k] [$l];
					$l ++;
				}
				echo $s;
			} elseif ($i < $gridSize && $j < $gridSize) {
				echo "<input type='text' onkeyup='numSelect(this.value,\"$i\",\"$j\", $userId)' value='" . $grid [$i] [$j] . "' size='2' style='maxlength:2;'>\n";
			}
			echo "</td>\n";
		}
		echo "</tr>\n";
	}
	echo "</table><br />";
	echo "<table cellpading='0' cellspacing='0'><tr>\n";
	for($i = 1; $i <= ($gridSize * $gridSize); ++ $i) {
		if ($i % 10 == 1)
			echo "</tr><tr>";
		$bgc = "#ffffff";
		for($j = 0; $j < $gridSize; ++ $j) {
			for($k = 0; $k < $gridSize; ++ $k) {
				if ($i == $grid [$j] [$k])
					$bgc = "#cccccc";
			}
		}
		echo "<td style='background-color:$bgc; min-width:15px; text-align:center;'>$i</td>\n";
	}
	echo "</tr></table><br><br>\n";
	echo "<button type='button'> Update </button><br /><br /><button type='button' onclick='puzzleReset($userId)'> Reset board </button>\n";
}
