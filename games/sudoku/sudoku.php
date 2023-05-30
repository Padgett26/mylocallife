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

$err = "";
$diff = filter_input ( INPUT_GET, 'difficulty', FILTER_SANITIZE_NUMBER_INT );
$reset = filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_STRING );
$pause = (filter_input ( INPUT_GET, 'pause', FILTER_SANITIZE_STRING ) == 'pause') ? 1 : 2;
$loci = filter_input ( INPUT_GET, 'loci', FILTER_SANITIZE_NUMBER_INT ) - 1;
$locj = filter_input ( INPUT_GET, 'locj', FILTER_SANITIZE_NUMBER_INT ) - 1;
$num = filter_input ( INPUT_GET, 'num', FILTER_SANITIZE_NUMBER_INT ) - 1;
$GoToMove = filter_input ( INPUT_GET, 'move', FILTER_SANITIZE_NUMBER_INT ) - 1;
$selectNum = filter_input ( INPUT_GET, 'selectNum', FILTER_SANITIZE_NUMBER_INT ) - 1;
$userId = filter_input ( INPUT_GET, 'userId', FILTER_SANITIZE_NUMBER_INT );
$letters = array (
		"a",
		"b",
		"c",
		"d",
		"e",
		"f",
		"g",
		"h",
		"i"
);
function checkCRB($i, $j, $array) {
	$x = column ( $i, $j, $array );
	$y = row ( $i, $j, $array );
	$z = block ( $i, $j, $array );
	if ($x && $y && $z) {
		return true;
	} else {
		return false;
	}
}
function column($i, $j, $array) {
	$num = $array [$i] [$j];
	if ($num != 0) {
		for($a = 0; $a < 9; $a ++) {
			if ($array [$a] [$j] == $num && $a != $i) {
				return false;
			}
		}
	}
	return true;
}
function row($i, $j, $array) {
	$num = $array [$i] [$j];
	if ($num != 0) {
		for($a = 0; $a < 9; $a ++) {
			if ($array [$i] [$a] == $num && $a != $j) {
				return false;
			}
		}
	}
	return true;
}
function block($i, $j, $array) {
	$num = $array [$i] [$j];
	if ($num != 0) {
		$a = floor ( $i / 3 ) * 3;
		$b = floor ( $j / 3 ) * 3;
		for($c = $a; $c < $a + 3; $c ++) {
			for($d = $b; $d < $b + 3; $d ++) {
				if ($c == $i && $d == $j) {
					continue;
				}
				if ($array [$c] [$d] == $num) {
					return false;
				}
			}
		}
	}
	return true;
}
function allDone($array) {
	for($k = 0; $k < 9; $k ++) {
		for($l = 0; $l < 9; $l ++) {
			if ($array [$k] [$l] == 0 || ! checkCRB ( $k, $l, $array )) {
				return FALSE;
			}
		}
	}
	return TRUE;
}

if (filter_input ( INPUT_GET, 'selectNum', FILTER_SANITIZE_NUMBER_INT )) {
	$_SESSION ['selectNum'] = $selectNum;
}
if (! isset ( $_SESSION ['selectNum'] )) {
	$_SESSION ['selectNum'] = 1;
}

if (filter_input ( INPUT_GET, 'difficulty', FILTER_SANITIZE_NUMBER_INT )) {
	if ($userId == 0) {
		$stmt = $db->prepare ( "SELECT id, keyBoard, gameBoard FROM sudokuBoards WHERE difficulty = ? ORDER BY RAND() LIMIT 1" );
		$stmt->execute ( array (
				$diff
		) );
		$row = $stmt->fetch ();
		if ($row) {
			$id = $row ['id'];
			$k = $row ['keyBoard'];
			$keyBoard = str_split ( $k );
			$g = $row ['gameBoard'];
			$gameBoard = str_split ( $g );
			$stmt2 = $db->prepare ( "UPDATE sudokuBoards SET playCount = playCount + 1 WHERE id = ?" );
			$stmt2->execute ( array (
					$id
			) );
		}
	} else {
		switch ($diff) {
			case 2 :
				$d = "sudokuEId";
				break;
			case 3 :
				$d = "sudokuMId";
				break;
			case 4 :
				$d = "sudokuHId";
				break;
			case 5 :
				$d = "sudokuEvilId";
				break;
			default :
				$d = "sudokuMId";
		}
		$getStmt = $db->prepare ( "SELECT $d FROM gameLog WHERE userId = ?" );
		$getStmt->execute ( array (
				$userId
		) );
		$gsr = $getStmt->fetch ();
		$lastPuzzle = ($gsr) ? $gsr ["$d"] : 0;
		$stmt = $db->prepare ( "SELECT id, keyBoard, gameBoard FROM sudokuBoards WHERE difficulty = ? AND id > ? ORDER BY id LIMIT 1" );
		$stmt->execute ( array (
				$diff,
				$lastPuzzle
		) );
		$row = $stmt->fetch ();
		if ($row) {
			$id = $row ['id'];
			$k = $row ['keyBoard'];
			$keyBoard = str_split ( $k );
			$g = $row ['gameBoard'];
			$gameBoard = str_split ( $g );
			$stmt2 = $db->prepare ( "UPDATE sudokuBoards SET playCount = playCount + 1 WHERE id = ?" );
			$stmt2->execute ( array (
					$id
			) );
			$stmt3 = $db->prepare ( "UPDATE gameLog SET $d = ? WHERE userId = ?" );
			$stmt3->execute ( array (
					$id,
					$userId
			) );
		}
	}
	$keyPuzzle = array ();
	for($i = 0; $i < 9; $i ++) {
		for($j = 0; $j < 9; $j ++) {
			$keyPuzzle [$i] [$j] = array_pop ( $keyBoard );
		}
	}
	$gamePuzzle = array ();
	for($i = 0; $i < 9; $i ++) {
		for($j = 0; $j < 9; $j ++) {
			$gamePuzzle [$i] [$j] = array_pop ( $gameBoard );
		}
	}

	$_SESSION ['basePuzzle'] = $gamePuzzle;
	$_SESSION ['workingPuzzle'] = $gamePuzzle;
	$_SESSION ['keyPuzzle'] = $keyPuzzle;
	$_SESSION ['startTime'] = time ();
	$_SESSION ['runningTime'] = 0;
	$_SESSION ['difficulty'] = $diff;
	$_SESSION ['moves'] = array ();
}

if ($err != "") {
	echo $err;
} else {
	$difficulty = $_SESSION ['difficulty'];
	$activeNum = $_SESSION ['selectNum'];
	$wp = $_SESSION ['workingPuzzle'];
	$bp = $_SESSION ['basePuzzle'];
	$kp = $_SESSION ['keyPuzzle'];

	switch ($difficulty) {
		case 2 :
			$diffName = " EASY";
			$cellName = "sudokuETime";
			break;
		case 3 :
			$diffName = " MEDIUM";
			$cellName = "sudokuMTime";
			break;
		case 4 :
			$diffName = " HARD";
			$cellName = "sudokuHTime";
			break;
		case 5 :
			$diffName = " EVIL";
			$cellName = "sudokuEvilTime";
			break;
		default :
			$diffName = " MEDIUM";
			$cellName = "sudokuMTime";
	}

	if ($_SESSION ['startTime'] != 0) {
		$_SESSION ['runningTime'] = $_SESSION ['runningTime'] + (time () - $_SESSION ['startTime']);
	}
	if ($pause == 2) {
		$_SESSION ['startTime'] = time ();
	} else {
		$_SESSION ['startTime'] = 0;
	}

	if (filter_input ( INPUT_GET, 'reset', FILTER_SANITIZE_STRING ) == "reset") {
		for($i = 0; $i < 9; $i ++) {
			for($j = 0; $j < 9; $j ++) {
				$wp [$i] [$j] = $bp [$i] [$j];
			}
		}
		$_SESSION ['workingPuzzle'] = $wp;
		unset ( $_SESSION ['moves'] );
		$moves = array ();
		$_SESSION ['moves'] = $moves;
	}

	$moves = $_SESSION ['moves'];

	if (filter_input ( INPUT_GET, 'move', FILTER_SANITIZE_NUMBER_INT )) {
		$x = count ( $moves ) - 1;
		for($i = $x; $i > $GoToMove; $i --) {
			$step = array_pop ( $moves );
			$wp [$step [0]] [$step [1]] = $step [2];
		}
		$_SESSION ['moves'] = $moves;
		$_SESSION ['workingPuzzle'] = $wp;
	}

	if (filter_input ( INPUT_GET, 'loci', FILTER_SANITIZE_NUMBER_INT ) && filter_input ( INPUT_GET, 'locj', FILTER_SANITIZE_NUMBER_INT ) && filter_input ( INPUT_GET, 'num', FILTER_SANITIZE_NUMBER_INT )) {
		$n = $wp [$loci] [$locj];
		$movesCount = count ( $moves );
		$moves [$movesCount] [0] = $loci;
		$moves [$movesCount] [1] = $locj;
		$moves [$movesCount] [2] = $n;
		$moves [$movesCount] [3] = $num;
		$_SESSION ['moves'] = $moves;
		$wp [$loci] [$locj] = $num;
		$_SESSION ['workingPuzzle'] = $wp;
	}

	if (allDone ( $wp )) {
		$tot = $_SESSION ['runningTime'];
		$hours = ( int ) ($tot / 3600);
		$mins = ( int ) (($tot - ($hours * 3600)) / 60);
		$secs = ( int ) (($tot - ($hours * 3600)) % 60);
		echo "<span style='font-weight:bold;'>You won!!! And it only took ";
		if ($hours != 0) {
			echo "$hours hours";
			$h = 1;
		} else {
			$h = 0;
		}
		if ($mins != 0) {
			if ($h == 1) {
				echo ", ";
			}
			echo "$mins mins";
			$m = 1;
		} else {
			$m = 0;
		}
		if ($secs != 0) {
			if ($h == 1 || $m == 1) {
				echo ", ";
			}
			echo "$secs secs</span><br /><br />";
		}
		echo "<br />Play again? Select a difficulty.<br /><br />";

		// Grab easy board availablity
		if ($userId == 0) {
			$e2 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE difficulty = ?" );
			$e2->execute ( array (
					'2'
			) );
			$e2r = $e2->fetch ();
			if ($e2r) {
				$c = $e2r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(2, 0)'> Easy </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}
			$e4 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE difficulty = ?" );
			$e4->execute ( array (
					'3'
			) );
			$e4r = $e4->fetch ();
			if ($e4r) {
				$c = $e4r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(3, 0)'> Medium </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}
			$e6 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE difficulty = ?" );
			$e6->execute ( array (
					'4'
			) );
			$e6r = $e6->fetch ();
			if ($e6r) {
				$c = $e6r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(4, 0)'> Hard </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}
			$e8 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE difficulty = ?" );
			$e8->execute ( array (
					'5'
			) );
			$e8r = $e8->fetch ();
			if ($e8r) {
				$c = $e8r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(5, 0)'> Evil </button>";
				}
			}
		} else {
			$e1 = $db->prepare ( "SELECT sudokuEId FROM gameLog WHERE userId = ?" );
			$e1->execute ( array (
					$userId
			) );
			$e1r = $e1->fetch ();
			if ($e1r) {
				$gameEId = $e1r [0];
			}
			$e2 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE id > ? AND difficulty = ?" );
			$e2->execute ( array (
					$gameEId,
					'2'
			) );
			$e2r = $e2->fetch ();
			if ($e2r) {
				$c = $e2r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(2, \"$userId\")'> Easy </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}

			// Grab medium board availablity
			$e3 = $db->prepare ( "SELECT sudokuMId FROM gameLog WHERE userId = ?" );
			$e3->execute ( array (
					$userId
			) );
			$e3r = $e3->fetch ();
			if ($e3r) {
				$gameMId = $e3r [0];
			}
			$e4 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE id > ? AND difficulty = ?" );
			$e4->execute ( array (
					$gameMId,
					'3'
			) );
			$e4r = $e4->fetch ();
			if ($e4r) {
				$c = $e4r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(3, \"$userId\")'> Medium </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}

			// Grab hard board availablity
			$e5 = $db->prepare ( "SELECT sudokuHId FROM gameLog WHERE userId = ?" );
			$e5->execute ( array (
					$userId
			) );
			$e5r = $e5->fetch ();
			if ($e5r) {
				$gameHId = $e5r [0];
			}
			$e6 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE id > ? AND difficulty = ?" );
			$e6->execute ( array (
					$gameHId,
					'4'
			) );
			$e6r = $e6->fetch ();
			if ($e6r) {
				$c = $e6r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(4, \"$userId\")'> Hard </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
				}
			}

			// Grab evil board availablity
			$e7 = $db->prepare ( "SELECT sudokuEvilId FROM gameLog WHERE userId = ?" );
			$e7->execute ( array (
					$userId
			) );
			$e7r = $e7->fetch ();
			if ($e7r) {
				$gameEvilId = $e7r [0];
			}
			$e8 = $db->prepare ( "SELECT COUNT(*) FROM sudokuBoards WHERE id > ? AND difficulty = ?" );
			$e8->execute ( array (
					$gameEvilId,
					'5'
			) );
			$e8r = $e8->fetch ();
			if ($e8r) {
				$c = $e8r [0];
				if ($c >= 1) {
					echo "<button type='button' onclick='sudokuStart(5, \"$userId\")'> Evil </button>";
				}
			}
		}
		?>
<br /><br /><br />
Fill in the blank squares with numbers 1 - 9 so that there are no duplicates in any row, column, or 3x3 square.<br />
Highlight a number, then click on a square to place it.<br /><br />
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
					echo "<div style='font-weight:bold; font-size:1.25em;'>My best";
					echo " $diffName level score:";
					$sb = $db->prepare ( "SELECT $cellName FROM gameLog WHERE userId=?" );
					$sb->execute ( array (
							$userId
					) );
					$sbrow = $sb->fetch ();
					if ($sbrow) {
						$oldTime = $sbrow [0];
						if ($oldTime == 0 || $tot < $oldTime) {
							$sb1 = $db->prepare ( "UPDATE gameLog SET $cellName = ? WHERE userId = ?" );
							$sb1->execute ( array (
									$tot,
									$userId
							) );
							$best = $tot;
						} else {
							$best = $oldTime;
						}
					}
					$hours = ( int ) ($best / 3600);
					$mins = ( int ) (($best - ($hours * 3600)) / 60);
					$secs = ( int ) (($best - ($hours * 3600)) % 60);
					if ($hours != 0) {
						echo "$hours hours";
						$h = 1;
					} else {
						$h = 0;
					}
					if ($mins != 0) {
						if ($h == 1) {
							echo ", ";
						}
						echo "$mins mins";
						$m = 1;
					} else {
						$m = 0;
					}
					if ($secs != 0) {
						if ($h == 1 || $m == 1) {
							echo ", ";
						}
						echo "$secs secs";
					}
					echo "</div><div style='font-weight:bold; font-size:1.25em; margin-top:20px;'>Global best scores: $diffName</div>";
					$sb2 = $db->prepare ( "SELECT t1.$cellName, t2.firstName, t2.lastName FROM gameLog AS t1 INNER JOIN users AS t2 ON t1.userId = t2.id WHERE t1.$cellName > '0' ORDER BY t1.$cellName LIMIT 10" );
					$sb2->execute ();
					$t = '1';
					while ( $sb2row = $sb2->fetch () ) {
						$score = $sb2row ["$cellName"];
						$fname = $sb2row ['firstName'];
						$lname = str_split ( $sb2row ['lastName'] );
						$s = "<span style='font-weight:bold;'>$t.</span> ";
						$s .= ($score == $tot) ? "<span style='font-weight:bold; font-size:1.25em;'>" : "<span style=''>";
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
		echo "<div style='text-align:center; font-weight:bold; font-size:2em'>$diffName</div>";
		echo '<div style="margin:0px;">';
		$dupe = 0;
		for($i = 0; $i < 9; $i ++) {
			for($j = 0; $j < 9; $j ++) {
				if (! checkCRB ( $i, $j, $wp )) {
					echo "<div style='color:red; font-size:1em;'>You have duplicate numbers in a row, column, or block. Please change your inputs, or reset the game.</div><br /><br />";
					$dupe = 1;
					break 2;
				}
			}
		}
		echo "<div style='margin-left:75px;'>";
		for($i = 0; $i <= 9; $i ++) {
			$full = array (
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0,
					0
			);
			for($a = 0; $a < 9; $a ++) {
				for($b = 0; $b < 9; $b ++) {
					$n = $wp [$a] [$b];
					if ($n != 0) {
						$full [$n] += 1;
					}
				}
			}
			if ($full [$i] == 9) {
				echo "<div style='float:left; padding:5px 10px; margin: 5px; font-size:2em; border:2px solid white; color:black; background-color:#ffffff;'>$i</div>";
			} else {
				$style = ($i == $activeNum) ? "border:2px solid blue; border-radius:25px; color:black; background-color:#ffffff;" : "border:2px solid blue; border-radius:25px; color:#ffffff; background-color:blue; cursor:pointer;";
				echo "<div style='float:left; padding:5px 10px; margin: 5px; font-size:2em; $style' onclick='sudokuNumSelect(" . ($i + 1) . ", $userId)'>";
				echo ($i == 0) ? "&#8709;" : $i;
				echo "</div>";
			}
		}
		echo "</div><table cellspacing='0' style='width:100%;'><tr><td style='width:100;'><div onclick='sudokuReset(\"reset\", $userId)' style='width:80px; margin-right:8px; padding:10px 0px; text-align:center; cursor:pointer; background-color:#cccccc; border:1px solid black; border-radius:15px; font-weight:bold; font-size:.75em;'>Reset Puzzle</div><br /><br />";
		if ($pause == 2) {
			echo "<div onclick='sudokuPause(\"pause\", $userId)' style='width:80px; margin-right:8px; padding:10px 0px; text-align:center; cursor:pointer; background-color:#cccccc; border:1px solid black; border-radius:15px; font-weight:bold; font-size:.75em;'> Pause </div>";
		} else {
			echo "<div onclick='sudokuPause(\"resume\", $userId)' style='width:80px; margin-right:8px; padding:10px 0px; text-align:center; cursor:pointer; background-color:#cccccc; border:1px solid black; border-radius:15px; font-weight:bold; font-size:.75em;'> Resume </div>";
		}
		echo "</td><td><table cellspacing='0px' style='border:2px solid ";
		echo ($dupe == 1) ? "red" : "blue";
		echo "; width:500px;'><tr>";
		if ($pause == 1) {
			echo "<td style='padding:50px 0px; font-weight:bold; text-align:center;'>";
			$tot = $_SESSION ['runningTime'];
			$hours = ( int ) ($tot / 3600);
			$mins = ( int ) (($tot - ($hours * 3600)) / 60);
			$secs = ( int ) (($tot - ($hours * 3600)) % 60);
			echo "<span style='font-weight:bold;'>Paused @ ";
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
				echo "$secs secs</span>";
			}
			echo "</td>";
		} else {
			$t = 0;
			$s = 0;
			for($i = 0; $i < 9; $i ++) {
				echo ($i % 3 == 0 && $i != 0) ? "<tr><td colspan='11' style='padding:0px; margin:0px'><div style='width:100%; height:5px; background-color:#000000; padding:0px; margin:0px'></div></td></tr>" : "";
				for($j = 0; $j < 9; $j ++) {
					echo ($j % 3 == 0 && $j != 0) ? "<td style='padding:0px; margin:0px; background-color:#000000;'><div style='width:5px; padding:0px; margin:0px;'></div></td>" : "";
					$displayMap = "position:absolute; top:0px; left:2px; color:#888888; font-size:.75em;";
					if ($bp [$i] [$j] == 0) {
						echo "<td style='vertical-align:middle;'><div style='position:relative; top:0px; left:0px; margin:0px; padding:0px;'>";
						echo "<button type='button' onclick='sudokuUpdate(" . ($i + 1) . ", " . ($j + 1) . ", " . ($activeNum + 1) . ", $userId)' style='width:52px; height:52px; border:1px solid black;";
						if ($wp [$i] [$j] == $activeNum && $activeNum != 0) {
							echo " background-color:lightblue;";
						} elseif ($activeNum == 0) {
							echo " background-color:lightblue;";
						} else {
							echo " background-color:#ffffff;";
						}
						echo "'><div style='position:relative; top:0px; left:0px; margin:0px; padding:0px;'>";
						if ($wp [$i] [$j] != 0) {
							echo "<div style='margin-top:0px; font-weight:bold; font-size:1.25em; text-align:center;'>" . $wp [$i] [$j] . "</div>";
						}
						echo "</button>";
						if ($i == 0 && $j == 0) {
							echo "<div style='$displayMap'>" . $letters [$j] . $i . "</div>";
						} else {
							if ($i == 0) {
								echo "<div style='$displayMap'>" . $letters [$j] . "</div>";
							}
							if ($j == 0) {
								echo "<div style='$displayMap'>" . $i . "</div>";
							}
						}
						echo "</div></td>";
					} else {
						echo "<td style=' padding:1px;'>";
						if ($wp [$i] [$j] == $activeNum) {
							echo "<div style='width:50px; height:40px; border:1px solid black; padding-top:10px; position:relative; top:0px; left:0px; background-color:lightblue;'>";
						} else {
							echo "<div style='width:50px; height:40px; border:1px solid black; padding-top:10px; position:relative; top:0px; left:0px; background-color:#eeeeee;'>";
						}
						echo "<div style='margin-top:0px; font-weight:bold; font-size:1.25em; text-align:center;'>" . $wp [$i] [$j] . "</div>";
						if ($i == 0 && $j == 0) {
							echo "<div style='$displayMap'>" . $letters [$j] . $i . "</div>";
						} else {
							if ($i == 0) {
								echo "<div style='$displayMap'>" . $letters [$j] . "</div>";
							}
							if ($j == 0) {
								echo "<div style='$displayMap'>" . $i . "</div>";
							}
						}
						echo "</div></td>";
					}
					$t ++;
					if ($t % 9 == 0) {
						echo "</tr><tr>";
					}
				}
				$s ++;
			}
		}
		echo "</tr></table></td><td style='width:50px; padding-left:8px;'><span style='font-weight:bold;'>Moves</span><br />";
		$moveCount = count ( $moves );
		for($f = 0; $f < $moveCount; $f ++) {
			$g = $f + 1;
			echo "<a style='' onclick='sudokuMoves($g, $userId)'>" . $letters [$moves [$f] [1]] . $moves [$f] [0] . "&nbsp;#" . $moves [$f] [3] . "</a> <br />";
			if ($f % 19 == 0 && $f != 0) {
				echo "</td><td style='width:50px;'><br />";
			}
		}
		echo "</td><td style='width:100%;'></td></tr></table></div>";
	}
}