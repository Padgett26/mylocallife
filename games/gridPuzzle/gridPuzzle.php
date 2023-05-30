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

if (filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_STRING )) {
	$userId = filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_STRING );
}

if ($_SESSION ['myTheme']) {
	$myTheme = $_SESSION ['myTheme'];
} elseif (filter_input ( INPUT_COOKIE, 'myTheme', FILTER_SANITIZE_STRING )) {
	$myTheme = filter_input ( INPUT_COOKIE, 'myTheme', FILTER_SANITIZE_STRING );
} else {
	$myTheme = "default";
}
$getTheme = $db->prepare ( "SELECT highlightColor FROM themes WHERE themeName=?" );
$getTheme->execute ( array (
		$myTheme
) );
$rowTheme = $getTheme->fetch ();
$highlightColor = $rowTheme ['highlightColor'];

$time = time ();
$gridSize = 0;

if (filter_input ( INPUT_GET, 'gridSize', FILTER_SANITIZE_NUMBER_INT )) {
	$g = filter_input ( INPUT_GET, 'gridSize', FILTER_SANITIZE_NUMBER_INT );
	if ($g >= 4 && $g <= 20) {
		$_SESSION ['gridSize'] = $g;
		$_SESSION ['solution'] = array ();
		for($i = 0; $i <= $g; $i ++) {
			for($j = 0; $j <= $g; $j ++) {
				if ($i == 0 || $j == 0) {
					$_SESSION ['solution'] [$i] [$j] = 0;
				}
				$base = RAND ( 0, 9 );
				if ($base >= 4) {
					$_SESSION ['solution'] [$i] [$j] = 1;
				} else {
					$_SESSION ['solution'] [$i] [$j] = 0;
				}
			}
		}
		$_SESSION ['playArea'] = array ();
		for($i = 0; $i <= $g; $i ++) {
			for($j = 0; $j <= $g; $j ++) {
				$_SESSION ['playArea'] [$i] [$j] = 0;
			}
		}
		$_SESSION ['startTime'] = $time;
	}
}

$gridSize = ($_SESSION ['gridSize'] && $_SESSION ['gridSize'] != 0) ? $_SESSION ['gridSize'] : 0;
$solution = array ();
$playArea = array ();
$complete = 0;

if (filter_input ( INPUT_GET, 'i', FILTER_SANITIZE_NUMBER_INT ) && filter_input ( INPUT_GET, 'j', FILTER_SANITIZE_NUMBER_INT )) {
	$newi = filter_input ( INPUT_GET, 'i', FILTER_SANITIZE_NUMBER_INT );
	$newj = filter_input ( INPUT_GET, 'j', FILTER_SANITIZE_NUMBER_INT );
	$_SESSION ['playArea'] [$newi] [$newj] = ($_SESSION ['playArea'] [$newi] [$newj] == 1) ? 0 : 1;
	$complete = 1;
	for($i = 1; $i <= $gridSize; $i ++) {
		for($j = 1; $j <= $gridSize; $j ++) {
			$complete = ($_SESSION ['solution'] [$i] [$j] != $_SESSION ['playArea'] [$i] [$j]) ? 0 : $complete;
		}
	}
}

if (filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_STRING ) == "yes") {
	for($i = 0; $i <= $gridSize; $i ++) {
		for($j = 0; $j <= $gridSize; $j ++) {
			$_SESSION ['playArea'] [$i] [$j] = 0;
		}
	}
}

for($i = 0; $i <= $gridSize; $i ++) {
	for($j = 0; $j <= $gridSize; $j ++) {
		$solution [$i] [$j] = $_SESSION ['solution'] [$i] [$j];
		$playArea [$i] [$j] = $_SESSION ['playArea'] [$i] [$j];
	}
}

if ($gridSize == 0) {
	echo "Select a grid size: <button onclick='puzzleStart(5, \"$userId\")'> 5 </button> <button onclick='puzzleStart(10, \"$userId\")'> 10 </button> <button onclick='puzzleStart(15, \"$userId\")'> 15 </button> <button onclick='puzzleStart(20, \"$userId\")'> 20 </button>\n";
} elseif ($complete == 1) {
	$tot = ($time - $_SESSION ['startTime']);
	$hours = ( int ) ($tot / 3600);
	$mins = ( int ) (($tot - ($hours * 3600)) / 60);
	$secs = ( int ) (($tot - ($hours * 3600)) % 60);
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
		if ($h == 1 || $m == 1) {
			echo ", ";
		}
		echo "$secs secs</span><br /><br />";
	}
	echo "Select a grid size: <button onclick='puzzleStart(5, \"$userId\")'> 5 </button> <button onclick='puzzleStart(10, \"$userId\")'> 10 </button> <button onclick='puzzleStart(15, \"$userId\")'> 15 </button> <button onclick='puzzleStart(20, \"$userId\")'> 20 </button>\n<br /><br />";
	if ($userId != '0') {
		$als = $db->prepare ( "SELECT accessLevel FROM users WHERE id = ?" );
		$als->execute ( array (
				$userId
		) );
		$alr = $als->fetch ();
		if ($alr) {
			$al = $alr ['accessLevel'];
			if ($al >= 1) {
				echo "<div style='font-weight:bold; font-size:1.25em;'>My best score:</div>";
				$sb = $db->prepare ( "SELECT gridPuzzle$gridSize FROM gameLog WHERE userId=?" );
				$sb->execute ( array (
						$userId
				) );
				$sbrow = $sb->fetch ();
				if ($sbrow) {
					$t = $sbrow [0];
					if ($tot < $t || $t == 0) {
						$sb1 = $db->prepare ( "UPDATE gameLog SET gridPuzzle$gridSize = ? WHERE userId = ?" );
						$sb1->execute ( array (
								$tot,
								$userId
						) );
					} else {
						$tot = $t;
					}
					$s = "<span style=''>";
					$hours = ( int ) ($tot / 3600);
					$mins = ( int ) (($tot - ($hours * 3600)) / 60);
					$secs = ( int ) (($tot - ($hours * 3600)) % 60);
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
				}
				echo "<br /><div style='font-weight:bold; font-size:1.25em;'>Global best scores:</div>";
				$sb2 = $db->prepare ( "SELECT t1.userId, t1.gridPuzzle$gridSize, t2.firstName, t2.lastName FROM gameLog AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t1.gridPuzzle$gridSize != '0' ORDER BY t1.gridPuzzle$gridSize LIMIT 10" );
				$sb2->execute ();
				$t = '1';
				while ( $sb2row = $sb2->fetch () ) {
					$u = $sb2row ['userId'];
					$score = $sb2row ["gridPuzzle$gridSize"];
					$fname = $sb2row ['firstName'];
					$lname = str_split ( $sb2row ['lastName'] );
					$s = "<span style='font-weight:bold;'>$t.</span> ";
					$s .= ($userId == $u) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
					$hours = ( int ) ($score / 3600);
					$mins = ( int ) (($score - ($hours * 3600)) / 60);
					$secs = ( int ) (($score - ($hours * 3600)) % 60);
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
	?>
    <table cellpadding="0" cellspacing="0">
        <?php
	for($i = 0; $i <= $gridSize; $i ++) {
		for($j = 0; $j <= $gridSize; $j ++) {
			if ($i == 0 && $j == 0) {
				echo "<tr><td><div style='text-align:center;'>$gridSize X $gridSize</div></td>\n";
			} elseif ($i != 0 && $j == 0) {
				$clue = "";
				$t = 0;
				$rowCorrect = 1;
				for($k = 1; $k <= $gridSize; $k ++) {
					if ($solution [$i] [$k] == 0) {
						if ($t != 0) {
							if ($clue != "") {
								$clue .= ", ";
							}
							$clue .= $t;
							$t = 0;
						}
					} else {
						$t ++;
						if ($k == $gridSize) {
							if ($clue != "") {
								$clue .= ", ";
							}
							$clue .= $t;
						}
					}
					$rowCorrect = ($_SESSION ['solution'] [$i] [$k] != $_SESSION ['playArea'] [$i] [$k]) ? 0 : $rowCorrect;
				}
				echo "<td style='text-align:right; border:1px solid $highlightColor;";
				if ($rowCorrect == 1) {
					echo " background-color:#cccccc;";
				}
				echo "'>$clue</td>\n";
			} elseif ($i == 0 && $j != 0) {
				$clue = "";
				$t = 0;
				$colCorrect = 1;
				for($k = 1; $k <= $gridSize; $k ++) {
					if ($solution [$k] [$j] == 0) {
						if ($t != 0) {
							if ($clue != "") {
								$clue .= ",<br />";
							}
							$clue .= $t;
							$t = 0;
						}
					} else {
						$t ++;
						if ($k == ($gridSize)) {
							if ($clue != "") {
								$clue .= ",<br />";
							}
							$clue .= $t;
						}
					}
					$colCorrect = ($_SESSION ['solution'] [$k] [$j] != $_SESSION ['playArea'] [$k] [$j]) ? 0 : $colCorrect;
				}
				echo "<td style='vertical-align:bottom; text-align:center; border:1px solid $highlightColor;";
				if ($colCorrect == 1) {
					echo " background-color:#cccccc;";
				}
				echo "'>$clue</td>\n";
				if ($j == $gridSize) {
					echo "</tr>";
				}
			} else {
				$bgcolor = ($playArea [$i] [$j] == 0) ? "white" : "black";
				echo "<td><button type='button' onclick='puzzleUpdate(\"$i\",\"$j\",\"$userId\")' style='background-color:$bgcolor; width:25px; height:25px;'>&nbsp;</button></td>\n";
				if ($j == $gridSize) {
					echo "</tr>";
				}
			}
		}
	}
	?>
    </table>
    <?php
	echo "<br /><br /><button type='button' onclick='puzzleReset(\"yes\",\"$userId\")'> Reset game? </button><br /><br /><br />\n";
	echo "Select a grid size: <input size='2' onchange='puzzleStart(this.value,\"userId\")' max-length='2' /> (4-20) <button type='button'> New Puzzle </button>\n";
}
?>
