<?php
$userId = ($myId != '0') ? $myId : '0';

echo '<div id="puzzle" style="position:relative; top:0px; left:0px;">';

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
			echo "<button type='button' onclick='sudokuStart(2, \"$userId\")'> Easy </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
			echo "<button type='button' onclick='sudokuStart(3, \"$userId\")'> Medium </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
			echo "<button type='button' onclick='sudokuStart(4, \"$userId\")'> Hard </button>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;";
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
			echo "<button type='button' onclick='sudokuStart(5, \"$userId\")'> Evil </button>";
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
</div>