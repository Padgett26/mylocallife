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

$time = time ();
function createBoard(&$board) {
	$t = 0;
	for($i = 0; $i < 16; $i ++) {
		$board [$i] = $t;
		$t ++;
	}
	shuffle ( $board );
	for($i = 0; $i < 16; $i ++) {
		if ($board [$i] == 0) {
			$geti = $i;
		}
	}
	$temp = $board [15];
	$board [$geti] = $temp;
	$board [15] = 0;
	if (! isSolvable ( $board )) {
		createBoard ( $board );
	}
}
function isSolvable($test) {
	$t = 0;
	for($i = 0; $i < 15; $i ++) {
		for($j = $i; $j < 16; $j ++) {
			if ($test [$i] > $test [$j] && $test [$j] != 0) {
				$t ++;
			}
		}
	}
	return ($t % 2 == 0) ? TRUE : FALSE;
}
function puzzleComplete($checkBoard) {
	$t = 1;
	for($i = 0; $i < 4; $i ++) {
		for($j = 0; $j < 4; $j ++) {
			if ($i == 3 && $j == 3) {
				$t = 0;
			}
			if ($checkBoard [$i] [$j] != $t) {
				return FALSE;
			}
			$t ++;
		}
	}
	return TRUE;
}

if (filter_input ( INPUT_GET, "getnew", FILTER_SANITIZE_STRING ) == "new") {
	$_SESSION ['startTime'] = $time;
	$boardArray = array ();
	for($i = 0; $i < 16; $i ++) {
		$boardArray [$i] = 0;
	}
	createBoard ( $boardArray );
	$t = 0;
	for($i = 0; $i < 4; $i ++) {
		for($j = 0; $j < 4; $j ++) {
			$board [$i] [$j] = $boardArray [$t];
			$t ++;
		}
	}
	$_SESSION ['board'] = array ();
	for($i = 0; $i < 4; $i ++) {
		for($j = 0; $j < 4; $j ++) {
			$_SESSION ['board'] [$i] [$j] = $board [$i] [$j];
		}
	}
}

if (filter_input ( INPUT_GET, "i", FILTER_SANITIZE_STRING ) && filter_input ( INPUT_GET, "j", FILTER_SANITIZE_STRING )) {
	$geti = filter_input ( INPUT_GET, "i", FILTER_SANITIZE_NUMBER_INT );
	$getj = filter_input ( INPUT_GET, "j", FILTER_SANITIZE_NUMBER_INT );
	for($i = 0; $i < 4; $i ++) {
		for($j = 0; $j < 4; $j ++) {
			if ($_SESSION ['board'] [$i] [$j] == 0) {
				$zeroi = $i;
				$zeroj = $j;
			}
		}
	}
	if ($zeroi == $geti) {
		if ($zeroj > $getj) {
			for($j = $zeroj; $j > $getj; $j --) {
				$_SESSION ['board'] [$geti] [$j] = $_SESSION ['board'] [$geti] [$j - 1];
			}
			$_SESSION ['board'] [$geti] [$getj] = 0;
		}
		if ($zeroj < $getj) {
			for($j = $zeroj; $j < $getj; $j ++) {
				$_SESSION ['board'] [$geti] [$j] = $_SESSION ['board'] [$geti] [$j + 1];
			}
			$_SESSION ['board'] [$geti] [$getj] = 0;
		}
	}
	if ($zeroj == $getj) {
		if ($zeroi > $geti) {
			for($i = $zeroi; $i > $geti; $i --) {
				$_SESSION ['board'] [$i] [$getj] = $_SESSION ['board'] [$i - 1] [$getj];
			}
			$_SESSION ['board'] [$geti] [$getj] = 0;
		}
		if ($zeroi < $geti) {
			for($i = $zeroi; $i < $geti; $i ++) {
				$_SESSION ['board'] [$i] [$getj] = $_SESSION ['board'] [$i + 1] [$getj];
			}
			$_SESSION ['board'] [$geti] [$getj] = 0;
		}
	}
}

for($i = 0; $i < 4; $i ++) {
	for($j = 0; $j < 4; $j ++) {
		$board [$i] [$j] = $_SESSION ['board'] [$i] [$j];
	}
}

if (puzzleComplete ( $board )) {
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
		if (($h == 1 && $m == 0) || ($h == 0 && $m == 1)) {
			echo ", ";
		}
		echo "$secs secs</span><br /><br />";
	}
	echo "<button type='button' onclick='puzzleStart(\"new\", \"$userId\")'> Create new puzzle </button><br /><br />";
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
				$sb = $db->prepare ( "SELECT gameOfFifteenTime FROM gameLog WHERE userId = ?" );
				$sb->execute ( array (
						$userId
				) );
				$sbrow = $sb->fetch ();
				if ($sbrow && ($sbrow [0] == 0 || $sbrow [0] > $tot)) {
					$sb1 = $db->prepare ( "UPDATE gameLog SET gameOfFifteenTime = ? WHERE userId = ?" );
					$sb1->execute ( array (
							$tot,
							$userId
					) );
					$score = $tot;
				} else {
					$score = ($sbrow) ? $sbrow [0] : 0;
				}
				$s = "<span style=''>";
				$hours = ( int ) ($score / 3600);
				$mins = ( int ) (($score - ($hours * 3600)) / 60);
				$secs = ( int ) (($score - ($hours * 3600)) % 60);
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
				$sb4 = $db->prepare ( "SELECT t1.userId, t1.gameOfFifteenTime, t2.firstName, t2.lastName FROM gameLog AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t1.gameOfFifteenTime != '0' ORDER BY t1.gameOfFifteenTime LIMIT 10" );
				$sb4->execute ();
				$t = '1';
				while ( $sb4row = $sb4->fetch () ) {
					$u = $sb4row ['userId'];
					$score = $sb4row ['gameOfFifteenTime'];
					$fname = $sb4row ['firstName'];
					$lname = str_split ( $sb4row ['lastName'] );
					$s = ($userId == $u) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
					$s .= "$t. ";
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
	echo "<table cellpadding='0' cellspacing='5' style='background-color:#000000;'><tr>";
	for($i = 0; $i < 4; $i ++) {
		for($j = 0; $j < 4; $j ++) {
			$bgc = ($board [$i] [$j] == 0) ? "#000000" : "#ffffff";
			echo "<td style='border:1px solid black;'><div style='height:60px; width:60px; text-align:center; border:1px solid $bgc; border-radius:10px; background-color:$bgc; font-size:3em; font-weight:bold;' onclick='puzzleUpdate(\"i$i\",\"j$j\", \"$userId\")'>" . $board [$i] [$j] . "</div></td>";
			if ($j == 3)
				echo "</tr><tr>";
		}
	}
	echo "</tr></table>";
}
